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

				$bg = $val;

				// Get the type of variable to determine the input type
				$type = $helper->detectCssVariableType($val);

				// If the variable is mapped to another variable, we'll need to traverse until we get the final colour.
				if ($type === 'mapped')
				{
					$var = $val;
					do
					{
						// Remove wrapped "var()" from string
						$trimmed = $helper->trimVariable($var);

						// Firstly check if the variable belongs in the dark variables array, then the default array
						if (isset($variables['dark'][$trimmed]))
						{
							$var = is_array($variables['dark'][$trimmed]) ? $variables['dark'][$trimmed][0] : $variables['dark'][$trimmed];
						}
						else if (isset($variables['default'][$trimmed]))
						{
							$var = is_array($variables['default'][$trimmed]) ? $variables['default'][$trimmed][0] : $variables['default'][$trimmed];
						}
						else
						{
							// If if can't be found, break the loop
							break;
						}

						// Update the variables for the next loop
						$type = $helper->detectCssVariableType($var);
						$bg = $var;
					}
					while ($type === 'mapped');
				}

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
					$html .= '<colour-swatch style="background-color:' . htmlspecialchars($bg, ENT_QUOTES) . '"></colour-swatch>';
					$html .= '</div>';
				}
				$html .= '</td>';
				$html .= '<td>';
				
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
