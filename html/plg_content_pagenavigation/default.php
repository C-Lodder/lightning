<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('stylesheet', Uri::root() . 'templates/lightning/css/pagination.css', ['version' => 'auto']);

$lang = Factory::getLanguage(); ?>

<nav>
	<ul class="pagination">
	<?php if ($row->prev) :
		$direction = $lang->isRtl() ? 'right' : 'left'; ?>
		<li class="page-item">
			<a class="page-link" href="<?php echo Route::_($row->prev); ?>" rel="prev">
			<span class="sr-only">
				<?php echo Text::sprintf('JPREVIOUS_TITLE', htmlspecialchars($rows[$location-1]->title)); ?>
			</span>
			<?php echo '<span class="fas fa-chevron-' . $direction . '" aria-hidden="true"></span> <span aria-hidden="true">' . $row->prev_label . '</span>'; ?>
			</a>
		</li>
	<?php endif; ?>
	<?php if ($row->next) :
		$direction = $lang->isRtl() ? 'left' : 'right'; ?>
		<li class="page-item">
			<a class="page-link" href="<?php echo Route::_($row->next); ?>" rel="next">
			<span class="sr-only">
				<?php echo Text::sprintf('JNEXT_TITLE', htmlspecialchars($rows[$location+1]->title)); ?>
			</span>
			<?php echo '<span aria-hidden="true">' . $row->next_label . '</span> <span class="fas fa-chevron-' . $direction . '" aria-hidden="true"></span>'; ?>
			</a>
		</li>
	<?php endif; ?>
	</ul>
</nav>
