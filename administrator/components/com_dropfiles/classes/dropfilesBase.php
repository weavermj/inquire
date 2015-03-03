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

class dropfilesBase {

    /**
     * 
     */
    public static function initComponent(){
        //Load language from non default position
        self::loadLanguage();

        // Register helper class
        JLoader::register('DropfilesHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/dropfiles.php');
        JLoader::register('DropfilesFilesHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/files.php');
        // Register helper class
        JLoader::register('DropfilesComponentHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/component.php');
        JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
        
        //Load scripts and stylesheets
        $document = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_dropfiles');
        
        if(self::isJoomla30()){
            JHtml::_('jquery.framework');
            if(JFactory::getApplication()->isSite()){
                $document->addScript(JURI::root().'components/com_dropfiles/assets/js/modal.min.js');
                $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/modal.min.css');
            }
            $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery-ui-1.9.2.custom.min.js');
            $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/ui-lightness/jquery-ui-1.9.2.custom.min.css');
            $app = JFactory::getApplication();
            if($app->isSite()){
                $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/bootstrap.min.css');
            }
        }else{
            $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery-1.8.3.js');
            $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery-noconflict.js');
            
            $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery-ui-1.9.2.custom.min.js');
            $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/ui-lightness/jquery-ui-1.9.2.custom.min.css');

            $document->addScript(JURI::root().'components/com_dropfiles/assets/js/bootstrap.min.js');
            $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/bootstrap.min.css');
        }
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/icons.min.css');
        //For touch devices
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery.ui.touch-punch.min.js');

        $app = JFactory::getApplication();
        if($app->isSite()){
            $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/frontstyle.css');
        }
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/upload.min.css');
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/style.css');
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/jaofiletree.css');
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/jquery.restable.css');
                
        
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/dropfiles.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery.filedrop.min.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery.textselect.min.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery.nestable.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/bootbox.min.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jaofiletree.js');
        $document->addScript(JURI::root().'components/com_dropfiles/assets/js/jquery.restable.js');
        
        dropfilesBase::setDefine();
    }
    
    public static function initFrontComponent(){
        //Load language from non default position
        self::loadLanguage();

        JHtml::_('behavior.framework');
        // Register helper class
        JLoader::register('DropfilesHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/dropfiles.php');
        // Register helper class
        JLoader::register('DropfilesComponentHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/component.php');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root().'components/com_dropfiles/assets/css/front.css');
        dropfilesBase::setDefine();
    }


    /**
     * Define values needed by the component and plugin
     */
    public static function setDefine(){
//        $path = "file_path";
//        $paramsmedia = JComponentHelper::getParams('com_media');
//        if(!defined('COM_MEDIA_BASE')){
//            define('COM_MEDIA_BASE',	JPATH_ROOT.'/'.$paramsmedia->get($path, 'images'));
//        }
//        if(!defined('COM_MEDIA_BASEURL')){    
//            define('COM_MEDIA_BASEURL', JURI::root().$paramsmedia->get($path, 'images'));
//        }
        
    }

    /**
     * Search a param into the component config
     * @param string $path
     * @param type $default
     * @return param 
     */
    public static function getParam($path,$default=null){
        $params = JComponentHelper::getParams('com_dropfiles');
        return $params->get($path,$default);
    }
    
    /**
     * method to retrieve the path to the component full width image directory
     * @param type $id_category 
     * @return string directory path
     */
    public static function getFullPicturePath($id_category){
        if($id_category>0){
            return COM_MEDIA_BASE.'/com_dropfiles/'.(int)$id_category.'/full/';
        } else {
            return '';
        }
    }
    
    /**
     * method to retrieve the path to the component image directory
     * @param type $id_category 
     * @return string directory path
     */
    public static function getFilesPath($id_category=null){
        if($id_category===null){
            return JPATH_ROOT.'/media/com_dropfiles/';
        }
        return JPATH_ROOT.'/media/com_dropfiles/'.$id_category.'/';
    }

    /**
     * method to retrieve the path to the component image directory
     * @param type $id_category 
     * @return string directory path
     */
    public static function getVersionPath($id_category){
        $path = self::getFilesPath($id_category);
        return $path.'versions'.DIRECTORY_SEPARATOR;
    }
    
    /**
     * Method to return the current joomla version
     * @param string $format
     * @return string version
     */
    public static function getJoomlaVersion($format='short'){
        $method = 'get' . ucfirst($format) . "Version";

        // Get the joomla version
        $instance = new JVersion();
        $version = call_user_func(array($instance, $method));

        return $version;        
    }
    
    /**
     * Method to check if current joomla version is 3.X
     * @return boolean 
     */
    public static function isJoomla30(){
        if(version_compare(self::getJoomlaVersion(),'3.0')>=0){
            return true;
        }
        return false;
    }
       
    
    /**
     * Method to check if current joomla version is 2.5
     * @return boolean 
     */
    public static function isJoomla25(){        
        if(version_compare(self::getJoomlaVersion(),'2.5',">=") && version_compare('3',self::getJoomlaVersion(),'>')){
            return true;
        }
        return false;
    }
       
    
    /**
     * Check if a component is installed and activated 
     * @param string $extension
     * @param string $type
     * @return boolean 
     */
    public static function isExtensionActivated($extension,$type=''){
        $db = JFactory::getDbo();
        $query = 'SELECT extension_id FROM #__extensions WHERE element='.$db->quote($extension);

        if($type!=''){
            $query.=' AND type='.$db->quote($type);
        }
        $query.=' AND enabled=1';
        $db->setQuery($query);
        if($db->query()){
            if($db->getNumRows()>0){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Method to set config parameters
     * @param array $datas
     * @return boolean 
     */
    public static function setParams($datas){ 
        return dropfilesComponentHelper::setParams($datas);
    }
    
    
    /**
     * Load global file language
     */
    public static function loadLanguage(){
        $lang = JFactory::getLanguage();
        $lang->load('com_dropfiles',JPATH_ADMINISTRATOR.'/components/com_dropfiles',null,true);
        $lang->load('com_dropfiles.sys',JPATH_ADMINISTRATOR.'/components/com_dropfiles',null,true);
    }
    
    public static function loadValue($var,$value,$default=''){
        if(is_object($var) && isset($var->$value)){
            return $var->$value;
        }elseif(is_array($var) && isset($var[$value])){
            return $var[$value];
        }
        return $default;
    }

    /**
     * Check if htaccess with limit directive installed
     * @return true if installed false if not installed null if cant check
     */
    public function isHtaccesOk(){
        $url = JURI::root().'media/com_dropfiles/index.html';
//        if(function_exists('curl_init')){
//            $ch = curl_init($url);
//            if(!curl_errno($ch)){
//                $info = curl_getinfo($ch);
//                var_dump($info);
//                if($info['http_code']==403){
//                    curl_close($ch);
//                    return true;
//                }else{
//                    return false;
//                }
//            }
//            curl_close($ch);
//        }else{
            if(function_exists('get_headers')){
                $headers = get_headers($url,1);
                if($headers[0]=='HTTP/1.1 403 Forbidden'){
                    return true;
                }else{
                    return false;
                }
            }
//        }
        return null;
    }
    
   /**
    * Check on Joomunited website the latest version number of the component
    * @param string $extension
    * @return false or version number (string)
    */
   public static function getLastExtensionVersion($extension=null){
        if($extension===null){
            $extension = JFactory::getApplication()->input->getString('option', '');
        }
        if (ini_get("allow_url_fopen") == 1) {
            $content = file_get_contents('http://www.joomunited.com/UPDATE-INFO/updates.json');
        }else{
            return false;
        } 
        $json = json_decode($content);
        return $json->extensions->$extension->version;       
   }
   
   public static function getExtensionVersion($extension=null,$type=''){
        if($extension===null){
             $extension = JFactory::getApplication()->input->getString('option', '');
        }
        $db = JFactory::getDbo();
        $query = 'SELECT manifest_cache FROM #__extensions WHERE element='.$db->quote($extension);

        if($type!=''){
            $query.=' AND type='.$db->quote($type);
        }
        $db->setQuery($query);
        if($db->query()){
            $manifest = $db->loadResult();
            $json = json_decode($manifest);
            if(property_exists($json, 'version')){
                return $json->version;
            }
        }
        return false;
   }
}

?>
