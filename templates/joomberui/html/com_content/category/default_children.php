<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$class = ' class="first"';

JHtml::_('bootstrap.tooltip');
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
		if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>

		<div<?php echo $class; ?>>
			<?php $class = ''; ?>


			<?php
			$image = json_decode($child->params)->{'image'};

			if (!$image) : ?>
				<h3 class="page-header item-title">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>">
						<?php echo $this->escape($child->title); ?>
					</a>
					<?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
					<span class="badge badge-info tip hasTooltip" title="<?php echo JText::_('COM_CONTENT_NUM_ITEMS'); ?>">
						<?php echo $child->getNumItems(true); ?>
					</span>
					<?php endif; ?>
				</h3>
				<?php if ($this->params->get('show_subcat_desc') == 1) :?>
					<?php if ($child->description) : ?>
						<div class="category-desc">
							<?php echo JHtml::_('content.prepare', $child->description, '', 'com_content.category'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			<?php else: ?>
				<a class="image-wrapper" href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>">
					<img src="<?php echo htmlspecialchars($image); ?>">
					<span class="image-text"><?php echo $this->escape($child->title); ?></span>
				</a>
			<?php endif; ?>

			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
