<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
$fieldsets = $this->form->getFieldsets();

$conf = new UuConfig();
$required = false;
$newgroup = false;

?>
<div id="uu-wrap" class="profile<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<form id="uuForm" action="<?php echo JRoute::_('index.php?option=com_uu&task=profile.save'); ?>" method="post" class="uu-form-validate" enctype="multipart/form-data">
    <input type="hidden" name="option" value="com_uu" />
    <input type="hidden" name="task" value="profile.save" />
    <?php echo JHtml::_('form.token'); ?>

    <?php
    foreach ($this->extraFields as $key => $field):?>
    <?php
        //first element
        if ($key == 0) {
            echo '<ul class="cFormList cFormHorizontal cResetList">';
        }
        if ($field->type == 'group'){
            ?>
            <div class="ctitle">
                <h2><?php echo JText::_( $field->name ); ?></h2>
            </div>
            <?php
        }
        //not first element and it's a group
        if ($key > 0 &&  $field->type == 'group') {
            ?>
            </ul>
          <ul class="cFormList cFormHorizontal cResetList">
        <?php
        }

        if( !$required && $field->required > 0 ) {$required	= true;}

        //don't display email2
        if ($field->fieldcode == 'email2') {continue;}

        //don't display username if allow mail as username
        if ($conf->get('email_as_username') && $field->fieldcode == 'username') {continue;}

        if ($field->type != 'group') { ?>
            <li>
                <label id="lblfield<?php echo $field->id;?>" for="jform_<?php echo $field->fieldcode;?>" class="form-label"><?php if($field->required > 0) echo '*'; ?><?php echo JText::_($field->name); ?></label>
                <div class="form-field">
                    <?php echo $field->html; ?>
                </div>

            </li>
        <?php } ?>

        <?php endforeach;?>

        </ul>
        <ul class="cFormList cFormHorizontal cResetList">
        <?php

        if( $required )
        {
            ?>
            <li></li>
            <li class="has-seperator">
                <div class="form-field">
                    <span class="form-helper"><?php echo JText::_( 'COM_UU_REGISTRATION_REQUIRED_FILEDS' ); ?></span>
                </div>
            </li>
            <?php
        }
        ?>
        <li>
            <div class="form-field">
                <div id="cwin-wait" style="display:none;"></div>
                <div id="cwin-btn">
                    <input class="cButton cButton-Blue validateSubmit" onclick="submitbutton('uuForm')" type="submit" id="btnSubmit" value="<?php echo JText::_('JSUBMIT'); ?>" name="submit">
                    <?php echo JText::_('COM_UU_OR');?>
                    <a href="<?php echo JRoute::_('index.php?option=com_uu&view=profile');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
                </div>
            </div>
        </li>
        </ul>
    </form>
</div>


<script type="text/javascript">
    cvalidate.init();
    cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_UU_ENTRY_MISSING")); ?>');

    joms.jQuery( document ).ready( function(){
        //remove check username for an edit form
        //because it's readonly
        joms.jQuery('#jform_username').removeClass('validate-username');
        //set value in emailpass to bypass verification on no change
        joms.jQuery('#uuForm').append('<input type="hidden" name="emailpass" id="emailpass" value="N"/>');
        joms.jQuery('#emailpass').val(joms.jQuery('#jform_email1').val());

        //set required to password to no
        joms.jQuery('#jform_password1').removeClass('required');
        joms.jQuery('#jform_password2').removeClass('required');

    });



    // Password strenght indicator
    var password_strength_settings = {
        'texts' : {
            1 : '<?php echo addslashes(JText::_('COM_UU_PASSWORD_STRENGHT_L1')); ?>',
            2 : '<?php echo addslashes(JText::_('COM_UU_PASSWORD_STRENGHT_L2')); ?>',
            3 : '<?php echo addslashes(JText::_('COM_UU_PASSWORD_STRENGHT_L3')); ?>',
            4 : '<?php echo addslashes(JText::_('COM_UU_PASSWORD_STRENGHT_L4')); ?>',
            5 : '<?php echo addslashes(JText::_('COM_UU_PASSWORD_STRENGHT_L5')); ?>'
        }
    };

    joms.jQuery('#jform_password1').password_strength(password_strength_settings);

    function submitbutton(formId) {
        joms.jQuery('#cwin-btn').hide();
        joms.jQuery('#cwin-wait').show();
    }

</script>
