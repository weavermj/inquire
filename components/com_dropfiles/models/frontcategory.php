<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_dropfiles
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * This models supports retrieving a category, the files associated with the category,
 * sibling, child and parent categories.
 *
 * @package		Joomla.Site
 * @subpackage	com_dropfiles
 * @since		1.5
 */
class DropfilesModelFrontcategory extends JModelItem
{
    public function getCategory($idcat=null){
        if($idcat==null){
            $idcat = JRequest::getInt('id',0);
        }
        $user = JFactory::getUser();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->from('#__categories as a');
        
        $query->select('a.level, a.id, a.title, a.parent_id, a.access');
        
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
}
