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

jimport('joomla.application.component.modellist');
jimport('joomla.access.access');


class DropfilesModelFiles extends JModelList
{       
    
    protected $allowedOrdering = array('ordering','ext','title','description','created_time','size','version','hits');


    /**
	 * @return	string
	 * @since	1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
                                    'f.id, f.catid, f.file, f.ordering, f.title, f.description, f.ext' .
				', f.hits, f.version, f.size, f.created_time, f.modified_time, f.author'.
				', f.language'
			)
		);
		$query->from('#__dropfiles_files AS f');

		// Join over the language
		$query->select('f.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = f.language');
		
		// Filter by category
		if ($category = $this->getState('filter.category')) {
			$query->where('f.catid = '.$db->quote($category));
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('f.language = '.$db->quote($language));
		}

                // Add the list ordering clause.
                if($this->getState('ordering')){
                    $orderCol = $this->state->get('list.ordering', 'ordering');
                    $orderDirn = $this->state->get('list.direction', 'asc');
                    
                }else{
                    $orderCol = 'ordering';
                    $orderDirn = 'asc';
                    
                    $dbo = $this->getDbo();
                    $dbo->setQuery('SELECT params FROM #__dropfiles WHERE id='.(int)$category);
                    $dbo->query();
                    $params = $dbo->loadResult();
                    $params = json_decode($params);
                    
                    if(isset($params->ordering)){
                        if(in_array($params->ordering,$this->allowedOrdering)){
                            $orderCol = $this->state->get('list.ordering', $params->ordering);
                        }else{
                            $orderCol = 'ordering';
                        }
                    }
                    
                    if(isset($params->orderingdir)){
                        if($params->orderingdir==='asc' || $params->orderingdir==='desc'){
                            $orderDirn = $this->state->get('list.direction', $params->orderingdir);
                        }
                    }else{
                        $orderDirn = 'asc';
                    }
                }
                $this->setState('list.ordering',$orderCol);
                $this->setState('list.direction',$orderDirn);

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
//		dump(nl2br(str_replace('#__','m7rgh_',$query)));
		return $query;
	}
    
    
    
    
    
    /**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
            
		$category = JRequest::getInt('id_category', 0);
		$this->setState('filter.category', $category );
                
                $ordering = JFactory::getApplication()->input->getCmd('orderCol',null);
                if($ordering!==null){
                    if(!in_array($ordering, $this->allowedOrdering)){
                        $ordering = 'ordering';
                    }else{
                        $direction = JFactory::getApplication()->input->getCmd('orderDir','asc');
                        if($direction!=='desc'){
                            $direction = 'asc';
                        }
                        $this->setState('ordering',true);
                    }
                }
                parent::populateState($ordering, $direction);
                
                $this->setState('list.limit', 100000);
	}
    
    
    
    /**
     * Method to add a file into database
     * @param string $file 
     * @param int   $id_category
     * @return inserted row id, false if an error occurs
     */
    public function addFile($data){
        $dbo = $this->getDbo();
        $date	= JFactory::getDate();
        $ordering = $this->getNextPosition($data['id_category']);
        $query = 'INSERT INTO #__dropfiles_files (file,catid,ordering,title,ext,size,created_time,modified_time) 
                  VALUES ('.$dbo->quote($data['file']).','.intval($data['id_category']).','.intval($ordering).','.$dbo->quote($data['title']).','.$dbo->quote($data['ext']).','.(int)$data['size'].','.$dbo->quote($date->toSql()).','.$dbo->quote($date->toSql()).')';
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return $dbo->insertid();
    }
    
    /**
     * Methode to retrieve the next file ordering for a category
     * @param int $id_category 
     * @return int next ordering
     */
    private function getNextPosition($id_category){
        $dbo = $this->getDbo();
        $query = 'SELECT ordering FROM #__dropfiles_files WHERE catid='.$dbo->quote($id_category).' ORDER BY ordering DESC LIMIT 0,1';
        $dbo->setQuery($query);
        if($dbo->query() && $dbo->getNumRows()>0){
           return $dbo->loadResult()+1; 
        }
        return 0;
    }
    
    /**
     * Methode to retrieve file information
     * @param int $id_file 
     * @return object file, false if an error occurs
     */
    public function getFile($id_file){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__dropfiles_files WHERE id='.$dbo->quote($id_file);
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObject();
    }
    
    /**
     * Methode to reorder 
     * @param int $id_category 
     * @param array $files
     * @return boolean result
     */
    public function reorder($files){
        $dbo = $this->getDbo();
        foreach ($files as $key => $file) {
            $query = 'UPDATE #__dropfiles_files SET ordering = '.intval($key).' WHERE id='.intval($file);
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false; 
            }
        }
        return true;
    }
    
    /**
     * Method to delete a file from the database
     * @param int   $id_file
     * @return number of affected rows, false if an error occurs
     */
    public function removePicture($id_file){
        $dbo = $this->getDbo();
        $query = 'DELETE FROM #__dropfiles_files WHERE id='.$dbo->quote($id_file);
        $dbo->setQuery($query);
        if(!$dbo->query()){
//            $dbo->getErrorMsg();
            return false;
        }
        return $dbo->getAffectedRows();
    }    
    
    /**
     * Methode to retrieve all files information
     * @return object file, false if an error occurs
     */
    public function getAllPictures(){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__dropfiles_files';
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObjectList();
    }
    
    public function updateFile($data){
        $dbo = $this->getDbo();
        $query = 'UPDATE #__dropfiles_files SET file='.$dbo->quote($data['file']).', ext='.$dbo->quote($data['ext']).', size='.$dbo->quote($data['size']).' WHERE id='.(int)$data['id'];
        if(!$dbo->setQuery($query)){
            return false;
        }
        if(!$dbo->query()){
           return false; 
        }
        return true;
    }
    
    public function addVersion($data){
        $dbo = $this->getDbo();
        $query = 'INSERT INTO #__dropfiles_versions (id_file,file,ext,size,created_time) VALUES ('.(int)$data['id_file'].','.$dbo->quote($data['file']).','.$dbo->quote($data['ext']).','.$dbo->quote($data['size']).','.$dbo->quote(date('Y-m-d H:i:s')).')';
        if(!$dbo->setQuery($query)){
            return false;
        }
        if(!$dbo->query()){
           return false; 
        }
        return true;
    }

    public function getVersions($id_file){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__dropfiles_versions WHERE id_file='.(int)$id_file.' ORDER BY created_time DESC';
        if(!$dbo->setQuery($query)){
            return false;
        }
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObjectList();
    }

}