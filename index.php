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

/** @var JDocumentHtml $this */

$app  = Factory::getApplication();
$lang = $app->getLanguage();

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu->getParams()->get('pageclass_sfx');

// Template params
$themeSwitcher = (boolean)$this->params->get('theme-switcher', true);
if ($themeSwitcher)
{
	HTMLHelper::_('stylesheet', 'switch.css', ['version' => 'auto', 'relative' => true]);
	HTMLHelper::_('script', 'switch.min.js', ['version' => 'auto', 'relative' => true]);
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

$hasBanner = '';

if ($this->countModules('banner'))
{
	$hasBanner = 'has-banner';
}

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<style><?php echo $css; ?></style>
</head>

<body class="site-grid site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ' ' . $pageclass
	. $hasSidebar;
	echo ($this->direction == 'rtl' ? ' rtl' : '');
?>">
	<header class="grid-child container-header full-width header <?php echo $hasBanner; ?>">
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
					<jdoc:include type="modules" name="menu" style="none" />
					<?php if ($this->countModules('search')) : ?>
						<div>
							<jdoc:include type="modules" name="search" style="none" />
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
		<jdoc:include type="modules" name="banner" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('top-a')) : ?>
	<div class="grid-child container-top-a">
		<jdoc:include type="modules" name="top-a" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('top-b')) : ?>
	<div class="grid-child container-top-b">
		<jdoc:include type="modules" name="top-b" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('sidebar-left')) : ?>
	<div class="grid-child container-sidebar-left">
		<jdoc:include type="modules" name="sidebar-left" style="default" />
	</div>
	<?php endif; ?>

	<div class="grid-child container-component">
		<jdoc:include type="modules" name="main-top" style="default" />
		<jdoc:include type="message" />
		<jdoc:include type="modules" name="breadcrumbs" style="none" />
		<jdoc:include type="component" />
		<jdoc:include type="modules" name="main-bottom" style="default" />
	</div>

	<?php if ($this->countModules('sidebar-right')) : ?>
	<div class="grid-child container-sidebar-right">
		<jdoc:include type="modules" name="sidebar-right" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('bottom-a')) : ?>
	<div class="grid-child container-bottom-a">
		<jdoc:include type="modules" name="bottom-a" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('bottom-b')) : ?>
	<div class="grid-child container-bottom-b">
		<jdoc:include type="modules" name="bottom-b" style="default" />
	</div>
	<?php endif; ?>

	<?php if ($this->countModules('footer')) : ?>
	<footer class="grid-child container-footer full-width footer">
		<div class="container">
			<jdoc:include type="modules" name="footer" style="none" />
		</div>
	</footer>
	<?php endif; ?>

	<jdoc:include type="modules" name="debug" style="none" />

	<?php if ($themeSwitcher) : ?>
	<script>
		(() => {
			const theme = localStorage.getItem('theme') ?? 'is-light'
			document.documentElement.classList.add(theme)
		})()
	</script>
	<?php endif; ?>

	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</body>
</html>
