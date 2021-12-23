<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Registry\Registry;

FormHelper::loadFieldClass('list');

class JFormFieldFonts extends Joomla\CMS\Form\Field\ListField
{
	/**
	 * @var  string
	 */
	protected $type = 'Fonts';

	/**
	 * @var  string
	 */
	private $apiUrl = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';

	/**
	 * @var  string
	 */
	protected $layout = 'joomla.form.field.list';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$data = $this->getLayoutData();

		$data['options'] = (array) $this->getOptions();

		return $this->getRenderer($this->layout)->render($data);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options = new Registry;
		$options->set('userAgent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0');
		$http = HttpFactory::getHttp($options);

		$array = [[
			'value' => '',
			'text'  => 'None',
		]];

		try
		{
			$result = $http->get($this->apiUrl . $this->getParams()->get('google-fonts-api-key', ''));
			$json = json_decode($result->body);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage('Unable to fetch API data', 'error');
			throw new \RuntimeException('Unable to fetch API data.', $e->getCode(), $e);
		}

		if (isset($json->error))
		{
			return Factory::getApplication()->enqueueMessage($json->error->message, 'error');
		}

		foreach ($json->items as $font)
		{
			$tmp = [
				'value' => $font->family,
				'text'  => $font->family,
			];
			$array[] = (object) $tmp;
		}

		return $array;
	}

	/**
	 * Method to get the lightning template parameters.
	 *
	 * @return  Registry  The params object.
	 */
	protected function getParams()
    {
        $db = Factory::getDbo();

        $db->setQuery(
            $db->getQuery(true)
                ->select('params')
                ->from('#__template_styles')
                ->where('template = ' . $db->q('lightning'))
                ->where('client_id = 0')
        );
        $registry = new Registry;
        $registry->loadString($db->loadResult());

        return $registry;
    }
}
