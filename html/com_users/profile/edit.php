<?php
/*
 * @package     Lightning
 *
 * @copyright   Copyright (C) 2020-2021 Nicholas K. Dionysopoulos. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This template is a derivative work of the Lightning template which is
 * Copyright (C) 2020 JoomJunk.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

//HTMLHelper::_('bootstrap.tooltip');

// Load user_profile plugin language
$lang = Factory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('com_users.two-factor-switcher');

?>
<div class="com-users-profile__edit profile-edit">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?= $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<form id="member-profile" action="<?= Route::_('index.php?option=com_users'); ?>" method="post" class="com-users-profile__edit-form form-validate form-horizontal well" enctype="multipart/form-data">
		<?php // Iterate through the form fieldsets and display each one. ?>
		<?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
			<?php $fields = $this->form->getFieldset($group); ?>
			<?php if (count($fields)) : ?>
				<div class="com-users-profile-fieldset-container--<?= $group ?>">
					<?php if (isset($fieldset->label)) : ?>
						<h2>
							<?= Text::_($fieldset->label); ?>
						</h2>
					<?php endif; ?>
					<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
						<p class="com-users-profile-fieldset-description alert alert-info">
							<?= $this->escape(Text::_($fieldset->description)); ?>
						</p>
					<?php endif; ?>
					<?php foreach ($fields as $field) : ?>
						<?= $field->renderField(); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if (count($this->twofactormethods) > 1) : ?>
			<div class="com-users-profile__twofactor com-users-profile-fieldset-container--twofactor">
				<h2><?= Text::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH'); ?></h2>

				<div class="com-users-profile__twofactor-method control-group">
					<div class="control-label">
						<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
							   title="<?= '<strong>' . Text::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') . '</strong><br>' . Text::_('COM_USERS_PROFILE_TWOFACTOR_DESC'); ?>">
							<?= Text::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
						</label>
					</div>
					<div class="controls">
						<?= HTMLHelper::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange();', 'class' => 'custom-select'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
					</div>
				</div>
				<div id="com_users_twofactor_forms_container" class="com-users-profile__twofactor-form">
					<?php foreach ($this->twofactorform as $form) : ?>
						<?php $class = $form['method'] == $this->otpConfig->method ? '' : ' class="hidden"'; ?>
						<div id="com_users_twofactor_<?= $form['method']; ?>"<?= $class; ?>>
							<?= $form['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="com-users-profile__oteps">
				<h2>
					<?= Text::_('COM_USERS_PROFILE_OTEPS'); ?>
				</h2>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span class="sr-only"><?= Text::_('INFO'); ?></span>
					<?= Text::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
				</div>
				<?php if (empty($this->otpConfig->otep)) : ?>
					<div class="alert alert-warning">
						<span class="icon-exclamation-circle" aria-hidden="true"></span><span class="sr-only"><?= Text::_('WARNING'); ?></span>
						<?= Text::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
					</div>
				<?php else : ?>
					<?php foreach ($this->otpConfig->otep as $otep) : ?>
						<span class="col-md-3">
							<?= substr($otep, 0, 4); ?>-<?= substr($otep, 4, 4); ?>-<?= substr($otep, 8, 4); ?>-<?= substr($otep, 12, 4); ?>
						</span>
					<?php endforeach; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="com-users-profile__edit-submit control-group">
			<div class="controls">
				<button type="submit" class="btn btn-success validate" name="task" value="profile.save">
					<?= Text::_('JSAVE'); ?>
				</button>
				<button type="submit" class="btn btn-danger" name="task" value="profile.cancel" formnovalidate>
					<?= Text::_('JCANCEL'); ?>
				</button>
				<input type="hidden" name="option" value="com_users">
			</div>
		</div>
		<?= HTMLHelper::_('form.token'); ?>
	</form>
</div>
