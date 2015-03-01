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
        <legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_REDIRECTION_LOGIN'); ?></legend>
        <ul class="adminformlist">
            <li>
                <?php echo $this->form->getLabel('red_login_success'); ?>
                <?php echo $this->form->getInput('red_login_success'); ?>
                <?php echo $this->form->getLabel('red_login_success_custom'); ?>
                <?php echo $this->form->getInput('red_login_success_custom'); ?>

            </li>
            <li>
                <?php echo $this->form->getLabel('red_logout_success'); ?>
                <?php echo $this->form->getInput('red_logout_success'); ?>
                <?php echo $this->form->getLabel('red_logout_success_custom'); ?>
                <?php echo $this->form->getInput('red_logout_success_custom'); ?>

            </li>
        </ul>
    </fieldset>
</div>
<div class="width-50 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_REDIRECTION_REGISTRATION'); ?></legend>
        <ul class="adminformlist">
            <li>
                <?php echo $this->form->getLabel('red_registration_success'); ?>
                <?php echo $this->form->getInput('red_registration_success'); ?>
                <?php echo $this->form->getLabel('red_registration_success_custom'); ?>
                <?php echo $this->form->getInput('red_registration_success_custom'); ?>

            </li>
            <li>
                <?php echo $this->form->getLabel('red_registration_with_activation'); ?>
                <?php echo $this->form->getInput('red_registration_with_activation'); ?>
                <?php echo $this->form->getLabel('red_registration_with_activation_custom'); ?>
                <?php echo $this->form->getInput('red_registration_with_activation_custom'); ?>

            </li>
            <li>
                <?php echo $this->form->getLabel('red_registration_with_approval'); ?>
                <?php echo $this->form->getInput('red_registration_with_approval'); ?>
                <?php echo $this->form->getLabel('red_registration_with_approval_custom'); ?>
                <?php echo $this->form->getInput('red_registration_with_approval_custom'); ?>

            </li>
        </ul>
    </fieldset>
</div>
<div class="width-50 fltlft" style="display: none">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_UU_CONFIGURATION_EDIT_REDIRECTION_ACTIVATION'); ?></legend>
        <ul class="adminformlist">
            <li>
                <?php echo $this->form->getLabel('red_activation_success'); ?>
                <?php echo $this->form->getInput('red_activation_success'); ?>
                <?php echo $this->form->getLabel('red_activation_success_custom'); ?>
                <?php echo $this->form->getInput('red_activation_success_custom'); ?>

            </li>
            <li>
                <?php echo $this->form->getLabel('red_activation_with_approval'); ?>
                <?php echo $this->form->getInput('red_activation_with_approval'); ?>
                <?php echo $this->form->getLabel('red_activation_with_approval_custom'); ?>
                <?php echo $this->form->getInput('red_activation_with_approval_custom'); ?>

            </li>
        </ul>
    </fieldset>
</div>

<div style="clear: both;"></div>

