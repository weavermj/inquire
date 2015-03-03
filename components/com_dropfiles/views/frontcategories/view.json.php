<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content categories view.
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class DropfilesViewFrontcategories extends JViewLegacy
{
	protected $items = null;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
		// Initialise variables
		

                $items = $this->get('Items');

                $modelCat = $this->getModel('frontcategory');
                $item = $modelCat->getCategory();
                
                $content = new stdClass();
                $content->categories = $items;
                $content->category = $item;

                echo json_encode($content);
                JFactory::getApplication()->close();
	}

}
