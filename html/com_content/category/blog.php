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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentHelperRoute;
use Joomla\Template\Lightning\ImageResizer;

/**
 * @var \Joomla\CMS\Categories\CategoryNode     $category
 * @var \Joomla\CMS\Document\HtmlDocument       $doc
 * @var \Joomla\CMS\Application\SiteApplication $app
 */
$category = $this->category;
$doc      = $this->document;
$app      = Factory::getApplication();

include_once JPATH_THEMES . '/lightning/helper/ImageResizer.php';

$canonicalURL = Route::_(ContentHelperRoute::getCategoryRoute($category->id), true, Route::TLS_IGNORE, true);

ImageResizer::getInstance()->setArticleImages(
	$doc, $category->params->get('image')
);

$doc->setMetaData('og:type', 'website', 'property');
$doc->setMetaData('og:title', $this->params->get('page_title', $category->title), 'property');
$doc->setMetaData('og:description', $doc->getDescription(), 'property');
$doc->setMetaData('og:site_name', $app->get('sitename'), 'property');
$doc->setMetaData('og:url', $canonicalURL, 'property');
$doc->setMetaData('twitter:card', 'summary_large_image');
$doc->setMetaData('twitter:site', '@sledge812');
$doc->setMetaData('twitter:creator', '@sledge812');

require JPATH_COMPONENT . '/tmpl/category/blog.php';