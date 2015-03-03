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
class plgDropfilesthemesIndexer extends dropfilesPluginBase
{
    public $name = 'indexer';
    
    public function onShowFrontCategory($options){
        $this->options = $options;
        if($this->options['theme']!= $this->name){
            return null;
        }
        
        $content = '';
        if(!empty($this->options['files']) || dropfilesBase::loadValue($this->params,'showsubcategories',1)==1){
            $this->files = $this->options['files'];
            $this->category = $this->options['category'];
            $this->categories = $this->options['categories'];
            $this->params = $this->options['params'];

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
        
    public function getThemeName() {
        return null;
    }
}
