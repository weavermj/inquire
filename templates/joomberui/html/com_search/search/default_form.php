<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>

<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if (!empty($this->searchword)):?>
	<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">' . $this->total . '</span>');?></p>
	<?php endif;?>
</div>

<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post">

	<div class="btn-toolbar">
		<div class="row collapse">
			<div class="small-11 medium-5 columns">
				<input class="inputbox search-query" type="text" name="searchword" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
			</div>
			<div class="small-1 column end">
				<button class="button tiny postfix radius" name="Search" onclick="this.form.submit()" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('COM_SEARCH_SEARCH');?>"><span class="icon-search">Go</span></button>
			</div>
		</div>

		<input type="hidden" name="task" value="search" />
	</div>



	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
			<legend><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></legend>
			<?php foreach ($this->searchareas['search'] as $val => $txt) :
				$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
			?>
			<label for="area-<?php echo $val;?>" class="checkbox">
				<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> >
				<?php echo JText::_($txt); ?>
			</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

<?php if ($this->total > 0) : ?>

	<?php if ($this->total > 1) : ?>
		<div class="row">
			<div class="small-5 medium-2 large-2 columns">
				<label for="ordering" class="ordering">
					<?php echo JText::_('COM_SEARCH_ORDERING');?>
				</label>
			</div>
			<div class="small-7 medium-3 large-2 columns end">
				<?php echo $this->lists['ordering'];?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->total > 5) : ?>
		<div class="row">
			<div class="small-5 medium-2 large-2 columns">
				<label for="limit">
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
			</div>
			<div class="small-7 medium-3 large-2 columns end">
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
	<?php endif; ?>

<?php endif; ?>

</form>
