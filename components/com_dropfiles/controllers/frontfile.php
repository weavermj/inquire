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

//-- No direct access
defined('_JEXEC') || die('=;)');


jimport('joomla.application.component.controller');
jimport( 'joomla.filesystem.file' );

class dropfilesControllerFrontfile extends JControllerLegacy
{
    public function getModel($name = 'frontfile', $prefix = 'dropfilesModel', $config = array('ignore_request' => true))
    {
            $model = parent::getModel($name, $prefix, $config);

            return $model;
    }


    public function download(){
        $model = $this->getModel('frontfile');

        $id = JFactory::getApplication()->input->getString('id', 0);
        $catid = JFactory::getApplication()->input->getInt('catid', 0);

        $catmod = $this->getModel('frontcategory');

        $category = $catmod->getCategory($catid);
        $user	= JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();

        if (!in_array($category->access, $groups)) {
            $token = JRequest::getString('token');
            $modelTokens =  $this->getModel('tokens');
            $modelTokens->removeTokens();
            $tokenId = $modelTokens->tokenExists($token);
            if($tokenId){
                $modelTokens->updateToken($tokenId);
            }else{
                $this->setRedirect('index.php',JText::_('JERROR_ALERTNOAUTHOR'));
                $this->redirect();
            }
        }

        switch ($category->type) {
            case 'googledrive':
                JLoader::register('DropfilesGoogle', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesGoogle.php');
                $google = new dropfilesGoogle();
                $file = $google->download($id,$category->cloud_id);

                if(!is_object($file)){
                    $this->setRedirect('index.php');
                    $this->redirect();
                }

                $google->incrHits($id);

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
                $file = $model->getFile($id);
                /* -------- */
                // MW Added 27-09-2015 for Inquire
                if (!empty($file->description)) {
                    $custom_filename = htmlspecialchars($file->description);
                } else {
                    $custom_filename = htmlspecialchars($file->title.'.'.$file->ext);
                }
                /* -------- */
                if(!$file){
                    $this->setRedirect('index.php');
                    $this->redirect();
                }
                $model->hit($id);
                if($file->id){
                    JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesBase.php');
                    $sysfile = dropfilesBase::getFilesPath($file->catid).'/'.$file->file;
                    if(file_exists($sysfile)) {
                        header('Content-Disposition: attachment; filename="'.$custom_filename.'"');
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
}
