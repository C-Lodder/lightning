<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class JFormFieldAbout extends Joomla\CMS\Form\FormField
{
	/**
	 * @var string
	 */
	protected $type = 'About';

	/**
	 * @var  boolean
	 */
	protected $hiddenLabel = true;

	/**
	 * @return string
	 */
	protected function getInput()
	{
		$xml  = file_get_contents(JPATH_SITE . '/templates/lightning/templateDetails.xml');
		$json = simplexml_load_string($xml);

		$html = '<ul>';
		$html .= '<li>' . Text::_('TPL_LIGHTNING_FIELDSET_ABOUT_VERSION') . ' - <strong>' . $json->version . '</strong></li>';
		$html .= '<li>' . Text::_('TPL_LIGHTNING_FIELDSET_ABOUT_REPORT_BUG') . ' - <a href="https://github.com/C-Lodder/lightning" target="_blank" rel="noopener">https://github.com/C-Lodder/lightning</a></li>';
		$html .= '</ul>';

		return $html;
	}
}
