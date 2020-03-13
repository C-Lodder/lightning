<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');
?>
<form id="login-form-<?php echo $module->id; ?>" class="mod-login" action="<?php echo Route::_('index.php', true); ?>" method="post">

	<?php if ($params->get('pretext')) : ?>
		<div class="mod-login__pretext pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>

	<div class="mod-login__userdata userdata">
		<p>
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="username" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>">
					<label for="modlgn-username-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
				</div>
			<?php else : ?>
				<label for="modlgn-username-<?php echo $module->id; ?>"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
				<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="username" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>">
			<?php endif; ?>
		</p>

		<p>
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<label for="modlgn-passwd-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
					<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
				</div>
			<?php else : ?>
				<label for="modlgn-passwd-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
			<?php endif; ?>
		</p>

		<?php if (count($twofactormethods) > 1) : ?>
			<p>
				<?php if (!$params->get('usetext', 0)) : ?>
					<div class="input-group">
						<label for="modlgn-secretkey-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="off" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-append" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>">
							<span class="input-group-text">
								<span class="fas fa-question" aria-hidden="true"></span>
							</span>
						</span>
					</div>
				<?php else : ?>
					<label for="modlgn-secretkey-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="off" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
					<span class="btn width-auto" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>">
						<span class="fas fa-question" aria-hidden="true"></span>
					</span>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="mod-login__remember form-group">
				<div id="form-login-remember-<?php echo $module->id; ?>" class="form-check">
					<label class="form-check-label">
						<input type="checkbox" name="remember" class="form-check-input" value="yes">
						<?php echo Text::_('MOD_LOGIN_REMEMBER_ME'); ?>
					</label>
				</div>
			</div>
		<?php endif; ?>

		<?php foreach($extraButtons as $button): ?>
			<p>
				<button type="button"
				        class="btn btn-secondary <?php echo $button['class'] ?? '' ?>"
				        onclick="<?php echo $button['onclick'] ?>"
				        title="<?php echo Text::_($button['label']) ?>"
				        id="<?php echo $button['id'] ?>"
						>
					<?php if (!empty($button['icon'])): ?>
						<span class="<?php echo $button['icon'] ?>"></span>
					<?php elseif (!empty($button['image'])): ?>
						<?php echo HTMLHelper::_('image', $button['image'], Text::_('PLG_SYSTEM_WEBAUTHN_LOGIN_DESC'), [
							'class' => 'icon',
						], true) ?>
					<?php endif; ?>
					<?= Text::_($button['label']) ?>
				</button>
			</p>
		<?php endforeach; ?>

		<p>
			<button type="submit" name="Submit" class="btn btn-primary"><?php echo Text::_('JLOGIN'); ?></button>
		</p>

		<?php
			$usersConfig = ComponentHelper::getParams('com_users'); ?>
			<ul class="mod-login__options list-unstyled">
			<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li>
					<a href="<?php echo Route::_($registerLink); ?>">
					<?php echo Text::_('MOD_LOGIN_REGISTER'); ?> <span class="fas fa-arrow-alt-circle-right"></span></a>
				</li>
			<?php endif; ?>
				<li>
					<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li>
				<li>
					<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
			</ul>
		<input type="hidden" name="option" value="com_users">
		<input type="hidden" name="task" value="user.login">
		<input type="hidden" name="return" value="<?php echo $return; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="mod-login__posttext posttext">
			<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
