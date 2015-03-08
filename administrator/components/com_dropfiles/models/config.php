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

jimport('joomla.application.component.modeladmin');

class DropfilesModelConfig extends JModelAdmin
{
        
   public function getForm($data = array(), $loadData = true){
        //Get the theme
        $theme =  $this->getCurrentTheme();

        // Add the search path for the admin component config.xml file.
        JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_dropfiles');

        // Get the form.
        $xmlform = '<form>
            <fieldset>
                
            </fieldset>
        </form>';
        $form = $this->loadForm('com_dropfiles.config', $xmlform, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
                return false;
        }

        // If type is already known we can load the plugin form
        JPluginHelper::importPlugin('dropfilesthemes');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('getConfigForm',array($theme,&$form));

        if (isset($loadData) && $loadData){
                // Get the data for the form.
                $data = $this->loadFormData();
                $form->bind($data);
        }

        return $form;
    } 
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
//    protected function loadFormData()
//    {
//        // Check the session for previously entered form data.
//        $data = $this->getParams();
//        return array('params'=>$data);
//    }

    public function save($data) {
        $dbo = $this->getDbo();
        $query = 'UPDATE #__dropfiles SET params='.$dbo->quote(json_encode($data['params'])).' WHERE id='.(int)$data['id'];
        $dbo->setQuery($query);
        if($dbo->query()){
            return true;
        }
        return false;
    }
    
    /**
     * Get the params from a gallery id
     * @param type $id
     * @return boolean
     */
    public function getParams($id){
        $dbo = $this->getDbo();
        $query = 'SELECT params FROM #__dropfiles WHERE id='.(int)$id;
        $dbo->setQuery($query);
        if($dbo->query()){
            return json_decode($dbo->loadResult());
        }
        return false;
    }

    
    public function getCurrentTheme($id){
        $dbo = $this->getDbo();
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadResult();
        }
        return 'default';
    }
    
    public function setTheme($theme,$id){
        $dbo = $this->getDbo();
        $query = 'UPDATE #__dropfiles SET theme='.$dbo->quote($theme).' WHERE id='.(int)$id;
        $dbo->setQuery($query);
        if($dbo->query()){
            return true;
        }
        return false;
    }

    
}