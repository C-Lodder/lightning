<?php
/**
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020 JoomJunk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;

class PlgSampledataLightning extends CMSPlugin
{
	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 */
	protected $db;

	/**
	 * Application object
	 *
	 * @var    AdministratorApplication
	 */
	protected $app;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * Get an overview of the proposed sampledata.
	 *
	 * @return  object  Object containing the name, title, description, icon and steps.
	 */
	public function onSampledataGetOverview()
	{
		$data              = new stdClass;
		$data->name        = $this->_name;
		$data->title       = Text::_('PLG_SAMPLEDATA_LIGHTNING_OVERVIEW_TITLE');
		$data->description = Text::_('PLG_SAMPLEDATA_LIGHTNING_OVERVIEW_DESC');
		$data->icon        = 'bolt';
		$data->steps       = 2;

		return $data;
	}

	/**
	 * Eighth step to enter the sampledata. Modules.
	 *
	 * @return  array or void  Will be converted into the JSON response to the module.
	 */
	public function onAjaxSampledataApplyStep1()
	{
		if ($this->app->input->get('type') !== $this->_name)
		{
			return;
		}

		if (!ComponentHelper::isEnabled('com_modules'))
		{
			$response            = [];
			$response['success'] = true;
			$response['message'] = Text::sprintf('PLG_SAMPLEDATA_LIGHTNING_STEP_SKIPPED', 1, 'com_modules');

			return $response;
		}

		$model = $this->app->bootComponent('com_modules')->getMVCFactory()->createModel('Module', 'Administrator', ['ignore_request' => true]);

		$modules = [
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BANNER_TITLE'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BANNER_CONTENT'),
				'ordering'  => 1,
				'position'  => 'banner',
				'module'    => 'mod_custom',
				'showtitle' => 0,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],

			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-primary',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-primary',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-primary',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-primary',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],

			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-gray',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-gray',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-gray',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_TOP_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'top-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => 'border-gray',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			
			
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_A'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-a',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],

			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_BOTTOM_B'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_SHORT'),
				'ordering'  => 1,
				'position'  => 'bottom-b',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_MAIN_TOP'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_LONG'),
				'ordering'  => 1,
				'position'  => 'main-top',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_MAIN_BOTTOM'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_LOREM_IPSUM_LONG'),
				'ordering'  => 1,
				'position'  => 'main-bottom',
				'module'    => 'mod_custom',
				'showtitle' => 1,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'style'           => 'Lightning-default',
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_FOOTER_1_TITLE'),
				'content'   => Text::sprintf('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_FOOTER_1_CONTENT', date('Y')),
				'ordering'  => 1,
				'position'  => 'footer',
				'module'    => 'mod_custom',
				'showtitle' => 0,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
			[
				'title'     => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_FOOTER_2_TITLE'),
				'content'   => Text::_('PLG_SAMPLEDATA_LIGHTNING_SAMPLEDATA_MODULES_FOOTER_2_CONTENT'),
				'ordering'  => 1,
				'position'  => 'footer',
				'module'    => 'mod_custom',
				'showtitle' => 0,
				'params'    => [
					'target'          => 1,
					'count'           => 1,
					'cid'             => 3,
					'catid'           => [],
					'moduleclass_sfx' => '',
					'tag_search'      => 0,
					'ordering'        => 0,
					'cache'           => 1,
					'cache_time'      => 900
				],
			],
		];

		foreach ($modules as $module)
		{
			// Set values which are always the same.
			$module['id']          = 0;
			$module['asset_id']    = 0;
			$module['language']    = '*';
			$module['description'] = '';
			$module['assignment']  = 0;
			$module['access']      = (int) $this->app->get('access', 1);

			if (!isset($module['published']))
			{
				$module['published'] = 1;
			}

			if (!isset($module['note']))
			{
				$module['note'] = '';
			}

			if (!isset($module['content']))
			{
				$module['content'] = '';
			}

			if (!isset($module['showtitle']))
			{
				$module['showtitle'] = 1;
			}

			if (!isset($module['position']))
			{
				$module['position'] = '';
			}

			if (!isset($module['params']))
			{
				$module['params'] = [];
			}

			if (!isset($module['client_id']))
			{
				$module['client_id'] = 0;
			}

			if (!isset($module['assignment']))
			{
				$module['assignment'] = 0;
			}

			if (!$model->save($module))
			{
				Factory::getLanguage()->load('com_modules');
				$response            = [];
				$response['success'] = false;
				$response['message'] = Text::sprintf('PLG_SAMPLEDATA_LIGHTNING_STEP_FAILED', 1, Text::_($model->getError()));

				return $response;
			}
		}

		$response            = [];
		$response['success'] = true;
		$response['message'] = Text::_('PLG_SAMPLEDATA_LIGHTNING_STEP1_SUCCESS');

		return $response;
	}

	/**
	 * Final step to show completion of sampledata.
	 *
	 * @return  array or void  Will be converted into the JSON response to the module.
	 */
	public function onAjaxSampledataApplyStep2()
	{
		$response['success'] = true;
		$response['message'] = Text::_('PLG_SAMPLEDATA_LIGHTNING_STEP2_SUCCESS');

		return $response;
	}
}
