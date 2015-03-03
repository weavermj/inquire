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
<div class="dropfiles-content dropfiles-content-single dropfiles-files dropfiles-content-tree" data-file="<?php echo $this->file->id; ?>" data-category="<?php echo $this->category->id; ?>" data-current="<?php echo $this->category->id; ?>">
    <div class="dropblock">
        <?php if(!empty($this->file)): ?>
            <a class="downloadlink" href="<?php echo $this->file->link ; ?>">
                <h2><?php echo $this->file->title ; ?></h2>
            </a>
            <div class="ext <?php echo $this->file->ext ; ?>"><span class="txt"><?php echo $this->file->ext ; ?></div>
            <div class="dropfiles-extra">
                <?php if(dropfilesBase::loadValue($this->params,'tree_description',1)==1): ?>
                    <?php if ($this->file->description) : ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DESCRIPTION'); ?> : </span> <?php echo $this->file->description ; ?>&nbsp;</div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showversion',1)==1): ?>
                    <?php if ($this->file->version) : ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?> : </span> <?php echo $this->file->version ; ?>&nbsp;</div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showsize',1)==1): ?>
                        <?php if ($this->file->size) : ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?> : </span> <?php echo DropfilesFilesHelper::bytesToSize($this->file->size) ; ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showhits',1)==1): ?>
                    <?php if ($this->file->hits) : ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?> : </span> <?php echo $this->file->hits ; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showdateadd',1)==1): ?>
                    <?php if ($this->file->created_time) : ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?> : </span> <?php echo $this->file->created_time ; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showdatemodified',1)==1): ?>
                    <?php if ($this->file->modified_time) : ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?> : </span> <?php echo $this->file->modified_time ; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="extra-content">
                <div class="extra-downloadlink">
                    <a href="<?php echo $this->file->link ; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                </div>
                <?php if($this->componentParams->get('usegoogleviewer',1)>0 && isset($this->file->viewerlink)): ?>
                    <div class="extra-openlink">
                        <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href='<?php echo $this->file->viewerlink; ?>'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
</div>
