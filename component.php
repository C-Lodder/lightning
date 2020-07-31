<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Fetch CSS
$css = file_get_contents(__DIR__ . '/css/template.css');

if (Factory::getApplication()->input->get('option') === 'com_media')
{
	Joomla\CMS\HTML\HTMLHelper::_('stylesheet',
		Joomla\CMS\Uri\Uri::root() . 'templates/lightning/css/modal.css', ['version' => 'auto']);
}
HTMLHelper::_('script', 'switch.min.js', ['version' => 'auto', 'relative' => true], ['type' => 'module']);

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style><?php echo $css; ?></style>
	<jdoc:include type="head" />
</head>
<body>
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
