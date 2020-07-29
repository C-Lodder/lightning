<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params = $displayData->params;
$images = json_decode($displayData->images);

?>
<?php if (!empty($images->image_intro)) : ?>
	<?php
	$templateParams = \Joomla\CMS\Factory::getApplication()->getTemplate(true)->params;
	$autoResize     = $templateParams->get('auto-resize', 1);

	$imageLayout    = new FileLayout('lightning.picture_element');
	$pictureElement = $imageLayout->render([
		'file'          => $images->image_intro,
		'caption'       => $images->image_intro_caption,
		'alt'           => $images->image_intro_alt,
		'resize'        => $autoResize,
		'webp'          => function_exists('imagewebp'),
		'webp_priority' => true,
		'lazy'          => true,
		'attributes'    => [
			'itemprop' => 'thumbnailUrl',
		],
	]);
	?>
	<?php $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro; ?>
	<figure class="float-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?> item-image">
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
			<a href="<?php echo Route::_(RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>">
				<?= $pictureElement ?>
			</a>
		<?php else : ?>
			<?= $pictureElement ?>
		<?php endif; ?>
		<?php if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') : ?>
			<figcaption
					class="caption"><?php echo htmlspecialchars($images->image_intro_caption, ENT_COMPAT, 'UTF-8'); ?></figcaption>
		<?php endif; ?>
	</figure>
<?php endif; ?>
