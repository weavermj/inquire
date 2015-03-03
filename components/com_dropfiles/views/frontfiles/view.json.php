<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class DropfilesViewFrontfiles extends JViewLegacy
{
	function display($tpl = null)
	{
            JLoader::register('DropfilesFilesHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/files.php');
            $model = $this->getModel();
            $modelCat = $this->getModel('frontcategory');
            $category = $modelCat->getCategory();
            $modelConfig = JModelLegacy::getInstance('Frontconfig','dropfilesModel');
            

            if(!$category){
                return false;
            }
            
            $params = $modelConfig->getParams($category->id);
            
            if($category->type=='googledrive'){
                
                $user	= JFactory::getUser();
                $access = $user->getAuthorisedViewLevels();
                if(!in_array($category->access,$access)){
                    return false;
                }
                JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
                $google = new dropfilesGoogle();
                if(isset($params->params->ordering)){
                    $ordering = $params->params->ordering;
                }else{
                    $ordering = 'ordering';
                }
                if(isset($params->params->orderingdir)){
                    $direction = $params->params->orderingdir;
                }else{
                    $direction = 'asc';
                }
                $files = $google->listFiles($category->cloud_id,$ordering,$direction);
            }else{
                $files = $this->get('Items');                

                $model->getState('onsenfout'); //To autopopulate state
                if(isset($params->params->ordering)){                
                    $model->setState('list.ordering',$params->params->ordering);
                }
                if(isset($params->params->orderingdir)){
                    $model->setState('list.direction',$params->params->orderingdir);
                }            
                $files = $model->getItems();
            }
            
            $content = new stdClass();
            $content->files = DropfilesFilesHelper::addInfosToFile($files,$category);
            $content->category = $category;
            
            echo json_encode($content);
            JFactory::getApplication()->close();
        }
}
