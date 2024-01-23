<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nggallery_admin_roles() {

	if ( ! empty( $_POST ) ) {

		check_admin_referer( 'ngg_addroles' );

		// now set or remove the capability.
		ngg_set_capability( $_POST['general'], 'NextGEN Gallery overview' );
		ngg_set_capability( $_POST['tinymce'], 'NextGEN Use TinyMCE' );
		ngg_set_capability( $_POST['add_gallery'], 'NextGEN Upload images' );
		ngg_set_capability( $_POST['manage_gallery'], 'NextGEN Manage gallery' );
		ngg_set_capability( $_POST['manage_others'], 'NextGEN Manage others gallery' );
		ngg_set_capability( $_POST['manage_tags'], 'NextGEN Manage tags' );
		ngg_set_capability( $_POST['edit_album'], 'NextGEN Edit album' );
		ngg_set_capability( $_POST['change_style'], 'NextGEN Change style' );
		ngg_set_capability( $_POST['change_options'], 'NextGEN Change options' );
		ngg_set_capability( $_POST['attach_interface'], 'NextGEN Attach Interface' );
	}

	?>
	<div class="wrap">
	<p>
		<?php esc_html_e( 'Select the lowest role which should be able to access the following capabilities. NextGEN Gallery supports the standard roles from WordPress.', 'nggallery' ); ?> <br />
	</p>
		<?php wp_nonce_field( 'ngg_addroles' ); ?>
			<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Main NextGEN Gallery overview', 'nggallery' ); ?>:</th>
				<td><label for="general"><select name="general" id="general"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Gallery overview' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Use TinyMCE Button / Upload tab', 'nggallery' ); ?>:</th>
				<td><label for="tinymce"><select name="tinymce" id="tinymce"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Use TinyMCE' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Add gallery / Upload images', 'nggallery' ); ?>:</th>
				<td><label for="add_gallery"><select name="add_gallery" id="add_gallery"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Upload images' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage gallery', 'nggallery' ); ?>:</th>
				<td><label for="manage_gallery"><select name="manage_gallery" id="manage_gallery"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Manage gallery' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage others gallery', 'nggallery' ); ?>:</th>
				<td><label for="manage_others"><select name="manage_others" id="manage_others"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Manage others gallery' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage tags', 'nggallery' ); ?>:</th>
				<td><label for="manage_tags"><select name="manage_tags" id="manage_tags"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Manage tags' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Edit Album', 'nggallery' ); ?>:</th>
				<td><label for="edit_album"><select name="edit_album" id="edit_album"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Edit album' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Change style', 'nggallery' ); ?>:</th>
				<td><label for="change_style"><select name="change_style" id="change_style"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Change style' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Change options', 'nggallery' ); ?>:</th>
				<td><label for="change_options"><select name="change_options" id="change_options"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Change options' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'NextGEN Attach Interface', 'nggallery' ); ?>:</th>
				<td><label for="attach_interface"><select name="attach_interface" id="attach_interface"><?php wp_dropdown_roles( ngg_get_role( 'NextGEN Attach Interface' ) ); ?></select></label></td>
			</tr>
			</table>
	</div>
	<?php
}

function ngg_get_sorted_roles() {
	// This function returns all roles, sorted by user level (lowest to highest).
	global $wp_roles;
	$roles  = $wp_roles->role_objects;
	$sorted = [];

	if ( class_exists( 'RoleManager' ) ) {
		foreach ( $roles as $role_key => $role_name ) {
			$role = get_role( $role_key );
			if ( empty( $role ) ) {
				continue;
			}
			$role_user_level            = array_reduce( array_keys( $role->capabilities ), [ 'WP_User', 'level_reduction' ], 0 );
			$sorted[ $role_user_level ] = $role;
		}
		$sorted = array_values( $sorted );
	} else {
		$role_order = [ 'subscriber', 'contributor', 'author', 'editor', 'administrator' ];
		foreach ( $role_order as $role_key ) {
			$sorted[ $role_key ] = get_role( $role_key );
		}
	}
	return $sorted;
}

function ngg_get_role( $capability ) {
	// This function return the lowest roles which has the capabilities.
	$check_order = ngg_get_sorted_roles();

	$args = array_slice( func_get_args(), 1 );
	$args = array_merge( [ $capability ], $args );

	foreach ( $check_order as $check_role ) {
		if ( empty( $check_role ) ) {
			return false;
		}

		if ( call_user_func_array( [ &$check_role, 'has_cap' ], $args ) ) {
			return $check_role->name;
		}
	}
	return false;
}

function ngg_set_capability( $lowest_role, $capability ) {
	// This function set or remove the $capability.
	$check_order = ngg_get_sorted_roles();

	$add_capability = false;

	foreach ( $check_order as $the_role ) {
		$role = $the_role->name;

		if ( $lowest_role == $role ) {
			$add_capability = true;
		}

		// If you rename the roles, then please use a role manager plugin.
		if ( empty( $the_role ) ) {
			continue;
		}

		$add_capability ? $the_role->add_cap( $capability ) : $the_role->remove_cap( $capability );
	}
}

?>