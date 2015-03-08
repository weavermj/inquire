<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_COMPONENT .'/helpers/config.php';
require_once JPATH_COMPONENT .'/helpers/ustring.php';
require_once JPATH_COMPONENT .'/helpers/uu.php';
require_once JPATH_COMPONENT .'/helpers/jaxresponse.php';
require_once JPATH_COMPONENT .'/libraries/uufieldinterface.php';

$conf = new UuConfig();
if (!$conf->get('enable_user_registration')) {
    $app = JFactory::getApplication();
    $app->redirect('index.php',JText::_('COM_UU_USER_REGISTRATION_NOT_ENABLED'));
    jexit();
}

// Execute the task.
$controller	= JControllerLegacy::getInstance('Uu');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
