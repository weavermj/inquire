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
jimport( 'joomla.filesystem.file' );

class DropfilesControllerFile extends JControllerForm
{

    public function save($key=null,$urlVar=null) {
        
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app   = JFactory::getApplication();
        $lang  = JFactory::getLanguage();
        $model = $this->getModel();
        $table = $model->getTable();
        $data  = $app->input->post->get('jform', array(), 'array');
        $checkin = property_exists($table, 'checked_out');
        $context = "$this->option.edit.$this->context";
        $task = $this->getTask();

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
                $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
                $urlVar = $key;
        }

        $recordId = $app->input->getString($urlVar);

        // Populate the row id from the session.
        $data[$key] = $recordId;


        // Access check.
        if (!$this->allowSave($data, $key))
        {
                $this->exit_status(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
        }

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form)
        {
                $this->exit_status($model->getError());
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
                // Get the validation messages.
                $errors = $model->getErrors();

                // Push up to three validation messages out to the user.
                for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
                {
                        if ($errors[$i] instanceof Exception)
                        {
                                $this->exit_status($errors[$i]->getMessage());
                        }
                        else
                        {
                            $this->exit_status($errors[$i]);
                        }
                }

        }

        if (!isset($validData['tags']))
        {
                $validData['tags'] = null;
        }

        // Attempt to save the data.
        if (!$model->save($validData))
        {
                // Save the data in the session.
                $app->setUserState($context . '.data', $validData);

                // Redirect back to the edit screen.
                $this->exit_status(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
        }

        // Save succeeded, so check-in the record.
        if ($checkin && $model->checkin($validData[$key]) === false)
        {
                // Save the data in the session.
                $app->setUserState($context . '.data', $validData);

                // Check-in failed, so go back to the record and display a notice.
                $this->exit_status(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));


        }

        // Clear the record id and data from the session.
        $this->releaseEditId($context, $recordId);
        $app->setUserState($context . '.data', null);


        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $validData);


        $this->exit_status(true);
        
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
     * We cannot checkin and checkout because we use ajax
     */
    protected function checkEditId($context, $id){
                return true;
    }
    
    public function getRedirectToItemAppend($recordId = NULL, $urlVar = 'id'){
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        
        $format = JRequest::getCmd('format', 'raw');

        $modelC = $this->getModel('category');
        $idcat = JFactory::getApplication()->input->getInt('catid', 0);
        $category = $modelC->getCategory($idcat);
        if($category->type=='googledrive'){
            $append .= '&type=googledrive&id='.JFactory::getApplication()->input->getString('id');
        }else{
            $append .= '&type=default&id='.JFactory::getApplication()->input->getString('id');
        }
        
        // Setup redirect info.
        if ($format)
        {
                $append .= '&format=' . $format;
        }
        return $append; 
    }    
    
    protected function allowEdit($data=array(),$key='id')
    {
        $id = JFactory::getApplication()->input->getString('id', 0);
        $idcat = JFactory::getApplication()->input->getInt('catid', 0);
        $canDo = DropfilesHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $modelC = $this->getModel('category');
                $category = $modelC->getItem($idcat);
                if($category->created_user_id !== JFactory::getUser()->id){
                    return false;
                }
                $category = $modelC->getCategory($idcat);
                if($category->type!='googledrive'){
                    $modelF = $this->getModel('files');                
                    $file = $modelF->getFile($id);
                    if((int)$file->catid !== $idcat){
                        return false;
                    }
                }
                
            }else{
                return false;
            }
        }
        return true;
    }
    
    public function download(){
        $model = $this->getModel();
        
        $id = JFactory::getApplication()->input->getString('id', 0);
        $catid = JFactory::getApplication()->input->getInt('catid', 0);
        $version = JFactory::getApplication()->input->getString('version', false);
        
        if(!$this->allowEdit()){
            $this->setRedirect('index.php',JText::_('JERROR_ALERTNOAUTHOR'));
        }
        
        $modelC = $this->getModel('category');
        $category = $modelC->getCategory($catid);

        switch ($category->type) {
            case 'googledrive':
                $google = new dropfilesGoogle();
                $file = $google->download($id,$category->cloud_id,$version);

                if(!is_object($file)){
                    $this->setRedirect('index.php');
                    $this->redirect();
                }

                header('Content-Disposition: attachment; filename="'.htmlspecialchars($file->title.'.'.$file->ext).'"');
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . $file->size);
                ob_clean();
                flush();
                echo $file->datas;
                jexit();
                break;
            default:
                if(!$version){
                    $file = $model->getItem($id);
                }else{
                    $file = $model->getVersion($id);
                }
                if(!$file){
                    $this->setRedirect('index.php');
                    $this->redirect();
                }

                if($file->id){
                    if(!(bool)$version){
                        $sysfile = dropfilesBase::getFilesPath($file->catid).'/'.$file->file;
                    }else{
                        $sysfile = dropfilesBase::getVersionPath($file->catid).'/'.$file->file;
                    }

                    if(file_exists($sysfile)) {
                        header('Content-Disposition: attachment; filename="'.htmlspecialchars($file->title.'.'.$file->ext).'"');
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($sysfile));
                        ob_clean();
                        
                        $params = JComponentHelper::getParams('com_dropfiles');
                        if($params->get('readfiletype',0)==0){
                            flush();
                            readfile($sysfile);
                        }else{
                            ob_end_flush();
                            $handle = fopen($sysfile, "rb");
                            while (!feof($handle)) {
                              echo fread($handle, 1000);
                            }
                        }
                        jexit();
                    }
                }
                break;
        }
        
    }
    
    public function deleteVersion(){
        $model = $this->getModel();
        
        $id = JFactory::getApplication()->input->getString('id', 0);
        $id_file = JFactory::getApplication()->input->getString('id_file', 0);
        $catid = JFactory::getApplication()->input->getInt('catid', 0);
        
        if(!$this->allowEdit()){
            $this->setRedirect('index.php',JText::_('JERROR_ALERTNOAUTHOR'));
        }
        
        $modelC = $this->getModel('category');
        $category = $modelC->getCategory($catid);
        
        if(empty($category)){
            $this->exit_status('Error deleting');
        }

        switch ($category->type) {
            case 'googledrive':
                $google = new dropfilesGoogle();
                if(!$google->deleteRevision($id_file, $id, $category->cloud_id)){
                    $this->exit_status('Error deleting');
                }
                break;
            default:
                $file = $model->getVersion($id);
                if($file->catid!==$category->id){
                    $this->exit_status('Error deleting');
                }
                if(!$model->deleteVersion($id,$id_file)){
                    $this->exit_status('Error deleting');
                }
                JFile::delete(dropfilesBase::getVersionPath($file->catid).$file->file);
                break;
        }
        $this->exit_status(true);
    }
}