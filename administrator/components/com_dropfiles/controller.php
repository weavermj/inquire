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

jimport('joomla.application.component.controllerlegacy');

class DropfilesController extends JControllerLegacy
{
      
    /**
    * default view for this controller
    */
    protected $default_view = 'dropfiles';
    
    /**
     * Method to display the view.
     *
     * @access	public
     */
    function display($cachable = false, $urlparams = false) 
    {
        // Load the submenu.
        DropfilesHelper::addSubmenu(JRequest::getCmd('view', $this->default_view ));
        
        
        $vName = JRequest::getCmd('view', $this->default_view);
        $layout = JRequest::getCmd('layout','default');

            if($vName=='dropfiles'){
                $view = $this->getView($vName,'html');        
                $modelCategory = $this->getModel('category');
                $view->setModel($modelCategory, false);
                $modelCategories = $this->getModel('categories');
                $view->setModel($modelCategories, false);
                $modelFiles = $this->getModel('files');
                $view->setModel($modelFiles, false);
            }elseif($vName=='category' && $layout=='default'){
                $view = $this->getView($vName,'raw');
                $model = $this->getModel('category');
                $view->setModel($model, false);
            }elseif($vName=='files'){
                $view = $this->getView($vName,'raw');
                $model = $this->getModel('category');
                $view->setModel($model, false);
            }
//        }
        parent::display($cachable, $urlparams);
        return $this;
    }
    
    public function htaccessdo(){
        DropfilesBase::edithtaccess();
    }
}
