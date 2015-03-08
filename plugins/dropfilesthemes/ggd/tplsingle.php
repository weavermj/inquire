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
DropfilesFilesHelper::includeJSHelper();

?>
<script type="text/javascript">
    dropfilesGVExt = ["<?php echo implode('","', explode(",",dropfilesBase::loadValue($this->params,'allowedgoogleext','pdf,ppt,pptx,doc,docx,xls,xlsx,dxf,ps,eps,xps,psd,tif,tiff,bmp,svg,pages,ai,dxf,ttf'))); ?>"];
</script>
<script type="text/x-handlebars-template" id="dropfiles-template-ggd-box">
    {{#with file}}
        <a href="#" class="dropfiles-close"></a>
        <div class="dropblock">
            <a class="downloadlink" href='{{link}}'>           
                <div class="ext {{ext}}"><span class="txt">{{ext}}</div>
                <h2>{{title}}</h2>
                <div>{{description}}</div>
            </a>
            <div class="dropfiles-extra">
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showversion',1)==1): ?>
                    {{#if version}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?></span> {{version}}&nbsp;</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showsize',1)==1): ?>
                    {{#if size}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?></span> {{bytesToSize size}}</div>
                    {{/if}}
                    <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showhits',1)==1): ?>
                    {{#if hits}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?></span> {{hits}}</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showdateadd',1)==1): ?>
                    {{#if created_time}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?></span> {{created_time}}</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showdatemodified',1)==1): ?>
                    {{#if modified_time}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?></span> {{modified_time}}</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'gdd_showdownload',1)==1): ?>
                    <div class="extra-downloadlink">
                        <a href='{{link}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                    </div>
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'usegoogleviewer',1)>0 ): ?>
                    {{#isGGExt ext}}
                    <div class="extra-openlink">
                        <a class="openlink <?php echo (dropfilesBase::loadValue($this->params,'usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo (dropfilesBase::loadValue($this->params,'usegoogleviewer',1)==2)?'target="_blank"':'';?> href='{{viewerlink}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                    </div>
                    {{/isGGExt}}
                <?php endif; ?>
            </div>
        </div>
    {{/with}}
</script>


<div class="dropfiles-content dropfiles-content-ggd dropfiles-content-single dropfiles-files" data-file="<?php echo $this->file->id; ?>" data-category="<?php echo $this->category->id; ?>" data-current="<?php echo $this->category->id; ?>">
    <?php if(!empty($this->file)): ?>
            <a class="dropfile-file-link" href="#" data-id="<?php echo $this->file->id ; ?>">
                <div class="dropblock">
                    <div class="ext <?php echo $this->file->ext ; ?>"><span class="txt"><?php echo $this->file->ext ; ?></div>
                </div>
                <div class="droptitle">
                    <?php echo $this->file->title ; ?>
                </div>
            </a>
    <?php endif; ?>
</div>