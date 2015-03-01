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
class FieldText extends CustomField implements uuFieldInterface
{

    public function getSqlType()
    {
        return 'varchar(255)';
    }

    public function hasOptions(){
        return false;
    }

	public function getFieldHTML( $field , $required )
	{
        $params	= new UuParameter($field->params);

		$readonly	= $params->get('readonly') ? ' readonly="readonly"' : '';

		$style 				= $this->getStyle()?' style="' .$this->getStyle() . '" ':'';

		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;
		$class	= ($field->required > 0) ? ' required' : '';
		$class	.= !empty( $field->description ) ? ' uuNameTips tipRight' : '';
        $class	.= !empty( $readonly) ? ' readonly' : '';
        $class  .= !empty( $field->core) ? ' validate-'.$field->fieldcode : '';


		$html	= '<input title="' . UStringHelper::escape( JText::_( $field->description ) ).'" type="text" value="' . $field->value . '" id="jform_' . $field->fieldcode . '" name="jform[' . $field->fieldcode . ']" maxlength="' . $field->max . '" size="40" class=" tipRight' . $class . '" '.$style.$readonly.' />';
		$html   .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display:none;">&nbsp;</span>';

		return $html;
	}

	public function isValid( $value , $required )
	{
		if( $required && empty($value))
		{
			return false;
		}
		//validate string length
		if(!$this->validLength($value)){
			return false;
		}
		return true;
	}


}