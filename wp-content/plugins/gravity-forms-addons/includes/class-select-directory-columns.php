<?php

// For backwards compatibility, load WordPress if it hasn't been loaded yet
// Will be used if this file is being called directly
if ( ! class_exists( 'RGForms' ) ) {
	for ( $i = 0; $i < $depth = 10; $i ++ ) {
		$wp_root_path = str_repeat( '../', $i );

		if ( file_exists( "{$wp_root_path}wp-load.php" ) ) {
			require_once( "{$wp_root_path}wp-load.php" );
			require_once( "{$wp_root_path}wp-admin/includes/admin.php" );
			break;
		}
	}

	auth_redirect();
}

class GFDirectorySelectColumns {

	public function __construct() {
		self::select_columns_page();
	}


	public static function select_columns_page() {

		$form_id = intval( $_GET['id'] );
		if ( empty( $form_id ) ) {
			echo esc_html__( 'Oops! We could not locate your form. Please try again.', 'gravity-forms-addons' );
			exit;
		}

		//reading form metadata
		$form = RGFormsModel::get_form_meta( $form_id );

		$columns   = GFDirectory_shortcode::get_grid_columns( $form_id );
		$field_ids = array_keys( $columns );
		$form = RGFormsModel::get_form_meta( $form_id );
		array_push(
			$form['fields'],
			array(
				'id' => 'id',
				'label' => __(
					'Entry Id',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'date_created',
				'label' => __(
					'Entry Date',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'ip',
				'label' => __(
					'User IP',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'source_url',
				'label' => __(
					'Source Url',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'payment_status',
				'label' => __(
					'Payment Status',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'transaction_id',
				'label' => __(
					'Transaction Id',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'payment_amount',
				'label' => __(
					'Payment Amount',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'payment_date',
				'label' => __(
					'Payment Date',
					'gravity-forms-addons'
				),
			)
		);
		array_push(
			$form['fields'],
			array(
				'id' => 'created_by',
				'label' => __(
					'User',
					'gravity-forms-addons'
				),
			)
		);

		$form = self::get_selectable_entry_meta( $form );
		$form = GFFormsModel::convert_field_objects( $form );

		include_once( GF_DIRECTORY_PATH . 'includes/views/html-select-column-page.php' );
	}

	public static function get_selectable_entry_meta( $form ) {
		$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
		$keys       = array_keys( $entry_meta );
		foreach ( $keys as $key ) {
			array_push(
				$form['fields'],
				array(
					'id' => $key,
					'label' => $entry_meta[ $key ]['label'],
				)
			);
		}

		return $form;
	}

}

new GFDirectorySelectColumns();
