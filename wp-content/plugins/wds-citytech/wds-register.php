<?php
	// Email validation code
function openlab_registration_avatars() {
	global $bp, $wpdb;

	if ( ! bp_is_register_page() ) {
		return;
	}

	if ( empty( $bp->avatar_admin ) ) {
		$bp->avatar_admin = new stdClass();
	}

	$bp->avatar_admin->step = 'upload-image';

	/* If user has uploaded a new avatar */
	if ( ! empty( $_FILES ) ) {

		/* Check the nonce */
		check_admin_referer( 'bp_avatar_upload' );

		$bp->signup->step = 'completed-confirmation';

		if ( is_multisite() ) {
			/* Get the activation key */
			if ( ! $bp->signup->key = $wpdb->get_var( $wpdb->prepare( "SELECT activation_key FROM {$wpdb->signups} WHERE user_login = %s AND user_email = %s", $_POST['signup_username'], $_POST['signup_email'] ) ) ) {
				bp_core_add_message( __( 'There was a problem uploading your avatar, please try uploading it again', 'buddypress' ) );
			} else {
				/* Hash the key to create the upload folder (added security so people don't sniff the activation key) */
				$bp->signup->avatar_dir = wp_hash( $bp->signup->key );
			}
		} else {
			$user_id                = bp_core_get_userid( $_POST['signup_username'] );
			$bp->signup->avatar_dir = wp_hash( $user_id );
		}

		/* Pass the file to the avatar upload handler */
		if ( bp_core_avatar_handle_upload( $_FILES, 'bp_core_signup_avatar_upload_dir' ) ) {
			$bp->avatar_admin->step = 'crop-image';

			/* Make sure we include the jQuery jCrop file for image cropping */
			add_action( 'wp_enqueue_scripts', 'bp_core_add_jquery_cropper' );

			bp_core_load_template( apply_filters( 'bp_core_template_register', 'registration/register' ) );
		}
	}

	/* If the image cropping is done, crop the image and save a full/thumb version */
	if ( isset( $_POST['avatar-crop-submit'] ) ) {

		/* Check the nonce */
		check_admin_referer( 'bp_avatar_cropstore' );

		/* Reset the avatar step so we can show the upload form again if needed */
		$bp->signup->step       = 'completed-confirmation';
		$bp->avatar_admin->step = 'upload-image';
		$bp->signup->key        = $wpdb->get_var( $wpdb->prepare( "SELECT activation_key FROM {$wpdb->signups} WHERE user_login = %s AND user_email = %s", $_POST['signup_username'], $_POST['signup_email'] ) );
		$bp->signup->avatar_dir = wp_hash( $bp->signup->key );

		if ( ! bp_core_avatar_handle_crop(
			array(
				'original_file' => $_POST['image_src'],
				'crop_x'        => $_POST['x'],
				'crop_y'        => $_POST['y'],
				'crop_w'        => $_POST['w'],
				'crop_h'        => $_POST['h'],
			)
		) ) {
			bp_core_add_message( __( 'There was a problem cropping your avatar, please try uploading it again', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Your new avatar was uploaded successfully', 'buddypress' ) );
		}

		bp_core_load_template( apply_filters( 'bp_core_template_register', 'registration/register' ) );
	}
}
add_action( 'bp_screens', 'openlab_registration_avatars', 9 );

function wds_email_error() {
	echo 'Validate Error!';
	?>
		<div class="email-validate error">
			You must register with a @citytech.cuny.edu e-mail address!
		</div>
	<?php
}

function wds_email_validate() {
	global $bp;

	// Check that the email addresses match
	if ( empty( $_POST['signup_email_confirm'] ) ) {
		$bp->signup->errors['signup_email'] = 'Please confirm your email address.';
	} elseif ( trim( $_POST['signup_email'] ) != trim( $_POST['signup_email_confirm'] ) ) {
		$bp->signup->errors['signup_email'] = 'Email addresses do not match. Please double check and resubmit.';
	}

	// Maybe skip email checks for development.
	if ( defined( 'OPENLAB_SKIP_EMAIL_CHECK' ) && OPENLAB_SKIP_EMAIL_CHECK ) {
		return;
	}

	$email        = $_POST['signup_email'];
	$email_parts  = explode( '@', $email );
	$domain       = isset( $email_parts[1] ) ? stripslashes( $email_parts[1] ): '';
	$account_type = isset( $_POST['openlab-account-type'] ) ? stripslashes( $_POST['openlab-account-type'] ) : 'student';

	switch ( $account_type ) {
		case 'student':
		case 'alumni':
			if ( 'mail.citytech.cuny.edu' !== $domain ) {
				$bp->signup->errors['signup_email'] = 'Students must register with an @mail.citytech.cuny.edu e-mail address!';
			}
			break;

		case 'faculty':
		case 'staff':
			if ( 'citytech.cuny.edu' !== $domain ) {
				$bp->signup->errors['signup_email'] = 'You must register with an @citytech.cuny.edu e-mail address!';
			}

			break;

		case 'non-city-tech':
		default :
			$code = isset( $_POST['signup_validation_code'] ) ? $_POST['signup_validation_code'] : null;
			if ( ! cac_ncs_validate_code( $code ) ) {
				$bp->signup->errors['signup_email'] = 'Non-City Tech addresses need a valid registration code to sign up for the OpenLab.';

			}
			break;
	}
}
add_action( 'bp_signup_validate', 'wds_email_validate' );

function wds_get_register_fields( $account_type, $post_data = array() ) {
	// Fake it until you make it
	if ( ! empty( $post_data ) ) {
		foreach ( $post_data as $pdk => $pdv ) {
			$_POST[ $pdk ] = $pdv;
		}
	}

	$exclude_groups = openlab_get_exclude_groups_for_account_type( $account_type );
	$exclude_fields = array(
		openlab_get_xprofile_field_id( 'Account Type' ),
		openlab_get_xprofile_field_id( 'First Name' ),
		openlab_get_xprofile_field_id( 'Last Name' ),
		openlab_get_xprofile_field_id( 'Major Program of Study' ),
		openlab_get_xprofile_field_id( 'Department' ),
		openlab_get_xprofile_field_id( 'Email address (Student)' ),
	);

	// Legacy: Make sure we exclude all 'Google Scholar' fields.
	global $wpdb;
	$google_scholar_field_ids = $wpdb->get_col( "SELECT id FROM {$wpdb->prefix}bp_xprofile_fields WHERE name LIKE '%Google Scholar%'" );
	if ( ! empty( $google_scholar_field_ids ) ) {
		$exclude_fields = array_merge( $exclude_fields, $google_scholar_field_ids );
	}

	$exclude_fields = array_merge( $exclude_fields, wp_list_pluck( openlab_social_media_fields(), 'field_id' ) );

	$has_profile_args = array(
		'exclude_groups' => $exclude_groups,
		'exclude_fields' => $exclude_fields,
	);

	/* Use the profile field loop to render input fields for the 'base' profile field group */
	$return = '';
	if ( function_exists( 'bp_has_profile' ) ) :
		if ( 'staff' === $account_type || 'faculty' === $account_type ) :
			?>
			<div class="editfield field_name alt form-group">
				<label for="ol-offices"><span class="label-text">School / Office / Department</span> <span class="label-gloss">(required; public)</span></label>
				<?php
				$selector_args = [
					'required' => true,
					'checked'   => false,
				];
				openlab_academic_unit_selector( $selector_args );
				?>
			</div>
		<?php elseif ( 'alumni' === $account_type || 'student' === $account_type ) : ?>
			<?php
			$depts   = [];
			$checked = openlab_get_user_academic_units( 0 );

			$schools = openlab_get_school_list();
			foreach ( $schools as $school => $_ ) {
				$depts += openlab_get_entity_departments( $school );
			}
			?>
			<div class="form-group editfield field_name alt">
				<div class="error-container" id="academic-unit-selector-error"></div>
				<label for="ol-offices"><span class="label-text">Major Program of Study</span> <span class="label-gloss">(required; public)</span></label>
				<select
				  name="departments-dropdown"
				  class="form-control"
				  data-parsley-required
				  data-parsley-required-message="You must provide a Major Program of Study"
				  data-parsley-errors-container="#academic-unit-selector-error">
					<option value="" <?php selected( empty( $checked['departments'] ) ); ?>>----</option>
					<option value="undecided" <?php selected( in_array( 'undecided', $checked['departments'], true ) ); ?>>Undecided</option>
					<?php foreach ( $depts as $dept_value => $dept ) : ?>
						<option value="<?php echo esc_attr( $dept_value ); ?>" <?php selected( in_array( $dept_value, $checked['departments'], true ) ); ?>><?php echo esc_html( $dept['label'] ); ?></option>
					<?php endforeach; ?>
				</select>

				<?php wp_nonce_field( 'openlab_academic_unit_selector_legacy', 'openlab-academic-unit-selector-legacy-nonce', false ); ?>
			</div>
			<?php
		endif;

		$return .= ob_get_clean();

if ( bp_has_profile( $has_profile_args ) ) :
	$return .= '<p>The information below is optional and you can choose who is able to see it.</p>';

	while ( bp_profile_groups() ) :
		bp_the_profile_group();
		while ( bp_profile_fields() ) :
			bp_the_profile_field();

			// Skip legacy social media fields.
			if ( bp_xprofile_get_meta( bp_get_the_profile_field_id(), 'field', 'is_legacy_social_media_field' ) ) {
				continue;
			}

			$return .= '<div class="editfield form-group">';
			if ( 'textbox' == bp_get_the_profile_field_type() ) :
				if ( bp_get_the_profile_field_name() == 'Name' ) {
					$return .= '<label class="control-label" for="' . bp_get_the_profile_field_input_name() . '"><span class="label-text">Display Name</span>';
				} else {
					$return .= '<label class="control-label" for="' . bp_get_the_profile_field_input_name() . '"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				}

				if ( bp_get_the_profile_field_is_required() ) {
					$public_required_textbox_fields = [ 'Name' ];

					if ( bp_get_the_profile_field_name() == 'First Name' || bp_get_the_profile_field_name() == 'Last Name' ) {
						$return .= ' <span class="label-gloss">(required, but not displayed on Public Profile)</span>';
					} elseif ( in_array( bp_get_the_profile_field_name(), $public_required_textbox_fields, true ) ) {
						$return .= ' <span class="label-gloss">(required; public)</span>';
					} else {
						$return .= ' <span class="label-gloss">(required)</span>';
					}
				}
				$return .= '</label>';

				/*
				$input_name = trim(bp_get_the_profile_field_input_name());
				$return.="<br />Input field name: " . $input_name;
				$return.="<br />Post Value: " . $_POST["{$input_name}"];
				$return .= "<br />Post Field 193: " . $_POST['field_193'];
				$input_value = $_POST["{$input_name}"];
				*/

				if ( bp_get_the_profile_field_is_required() ) {

					$this_field = bp_get_the_profile_field_input_name();
					$return    .= "<div id='{$this_field}_confirm_error' class='error-container'></div>";

				}

				$placeholder = '';
				if ( bp_get_the_profile_field_id() === openlab_get_xprofile_field_id( 'Phone' ) ) {
					$placeholder = 'Note: Your phone number will be public.';
				}

				$return .= '<input
						class="form-control"
						type="text"
						name="' . bp_get_the_profile_field_input_name() . '"
						id="' . bp_get_the_profile_field_input_name() . '"
						value="' . bp_get_the_profile_field_edit_value() . '"
						placeholder="' . esc_attr( $placeholder ) . '"
						' . openlab_profile_field_input_attributes() . '
						/>';

				if ( bp_get_the_profile_field_name() == 'Name' ) {
					$return .= '<p class="register-field-note" id="display-name-help-text">' . openlab_get_profile_field_helper_text( 'display_name' ) . '</p>';
				}
				endif;
			if ( 'textarea' == bp_get_the_profile_field_type() ) :
				$return .= '<label for="' . bp_get_the_profile_field_input_name() . '"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return .= '</label>';
				$return .= '<textarea class="form-control" rows="5" cols="40" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '">' . bp_get_the_profile_field_edit_value();
				$return .= '</textarea>';
				endif;
			if ( 'selectbox' == bp_get_the_profile_field_type() ) :
				$return .= '<label class="control-label" for="' . bp_get_the_profile_field_input_name() . '"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return .= '</label>';

				if ( bp_get_the_profile_field_is_required() ) {

					$this_field = bp_get_the_profile_field_input_name();
					$return    .= "<div id='{$this_field}_confirm_error' class='error-container'></div>";

				}

				// WDS ADDED $$$
				$onchange = '';

				$return .= '<select
						class="form-control"
						name="' . bp_get_the_profile_field_input_name() . '"
						id="' . bp_get_the_profile_field_input_name() . '" ' .
				$onchange .
				openlab_profile_field_input_attributes() .
				' >';
				if ( 'Account Type' == bp_get_the_profile_field_name() ) {
					$return .= '<option selected="selected" value=""> ---- </option>';
				}
					 $return .= bp_get_the_profile_field_options();
				$return      .= '</select>';

				endif;
			if ( 'multiselectbox' == bp_get_the_profile_field_type() ) :
				$return .= '<label for="' . bp_get_the_profile_field_input_name() . '"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return     .= '</label>';
				$return     .= '<select class="form-control" name="' . bp_get_the_profile_field_input_name() . '" id="' . bp_get_the_profile_field_input_name() . '" multiple="multiple">';
					$return .= bp_get_the_profile_field_options();
				$return     .= '</select>';
				endif;
			if ( 'radio' == bp_get_the_profile_field_type() ) :
				$return .= '<div class="radio">';
				$return .= '<span class="label"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return .= '</span>';
				$return .= bp_get_the_profile_field_options();
				if ( ! bp_get_the_profile_field_is_required() ) :
					// $return.='<a class="clear-value" href="javascript:clear( \''.bp_get_the_profile_field_input_name().'\' );">'._e( 'Clear', 'buddypress' ).'</a>';
				endif;
				$return .= '</div>';
				endif;
			if ( 'checkbox' == bp_get_the_profile_field_type() ) :
				$return .= '<div class="checkbox">';
				$return .= '<span class="label"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return .= '</span>';
				$return .= bp_get_the_profile_field_options();
				$return .= '</div>';
				endif;
			if ( 'datebox' == bp_get_the_profile_field_type() ) :
				$return .= '<div class="datebox">';
				$return .= '<label for="' . bp_get_the_profile_field_input_name() . '_day"><span class="label-text">' . bp_get_the_profile_field_name() . '</span>';
				if ( bp_get_the_profile_field_is_required() ) :
					$return .= ' <span class="label-gloss">(required)</span>';
				endif;
				$return     .= '</label>';
				$return     .= '<select name="' . bp_get_the_profile_field_input_name() . '_day" id="' . bp_get_the_profile_field_input_name() . '_day">';
					$return .= bp_get_the_profile_field_options( 'type=day' );
				$return     .= '</select>';
				$return     .= '<select name="' . bp_get_the_profile_field_input_name() . '_month" id="' . bp_get_the_profile_field_input_name() . '_month">';
					$return .= bp_get_the_profile_field_options( 'type=month' );
				$return     .= '</select>';
				$return     .= '<select name="' . bp_get_the_profile_field_input_name() . '_year" id="' . bp_get_the_profile_field_input_name() . '_year">';
					$return .= bp_get_the_profile_field_options( 'type=year' );
				$return     .= '</select>';
				$return     .= '</div>';
				endif;
			$return .= '<p class="description">' . bp_get_the_profile_field_description() . '</p>';

			ob_start();
			$visibility_selector = openlab_xprofile_field_visibility_selector();
			$return .= ob_get_clean();

			$return .= '</div>';
					endwhile;

			// Only add for AJAX requests that are member-type-specific.
			if ( 'Base' !== $account_type ) {
				ob_start();
				openlab_social_fields_edit_markup();
				$return .= ob_get_clean();
			}

		/**
 * Left over from WDS, we need to hardcode 3,7,241 in some cases.
	 *
 * @todo Investigate
 */
		$profile_field_ids = bp_get_the_profile_group_field_ids();

		$pfids_a = explode( ',', $profile_field_ids );
		if ( ! in_array( 1, $pfids_a ) ) {
			$pfids_a[]         = 1;
			$profile_field_ids = implode( ',', $pfids_a );
		}

		$return .= '<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="3,7,241,' . $profile_field_ids . '" />';

		endwhile;
endif;
endif;

		return $return;
}

/**
 * Gets the helper text for a given registration/profile field.
 *
 * Centralized here because we build the markup in two different places. This
 * allows us to have a single copy of each string.
 *
 * @param string $field_name
 * @return string
 */
function openlab_get_profile_field_helper_text( $field_name ) {
	switch ( $field_name ) {
		case 'username' :
			return "Please choose your username. You will use your username to sign in, and it will also be displayed in the URL of your public OpenLab member profile. <strong>Because the username is public, we recommend that students do not use their full name. You don't need to use your real name.</strong> You cannot change your username after you sign up.</p>";

		case 'display_name' :
			return "Please choose your Display Name. Your Display Name will appear on your public OpenLab profile and wherever you post on the OpenLab. <strong>Because your Display Name is public, you don't need to use your real name or your full name.</strong> Your Display Name can be changed at any time by editing your profile.";

		case 'portfolio_name' :
			$group_type_label = openlab_get_portfolio_label(
				[
					'case'    => 'upper',
					'user_id' => bp_loggedin_user_id(),
				],
			);

			return sprintf(
				'Depending on the privacy settings you choose, your %s name may be publicly visible, so you may not wish to include your full name. We recommend keeping your %s name under 50 characters. You can change your %s name at any time.',
				$group_type_label,
				$group_type_label,
				$group_type_label
			);

		case 'password' :
			return 'Your password should be at least nine characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).';

		default :
			return '';
	}
}

/**
 * Output registration errors into a JS variable.
 *
 * These error values can then be used to create dynamic error messages for objects inserted
 * into the DOM, as is the case with account-type-specific profile fields.
 */
function openlab_registration_errors_object() {
	if ( ! bp_is_register_page() ) {
		return;
	}

	// Instead of doing a database query to pull up every registration field ID (and thus
	// dynamically build hook names), do the quicker and more terrible loop through
	// existing hooks.
	global $wp_filter;
	$errors = array();
	foreach ( $wp_filter as $filter_name => $callbacks ) {
		// Faster than regex.
		if ( 0 !== strpos( $filter_name, 'bp_' ) ) {
			continue;
		}

		if ( '_errors' !== substr( $filter_name, -7 ) ) {
			continue;
		}

		ob_start();
		do_action( $filter_name );
		$error = ob_get_clean();

		if ( ! empty( $error ) ) {
			preg_match( '/bp_(field_[0-9]+)_errors/', $filter_name, $matches );
			$field_name            = $matches[1];
			$errors[ $field_name ] = $error;
		}
	}

	$error_json = json_encode( $errors );
	echo '<script type="text/javascript">var OpenLab_Registration_Errors = ' . $error_json . '</script>';

	$submitted_visibility_values = [];
	foreach ( $_POST as $key => $value ) {
		$matched = preg_match( '/^field_(\d+)_visibility$/', $key, $matches );
		if ( $matched ) {
			$submitted_visibility_values[ $matches[1] ] = $value;
		}
	}

	echo '<script type="text/javascript">var OpenLab_Submitted_Visibility_Values = ' . json_encode( $submitted_visibility_values ) . '</script>';
}
add_action( 'wp_head', 'openlab_registration_errors_object' );

/**
 * Unset the activation-key current_action, so that BP doesn't auto-activate.
 *
 * See #2081.
 */
function openlab_unload_activation_key() {
	if ( ! bp_is_current_component( 'activate' ) ) {
		return;
	}

	$key = bp_current_action();
	if ( ! $key ) {
		return;
	}

	buddypress()->current_activation_key = $key;
	buddypress()->current_action         = '';
}
add_action( 'bp_init', 'openlab_unload_activation_key' );

/**
 * Saves "meta" data on registration.
 *
 * Includes academic units, member type, social fields.
 */
function openlab_save_meta_data_at_registration( $usermeta ) {
	$to_save = [];
	if ( isset( $_POST['openlab-academic-unit-selector-nonce'] ) ) {
		check_admin_referer( 'openlab_academic_unit_selector', 'openlab-academic-unit-selector-nonce' );
		$to_save = openlab_get_academic_unit_data_from_post();

	} elseif ( isset( $_POST['openlab-academic-unit-selector-legacy-nonce'] ) ) {
		check_admin_referer( 'openlab_academic_unit_selector_legacy', 'openlab-academic-unit-selector-legacy-nonce' );
		$to_save = openlab_get_legacy_academic_unit_data_from_post();
	}

	if ( $to_save ) {
		$usermeta['academic_units'] = $to_save;
	}

	$account_type = isset( $_POST['openlab-account-type'] ) ? $_POST['openlab-account-type'] : 'student';
	if ( ! openlab_get_member_type_object( $account_type ) ) {
		$account_type = 'student';
	}

	$usermeta['account_type'] = $account_type;

	$usermeta['social-links'] = isset( $_POST['social-links'] ) ? wp_unslash( $_POST['social-links'] ) : [];

	return $usermeta;
}
add_filter( 'bp_signup_usermeta', 'openlab_save_meta_data_at_registration' );

/**
 * Processes academic unit data on account activation.
 */
function openlab_process_academic_unit_data_at_activation( $user_id, $key, $data ) {
	if ( ! isset( $data['meta']['academic_units'] ) ) {
		return;
	}

	openlab_set_user_academic_units( $user_id, $data['meta']['academic_units'] );
}
add_action( 'bp_core_activated_user', 'openlab_process_academic_unit_data_at_activation', 10, 3 );

/**
 * Processes member type on account activation.
 */
function openlab_process_member_type_at_activation( $user_id, $key, $data ) {
	if ( ! isset( $data['meta']['account_type'] ) ) {
		return;
	}

	$member_type = openlab_get_member_type_object( $data['meta']['account_type'] );
	if ( $member_type ) {
		bp_set_member_type( $user_id, $member_type->name );
	}
}
add_action( 'bp_core_activated_user', 'openlab_process_member_type_at_activation', 10, 3 );

/**
 * Processes social links on account activation.
 */
function openlab_process_social_links_at_activation( $user_id, $key, $data ) {
	if ( ! isset( $data['meta']['social-links'] ) ) {
		return;
	}

	$all_fields = openlab_social_media_fields();

	foreach ( $data['meta']['social-links'] as $social_link ) {
		openlab_set_social_media_field_for_user( $user_id, $social_link['service'], $social_link['url'] );

		$field_id   = $all_fields[ $social_link['service'] ]['field_id'];
		$visibility = isset( $social_link['visibility'] ) ? $social_link['visibility'] : 'public';
		xprofile_set_field_visibility_level( $field_id, $user_id, $visibility );
	}
}
add_action( 'bp_core_activated_user', 'openlab_process_social_links_at_activation', 10, 3 );

/**
 * Send "Office of the Provost" group invites to "Faculty" and "Staff" members.
 *
 * @param int $user_id
 * @return void
 */
function openlab_user_activated_send_group_invites( $user_id ) {
	$group_id      = 22629;
	$account_types = [ 'faculty', 'staff' ];
	$account_type  = openlab_get_user_member_type( $user_id );

	if ( ! in_array( $account_type, $account_types ) ) {
		return;
	}

	// Set group admin as 'inviter'.
	$admins     = groups_get_group_admins( $group_id );
	$inviter_id = isset( $admins[0] ) ? $admins[0]->user_id : 0;

	groups_invite_user(
		[
			'user_id'    => $user_id,
			'group_id'   => $group_id,
			'inviter_id' => $inviter_id,
		]
	);

	groups_send_invites( $inviter_id, $group_id );
}
add_action( 'bp_core_activated_user', 'openlab_user_activated_send_group_invites', 11 );

/**
 * Add a specific class to server-side validation errors.
 *
 * This allows us to remove them easily using client-side validation.
 */
add_filter(
	'bp_members_signup_error_message',
	function( $message ) {
		return str_replace( 'class="error"', 'class="error submitted-form-validation-error"', $message );
	}
);
