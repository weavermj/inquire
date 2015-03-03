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

jimport('joomla.application.component.controllerlegacy');

class dropfilesControllerUpdate extends JControllerLegacy
{
    
        public function check(){
            $document = JFactory::getDocument();
            $document->setMimeEncoding('application/json');
            
            $cookie = JFactory::getApplication()->input->cookie->get('com_dropfiles_noCheckUpdates',false);
            $hide = JComponentHelper::getParams('com_dropfiles')->get('hideupdate',0);

            if($cookie!==false || $hide==1){
                $return = false;
            }else{
            
                $lastVersion = dropfilesBase::getLastExtensionVersion();
                $currentVersion = dropfilesBase::getExtensionVersion();
                
                if(version_compare($lastVersion, $currentVersion)==1){
                    $return = $lastVersion;
                }else{
                    $return = false;
                }
                
            }
            echo json_encode($return);
            JFactory::getApplication()->close();
        }

}
