<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ROOT.'/administrator/components/com_uu/legacy/view.php';
jimport('joomla.application.component.view');


/**
 * View class for a list of Uu.
 */
class UuViewUsers extends LegacyView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

        $model = $this->getModel();
        $usermissing = $model->checkSyncUserMissing();
        $userextra = $model->checkSyncUserExtra();

        if ($usermissing > 0 || $userextra > 0) {
            $url = 'index.php?option=com_uu&task=users.sync';
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_UU_USERS_NOT_SYNCHRONIZED',$url));
        }

        //check plugin redirect activation
        $uuRedirect = JPluginHelper::isEnabled("system","uuredirect");
        if (!$uuRedirect){
            JFactory::getApplication()->enqueueMessage(JText::_('COM_UU_PLUGIN_REDIRECT_NOT_PUBLISHED'),'warning');
        }

        //check plugin user synchro
        $uuUser = JPluginHelper::isEnabled("user","ultimateuser");
        if (!$uuUser){
            JFactory::getApplication()->enqueueMessage(JText::_('COM_UU_PLUGIN_ULTIMATEUSER_NOT_PUBLISHED'),'warning');
        }

        //TODO regarder ici un problème de cache
        //check new version
        require_once(JPATH_ADMINISTRATOR.'/components/com_uu/liveupdate/liveupdate.php');
        $updateInfo = LiveUpdate::getUpdateInformation(true);
        if ($updateInfo->hasUpdates) {
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_UU_NEW_VERSION_RELEASED',$updateInfo->version),'warning');
        }

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

        // Import CSS
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_uu/assets/css/uu.css');

		$this->addToolbar();
        
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        UuHelper::addSubmenu($view);
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/uu.php';

		$state	= $this->get('State');
		$canDo	= UuHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_UU_TITLE_USERS'), 'users.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/user';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    //JToolBarHelper::addNew('user.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('user.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('users.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('users.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'users.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('users.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('users.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'users.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('users.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_uu');
            JToolBarHelper::divider();
            JToolBarHelper::custom( 'users.export' , 'csv' , 'csv' , JText::_( 'COM_UU_CONFIGURATION_EXPORT_TO_CSV' ),false );
		}


	}
}
