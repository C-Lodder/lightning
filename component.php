<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Fetch CSS
$css = file_get_contents(__DIR__ . '/css/template.css');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style><?php echo $css; ?></style>
	<jdoc:include type="head" />
</head>
<body class="<?php echo $this->direction === 'rtl' ? 'rtl' : ''; ?>">
	<jdoc:include type="message" />
	<jdoc:include type="component" />

	<script>
		(() => {
			const theme = localStorage.getItem('theme') ?? 'is-light'
			document.documentElement.classList.add(theme)
		})()
	</script>
</body>
</html>
