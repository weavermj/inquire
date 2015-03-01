<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="width-50 fltlft">
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_REGISTRATION'); ?></legend>
	<ul class="adminformlist">
            <table>
            <tr>
                <td><?php echo $this->form->getLabel('enable_user_registration'); ?></td>
                <td><?php echo $this->form->getInput('enable_user_registration'); ?></td>
                <td><span class="faux-label"><?php echo JText::_($this->form->getFieldAttribute('enable_user_registration','description')); ?></span></td>
            </tr>
            <tr>
                <td><?php echo $this->form->getLabel('email_as_username'); ?></td>
                <td><?php echo $this->form->getInput('email_as_username'); ?></td>
                <td> <span class="faux-label"><?php echo JText::_($this->form->getFieldAttribute('email_as_username','description')); ?></span></td>
            </tr>
            <tr>
                <td><?php echo $this->form->getLabel('enable_terms'); ?></td>
                <td><?php echo $this->form->getInput('enable_terms'); ?></td>
                <td> <span class="faux-label"><?php echo JText::_($this->form->getFieldAttribute('enable_terms','description')); ?></span></td>
            </tr>
            <tr>
                <td><?php echo $this->form->getLabel('enable_terms_url'); ?></td>
                <td><?php echo $this->form->getInput('enable_terms_url'); ?></td>
                <td> <span class="faux-label"><?php echo JText::_($this->form->getFieldAttribute('enable_terms_url','description')); ?></span></td>
            </tr>
            <tr>
                <td><?php echo $this->form->getLabel('registation_text_intro'); ?></td>
                <td colspan="2"><?php echo $this->form->getInput('registation_text_intro'); ?></td>
            </tr>
            <tr>
                <td><?php echo $this->form->getLabel('registration_text_concluding'); ?></td>
                <td colspan="2"><?php echo $this->form->getInput('registration_text_concluding'); ?></td>
            </tr>
        </table>
</fieldset>
</div>
<div class="width-50 fltrt">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_JOOMLA_REGISTRATION_PARAM'); ?></legend>
            <?php
                jimport('joomla.application.component.helper');
                $params = JComponentHelper::getParams('com_users');
            ?>
        <table>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_ALLOWREGISTRATION_LABEL')?></label></td>
                <td><input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $params->get('allowUserRegistration') == 1 ? JText::_('JYES'): JText::_('JNO') ?>"></td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_ALLOWREGISTRATION_DESC'); ?></span></td>
            </tr>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_SENDPASSWORD_LABEL')?></label></td>
                <td><input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $params->get('sendpassword')== 1 ? JText::_('JYES'): JText::_('JNO') ?>"></td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_SENDPASSWORD_DESC'); ?><span></td>
            </tr>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_LABEL')?></label></td>
                <td>
                    <?php switch ($params->get('useractivation')) {
                    case 0: $value = JText::_('JYES');
                        break;
                    case 1: $value = JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_SELFACTIVATION');
                        break;
                    case 2: $value = JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION');
                        break;
                }
                    ?>
                    <input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $value;?>">
                </td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_DESC'); ?></span></td>
            </tr>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_MAILTOADMIN_LABEL')?></label></td>
                <td><input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $params->get('mail_to_admin')== 1 ? JText::_('JYES'): JText::_('JNO') ?>"></td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_MAILTOADMIN_DESC'); ?></span></td>
            </tr>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_FRONTEND_USERPARAMS_LABEL')?></label></td>
                <td><input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $params->get('frontend_userparams') == 1 ? JText::_('JYES'): JText::_('JNO') ?>"></td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_FRONTEND_USERPARAMS_DESC'); ?></span></td>
            </tr>
            <tr>
                <td><label><?php echo JText::_('COM_USERS_CONFIG_FIELD_CHANGEUSERNAME_LABEL')?></label></td>
                <td><input type="text" readonly="readonly" class="inputbox readonly" value="<?php echo $params->get('change_login_name')== 1 ? JText::_('JYES'): JText::_('JNO') ?>"></td>
                <td><span class="faux-label"><?php echo JText::_('COM_USERS_CONFIG_FIELD_CHANGEUSERNAME_DESC'); ?></span></td>
            </tr>
        </table>
    </fieldset>
    </div>
    <div class="width-50 fltrt">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_PASSWORD'); ?></legend>
        <table>
            <tr>
                <td><?php echo $this->form->getLabel('enable_password_display'); ?></td>
                <td><?php echo $this->form->getInput('enable_password_display'); ?></td>
                <td><span class="faux-label"><?php echo JText::_($this->form->getFieldAttribute('enable_password_display','description')); ?></span></td>
            </tr>
        </table>
    </fieldset>
    </div>
<div style="clear: both;"></div>