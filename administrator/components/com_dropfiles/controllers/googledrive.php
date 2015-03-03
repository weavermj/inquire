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


class DropfilesControllerGoogledrive extends JControllerLegacy
{
   
    public function getAuthorizeUrl(){
        $google = new dropfilesGoogle();
        $url = $google->getAuthorisationUrl();
        $this->setRedirect($url);
        $this->redirect();
    }
    
    public function authenticate(){
        $google = new dropfilesGoogle();
        $credentials = $google->authenticate();
        $google->storeCredentials($credentials);

        //Check if dropfiles folder exists and create if not
        $params = JComponentHelper::getParams('com_dropfiles');
        if(!$google->folderExists($params->get('google_base_folder',null))){
            $folder = $google->createFolder('Dropfiles - '.JFactory::getApplication()->getCfg('sitename'));
            dropfilesComponentHelper::setParams(array('google_base_folder'=> $folder->id));
        }
        
        $this->setRedirect('index.php?option=com_dropfiles&view=googledrive&layout=redirect');
        $this->redirect();
    }
    
    public function checkauth(){
        $google = new dropfilesGoogle();
        return $google->checkAuth();
    }
    
    public function logout(){
        $google = new dropfilesGoogle();
        $google->logout();
        
        dropfilesComponentHelper::setParams(array('google_base_folder'=> ''));
        dropfilesComponentHelper::setParams(array('google_credentials'=> ''));
        
        $this->setRedirect('index.php?option=com_dropfiles&view=googledrive&tmpl=component');
        $this->redirect();
    }

}