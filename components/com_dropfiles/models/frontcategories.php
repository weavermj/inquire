<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');


class DropfilesModelFrontcategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_dropfiles.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_dropfiles';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = JRequest::getInt('id');
		$this->setState('filter.parentId', $parentId);
		$this->setState('category.id', $parentId);

//		$params = $app->getParams();
//		$this->setState('params', $params);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
	}

	/**
	 * Redefine the function an add some properties to make the styling more easy
	 *
	 * @param	bool	$recursive	True if you want to return children recursively.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6
	 */
	public function getItems($recursive = false)
	{
		if (!count($this->_items)) {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();

			if ($active) {
				$params->loadString($active->params);
			}

			$options = array();
			$options['countItems'] = 1;
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_dropfiles/models/','DropfilesModelCategories');
                        $categories = JModelLegacy::getInstance('Categories','dropfilesModel',$options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));

                        $categories->setState('category.id', $this->getState('category.id',0));
                        $categories->setState('category.frontcategories', true);
                        $cats = $categories->getItems();
                        
                        array_walk($cats, array($this, 'unsetValues'));
                        
                        return $cats;
		}

		return $this->_items;
	}

        private function unsetValues(&$item,$key){
            unset($item->note);
            unset($item->access);
            unset($item->published);
            unset($item->checked_out);
            unset($item->checked_out_time);
            unset($item->created_user_id);
            unset($item->path);
            unset($item->lft);
            unset($item->rgt);
            unset($item->editor);
            unset($item->access_level);
            unset($item->author_name);
        }
        
	public function getParent()
	{
		if (!is_object($this->_parent)) {
			$this->getItems();
		}

		return $this->_parent;
	}
        
        
}
