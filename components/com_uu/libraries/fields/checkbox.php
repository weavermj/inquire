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

class FieldCheckbox extends CustomField implements uuFieldInterface
{

    public function getSqlType() {
        return  "tinytext";
    }

    public function hasOptions(){
        return true;
    }

    public function getFieldHTML( $field , $required, $isDropDown = false )
    {

        $params	= new UuParameter($field->params);
        $readonly	= $params->get('readonly') ? ' disabled=disabled' : '';

        $class	= ($field->required == 1) ? ' required validate-custom-checkbox' : '';
        $class	.= !empty( $field->description ) ? ' uuNameTips tipRight' : '';
        $class	.= !empty( $readonly) ? ' readonly' : '';


        $field->options = $this->options;
        $lists = array();
        //a fix for wrong data input
        $field->value= JString::trim($field->value);

        if(is_array($field->value)){
            $tmplist = $field->value;
        } else {
//            if(JString::strrpos($field->value,',') == (JString::strlen($field->value) - 1)) {
//                $field->value = JString::substr($field->value,0,-1);
//            }
            $tmplist	 = explode(',', $field->value);
        }
        if($tmplist){
            foreach($tmplist as $value){
                $lists[] = JString::trim( $value );
            }
        }
        $html				= '';
        $elementSelected	= 0;
        $elementCnt	        = 0;
        $style 				= ' style="margin: 0 5px 5px 0;' .$this->getStyle() . '" ';
        $cnt = 0;
        //CFactory::load( 'helpers' , 'string' );
        $class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';

        $html	.= '<div class="' . $class . '" style="display: inline-block;" title="' . UStringHelper::escape( JText::_( $field->description ) ). '">';
        //$html	.= '<div id="jform_'.$field->fieldcode.'">';

        if( is_array( $field->options ) )
        {
            foreach( $field->options as $key =>$option )
            {
                if(trim($option->value)==''){
                    //do not display blank options
                    continue;
                }
                $selected	= in_array(trim( $option->value ) , $lists ) ? ' checked="checked"' : '';

                if( empty( $selected ) )
                {
                    $elementSelected++;
                }
                $html .= '<label class="lblradio-block">';
                $html .= '<input type="checkbox" id="jform_'.$field->fieldcode.$key.'" name="jform[' . $field->fieldcode . '][]" value="' . $option->value . '"' . $selected . ' class="checkbox '.$class . '"' .$style.'"'.$readonly.' />';
                $html .= JText::_( $option->title ) . '</label>';
                $elementCnt++;

            }
        }
        //$html   .= '</div>';
        $html   .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display: none;">&nbsp;</span>';
        $html	.= '</div>';

        return $html;
    }

    public function isValid( $value , $required )
    {
        if( $required && empty($value))
        {
            return false;
        }
        return true;
    }

//    public function formatdata( $value )
//    {
//        $finalvalue = '';
//        if(!empty($value))
//        {
//            foreach($value as $listValue){
//                $finalvalue	.= $listValue . ',';
//            }
//        }
//        return $finalvalue;
//    }
}