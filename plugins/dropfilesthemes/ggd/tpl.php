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
                    <?php if(dropfilesBase::loadValue($this->params,'usegoogleviewer',1)>0 ): ?>
                    {{#isGGExt ext}}
                    <div class="extra-openlink">
                        <a class="openlink <?php echo (dropfilesBase::loadValue($this->params,'usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo (dropfilesBase::loadValue($this->params,'usegoogleviewer',1)==2)?'target="_blank"':'';?> href='{{viewerlink}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                    </div>
                    {{/isGGExt}}
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    {{/with}}
</script>


<?php if(dropfilesBase::loadValue($this->params,'gdd_showsubcategories',1)==1): ?>
<script type="text/x-handlebars-template" id="dropfiles-template-ggd-categories">
<?php if(dropfilesBase::loadValue($this->params,'gdd_showcategorytitle',1)==1): ?>
{{#with category}}
    <h2>{{title}}</h2>
{{/with}}
<?php endif; ?>
{{#with category}}
    {{#if parent_id}}
        <a class="catlink  dropfilescategory backcategory" href="#" data-idcat="{{parent_id}}">
                <div class="dropblock">
                    <div class="ext back"></div>
                </div>
                <div class="droptitle">
                   <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_BACK_TO'); ?> {{parent_title}}
                </div>
        </a>
    {{/if}}
{{/with}}

{{#if categories}}
    {{#each categories}}
        <a class="dropfilescategory catlink" href="#" data-idcat="{{id}}">
            <div class="dropblock">
                <div class="ext"></div>
            </div>
            <div class="droptitle">
               {{title}}
            </div>
        </a>
    {{/each}}
{{/if}}
</script>
<?php endif; ?>

<script type="text/x-handlebars-template" id="dropfiles-template-ggd-files">
{{#if files}}
    {{#each files}}
                <a class="dropfile-file-link" href="#" data-id="{{id}}">
                    <div class="dropblock">
                        <div class="ext {{ext}}"><span class="txt">{{ext}}</div>
                    </div>
                    <div class="droptitle">
                        {{title}}
                    </div>
                </a>
        {{/each}}
</div>
{{/if}}
</script>

<?php if(!empty($this->files) || !empty($this->categories)): ?>
<div class="dropfiles-content dropfiles-content-ggd dropfiles-content-multi dropfiles-files" data-category="<?php echo $this->category->id; ?>" data-current="<?php echo $this->category->id; ?>">
    <?php if(dropfilesBase::loadValue($this->params,'gdd_showcategorytitle',1)==1): ?>
    <h2><?php echo $this->category->title; ?></h2>
    <?php endif; ?>
    
    <?php if(count($this->categories) && dropfilesBase::loadValue($this->params,'gdd_showsubcategories',1)==1): ?>
        <?php foreach ($this->categories as $category): ?>
        <a class="dropfilescategory catlink" href="#" data-idcat="<?php echo $category->id; ?>">
            <div class="dropblock">
                <div class="ext"></div>
            </div>
            <div class="droptitle">
                <?php echo $category->title; ?>
            </div>
        </a>
        <?php  endforeach; ?>
    <?php endif; ?>
    <?php if(count($this->files)): ?>
        <?php foreach ($this->files as $file): ?>
            <a class="dropfile-file-link" href="#" data-id="<?php echo $file->id ; ?>">
                <div class="dropblock">
                    <div class="ext <?php echo $file->ext ; ?>"><span class="txt"><?php echo $file->ext ; ?></div>
                </div>
                <div class="droptitle">
                    <?php echo $file->title ; ?>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>