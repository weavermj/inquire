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


<script type="text/x-handlebars-template" id="dropfiles-template-table">
    <?php if(dropfilesBase::loadValue($this->params,'table_showsubcategories',1)==1): ?>
    {{#with category}}
        {{#if parent_id}}
            <tr class="nohide">
                <td colspan="100" class="essential">
                <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_BACK_TO'); ?> : 
                <a class="dropfilescategory catlink" href="#" data-idcat="{{parent_id}}">
                    {{parent_title}}
                </a>
                </td>
            </tr>
        {{/if}}
    {{/with}}
    
    <?php if(dropfilesBase::loadValue($this->params,'table_showcategorytitle',1)==1 && dropfilesBase::loadValue($this->params,'table_showcategoriesposition',0)==0): ?>
    {{#with category}}
        <tr><td colspan="100"><h2>{{title}}</h2></td></tr>
    {{/with}}
    <?php endif; ?>
    
    {{#if categories}}
        {{#each categories}}
                <tr class="nohide">
                    <td colspan="100" class="essential">
                    <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_CATEGORY'); ?> : 
                    <a class="dropfilescategory catlink" href="#" data-idcat="{{id}}">
                        {{title}}
                    </a>
                    </td>
                </tr>
        {{/each}}
    {{/if}}
    <?php endif; ?>
    
    <?php if(dropfilesBase::loadValue($this->params,'table_showcategorytitle',1)==1 && dropfilesBase::loadValue($this->params,'table_showcategoriesposition',0)==1): ?>
    {{#with category}}
        <tr><td colspan="100" class="essential"><h2>{{title}}</h2></td></tr>
    {{/with}}
    <?php endif; ?>

    {{#if files}}
        {{#each files}}                
            <tr>
                <td class="extcol"><span class="ext {{ext}}"></span></td>

                <?php if(dropfilesBase::loadValue($this->params,'table_showtitle',1)==1): ?>
                <td>
                    <a href='{{link}}'>{{title}}</a>
                </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showversion',1)==1): ?>
                    <td>
                        {{version}}
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdownload',1)==1): ?>
                    <td>
                        <a class="downloadlink" href='{{link}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                        <?php if($this->componentParams->get('usegoogleviewer',1)>0 ): ?>
                            {{#if viewerlink}}
                            <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href='{{viewerlink}}'><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                            {{/if}}
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdescription',1)==1): ?>
                    <td>
                        {{description}}
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showsize',1)==1): ?>
                    <td>
                        {{bytesToSize size}}
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showhits',1)==1): ?>
                    <td>
                        {{hits}}
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdateadd',0)==1): ?>
                    <td>
                        {{created_time}}
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdatemodified',0)==1): ?>
                    <td>
                        {{modified_time}}
                    </td>
                <?php endif; ?>
            </tr>
        {{/each}}
    {{/if}}
        
    
</script>

<?php if(!empty($this->files) || !empty($this->categories)): ?>
<div class="dropfiles-content dropfiles-content-table dropfiles-content-multi dropfiles-files <?php echo $this->dropfilesclass; ?>"  data-category="<?php echo $this->category->id; ?>" data-current="<?php echo $this->category->id; ?>">

<table class="<?php echo $this->tableclass; ?> mediaTable">
        <thead>
            <tr>
                <th>#</th>
                <?php if(dropfilesBase::loadValue($this->params,'table_showtitle',1)==1): ?>
                <th class="essential persist">
                    <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_TITLE'); ?>
                </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showversion',1)==1): ?>
                    <th class="optional">
                       <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdownload',1)==1): ?>
                    <th class="essential">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdescription',1)==1): ?>
                    <th class="optional">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DESCRIPTION'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showsize',1)==1): ?>
                    <th class="optional">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showhits',1)==1): ?>
                    <th class="optional">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdateadd',0)==1): ?>
                    <th class="optional">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?>
                    </th>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdatemodified',0)==1): ?>
                    <th class="optional">
                        <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?>
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
  
        <tbody> 
            <?php if(count($this->files)): ?>
                <?php if(dropfilesBase::loadValue($this->params,'table_showcategorytitle',1)==1 && dropfilesBase::loadValue($this->params,'table_showcategoriesposition',0)==0): ?>
                    <tr><td colspan="100"><h2><?php echo $this->category->title; ?></h2></td></tr>
                <?php endif; ?>
                    
            <?php endif; ?>    
                    
            <?php if(count($this->categories) && dropfilesBase::loadValue($this->params,'table_showsubcategories',1)==1): ?>
                <?php foreach ($this->categories as $category): ?>
                <tr class="nohide">
                    <td colspan="100" class="essential">
                    <?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_CATEGORY'); ?> : 
                    <a class="dropfilescategory catlink" href="#" data-idcat="<?php echo $category->id; ?>">
                        <?php echo $category->title; ?>
                    </a>
                    </td>
                </tr>
                <?php  endforeach; ?>
            <?php endif; ?>
            
            <?php if(count($this->files)): ?>
                <?php if(dropfilesBase::loadValue($this->params,'table_showcategorytitle',1)==1 && dropfilesBase::loadValue($this->params,'table_showcategoriesposition',0)==1): ?>
                    <tr><td colspan="100"><h2><?php echo $this->category->title; ?></h2></td></tr>
                <?php endif; ?>
                    
            <?php endif; ?>    
            
            <?php foreach ($this->files as $file): ?>
            <tr>
                <td class="extcol"><a href="<?php echo $file->link ; ?>"><span class="ext <?php echo $file->ext ; ?>"></span></a></td>
                
                <?php if(dropfilesBase::loadValue($this->params,'table_showtitle',1)==1): ?>
                <td>
                    <a href="<?php echo $file->link ; ?>"><?php echo $file->title ; ?></a>
                </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showversion',1)==1): ?>
                    <td>
                        <?php echo $file->version; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdownload',1)==1): ?>
                    <td>
                        <a class="downloadlink" href="<?php echo $file->link ; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a>
                        <?php if(isset($file->viewerlink)): ?>
                        <br/>
                        <a class="openlink <?php echo ($this->componentParams->get('usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo ($this->componentParams->get('usegoogleviewer',1)==2)?'target="_blank"':'';?> href="<?php echo $file->viewerlink; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdescription',1)==1): ?>
                    <td>
                        <?php echo $file->description ; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showsize',1)==1): ?>
                    <td>
                        <?php echo DropfilesFilesHelper::bytesToSize($file->size); ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showhits',1)==1): ?>
                    <td>
                        <?php echo $file->hits; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdateadd',0)==1): ?>
                    <td>
                        <?php echo $file->created_time; ?>
                    </td>
                <?php endif; ?>

                <?php if(dropfilesBase::loadValue($this->params,'table_showdatemodified',0)==1): ?>
                    <td>
                        <?php echo $file->modified_time; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    
    
</table>
</div>
<?php endif; ?>