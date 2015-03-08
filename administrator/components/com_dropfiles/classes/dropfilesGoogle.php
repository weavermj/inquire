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
jimport( 'joomla.filesystem.file' );

class dropfilesGoogle {

    protected $params;
    protected $lastError;


    public function __construct() {
        set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
        require_once 'Google/Client.php';
        require_once 'Google/Service/Drive.php';
        require_once 'Google/Auth/Exception.php';
        require_once 'Google/Auth/OAuth2.php';
        require_once 'Google/Service/Oauth2.php';
        $this->loadParams();
    }
    
    public function getLastError(){
        return $this->lastError;
    }
    
    protected function loadParams(){
        $params = JComponentHelper::getParams('com_dropfiles');
        $this->params = new stdClass();
        $this->params->google_client_id = $params->get('google_client_id');
        $this->params->google_client_secret = $params->get('google_client_secret');
        $this->params->google_credentials = $params->get('google_credentials');
    }

    protected function saveParams(){ 
        JLoader::register('DropfilesComponentHelper', JPATH_ADMINISTRATOR.'/components/com_dropfiles/helpers/component.php');
        dropfilesComponentHelper::setParams(array(
            'google_client_id' => $this->params->google_client_id,
            'google_client_secret' => $this->params->google_client_secret,
            'google_credentials'=>$this->params->google_credentials));
    }

    public function getAuthorisationUrl(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setRedirectUri(JURI::root().'administrator/index.php?option=com_dropfiles&task=googledrive.authenticate');
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setState('');
        $client->setScopes(array(
                    'https://www.googleapis.com/auth/drive', 
                   'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile'));
        $tmpUrl = parse_url($client->createAuthUrl());
        $query = explode('&', $tmpUrl['query']);
        return $tmpUrl['scheme'] . '://' . $tmpUrl['host'] . @$tmpUrl['port'] .$tmpUrl['path'] . '?' . implode('&', $query);
    }
    
    public function authenticate(){
        $code = JFactory::getApplication()->input->get('code','','RAW');
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setRedirectUri(JURI::root().'administrator/index.php?option=com_dropfiles&task=googledrive.authenticate');

        return $client->authenticate($code);
    }
    
    public function logout(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        $client->revokeToken();
    }
    
    public function storeCredentials($credentials){
        $this->params->google_credentials = $credentials;
        $this->saveParams();
    }
    
    public function getCredentials(){
        return $this->params->google_credentials;
    }
    
    public function checkAuth(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);

        try {
            $client->setAccessToken($this->params->google_credentials);
            $service = new Google_Service_Drive($client);
            $service->files->listFiles(array());
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }
    
    public function folderExists($id){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        $service = new Google_Service_Drive($client);
        try{
            $file = $service->files->get($id);
            return true;
        }catch(Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }
        return false;
    }
    
    public function createFolder($title,$parentId=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        $service = new Google_Service_Drive($client);
        $file = new Google_Service_Drive_DriveFile();
        $file->title = $title;
        $file->mimeType = "application/vnd.google-apps.folder";
        
        if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
          }
        
        try {
            $fileId = $service->files->insert($file);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return $fileId;
    }

    public function listFiles($folder_id,$ordering='ordering',$direction='asc'){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);

        try{
            $client->setAccessToken($this->params->google_credentials);
        
    //        if($client->isAccessTokenExpired()){
    //            $client->refreshToken($creds->refresh_token);
    //        
        
        
            $service = new Google_Service_Drive($client);

            $fs = $service->files->listfiles(array('q' =>  "'".$folder_id."' in parents"));

            $files = array();
            foreach ($fs as $f){
                $file = new stdClass();
                $file->id = $f->getId();
                $file->title = JFile::stripExt($f->getTitle());
                $file->description = $f->getDescription();
                $file->ext = $f->fileExtension?$f->fileExtension:JFile::getExt($f->originalFilename);
                $file->size = $f->getFileSize();
                $file->created_time = date('Y-m-d H:i:s',strtotime($f->getCreatedDate()));
                $file->modified_time = date('Y-m-d H:i:s',strtotime($f->getModifiedDate()));
                $file->version = '';
                $file->hits = 0;
                $file->ordering = 0;
                $properties = $f->getProperties();
                if(!empty($properties)){
                    foreach ($properties as $property) {                    
                        switch ($property->key){
                            case 'version':
                                $file->version = $property->value;
                                break;
                            case 'hits':
                                $file->hits = $property->value;
                                break;
                            case 'ordering':
                                $file->ordering = $property->value;
                        }
                    }
                }
                $files[] = $file;
                unset($file);
            }

            $files = $this->subval_sort($files, $ordering,$direction);
        }  catch (Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }
        return $files;

    }
    
    private function subval_sort($a,$subkey,$direction) {
            if(empty($a)){
                return $a;
            }
            foreach($a as $k=>$v) {
                $b[$k] = strtolower($v->$subkey);
            }
            if($direction=='asc'){
                asort($b);
            }else{
                arsort($b);
            }
            foreach($b as $key=>$val) {
                    $c[] = $a[$key];
            }
            return $c;
    }


    public function uploadFile($filename,$fileContent,$mime,$id_folder){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        $file = new Google_Service_Drive_DriveFile();
        $parent = new Google_Service_Drive_ParentReference();
        $parent->setId($id_folder);
        $file->setParents(array($parent));        
        $file->setTitle($filename); 
        $file->setMimeType($mime);

        try {
            $service = new Google_Service_Drive($client);
            $insertedFile = $service->files->insert($file, array('data' => $fileContent,'mimeType'=>$mime,'uploadType'=>'media'));
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return $insertedFile;
    }
    
    public function getFileInfos($id,$cloud_id=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($id);
            
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }            
            
            $data = array();
            $data['id'] = $id;
            $data['title'] = JFile::stripExt($file->title);
            $data['description'] = $file->description;
            $data['file'] = $file->title;
            $data['ext'] = $file->fileExtension?$file->fileExtension:JFile::getExt($file->originalFilename);
            $data['created_time'] = date('Y-m-d H:i:s',strtotime($file->createdDate));
            $data['modified_time'] = date('Y-m-d H:i:s',strtotime($file->modifiedDate));
            try{
                $hits = $service->properties->get($id, 'hits', array('visibility'=>'PRIVATE'));
                $hits = $hits->value;
            } catch (Exception $ex) {
                $hits = 0;
            }
            $data['hits'] = $hits;
            $data['size'] = $file->fileSize;
            try{
                $version = $service->properties->get($id, 'version', array('visibility'=>'PRIVATE'));
                $version = $version->value;
            } catch (Exception $e) {
                $version = '';
            }
            $data['version'] = $version;
            try{
                $order = $service->properties->get($id, 'order', array('visibility'=>'PRIVATE'));
                $order = $order->value;
            } catch (Exception $e) {
                $order = 0;
            }
            $data['ordering'] = $order;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return $data;
    }

    public function saveFileInfos($datas,$cloud_id=null){
        if(empty($datas['id'])){
            $datas['id'] = JFactory::getApplication()->input->getString('id');
        }
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($datas['id']);
            $params = array('uploadType'=>'multipart');
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }
            
            $properties = $service->properties->listProperties($datas['id']);
            $propertiesList = $properties->getItems();

            if(isset($datas['hits'])){
                $found = false;
                foreach ($propertiesList as $property) {
                    if($property->key=='hits' && $property->visibility=='PRIVATE'){
                        $found = true;
                        break;
                    }
                }
                if($found){
                    try{
                    $hits = $service->properties->get($datas['id'], 'hits', array('visibility'=>'PRIVATE'));
                    $hits->setValue($datas['hits']);
                    $service->properties->update($datas['id'], 'hits', $property, array('visibility'=>'PRIVATE'));
                    }catch (Exception $e) {
                        return false;
                      }
                }else{
                    $newProperty = new Google_Service_Drive_Property();
                    $newProperty->setKey('hits');
                    $newProperty->setValue($datas['hits']);
                    $newProperty->setVisibility('PRIVATE');
                    $service->properties->insert($datas['id'], $newProperty);
                }
            }
            
            if(isset($datas['version'])){
                $found = false;
                foreach ($propertiesList as $property) {
                    if($property->key=='version' && $property->visibility=='PRIVATE'){
                        $found = true;
                        break;
                    }
                }
                if($found){
                    $version = $service->properties->get($datas['id'], 'version', array('visibility'=>'PRIVATE'));
                    $version->setValue($datas['version']);
                    $service->properties->update($datas['id'], 'version', $version, array('visibility'=>'PRIVATE'));
                }else{
                    $newProperty = new Google_Service_Drive_Property();
                    $newProperty->setKey('version');
                    $newProperty->setValue($datas['version']);
                    $newProperty->setVisibility('PRIVATE');
                    $service->properties->insert($datas['id'], $newProperty);
                }
            }
            
            if(isset($datas['title'])){
                $file->setTitle($datas['title'].'.'.$file->fileExtension);
            }
            if(isset($datas['description'])){
                $file->setDescription($datas['description']);
            }
            if(isset($datas['data'])){
                $params['data'] = $datas['data'];
            }
            if(isset($datas['newRevision'])){
                $params['newRevision'] = true;
            }else{
                $params['newRevision'] = false;
            }
            $service->files->update($datas['id'], $file, $params);
            
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }

    public function changeFilename($id,$filename){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        try{
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($id);
            $file->setTitle($filename);
            $service->files->update($id, $file, array());
        }catch(Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }

    public function incrHits($id){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            try{
                $hits = $service->properties->get($id, 'hits', array('visibility'=>'PRIVATE'));
                $hits = $hits->value;
            } catch (Exception $e){
                $hits = 0;
            }
            
            $newProperty = new Google_Service_Drive_Property();
            $newProperty->setKey('hits');
            $newProperty->setValue($hits+1);
            $newProperty->setVisibility('PRIVATE');
            $service->properties->insert($id, $newProperty);
            
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }
    
    public function reorder($files,$category_id){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        try {
            $service = new Google_Service_Drive($client);
            foreach ($files as $key => $file) {
                $newProperty = new Google_Service_Drive_Property();
                $newProperty->setKey('order');
                $newProperty->setValue($key);
                $newProperty->setVisibility('PRIVATE');
                $service->properties->insert($file, $newProperty);
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }
    
    public function download($id,$cloud_id=null,$version=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($id);
                        
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }
                    

            $downloadUrl = $file->getDownloadUrl();
            if($version!==null){
                $revision = $service->revisions->get($id, $version);
            }
            if ($downloadUrl) {
              $request = new Google_Http_Request($downloadUrl);
              $httpRequest = $client->getAuth()->authenticatedRequest($request);

              if ($httpRequest->getResponseHttpCode() == 200) {
                $ret = new stdClass();
                $ret->datas = $httpRequest->getResponseBody();
                $ret->title = JFile::stripExt($file->getTitle());
                if(isset($revision)){
                    $ret->ext = $revision->fileExtension?$revision->fileExtension:JFile::getExt($revision->originalFilename);
                    $ret->size = $revision->fileSize;
                }else{
                    $ret->ext = $file->fileExtension?$file->fileExtension:JFile::getExt($file->originalFilename);
                    $ret->size = $file->getFileSize();
                }
                return $ret;
              } else {
                // An error occurred.
                return false;
              }
            } else {
              // The file doesn't have any content stored on Drive.
              return false;
            }
        }  catch (Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }

    }
    
    public function delete($id,$cloud_id=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        
        $service = new Google_Service_Drive($client);
        try {
            $file = $service->files->get($id);
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }
            $service->files->delete($id);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }
    
    public function listVersions($id,$cloud_id=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($id);
                        
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }
            $revisions = $service->revisions->listRevisions($id);
            $revs = array();
            foreach ($revisions as $revision) {
                if($revision->id!==$file->headRevisionId){
                    $rev = new stdClass();
                    $rev->id = $id;
                    $rev->id_version = $revision->id;
                    $rev->size = $revision->fileSize;
                    $rev->created_time = date('Y-m-d H:i:s',strtotime($revision->modifiedDate));
                    $revs[] = $rev;
                }
            }
            return $revs;
            
            
        }catch(Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    public function deleteRevision($id,$revision=null,$cloud_id=null){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);

        try {
            $service = new Google_Service_Drive($client);
            $file = $service->files->get($id);
                        
            if($cloud_id!==null){
                $found = false;
                foreach ($file->getParents() as $parent) {
                    if($parent->id==$cloud_id){
                        $found = true;
                        break;
                    }
                }
                if(!$found){
                    return false;
                }
            }
            $service->revisions->delete($id,$revision);
            return true;
        }  catch (Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }

        
    }

}

?>
