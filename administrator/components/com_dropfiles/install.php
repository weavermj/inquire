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


class com_dropfilesInstallerScript{
            
        public function __construct() {
            $this->oldRelease = $this->getVersion('com_dropfiles');
        }
    
        /**
         * method to install the component
         *
         * @return void
         */
        static function install($parent) 
        {
            $lang = JFactory::getLanguage();
            $lang->load('com_dropfiles.sys',JPATH_BASE.'/components/com_dropfiles',null,true);
            $dbo = JFactory::getDbo();
            $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles_files` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `catid` int(11) NOT NULL,
                        `file` varchar(100) NOT NULL,
                        `state` int(11) NOT NULL,
                        `ordering` int(11) NOT NULL,
                        `title` varchar(200) NOT NULL,
                        `description` text NOT NULL,
                        `ext` varchar(20) NOT NULL,
                        `size` int(11) NOT NULL,
                        `hits` int(11) NOT NULL,
                        `version` varchar(20) NOT NULL,
                        `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `author` varchar(100) NOT NULL,
                        `language` char(7) NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `id_gallery` (`catid`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles` (
                        `id` int(11) NOT NULL,
                        `type` VARCHAR( 20 ) NOT NULL,
                        `cloud_id` VARCHAR( 200 ) NOT NULL,
                        `params` text NOT NULL,
                        `theme` varchar(20) NOT NULL,
                        UNIQUE KEY `id` (`id`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles_tokens` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `id_user` int(11) NOT NULL,
                        `time` varchar(15) NOT NULL,
                        `token` varchar(32) NOT NULL,
                        PRIMARY KEY (`id`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $dbo->setQuery($query);
            if(!$dbo->query()){}

            $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles_versions` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `id_file` int(11) NOT NULL,
                        `file` varchar(100) NOT NULL,
                        `ext` varchar(100) NOT NULL,
                        `size` int(11) NOT NULL,
                        `created_time` datetime NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `id_file` (`id_file`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            $query = "INSERT INTO `#__dropfiles` (`theme`) VALUES (
                        'default'
                      );";
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
            $dbo = JFactory::getDbo();
            // $parent is the class calling this method
            if(version_compare($this->oldRelease, '2.0.0','lt')){
                $query = 'SELECT * FROM #__dropfiles LIMIT 0, 1';
                $dbo->setQuery($query);
                $current = $dbo->loadObject();
                
                $query = 'DELETE FROM  `#__dropfiles`';
                $dbo->setQuery($query);
                if(!$dbo->query()){}

                $query = 'ALTER IGNORE TABLE  `#__dropfiles` ADD `id` INT NOT NULL FIRST;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER IGNORE TABLE  `#__dropfiles` ADD PRIMARY KEY (`id`);';
                $dbo->setQuery($query);
                if(!$dbo->query()){}

                $query = 'ALTER TABLE  `#__dropfiles` ADD  `type` VARCHAR( 20 ) NOT NULL AFTER  `id` ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE  `#__dropfiles` ADD  `cloud_id` VARCHAR( 200 ) NOT NULL AFTER  `type` ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}

                $query = 'INSERT INTO `#__dropfiles` (`id`,`theme`,`params`) SELECT id,'.$dbo->quote($current->theme).','.$dbo->quote($current->params).' FROM `#__categories` WHERE extension="com_dropfiles";';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles_tokens` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                            `id_user` int(11) NOT NULL,
                            `time` varchar(15) NOT NULL,
                            `token` varchar(32) NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = "CREATE TABLE IF NOT EXISTS `#__dropfiles_versions` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `id_file` int(11) NOT NULL,
                        `file` varchar(100) NOT NULL,
                        `ext` varchar(100) NOT NULL,
                        `size` int(11) NOT NULL,
                        `created_time` datetime NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `id_file` (`id_file`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
            if($type=='uninstall'){
//                if(JFactory::getApplication()->input->getBool('confirm', false)===false){
//                    Jerror::raiseWarning(null, JText::printf('COM_DROPFILES_INSTALLER_UNINSTALL_DB', JFactory::getApplication()->input->getInt('cid', 0)));
//                    $controller = new JController();
//                    $controller->setRedirect('index.php?option=com_installer&view=install');
//                    $controller->redirect();
//                }
            }elseif($type=='update'){
                if(version_compare($this->oldRelease, $parent->get( 'manifest' )->version,'gt')){
                    Jerror::raiseWarning(null, 'You already have a newer version of Dropfiles');
                    jimport('joomla.application.component.controler');
                    $controller = new JController();
                    $controller->setRedirect('index.php?option=com_installer&view=install');
                    $controller->redirect();
                    return false;
                }
            }
            else{
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                $this->release = $parent->get( 'manifest' )->version;
                $jversion = new JVersion();
                // abort if the current Joomla release is older
                if( version_compare( $jversion->getShortVersion(), '2.5.6', 'lt' ) ) {
                        Jerror::raiseWarning(null, 'Cannot install Dropfiles component in a Joomla release prior to 2.5.6');
                        $controller = new JController();
                        $controller->setRedirect('index.php?option=com_installer&view=install');
                        $controller->redirect();
                        return false;
                }
            }
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
            if($type=='install'){
                $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
                require_once $basePath . '/models/category.php';
                $config = array( 'table_path' => $basePath . '/tables');
                $catmodel = new CategoriesModelCategory( $config);
                $catData = array( 'id' => 0, 'parent_id' => 1, 'level' => 1, 'extension' => 'com_dropfiles'
                , 'title' => JText::_('COM_DROPFILES_INSTALLER_NEW_CATEGORY'), 'alias' => 'new-category',  'published' => 1, 'language' => '*','associations'=>array());
                $status = $catmodel->save( $catData);

                if(!$status) 
                {
                  JError::raiseWarning(500, JText::_('Unable to create default content category!'));
                }
            }
            if($type=='install' || $type=='update'){
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                $lang = JFactory::getLanguage();
                $lang->load('com_dropfiles.sys',JPATH_BASE.'/components/com_dropfiles',null,true);

                $manifest = $parent->get('manifest');
                JLoader::register('DropfilesInstallerHelper', JPATH_ADMINISTRATOR . '/components/com_dropfiles/helpers/installer.php');
                echo '<h2>'.JText::_('COM_DROPFILES_INSTALLER_TITLE').'</h2>';
                echo JText::_('COM_DROPFILES_INSTALLER_MSG');


                $extensions = $manifest->extensions;

                foreach($extensions->children() as $extension){
                        $folder = $extension->attributes()->folder;
                        $enable = $extension->attributes()->enable;
                        if(DropfilesInstallerHelper::install(JPATH_ADMINISTRATOR.'/components/com_dropfiles/extensions/'.$folder,$enable)){
    //                        JFile::delete(JPATH_ADMINISTRATOR.'/components/com_sponsorshipreward/extensions/'.$folder);
                            echo '<img src="'. JURI::root().'/components/com_dropfiles/assets/images/tick.png" />'.$folder.' : '.JText::sprintf('COM_DROPFILES_INSTALLER_EXT_OK','').'<br/>';
                        }else{
                            echo '<img src="'. JURI::root().'/components/com_dropfiles/assets/images/exclamation.png" />'.$folder.' : '.JText::sprintf('COM_DROPFILES_INSTALLER_EXT_NOK','').'<br/>';
                        }
                }                

                //Set the default parameters
                if($type=='install'){
                    $component = JComponentHelper::getComponent('com_dropfiles');
                    $data['params']['jquerybase'] =  '1';
                    $data['params']['allowedext'] =  '7z,ace,bz2,dmg,gz,rar,tgz,zip,csv,doc,docx,html,key,keynote,odp,ods,odt,pages,pdf,pps,ppt,pptx,rtf,tex,txt,xls,xlsx,xml,bmp,exif,gif,ico,jpeg,jpg,png,psd,tif,tiff,aac,aif,aiff,alac,amr,au,cdda,flac,m3u,m4a,m4p,mid,mp3,mp4,mpa,ogg,pac,ra,wav,wma,3gp,asf,avi,flv,m4v,mkv,mov,mpeg,mpg,rm,swf,vob,wmv';
                    $data['params']['import'] =  '0';
             
                    $max_upload = (int)(ini_get('upload_max_filesize'));
                    $max_post = (int)(ini_get('post_max_size'));
                    $memory_limit = (int)(ini_get('memory_limit'));
                    $maxupload = min($max_upload, $max_post, $memory_limit);
                    
                    $data['params']['maxinputfile'] =  $maxupload;
                    
                    $table	= JTable::getInstance('extension');
                    // Load the previous Data
                    if (!$table->load($component->id)) {

                            return false;
                    }
                    // Bind the data.
                    if (!$table->bind($data)) {

                            return false;
                    }

                    // Check the data.
                    if (!$table->check()) {

                            return false;
                    }

                    // Store the data.
                    if (!$table->store()) {

                            return false;
                    }
                }
//                echo '<p><img src="http://www.joomunited.com/images/dropfiles/dropfiles-summary.gif" alt="Dropfiles explanation" /></p>';
                
                //Test if htaccess is enabled
                JLoader::register('DropfilesBase', JPATH_ADMINISTRATOR.'/components/com_dropfiles/classes/dropfilesBase.php');
                jimport( 'joomla.filesystem.file' );
                $file_dir = dropfilesBase::getFilesPath();
                if(!file_exists($file_dir)){
                    JFolder::create($file_dir);
                    $data = '<html><body bgcolor="#FFFFFF"></body></html>';
                    JFile::write($file_dir.'index.html', $data);
                    $data = 'deny from all';
                    JFile::write($file_dir.'.htaccess', $data);
                }
		
		//Check if htaccess file is up to date
		if(file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'.htaccess')){
		    $lines = file(JPATH_ROOT.DIRECTORY_SEPARATOR.'.htaccess');
		    foreach($lines as $line){		      
		      if(strpos($line, 'RewriteCond %{REQUEST_URI} /component/|(/[^.]*|\.(php|html?|feed|pdf|vcf|raw))$ [NC]') === 0){
			  echo '<p><img src="'. JURI::root().'/components/com_dropfiles/assets/images/exclamation.png" /><b>'.JText::_('COM_DROPFILES_INSTALLER_HTACCESS_OLD').' <a target="_blank" href="http://www.joomunited.com/joomunited-faq/41-dropfiles-faq">FAQ</a></b></p>';
			  break;
		      }
		    }
		    
		}
		
            }
            
            return true;
        }
        
        /**
        * Method to get the version of a component
        * @param string $option
        * @return null
        */
        private function getVersion($option){
                $manifest = self::getManifest($option);
                if(property_exists($manifest, 'version')){
                         return $manifest->version;
                }
                return null;
        }
        
        /**
        * Method to get an object containing the manifest values
        * @param string $option
        * @return object
        */
        private function getManifest($option){
//                $component = JComponentHelper::getComponent($option);
                $dbo = JFactory::getDbo();
                $query = 'SELECT extension_id FROM #__extensions WHERE element='.$dbo->quote($option).' AND type="component"';
                if(!$dbo->setQuery($query)){
                    return false;
                }
                if(!$dbo->query()){
                    return false;
                }
                $component = $dbo->loadResult();
                if(!$component){
                    return false;
                }
                $table	= JTable::getInstance('extension');
                // Load the previous Data
                if (!$table->load($component,false)) {
                         return false;
                }
                return json_decode($table->manifest_cache);
        }
        
}