<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var JDocumentHtml $this */

$twofactormethods = AuthenticationHelper::getTwoFactorMethods();
$app              = Factory::getApplication();

// Template params
$themeSwitcher = (boolean)$this->params->get('theme-switcher', true);
if ($themeSwitcher)
{
	HTMLHelper::_('stylesheet', 'switch.css', ['version' => 'auto', 'relative' => true]);
}
HTMLHelper::_('script', 'switch.min.js', ['version' => 'auto', 'relative' => true], ['type' => 'module']);

// Fetch CSS
$css = file_get_contents(__DIR__ . '/css/template.css');

// Logo file or site title param
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . Uri::root() . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES) . '" alt="' . $sitename . '">';
}
elseif ($this->params->get('siteTitle'))
{
	$logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
}
else
{
	$logo = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 508.928 508.928" height="50"><path fill="hsl(210, 100%, 50%)" d="M403.712 201.04H256.288L329.792 0 105.216 307.888H252.64l-73.504 201.04z"/></svg>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<jdoc:include type="metas" />
	<style><?php echo $css; ?></style>
</head>
<body class="site-grid site">
	<div class="grid-child container-header full-width">
		<header class="header">
			<nav class="grid-child navbar">
				<div class="navbar-brand">
					<?php if (!empty($logo)) : ?>
						<a href="<?php echo $this->baseurl; ?>/">
							<?php echo $logo; ?>
							<span class="sr-only"><?php echo Text::_('TPL_LIGHTNING_LOGO_LABEL'); ?></span>
						</a>
					<?php endif; ?>
				</div>
				<?php if ($themeSwitcher) : ?>
				<div class="color-scheme-switch" id="color-scheme-switch">
					<input type="radio" name="color-scheme-switch" value="is-light" class="color-scheme-switch-radio" aria-label="Light color scheme">
					<input type="radio" name="color-scheme-switch" value="is-system" class="color-scheme-switch-radio" aria-label="System color scheme">
					<input type="radio" name="color-scheme-switch" value="is-dark" class="color-scheme-switch-radio" aria-label="Dark color scheme">
					<label class="color-scheme-switch-label" for="color-scheme-switch"></label>
				</div>
			<?php endif; ?>
			</nav>
		</header>
		<div class="grid-child container-component">
			<div class="container">
				<h1><?php echo $sitename; ?></h1>
				<?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
					<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo $sitename; ?>">
				<?php endif; ?>
				<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
					<p><?php echo $app->get('offline_message'); ?></p>
				<?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
					<p><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
				<?php endif; ?>
				<div>
					<jdoc:include type="message" />
					<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
						<fieldset>
							<p>
								<label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
								<input name="username" id="username" type="text">
							</p>

							<p>
								<label for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
								<input name="password" id="password" type="password">
							</p>

							<?php if (count($twofactormethods) > 1) : ?>
							<p>
								<label for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
								<input name="secretkey" id="secretkey" type="text">
							</p>
							<?php endif; ?>

							<p>
								<button type="submit" name="Submit"><?php echo Text::_('JLOGIN'); ?></button>
							<p>

							<input type="hidden" name="option" value="com_users">
							<input type="hidden" name="task" value="user.login">
							<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
							<?php echo HTMLHelper::_('form.token'); ?>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>

	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</body>
</html>
