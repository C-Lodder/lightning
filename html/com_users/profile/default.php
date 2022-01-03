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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

$isThisMe = Factory::getUser()->id == $this->data->id;

PluginHelper::importPlugin('engage');
$user           = Factory::getUser($this->data->id);
$app            = Factory::getApplication();
$filterFunction = function ($carry, $result) {
	if (!is_null($carry))
	{
		return $carry;
	}

	return (empty($result) || !is_string($result)) ? $carry : $result;
};
$avatarResults  = $app->triggerEvent('onAkeebaEngageUserAvatarURL', [$user, 256]);
$profileResults = $app->triggerEvent('onAkeebaEngageUserProfileURL', [$user]);
$avatarURL      = array_reduce($avatarResults, $filterFunction, null);
$profileURL     = array_reduce($profileResults, $filterFunction, null);

?>
<div class="com-users-profile profile">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?= $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<div class="com-users-profile-basic-container">
		<div class="com-users-profile-basic-avatar">
			<img src="<?= $avatarURL ?>" alt="<?= $this->escape($this->data->name) ?>">
			<div>
				<a href="<?= $profileURL ?>" target="_blank"> Edit on Gravatar <span
							class="icon-external-link-alt"></span> </a>
			</div>
		</div>
		<div class="com-users-profile-basic-info">
			<h2><?= $this->escape($this->data->name) ?></h2>
			<div class="username">
				<?= $this->escape($this->data->username); ?>
			</div>
			<?php if ($isThisMe): ?>
				<div class="email">
					<?= $this->escape($this->data->email); ?>
				</div>
				<div class="member-since">
					<?= Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
					<span>
					<?= HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?>
				</span>
				</div>
				<div class="last-visited">
					<?php if ($this->data->lastvisitDate !== null) : ?>
						<?= Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
						<span>
					<?= HTMLHelper::_('date', $this->data->lastvisitDate, Text::_('DATE_FORMAT_LC1')); ?>
				</span>
					<?php else: ?>
						<?= Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($isThisMe) : ?>
				<div class="edit-profile">
					<a class="btn"
					   href="<?= Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
						<span class="icon-user" aria-hidden="true"></span> <?= Text::_('COM_USERS_EDIT_PROFILE'); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ($isThisMe) : ?>
			<div class="com-users-profile-sociallogin">
				<h2>Social media login</h2>
				<?php if (class_exists('Akeeba\\SocialLogin\\Library\\Helper\\Integrations')) echo \Akeeba\SocialLogin\Library\Helper\Integrations::getSocialLinkButtons() ?>
			</div>
		<?php endif; ?>
	</div>
</div>
