<?php
/**
 * BP Avatar on Registration
 *
 * @wordpress-plugin
 * Plugin Name: BP Avatar on Registration
 * Description: Allows new users to upload an avatar on the same page as registration.
 * Version:     0.1.0
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

function bpar_init_plugin() {
	add_action( 'bp_enqueue_scripts', 'bpar_enqueue_scripts', 1 );
	add_action( 'bp_after_signup_profile_fields', 'bpar_add_uuid_field_to_signup_form' );

	add_action( 'bp_after_register_page', 'bpar_avatar_upload' );
	add_action( 'wp_ajax_nopriv_bp_avatar_upload', 'bp_avatar_ajax_upload' );
	add_action( 'wp_ajax_nopriv_bp_avatar_set', 'bp_avatar_ajax_set' );

	add_action( 'bp_core_activated_user', 'bpar_move_avatars', 10, 3 );

	add_filter( 'bp_avatar_upload_prefilter', 'bpar_validate_upload', 100 );
	add_filter( 'bp_core_fetch_avatar_url', 'bpar_filter_avatar_preview_url', 10, 2 );
	add_filter( 'bp_core_avatar_ajax_upload_params', 'bpar_set_avatar_dir_callback' );
	add_filter( 'bp_attachment_avatar_params', 'bpar_force_bp_script_params' );
	add_filter( 'bp_signup_usermeta', 'bpar_add_uuid_to_signup_meta' );
}
add_action( 'bp_include', 'bpar_init_plugin' );

function bpar_avatar_upload() {
	?>

	<div class="editfield avatar-upload register-avatar-upload" id="register-avatar-upload" style="display:none;">

		<fieldset>
			<legend>Profile Picture</legend>

			<?php do_action( 'bp_before_profile_avatar_upload_content' ) ?>

			<?php /* BP's removeLegacyUI method removes paragraph elements, so we use divs */ ?>
			<div class="avatar-upload-form" id="avatar-upload-form">
				<div class="avatar-preview-column">
					<div class="avatar-preview-outside-wrap">
						<div class="avatar-preview-wrap">
							<img id="avatar-preview" src="<?php echo esc_url( openlab_get_default_avatar_uri() ); ?>" alt="" />
						</div>
					</div>

					<a id="remove-avatar-link" href="#" class="remove-avatar-link">Remove Profile Photo</a>
				</div>

				<div class="avatar-upload-actions">
					<div>Upload an avatar to be used on your profile and throughout the site. Or, you can use the avatar shown here instead. You can change your avatar at any time from your profile.</div>

					<?php bp_attachments_get_template_part( 'avatars/index' ); ?>
				</div>
			</div>
		</fieldset>
	</div>

	<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

		<h5><?php _e( 'Crop Your New Profile Photo', 'buddypress' ) ?></h5>

		<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Profile photo to crop', 'buddypress' ) ?>" />

		<!-- This is what desparation looks like -->
		<div style="position: relative; overflow:hidden !important, width: 150px; height: 150px">
		<div id="avatar-crop-pane">
			<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Profile photo preview', 'buddypress' ) ?>" />
		</div>

		<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

		<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />
		<input type="hidden" name="signup-uuid" value="<?php echo esc_attr( bpar_signup_uuid() ); ?>" />

		<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

	<?php endif; ?>

	<?php do_action( 'bp_after_profile_avatar_upload_content' );
}

function bpar_enqueue_scripts() {
	if ( ! bp_is_register_page() ) {
		return;
	}

	$scripts = array( 'bp-plupload', 'bp-avatar', 'bp-webcam' );
	foreach ( $scripts as $id => $script ) {
		wp_enqueue_script( $id );
	}

	// Enqueue the Attachments scripts for the Avatar UI.
	bp_attachments_enqueue_scripts( 'BP_Attachment_Avatar' );

	// Add Some actions for Theme backcompat.
	add_action( 'bp_after_profile_avatar_upload_content', 'bp_avatar_template_check' );
	add_action( 'bp_after_group_admin_content',           'bp_avatar_template_check' );
	add_action( 'bp_after_group_avatar_creation_step',    'bp_avatar_template_check' );

	wp_enqueue_script( 'bp-avatar-on-register', plugin_dir_url( __FILE__ ) . 'bp-avatar-on-register.js', array( 'bp-avatar' ), null, true );
	wp_enqueue_style( 'jcrop' );
}

/**
 * Ensure that the bp_params variable is non-empty, to avoid script errors.
 */
function bpar_force_bp_script_params( $params ) {
	if ( ! bp_is_register_page() ) {
		return $params;
	}

	$params['nonces'] = array(
		'set' => wp_create_nonce( 'bp_avatar_cropstore' ),
	);
	$params['is_registration'] = true;
	$params['object'] = 'signup';
	$params['item_id'] = bpar_signup_uuid();
	$params['has_avatar'] = false;

	return $params;
}

/**
 * Add the avatar_dir param to the avatar upload handler.
 *
 * This needs to happen early enough that the uploader doesn't bail.
 *
 * @param array $params
 */
function bpar_set_avatar_dir_callback( $params ) {
	if ( ! bp_is_register_page() ) {
		return $params;
	}

	$params['upload_dir_filter'] = 'bpar_avatar_upload_dir';
	return $params;
}

/**
 * Whitelist avatar upload for anon users on the register page.
 */
function bpar_force_edit_avatar_cap_on_register( $can, $capability, $args ) {
	if ( bp_is_register_page() && 'edit_avatar' === $capability && isset( $args['object'] ) && 'signup' === $args['object'] ) {
		$can = true;
	}
	return $can;
}
add_filter( 'bp_attachments_current_user_can', 'bpar_force_edit_avatar_cap_on_register', 10, 3 );

/**
 * Generate a UUID for a signup.
 *
 * Can be called multiple times on a single pageload with the same result.
 */
function bpar_signup_uuid() {
	static $uuid;

	if ( ! isset( $uuid ) ) {
		$uuid = wp_rand();
	}

	return $uuid;
}

/**
 * upload_dir callback.
 *
 * Points uploads at the signup-avatars/[uuid] directory.
 */
function bpar_avatar_upload_dir( $uuid = null ) {
	if ( null === $uuid ) {
		$uuid = intval( $_POST['bp_params']['item_id'] );
	}
	$params = bp_members_avatar_upload_dir( 'signup-avatars', $uuid );
	return $params;
}

/**
 * Adds the signup-uuid hidden variable to the metadata stored with signups.
 */
function bpar_add_uuid_to_signup_meta( $meta ) {
	if ( ! isset( $_POST['signup-uuid'] ) ) {
		return $meta;
	}

	$meta['signup-uuid'] = wp_unslash( $_POST['signup-uuid'] );
	return $meta;
}

/**
 * Outputs the signup-uuid field on the signup form.
 */
function bpar_add_uuid_field_to_signup_form() {
	printf(
		'<input type="hidden" name="signup-uuid" value="%s" />',
		esc_attr( bpar_signup_uuid() )
	);
}

/**
 * 'bp_core_activated_user' callback that moves the signup-avatars to the permanent path.
 */
function bpar_move_avatars( $user_id, $key, $signup_data ) {
	if ( empty( $signup_data['meta']['signup-uuid'] ) ) {
		return;
	}

	$uuid = (int) $signup_data['meta']['signup-uuid'];
	$signup_dir = bpar_avatar_upload_dir( $uuid );

	$xprofile_dir = bp_members_avatar_upload_dir( 'avatars', $user_id );

	rename( $signup_dir['path'], $xprofile_dir['path'] );
}

/**
 * Filters the URL returned by bp_core_fetch_avatar() to point to signup-avatars.
 */
function bpar_filter_avatar_preview_url( $url, $params ) {
	if ( ! isset( $params['object'] ) || 'signup' !== $params['object'] ) {
		return $url;
	}

	$params['avatar_dir'] = 'signup-avatars';

	remove_filter( 'bp_core_fetch_avatar_url', 'bpar_filter_avatar_preview_url', 10, 2 );
	$url = bp_core_fetch_avatar( $params );
	add_filter( 'bp_core_fetch_avatar_url', 'bpar_filter_avatar_preview_url', 10, 2 );

	return $url;
}

function bpar_validate_upload( $upload ) {
	$tmp_name = $upload['tmp_name'];
	$finfo = finfo_open( FILEINFO_MIME_TYPE );
	$real_mime = finfo_file( $finfo, $tmp_name );
	finfo_close( $finfo );

	$file_is_ok = true;
	switch ( $real_mime ) {
		case 'image/gif' :
			$gif = imagecreatefromgif( $tmp_name );
			if ( empty( $gif ) ) {
				$file_is_ok = false;
			} else {
				$new_tmp_name = wp_tempnam( wp_rand() );
				$copied = imagegif( $gif, $new_tmp_name, 100 );
				$upload['tmp_name'] = $new_tmp_name;
				$upload['size'] = filesize($new_tmp_name );
			}
		break;

		case 'image/jpeg' :
			$jpg = imagecreatefromjpeg( $tmp_name );

			if ( empty( $jpg ) ) {
				$file_is_ok = false;
			} else {
				$exif = exif_read_data( $tmp_name );
				if ( ! empty( $exif['Orientation'] ) ) {
					$jpg = imagecreatefromjpeg( $tmp_name );
					$jpg = bpar_apply_orientation( $jpg, $exif['Orientation'] );
				}

				$new_tmp_name = wp_tempnam( wp_rand() );
				$copied = imagejpeg( $jpg, $new_tmp_name, 100 );
				$upload['tmp_name'] = $new_tmp_name;
				$upload['size'] = filesize($new_tmp_name );
			}
		break;
	}

	if ( ! $file_is_ok ) {
		$upload['error'] = 5;
	}

	return $upload;
}

function bpar_apply_orientation( $image, $orientation ) {
	switch ( $orientation ) {
		case 3:
			$image = imagerotate( $image, 180, 0 );
			break;
		case 6:
			$image = imagerotate( $image, -90, 0 );
			break;
		case 8:
			$image = imagerotate( $image, 90, 0 );
			break;
	}

	return $image;
}
