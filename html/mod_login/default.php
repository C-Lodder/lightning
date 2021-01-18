<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

$app->getDocument()->getWebAssetManager()
	->useScript('core')
	->useScript('keepalive')
	->useScript('field.passwordview');

Text::script('JSHOWPASSWORD');
Text::script('JHIDEPASSWORD');
?>
<form id="login-form-<?php echo $module->id; ?>" class="mod-login" action="<?php echo Route::_('index.php', true); ?>" method="post">

	<?php if ($params->get('pretext')) : ?>
		<div class="mod-login__pretext pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>

	<div class="mod-login__userdata userdata">
		<div class="mod-login__username form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="username" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>">
					<span class="input-group-append">
						<label for="modlgn-username-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
						<span class="input-group-text" title="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
						</span>
					</span>
				</div>
			<?php else : ?>
				<label for="modlgn-username-<?php echo $module->id; ?>"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
				<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="username" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>">
			<?php endif; ?>
		</div>

		<div class="mod-login__password form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
					<span class="input-group-append">
						<label for="modlgn-passwd-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
						<button type="button" class="btn btn-secondary input-password-toggle">
							<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
							<span class="sr-only"><?php echo Text::_('JSHOWPASSWORD'); ?></span>
						</button>
					</span>
				</div>
			<?php else : ?>
				<label for="modlgn-passwd-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
			<?php endif; ?>
		</div>

		<?php if (count($twofactormethods) > 1) : ?>
			<div class="mod-login__twofactor form-group">
				<?php if (!$params->get('usetext', 0)) : ?>
					<div class="input-group">
						<span class="input-group-prepend" title="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
							<span class="input-group-text">
								<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
							</span>
							<label for="modlgn-secretkey-<?php echo $module->id; ?>" class="sr-only"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
						</span>
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-append" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>">
							<span class="input-group-text">
								<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/></svg>
							</span>
						</span>
					</div>
				<?php else : ?>
					<label for="modlgn-secretkey-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<div class="input-group">
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-append" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>">
							<span class="input-group-text">
								<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/></svg>
							</span>
						</span>
					</div>
				<?php endif; ?>
			</div>
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

		<?php foreach($extraButtons as $button):
			$dataAttributeKeys = array_filter(array_keys($button), function ($key) {
				return substr($key, 0, 5) == 'data-';
			});
			?>
			<div class="mod-login__submit form-group">
				<button type="button"
						class="btn btn-secondary btn-block mt-4 <?php echo $button['class'] ?? '' ?>"
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
			</div>
		<?php endforeach; ?>

		<div class="mod-login__submit form-group">
			<button type="submit" name="Submit" class="btn btn-primary"><?php echo Text::_('JLOGIN'); ?></button>
		</div>

		<?php
			$usersConfig = ComponentHelper::getParams('com_users'); ?>
			<ul class="mod-login__options list-unstyled">
				<li>
					<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
				<li>
					<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li>
				<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li>
					<a href="<?php echo Route::_($registerLink); ?>"><?php echo Text::_('MOD_LOGIN_REGISTER'); ?></a>
				</li>
				<?php endif; ?>
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
