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

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class DropfilesModelFile extends JModelAdmin
{
    /**
    * Returns a Table object, always creating it.
    *
    * @param	type	The table type to instantiate
    * @param	string	A prefix for the table class name. Optional.
    * @param	array	Configuration array for model. Optional.
    *
    * @return	JTable	A database object
   */
   public function getTable($type = 'File', $prefix = 'DropfilesTable', $config = array())
   {
           return JTable::getInstance($type, $prefix, $config);
   }
   
    public function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_dropfiles.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
                return false;
        }
        return $form;
    }
    
    public function save($data) {
        $modelC = $this->getInstance('category','DropfilesModel');
        $idcat = JFactory::getApplication()->input->getInt('catid', 0);
        $category = $modelC->getCategory($idcat);
        if($category->type=='googledrive'){
            $google = new dropfilesGoogle();
            return $google->saveFileInfos($data,$category->cloud_id);
        }else{
            return parent::save($data);
        }
    }
    
    protected function loadFormData()
    {
        $type = JFactory::getApplication()->input->getCmd('type','default');
        if($type=='googledrive'){
            $google = new dropfilesGoogle();
            $data = $google->getFileInfos(JFactory::getApplication()->input->getString('id'));
        }else{
            // Check the session for previously entered form data.
            $data = $this->getItem();
        }
        return $data;
    }
    
    public function getVersion($id){
        $dbo = $this->getDbo();
        $query = 'SELECT v.*, f.catid FROM #__dropfiles_versions as v LEFT JOIN #__dropfiles_files as f ON v.id_file = f.id WHERE v.id='.(int)$id;
        if(!$dbo->setQuery($query)){
            return false;
        }
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObject();
    }
    
    public function deleteVersion($id,$id_file=null){
        $dbo = $this->getDbo();
        $query = 'DELETE FROM #__dropfiles_versions WHERE id='.(int)$id;
        if($id_file!==null){
            $query .= ' AND id_file='.(int)$id_file;
        }
        if(!$dbo->setQuery($query)){
            return false;
        }
        if(!$dbo->query()){
           return false; 
        }
        return true;
    }
}