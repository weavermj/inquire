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

/**
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * @since		1.6
 */
class DropfilesFilesHelper
{
    
        static function bytesToSize($bytes, $precision = 2){
            $sz = array('COM_DROPFILES_FIELD_FILE_BYTE','COM_DROPFILES_FIELD_FILE_KILOBYTE','COM_DROPFILES_FIELD_FILE_MEGABYTE','COM_DROPFILES_FIELD_FILE_GIGABYTE','COM_DROPFILES_FIELD_FILE_TERRABYTE','COM_DROPFILES_FIELD_FILE_PETABYTE');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$precision}f", $bytes / pow(1000, $factor)) . ' ' . @JText::_($sz[$factor]);
        }
        
        static function includeJSHelper(){
            $doc = JFactory::getDocument();
            $doc->addScript(JURI::root().'components/com_dropfiles/assets/js/helper.js');
            JHtml::_('behavior.framework');
            JText::script('COM_DROPFILES_FIELD_FILE_BYTE');
            JText::script('COM_DROPFILES_FIELD_FILE_KILOBYTE');
            JText::script('COM_DROPFILES_FIELD_FILE_MEGABYTE');
            JText::script('COM_DROPFILES_FIELD_FILE_GIGABYTE');
            JText::script('COM_DROPFILES_FIELD_FILE_TERRABYTE');
            JText::script('COM_DROPFILES_FIELD_FILE_PETABYTE');
        }
        
        static function genUrl($id,$id_category,$categoryname='',$token=false,$filename=null){
            $config = JFactory::getConfig();
            $params = JComponentHelper::getParams('com_dropfiles');
            $dropfilesUri = $params->get('uri','files');
            $url = JURI::root();
            if($config->get('sef') && $dropfilesUri){
                if(!$config->get('sef_rewrite')){
                    $url .= 'index.php/';
                }
                
                $url .= $dropfilesUri;
                
                $url .= '/'.$id_category;
                if($categoryname){
                    $url .= '/'.self::makeSafeFilename($categoryname,false);
                }
                
                $url .= '/'.$id;
                if($filename!==null){
                    $url .= '/'.self::makeSafeFilename($filename);
                }
                
                if($token){
                    $url .= '?token='.$token;
                }
            }else{
                $url = JURI::root().'index.php?option=com_dropfiles&task=frontfile.download&&id='.$id.'&catid='.$id_category;
                if($token){
                    $url .= '&token='.$token;
                }
                $url = JRoute::_($url);
            }
            return $url;
        }
        
        static function genViewerUrl($id,$id_category,$categoryname='',$token=false,$filename=null){
            $url = self::genUrl($id, $id_category,$categoryname,$token,$filename);
            return 'https://docs.google.com/viewer?url='.urlencode($url).'&embedded=true';
        }
        
        static function addInfosToFile($items,$category){
            JLoader::register('DropfilesModelTokens', JPATH_ROOT.'/components/com_dropfiles/models/tokens.php');
            $params = JComponentHelper::getParams('com_dropfiles');
            $model = DropfilesModelTokens::getInstance('dropfilesModelTokens');  
            $model->removeTokens();
            $session = JFactory::getSession();
            $sessionToken = $session->get('dropfilesToken',null);
            if($sessionToken===null){
                $token = $model->createToken();
                $session->set('dropfilesToken', $token);
            }else{
                $tokenId = $model->tokenExists($sessionToken);
                if($tokenId){
                    $model->updateToken($tokenId);
                    $token = $sessionToken;
                }else{
                    $token = $model->createToken();
                    $session->set('dropfilesToken', $token);
                }
            }
            if(!empty($items)){
                if(is_array($items)){
                    foreach ($items as &$item){
                        $item->link = DropfilesFilesHelper::genUrl($item->id,$category->id,$category->title,'',$item->title.'.'.$item->ext);
                        if($params->get('usegoogleviewer',1)>0 && 
                                in_array($item->ext,explode(',', $params->get('allowedgoogleext','pdf,ppt,pptx,doc,docx,xls,xlsx,dxf,ps,eps,xps,psd,tif,tiff,bmp,svg,pages,ai,dxf,ttf,txt')))){
                            $item->viewerlink = DropfilesFilesHelper::genViewerUrl($item->id,$category->id,$category->title,$token,$item->title.'.'.$item->ext);
                        }
			$item->created_time = JHtml::_('date', $item->created_time, JText::_('COM_DROPFILES_DEFAULT_DATE_FORMAT'));
			$item->modified_time = JHtml::_('date', $item->modified_time, JText::_('COM_DROPFILES_DEFAULT_DATE_FORMAT'));
                    }
                }else{
                    $items->link = DropfilesFilesHelper::genUrl($items->id,$category->id,$category->title,'',$items->title.'.'.$items->ext);
                    if($params->get('usegoogleviewer',1)>0 && 
                            in_array($items->ext,explode(',', $params->get('allowedgoogleext','pdf,ppt,pptx,doc,docx,xls,xlsx,dxf,ps,eps,xps,psd,tif,tiff,bmp,svg,pages,ai,dxf,ttf,txt')))){
                        $items->viewerlink = DropfilesFilesHelper::genViewerUrl($items->id,$category->id,$category->title,$token,$items->title.'.'.$items->ext);
                    }
                }
            }
            return $items;
        }

//        public static function getDownloadlink($id_file,$id_category=null){
//            $url = 'index.php?option=com_dropfiles&task=frontfile.download&id='.$id_file;
//            if($id_category!==null){
//                $url.='&cat='.$id_category;
//            }
//            return JRoute::_($url);
//        }
//
//        public static function getGoogleViewerlink($id_file,$id_category=null){
//            $url = 'http://docs.google.com/viewer?url='.urlencode($file->link).'&embedded=true';
//            return $url;
//        }
        

    /**
     * Sanitize a file name to get only one extension
     * @param type $filename
     * @return false if failed string otherwise
     */
    public static function makeSafeFilename($filename,$withext=true){
        $name = $filename;
        
        $replace = array(
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ă'=>'A',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'ă'=>'a', 'æ'=>'ae',
            'þ'=>'b', 'Þ'=>'B',
            'Ç'=>'C', 'ç'=>'c',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
            'Ğ'=>'G', 'ğ'=>'g',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'Ñ'=>'N',
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'ö'=>'oe', 'ø'=>'o',
            'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'Š'=>'S', 'š'=>'s', 'Ş'=>'S', 'ș'=>'s', 'Ș'=>'S', 'ş'=>'s', 'ß'=>'ss',
            'ț'=>'t', 'Ț'=>'T',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 
            'Ý'=>'Y',
            'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y',
            'Ž'=>'Z', 'ž'=>'z'
        );
//        $chars = array_keys($replace);
        $name= strtr($filename, $replace);
        
        if($withext){
            //get last extension
            $exploded = explode('.', $name);
            $ext = $exploded[count($exploded)-1];

            $name = substr($name, 0,strlen($name)-strlen($ext)-1);
        }else{
            $ext='';
        }
        $name = preg_replace('/([^a-zA-Z0-9-]+)/', '_', $name);
        
        if($ext===''){
            return $name;
        }
        
        return $name.'.'.$ext;
    }

        
}
