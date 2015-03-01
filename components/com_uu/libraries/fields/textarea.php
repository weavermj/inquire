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

class FieldTextarea extends CustomField implements uuFieldInterface
{
    public function getSqlType() {
        return  "text";
    }

    public function hasOptions() {
        return false;
    }

	public function getFieldHTML($field, $required)
	{
		$params   = new UuParameter($field->params);
		$readonly = $params->get('readonly') ? ' readonly=""' : '';
		$style    = $this->getStyle() ? ' style="'.$this->getStyle().'" ' : '';

		//extract the max char since the settings is in params
		$max_char = $params->get('max_char');

		// If maximum is not set, we define it to a default
		$max_char = empty( $max_char ) ? 200 : $max_char;
		$class    = ($field->required > 0) ? ' required' : '';
		$class    .= !empty( $field->tips ) ? ' uuNameTips tipRight' : '';
        $class	  .= !empty( $readonly) ? ' readonly' : '';

        $html     = '<textarea id="jform_'.$field->fieldcode.'" name="jform['.$field->fieldcode.']" class="textarea'.$class.'" title="'.UStringHelper::escape( JText::_( $field->description ) ).'"'.$style.$readonly.'>'.$field->value.'</textarea>';
		$html     .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display:none;">&nbsp;</span>';
		//$html     .= '<script type="text/javascript">cvalidate.setMaxLength("#field'.$field->id.'", "'.$max_char.'");</script>';

		return $html;
	}

	public function isValid( $value , $required )
	{
		if ($required && empty($value))
		{
			return false;
		}
		//validate string length
		if ( ! $this->validLength($value))
		{
			return false;
		}

		return true;
	}
}
