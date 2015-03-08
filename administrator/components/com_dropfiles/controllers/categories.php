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

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'categories.php');
jimport( 'joomla.filesystem.folder' );

class DropfilesControllerCategories extends CategoriesControllerCategories
{
    
    
    
    /**
    * Proxy for getModel
    *
    * @param	string	$name	The model name. Optional.
    * @param	string	$prefix	The class prefix. Optional.
    *
    * @return	object	The model.
    * @since	1.6
    */
   function getModel($name = 'Category', $prefix = 'DropfilesModel', $config = array('ignore_request' => true))
   {
           $model = parent::getModel($name, $prefix, $config);
           return $model;
   }
    
	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JRequest::getInt('id_category',0);

		if($cid){
			// Get the model.
			$model = $this->getModel();
                        JFactory::getApplication()->setUserState('list.limit', 100000);
                        
                        $modelCats = $this->getModel('categories');                        
                        $modelCats->setState('category.id',$cid);
                        $items = $modelCats->getItems();
                        
                        $canDo = DropfilesHelper::getActions();
                        if($canDo->get('core.delete')){
                            if(!$canDo->get('core.edit')){
                                if($canDo->get('core.edit.own')){
                                    $gallery = $model->getItem($cid);
                                    if($gallery->created_user_id != JFactory::getUser()->id){
                                        $this->exit_status('not permitted');
                                    }
                                }else{
                                    $this->exit_status('not permitted');
                                }
                            }
                        }else{
                            $this->exit_status('not permitted');
                        }
                        
                        //little hack because joomla always delete children, bug in version 3.1
                        $joomla31 = false;
                        if(version_compare(dropfilesBase::getJoomlaVersion(), '3.1')){
                            $joomla31 = true;
                        }
                        
                        
                        $errors = array();
                        // Remove the items.
                        $modelCat = $this->getModel('category');
                        $category = $modelCat->getCategory($cid);
                        if ($model->delete($cid))
                        {
                                //todo : delete files from database                                
                                if($category->type == 'googledrive'){
                                    $google = new dropfilesGoogle();
                                    if(!$google->delete($category->cloud_id)){
                                        $errors[] = 'error while deleting directory, please delete google drive folder manually'; //todo: translate
                                    }
                                }else{
                                    $path = dropfilesBase::getFilesPath($cid[0]);
                                    if(is_dir($path)){
                                        if(!JFolder::delete($path)){
                                            $errors[] = 'error while deleting directory, please delete folder '.$path.' manually'; //todo: translate
                                        }
                                    }
                                }
                                //delete children
                                foreach ($items as $item) {
                                    if($item->id==$cid[0]){
                                        continue;
                                    }
                                    $id = $item->id;
                                    $category = $modelCat->getCategory($id);
                                    if($model->delete($id) || $joomla31 ){                                        
                                        if($category->type == 'googledrive'){
                                            if(!$google->delete($category->cloud_id)){
                                                $errors[] = 'error while deleting directory, please delete google drive folder manually'; //todo: translate
                                            }
                                        }else{
                                            $path = dropfilesBase::getFilesPath($item->id);
                                            if(is_dir($path)){
                                                if(!JFolder::delete($path)){
                                                    $errors[] = 'error while deleting directory, please delete folder '.$path.' manually'; //todo: translate
                                                }
                                            }
                                        }
                                    }else{
                                        $errors[] = 'error while deleting category'; //todo: translate
                                    }
                                }
                        }else
                        {
                            $errors[] = $model->getError();
                        }
                        if(count($errors)){
                            $this->exit_status(implode('<br/>', $errors));
                        }
                        $this->exit_status(true);
			
		}

	}

        public function order() {
            $position = JRequest::getCmd('position','after');
            $pk = JRequest::getInt('pk',null);
            $ref = JRequest::getInt('ref');
            $model = $this->getModel();
            $canDo = DropfilesHelper::getActions();
            if(!$canDo->get('core.edit')){
                if($canDo->get('core.edit.own')){
                    $category = $model->getItem($pk);
                    if($category->created_user_id != JFactory::getUser()->id){
                        $this->exit_status('not permitted');
                    }
                }else{
                    $this->exit_status('not permitted');
                }
            }
            
            if($ref==0){
                $ref=1;
            }
            if($position!=='after'){
                $position = 'first-child';
            }
            
            $table = $model->getTable();
            if($table->moveByReference($ref,$position,$pk)){
                $this->exit_status(true,$pk.' '.$position.' '.$ref);
            }
            $this->exit_status('problem');
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
