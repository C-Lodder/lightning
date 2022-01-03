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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

/** @var array $displayData */

// Get the author's avatar and profile link, if any
$avatarURL      = null;
$profileResults = null;

// Avatars and profiles only apply on articles which have not been filed pseudonymously for privacy reasons
if (empty($displayData['item']->created_by_alias))
{
	PluginHelper::importPlugin('engage');
	$user           = Factory::getUser($displayData['item']->created_by);
	$app            = Factory::getApplication();
	$filterFunction = function ($carry, $result) {
		if (!is_null($carry))
		{
			return $carry;
		}

		return (empty($result) || !is_string($result)) ? $carry : $result;
	};
	$avatarResults  = $app->triggerEvent('onAkeebaEngageUserAvatarURL', [$user, 96]);
	$profileResults = $app->triggerEvent('onAkeebaEngageUserProfileURL', [$user]);
	$avatarURL      = array_reduce($avatarResults, $filterFunction, null);
	$profileURL     = array_reduce($profileResults, $filterFunction, null);
}

$author    = ($displayData['item']->created_by_alias ?: $displayData['item']->author);
$authorURL = $displayData['item']->contact_link ?? null;
$target    = '_self';
$rel       = 'author';

if (empty($authorURL) && !empty($avatarURL) && !empty($profileURL))
{
	$displayData['params']->set('link_author', 1);
	$authorURL = $profileURL;
	$target    = '_blank';
	$rel       = 'noopener';
}

?>
<dd class="createdby" itemprop="author" itemscope itemtype="https://schema.org/Person">
	<?php if ($avatarURL): ?>
		<img src="<?= $avatarURL ?>" width="32" height="32" itemprop="image" alt="" class="img-circle author-image">
	<?php endif; ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php if (!empty($authorURL) && $displayData['params']->get('link_author') == true) : ?>
		<?= Text::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', $authorURL, $author, [
			'itemprop' => 'url', 'target' => $target, 'rel' => $rel,
		])); ?>
	<?php else : ?>
		<?= Text::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
	<?php endif; ?>
</dd>
