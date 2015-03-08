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

jimport('joomla.access.access');


class DropfilesModelFrontfile extends JModelLegacy
{       
    
    /**
     * Methode to retrieve file
     * @param int $id_file 
     * @return object file, false if an error occurs
     */
    public function getFile($id){
        $dbo = $this->getDbo();
        $query = $dbo->getQuery(true);
        $query->select('*');
        $query->from('#__dropfiles_files');

        $query->where('id='.(int)$id);
        
        $dbo->setQuery($query);
        
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObject();
    }
    
    public function hit($id){
        $dbo = $this->getDbo();
        $query = 'UPDATE #__dropfiles_files SET hits=(hits+1) WHERE id='.(int)$id;
        
        $dbo->setQuery($query);
        
        if(!$dbo->query()){
           return false; 
        }
        return true;
    }
}