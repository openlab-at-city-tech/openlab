<?php
/**
 * NextGEN Gallery Roles Management
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nggallery_admin_roles() {

	if ( ! empty( $_POST ) ) {

		check_admin_referer( 'ngg_addroles' );

		// now set or remove the capability.
		ngg_set_capability( isset( $_POST['general'] ) ? sanitize_text_field( wp_unslash( $_POST['general'] ) ) : '', 'NextGEN Gallery overview' );
		ngg_set_capability( isset( $_POST['tinymce'] ) ? sanitize_text_field( wp_unslash( $_POST['tinymce'] ) ) : '', 'NextGEN Use TinyMCE' );
		ngg_set_capability( isset( $_POST['add_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['add_gallery'] ) ) : '', 'NextGEN Upload images' );
		ngg_set_capability( isset( $_POST['manage_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['manage_gallery'] ) ) : '', 'NextGEN Manage gallery' );
		ngg_set_capability( isset( $_POST['manage_others'] ) ? sanitize_text_field( wp_unslash( $_POST['manage_others'] ) ) : '', 'NextGEN Manage others gallery' );
		ngg_set_capability( isset( $_POST['manage_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['manage_tags'] ) ) : '', 'NextGEN Manage tags' );
		ngg_set_capability( isset( $_POST['edit_album'] ) ? sanitize_text_field( wp_unslash( $_POST['edit_album'] ) ) : '', 'NextGEN Edit album' );
		ngg_set_capability( isset( $_POST['change_style'] ) ? sanitize_text_field( wp_unslash( $_POST['change_style'] ) ) : '', 'NextGEN Change style' );
		ngg_set_capability( isset( $_POST['change_options'] ) ? sanitize_text_field( wp_unslash( $_POST['change_options'] ) ) : '', 'NextGEN Change options' );
		ngg_set_capability( isset( $_POST['attach_interface'] ) ? sanitize_text_field( wp_unslash( $_POST['attach_interface'] ) ) : '', 'NextGEN Attach Interface' );
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
				<td><label for="general"><select name="general" id="general"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Gallery overview' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Use TinyMCE Button / Upload tab', 'nggallery' ); ?>:</th>
				<td><label for="tinymce"><select name="tinymce" id="tinymce"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Use TinyMCE' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Add gallery / Upload images', 'nggallery' ); ?>:</th>
				<td><label for="add_gallery"><select name="add_gallery" id="add_gallery"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Upload images' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage gallery', 'nggallery' ); ?>:</th>
				<td><label for="manage_gallery"><select name="manage_gallery" id="manage_gallery"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Manage gallery' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage others gallery', 'nggallery' ); ?>:</th>
				<td><label for="manage_others"><select name="manage_others" id="manage_others"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Manage others gallery' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Manage tags', 'nggallery' ); ?>:</th>
				<td><label for="manage_tags"><select name="manage_tags" id="manage_tags"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Manage tags' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Edit Album', 'nggallery' ); ?>:</th>
				<td><label for="edit_album"><select name="edit_album" id="edit_album"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Edit album' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Change style', 'nggallery' ); ?>:</th>
				<td><label for="change_style"><select name="change_style" id="change_style"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Change style' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Change options', 'nggallery' ); ?>:</th>
				<td><label for="change_options"><select name="change_options" id="change_options"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Change options' ) ); ?></select></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'NextGEN Attach Interface', 'nggallery' ); ?>:</th>
				<td><label for="attach_interface"><select name="attach_interface" id="attach_interface"><?php ngg_dropdown_roles( ngg_get_role( 'NextGEN Attach Interface' ) ); ?></select></label></td>
			</tr>
			</table>
	</div>
	<?php
}

function ngg_get_sorted_roles() {
	// This function returns the standard roles present on this site, lowest to highest.
	$sorted = [];

	foreach ( [ 'subscriber', 'contributor', 'author', 'editor', 'administrator' ] as $role_key ) {
		$role = get_role( $role_key );

		// A site can delete a standard role. Leaving a null in place here would break
		// every caller, so it is left out of the order entirely.
		if ( $role instanceof WP_Role ) {
			$sorted[ $role_key ] = $role;
		}
	}

	return $sorted;
}

function ngg_dropdown_roles( $selected = '' ) {
	// Only the roles a capability can actually be assigned from are offered. Anything
	// else has no position in the order, so it could never be applied.
	$role_names = wp_roles()->roles;

	// No role holds this capability. Without a placeholder the browser would select the
	// first option on its own, so simply saving the page would hand the capability to the
	// lowest role on the list. The empty value is refused on submit, leaving it unset.
	if ( empty( $selected ) ) {
		printf(
			"\n\t<option selected='selected' value=''>%s</option>",
			esc_html__( 'Not set', 'nggallery' )
		);
	}

	foreach ( ngg_get_sorted_roles() as $role_key => $role ) {
		printf(
			"\n\t<option %s value='%s'>%s</option>",
			selected( $selected, $role_key, false ),
			esc_attr( $role_key ),
			esc_html( translate_user_role( $role_names[ $role_key ]['name'] ) )
		);
	}
}

function ngg_get_role( $capability ) {
	// This function return the lowest roles which has the capabilities.
	foreach ( ngg_get_sorted_roles() as $role_key => $check_role ) {
		if ( $check_role->has_cap( $capability ) ) {
			return $role_key;
		}
	}

	return false;
}

function ngg_set_capability( $lowest_role, $capability ) {
	// This function set or remove the $capability.
	$check_order = ngg_get_sorted_roles();

	// Without a position in the order there is nothing to assign from, and walking
	// anyway would strip the capability from every role including administrator.
	if ( ! isset( $check_order[ $lowest_role ] ) ) {
		return;
	}

	$add_capability = false;

	foreach ( $check_order as $role_key => $the_role ) {
		if ( $lowest_role === $role_key ) {
			$add_capability = true;
		}

		$add_capability ? $the_role->add_cap( $capability ) : $the_role->remove_cap( $capability );
	}
}

?>