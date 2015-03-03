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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

class DropfilesControllerFiles extends JControllerForm
{
    
    private $allowed_ext = array('jpg','jpeg','png','gif','pdf','doc','docx','xls','xlsx','zip','tar','rar','odt','ppt','pps','txt');

    public function __construct($config = array()) {
        $params = JComponentHelper::getParams('com_dropfiles');
        $this->allowed_ext = explode(',', $params->get('allowedext'));
        foreach ($this->allowed_ext as $key => $value) {
            $this->allowed_ext[$key] = strtolower(trim($this->allowed_ext[$key]));
            if($this->allowed_ext[$key]==''){
                unset($this->allowed_ext[$key]);
            }
        }
        parent::__construct($config);
    }

    public function upload(){
        $id_category = JFactory::getApplication()->input->getInt('id_category', 0);
        if($id_category<=0){
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_CATEGORY'));
        }
        
        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){            
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_HTTP_RESPONSE'));
        }
        
        $this->canEdit($id_category);

        $modelCat = $this->getModel('category');
        $category = $modelCat->getCategory($id_category);
        
        if($category->type=='googledrive'){
            
        }else{
            //todo: créer un répertoire spécial pour les categories
            $file_dir = dropfilesBase::getFilesPath($id_category);
            if(!file_exists($file_dir)){
                JFolder::create($file_dir);
                $data = '<html><body bgcolor="#FFFFFF"></body></html>';
                JFile::write($file_dir.'index.html', $data);
                $data = 'deny from all';
                JFile::write($file_dir.'.htaccess', $data);
            }
        }

        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
                $pic = $_FILES['pic'];
                $pic['name'] = html_entity_decode($pic['name']);
                
                if(!in_array(strtolower(JFile::getExt($pic['name'])),$this->allowed_ext)){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
                }

                if($category->type=='googledrive'){
                    $google = new dropfilesGoogle();
                    
                    $fileContent = file_get_contents($pic['tmp_name']);
                    
                    if(!$google->uploadFile($pic['name'], $fileContent,$pic['type'], $category->cloud_id)){
                        $this->exit_status($google->getLastError());
                    }
                }else{
                    $newname = uniqid().'.'.strtolower(JFile::getExt($pic['name']));
                    if(!JFile::upload($pic['tmp_name'], $file_dir.$newname)){
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                    }

                    //Insert new image into databse
                    $model = $this->getModel();
                    $id_file = $model->addFile(array(
                        'title' => JFile::stripExt($pic['name']),
                        'id_category' => $id_category,
                        'file' => $newname,
                        'ext' => strtolower(JFile::getExt($pic['name'])),
                        'size' => filesize($file_dir.$newname)
                        ));
                    if(!$id_file){
                        JFile::delete($file_dir.$newname);
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_SAVE_TO_DB'));
                    }
                }
                $this->exit_status(true,array());
        }
        $this->exit_status(JText::_('Error while uploading')); //todo : translate
    }

    public function version(){
        $id_file = JFactory::getApplication()->input->getString('id_file', null);
        $id_category = JFactory::getApplication()->input->getInt('id_category', 0);
        if($id_file===null){
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FILE'));
        }
        
        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){            
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_HTTP_RESPONSE'));
        }
        
        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
                $pic = $_FILES['pic'];
                if(!in_array(strtolower(JFile::getExt($pic['name'])),$this->allowed_ext)){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
                }

                $modelCat = $this->getModel('category');
                $category = $modelCat->getCategory($id_category);
                
                $this->canEdit($category->id);
                
                if($category->type=='googledrive'){
                    $google = new dropfilesGoogle();
                    
                    $fileContent = file_get_contents($pic['tmp_name']);
                    
                    $google->saveFileInfos(array('id'=>$id_file,'newRevision'=>true,'title'=>$pic['name'], 'data'=>$fileContent,'ext' => strtolower(JFile::getExt($pic['name']))), $category->cloud_id);
                }else{
                    $model=$this->getModel();
                    $file = $model->getFile($id_file);

                    if($file->catid!==$category->id){
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                    }
                    
                    $version_dir = dropfilesBase::getVersionPath($id_category);
                    if(!file_exists($version_dir)){
                        JFolder::create($version_dir);
                        $data = '<html><body bgcolor="#FFFFFF"></body></html>';
                        JFile::write($version_dir.'index.html', $data);
                        $data = 'deny from all';
                        JFile::write($version_dir.'.htaccess', $data);
                    }

                    $newname = uniqid().'.'.strtolower(JFile::getExt($pic['name']));

                    if(JFile::move(dropfilesBase::getFilesPath($file->catid).$file->file,$version_dir.$file->file)!==true){
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                    }

                    if(!JFile::upload($pic['tmp_name'], dropfilesBase::getFilesPath($file->catid).$newname)){
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                    }

                    $modelF = $this->getModel('file');
                    $table = $modelF->getTable();
                    $table->save(array('id'=>$file->id,'file'=>$newname,'ext'=>JFile::getExt($pic['name']),'size'=>filesize(dropfilesBase::getFilesPath($file->catid).$newname)));

                    $model->addVersion(array('id_file'=>$file->id,'file'=>$file->file,'ext'=>$file->ext,'size'=>$file->size));
                }
                
                $this->exit_status(true);
        }
    }
    
    public function import(){
        $user = JFactory::getUser();
        if(!$user->authorise('core.admin')){
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_PERMISSION'));
        }
        $id_category = JFactory::getApplication()->input->getInt('id_category', 0);
        if($id_category<=0){
            $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_CATEGORY'));
        }
        if(!JSession::checkToken('get')){
            $this->exit_status(JText::_('WRONG_TOKEN'));
        }
        
        $modelCat = $this->getModel('category');
        $category = $modelCat->getCategory($id_category);
        $this->canEdit($category->id);
        
        $params = JComponentHelper::getParams('com_dropfiles');
        $do = $params->get('import');
        if(!$do){
            $this->exit_status('',array('noerror'));
        }
        //todo: créer un répertoire spécial pour les categories
        $file_dir = dropfilesBase::getFilesPath($id_category);
        if(!file_exists($file_dir)){
            JFolder::create($file_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($file_dir.'index.html', $data);
            $data = 'deny from all';
            JFile::write($file_dir.'.htaccess', $data);
        }        
        $files = JFactory::getApplication()->input->get('files',null,'array');        
        if(!empty($files)){
            $count = 0;
            foreach ($files as $file) {
                $file = JPATH_ROOT.DIRECTORY_SEPARATOR.$file;
                if (strpos($file, '..') !== false){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FOLDER'));
                }
                if ((JPATH_ROOT != '') && strpos($file, JPath::clean(JPATH_ROOT)) !== 0){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FOLDER'));
                }
                if(!in_array(strtolower(JFile::getExt($file)),$this->allowed_ext)){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
                }
                if(!file_exists($file)){
                    $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                }
                
                if($category->type=='googledrive'){
                    $google = new dropfilesGoogle();
                    if(!$google->uploadFile(JFile::getName($file), file_get_contents($file), '', $category->cloud_id)){
                        $this->exit_status($google->getLastError());
                    }
                }else{
                    $newname = uniqid().'.'.strtolower(JFile::getExt($file));
                    if(!JFile::copy($file, $file_dir.$newname)){
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_MOVE_FILE'));
                    }

                    //Insert new image into databse
                    $model = $this->getModel();
                    $id_file = $model->addFile(array(
                        'title' => JFile::stripExt(JFile::getName($file)), 
                        'id_category' => $id_category,
                        'file' => $newname,
                        'ext' => strtolower(JFile::getExt($file)),
                        'size' => filesize($file_dir.$newname)
                        ));
                    if(!$id_file){
                        JFile::delete($file_dir.$newname);
                        $this->exit_status(JText::_('COM_DROPFILES_CTRL_FILES_CANT_SAVE_TO_DB'));
                    }
                }
                $count++;
            }
            $this->exit_status(true,array('nb'=>$count));
        }
        $this->exit_status(JText::_('Error while importing')); //todo : translate
    }
    
    
    /**
     * Delete a file 
     */
    public function delete(){
        $return = false;
        $id_file = JRequest::getString('id_file',0);
        $id_cat = JRequest::getInt('id_cat',0);
        
        $modelC = $this->getModel('category');        
        $category = $modelC->getCategory($id_cat);        
        if($category){
            switch($category->type){
                case 'googledrive';
                    $google = new dropfilesGoogle();
                    if($google->delete($id_file,$category->cloud_id)){
                        $return = true;
                    }
                    break;
                default :
                    $model = $this->getModel();
                    $file = $model->getFile($id_file);
                    if($file!==false){
                        $this->canEdit($file->catid);
                    }
                    $file_dir = dropfilesBase::getFilesPath($file->catid);
                    if(file_exists($file_dir.$file->file)){
                        JFile::delete($file_dir.$file->file);
                    }
                    if($model->removePicture($file->id)){
                            $return = true;
                    }else{
                        $return = false;
                    }
                    break;    
            }    
        }
        echo json_encode($return);
        
        JFactory::getApplication()->close();
    }

    /**
     * Reorder category
     */
    public function reorder(){
        $files = JRequest::getString('order',null);
        $idcat = JFactory::getApplication()->input->getInt('idcat', false);
        $modelCat = $this->getModel('category');
        $category = $modelCat->getCategory($idcat);
        $this->canEdit($category->id);
        
        $files = json_decode($files);
        
        if($category->type==='googledrive'){
            $google = new dropfilesGoogle();
            $return = $google->reorder($files,$category->cloud_id);
        }else{
            $model = $this->getModel();
            foreach ($files as $key => $file) {
                $f = $model->getFile($file);
                $filesok = true;
                if($f->catid != $category->id){
                    $filesok = false;
                    break;
                }
            }

            if($filesok){
                if($model->reorder($files)){
                    $return = true;
                }else{
                    $return = false;
                }
            }else{
                $return = false;
            }
        }
        echo json_encode($return);        
        JFactory::getApplication()->close();
    }

    
    /**
     * Return a json response
     * @param $status
     * @param array $datas array of datas to return with the json string
     * 
     */
    private function exit_status($status,$datas=array()){
            $response = array('response'=>$status,'datas'=>$datas);
            echo json_encode($response);
            JFactory::getApplication()->close();
    }
    
    /**
     * Check if the current user has permission on the current gallery
     * @param type $id_gallery
     */
    private function canEdit($id_category){
        $model = $this->getModel('category');
        $canDo = DropfilesHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $category = $model->getItem($id_category);
                if($category->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }
    }

}