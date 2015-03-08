<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class DropfilesViewFrontcategory extends JViewLegacy
{
	function display($tpl = null)
	{
            $model = $this->getModel();
            $item = $model->getCategory();
            if($item!==null){
                echo json_encode((object)array('category'=>$item));
            }else{
                echo json_decode(array());
            }
            JFactory::getApplication()->close();
        }
}
