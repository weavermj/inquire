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
<?php if(dropfilesBase::loadValue($this->params,'showsubcategories',1)==1): ?>
<script type="text/x-handlebars-template" id="dropfiles-template-default-categories">
<?php if(dropfilesBase::loadValue($this->params,'showcategorytitle',1)==1): ?>
{{#with category}}
    <h2>{{title}}</h2>
{{/with}}
<?php endif; ?>
{{#if categories}}
    {{#each categories}}
        <a class="catlink dropfilescategory" href="#" data-idcat="{{id}}">{{title}}</a>
    {{/each}}
{{/if}}
{{#with category}}
    {{#if parent_id}}
    <a class="catlink dropfilescategory backcategory" href="#" data-idcat="{{parent_id}}"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_BACK_TO'); ?> {{parent_title}}</a>
    {{/if}}
{{/with}}
</script>
<?php endif; ?>


<script type="text/x-handlebars-template" id="dropfiles-template-default-files">
{{#if files}}
    {{#each files}}
                <div class="file">
                    <div class="ext {{ext}}"><span class="txt">{{ext}}</div>
                    <div class="filecontent">
                        <div class="downloadblock">
                            <?php if(dropfilesBase::loadValue($this->params,'showdownload',1)==1): ?>
                            <a class="downloadlink" href='{{link}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                            <?php if($this->componentParams->get('usegoogleviewer',1)>0 ): ?>
                            {{#if viewerlink}}
                            <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href='{{viewerlink}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                            {{/if}}
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php if(dropfilesBase::loadValue($this->params,'showtitle',1)==1): ?>
                        <h3><a href='{{link}}'>{{title}}</a></h3>
                        <?php endif; ?>
                        {{#if description}}
                        <div>{{description}}</div>
                        {{/if}}
                        <?php if(dropfilesBase::loadValue($this->params,'showversion',1)==1): ?>
                        {{#if version}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?> : </span> {{version}}&nbsp;</div>
                        {{/if}}
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'showsize',1)==1): ?>
                        {{#if size}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?> : </span> {{bytesToSize size}}</div>
                        {{/if}}
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'showhits',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?> : </span> {{hits}}</div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'showdateadd',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?> : </span> {{created_time}}</div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'showdatemodified',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?> : </span> {{modified_time}}</div>
                        <?php endif; ?>
                    </div>
                </div>
        {{/each}}
</div>
{{/if}}
</script>

<?php if(!empty($this->files) || !empty($this->categories)): ?>
<div class="dropfiles-content dropfiles-content-multi dropfiles-files dropfiles-content-default" data-category="<?php echo $this->category->id; ?>">
    <?php if(dropfilesBase::loadValue($this->params,'showcategorytitle',1)==1): ?>
    <h2><?php echo $this->category->title; ?></h2>
    <?php endif; ?>
    
    <?php if(count($this->categories) && dropfilesBase::loadValue($this->params,'showsubcategories',1)==1): ?>
        <?php foreach ($this->categories as $category): ?>
            <a class="dropfilescategory catlink" href="#" data-idcat="<?php echo $category->id; ?>"><?php echo $category->title; ?></a>
        <?php  endforeach; ?>
    <?php endif; ?>
    <?php if(count($this->files)): ?>
        <?php foreach ($this->files as $file): ?>
            <div class="file">
                <div class="ext <?php echo $file->ext; ?>"><span class="txt"><?php echo $file->ext; ?></div>
                <div class="filecontent">
                    <?php if(dropfilesBase::loadValue($this->params,'showdownload',1)==1): ?>
                    <div class="downloadblock">
                        <a class="downloadlink" href="<?php echo $file->link; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a><br/>
                        <?php if(isset($file->viewerlink)): ?>
                        <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href="<?php echo $file->viewerlink; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if(dropfilesBase::loadValue($this->params,'showtitle',1)==1): ?>
                    <h3><a href="<?php echo $file->link ; ?>"><?php echo $file->title; ?></a></h3>
                    <?php endif; ?>
                    <div><?php echo $file->description; ?></div>
                        <?php if(dropfilesBase::loadValue($this->params,'showversion',1)==1 && trim($file->version)): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?> : </span> <?php echo $file->version; ?>&nbsp;</div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'showsize',1)==1): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?> : </span> <?php echo DropfilesFilesHelper::bytesToSize($file->size); ?></div>
                        <?php endif; ?>
                    <?php if(dropfilesBase::loadValue($this->params,'showhits',1)==1): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?> : </span> <?php echo $file->hits; ?></div>
                    <?php endif; ?>
                    <?php if(dropfilesBase::loadValue($this->params,'showdateadd',1)==1): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?> : </span> <?php echo $file->created_time; ?></div>
                    <?php endif; ?>
                    <?php if(dropfilesBase::loadValue($this->params,'showdatemodified',1)==1): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?> : </span> <?php echo $file->modified_time; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>