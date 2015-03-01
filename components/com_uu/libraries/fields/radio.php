<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ROOT .'/components/com_uu/libraries/fields/customfield.php');

class FieldRadio extends CustomField implements uuFieldInterface
{

    public function getSqlType() {
        return  "tinytext";
    }

    public function hasOptions(){
        return true;
    }

	public function getFieldHTML( $field , $required)
	{

        $params	= new UuParameter($field->params);
        $readonly	= $params->get('readonly') ? ' disabled' : '';

        $class		= ($field->required == 1) ? ' required validate-custom-radio ' : '';

		$class	.= !empty( $field->description ) ? ' uuNameTips tipRight' : '';
        $class	.= !empty( $readonly) ? ' readonly' : '';
        $disabled = !empty( $readonly) ? 'disabled' : '';


        $style 				= ' style="margin: 0 5px 0 0;' .$this->getStyle() . '" ';

        $field->options = $this->options;

        $html	    = '<div class="' . $class . '" style="display: inline-block;" title="' . UStringHelper::escape( JText::_( $field->description ) ). '">';

        if( is_array( $field->options ) ) {

            foreach ($field->options as $key => $option) {
                $checked = ($option->value == $field->value) ? ' checked="checked"' : '';

                $html .= '<label class="lblradio-block">';
                $html .= '<input type="radio" id="jform_' . $field->fieldcode . $key . '" name="jform[' . $field->fieldcode . '][]" value="' . UStringHelper::escape($option->value) . '"' . $checked . ' '.$disabled.' class="radio ' . $class . '" ' . $style . ' />';
                $html .= JText::_($option->title) . '</label>';
            }
        }
        $html   .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display: none;">&nbsp;</span>';
        $html	.= '</div>';

        return $html;

	}
	
	public function isValid( $value , $required )
	{
		if( ($required && empty($value)) )
		{
			return false;
		}
		
		return true;
	}	
}
