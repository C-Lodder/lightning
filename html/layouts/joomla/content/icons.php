<?php
/*
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020-2021 Nicholas K. Dionysopoulos. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This template is a derivative work of the Lightning template which is
 * Copyright (C) 2020 JoomJunk.
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$canEdit   = $displayData['params']->get('access-edit');
$articleId = $displayData['item']->id;
?>

<?php if ($canEdit) : ?>
	<div class="dropdown float-right">
		<button
			class="dropdown-toggle"
			type="button" id="dropdownMenuButton-<?php echo $articleId; ?>"
			aria-label="<?php echo Text::_('JUSER_TOOLS'); ?>"
			aria-haspopup="true">
			<span class="fas fa-cog" aria-hidden="true"></span>
		</button>
		<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-<?php echo $articleId; ?>">
			<?php echo HTMLHelper::_('icon.edit', $displayData['item'], $displayData['params']); ?>
		</div>
	</div>
<?php endif; ?>
