<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

include_once __DIR__ . '/helper/metas.php';
include_once __DIR__ . '/helper/styles.php';
include_once __DIR__ . '/helper/scripts.php';

/** @var JDocumentHtml $this */

$app           = Factory::getApplication();
$sitename      = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$pageclass     = $app->getMenu()->getActive()->getParams()->get('pageclass_sfx');
$themeSwitcher = (boolean)$this->params->get('theme-switcher', true);

// Template params
if ($themeSwitcher)
{
	HTMLHelper::_('stylesheet', 'switch.css', ['version' => 'auto', 'relative' => true]);
	HTMLHelper::_('script', 'switch.min.js', ['version' => 'auto', 'relative' => true], ['type' => 'module']);
}

// Font Awesome
HTMLHelper::_('stylesheet', 'media/vendor/fontawesome-free/css/fontawesome.min.css', ['version' => 'auto']);

// Fetch CSS
$css = file_get_contents(__DIR__ . '/css/template.css');

// Logo file or site title param
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

$this->addCustomTag('<link rel="apple-touch-icon" sizes="180x180" href="' . $faviconPath . '/apple-touch-icon.png">');
$this->addCustomTag('<link rel="icon" type="image/png" sizes="32x32" href="' . $faviconPath . '/favicon-32x32.png">');
$this->addCustomTag('<link rel="icon" type="image/png" sizes="16x16" href="' . $faviconPath . '/favicon-16x16.png">');
$this->addCustomTag('<link rel="manifest" href="' . $faviconPath . '/site.webmanifest">');
$this->addCustomTag('<link rel="mask-icon" href="' . $faviconPath . '/safari-pinned-tab.svg" color="#5bbad5">');
$this->addCustomTag('<link rel="shortcut icon" href="' . $faviconPath . '/favicon.ico">');

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
$metas        = $this->getBuffer('metas');
$styles       = $this->getBuffer('styles');
$scripts      = $this->getBuffer('scripts');

$cachesStyleSheets = json_encode(array_values($styles));
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<?php echo $metas; ?>
	<style><?php echo $css; ?></style>
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
						<div>
							<?php echo $search; ?>
						</div>
					<?php endif; ?>
				</div>
				<span id="navbar-menu-toggle" class="navbar-menu-toggle"><span></span></span>
			<?php endif; ?>
			<?php if ($themeSwitcher) : ?>
				<div class="color-scheme-switch">
					<input type="checkbox" name="color-scheme-switch" class="color-scheme-switch-checkbox" id="color-scheme-switch">
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

	<?php if ($themeSwitcher) : ?>
	<script>
		(() => {
			const prefersColourScheme = window.matchMedia('(prefers-color-scheme:light)')
			const colourScheme = prefersColourScheme.matches ? 'is-light' : 'is-dark'
			const theme = localStorage.getItem('theme') ?? colourScheme
			document.documentElement.classList.add(theme)

			const styles = <?php echo $cachesStyleSheets; ?>;
			styles.forEach(item => {
				document.body.insertAdjacentHTML('beforeend', item);
			})
		})()
	</script>
	<?php endif; ?>

	<?php echo $scripts; ?>
</body>
</html>
