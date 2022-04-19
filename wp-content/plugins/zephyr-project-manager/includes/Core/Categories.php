<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Utillities;
use Inc\Core\Projects;
use Inc\Base\BaseController;
use Inc\Api\ColorPickerApi;

class Categories {

	/**
	* Creates a new category
	*/
	public static function create( $args ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array();
		$settings['name'] = (isset($args['name'])) ? sanitize_text_field( $args['name']) : 'Untitled';
		$settings['description'] = (isset($args['description'])) ? sanitize_text_field( $args['description']) : '';
		$settings['color'] 	= (isset($args['color'])) ? sanitize_text_field( $args['color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$wpdb->insert( $table_name, $settings );
		return $wpdb->insert_id;
	}

	/**
	* Updates category
	*/
	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		if (isset($args['color'])) {
			if ( ColorPickerApi::checkColor( $args['color'] ) !== false ) {
				$args['color'] = ColorPickerApi::sanitizeColor( $args['color'] );
			} else {
				$args['color'] = '#eee';
			}
		}

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
		return $args;
	}

	/**
	* Deletes a category
	*/
	public static function delete( $id ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array(
			'id' => $id
		);

		$wpdb->delete( $table_name, $settings, [ '%d' ] );
	}

	/**
	* Retrieves all categories from the database
	* @return object
	*/
	public static function fetch() {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$query = "SELECT * FROM $table_name";
		$categories = $wpdb->get_results($query);
		return $categories;
	}

	/**
	* Retrieves all categories
	* @return object
	*/
	public static function get_categories() {
		global $wpdb;
		$manager = ZephyrProjectManager();
		$categories = $manager::get_categories();
		return $categories;
	}

	/**
	* Retrieves the data for a category from the database
	* @param int $id The ID of the category to retrieve the data for
	* @return object
	*/
	public static function fetch_category( $id ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		if (!empty($id)) {
			$query = "SELECT * FROM $table_name WHERE id = $id";
			$category = $wpdb->get_row($query);
		} else {
			$category = null;
		}
		
		return $category;
	}

	/**
	* Retrieves the data for a category from the global manager
	* @param int $id The ID of the category to retrieve the data for
	* @return object
	*/
	public static function get_category( $id ) {
		global $wpdb;
		$manager = ZephyrProjectManager();
		$category = $manager::get_category( $id );
		//$table_name = ZPM_CATEGORY_TABLE;
		// if (!empty($id)) {
		// 	$query = "SELECT * FROM $table_name WHERE id = $id";
		// 	$category = $wpdb->get_row($query);
		// } else {
		// 	$category = null;
		// }
		
		return $category;
	}

	/**
	* Returns the total number of categories
	* @return int
	*/
	public static function get_category_total() {
		$categories = Categories::get_categories();
		$category_count = sizeof($categories);
		return $category_count;
	}

	/**
	* Displays a list of created categories
	*/
	public static function display_category_list() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/category_list.php' );
	}

	public static function new_category_modal() {
		?>
		<!-- New Category modal -->
		<div id="zpm_new_category_modal" class="zpm-modal">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php _e( 'New Category', 'zephyr-project-manager' ); ?></h3>

				<div class="zpm-form__group">
					<input type="text" name="zpm_category_name" id="zpm_category_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>">
					<label for="zpm_category_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				</div>

 				<div class="zpm-form__group">
					<textarea type="text" name="zpm_category_description" id="zpm_category_description" class="zpm-form__field" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"></textarea>
					<label for="zpm_category_description" class="zpm-form__label"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php _e( 'Color', 'zephyr-project-manager' ); ?></label>
				<input type="text" id="zpm_category_color" class="zpm_input">
			</div>
			<button class="zpm_button" name="zpm_create_category" id="zpm_create_category"><?php _e( 'Create Category', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
	}

	public static function new_status_modal() {
		?>
		<!-- New Status modal -->
		<div id="zpm_new_status_modal" class="zpm-modal">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php _e( 'New Priority / Status', 'zephyr-project-manager' ); ?></h3>

				<div class="zpm-form__group">
					<input type="text" name="zpm_status_name" id="zpm_status_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>">
					<label for="zpm_status_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php _e( 'Color', 'zephyr-project-manager' ); ?></label>
				<input type="text" id="zpm_status_color" class="zpm_input zpm-color-picker">
				<input id="zpm-status-type__new" type="hidden">
			</div>
			<button class="zpm_button" name="zpm_create_status" id="zpm_create_status"><?php _e( 'Create Priority', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
	}

	public static function edit_status_modal() {
		?>
		<!-- Edit Status modal -->
		<div id="zpm_edit_status_modal" class="zpm-modal">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php _e( 'Edit Priority / Status', 'zephyr-project-manager' ); ?></h3>

				<input type="hidden" id="zpm-edit-status-id" />
				<div class="zpm-form__group">
					<input type="text" name="zpm_status_name" id="zpm_edit_status_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>">
					<label for="zpm_status_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				</div>

				<label class="zpm_label" for="zpm_category_color"><?php _e( 'Color', 'zephyr-project-manager' ); ?></label>
				<input type="text" id="zpm_edit_status_color" class="zpm_input zpm-color-picker">
				<input id="zpm-status-type__edit" type="hidden">
			</div>
			<button class="zpm_button" name="zpm_create_status" id="zpm_edit_status"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
	}

	public static function card_html( $category ) {
		$color = $category->color;
		$color_dark = Utillities::adjust_brightness( $color, -40 );
		$cat_projects = Projects::category_projects( $category->id, true );
		$filtered_projects = apply_filters( 'zpm_category_projects', $cat_projects );
		$project_count = sizeof( $filtered_projects );
		$base_url = is_admin() ? esc_url( admin_url( '/admin.php?page=zephyr_project_manager_projects' ) ) : Utillities::get_frontend_url() . '?action=projects';
		$base_url .= '&category_id=' . $category->id;
		$url = apply_filters( 'zpm_category_project_url', $base_url );
		
		?>
			<a class="zpm-category__grid-cell zpm-grid__cell" href="<?php echo $url; ?>" data-category-id="<?php echo $category->id; ?>">
				<div class="zpm-card zpm-category-card" style="background: <?php echo $color; ?>; 
					background: -moz-linear-gradient(-45deg, <?php echo $color; ?> 0%, <?php echo $color_dark; ?> 100%); 
					background: -webkit-linear-gradient(-45deg, <?php echo $color; ?> 0%,<?php echo $color_dark; ?> 100%); 
					background: linear-gradient(135deg, <?php echo $color; ?> 0%,<?php echo $color_dark; ?> 100%); 
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $color; ?>', endColorstr='<?php echo $color_dark; ?>',GradientType=1 );">
					<p class="zpm-category-card__name"><?php echo $category->name; ?></p>
					<p class="zpm-category-card__description"><?php echo $category->description; ?></p>
					<span class="zpm-category-card__count"><span class="zpm-category-card__count-value"><?php echo $project_count ?></span> <?php echo sprintf( _n( 'Project', 'Projects', $project_count, 'zephyr-project-manager' ) ); ?></span>
				</div>
			</a>
			
		<?php
	}
}