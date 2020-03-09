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

// Logo file or site title param
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . Uri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '">';
}
elseif ($this->params->get('siteTitle'))
{
	$logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
}
else
{
	$logo = '<img src="' . $this->baseurl . '/templates/' . $this->template . '/images/logo.svg" class="logo d-inline-block" alt="' . $sitename . '">';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<jdoc:include type="head" />
	<style><?php echo $css; ?></style>
</head>
<body class="site">
	<div class="outer">
		<div class="offline-card">
			<div class="header">
				<?php if (!empty($logo)) : ?>
					<h1><?php echo $logo; ?></h1>
				<?php else : ?>
					<h1><?php echo $sitename; ?></h1>
				<?php endif; ?>
				<?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
					<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo $sitename; ?>">
				<?php endif; ?>
				<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
					<p><?php echo $app->get('offline_message'); ?></p>
				<?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
					<p><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
				<?php endif; ?>
				<div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 74.8 74.8"><g fill="#1C3D5C"><path d="M13.5 37.7L12 36.3c-4.5-4.5-5.8-10.8-4.2-16.5-4.5-1-7.8-5-7.8-9.8C0 4.5 4.5 0 10 0c5 0 9.1 3.6 9.9 8.4 5.4-1.3 11.3.2 15.5 4.4l.6.6-7.4 7.4-.6-.6c-2.4-2.4-6.3-2.4-8.7 0-2.4 2.4-2.4 6.3 0 8.7l1.4 1.4 7.4 7.4 7.8 7.8-7.4 7.4-7.8-7.8-7.2-7.4z"/><path d="M21.8 29.5l7.8-7.8 7.4-7.4 1.4-1.4C42.9 8.4 49.2 7 54.8 8.6c.7-4.8 4.9-8.6 10-8.6 5.5 0 10 4.5 10 10 0 5.1-3.8 9.3-8.7 9.9 1.6 5.6.2 11.9-4.2 16.3l-.6.6-7.4-7.4.6-.6c2.4-2.4 2.4-6.3 0-8.7-2.4-2.4-6.3-2.4-8.7 0l-1.4 1.4L37 29l-7.8 7.8-7.4-7.3z"/><path d="M55 66.8c-5.7 1.7-12.1.4-16.6-4.1l-.6-.6 7.4-7.4.6.6c2.4 2.4 6.3 2.4 8.7 0 2.4-2.4 2.4-6.3 0-8.7L53 45.1l-7.4-7.4-7.8-7.8 7.4-7.4 7.8 7.8 7.4 7.4 1.5 1.5c4.2 4.2 5.7 10.2 4.4 15.7 4.9.7 8.6 4.9 8.6 9.9 0 5.5-4.5 10-10 10-4.9 0-8.9-3.5-9.9-8z"/><path d="M52.2 46l-7.8 7.8-7.4 7.4-1.4 1.4c-4.3 4.3-10.3 5.7-15.7 4.4-1 4.5-5 7.8-9.8 7.8-5.5 0-10-4.5-10-10C0 60 3.3 56.1 7.7 55c-1.4-5.5.1-11.5 4.3-15.8l.6-.6L20 46l-.6.6c-2.4 2.4-2.4 6.3 0 8.7 2.4 2.4 6.3 2.4 8.7 0l1.4-1.4 7.4-7.4 7.8-7.8 7.5 7.3z"/></g></svg>
				</div>
			</div>
			<div>
				<jdoc:include type="message" />
				<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
					<fieldset>
						<label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
						<input name="username" id="username" type="text">

						<label for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
						<input name="password" id="password" type="password">

						<?php if (count($twofactormethods) > 1) : ?>
						<label for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
						<input name="secretkey" id="secretkey" type="text">
						<?php endif; ?>

						<button type="submit" name="Submit"><?php echo Text::_('JLOGIN'); ?></button>

						<input type="hidden" name="option" value="com_users">
						<input type="hidden" name="task" value="user.login">
						<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
						<?php echo HTMLHelper::_('form.token'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
