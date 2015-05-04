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


class pkg_dropfilesthemespack1InstallerScript{
            
        public function __construct() {
            JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR . '/components/com_dropfiles/classes/dropfilesBase.php');
            JLoader::register('DropfilesInstallerHelper', JPATH_ADMINISTRATOR . '/components/com_dropfiles/helpers/installer.php');
            JLoader::register('DropfilesComponentHelper', JPATH_ADMINISTRATOR . '/components/com_dropfiles/helpers/component.php');
            if(class_exists('DropfilesBase')){
                DropfilesBase::loadLanguage();
            }
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
            // $parent is the class calling this method
            // $type is the type of change (install, update or discover_install)
            if($type=='install' || $type=='update'){
                if(!class_exists('DropfilesBase')){
                    Jerror::raiseWarning(null, 'Cannot install themes package, you need to install Dropfiles component first');
                    if(class_exists('JController')){
                        $controller = new JController();
                    }else{
                        $controller = new JControllerLegacy();
                    }
                    $controller->setRedirect('index.php?option=com_installer&view=install');
                    $controller->redirect();
                    return false;
                }
                $this->release = $parent->get( 'manifest' )->version;
                $minDropfilesVersion = $parent->get( 'manifest' )->dropfilesversion;
                $dropfilesVersion = dropfilesComponentHelper::getVersion();
                if(version_compare($dropfilesVersion, $minDropfilesVersion)==-1){
                    Jerror::raiseWarning(null, 'Cannot install themes package minimum Dropfiles component version is '.$minDropfilesVersion.', your version is '.$dropfilesVersion);
                    if(class_exists('JController')){
                        $controller = new JController();
                    }else{
                        $controller = new JControllerLegacy();
                    }
                    $controller->setRedirect('index.php?option=com_installer&view=install');
                    $controller->redirect();
                    return false;
                }
            }
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
            if($type=='install' || $type=='update'){
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                $manifest = $parent->get('manifest');

                echo '<h2>'.JText::_('COM_DROPFILES_INSTALLER_TITLE').'</h2>';
                echo JText::_('COM_DROPFILES_INSTALLER_MSG');


                $extensions = $manifest->extensions;

                foreach($extensions->children() as $extension){
                        $name = $extension->attributes()->name;
                        $enable = $extension->attributes()->enable;
                        $type = $extension->attributes()->type;
                        $folder = $extension->attributes()->folder;
                        if($enable){
                            if(DropfilesInstallerHelper::enableExtension($name,$type,$folder)){
                                echo '<img src="'. JURI::root().'/components/com_dropfiles/assets/images/tick.png" />'.$name.' : '.JText::sprintf('COM_DROPFILES_INSTALLER_EXT_OK','').'<br/>';
                            }else{
                                echo '<img src="'. JURI::root().'/components/com_dropfiles/assets/images/exclamation.png" />'.$name.' : '.JText::sprintf('COM_DROPFILES_INSTALLER_EXT_NOK','').'<br/>';
                            }
                        }
                }
            }
            
            return true;
        }
}