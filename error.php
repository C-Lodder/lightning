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

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app           = Factory::getApplication();
$wa            = $this->getWebAssetManager();
$sitename      = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu          = $app->getMenu()->getActive();
$pageclass     = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$themeSwitcher = (boolean)$this->params->get('theme-switcher', 1);
$fontAwesome   = (boolean)$this->params->get('font-awesome-thats-actually-rather-shit', 1);

// Template params
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

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>

<body class="site-grid site <?php echo $pageclass; ?>">
	<header class="grid-child container-header full-width header">
		<nav class="navbar">
			<div class="navbar-brand">
				<a href="<?php echo $this->baseurl; ?>/">
					<?php echo $logo; ?>
				</a>
				<?php if ($this->params->get('siteDescription')) : ?>
					<div><?php echo htmlspecialchars($this->params->get('siteDescription')); ?></div>
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
		<h1><?php echo Text::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
		<div class="card">
			<jdoc:include type="message" />
			<p><strong><?php echo Text::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
			<p><?php echo Text::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
			<ul>
				<li><?php echo Text::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
				<li><?php echo Text::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
				<li><?php echo Text::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
				<li><?php echo Text::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
			</ul>
			<p><?php echo Text::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
			<p><a href="<?php echo $this->baseurl; ?>/index.php"><?php echo Text::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
			<hr>
			<p><?php echo Text::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
			<blockquote>
				<span class="badge badge-primary"><?php echo $this->error->getCode(); ?></span> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
			</blockquote>
			<?php if ($this->debug) : ?>
				<div>
					<?php echo $this->renderBacktrace(); ?>
					<?php // Check if there are more Exceptions and render their data as well ?>
					<?php if ($this->error->getPrevious()) : ?>
						<?php $loop = true; ?>
						<?php // Reference $this->_error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly ?>
						<?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions ?>
						<?php $this->setError($this->_error->getPrevious()); ?>
						<?php while ($loop === true) : ?>
							<p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
							<p><?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
							<?php echo $this->renderBacktrace(); ?>
							<?php $loop = $this->setError($this->_error->getPrevious()); ?>
						<?php endwhile; ?>
						<?php // Reset the main error object to the base error ?>
						<?php $this->setError($this->error); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($this->countModules('footer')) : ?>
	<footer class="grid-child container-footer full-width footer">
		<div class="container">
			<jdoc:include type="modules" name="footer" style="none" />
		</div>
	</footer>
	<?php endif; ?>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
