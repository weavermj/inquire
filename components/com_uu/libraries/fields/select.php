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

class FieldSelect extends CustomField implements uuFieldInterface
{

    public function getSqlType() {
        return  "tinytext";
    }

    public function hasOptions(){
        return true;
    }

	public function getFieldHTML( $field , $required, $isDropDown = true)
	{

        //TODO in readonly mode replace the select by an input to remove the arrow.
        $params	= new UuParameter($field->params);
        $readonly	= $params->get('readonly') ? ' disabled' : '';

		$class		= ($field->required == 1) ? ' required' : '';
		$class	.= !empty( $field->description ) ? ' uuNameTips tipRight' : '';
        $class	.= !empty( $readonly) ? ' readonly' : '';

		$optionSize	= 1; // the default 'select below'

        $field->options = $this->options;

		if( !empty( $field->options ) )
		{
			$optionSize	+= count($field->options);
		}
		
		$dropDown	= ($isDropDown) ? '' : ' size="'.$optionSize.'"';

		$html		= '<select id="jform_'.$field->fieldcode.'" name="jform[' . $field->fieldcode . ']"' . $dropDown . ' class="select'.$class.'" title="' . UStringHelper::escape( JText::_( $field->description ) ). '" style="'.$this->getStyle().'" size="'.$this->params->get('size').'" '.$readonly.'>';
		
		$defaultSelected	= '';
		
		//@rule: If there is no value, we need to default to a default value
		if(empty( $field->value ) )
		{
			$defaultSelected	.= ' selected="selected"';
		}
		
		if($isDropDown)
		{
			$html	.= '<option value="" ' . $defaultSelected . '>' . JText::_('COM_UU_SELECT_BELOW') . '</option>';
		}	
		
		if( !empty( $field->options ) )
		{
			$selectedElement	= 0;

			foreach( $field->options as $option )
			{
				$selected	= ( $option->value == $field->value ) ? ' selected="selected"' : '';
				
				if( !empty( $selected ) )
				{
					$selectedElement++;
				}
				
				$html	.= '<option value="' . UStringHelper::escape( $option->value ) . '"' . $selected . '>' . JText::_( $option->title ) . '</option>';
			}

			if($selectedElement == 0)
			{
				//if nothing is selected, we default the 1st option to be selected.
				$eleName	= 'jform_'.$field->fieldcode;
				$html			.=<<< HTML
					   <script type='text/javascript'>
						   var slt = document.getElementById('$eleName');
						   if(slt != null)
						   {
						       slt.options[0].selected = true;
						   }
					   </script>
HTML;
			}
		}
		$html	.= '</select>';
		$html   .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display:none;">&nbsp;</span>';
		
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
