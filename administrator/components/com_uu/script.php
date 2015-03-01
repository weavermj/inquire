<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access to this file
defined('_JEXEC') or die;


jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_uuInstallerScript
{
    function install($parent)
    {
        $this->addPref();
        $this->addDefaultField();
    }

    function uninstall($parent)
    {
    }

    function update($parent)
    {
        $this->addPref();
        $this->addDefaultField();
    }

    function preflight($type, $parent)
    {
    }

    function postflight($type, $parent)
    {
    }

    private function addPref() {
        $allPref = array();
        $db = JFactory::getDbo();
        $allPref['enable_user_registration'] =  '1';
        $allPref['email_as_username'] =  '0';
        $allPref['enable_password_display'] =  '1';
        $allPref['approval_mail_subject'] =  'Your Registration is pending approval';
        $allPref['approval_mail_body'] =  '<p>Hello {hi_name},<br />Thank you for registering at {sitename}.<br />When the admins approve your account you will recieve an email. After that you may log in using the username and password you registered with.</p>';
        $allPref['activation_mail_subject'] =  'Your Registration is pending activation';
        $allPref['activation_mail_body'] =  '<p>Hello {hi_name},<br /><br />Thank you for registering at {sitename}. Your account is created and must be activated before you can use it.<br />To activate the account click on the following link or copy-paste it in your browser:<br />{activate_link}<br /><br />After activation you may login to {siteurl} using the following username and password:<br /><br />Username: {username}<br />Password: {password}</p>';
        $allPref['admin_activation_mail_subject'] =  'Account Details for {name} at {sitename}';
        $allPref['admin_activation_mail_body'] =  '<p>Hello {name},<br /><br />Thank you for registering at {sitename}. Your account is created and must be verified before you can use it.<br />To verify the account click on the following link or copy-paste it in your browser:<br />{activate_link}<br /><br />After verification an administrator will be notified to activate your account. You\'ll receive a confirmation when it\'s done.<br />Once that account has been activated you may login to {username} using the following username and password:<br /><br />Username: {username}<br />Password: {password}</p>';
        $allPref['admin_activation_mail_body_nopw'] =  '<p>Hello {name},<br /><br />Thank you for registering at {sitename}. Your account is created and must be verified before you can use it.<br />To verify the account click on the following link or copy-paste it in your browser:<br />{activate_link}<br /><br />After verification an administrator will be notified to activate your account. You\'ll receive a confirmation when it\'s done.<br />Once that account has been activated you may login to {username} using the following username and the password you entered during registration:<br /><br />Username: {username}</p>';
        $allPref['activation_mail_body_nopw'] =  '<p>Hello {hi_name},<br /><br />Thank you for registering at {sitename}. Your account is created and must be activated before you can use it.<br />To activate the account click on the following link or copy-paste it in your browser:<br />{activate_link}<br /><br />After activation you may login to {siteurl} using the following username and the password you entered during registration:<br /><br />Username: {username}</p>';
        $allPref['registered_mail_subject'] =  'Account Details for {name} at {sitename}';
        $allPref['registered_mail_body'] =  '<p>Hello {hi_name},<br /><br />Thank you for registering at {sitename}.<br /><br />You may now log in to {siteurl} using the username and password you registered with.</p>';
        $allPref['activate_by_admin_activation_mail_subject'] =  'Registration approval required for account of {hi_name} at {sitename}';
        $allPref['activate_by_admin_activation_mail_body'] =  '<p>Hello {hi_name},<br /><br />Your account has been activated by an administrator. You can now login at {siteurl} using the username {username} and the password you chose while registering.</p>';
        $allPref['red_login_success'] =  'home';
        $allPref['red_login_success_custom'] =  '';
        $allPref['red_logout_success'] =  'home';
        $allPref['red_logout_success_custom'] =  '';
        $allPref['red_registration_success'] =  'home';
        $allPref['red_registration_success_custom'] =  '';
        $allPref['red_registration_with_activation'] =  'home';
        $allPref['red_registration_with_activation_custom'] =  '';
        $allPref['red_registration_with_approval'] =  'home';
        $allPref['red_registration_with_approval_custom'] =  '';
        $allPref['red_activation_success'] =  'home';
        $allPref['red_activation_success_custom'] =  '';
        $allPref['red_activation_with_approval'] =  'home';
        $allPref['red_activation_with_approval_custom'] =  '';
        $allPref['reset_mail_subject'] =  'Your {sitename} password reset request';
        $allPref['reset_mail_body'] =  '<p>Hello,<br /><br />A request has been made to reset your {sitename} account password. To reset your password, you will need to submit this verification code in order to verify that the request was legitimate.<br /><br />The verification code is {reset_token}<br /><br />Click on the URL below to enter the verification code and proceed with resetting your password.<br /><br />{reset_confirm_link}<br /><br />Thank you.</p>';
        $allPref['notification_to_admin_mail_subject'] =  'Account Details for {name} at {sitename}';
        $allPref['notification_to_admin_mail_body'] =  'Hello administrator, <br/><br/>A new user {name}, username {username}, has registered at {sitename}.';
        $allPref['registation_text_intro'] =  '';
        $allPref['registration_text_concluding'] =  '';
        $allPref['enable_terms'] =  '0';
        $allPref['enable_terms_url'] =  '';

        $query = "INSERT IGNORE INTO `#__uu_configuration` (`key`,`value`) VALUES ";
        foreach($allPref as $key => $value){
            $query .= '('.$db->Quote($key).','.$db->Quote($value).'),';
        }
        $query = rtrim($query,',');
        $db->setQuery($query);
        $db->query();


    }

    private function addDefaultField() {
        $db = JFactory::getDbo();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (1, 'group', 'Default Joomla', '', 0, 1, 1, 0, 1, 1, '', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (2, 'text', 'Name', '', 1, 2, 2, 2, 2, 1, 'name', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (3, 'text', 'Username', '', 1, 3, 2, 2, 2, 2, 'username', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (5, 'email', 'Email', '', 1, 4, 2, 2, 2, 1, 'email1', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (4, 'email', 'Email Confirmation', '', 1, 5, 2, 2, 2, 2, 'email2', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (6, 'password', 'Password', '', 1, 6, 2, 2, 2, 1, 'password1', '{\"size\":\"20\"}')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (7, 'password', 'Password Confirmation', '', 1, 7, 2, 2, 2, 1, 'password2', '')");
        $db->query();
        $db->setQuery("INSERT IGNORE #__uu_fields (`id`, `type`, `name`, `description`, `core`, `ordering`, `published`, `required`, `registration`, `editable`, `fieldcode`, `params`) VALUES (8, 'group', 'Extra Info', 'Extra Info Description', 0, 8, 1, 0, 1, 1, '', '')");
        $db->query();

    }





}
