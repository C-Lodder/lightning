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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$usersConfig = ComponentHelper::getParams('com_users');

?>
<div class="com-users-login login">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="com-users-login__description login-description">
	<?php endif; ?>

		<?php if ($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if ($this->params->get('login_image') != '') : ?>
			<?php $alt = empty($this->params->get('login_image_alt')) && empty($this->params->get('login_image_alt_empty'))
				? ''
				: 'alt="' . htmlspecialchars($this->params->get('login_image_alt'), ENT_COMPAT, 'UTF-8') . '"'; ?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="com-users-login__image login-image" <?php echo $alt; ?>>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="com-users-login__form form-validate form-horizontal well" id="com-users-login__form">

		<fieldset>
			<?php echo $this->form->renderFieldset('credentials', ['class' => 'com-users-login__input']); ?>

			<?php if ($this->tfa) : ?>
				<?php echo $this->form->renderField('secretkey', null, null, ['class' => 'com-users-login__secretkey']); ?>
			<?php endif; ?>

			<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
				<div  class="com-users-login__remember control-group">
					<div class="control-label">
						<label for="remember">
							<?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
						</label>
					</div>
					<div class="controls">
						<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes">
					</div>
				</div>
			<?php endif; ?>

			<div class="com-users-login-buttons">
				<button type="submit" class="btn btn-primary akeeba-sociallogin-link-button-j4 akeeba-sociallogin-link-button-email">
					<span class="icon-user icon-social-user"></span>
					<?php echo Text::_('JLOGIN'); ?>
				</button>

				<?php foreach ($this->extraButtons as $button):
					$dataAttributeKeys = array_filter(array_keys($button), function ($key) {
						return substr($key, 0, 5) == 'data-';
					});
					?>
					<button type="button"
							class="btn btn-secondary <?php echo $button['class'] ?? '' ?>"
					<?php foreach ($dataAttributeKeys as $key): ?>
					<?php echo $key ?>="<?php echo $button[$key] ?>"
				<?php endforeach; ?>
					<?php if ($button['onclick']): ?>
					onclick="<?php echo $button['onclick'] ?>"
				<?php endif; ?>
					title="<?php echo Text::_($button['label']) ?>"
					id="<?php echo $button['id'] ?>"
					>
					<?php if (!empty($button['icon'])): ?>
					<span class="<?php echo $button['icon'] ?>"></span>
				<?php elseif (!empty($button['image'])): ?>
					<?php echo HTMLHelper::_('image', $button['image'], Text::_($button['tooltip'] ?? ''), [
						'class' => 'icon',
					], true) ?>
				<?php elseif (!empty($button['svg'])): ?>
					<?php echo $button['svg']; ?>
				<?php endif; ?>
					<?php echo Text::_($button['label']) ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
			<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>">
			<?php echo HTMLHelper::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div>
	<div class="com-users-login__options list-group">
		<a class="com-users-login__reset list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo Text::_('COM_USERS_LOGIN_RESET'); ?>
		</a>
		<a class="com-users-login__remind list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?>
		</a>
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<a class="com-users-login__register list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
