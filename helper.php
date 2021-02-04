<?php

\defined('JPATH_PLATFORM') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class TplLightningHelper
{
	/**
	 * Ajax wrapper for saveCss()
	 */
	public static function saveCssAjax()
	{
		$json = Factory::getApplication()->input->json->getArray();

		$helper = new TplLightningHelper();

		return $helper->saveCss($json);
	}

	/**
	 * Method saving custom CSS properties.
	 */
	private function saveCss($json)
	{
		$css = $json['css'];
		$response = '';

		try
		{
			// Add the default variables
			$string = ":root {\n";
			foreach ($css['default'] as $key => $value)
			{
				$string .= '  ' . $key . ': ' . $value . ";\n";
			}
			$string .= "}\n\n";

			// Add the dark variables
			$string .= ":root.is-dark {\n";
			foreach ($css['dark'] as $key => $value)
			{
				$string .= '  ' . $key . ': ' . $value . ";\n";
			}
			$string .= '}';

			// Write to file
			file_put_contents(
				JPATH_SITE . '/templates/lightning/css/custom-variables.css',
				$string,
			);

			$response = Text::_('CSS variables saved');
		}
		catch (Exception $e)
		{
		   $response = $e->getMessage();
		}

		return $response;
	}

	/**
	 * Parse the CSS file string and return an array of CSS variables
	 */
	private function parseCss($file)
	{
		$variables = [
			'default' => [],
			'dark' => [],
		];

		$colourScheme = 'default';

		$lines = explode("\n", $file);

		foreach ($lines as $line)
		{
			// Trim the line
			$trimmed = trim($line);

			// Check if we're dealing with a blank line
			if ($trimmed !== '')
			{
				// If the string contains ":root.is-dark", then we've started parsing the dark variables
				if (strpos($trimmed, ':root.is-dark') !== false)
				{
					$colourScheme = 'dark';
				}

				// Get the variable name and value
				preg_match('/^(--.*?):(.*?);$/', $trimmed, $match);

				if (isset($match[1]) && isset($match[2]))
				{
					$variables[$colourScheme][trim($match[1])] = trim($match[2]);
				}
			}
		}

		return $variables;
	}

	/**
	 * Get the source CSS variables file
	 */
	public function getCssVariables()
	{
		$templateVariables = $this->parseCss(
			file_get_contents(JPATH_SITE . '/templates/lightning/src/css/variables.css')
		);
		$overrideVariables = [
			'default' => [],
			'dark' => [],
		];

		// Check if the custom variable file exists
		if (file_exists(JPATH_SITE . '/templates/lightning/css/custom-variables.css'))
		{
			$overrideVariables = $this->parseCss(
				file_get_contents(JPATH_SITE . '/templates/lightning/css/custom-variables.css')
			);
		}

		$defaultVariables = array_merge_recursive($templateVariables['default'], $overrideVariables['default']);
		$darkVariables = array_merge_recursive($templateVariables['dark'], $overrideVariables['dark']);

		return [
			'default' => $defaultVariables,
			'dark' => $darkVariables
		];
	}

	/**
	 * Detect the type of CSS variable based on the value
	 */
	public function detectCssVariableType($variable)
	{
		$type = 'text';

		if (substr($variable, 0, 3) === 'hsl'
			|| substr($variable, 0, 1) === '#'
			|| substr($variable, 0, 3) === 'rgb'
			|| substr($variable, 0, 4) === 'rgba'
		)
		{
			$type = 'color';
		}
		else if (is_numeric($variable))
		{
			$type = 'number';
		}

		return $type;
	}
}
