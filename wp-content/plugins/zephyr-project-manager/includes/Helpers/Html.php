<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Helpers;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Core\Members;
use Inc\Core\Projects;
use Inc\ZephyrProjectManager;

class Html {

	public static function inputField($label = '', $value = '', $id = '', $classes = '', $atts = []) {
		ob_start();
		?>

		<div class="zpm-form__group">
			<input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="zpm-form__field" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>" <?php foreach($atts as $key => $att) { echo $key . '="' . $att . '"'; } ?> />
			<label for="<?php echo $id; ?>" class="zpm-form__label"><?php echo $label; ?></label>
		</div>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function textarea($label = '', $value = '', $id = '', $classes = '', $atts = []) {
		ob_start();
		?>

		<div class="zpm-form__group">
			<textarea type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="zpm-form__field" placeholder="<?php echo $label; ?>" <?php foreach($atts as $key => $att) { echo $key . '="' . $att . '"'; } ?>><?php echo $value; ?></textarea>
			<label for="<?php echo $id; ?>" class="zpm-form__label"><?php echo $label; ?></label>
		</div>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function hiddenField($value = '', $id = '', $classes = '', $atts = []) {
		ob_start();
		?>

		<div class="zpm-form__group">
			<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" <?php foreach($atts as $key => $att) { echo $key . '="' . $att . '"'; } ?> />
		</div>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function selectField($label = '', $val = '', $multiple = false, $options = [], $atts = [], $id = '', $classes = '') {
		ob_start();
		?>

		<div class="zpm-select__container">
			<label class="zpm_label" for="<?php echo $id; ?>"><?php echo $label; ?></label>
			<select id="<?php echo $id; ?>" <?php echo $multiple ? 'multiple' : ''; ?> data-placeholder="<?php echo $label; ?>" class="zpm-select <?php echo $classes; ?> <?php echo $multiple ? 'zpm-multi-select' : ''; ?>" <?php foreach($atts as $key => $att) { echo $key . '="' . $att . '"'; } ?>>
				<?php foreach ($options as $key => $value) : ?>
					<option value="<?php echo $key; ?>" <?php echo $key == $val ? 'selected' : ''; ?> <?php echo is_array($val) && in_array($key, $val) ? 'selected' : ''; ?> ><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function label($value = '', $for = '') {
		ob_start();
		?>
		<label class="zpm_label" for="<?php echo $for; ?>"><?php echo $value; ?></label>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function memberSelectField( $args = [] ) {
		$defaults = [
			'id' => 'zpm-member-select',
			'name' => 'member-select',
			'title' => 'Assignee',
			'value' => []
		];
		$args = wp_parse_args( $args, $defaults );
		ob_start();
		?>
		<?php
			$members = Members::get_zephyr_members();
			$options = [];

			foreach ($members as $member) {
				$options[$member['id']] = $member['name'];
			}

			$atts = [
				'name' => $args['name']
			];
		?>
		<?php echo Html::selectField( $args['title'], $args['value'], true, $options, $atts, $args['id'] ); ?>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function projectSelectField( $args = [] ) {
		$defaults = [
			'id' => 'zpm-member-select',
			'name' => 'member-select',
			'title' => 'Project',
			'value' => ''
		];

		$args = wp_parse_args( $args, $defaults );
		ob_start();
		?>

		<?php
			$manager = ZephyrProjectManager::get_instance();
			$projects = $manager::get_projects();
			$options = [];
			$options['-1'] = __( 'None', 'zephyr-project-manager' );

			foreach ($projects as $project) {
				if (Projects::has_project_access($project)) {
					$options[$project->id] = $project->name;
				}
			}

			$atts = [
				'name' => $args['name']
			];
		?>
		<?php echo Html::selectField( $args['title'], $args['value'], false, $options, $atts, $args['id'] ); ?>

		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function teamSelectField( $args = [] ) {
		$defaults = [
			'id' => 'zpm-team-select',
			'name' => 'team-select',
			'title' => 'Team',
			'value' => ''
		];

		$args = wp_parse_args( $args, $defaults );
		ob_start();
		?>
		<?php
			$teams = Members::get_teams();
			$options = [];
			$options['-1'] = __( 'None', 'zephyr-project-manager' );

			foreach ($teams as $team) {
				$options[$team['id']] = $team['name'];
			}

			$atts = [
				'name' => $args['name']
			];
		?>
		<?php echo Html::selectField( $args['title'], $args['value'], false, $options, $atts, $args['id'] ); ?>

		<?php
		$html = ob_get_clean();
		return $html;
	}
}