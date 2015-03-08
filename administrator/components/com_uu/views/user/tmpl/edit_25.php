<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_uu/assets/css/uu.css');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
$required = false;
$newgroup = false;

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid(document.id('user-form'))) {
			Joomla.submitform(task, document.getElementById('user-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_uu&layout=edit&user_id='.(int) $this->item->user_id); ?>" method="post" name="adminForm" id="user-form" class="form-validate">
	<div class="width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_UU_LEGEND_JOOMLA_USER'); ?></legend>
			<ul class="adminformlist">
                <?php foreach($this->form->getFieldset('user_details') as $field) :?>
                <li><?php echo $field->label; ?>
                    <?php echo $field->input; ?></li>
                <?php endforeach; ?>
            </ul>
		</fieldset>

        <?php if ($this->grouplist) :?>
        <fieldset id="user-groups" class="adminform">
            <legend><?php echo JText::_('COM_USERS_ASSIGNED_GROUPS'); ?></legend>
            <?php echo $this->loadTemplate('groups');?>
        </fieldset>
        <?php endif; ?>

	</div>


    <div class="width-50 fltrt">
        <?php
        foreach ($fieldsets as $fieldset) :
            if ($fieldset->name == 'user_details') :
                continue;
            endif;
            ?>
            <fieldset class="panelform">
                <legend><?php echo JText::_('COM_UU_LEGEND_JOOMLA_USER_PARAMS'); ?></legend>

                <ul class="adminformlist">
                    <?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
                    <?php if ($field->hidden): ?>
                        <?php echo $field->input; ?>
                        <?php else: ?>
                        <li><?php echo $field->label; ?>
                            <?php echo $field->input; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </fieldset>
            <?php endforeach; ?>

    </div>


    <div class="width-50 fltrt">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_UU_LEGEND_UU_USER'); ?></legend>
            <ul class="adminformlist">
                <li>
                    <?php echo $this->form->getLabel('ip_address'); ?>
                    <?php echo $this->form->getInput('ip_address'); ?>
                </li>

                <?php
                $firstgroup = false;
                foreach ($this->customFields as $field):?>
                    <?php if ($field->type == 'group') {
                        if (!$firstgroup = true){echo '</ul>';}
                        $newgroup = true;
                        ?><div class="ctitle">
                            <h2><?php echo JText::_( $field->name ); ?></h2>
                        </div>

                        <?php } else {
                        if( !$required && $field->required == 1 )
                            $required	= true;

                        if ($newgroup){echo '<ul class="cFormList cFormHorizontal cResetList">';$newgroup=false;}
                        ?>
                        <li>
                            <label id="lblfield<?php echo $field->id;?>" for="field<?php echo $field->id;?>" class="form-label"><?php if($field->required == 1) echo '*'; ?><?php echo JText::_($field->name); ?></label>
                            <div class="form-field">
                                <?php echo $field->html; ?>
                            </div>

                        </li>
                        <?php
                    }?>

                    <?php endforeach;?>

            </ul>
        </fieldset>
    </div>




	<input type="hidden" name="task" value="" />
    <?php echo $this->form->getInput('user_id');?>
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>