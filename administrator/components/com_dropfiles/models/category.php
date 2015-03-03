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
//jimport('joomla.access.access');

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'category.php');

class DropfilesModelCategory extends CategoriesModelCategory
{
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Category', $prefix = 'DropfilesTable', $config = array()){
            return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Get category file
     * @param type $id
     * @return boolean
     */
    public function getCategory($idcat=null){
        if($idcat==null){
            $idcat = JRequest::getInt('id',0);
        }
        $user = JFactory::getUser();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->from('#__categories as a');
        
        $query->select('a.level, a.id, a.title, a.parent_id');
        
        $query->where('a.published=1');
        $query->where('a.extension='.$db->quote('com_dropfiles'));
        $query->where('a.id='.(int)$idcat);
        
        $query->select('b.title as parent_title');
        $query->select('b.id as parent_id');
        $query->join('LEFT OUTER','#__categories AS b ON b.id=a.parent_id AND b.extension='.$db->quote('com_dropfiles'));
        
        $query->select('c.type, c.cloud_id');
        $query->join('LEFT OUTER','#__dropfiles AS c ON c.id=a.id');
        
        // Implement View Level Access
        if (!$user->authorise('core.admin')){
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN ('.$groups.')');
        }
        $db->setQuery($query);
        if($db->query()){
            return $db->loadObject();
        }
        return null;
    }
    
    /**
     * Get the current theme from a category id
     * @param type $id
     * @return boolean
     */
    public function getCategoryTheme($id){
        $dbo = $this->getDbo();
        $query = 'SELECT theme FROM #__dropfiles WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadResult();
        }
        return false;
    }
    
    /**
     * Get the params from a category id
     * @param type $id
     * @return boolean
     */
    public function getCategoryParams($id){
        $dbo = $this->getDbo();
        $query = 'SELECT params FROM #__dropfiles WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return json_decode($dbo->loadResult());
        }
        return false;
    }
    
    /**
     * Set the title of a category
     * @param int $id_category
     * @param string $title
     * @return int 
     */
    public function setTitle($id_category,$title){
        $dbo = $this->getDbo();
        if($title==''){
            return false;
        }
        $filter = JFilterInput::getInstance();
        $title = $filter->clean($title);
        
        $table = $this->getTable();
        if(!$table->load($id_category)){
            return false;
        }
        if(!$table->bind(array('title'=>$title))){
            return false;
        }
        if(!$table->store()){
            return false;
        }
        $query = 'SELECT * FROM #__dropfiles WHERE id='.(int)$id_category;
        $dbo->setQuery($query);
        $dbo->query();
        $ggd = $dbo->loadObject();
        if($ggd->type=='googledrive'){
            $google = new dropfilesGoogle();
            $google->changeFilename($ggd->cloud_id, $title);
        }
        return true;
    }

    /**
     * Set the theme of a category
     * @param int $id_category
     * @param string $theme
     * @return int 
     */
    public function setTheme($id_category,$theme){
        $dbo = $this->getDbo();
        if($theme==''){
            return false;
        }        
        $query = 'UPDATE #__dropfiles SET theme='.$dbo->quote($theme).' WHERE id='.$dbo->quote($id_category);
        $dbo->setQuery($query);
        if($dbo->query()){
            return true;
        }
        return false;
    }
    
    public function addCategory(){
        $dbo = $this->getDbo();
        $query = 'INSERT INTO #__dropfiles (name) VALUES ('.$dbo->quote(JText::_('COM_DROPFILES_MODEL_CATEGORY_DEFAULT_NAME')).')';
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->insertid();
        }
        return false;
    }
    

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getFormParams($data = array(), $loadData = true)
    {
            $id_category = JRequest::getInt('id',0);
            if(!$id_category){
                return false;
            }

            // Get the form.
            $form = $this->loadForm('com_dropfiles.categoryparams', 'categoryparams', array('control' => 'jform', 'load_data' => $loadData));
            $form->removeGroup('associations');

            //Get the theme
            $dbo = $this->getDbo();
            $query = 'SELECT theme,params FROM #__dropfiles WHERE id='.(int)$id_category;
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false;
            }
            $category = $dbo->loadObject();

            // If type is already known we can load the plugin form
            if(isset($category->theme)){
                JPluginHelper::importPlugin('dropfilesthemes');
                $dispatcher = JDispatcher::getInstance();
                $dispatcher->trigger('getConfigForm',array($category->theme,&$form));
            }
            if (isset($loadData) && $loadData){
                    // Get the data for the form.
                    $data = $this->loadFormData();
                    $form->bind($data);
            }

            if (empty($form)) {
                    return false;
            }
            return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = $this->getItem();
        
        //replace params with dropfiles parmas
        $modelConfig = $this->getInstance('config','dropfilesModel');
        $data->params = $modelConfig->getParams($data->id);
        
        return $data;
    }
    
    /**
    * Method to test whether a record can be deleted.
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
    *
    * @since	1.6
    */
   protected function canDelete($record)
   {
           if (!empty($record->id))
           {
                   $user = JFactory::getUser();

                   return $user->authorise('core.delete', $record->extension . '.category.' . (int) $record->id);
           }
   }
   
   public function delete(&$pks) {
        if(parent::delete($pks)){
            foreach($pks as $i=>$pk){
                $pks[$i] = (int)$pk;
            }
            //Delete files under category
            $dbo = $this->getDbo();
            $query = 'DELETE FROM #__dropfiles_files WHERE catid IN ('.implode(',', $pks).')';
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false;
            }
            $query = 'DELETE FROM #__dropfiles WHERE id IN ('.implode(',', $pks).')';
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false;
            }
            return true;
        }
        return false;
   }
   
   //There is no ckeckin or checkout in ajax
   public function checkin($pks = array()) {
       return true;
   }
   
   public function checkout($pk = null) {
       return true;
   }
   
    public function save($data) {
       $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
       if(parent::save($data) && !$id){
           //this is a new category
           $type = JFactory::getApplication()->input->get('type');
           $cloud_id = '';
           switch ($type) {
               case 'googledrive':
                    $params = JComponentHelper::getParams('com_dropfiles');
                    $google = new dropfilesGoogle();
                    $cloud_id = $google->createFolder($data['title'],$params->get('google_base_folder'));                    
                    $cloud_id = $cloud_id->id;
                   break;
               default:
                   $type = 'default';
                   $cloud_id = null;
                   break;
           }
           $id = (int) $this->getState($this->getName() . '.id');
           $dbo = $this->getDbo();
           $query = 'INSERT INTO #__dropfiles (id,type,cloud_id) VALUES ('.(int)$id.','.$dbo->quote($type).','.$dbo->quote($cloud_id).')';
           $dbo->setQuery($query);
           if(!$dbo->query()){
                return false;
           }
           return true;
       }
       return true;
   }

}