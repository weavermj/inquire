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

//jimport('joomla.application.component.modeladmin');
//jimport('joomla.access.access');

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'categories.php');

class DropfilesModelCategories extends CategoriesModelCategories
{   
    
    protected  $canDo;
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $app = JFactory::getApplication();
        $app->setUserState('com_categories.categories.filter.extension', 'com_dropfiles');
        $app->setUserState('list.limit',1000);
    }

    
    public function populateState($ordering = null, $direction = null) {
        parent::populateState($ordering, $direction);
        $this->setState('list.start', 0);
        $this->state->set('list.limit', 1000);
    }
    
    public function getListQuery() {
        $db = $this->getDbo();
        $this->setState('filter.access',null); //don't want to use Joomla access

        $query = $db->getQuery(true);
        

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select',
                        'a.id, a.title, a.alias, a.note, a.published, a.access' .
                        ', a.checked_out, a.checked_out_time, a.created_user_id' .
                        ', a.path, a.parent_id, a.level, a.lft, a.rgt' .
                        ', a.language'
                )
        );
        $query->from('#__categories AS a');

        // Join over the language
        $query->select('l.title AS language_title')
                ->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
                ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        //Join over the dropfiles categories
        $query->select('d.type AS type')
                ->join('LEFT', '#__dropfiles AS d ON d.id=a.id');
        
        // Filter by extension
        if ($extension = $this->getState('filter.extension'))
        {
                $query->where('a.extension = ' . $db->quote($extension));
        }

        // Filter on the level.
        if ($level = $this->getState('filter.level'))
        {
                $query->where('a.level <= ' . (int) $level);
        }
        
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published))
        {
                $query->where('a.published = ' . (int) $published);
        }
        elseif ($published === '')
        {
                $query->where('(a.published IN (0, 1))');
        }

        $catid = $this->getState('category.id',null);
        if($catid!==null){
            $subQuery = "SELECT rgt,lft FROM #__categories WHERE id=".(int)$catid." AND extension='com_dropfiles'";
            $db->setQuery($subQuery);
            if(!$db->query()){
                return false;
            }
            $parent = $db->loadObject();
            $recursive = $this->getState('category.recursive',null);
            if($recursive){
                $query->where('a.rgt<= '.(int)$parent->rgt);
                $query->where('a.lft> '.(int)$parent->lft);
            }else{
                $query->where('a.parent_id = '.(int)$catid);
            }
        }
        
        $query->select('COUNT(f.id) as files');
        $query->join('LEFT', '#__dropfiles_files AS f ON a.id = f.catid');
//        $query->where('a.id IS NOT null');
        $query->group('a.id');
        
        if($this->getState('category.frontcategories',false)==false){
            $canDo = DropfilesHelper::getActions();
            if(($canDo->get('core.edit.own') && !$canDo->get('core.edit'))||(!$canDo->get('core.edit.own') && !$canDo->get('core.edit'))){
                $query->where('created_user_id='.(int)JFactory::getUser()->id);
            }
        }        
        $query->order('a.lft ASC');

        return $query;
        
    }
}