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


//-- No direct access
defined('_JEXEC') || die('=;)');


jimport('joomla.plugin.plugin');
jimport( 'joomla.application.categories' );

/**
 * Content Plugin.
 *
 * @package    dropfiles
 * @subpackage Plugin
 */
class plgK2dropfiles extends JPlugin
{
  
    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string  $context     The context for the content passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     *
     * @return string
     */
    public function onK2PrepareContent(&$item,&$params,$limitstart)//onContentPrepare($context, &$article, &$params, $limitstart)
    {
//        $app = JFactory::getApplication();
//        if($app->isAdmin()){
//            return true;
//        }
        JLoader::register('DropfilesFilesHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/files.php');
//        $cont = explode('.', $context);
//        if($cont[0]=='com_content'){
            //Replace category
            $item->text = preg_replace_callback('@<img.*?data\-dropfilescategory="([0-9]+)".*?>@', array($this,'replace'),$item->text);
            //Replace single file
            $item->text = preg_replace_callback('@<img.*?data\-dropfilesfile="([[:alnum:]_]+)".*?>@', array($this,'replaceSingle'),$item->text);
//        }
        return true;
    }
    
    private function replace($match){
        jimport('joomla.application.component.model');
	JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesBase.php');
        JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_dropfiles/models/','DropfilesModelFrontfiles');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_dropfiles/models/','DropfilesModelFrontconfig');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_dropfiles/models/','DropfilesModelCategories');
        dropfilesBase::loadLanguage();
	
        $modelFiles = JModelLegacy::getInstance('Frontfiles','dropfilesModel');
        $modelConfig = JModelLegacy::getInstance('Frontconfig','dropfilesModel');
        $modelCategories = JModelLegacy::getInstance('Frontcategories','dropfilesModel');
        $modelCategory = JModelLegacy::getInstance('Frontcategory','dropfilesModel');
        
        $modelFiles->getState('onsenfout'); //To autopopulate state
        $modelFiles->setState('filter.category_id', (int)$match[1]); 
        $modelCategories->getState('onsenfout'); //To autopopulate state
        $modelCategories->setState('category.id', (int)$match[1]);


        $category = $modelCategory->getCategory((int)$match[1]);
        if(!$category){
            return '';
        }
        
        $categories = $modelCategories->getItems();
        $params = $modelConfig->getParams($category->id);
        
        if($category->type=='googledrive'){
            $google = new dropfilesGoogle();
            if(isset($params->params->ordering)){
                $ordering = $params->params->ordering;
            }else{
                $ordering = 'ordering';
            }
            if(isset($params->params->orderingdir)){
                $direction  = $params->params->orderingdir;
            }else{
                $direction = 'asc';
            }
            $files = $google->listFiles($category->cloud_id,$ordering,$direction);
            if($files===false){
                JFactory::getApplication()->enqueueMessage($google->getLastError(), 'error');
                return '';
            }
        }else{
            if(isset($params->params->ordering)){
                $modelFiles->setState('list.ordering',$params->params->ordering);
            }
            if(isset($params->params->orderingdir)){
                $modelFiles->setState('list.direction',$params->params->orderingdir);
            }
            $files = $modelFiles->getItems();
        }
        $files = DropfilesFilesHelper::addInfosToFile($files,$category);
        
        if($this->context === 'com_finder.indexer'){
            $theme = 'indexer';
        }else{
            if(!empty($params)){
                $theme = $params->theme;
            }else{
                $theme = 'default';
            }
        }

        JPluginHelper::importPlugin('dropfilesthemes');
        $dispatcher = JDispatcher::getInstance();
        $result = $dispatcher->trigger('onShowFrontCategory', array(array('files' => $files,'category'=>$category,'categories'=>$categories,'params'=>$params->params,'theme'=>$theme)));

        if(!empty($result[0])){
            $componentParams = JComponentHelper::getParams('com_dropfiles');
            if($componentParams->get('usegoogleviewer',1)==1){
                $doc = JFactory::getDocument();
                if($componentParams->get('jquerybase',true)){
                    JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/dropfilesBase.php');
                    if(dropfilesBase::isJoomla30()){
                        JHtml::_('jquery.framework');
                    }else{
                        $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-1.8.3.js');
                        $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-noconflict.js');
                    }
                }
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery.colorbox-min.js');
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/colorbox.init.js');
                $doc->addStyleSheet(JURI::base('true').'/components/com_dropfiles/assets/css/colorbox.css');
            }
            return $result[0];
        }
        return '';
    }
    
    /**
     * Replace a single image
     * @param type $match
     * @return string 
     */
    private function replaceSingle($match){
        jimport('joomla.application.component.model');
	JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesBase.php');
        JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_dropfiles/models/','DropfilesModelFrontfile');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_dropfiles/models/','DropfilesModelFrontconfig');
        dropfilesBase::loadLanguage();
	
        $modelFile = JModelLegacy::getInstance('Frontfile','dropfilesModel');
        $modelConfig = JModelLegacy::getInstance('Frontconfig','dropfilesModel');
        $modelCategory = JModelLegacy::getInstance('Frontcategory','dropfilesModel');

        preg_match('@.*data\-dropfilesfilecategory="([0-9]+)".*@', $match[0],$matchCat);
        
        if(!empty($matchCat)){
            $category = $modelCategory->getCategory((int)$matchCat[1]);
            if(!$category){
                return '';
            }
        }else{
            $file = $modelFile->getFile((int)$match[1]);
            if($file===null){
                return '';
            }
            $category = $modelCategory->getCategory($file->id_cat);
            if(!$category){
                return '';
            }
        }
        
        if($category->type=='googledrive'){
            $google = new dropfilesGoogle();
            $file = $google->getFileInfos($match[1],$category->cloud_id);
        }else{
            $file = $modelFile->getFile((int)$match[1]);
        }
        $file = DropfilesFilesHelper::addInfosToFile(json_decode(json_encode($file), false),$category);
        
        //Access check already done in category model
        $catmod = JCategories::getInstance('Dropfiles');
        $jcategory = $catmod->get($category->id);
        if (!$jcategory) {
                return '';
        }
        
        $params = $modelConfig->getParams($jcategory->id);
        
        if($this->context === 'com_finder.indexer'){
            $theme = 'indexer';
        }else{
            if(!empty($params)){
                $theme = $params->theme;
            }else{
                $theme = 'default';
            }
        }
        
        JPluginHelper::importPlugin('dropfilesthemes');
        $dispatcher = JDispatcher::getInstance();
        $result = $dispatcher->trigger('onShowFrontFile', array(array('file' => $file,'category'=>$category,'params'=>$params->params,'theme'=>$theme)));
        
        if(!empty($result[0])){
            $componentParams = JComponentHelper::getParams('com_dropfiles');
            if($componentParams->get('usegoogleviewer',1)==1){
                $doc = JFactory::getDocument();
                if($componentParams->get('jquerybase',true)){
                    JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/dropfilesBase.php');
                    if(dropfilesBase::isJoomla30()){
                        JHtml::_('jquery.framework');
                    }else{
                        $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-1.8.3.js');
                        $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-noconflict.js');
                    }
                }
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery.colorbox-min.js');
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/colorbox.init.js');
                $doc->addStyleSheet(JURI::base('true').'/components/com_dropfiles/assets/css/colorbox.css');
            }
            return $result[0];
        }
        return '';
    }

}
    