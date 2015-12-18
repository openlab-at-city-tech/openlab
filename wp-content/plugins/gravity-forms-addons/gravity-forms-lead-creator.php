<?php

if ( class_exists( 'KWS_GF_Change_Lead_Creator' ) ) {
	return;
}


/**
 * @since 3.6.2
 */
class KWS_GF_Change_Lead_Creator {

	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'load' ) );
	}

	/**
	 * @since  3.6.3
	 * @return void
	 */
	function load() {

		// Does GF exist? Can the user edit entries?
		if ( ! class_exists( 'GFCommon' ) ) {
			return;
		}

		if ( ! GFCommon::current_user_can_any( "gravityforms_edit_entries" ) ) {
			return;
		}

		// If screen mode isn't set, then we're in the wrong place.
		if ( empty( $_REQUEST['screen_mode'] ) ) {
			return;
		}

		// Now, no validation is required in the methods; let's hook in.
		add_action( 'admin_init', array( &$this, 'set_screen_mode' ) );

		add_action( "gform_entry_info", array( &$this, 'add_select' ), 10, 2 );

		add_action( "gform_after_update_entry", array( &$this, 'update_entry_creator' ), 10, 2 );

	}

	/**
	 * Allows for edit links to work with a link instead of a form (GET instead of POST)
	 *
	 * @return [type] [description]
	 */
	function set_screen_mode() {

		if ( ! empty( $_REQUEST["screen_mode"] ) ) {
			$_POST["screen_mode"] = esc_attr( $_REQUEST["screen_mode"] );
		}

	}

	/**
	 * When the entry creator is changed, add a note to the entry
	 *
	 * @param  array $form GF entry array
	 * @param  int $leadid Lead ID
	 *
	 * @return void
	 */
	function update_entry_creator( $form, $leadid ) {
		global $current_user;

		// Update the entry
		$created_by = intval( rgpost( 'created_by' ) );

		RGFormsModel::update_lead_property( $leadid, 'created_by', $created_by );

		// If the creator has changed, let's add a note about who it used to be.
		$originally_created_by = rgpost( 'originally_created_by' );

		if ( $originally_created_by !== $created_by ) {

			$user_data = get_userdata( $current_user->ID );

			$user_format = __( '%s (ID #%d)', 'gravity-view' );

			$original_name = $created_by_name = esc_attr__( 'No User', 'gravity-view' );

			if ( ! empty( $originally_created_by ) ) {
				$originally_created_by_user_data = get_userdata( $originally_created_by );
				$original_name                   = sprintf( $user_format, $originally_created_by_user_data->display_name, $originally_created_by_user_data->ID );
			}

			if ( ! empty( $created_by ) ) {
				$created_by_user_data = get_userdata( $created_by );
				$created_by_name      = sprintf( $user_format, $created_by_user_data->display_name, $created_by_user_data->ID );
			}

			RGFormsModel::add_note( $leadid, $current_user->ID, $user_data->display_name, sprintf( __( 'Changed lead creator from %s to %s', 'gravity-forms-addons' ), $original_name, $created_by_name ) );
		}

	}

	/**
	 * Output the select to change the entry creator
	 *
	 * @param int $form_id GF Form ID
	 * @param array $lead GF lead array
	 *
	 * @return void
	 */
	function add_select( $form_id, $lead ) {

		if ( rgpost( 'screen_mode' ) !== 'edit' ) {
			return;
		}

		/**
		 * There are issues with too many users where it breaks the select. We try to keep it at a reasonable number.
		 *
		 * @link   texthttp://codex.wordpress.org/Function_Reference/get_users
		 * @var  array Settings array
		 */
		$get_users_settings = apply_filters( 'gravityview_change_entry_creator_user_parameters', array( 'number' => 300 ) );

		$users = get_users( $get_users_settings );

		$output = '<label for="change_created_by">';
		$output .= esc_html__( 'Change Entry Creator:', 'gravity-forms-addons' );
		$output .= '</label>
        <select name="created_by" id="change_created_by" class="widefat">';
		$output .= '<option value=""> &mdash; ' . esc_attr__( 'No User', 'gravity-view' ) . ' &mdash; </option>';
		foreach ( $users as $user ) {
			$output .= '<option value="' . $user->ID . '"' . selected( $lead['created_by'], $user->ID, false ) . '>' . esc_attr( $user->display_name . ' (' . $user->user_nicename . ')' ) . '</option>';
		}
		$output .= '</select>';
		$output .= '<input name="originally_created_by" value="' . $lead['created_by'] . '" type="hidden" />';
		echo $output;

	}

}

new KWS_GF_Change_Lead_Creator;