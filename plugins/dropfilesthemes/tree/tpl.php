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

<script type="text/x-handlebars-template" id="dropfiles-template-tree-box">
    {{#with file}}
        <div class="dropblock">
            <a href="javascript:void(null)" class="dropfiles-close"></a>
            <a class="downloadlink" href='{{link}}'>
                <h2>{{title}}</h2>
            </a>
            <div class="ext {{ext}}"><span class="txt">{{ext}}</div>
            <div class="dropfiles-extra">
                <?php if(dropfilesBase::loadValue($this->params,'tree_description',1)==1): ?>
                    {{#if description}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DESCRIPTION'); ?> : </span> {{description}}&nbsp;</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showversion',1)==1): ?>
                    {{#if version}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?> : </span> {{version}}&nbsp;</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showsize',1)==1): ?>
                    {{#if size}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?> : </span> {{bytesToSize size}}</div>
                    {{/if}}
                    <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showhits',1)==1): ?>
                    {{#if hits}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?> : </span> {{hits}}</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showdateadd',1)==1): ?>
                    {{#if created_time}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?> : </span> {{created_time}}</div>
                    {{/if}}
                <?php endif; ?>
                <?php if(dropfilesBase::loadValue($this->params,'tree_showdatemodified',1)==1): ?>
                    {{#if modified_time}}
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?> : </span> {{modified_time}}</div>
                    {{/if}}
                <?php endif; ?>
            </div>
            <div class="extra-content">
                <div class="extra-downloadlink">
                    <a href='{{link}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                </div>
                <?php if($this->componentParams->get('usegoogleviewer',1)>0 ): ?>
                    {{#if viewerlink}}
                    <div class="extra-openlink">
                        <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href='{{viewerlink}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                    </div>
                    {{/if}}
                <?php endif; ?>
            </div>
        </div>
    {{/with}}
</script>


<?php if(dropfilesBase::loadValue($this->params,'tree_showsubcategories',1)==1): ?>
<script type="text/x-handlebars-template" id="dropfiles-template-tree-categories">
{{#if categories}}
    {{#each categories}}
        <li class="catlink">
            <a class="dropfilescategory catlink" href="#" data-idcat="{{id}}">
                    {{title}}
            </a>
        </li>
    {{/each}}
{{/if}}
</script>
<?php endif; ?>

<script type="text/x-handlebars-template" id="dropfiles-template-tree-files">
{{#if files}}
    {{#each files}}
        <li class="ext {{ext}}">
            <a class="dropfile-file-link" href="#" data-id="{{id}}">
                    {{title}}
            </a>
        </li>
        {{/each}}
</div>
{{/if}}
</script>
<?php if($this->category!==null):?>
    <?php if(!empty($this->files) || !empty($this->categories)): ?>
    <div class="dropfiles-content dropfiles-content-multi dropfiles-files dropfiles-content-tree" data-category="<?php echo $this->category->id; ?>" data-current="<?php echo $this->category->id; ?>">
        <?php if(dropfilesBase::loadValue($this->params,'tree_showcategorytitle',1)==1): ?>
        <h2><?php echo $this->category->title; ?></h2>
        <?php endif; ?>

        <ul>
            <?php if(count($this->categories) && dropfilesBase::loadValue($this->params,'tree_showsubcategories',1)==1): ?>
                <?php foreach ($this->categories as $category): ?>
                <li class="catlink">
                    <a class="dropfilescategory catlink" href="#" data-idcat="<?php echo $category->id; ?>">
                            <?php echo $category->title; ?>
                    </a>
                </li>
                <?php  endforeach; ?>
            <?php endif; ?>
            <?php if(count($this->files)): ?>
                <?php foreach ($this->files as $file): ?>
                <li class="ext <?php echo $file->ext ; ?>">
                    <a class="dropfile-file-link" href="#" data-id="<?php echo $file->id ; ?>">
                            <?php echo $file->title ; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
<?php endif; ?>