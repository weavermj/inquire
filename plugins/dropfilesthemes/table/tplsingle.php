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


//-- No direct access
defined('_JEXEC') || die('=;)');
?>
<?php if(!empty($this->file)): ?>
        <a class="dropfile-file-link dropfiles-content-single dropfiles-content-table" href="<?php echo $this->file->link; ?>" data-id="<?php echo $this->file->id ; ?>" title="<?php echo $this->file->description ; ?>">
            <span class="droptitle">
                <b><?php echo $this->file->title ; ?></b>
            </span>
            <br/>
            <span class="dropinfos">
                <?php if(dropfilesBase::loadValue($this->params,'table_showsize',1)==1): ?>
                    <b><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SINGLE_SIZE'); ?> : </b><?php echo DropfilesFilesHelper::bytesToSize($this->file->size) ; ?>
                <?php endif; ?>
                    <b><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SINGLE_FORMAT'); ?> : </b><?php echo strtoupper($this->file->ext); ?>
            </span>
        </a>
<?php endif; ?>