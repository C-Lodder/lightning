<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Document\Renderer\Html;

\defined('JPATH_PLATFORM') || die;

use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\WebAsset\WebAssetAttachBehaviorInterface;
use Joomla\Utilities\ArrayHelper;

class metasRenderer extends DocumentRenderer
{
	/**
	 * Renders the document metas and returns the results as a string
	 *
	 * @param   string  $head     (unused)
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  The script
	 *
	 * @return  string  The output of the script
	 *
	 * @since   4.0.0
	 */
	public function render($head, $params = [], $content = null)
	{
		// Convert the tagids to titles
		if (isset($this->_doc->_metaTags['name']['tags']))
		{
			$tagsHelper = new TagsHelper;
			$this->_doc->_metaTags['name']['tags'] = implode(', ', $tagsHelper->getTagNames($this->_doc->_metaTags['name']['tags']));
		}

		/** @var \Joomla\CMS\Application\CMSApplication $app */
		$wa = $this->_doc->getWebAssetManager();
		$wc = $this->_doc->getScriptOptions('webcomponents');

		// Check for AttachBehavior and web components
		foreach ($wa->getAssets('script', true) as $asset)
		{
			if ($asset instanceof WebAssetAttachBehaviorInterface)
			{
				$asset->onAttachCallback($this->_doc);
			}

			if ($asset->getOption('webcomponent'))
			{
				$wc[] = $asset->getUri();
			}
		}

		if ($wc)
		{
			$this->_doc->addScriptOptions('webcomponents', array_unique($wc));
		}

		// Trigger the onBeforeCompileHead event
		Factory::getApplication()->triggerEvent('onBeforeCompileHead');

		// Add Script Options as inline asset
		$scriptOptions = $this->_doc->getScriptOptions();

		if ($scriptOptions)
		{
			$prettyPrint = (JDEBUG && \defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false);
			$jsonOptions = json_encode($scriptOptions, $prettyPrint);
			$jsonOptions = $jsonOptions ? $jsonOptions : '{}';

			$wa->addInlineScript(
				$jsonOptions,
				['name' => 'joomla.script.options', 'position' => 'before'],
				['type' => 'application/json', 'class' => 'joomla-script-options new'],
				['core']
			);
		}

		// Lock the AssetManager
		$wa->lock();

		$buffer = '';

		// Generate charset when using HTML5 (should happen first)
		$buffer .= '<meta charset="' . $this->_doc->getCharset() . '">';

		// Generate base tag (need to happen early)
		$base = $this->_doc->getBase();

		if (!empty($base))
		{
			$buffer .= '<base href="' . $base . '">';
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($this->_doc->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $contents)
			{
				if ($type !== 'http-equiv' && !empty($contents))
				{
					$buffer .= '<meta ' . $type . '="' . $name . '" content="'
						. htmlspecialchars($contents, ENT_COMPAT, 'UTF-8') . '">';
				}
			}
		}

		// Don't add empty descriptions
		$documentDescription = $this->_doc->getDescription();

		if ($documentDescription)
		{
			$buffer .= '<meta name="description" content="' . htmlspecialchars($documentDescription, ENT_COMPAT, 'UTF-8') . '">';
		}

		// Don't add empty generators
		$generator = $this->_doc->getGenerator();

		if ($generator)
		{
			$buffer .= '<meta name="generator" content="' . htmlspecialchars($generator, ENT_COMPAT, 'UTF-8') . '">';
		}

		$buffer .= '<title>' . htmlspecialchars($this->_doc->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>';

		// Generate link declarations
		foreach ($this->_doc->_links as $link => $linkAtrr)
		{
			$buffer .= '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';

			if (\is_array($linkAtrr['attribs']))
			{
				if ($temp = ArrayHelper::toString($linkAtrr['attribs']))
				{
					$buffer .= ' ' . $temp;
				}
			}

			$buffer .= '>';
		}

		// Add the custom tags to the buffer
		foreach ($this->_doc->_custom as $tag)
		{
			$buffer .= $tag;
		}

		return ltrim($buffer);
	}
}
