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

class DropfilesViewCategory extends JViewLegacy
{
	
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
            if(JRequest::getCmd('layout')=='form'){   
                $model = $this->getModel();
                //load the parameters form
                $this->form = $model->getFormParams();
            }else{
//                $id_category = JRequest::getInt('id_category',null);
//                $model = $this->getModel();
//                $files = $model->getFiles($id_category);
//                
//                $content = '<div class="table">';
//                foreach ($files as $file){
//                    $content .= '<div class="file" data-id-file="'.$file->id.'">';
//                    $content .=     '<div class="ext '.$file->ext.'"></div>';
//                    $content .=     '<div class="title">';
//                    $content .=         $file->title;
//                    $content .=     '</div>';
//                    $content .=     '<div class="created">';
//                    $date = new JDate($file->created);
//                    $content .=  $date->format('Y-m-d');
//                    $content .=     '</div>';
//                    $content .=     '<div class="actions">';
//                    $content .=         '<a class="trash"><i class="icon-trash"></i></a>';
//                    $content .=     '</div>';
//                    $content .= '</div>';
//                }
//                $content .= '</div>';
               
//                $this->content = $content;                
            }
            parent::display($tpl);
	}
}
