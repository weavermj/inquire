<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
JHtml::_('behavior.noframes');
//@bug on modal http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=27239
//use iframe handler
JHTML::_('behavior.modal', 'a.modal', array('handler' => 'iframe'));

$conf = new UuConfig();
$required	= false;

?>



<div id="uu-wrap" class="registration<?php echo $this->pageclass_sfx?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
    <h1 class="componentheading"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <?php
    //display registration intro text
    $registration_text_intro = $conf->get('registation_text_intro');
    if (!empty($registration_text_intro)) {
       echo $registration_text_intro;
    }
    ?>

    <form id="uuForm" name="uuForm" action="<?php echo JRoute::_('index.php?option=com_uu&task=registration.register'); ?>" method="post" class="uu-form-validate">
        <?php
            foreach ($this->registrationFields as $key => $field):?>
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

            if( !$required && $field->required == 1 ) {$required	= true;}

            //don't display username if allow mail as username
            if ($conf->get('email_as_username') && $field->fieldcode == 'username') {continue;}

            if ($field->type != 'group') {
            ?>

                <li>
                    <label id="lblfield<?php echo $field->id;?>" for="jform_<?php echo $field->fieldcode;?>" class="form-label"><?php if($field->required > 0) echo '*'; ?><?php echo JText::_($field->name); ?></label>
                    <div class="form-field">
                        <?php echo $field->html; ?>
                    </div>

                </li>
                <?php
            }

            ?>

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
            <?php
            //display terms
            if ($conf->get('enable_terms'))
            {
            ?>
                <li>
                    <div class="form-field" class="form-label" for="jform_accepted_terms">
                        <label class="label-checkbox">
                            <input type="checkbox" name="jform[accepted_terms]" id="jform_accepted_terms" value="1" default="0" class="input checkbox required validate-terms"/>
                            <?php echo JText::_('COM_UU_REGISTRATION_I_HAVE_READ').' <a  class="modal" href="index.php?option=com_content&view=article&tmpl=component&id='.(int)$conf->get('enable_terms_url').'">'.JText::_('COM_UU_REGISTRATION_TERMS_AND_CONDITION').'</a>.';?>
                        </label>
                    </div>

            <?php } ?>
        </li>
        <li>
            <?php
            //display registration intro text
            if ($conf->get('registration_text_concluding')) {
                echo $conf->get('registration_text_concluding');
            }
            ?>
        </li>
        <li>
            <div class="form-field">
                <div id="cwin-wait" style="display:none;"></div>
                <div id="cwin-btn">
                    <input class="cButton cButton-Blue validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('JREGISTER'); ?>" name="submit">
                    <?php echo JText::_('COM_UU_OR');?>
                    <a href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
                </div>
            </div>
        </li>
           </ul>
        <div>
            <input type="hidden" name="option" value="com_uu" />
            <input type="hidden" name="task" value="registration.register" />
            <?php echo JHtml::_('form.token');?>
        </div>
    </form>
</div>

<script type="text/javascript">
    cvalidate.init();
    cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_UU_ENTRY_MISSING")); ?>');

    joms.jQuery( '#uuForm' ).submit( function() {
        joms.jQuery('#cwin-btn').hide();
        joms.jQuery('#cwin-wait').show();
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

    joms.jQuery( document ).ready( function(){
        //we add tis to the uuForm and not the #jform_username and jform_email1 due to a bug in ie8 with append
        //see here : http://forum.jquery.com/topic/problem-with-append-elem-method-in-ie8
        //TODO add this field only if required
        joms.jQuery('#uuForm').append('<input type="hidden" name="usernamepass" id="usernamepass" value="N"/>');
        joms.jQuery('#uuForm').append('<input type="hidden" name="emailpass" id="emailpass" value="N"/>');

        var first_name = '';
        var last_name = '';

        joms.jQuery("#lblfield2").hide();
        joms.jQuery("#jform_name").prop('disabled', true).hide();


        joms.jQuery('#jform_cf_firstname').blur(function(e) {
            first_name = e.currentTarget.value;
            // joms.jQuery('#jform_name').val(first_name + ' ' + last_name);
            joms.jQuery('#jform_name').remove();
            joms.jQuery('#uuForm').append('<input type="hidden" name="jform[name]" id="jform_name" value="' + first_name + ' '  + last_name + '"/>');
        });
        joms.jQuery('#jform_cf_lastname').blur(function(e) {
            last_name = e.currentTarget.value;
            joms.jQuery('#jform_name').remove();
            joms.jQuery('#uuForm').append('<input type="hidden" name="jform[name]" id="jform_name" value="' + first_name + ' '  + last_name + '"/>');
            // joms.jQuery('#jform_name').val(first_name + ' ' + last_name);
        });

    });

</script>
