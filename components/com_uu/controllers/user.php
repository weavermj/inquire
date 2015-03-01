<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Ultimate Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UuControllerUser extends UuController
{
	/**
	 * Method to log in a user.
	 *
	 * @since	1.6
	 */
	public function login()
	{
        //TODO check all return conf must probably be changed.
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();

		// Populate the data array:
		$data = array();
		$data['return'] = base64_decode(JRequest::getVar('return', '', 'POST', 'BASE64'));
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		// Set the return URL if empty.
		if (empty($data['return'])) {
			$data['return'] = 'index.php?option=com_uu&view=profile';
		}

		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('uu.login.form.return', $data['return']);

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];


        $conf = new UuConfig();

		// Perform the log in.
		if (true === $app->login($credentials, $options)) {
			// Success
            $url = UuSiteHelper::getRedirectUrl($conf->get('red_login_success'),$conf->get('red_login_success_custom'));
			//$app->setUserState('uu.login.form.data', array());
			//$app->redirect(JRoute::_($app->getUserState('uu.login.form.return'), false));
            $app->redirect(JRoute::_($url, false));
		} else {
			// Login failed !
			$data['remember'] = (int)$options['remember'];
			$app->setUserState('uu.login.form.data', $data);
			$app->redirect(JRoute::_('index.php?option=com_uu&view=login', false));
		}
	}

	/**
	 * Method to log out a user.
	 *
	 * @since	1.6
	 */
	public function logout()
	{
		//JSession::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();
        $conf = new UuConfig();

		// Perform the log in.
		$error = $app->logout();

		// Check if the log out succeeded.
		if (!($error instanceof Exception)) {
			// Get the return url from the request and validate that it is internal.
            $return = UuSiteHelper::getRedirectUrl($conf->get('red_logout_success'),$conf->get('red_logout_success_custom'));
			if (!JURI::isInternal($return)) {
				$return = '';
			}

			// Redirect the user.
			$app->redirect(JRoute::_($return, false));
		} else {
			$app->redirect(JRoute::_('index.php?option=com_uu&view=login', false));
		}
	}

	/**
	 * Method to register a user.
	 *
	 * @since	1.6
	 */
//	public function register()
//	{
//		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
//
//		// Get the form data.
//		$data	= JRequest::getVar('user', array(), 'post', 'array');
//
//		// Get the model and validate the data.
//		$model	= $this->getModel('Registration', 'UsersModel');
//		$return	= $model->validate($data);
//
//		// Check for errors.
//		if ($return === false) {
//			// Get the validation messages.
//			$app	= &JFactory::getApplication();
//			$errors	= $model->getErrors();
//
//			// Push up to three validation messages out to the user.
//			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
//				if ($errors[$i] instanceof Exception) {
//					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
//				} else {
//					$app->enqueueMessage($errors[$i], 'notice');
//				}
//			}
//
//			// Save the data in the session.
//			$app->setUserState('users.registration.form.data', $data);
//
//			// Redirect back to the registration form.
//			$this->setRedirect('index.php?option=com_users&view=registration');
//			return false;
//		}
//
//		// Finish the registration.
//		$return	= $model->register($data);
//
//		// Check for errors.
//		if ($return === false) {
//			// Save the data in the session.
//			$app->setUserState('users.registration.form.data', $data);
//
//			// Redirect back to the registration form.
//			$message = JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError());
//			$this->setRedirect('index.php?option=com_users&view=registration', $message, 'error');
//			return false;
//		}
//
//		// Flush the data from the session.
//		$app->setUserState('users.registration.form.data', null);
//
//		exit;
//	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function remind()
	{
		// Check the request token.
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$model	= $this->getModel('User', 'UsersModel');
		$data	= JRequest::getVar('jform', array(), 'post', 'array');

		// Submit the username remind request.
		$return	= $model->processRemindRequest($data);

		// Check for a hard error.
		if ($return instanceof Exception) {
			// Get the error message to display.
			if ($app->getCfg('error_reporting')) {
				$message = $return->getMessage();
			} else {
				$message = JText::_('COM_USERS_REMIND_REQUEST_ERROR');
			}

			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the complete form.
			$this->setRedirect(JRoute::_($route, false), $message, 'error');
			return false;
		} elseif ($return === false) {
			// Complete failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the complete form.
			$message = JText::sprintf('COM_USERS_REMIND_REQUEST_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($route, false), $message, 'notice');
			return false;
		} else {
			// Complete succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getLoginRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=login'.$itemid;

			// Proceed to the login form.
			$message = JText::_('COM_USERS_REMIND_REQUEST_SUCCESS');
			$this->setRedirect(JRoute::_($route, false), $message);
			return true;
		}
	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function resend()
	{
		// Check for request forgeries
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
	}
}
