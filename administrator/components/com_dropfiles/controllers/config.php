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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class DropfilesControllerConfig extends JControllerForm {
    
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$data  = JRequest::getVar('jform', array(), 'post', 'array');
		$context = "$this->option.edit.$this->context";
	
		// Access check.
                $canDo = DropfilesHelper::getActions();
                if(!$canDo->get('core.edit')){
                    if($canDo->get('core.edit.own')){
                        $category = $model->getItem(JFactory::getApplication()->input->getInt('id', 0));
                        if($category->created_user_id != JFactory::getUser()->id){
                            $this->exit_status('not permitted');
                        }
                    }else{
                        $this->exit_status('not permitted');
                    }
                }
                
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item.'&tmpl=component'
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

	

		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
					? $this->text_prefix
					: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

                $app->setUserState($context . '.data', null);

                // Redirect to the list screen.
                $this->setRedirect(
                        JRoute::_(
                                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&tmpl=component'
                                . $this->getRedirectToListAppend(), false
                        )
                );

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}

        
        public function getRedirectToItemAppend($recordId = NULL, $urlVar = 'id'){
            $append = parent::getRedirectToItemAppend($recordId, $urlVar);

            $format = JRequest::getCmd('format', 'raw');

            // Setup redirect info.
            if ($format)
            {
                    $append .= '&format=' . $format;
            }
            return $append; 
        }    

        protected function allowEdit($data = array(), $key = 'id')
        {
            return true;
        }
        
        public function setTheme(){
            $theme = JRequest::getCmd('theme');
            $id = JRequest::getInt('id');
            
            $canDo = DropfilesHelper::getActions();
            if(!$canDo->get('core.edit')){
                if($canDo->get('core.edit.own')){
                    $modelC = $this->getModel('category');
                    $category = $modelC->getItem($id);
                    if($category->created_user_id != JFactory::getUser()->id){
                        $this->exit_status('not permitted');
                    }
                }else{
                    $this->exit_status('not permitted');
                }
            }
            
            JPluginHelper::importPlugin('dropfilesthemes');
            $dispatcher = JDispatcher::getInstance();
            $themesObj = $dispatcher->trigger('getThemeName');

            $themes = array();
            foreach ($themesObj as $value) {
                $themes[] = $value['id'];
            }
            
            if(!in_array($theme, $themes)){
                $theme = 'default';
            }
            
            $model = $this->getModel();
            if($model->setTheme($theme,$id)){
                $result = true;
            }else{
                $result = false;
            }
            echo json_encode($result);
            JFactory::getApplication()->close();
        }
        
	/**
        * Return a json response
        * @param $status
        * @param array $datas array of datas to return with the json string
        * 
        */
       private function exit_status($status,$datas=array()){
               $response = array('response'=>$status,'datas'=>$datas);            
               echo json_encode($response);
               JFactory::getApplication()->close();
       }        
}