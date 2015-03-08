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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.framework');
JHtml::_('behavior.colorpicker');
JHtml::_('behavior.framework');
jimport('joomla.application.component.helper');

$function	= JRequest::getCmd('function', 'jInsertCategory');

JText::script('COM_DROPFILES_JS_DROP_FILES_HERE');
JText::script('COM_DROPFILES_JS_DROP_FILES_HERE');
JText::script('COM_DROPFILES_JS_USE_UPLOAD_BUTTON');
JText::script('COM_DROPFILES_JS_USE_UPLOAD_BUTTON');
JText::script('COM_DROPFILES_JS_ARE_YOU_SURE');
JText::script('COM_DROPFILES_JS_DELETE');
JText::script('COM_DROPFILES_JS_EDIT');
JText::script('COM_DROPFILES_JS_BROWSER_NOT_SUPPORT_HTML5');
JText::script('COM_DROPFILES_JS_TOO_ANY_FILES');
JText::script('COM_DROPFILES_JS_FILE_TOO_LARGE');
JText::script('COM_DROPFILES_JS_ONLY_IMAGE_ALLOWED');
JText::script('COM_DROPFILES_JS_DBLCLICK_TO_EDIT_TITLE');
JText::script('COM_DROPFILES_JS_WANT_DELETE_CATEGORY');
JText::script('COM_DROPFILES_JS_SELECT_FILES');
JText::script('COM_DROPFILES_JS_IMAGE_PARAMETERS');
JText::script('COM_DROPFILES_JS_CANCEL');
JText::script('COM_DROPFILES_JS_OK');
JText::script('COM_DROPFILES_JS_CONFIRM');
JText::script('COM_DROPFILES_JS_SAVE');
JText::script('COM_DROPFILES_JS_X_FILES_IMPORTED');
JText::script('COM_DROPFILES_JS_WAIT_UPLOADING');

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('gcaninsert='.(JRequest::getBool('caninsert',false)?'true':'false').';');
$doc->addScriptDeclaration('e_name="'.JRequest::getString('e_name').'";');
$params = JComponentHelper::getParams('com_dropfiles');

$collapse = DropfilesBase::getParam('catcollapsed',0);

$declaration = 
            "if(typeof(Dropfiles)=='undefined'){"
          . "     Dropfiles={};"
          . "}"
          . "Dropfiles.can = {};"
          . "Dropfiles.can.config=".(int)$this->canDo->get('core.admin').";"
          . "Dropfiles.can.create=".(int)$this->canDo->get('core.create').";"
          . "Dropfiles.can.edit=".(int)$this->canDo->get('core.edit').";"
          . "Dropfiles.can.editown=".(int)$this->canDo->get('core.edit.own').";"
          . "Dropfiles.can.delete=".(int)$this->canDo->get('core.delete').";"
	  . "Dropfiles.author=".(int)JFactory::getUser()->id.";"
        
          . "Dropfiles.collapse=".($collapse?'true':'false').";"
          . "Dropfiles.version='".dropfilesComponentHelper::getVersion()."';"
          . "Dropfiles.maxfilesize = ".$params->get('maxinputfile',10).";";

$doc->addScriptDeclaration($declaration);
?>
<div id="mybootstrap"class="<?php if(dropfilesBase::isJoomla30()) echo 'joomla30'; ?> <?php if(dropfilesBase::isJoomla25()) echo 'joomla25'; ?>">
    <?php echo $this->loadTemplate('cats'); ?>
    
    <div id="rightcol" class="">
        <?php if(JRequest::getBool('caninsert')): ?>
            <a id="insertcategory" class="btn btn-success btn-block" href="" onclick="if (window.parent) {window.parent.jInsertEditorText(insertCategory(),'<?php echo JFactory::getApplication()->input->getVar('e_name');?>');window.parent.SqueezeBox.close();}"><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_INSERT_CATEGORY'); ?></a>
            <a id="insertfile" class="btn btn-success btn-block" style="display: none;" href="" onclick="if (window.parent) {window.parent.jInsertEditorText(insertFile(),'<?php echo JFactory::getApplication()->input->getVar('e_name');?>');window.parent.SqueezeBox.close();}"><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_INSERT_FILE'); ?></a>
        <?php endif; ?>

        <div>
            <div class="categoryblock">
                <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                <div class="themesblock">
                    <div class="well">
                        <h4><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_THEME'); ?></h4>
                        <select id="themeselect" name="themeselect">
                            <?php 
                            JPluginHelper::importPlugin('dropfilesthemes');
                            $dispatcher = JDispatcher::getInstance();
                            $themes = $dispatcher->trigger('getThemeName');                            
                            foreach ($themes as $theme): ?>                            
                                <option value="<?php echo strtolower($theme['name']);?>" <?php //if($theme==$this->current) echo 'selected="selected"'?>><?php echo $theme['name'];?></option>
                            <?php endforeach; ?>
                        </select>                        
                    </div>

                    <div class="well">
                        <h4><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_PARAMETERS'); ?></h4>
                        <div id="galleryparams">

                        </div>
                    </div>
                </div>
                <?php endif; ?>
                    
                <?php if($this->importFiles && $this->canDo->get('core.admin')): ?>
                <div class="well">
                    <h4><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_IMPORT'); ?></h4>
                    <div id="filesimport">
                        <div id="jao"></div>
                        <div class="center">
                            <button class="btn btn-mini" id="selectAllImportFiles" type="button">Select all</button>
                            <button class="btn btn-large" id="importFilesBtn" type="button"><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_IMPORT_BTN'); ?></button>
                            <button class="btn btn-mini" id="unselectAllImportFiles" type="button">Unselect all</button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
            <div class="fileblock" style="display: none;">


                <div class="well">
                    <h4><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_PARAMETERS'); ?></h4>
                    <div id="fileparams">

                    </div>
                </div>
                <div id="fileversion">
                    <div class="well">
                        <h4><?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_VERSION'); ?></h4>
                        <div id="versions_content"></div>
                            <div id="dropbox_version">
                                <div class="upload">
                                    <span class="message"><?php echo JText::_('COM_DROPFILES_JS_DROP_FILES_HERE'); ?></span>
                                    <input class="hide" type="file" id="upload_input_version">
                                    <a href="" id="upload_button_version" class="btn btn-large btn-primary">
                                        <?php echo JText::_('COM_DROPFILES_JS_SELECT_FILES'); ?>
                                    </a>
                                </div>
                                <div class="progress progress-striped active hide">
                                    <div class="bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        <div class="clr"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="pwrapper">
        <div id="wpreview">
            <div id="preview"></div>        
        </div>
        <input type="hidden" name="id_category" value="" />
    </div>
</div>
