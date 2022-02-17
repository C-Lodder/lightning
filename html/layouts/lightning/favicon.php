<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2022 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

extract($displayData);

$doc = Factory::getApplication()->getDocument();

/**
 * @var string $path
 */

$doc->setMetaData('msapplication-config', $path . '/browserconfig.xml');
$doc->setMetaData('msapplication-TileColor', '#ffffff');

$doc->addHeadLink($path . '/apple-touch-icon.png', 'apple-touch-icon', 'rel', ['sizes' => '180x180']);
$doc->addHeadLink($path . '/favicon-32x32.png', 'icon', 'rel', ['sizes' => '32x32']);
$doc->addHeadLink($path . '/favicon-16x16.png', 'icon', 'rel', ['sizes' => '16x16']);
$doc->addHeadLink($path . '/site.webmanifest.json', 'manifest');
$doc->addHeadLink($path . '/safari-pinned-tab.svg', 'mask-icon', 'rel', ['color' => '#006bd6']);
$doc->addHeadLink($path . '/favicon.ico', 'shortcut icon');
