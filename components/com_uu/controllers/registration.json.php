<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';


class UuControllerRegistration extends UuController
{


    //public function ajaxSetMessage($fieldName, $txtLabel = '', $strMessage, $strParam='', $strParam2='')
    public function ajaxSetMessage()
    {
        $input = JFactory::getApplication()->input;
        $fieldId   = $input->getString('fieldId',null);
        $txtLabel    = $input->getString('txtLabel', '');
        $strMessage  = $input->getString('strMessage', null);
        $strParam    = $input->getString('strParam', '');
        $strParam2   = $input->getString('strParam2', '');
        // $strParam pending filter
        $objResponse = new JAXResponse();

        $langMsg     = '';

        if ( ! empty($strMessage))
        {
            if ($strParam !='' && $strParam2 != '')
            {
                $langMsg = (empty($strParam)) ? JText::_($strMessage) : JText::sprintf($strMessage, $strParam, $strParam2);
            }
            else
            {
                $langMsg = (empty($strParam)) ? JText::_($strMessage) : JText::sprintf($strMessage, $strParam);
            }
        }
        $myLabel = ($txtLabel == 'Field') ? JText::_('COM_UU_FIELD') : $txtLabel;

        $langMsg = (empty($txtLabel)) ? $langMsg : $myLabel.' '.$langMsg;

        $objResponse->addScriptCall('joms.jQuery("#err_'.$fieldId.'_msg").html("<br />'.$langMsg.'");');
        $objResponse->addScriptCall('joms.jQuery("#err_'.$fieldId.'_msg").show();');

        return $objResponse->sendResponse();
    }

    public function ajaxCheckUserName()
    {

        $input = JFactory::getApplication()->input;
        $param   = $input->getString('param','');

        // $param pending filter
        $objResponse = new JAXResponse();

        $username    = $param;
        $ipaddress   = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $model       =  $this->getModel('registration');

        $isInvalid   = false;
        $msg         = '';

        if ( ! empty($username))
        {
            if ( ! UValidateHelper::username($username))
            {
                $isInvalid = true;
                $msg       = JText::_('COM_UU_IMPROPER_USERNAME');
            }
        }

        if ( ! empty($username) && ! $isInvalid)
        {
            $isInvalid = $model->isUserNameExists(array('username'=>$username, 'ip'=>$ipaddress));
            $msg       = JText::sprintf('COM_UU_USERNAME_EXIST', $username);
        }

        if ($isInvalid)
        {
            $objResponse->addScriptCall('joms.jQuery("#jform_username").addClass("invalid");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_username_msg").show();');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_username_msg").html("<br/>'.$msg.'");');
            $objResponse->addScriptCall('joms.jQuery("#usernamepass").val("N");');
            $objResponse->addScriptCall('joms.jQuery(cvalidate.customMessage = "'.$msg.'");');
            $objResponse->addScriptCall('false;');
        }
        else
        {
            $objResponse->addScriptCall('joms.jQuery("#jform_username").removeClass("invalid");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_username_msg").html("&nbsp");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_username_msg").hide();');
            $objResponse->addScriptCall('joms.jQuery("#usernamepass").val("'.$username.'");');
            $objResponse->addScriptCall('joms.jQuery(cvalidate.customMessage = "");');
            $objResponse->addScriptCall('true;');
        }

        return $objResponse->sendResponse();
    }


    public function ajaxCheckEmail($param = '')
    {

        $input = JFactory::getApplication()->input;
        $param   = $input->getString('param','');

        //$param pending filter
        $objResponse = new JAXResponse();

        $email       = $param;
        $ipaddress   = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $model       =  $this->getModel('registration');

        $isValid     = false;

        $user = JFactory::getUser();

        if ( ! empty($email))
        {
            $isExists = $model->isEmailExists(array('email'=>$email, 'ip'=>$ipaddress, 'userid'=>$user->get('id')));
            $isValid  = $isExists?false:true;
            $msg      = JText::sprintf('COM_UU_EMAIL_EXIST', $email);
        }

          //TODO implement this
//        if ($isValid && !$model->isEmailAllowed($email))
//        {
//            $isValid = false;
//            $msg     = JText::sprintf('COM_COMMUNITY_EMAILDOMAIN_DISALLOWED', $email);
//        }
//
//        if ($isValid && $model->isEmailDenied($email))
//        {
//            $isValid = false;
//            $msg     = JText::sprintf('COM_COMMUNITY_EMAILDOMAIN_DENIED', $email);
//        }

        if ( ! $isValid)
        {
            $objResponse->addScriptCall('joms.jQuery("#jform_email1").addClass("invalid");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_email1_msg").show();');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_email1_msg").html("<br/>'.$msg.'");');
            $objResponse->addScriptCall('joms.jQuery("#emailpass").val("N");');
            $objResponse->addScriptCall('joms.jQuery(cvalidate.customMessage = "'.$msg.'");');
            $objResponse->addScriptCall('false;');
        }
        else
        {
            $objResponse->addScriptCall('joms.jQuery("#jform_email1").removeClass("invalid");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_email1_msg").html("&nbsp");');
            $objResponse->addScriptCall('joms.jQuery("#err_jform_email1_msg").hide();');
            $objResponse->addScriptCall('joms.jQuery("#emailpass").val("'.$email.'");');
            $objResponse->addScriptCall('joms.jQuery(cvalidate.customMessage = "");');
            $objResponse->addScriptCall('true;');
        }

        return $objResponse->sendResponse();
    }

    public function ajaxCheckCaptcha($param = '')
    {
        $input = JFactory::getApplication()->input;
        $objResponse = new JAXResponse();

        // Initialise variables.
        jimport('joomla.application.component.model');
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_uu/models');
        $model = JModelLegacy::getInstance( 'Registration', 'UuModel' );

        //get captcha key
        $filter       = array('type'=>"'captcha'");
        $fields = $model->getRegistrationFields($filter);

        foreach ($fields as $field) {
            require_once (JPATH_ROOT .'/components/com_uu/libraries/parameter.php');
            $captcha_params	= new UuParameter($field->params);
            $recaptcha_public = $captcha_params->get('recaptcha_public');
            $recaptcha_private = $captcha_params->get('recaptcha_private');

        }

        // get a reCAPTCHA object
        require_once(JPATH_COMPONENT.'/libraries/captcha/recaptcha.php');
        $recaptcha = JXRecaptcha::getInstance();

        // set the API keys for reCAPTCHA
        $recaptcha->setKeyPair($recaptcha_public, $recaptcha_private);

        // validate the captcha
        if ($recaptcha->checkCaptcha()) {
            $objResponse->addClear('ret', '1');
            // save to a session
            $challenge = JRequest::getVar('recaptcha_challenge_field', null);
            $user =& JFactory::getUser();
            $user->set($challenge, 1);
        } else {
            $objResponse->addClear('ret', '0');
        }

        //$param pending filter
        return $objResponse->sendResponse();

    }

}