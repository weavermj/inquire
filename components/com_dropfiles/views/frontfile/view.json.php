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
class DropfilesViewFrontfile extends JViewLegacy
{
	function display($tpl = null)
	{
                   
            $model = $this->getModel('frontfile');
            $id = JFactory::getApplication()->input->getString('id', 0);
            $catid = JFactory::getApplication()->input->getInt('catid', 0);
            $modelCat = $this->getModel('frontcategory');
            $category = $modelCat->getCategory($catid);
            
            if($category->type=='googledrive'){
                JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
                $google = new dropfilesGoogle();
                $file = $google->getFileInfos($id,$category->cloud_id);
            }else{
                $file = $model->getFile($id);
                if (!$file) {
                    return json_encode(new stdClass());
                }
            }
            
            $user	= JFactory::getUser();
            $groups	= $user->getAuthorisedViewLevels();
            $catmod = JCategories::getInstance('Dropfiles');
            $jcategory = $catmod->get($catid);
            if (!in_array($jcategory->access, $groups)) {
                    return json_encode(new stdClass());
            }
            
            $content = new stdClass();
            $content->file = DropfilesFilesHelper::addInfosToFile( json_decode(json_encode($file), false),$category);
            
            echo json_encode($content);
            JFactory::getApplication()->close();
        }
}
