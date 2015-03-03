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
class plgDropfilesthemesTable extends dropfilesPluginBase
{
    
    public $name = 'table';
    
    public function onShowFrontCategory($options){
        $this->options = $options;
        
        if($this->options['theme']!= $this->name){
            return null;
        }
        $doc = JFactory::getDocument();
        JHtml::_('behavior.framework');
        $this->componentParams = JComponentHelper::getParams('com_dropfiles');
        if($this->componentParams->get('jquerybase',true)){
            JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/dropfilesBase.php');
            if(dropfilesBase::isJoomla30()){
                JHtml::_('jquery.framework');
            }else{
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-1.8.3.js');
                $doc->addScript(JURI::base('true').'/components/com_dropfiles/assets/js/jquery-noconflict.js');
            }
        }
        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/table/js/handlebars-1.0.0-rc.3.js');
        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/table/js/script.js');
        $doc->addScript(JURI::base('true').'/plugins/dropfilesthemes/table/js/jquery.mediaTable.js');
        $doc->addStyleSheet(JURI::base('true').'/plugins/dropfilesthemes/table/css/style.css');
        $doc->addStyleSheet(JURI::base('true').'/plugins/dropfilesthemes/table/css/jquery.mediaTable.css');
        
        $this->componentParams = JComponentHelper::getParams('com_dropfiles');

        JText::script('COM_DROPFILES_DEFAULT_FRONT_COLUMNS'); 
        
        $content = '';
        if(!empty($this->options['files']) || dropfilesBase::loadValue($this->params,'table_showsubcategories',1)==1){
            $this->files = $this->options['files'];
            $this->category = $this->options['category'];
            $this->categories = $this->options['categories'];
            $this->params = $this->options['params'];

            $this->tableclass = '';
            $this->dropfilesclass = '';
            if(dropfilesBase::loadValue($this->params,'table_styling',true)){
                $this->tableclass .= 'table table-bordered ';
                if(dropfilesBase::loadValue($this->params,'table_showborderbg',true)){
                    $this->tableclass .= 'table-striped';
                }
            }
            if(dropfilesBase::loadValue($this->params,'table_stylingmenu',true)){
                $this->dropfilesclass .= 'colstyle';
            }
            
            ob_start();
            require dirname(__FILE__).DIRECTORY_SEPARATOR.'tpl.php';
            $content = ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }    

    public function onShowFrontFile($options){
        $this->options = $options;
        if($this->options['theme']!= $this->name){
            return null;
        }

        $doc = JFactory::getDocument();
        $doc->addStyleSheet(JURI::base('true').'/plugins/dropfilesthemes/table/css/style.css');
        
        $content = '';
        if(!empty($this->options['file'])){
            $this->file = $this->options['file'];
            $this->params = $this->options['params'];
                        
            ob_start();
            require dirname(__FILE__).DIRECTORY_SEPARATOR.'tplsingle.php';
            $content = ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }    
    
}
