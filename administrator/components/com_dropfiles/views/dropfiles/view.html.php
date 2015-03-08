<?php
/** 
 * Dropfiles
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */


defined('_JEXEC') or die;


class DropfilesViewDropfiles extends JViewLegacy
{
	
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
                $this->canDo = DropfilesHelper::getActions();
                $this->params = $params = JComponentHelper::getParams('com_dropfiles');
                
                $model = $this->getModel('categories');
//                $this->setState('list.limit',100000000);
                JFactory::getApplication()->setUserState('list.limit', 100000);
                $this->categories = $model->getItems();

                
                $user = JFactory::getUser();
                $params = JComponentHelper::getParams('com_dropfiles');
                if($params->get('import') && !JRequest::getBool('caninsert',0) && $user->authorise('core.admin')){
                    $this->importFiles = true;
                }else{
                    $this->importFiles = false;
                }

                $this->setLayout(JRequest::getCmd('layout','default'));
                
		parent::display($tpl);
                
                $app = JFactory::getApplication();
                if($app->isAdmin()){
                    $this->addToolbar();
                }
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{

		$canDo	= DropfilesHelper::getActions();

		JToolBarHelper::title(JText::_('COM_DROPFILES_MAIN_PAGE'), 'dropfiles.png');


		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_dropfiles');
                        
//                        if($this->params->get('google_credentials','')===''){
                            $toolbar = JToolBar::getInstance();
			    $toolbar->appendButton( 'popup', 'help', JText::_('COM_DROPFILES_VIEW_SUPPORT'), 'index.php?option=com_dropfiles&view=support&tmpl=component', 700, 600,0,0 );
                            $toolbar->appendButton( 'popup', 'googledrive', JText::_('COM_DROPFILES_VIEW_BTN_GOOGLE_LOGIN'), 'index.php?option=com_dropfiles&view=googledrive&tmpl=component', 700, 600,0,0 );
//                        }
		}

		JToolBarHelper::divider();
	}
}
