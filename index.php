<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app           = Factory::getApplication();
$wa            = $this->getWebAssetManager();
$sitename      = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$pageclass     = $app->getMenu()->getActive()->getParams()->get('pageclass_sfx');
$themeSwitcher = (boolean)$this->params->get('theme-switcher', 1);
$fontAwesome   = (boolean)$this->params->get('font-awesome-thats-actually-rather-shit', 1);

// Load switcher CSS
if ($themeSwitcher)
{
	$wa->useStyle('switch.css');
}

// Load template CSS
$wa->usePreset('template.lightning');

// Font Awesome
if ($fontAwesome)
{
	$wa->useStyle('fontawesome.css');
}

// Load switcher JS
// This should be loaded even if the themeSwitcher is disabled, so that the system preference will still dictate the theme
$wa->useScript('switch.js');

// Logo file or site title param
$logo = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 508.928 508.928" height="50"><path fill="hsl(210, 100%, 50%)" d="M403.712 201.04H256.288L329.792 0 105.216 307.888H252.64l-73.504 201.04z"/></svg>';

if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . Uri::root() . '/' . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES, 'UTF-8') . '" alt="' . $sitename . '">';
}
elseif ($this->params->get('logoSvg'))
{
	$logo = $this->params->get('logoSvg');
}
elseif ($this->params->get('siteTitle'))
{
	$logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
}

$hasSidebar = '';

if ($this->countModules('sidebar-left'))
{
	$hasSidebar .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right'))
{
	$hasSidebar .= ' has-sidebar-right';
}

$faviconPath = 'templates/' . $this->template . '/favicon';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$this->setMetaData('theme-color', '#ffffff');
$this->setMetaData('msapplication-config', $faviconPath . '/browserconfig.xml');
$this->setMetaData('msapplication-TileColor', '#ffffff');

$this->addHeadLink($faviconPath . '/apple-touch-icon.png', 'apple-touch-icon', 'rel', ['sizes' => '180x180']);
$this->addHeadLink($faviconPath . '/favicon-32x32.png', 'icon', 'rel', ['sizes' => '32x32']);
$this->addHeadLink($faviconPath . '/favicon-16x16.png', 'icon', 'rel', ['sizes' => '16x16']);
$this->addHeadLink($faviconPath . '/site.webmanifest.json', 'manifest');
$this->addHeadLink($faviconPath . '/safari-pinned-tab.svg', 'mask-icon', 'rel', ['color' => '#006bd6']);
$this->addHeadLink($faviconPath . '/favicon.ico', 'shortcut icon');

$menu         = $this->getBuffer('modules', 'menu', $attribs = ['style' => 'none']);
$search       = $this->getBuffer('modules', 'search', $attribs = ['style' => 'none']);
$banner       = $this->getBuffer('modules', 'banner', $attribs = ['style' => 'default']);
$topA         = $this->getBuffer('modules', 'top-a', $attribs = ['style' => 'default']);
$topB         = $this->getBuffer('modules', 'top-b', $attribs = ['style' => 'default']);
$sidebarLeft  = $this->getBuffer('modules', 'sidebar-left', $attribs = ['style' => 'default']);
$mainTop      = $this->getBuffer('modules', 'main-top', $attribs = ['style' => 'default']);
$message      = $this->getBuffer('message');
$breadcrumbs  = $this->getBuffer('modules', 'breadcrumbs', $attribs = ['style' => 'none']);
$component    = $this->getBuffer('component');
$mainBottom   = $this->getBuffer('modules', 'main-bottom', $attribs = ['style' => 'default']);
$sidebarRight = $this->getBuffer('modules', 'sidebar-right', $attribs = ['style' => 'default']);
$bottomA      = $this->getBuffer('modules', 'bottom-a', $attribs = ['style' => 'default']);
$bottomB      = $this->getBuffer('modules', 'bottom-b', $attribs = ['style' => 'default']);
$footer       = $this->getBuffer('modules', 'footer', $attribs = ['style' => 'none']);
$debug        = $this->getBuffer('modules', 'debug', $attribs = ['style' => 'none']);

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="site-grid site <?php echo $pageclass . $hasSidebar; ?>">
	<header class="grid-child container-header full-width header <?php echo $this->countModules('banner') ? 'has-banner' : ''; ?>">
		<nav class="navbar">
			<div class="navbar-brand">
				<a href="<?php echo $this->baseurl; ?>/">
					<?php echo $logo; ?>
					<span class="sr-only"><?php echo Text::_('TPL_LIGHTNING_LOGO_LABEL'); ?></span>
				</a>
				<?php if ($this->params->get('siteDescription')) : ?>
					<div><?php echo htmlspecialchars($this->params->get('siteDescription')); ?></div>
				<?php endif; ?>
			</div>

			<?php if ($this->countModules('menu') || $this->countModules('search')) : ?>
				<div class="navbar-menu">
					<?php echo $menu; ?>
					<?php if ($this->countModules('search')) : ?>
						<div class="navbar-end">
							<?php echo $search; ?>
						</div>
					<?php endif; ?>
				</div>
				<span id="navbar-menu-toggle" class="navbar-menu-toggle"><span></span></span>
			<?php endif; ?>
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

	<?php if ($this->countModules('banner')) : ?>
	<div class="grid-child full-width container-banner">
		<?php echo $banner; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('top-a')) : ?>
	<div class="grid-child container-top-a">
		<?php echo $topA; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('top-b')) : ?>
	<div class="grid-child container-top-b">
		<?php echo $topB; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('sidebar-left')) : ?>
	<div class="grid-child container-sidebar-left">
		<?php echo $sidebarLeft; ?>
	</div>
	<?php endif; ?>

	<div class="grid-child container-component">
		<?php echo $mainTop; ?>
		<?php echo $message; ?>
		<?php echo $breadcrumbs; ?>
		<?php echo $component; ?>
		<?php echo $mainBottom; ?>
	</div>

	<?php if ($this->countModules('sidebar-right')) : ?>
	<div class="grid-child container-sidebar-right">
		<?php echo $sidebarRight; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('bottom-a')) : ?>
	<div class="grid-child container-bottom-a">
		<?php echo $bottomA; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('bottom-b')) : ?>
	<div class="grid-child container-bottom-b">
		<?php echo $bottomB; ?>
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('footer')) : ?>
	<footer class="grid-child container-footer full-width footer">
		<div class="container">
			<?php echo $footer; ?>
		</div>
	</footer>
	<?php endif; ?>

	<?php echo $debug; ?>
</body>
</html>
