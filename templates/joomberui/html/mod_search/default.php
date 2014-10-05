<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="search<?php echo $moduleclass_sfx ?>">
	<form action="<?php echo JRoute::_('index.php');?>" method="post" class="form-inline">
		<div class="row collapse">
			<div class="small-11 columns">
				<input name="searchword" id="mod-search-searchword" maxlength="<?php echo $maxlength; ?>" class="inputbox search-query" type="text" size="<?php echo $width; ?>" value="<?php echo $text; ?>"  onblur="if (this.value=='') this.value='<?= $text; ?>'" onfocus="if (this.value=='<?= $text; ?>') this.value=''" />
			</div>
			<div class="small-1 columns">
				<button class="button tiny postfix radius btn btn-primary" onclick="this.form.searchword.focus();">Go</button>
			</div>
		</div>

		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</form>
</div>
