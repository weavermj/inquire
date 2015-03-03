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


class DropfilesViewFiles extends JViewLegacy
{
	
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
            $modelC = $this->getModel('category');

            if(JRequest::getCmd('layout')=='form'){   
                $this->form = $modelC->getForm();
            }elseif(JRequest::getCmd('layout')=='versions'){
                $category_id = JFactory::getApplication()->input->getInt('id_category', 0);
                $file_id = JFactory::getApplication()->input->getString('id_file', 0);
                $model = $this->getModel();
                $modelCategory = JModelLegacy::getInstance('Category','dropfilesModel');
                $category = $modelCategory->getCategory($category_id);

                if(!$category){
                    return '';
                }
                
                if($category->type=='googledrive'){
                    $google = new dropfilesGoogle();
                    $files = $google->listVersions($file_id);
                }else{
                    $files = $model->getVersions($file_id);
                }
                $content = '';
                if(!empty($files)){
                    $content .= '<table>';
                    foreach ($files as $file) {
                        $content .= '<tr>';
                        if($category->type=='googledrive'){
                            $version = $file->id_version;
                            $data_id = $file->id_version;
                        }else{
                            $version = '1';
                            $data_id = $file->id;
                        }
                        $content .= '<td><a href="index.php?option=com_dropfiles&task=file.download&version='.$version.'&id='.$file->id.'&catid='.$category_id.'" target="_blank">';
                        $content .= date("Y M d",strtotime($file->created_time)).' ';
                        $content .= '</a></td>';
                        $content .= '<td>'.DropfilesFilesHelper::bytesToSize($file->size).'</td>';
                        $content .= '<td><a data-id="'.$data_id.'" href="#" class="trash"><i class="icon-trash"></i></a></td>';
                        $content .= '</tr>';
                    }
                    $content .= '</table>';
                }
                echo $content;
            }else{
                $category_id = JFactory::getApplication()->input->getInt('id_category', 0);
                $model = $this->getModel();
                $modelCategory = JModelLegacy::getInstance('Category','dropfilesModel');
                $modelConfig = JModelLegacy::getInstance('Config','dropfilesModel');
                $category = $modelCategory->getCategory($category_id);
                $params = $modelConfig->getParams($category->id);
                if(!$params){
                    $params = new stdClass();
                    $params->ordering = 'ordering';
                    $params->orderingdir = 'asc';
                }
                if($category->type=='googledrive'){
                    $google = new dropfilesGoogle();
                    
                    $ordering = JFactory::getApplication()->input->getCmd('orderCol',$params->ordering);
                    if($ordering!==null){
                        if(!in_array($ordering, array('ordering','ext','title','description','created_time','size','version','hits'))){
                            $ordering = 'ordering';
                        }else{
                            $direction = JFactory::getApplication()->input->getCmd('orderDir',$params->orderingdir);
                            if($direction!=='desc'){
                                $direction = 'asc';
                            }
                        }
                    }else{
                        $ordering = 'ordering';
                    }

                    $files = $google->listFiles($category->cloud_id,$ordering,$direction);
                    if($files===false){
                        echo '<div class="alert alert-danger">'.$google->getLastError().'</div>';
                        return '';
                    }
                }else{
                    $files = $model->getItems();
                    $ordering = $model->getState('list.ordering',false);
                    $direction = $model->getState('list.direction',false);
                }
                $theme = $modelC->getCategoryTheme(JFactory::getApplication()->input->getInt('id_category', 0));
                                
                $canDo = DropfilesHelper::getActions();
                if($canDo->get('core.edit') || $canDo->get('core.edit.own')){
                    $canOrder = true;
                }else{
                    $canOrder = false;
                }
                
                $content = '<table class="restable">';
                $content .= '<thead><tr>';
                foreach (   array(
                                'ordering' => array('#',''),
                                'ext' => array('#',''),
                                'title'=>array(JText::_('COM_DROPFILES_FIELD_FILE_TITLE_LABEL'),'essential'),
                                'description'=>array(JText::_('COM_DROPFILES_FIELD_FILE_DESCRIPTION_LABEL'),''),
                                'size'=>array(JText::_('COM_DROPFILES_FIELD_FILE_FILESIZE_LABEL'),''),
                                'created_time'=> array(JText::_('COM_DROPFILES_FIELD_FILE_DATEADDED_LABEL'),''),
                                'version'=>array(JText::_('COM_DROPFILES_FIELD_FILE_VERSION_LABEL'),''),
                                'hits' => array(JText::_('COM_DROPFILES_FIELD_HITS_LABEL'),'')
                            ) 
                        as $row => $title) 
                {    
                    $content .= '<th class="'.$title[1].'">';
                    if($canOrder){
                        $content .= '<a href="#" class="'.($ordering===$row?'currentOrderingCol':'').'" data-ordering="'.$row.'" data-direction="'.$direction.'">';
                    }
                    $content .= $title[0];
                    
                    if($row===$ordering){
                        $icon = 'icon-arrow-'.($direction==='asc'?'up':'down');
                        if(dropfilesBase::isJoomla30()){
                            $icon .= '-3';
                        }
                        $content .= ' <i class="'.$icon.'"></i>';
                    }
                    
                    if($canOrder){
                        $content .= '</a>';
                    }
                    $content .= '</th>';
                }                
                $content .= '<th class="essential"></th>';
                $content .= '</tr></thead>';
                $content .= '<tbody>';

                if(dropfilesBase::isJoomla25()){
                    $iconOrder = 'icon-move';
                }else{
                    $iconOrder = 'icon-menu';
                }
                foreach ($files as $file){
                    $content .= '<tr class="file " data-id-file="'.$file->id.'">';
                    $content .=     '<td class="orderingCol">';
                    if($ordering==='ordering'){
                        $content .= '       <span class="sortable-handler" style="cursor: move;">
                                                    <i class="'.$iconOrder.'"></i>
                                            </span></td>';
                    }else{
                        $content .= '       <span class="sortable-handler inactive tip-top hasTooltip" title="">
                                                    <i class="'.$iconOrder.'"></i>
                                            </span></td>';
                    }
                    $content .=     '<td class="ext '.$file->ext.'"><div><span class="txt">'.$file->ext.'</span></div></td>';
                    $content .=     '<td class="title">';
                    $content .=         $file->title;
                    $content .=     '</td>';
                    $content .=     '<td class="description">';
                    $content .=         $file->description;
                    $content .=     '</td>';
                    $content .=     '<td class="size">';
                    $content .=         DropfilesFilesHelper::bytesToSize($file->size);
                    $content .=     '</td>';
                    $content .=     '<td class="created">';
                    $date =             new JDate($file->created_time);
                    $content .=         $date->format('Y-m-d');
                    $content .=     '</td>';
                    $content .=     '<td class="version">';
                    $content .=         $file->version;
                    $content .=     '</td>';
                    $content .=     '<td class="hits">';
                    $content .=         $file->hits.' '.JText::_('COM_DROPFILES_LAYOUT_DROPFILES_HITS');
                    $content .=     '</td>';
                    $content .=     '<td class="actions">';
                    $content .=         '<a class="trash"><i class="icon-trash"></i></a>';
                    $content .=         '<a class="download" href="index.php?option=com_dropfiles&task=file.download&id='.$file->id.'&catid='.$file->catid.'"><i class="icon-download"></i></a>';
                    $content .=     '</td>';
                    $content .= '</tr>';
                }
                $content .= '</tbody>';
                $content .= '</table>';
                $content .= '<input type="hidden" name="theme" value="'.strtolower($theme).'">';
                echo $content;
            }
//            parent::display($tpl);
	}
}
