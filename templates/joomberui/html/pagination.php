<?php
/**
 * @version        $Id: pagination.php 14401 2010-01-26 14:10:00Z louis $
 * @package        Joomla
 * @copyright    Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct accessdefined('_JEXEC') or die('Restricted access');
/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 *     Input variable $list is an array with offsets:
 *         $list[limit]        : int
 *         $list[limitstart]    : int
 *         $list[total]        : int
 *         $list[limitfield]    : string
 *         $list[pagescounter]    : string
 *         $list[pageslinks]    : string
 *
 * pagination_list_render
 *     Input variable $list is an array with offsets:
 *         $list[all]
 *             [data]        : string
 *             [active]    : boolean
 *         $list[start]
 *             [data]        : string
 *             [active]    : boolean
 *         $list[previous]
 *             [data]        : string
 *             [active]    : boolean
 *         $list[next]
 *             [data]        : string
 *             [active]    : boolean
 *         $list[end]
 *             [data]        : string
 *             [active]    : boolean
 *         $list[pages]
 *             [{PAGE}][data]        : string
 *             [{PAGE}][active]    : boolean
 *
 * pagination_item_active
 *     Input variable $item is an object with fields:
 *         $item->base    : integer
 *         $item->link    : string
 *         $item->text    : string
 *
 * pagination_item_inactive
 *     Input variable $item is an object with fields:
 *         $item->base    : integer
 *         $item->link    : string
 *         $item->text    : string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

 function pagination_list_footer($list)
 {
 	$html = "<div class=\"pagination\">\n";
 	$html .= $list['pageslinks'];
 	$html .= "\n<input type=\"hidden\" name=\"" . $list['prefix'] . "limitstart\" value=\"" . $list['limitstart'] . "\" />";
 	$html .= "\n</div>";

 	return $html;
 }

 function pagination_list_render($list)
 {
 	// Calculate to display range of pages
 	$currentPage = 1;
 	$range = 1;
 	$step = 5;

    $isFirstPageActive = true;
    $isLastPageActive = false;

 	foreach ($list['pages'] as $k => $page)
 	{
 		if (!$page['active'])
 		{
            if ($currentPage != $k) {
                $isFirstPageActive = false;
            }
 			$currentPage = $k;
 		}
 	}

    if ($currentPage == (sizeof($list['pages']))) {
        $isLastPageActive = true;
    }
 	if ($currentPage >= $step)
 	{
 		if ($currentPage % $step == 0)
 		{
 			$range = ceil($currentPage / $step) + 1;
 		}
 		else
 		{
 			$range = ceil($currentPage / $step);
 		}
 	}

 	$html = '<div class="pagination-list">';
     if(!$isFirstPageActive) {
         $html .= $list['start']['data'];
         $html .= $list['previous']['data'];
     }

 	foreach ($list['pages'] as $k => $page)
 	{
 		if (in_array($k, range($range * $step - ($step + 1), $range * $step)))
 		{
 			if (($k % $step == 0 || $k == $range * $step - ($step + 1)) && $k != $currentPage && $k != $range * $step - $step)
 			{
 				$page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
 			}
 		}

 		$html .= $page['data'];
 	}

     if(!$isLastPageActive) {
     	 $html .= $list['next']['data'];
         $html .= $list['end']['data'];
     }

 	$html .= '</div>';
 	return $html;
 }



function pagination_item_active(&$item)
{
  $app = JFactory::getApplication();

  //Override: Remove the hasTooltip class from the pagination.
  /*$title = '';
  if (!is_numeric($item->text))
  {
     JHtml::_('bootstrap.tooltip');
     $title = ' class="hasTooltip" title="' . $item->text . '"';
  }*/

  if ($app->isAdmin())
  {
    return '<a href="#" onclick="document.adminForm.' . $this->prefix
    . 'limitstart.value=' . ($item->base > 0 ? $item->base : '0') . '; Joomla.submitform();return false;">' . $item->text . '</a>';
  }
  else
  {
    return '<a href="' . $item->link . '">' . $item->text . '</a>';
  }
}


function pagination_item_inactive(&$item)
{
  return '<span>' . $item->text . '</span>';
}
