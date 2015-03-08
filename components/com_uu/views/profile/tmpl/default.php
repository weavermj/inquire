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

?>
<div id="uu-wrap" class="profile<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

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
        //don't display email2 password1 password2
        if ($field->fieldcode == 'email2' || $field->fieldcode == 'password1' || $field->fieldcode == 'password2') {continue;}
        //don't display username if allow mail as username
        if ($conf->get('email_as_username') && $field->fieldcode == 'username') {continue;}

        if ($field->type != 'group') {
            ?>
            <li>
                <label id="lblfield<?php echo $field->id;?>" for="jform[<?php echo $field->fieldcode;?>]" class="form-label"><?php echo JText::_($field->name); ?></label>
                <div class="form-field">
                    <?php echo $field->html; ?>
                </div>

            </li>
        <?php } ?>

        <?php endforeach;?>

        <li></li>
    </ul>
    <ul class="cFormList cFormHorizontal cResetList">
       <li >
           <div class="form-field">
            <?php if (JFactory::getUser()->id == $this->data->id) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_uu&task=profile.edit&user_id='.(int) $this->data->id);?>">
                <?php echo JText::_('COM_UU_EDIT_PROFILE'); ?></a>
            <?php endif; ?>
           </div>
       </li>
    </ul>

</div>

