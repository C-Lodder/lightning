<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// JLayout for standard handling of the edit modules:

$moduleHtml   = &$displayData['moduleHtml'];
$mod          = $displayData['module'];
$position     = $displayData['position'];
$menusEditing = $displayData['menusediting'];
$parameters   = ComponentHelper::getParams('com_modules');
$redirectUri  = '&return=' . urlencode(base64_encode(Uri::getInstance()->toString()));
$target       = '_blank';
$itemid       = Factory::getApplication()->input->get('Itemid', '0', 'int');
$editUrl      = Uri::base() . 'administrator/index.php?option=com_modules&task=module.edit&id=' . (int) $mod->id;

// If Module editing site
if ($parameters->get('redirect_edit', 'site') === 'site')
{
	$editUrl = Uri::base() . 'index.php?option=com_config&view=modules&id=' . (int) $mod->id . '&Itemid=' . $itemid . $redirectUri;
	$target  = '_self';
}

$svg = '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="1em"><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"/></svg>';

// Add link for editing the module
$count = 0;
$moduleHtml = preg_replace(
	// Find first tag of module
	'/^(\s*<(?:div|span|nav|ul|ol|h\d|section|aside|address|article|form) [^>]*>)/',
	// Create and add the edit link and tooltip
	'\\1 <a class="btn btn-link jmodedit" href="' . $editUrl . '" target="' . $target . '" aria-describedby="tip-' . (int) $mod->id . '">' . $svg . '
	<span class="visually-hidden">' . Text::_('JGLOBAL_EDIT') . '</span></a>
	<div role="tooltip" id="tip-' . (int) $mod->id . '">' . Text::_('JLIB_HTML_EDIT_MODULE') . '<br>' . htmlspecialchars($mod->title, ENT_COMPAT, 'UTF-8') . '<br>' . sprintf(Text::_('JLIB_HTML_EDIT_MODULE_IN_POSITION'), htmlspecialchars($position, ENT_COMPAT, 'UTF-8')) . '</div>',
	$moduleHtml,
	1,
	$count
);

// If menu editing is enabled and allowed and it's a menu module add link for editing
if ($menusEditing && $mod->module === 'mod_menu')
{
	// find the menu item id
	$regex = '/\bitem-(\d+)\b/';

	preg_match_all($regex, $moduleHtml, $menuItemids);
	if ($menuItemids)
	{
		foreach ($menuItemids[1] as $menuItemid)
			{
				$menuitemEditUrl = Uri::base() . 'administrator/index.php?option=com_menus&view=item&client_id=0&layout=edit&id=' . (int) $menuItemid;
				$moduleHtml = preg_replace(
					// Find the link
					'/(<li.*?\bitem-' . $menuItemid . '.*?>)/',
					// Create and add the edit link
					'\\1 <a class="jmenuedit small" href="' . $menuitemEditUrl . '" target="' . $target . '" title="' . Text::_('JLIB_HTML_EDIT_MENU_ITEM') . ' ' . sprintf(Text::_('JLIB_HTML_EDIT_MENU_ITEM_ID'), (int) $menuItemid) . '">
					' . $svg . '</span></a>',
					$moduleHtml
				);
			}
	}
}

if ($count)
{
	HTMLHelper::_('stylesheet', 'system/frontediting.css', ['version' => 'auto', 'relative' => true]);
}
