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

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldSliderinit extends JFormField
{
	
	protected $type = 'Sliderinit';

	/**
	 */
	protected function getInput()
	{            
            
            $scripts = array();
            $styles = array();
            $styles[] = JURI::root().'components/com_dropfiles/assets/css/slider.css';
            $scripts[] = JURI::root().'components/com_dropfiles/assets/js/bootstrap-slider.js';
            $scripts[] = JURI::root().'components/com_dropfiles/assets/js/sliderfieldinit.js';
            

            $return = '';
            foreach ($scripts as $script){
                $return .= '<script type="text/javascript" src="'.$script.'"></script>';
            }
            foreach ($styles as $style){
                $return .= '<link rel="stylesheet" href="'.$style.'" type="text/css">';
            }
            return $return;
	}
        
        protected function getLabel() {
            return '';
        }
        
}
