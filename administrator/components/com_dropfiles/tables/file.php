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

// No direct access
defined('_JEXEC') or die;

/**
 * Category Table class
 *
 * @package		Joomla.Administrator
 * @subpackage          com_dropfiles
 * @since		1.5
 */
class DropfilesTableFile extends JTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__dropfiles_files', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	 */
//	public function bind($array, $ignore = '')
//	{
//		if (isset($array['params']) && is_array($array['params'])) {
//			$registry = new JRegistry();
//			$registry->loadArray($array['params']);
//			$array['params'] = (string)$registry;
//		}
//		return parent::bind($array, $ignore);
//	}

        /**
	 * Overridden JTable::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
//		$user = JFactory::getUser();
                
		if ($this->id)
		{
			// Existing category
			$this->modified_time = $date->toSql();
//			$this->modified_user_id = $user->get('id');
		}
		else
		{
			// New category
			$this->created_time = $date->toSql();
//			$this->created_user_id = $user->get('id');
		}
		return parent::store($updateNulls);
	}
        
}
