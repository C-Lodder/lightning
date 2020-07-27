<?php
defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Template\Lightning\ImageResizer;
use Joomla\Utilities\ArrayHelper;

$app = Factory::getApplication();

if (!($app instanceof SiteApplication))
{
	return;
}

$supportsWebP = function_exists('imagewebp');

$displayData = array_merge([
	'file'          => null,
	'caption'       => null,
	'alt'           => null,
	'resize'        => true,
	'webp'          => true,
	'webp_priority' => true,
	'lazy'          => true,
	'attributes'    => [],
	'breakpoints'   => [],
], $displayData ?? []);

extract($displayData);

/**
 * @var string|null $file
 * @var string|null $caption
 * @var string|null $alt
 * @var bool|null   $resize
 * @var string|null $webp
 * @var string|null $webp_priority
 * @var string|null $lazy
 * @var array|null  $attributes
 * @var array|null  $breakpoints
 *
 * @var Registry    $params
 */

$params = $app->getTemplate(true)->params;
[$sizes, $srcSet, $imgSrc] = [null, null, $file];
[$webpSizes, $webpSrcSet, $webpSrc] = [null, null, null];

if (empty($file))
{
	return;
}

require_once JPATH_THEMES . '/' . $app->getTemplate() . '/helper/ImageResizer.php';

$caption = htmlspecialchars($caption ?? '');
$altText = htmlspecialchars($alt ?? '');
$resizer = ImageResizer::getInstance()
	->setRelativeCachePath($params->get('imgCacheFolder', 'autosized'));

if ($resize)
{
	// Use the default image breakpoints if none was specified
	if (empty($breakpoints))
	{
		// Default breakpoints when we have a right or a left column, but not both.
		$breakpoints = [
			'(min-width: 665px)' => 852,
			'(min-width: 485px)' => 635,
			''                   => 453,
		];

		/** @var \Joomla\CMS\Document\HtmlDocument $doc */
		$doc      = $app->getDocument();
		$hasLeft  = $doc->countModules('sidebar-left') > 0;
		$hasRight = $doc->countModules('sidebar-right') > 0;

		if (!$hasLeft && !$hasRight)
		{
			$breakpoints = [
				'(min-width: 884px)' => 1128,
				'(min-width: 667px)' => 852,
				'(min-width: 485px)' => 635,
				''                   => 453,
			];
		}
		elseif ($hasLeft && $hasRight)
		{
			$breakpoints = [
				'(min-width: 884px)' => 556,
				'(min-width: 667px)' => 852,
				'(min-width: 485px)' => 635,
				''                   => 453,
			];

		}
	}

	if (is_array($breakpoints) && !empty($breakpoints))
	{
		$resizer->setBreakPoints($breakpoints);
	}

	[$sizes, $srcSet, $imgSrc] = $resizer
		->getResizedSources($file);

	if ($webp)
	{
		[$webpSizes, $webpSrcSet, $webpSrc] = $resizer
			->getResizedSources($file, true);
	}
}

$classes       = $attributes['class'] ?? [];
$hasWebp       = !empty($webpSizes) && !empty($webpSrcSet);
$hasResized    = !empty($sizes) && !empty($srcSet);
$webp_priority = $hasWebp && $webp_priority && $hasResized;
$imageMime     = $resizer->getImageFileType($file);
[$width, $height] = $resizer->getImageSize($file);

if (!is_null($width))
{
	$attributes['width'] = $width;
}

if (!is_null($height))
{
	$attributes['height'] = $height;
}

if (isset($attributes['class']))
{
	unset($attributes['class']);
}
?>
<?php if (!$hasResized): ?>
	<img
		<?php if ($caption) : ?>
			title="<?= $caption ?>"
		<?php endif; ?>
		<?php if (!empty($classes)): ?>
			class="<?= implode(' ', $classes) ?>"
		<?php endif; ?>
			src="<?= $imgSrc ?>"
		<?php if ($lazy): ?>
			loading="lazy"
		<?php endif; ?>
			alt="<?= $altText; ?>"
		<?= ArrayHelper::toString($attributes) ?>
	/>
<?php elseif (!$webp_priority): ?>
	<picture>
		<?php if (!empty($webpSizes) && !empty($webpSrcSet)): ?>
			<source type="image/webp"
					sizes="<?= $webpSizes ?>"
					srcset="<?= $webpSrcSet ?>"
			>
		<?php endif; ?>
		<img
			<?php if ($caption) : ?>
				title="<?= $caption ?>"
			<?php endif; ?>
			<?php if (!empty($classes)): ?>
				class="<?= implode(' ', $classes) ?>"
			<?php endif; ?>
			<?php if (!empty($sizes) && !empty($srcSet)): ?>
				sizes="<?= $sizes ?>"
				srcset="<?= $srcSet ?>"
			<?php endif; ?>
				src="<?= $imgSrc ?>"
			<?php if ($lazy): ?>
				loading="lazy"
			<?php endif; ?>
				alt="<?= $altText; ?>"
			<?= ArrayHelper::toString($attributes) ?>
		/>
	</picture>
<?php else: ?>
	<picture>
		<source type="image/webp"
				sizes="<?= $webpSizes ?>"
				srcset="<?= $webpSrcSet ?>"
		>
		<source type="<?= $imageMime ?>"
				sizes="<?= $sizes ?>"
				srcset="<?= $srcSet ?>"
		>
		<img
			<?php if ($caption) : ?>
				title="<?= $caption ?>"
			<?php endif; ?>
			<?php if (!empty($classes)): ?>
				class="<?= implode(' ', $classes) ?>"
			<?php endif; ?>
				sizes="<?= $webpSizes ?>"
				srcset="<?= $webpSrcSet ?>"
				src="<?= $webpSrc ?>"
			<?php if ($lazy): ?>
				loading="lazy"
			<?php endif; ?>
				alt="<?= $altText; ?>"
			<?= ArrayHelper::toString($attributes) ?>
		/>
	</picture>
<?php endif; ?>
