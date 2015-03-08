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

class FieldCaptcha extends CustomField implements uuFieldInterface
{

    public function getSqlType() {
        return  null;
    }

    public function hasOptions(){
        return false;
    }

    public function getFieldHTML( $field , $required, $isDropDown = false )
    {
        $params	= new UuParameter($field->params);

        //get language code
        $lang = JFactory::getLanguage();
        $tag = $lang->getTag();
        $temp = explode('-',$tag);
        if( !empty($temp[0]) ) {
            $lang_code = $temp[0];
        } else {
            $lang_code = 'en';
        }

        $js =" 	var RecaptchaOptions = {
                    theme : '".$params->get('theme')."',
                     lang : '.$lang_code.'
			    };
			 ";

        $document = JFactory::getDocument();
        $document->addScriptDeclaration($js);

        //use JPATH ROOT to be use from administrator section too.
        require_once(JPATH_ROOT.'/components/com_uu/libraries/captcha/recaptcha.php');

        $recaptcha = JXRecaptcha::getInstance();

        // set the API keys for reCAPTCHA
        $recaptcha->setKeyPair($params->get('recaptcha_public'), $params->get('recaptcha_private'));
        $uri = JFactory::getURI();

        $html = '<div class="recaptcha">';
        $html .= $recaptcha->renderCaptcha($uri->isSSL());
        //$html   .= '<span id="err_jform_'.$field->fieldcode.'_msg" style="display: none;">&nbsp;</span>';
        $html   .= '<span id="err_recaptcha_response_field_msg" style="display: none;">&nbsp;</span>';
        $html .= '</div>';
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

}