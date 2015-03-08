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

/**
 * Content Plugin.
 *
 * @package    dropfiles
 * @subpackage Plugin
 */
class plgSystemDropfiles extends JPlugin
{

    public function onAfterInitialise()
    {
       $app = JFactory::getApplication();
      // get the router
      if ($app->isSite()) {
        $router = $app->getRouter();                
        $router->attachParseRule(array($this, 'replaceRoute'));
      }
    }
    
    /**
     * @param   JRouterSite &$router  The Joomla Site Router
     * @param   JURI  &$uri  The URI to parse
     *
     * @return  array  $vars The array of processed URI variables
     */
    public function replaceRoute (&$router, &$uri)
    {
        $array = array();
        
        $params = JComponentHelper::getParams('com_dropfiles');
        $dropfilesUri = $params->get('uri','files');
        $dropfilesUriSegs = sizeof(explode('/',$dropfilesUri));
        $dropfilesSegs = explode('/',$dropfilesUri);
        
        $path = explode('/',$uri->getPath());
        
        if(count($dropfilesSegs) < count($path)){
            for ($index = $dropfilesUriSegs-1; $index < count($dropfilesSegs); $index++) {
                if($dropfilesSegs[$index]!==$path[$index]){
                    return $array;
                }
            }
            if(!isset($path[1]) || $path[1]===''){
                return $array;
            }
            
            JRequest::setVar('option','com_dropfiles');
            JRequest::setVar('format','');
            JRequest::setVar('task','frontfile.download');
            JRequest::setVar('catid',$path[$dropfilesUriSegs]);
            JRequest::setVar('id',$path[$dropfilesUriSegs+2]);
            JRequest::setVar('Itemid',1000000000000);
	    
	    $uri->reset();
        }
        
        return $array;            
     }
    
}
    