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

$params = $displayData->params;
$images = json_decode($displayData->images);

?>
<?php if (!empty($images->image_fulltext)) : ?>
	<?php
	$templateParams = \Joomla\CMS\Factory::getApplication()->getTemplate(true)->params;
	$autoResize     = $templateParams->get('auto-resize', 1);

	$imageLayout    = new FileLayout('lightning.picture_element');
	$pictureElement = $imageLayout->render([
		'file'          => $images->image_fulltext,
		'caption'       => $images->image_fulltext_caption,
		'alt'           => $images->image_fulltext_alt,
		'resize'        => $autoResize,
		'webp'          => function_exists('imagewebp'),
		'webp_priority' => true,
		'lazy'          => true,
		'attributes'    => [
			'itemprop' => 'image',
		],
	]);
	?>
	<?php $imgfloat = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
	<figure class="float-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?> item-image">
		<?= $pictureElement ?>

		<?php if (isset($images->image_fulltext_caption) && $images->image_fulltext_caption !== '') : ?>
			<figcaption
					class="caption"><?php echo htmlspecialchars($images->image_fulltext_caption, ENT_COMPAT, 'UTF-8'); ?></figcaption>
		<?php endif; ?>
	</figure>
<?php endif; ?>
