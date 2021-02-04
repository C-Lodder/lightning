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

class JFormFieldCss extends Joomla\CMS\Form\FormField
{
	/**
	 * @var string
	 */
	protected $type = 'Css';

	/**
	 * @return void
	 */
	protected function getLabel()
	{
		return;
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		HTMLHelper::_('script', Uri::root() . 'templates/lightning/js/fields/colour-picker.min.js', ['version' => 'auto'], ['type' => 'module']);
		HTMLHelper::_('script', Uri::root() . 'templates/lightning/js/fields/custom-variables.min.js', ['version' => 'auto'], ['type' => 'module']);
		HTMLHelper::_('stylesheet', Uri::root() . 'templates/lightning/css/fields/colour-picker.css', ['version' => 'auto']);

		// Load helper
		\JLoader::register('TplLightningHelper', dirname(__DIR__) . '/helper.php');
		$helper = new TplLightningHelper();

		$variables = $helper->getCssVariables();

		$html = '';
		$html .= '<p><button class="btn btn-success" type="button" data="save-css">' . Text::_('Save CSS variables') . '</button></p>';

		$html .= '<table class="table align-middle" id="css-variables">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th scope="col" style="width:10%">Colour scheme</th>';
		$html .= '<th scope="col">Variable</th>';
		$html .= '<th scope="col">Default value</th>';
		$html .= '<th scope="col">Override</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ($variables as $colourScheme => $array)
		{
			foreach ($array as $variable => $value)
			{
				$val = $value;
				$override = '';

				if (is_array($value))
				{
					$val = $value[0];
					$override = $value[1];
				}

				// Get the type of variable to determine the input type
				$type = $helper->detectCssVariableType($val);
				$inputType = $type === 'color' ? 'text' : $type;

				$badgeClass = $colourScheme === 'dark' ? 'bg-dark' : 'bg-info';

				$html .= '<tr data-colour-scheme="' . $colourScheme . '">';
				$html .= '<td><span class="badge ' . $badgeClass . '">' . $colourScheme . '</span></td>';
				$html .= '<td class="css-variable">' . $variable . '</td>';
				$html .= '<td>';
				if ($type === 'color')
				{
					$html .= '<div class="has-swatch">';
				}
				$html .= '<input readonly type="' . $inputType . '" class="form-control css-default" value=\'' . htmlspecialchars($val, ENT_QUOTES) . '\'>';
				if ($type === 'color')
				{
					$html .= '<colour-swatch style="background-color:' . htmlspecialchars($val, ENT_QUOTES) . '"></colour-swatch>';
					$html .= '</div>';
				}
				$html .= '</td>';
				$html .= '<td>';
				
				// If value is a variable call var(--hiq-xxxx), we'll need to run a while loop to to continue searching for it
				// Add ability to create dark override for default
				if ($type === 'color')
				{
					$html .= '<colour-picker>';
				}
				$html .= '<input type="' . $inputType . '" class="form-control css-override" value=\'' . htmlspecialchars($override, ENT_QUOTES) . '\' data-default-value=\'' . htmlspecialchars($override, ENT_QUOTES) . '\'>';
				if ($type === 'color')
				{
					$html .= '</colour-picker>';
				}

				$html .= '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</tbody>';
		$html .= '</table>';

		$html .= '<p><button class="btn btn-success" type="button" data="save-css">' . Text::_('Save CSS variables') . '</button></p>';

		return $html;
	}
}
