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
<div class="dropfiles-content dropfiles-file dropfiles-content-default">
                <div class="file">
                    <div class="ext <?php echo $this->file->ext ; ?>"><span class="txt"><?php echo $this->file->ext ; ?></div>
                    <div class="filecontent">
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showdownload',1)==1): ?>
                        <div class="downloadblock">
                        <a class="downloadlink" href="<?php echo $this->file->link ; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DOWNLOAD'); ?></a><br/>
                        <?php if(dropfilesBase::loadValue($this->params,'usegoogleviewer',1)>0 && in_array($this->file->ext,explode(',', dropfilesBase::loadValue($this->params,'allowedgoogleext','pdf,ppt,pptx,doc,docx,xls,xlsx,dxf,ps,eps,xps,psd,tif,tiff,bmp,svg,pages,ai,dxf,ttf,txt')))): ?>
                        <a class="openlink <?php echo (dropfilesBase::loadValue($this->params,'inquire_usegoogleviewer',1)==1)?'dropfileslightbox':'';?>" <?php echo (dropfilesBase::loadValue($this->params,'usegoogleviewer',1)==2)?'target="_blank"':'';?> href="<?php echo $this->file->viewerlink; ?>"><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_OPEN'); ?></a>
                        <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showtitle',1)==1): ?>
                        <h3><a href="<?php echo $this->file->link ; ?>"><?php echo $this->file->title ; ?></a></h3>
                        <?php endif; ?>
                        <div><?php echo $this->file->description ; ?></div>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showversion',1)==1 && trim($this->file->version!='')): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_VERSION'); ?> : </span> <?php echo $this->file->version; ?>&nbsp;</div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showsize',1)==1): ?>
                        <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SIZE'); ?> : </span> <?php echo DropfilesFilesHelper::bytesToSize($this->file->size); ?></div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showhits',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_HITS'); ?> : </span> <?php echo $this->file->hits; ?></div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showdateadd',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_SHOWDATEADD'); ?> : </span> <?php echo $this->file->created_time; ?></div>
                        <?php endif; ?>
                        <?php if(dropfilesBase::loadValue($this->params,'inquire_showdatemodified',1)==1): ?>
                            <div><span><?php echo JText::_('COM_DROPFILES_DEFAULT_FRONT_DATEMODIFIED'); ?> : </span> <?php echo $this->file->modified_time; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
</div>
<?php endif; ?>
