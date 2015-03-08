<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * User controller class.
 */
class UuControllerUser extends JControllerForm
{

    function __construct() {
        $this->view_list = 'users';
        parent::__construct();
    }

    /**
     * Overrides parent save method to check the submitted passwords match.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   1.6
     */
    public function save($key = null, $urlVar = null)
    {

        //load message from com_users
        $lang =JFactory::getLanguage();
        $lang->load('com_users', JPATH_ADMINISTRATOR, 'en-GB', false);

        $data = JRequest::getVar('jform', array(), 'post', 'array');
        //TODO put this directly int javascript validation
        // TODO: JForm should really have a validation handler for this.
        if (isset($data['password']) && isset($data['password2']))
        {
            // Check the passwords match.
            if ($data['password'] != $data['password2'])
            {

                $this->setMessage(JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH'), 'warning');
                $this->setRedirect(JRoute::_('index.php?option=com_uu&view=user&layout=edit&user_id='.(int)$data['user_id'], false));
                return false;
            }

            unset($data['password2']);
        }

        //from com_users model user

        JLoader::import('joomla.application.component.model');
        //JLoader::import( 'User', JPATH_ADMINISTRATOR .  '/components/com_users/models' );
        require_once(JPATH_ADMINISTRATOR .'/components/com_users/models/user.php');
        //JLoader::register( 'UsersModelUser', JPATH_ADMINISTRATOR .  '/components/com_users/models' );

        if (UU_J30){
            $userModel = JModelLegacy::getInstance( 'User', 'UsersModel' );
        } else {
            $userModel = JModel::getInstance( 'User', 'UsersModel' );
        }

        //used to save a user
        JTable::addIncludePath(JPATH_ROOT.'/libraries/joomla/database/table');

        if (!$userModel->save($data)) {
            $errors = $userModel->getErrors();
            $this->setMessage($errors[0],'error');
            $this->setRedirect(JRoute::_('index.php?option=com_uu&view=user&layout=edit&user_id='.(int)$data['user_id'], false));
            return false;
        }
        return parent::save();

    }


}