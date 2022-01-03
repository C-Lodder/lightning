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

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Template\Lightning\ImageResizer;

defined('_JEXEC') or die;

/** @var array $displayData */

$blockPosition = $displayData['params']->get('info_block_position', 0);

/**
 * @var SiteApplication           $app
 * @var \Joomla\Registry\Registry $params
 */
$app        = JFactory::getApplication();
$params     = $app->getTemplate(true)->params;
$images     = call_user_func(function () use ($app, $params, $displayData): array {
	// Get the article images
	$images     = json_decode($displayData['item']->images);
	$introImage = $images->image_intro ?? '';
	$imagePath  = JPATH_SITE . '/' . $introImage;
	$imageUrl   = Uri::base(false) . $introImage;

	// No or invalid image? Early return
	if (empty($introImage) || !@file_exists($imagePath) || !is_readable($imagePath))
	{
		return [];
	}

	// If I don't need to resize the image only return the regular image's URL
	$autoResize = $params->get('auto-resize', 1);

	if (!$autoResize)
	{
		return [$imageUrl];
	}

	require_once JPATH_THEMES . '/' . $app->getTemplate() . '/helper/ImageResizer.php';

	$resizer = ImageResizer::getInstance()
		->setRelativeCachePath($params->get('imgCacheFolder', 'autosized'));
	/** @var HtmlDocument $doc */
	$doc      = $app->getDocument();
	$hasLeft  = $doc->countModules('sidebar-left') > 0;
	$hasRight = $doc->countModules('sidebar-right') > 0;
	$webp     = function_exists('imagewebp');

	// Get the breakpoints
	$breakpoints = [
		'(min-width: 665px)' => 852,
		'(min-width: 485px)' => 635,
		''                   => 453,
	];

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

	$srcSetToUrls = function (?string $srcSet): array {
		if (empty($srcSet) || empty(trim($srcSet)))
		{
			return [];
		}

		$urls = [];

		foreach (explode(',', $srcSet) as $line)
		{
			$line = trim($line);

			if (empty($line))
			{
				continue;
			}

			$lastSpace = strrpos($line, ' ');

			if ($lastSpace === false)
			{
				continue;
			}

			$urls[] = trim(substr($line, 0, $lastSpace - 1));
		}

		return $urls;
	};

	$resizer->setBreakPoints($breakpoints);

	[$sizes, $srcSet, $imgSrc] = $resizer
		->getResizedSources($imageUrl);
	$webpSrcSet = '';
	$webpSrc    = '';

	if ($webp)
	{
		[$webpSizes, $webpSrcSet, $webpSrc] = $resizer
			->getResizedSources($imageUrl, true);
	}

	$allUrls = array_merge([
		empty($imgSrc) ? '' : (Uri::base(false) . '/' . $imgSrc),
		empty($webpSrc) ? '' : (Uri::base(false) . '/' . $webpSrc),
	], $srcSetToUrls($srcSet), $srcSetToUrls($webpSrcSet));


	return array_unique(array_filter($allUrls, function ($x) {
		return !empty($x) && !empty(trim($x));
	}));
});

?>
	<dl class="article-info muted">

		<?php if ($displayData['position'] === 'above' && ($blockPosition == 0 || $blockPosition == 2)
				|| $displayData['position'] === 'below' && ($blockPosition == 1)
				) : ?>

			<dt class="article-info-term">
				<?php if ($displayData['params']->get('info_block_show_title', 1)) : ?>
					<?php echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?>
				<?php endif; ?>
			</dt>

			<?php if ($displayData['params']->get('show_author') && !empty($displayData['item']->author )) : ?>
				<?php echo $this->sublayout('author', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_parent_category') && !empty($displayData['item']->parent_slug)) : ?>
				<?php echo $this->sublayout('parent_category', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_category')) : ?>
				<?php echo $this->sublayout('category', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_associations')) : ?>
				<?php echo $this->sublayout('associations', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_publish_date')) : ?>
				<?php echo $this->sublayout('publish_date', $displayData); ?>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ($displayData['position'] === 'above' && ($blockPosition == 0)
				|| $displayData['position'] === 'below' && ($blockPosition == 1 || $blockPosition == 2)
				) : ?>
			<?php if ($displayData['params']->get('show_create_date')) : ?>
				<?php echo $this->sublayout('create_date', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_modify_date')) : ?>
				<?php echo $this->sublayout('modify_date', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_hits')) : ?>
				<?php echo $this->sublayout('hits', $displayData); ?>
			<?php endif; ?>
		<?php endif; ?>
	</dl>

	<meta itemprop="headline" content="<?= htmlentities($this->escape($displayData['item']->title)) ?>">
	<?php if (!empty($images)): ?>
	<meta itemprop="image" content="<?= implode(',', array_map(function ($url) {
		return htmlentities($url);
	}, $images)) ?>">
	<?php endif; ?>
	<meta itemprop="dateModified" content="<?= HTMLHelper::_('date', $displayData['item']->modified, 'c') ?>">
	<meta itemprop="mainEntityOfPage" content="<?= Route::_(
		RouteHelper::getArticleRoute($displayData['item']->slug, $displayData['item']->catid, $displayData['item']->language)
	) ?>">

	<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
		<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
			<meta itemprop="url" content="<?= Uri::base(false) ?>images/logos/webp/icon-red@4x.webp">
			<meta itemprop="width" content="400">
			<meta itemprop="height" content="400">
		</div>
		<meta itemprop="name" content="Dionysopoulos.me" />
	</div>
