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
jimport('joomla.plugin.plugin');

class dropfilesPluginBase extends JPlugin {

    public $name;
    protected $options;


    public function __construct(&$subject, $config = array()) {
        JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR . '/components/com_dropfiles/classes/dropfilesBase.php');
        DropfilesBase::setDefine();
        //Load language from non default positiond
        DropfilesBase::loadLanguage();
        parent::__construct($subject, $config);
    }
    
    public function getThemeName(){
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration('.themesblock a.themebtn.'.$this->name.' {background-image: url('.JURI::root().'/plugins/dropfiles/'.$this->name.'/btn.gif) }');
        return array('name'=>ucfirst($this->name),'id'=>$this->name);
    }
    
    
    /*
     * Load the form fields for the plugin
     */
    public function getConfigForm($theme,&$form){        
        if($theme===''){
            $theme = 'default';
        }
        if($theme!='' && $theme!= $this->name){
            return null;
        }
        $formfile = JPATH_PLUGINS.DIRECTORY_SEPARATOR.$this->_type.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.'/form.xml';
        if(!file_exists($formfile)){
            return null;
        }
        $form->loadFile($formfile);
        return ;
    }
    
}

?>
