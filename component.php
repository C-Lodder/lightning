<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$wa = $this->getWebAssetManager();

// Load template CSS
$wa->usePreset('template.lightning');

// Load switcher JS
// This should be loaded even if the themeSwitcher is disabled, so that the system preference will still dictate the theme
$wa->useScript('switch.js');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body>
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
