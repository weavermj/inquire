<?php
/** 
 * Dropfiles
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;

if($this->form){ 
$fieldSet = $this->form->getFieldset();
if(!empty($fieldSet)){
?>
<form class="dropfilesparams">
    <fieldset>
    <?php
    echo $this->form->getInput('id');
    
    foreach ($fieldSet as $name => $field) : ?>
            <?php echo $field->label; ?>
            <span class="paraminput input-block-level"><?php echo $field->input; ?></span>
            <!--<span class="help-block"><?php echo $field->description; ?></span>-->
    <?php endforeach; ?>
    <span class="paraminput"><?php echo JHtml::_('form.token'); ?></span>
    <button class="btn" type="submit"><?php echo JText::_('COM_DROPFILES_JS_SAVE'); ?></button>
    </fiedset>
</form>
<?php } 
}
?>