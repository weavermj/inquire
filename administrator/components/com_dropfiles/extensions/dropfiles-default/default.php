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

JLoader::register('dropfilesPluginBase', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesPluginBase.php');

/**
 * Content Plugin.
 *
 * @package    dropfiles
 * @subpackage Plugin
 */
class plgDropfilesthemesDefault extends dropfilesPluginBase
{
    public $name = 'default';
    
    public function onShowFrontCategory($options){
        $this->options = $options;
        if(@$this->options['theme']!='' && $this->options['theme']!= $this->name){
            return null;
        }
        $this->componentParams = JComponentHelper::getParams('com_dropfiles');
        $doc = JFactory::getDocument();
        if($this->componentParams->get('jquerybase',true)){
            JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/dropfilesBase.php');
            if(dropfilesBase::isJoomla30()){
                JHtml::_('jquery.framework');
            }else{
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-1.8.3.js');
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-noconflict.js');
            }
        }
        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/default/js/handlebars-1.0.0-rc.3.js');
        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/default/js/script.js');
        $doc->addStyleSheet(JURI::base('true').'/plugins/dropfilesthemes/default/style.css');

        $content = '';
        if(!empty($this->options['files']) || dropfilesBase::loadValue($this->params,'showsubcategories',1)==1){
            $this->files = $this->options['files'];
            $this->category = $this->options['category'];
            $this->categories = $this->options['categories'];
            $this->params = $this->options['params'];
            
            $style  = '.file {margin : '.dropfilesBase::loadValue($this->params,'margintop',5).'px '.dropfilesBase::loadValue($this->params,'marginright',5).'px '.dropfilesBase::loadValue($this->params,'marginbottom',5).'px '.dropfilesBase::loadValue($this->params,'marginleft',5).'px;}';
            $doc->addStyleDeclaration($style);
            
            ob_start();
            require dirname(__FILE__).DIRECTORY_SEPARATOR.'tpl.php';
            $content = ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }    

    public function onShowFrontFile($options){
        $this->options = $options;
        if(@$this->options['theme']!='' && $this->options['theme']!= $this->name){
            return null;
        }

        $doc = JFactory::getDocument();
//        $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-1.8.3.js');
//        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/default/js/handlebars-1.0.0-rc.3.js');
//        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/default/js/script.js');
        $doc->addStyleSheet(JURI::base('true').'/plugins/dropfilesthemes/default/style.css');
        
        $content = '';
        if(!empty($this->options['file'])){
            $this->file = $this->options['file'];
            $this->params = $this->options['params'];
            
            $style  = '.file {margin : '.dropfilesBase::loadValue($this->params,'margintop',5).'px '.dropfilesBase::loadValue($this->params,'marginright',5).'px '.dropfilesBase::loadValue($this->params,'marginbottom',5).'px '.dropfilesBase::loadValue($this->params,'marginleft',5).'px;}';
            $doc->addStyleDeclaration($style);
            
            ob_start();
            require dirname(__FILE__).DIRECTORY_SEPARATOR.'tplsingle.php';
            $content = ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }
        
}
