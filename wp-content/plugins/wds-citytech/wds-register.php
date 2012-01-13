<?php 
	//Email validation code


function openlab_registration_avatars() {
	global $bp, $wpdb;
	
	$bp->avatar_admin->step = 'upload-image';

	/* If user has uploaded a new avatar */
	if ( !empty( $_FILES ) ) {

		/* Check the nonce */
		check_admin_referer( 'bp_avatar_upload' );

		$bp->signup->step = 'completed-confirmation';

		if ( is_multisite() ) {
			/* Get the activation key */
			if ( !$bp->signup->key = $wpdb->get_var( $wpdb->prepare( "SELECT activation_key FROM {$wpdb->signups} WHERE user_login = %s AND user_email = %s", $_POST[ 'signup_username' ], $_POST[ 'signup_email' ] ) ) ) {
				bp_core_add_message( __( 'There was a problem uploading your avatar, please try uploading it again', 'buddypress' ) );
			} else {
				/* Hash the key to create the upload folder (added security so people don't sniff the activation key) */
				$bp->signup->avatar_dir = wp_hash( $bp->signup->key );
			}
		} else {
			$user_id = bp_core_get_userid( $_POST['signup_username'] );
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
		$bp->signup->step = 'completed-confirmation';
		$bp->avatar_admin->step = 'upload-image';

		if ( !bp_core_avatar_handle_crop( array( 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
			bp_core_add_message( __( 'There was a problem cropping your avatar, please try uploading it again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Your new avatar was uploaded successfully', 'buddypress' ) );
					
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
		
		// For development
		if ( defined( 'OPENLAB_SKIP_EMAIL_CHECK' ) && OPENLAB_SKIP_EMAIL_CHECK ) {
			return;
		}

//print_r($_POST);
		$email = $_POST['signup_email'];
		/*$email2 = $email;
		$email = explode ( '.', $email );
		$email2 = explode ( '@', $email[0] );
		$domains = array_merge ($email, $email2);*/
		//$error = 1;

		//print_r ($domains);
		//Students
		if($_POST['field_7']=="Student"){
			//if ( in_array('mail', $domains) && in_array('citytech', $domains) && in_array('cuny', $domains) && in_array('edu', $domains) && $_POST['field_7']=="Student") {
			//}else{
				$pos = strrpos($email, "@mail.citytech.cuny.edu");
				if ($pos === false) { 
    				$bp->signup->errors['signup_email'] = 'Students must register with an @mail.citytech.cuny.edu e-mail address!';
				}
				
			//}
		//}elseif ( in_array('citytech', $domains) && in_array('cuny', $domains) && in_array('edu', $domains) ) {
		
		}else{
			$pos = strrpos($email, "@citytech.cuny.edu");
			if ($pos === false) { 
				$bp->signup->errors['signup_email'] = 'You must register with an @citytech.cuny.edu e-mail address!';
			}
		}
		
		
		//check if privacy policy is checked
		
		/*$bp_tos_agree = $_POST['signup_email'];
		if($bp_tos_agree!="1"){
			$bp->signup->errors['bp_tos_agree'] = 'Please aggree to the terms of service!';
		
		}*/
		/*if ( in_array('citytech', $domains) && in_array('cuny', $domains) && in_array('edu', $domains) ) {
			echo 'No error!';
			$error = 0;
		}elseif ( in_array('mail', $domains) && in_array('citytech', $domains) && in_array('cuny', $domains) && in_array('edu', $domains) && $_POST['field_7']=="Student") {
			echo 'No error!';
			$error = 0;
			
		}*/

		//if ( $error == 1) $bp->signup->errors['signup_email'] = 'You must register with an @citytech.cuny.edu e-mail address!';
	}
//
//   to temporarily disable this email edit, just comment out the line below
//
	add_action( 'bp_signup_validate', 'wds_email_validate' );
//
	function wds_get_register_fields($group_id=1){
		/* Use the profile field loop to render input fields for the 'base' profile field group */
		$return="";
		if ( function_exists( 'bp_has_profile' ) ) :
		if ( bp_has_profile( 'profile_group_id='.$group_id ) ) : while ( bp_profile_groups() ) : bp_the_profile_group();
			while ( bp_profile_fields() ) : bp_the_profile_field();
				
				$return.='<div class="editfield">';
				if ( 'textbox' == bp_get_the_profile_field_type() ) :
					if(bp_get_the_profile_field_name()=="Name"){
						$return.='<label for="'.bp_get_the_profile_field_input_name().'">Display Name';
					}else{
						$return.='<label for="'.bp_get_the_profile_field_input_name().'">'.bp_get_the_profile_field_name(); 
					}
					if ( bp_get_the_profile_field_is_required() ) {
						if (bp_get_the_profile_field_name()=="First Name" || bp_get_the_profile_field_name()=="Last Name") {
							$return.=' (required, but not displayed on Public Profile)';
						} else {
							$return.=' (required)';
						}
					}
					$return.='</label>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					/*
					$input_name = trim(bp_get_the_profile_field_input_name());
					$return.="<br />Input field name: " . $input_name;
					$return.="<br />Post Value: " . $_POST["{$input_name}"];
					$return .= "<br />Post Field 193: " . $_POST['field_193'];
					$input_value = $_POST["{$input_name}"];
					*/
					$return.='<input type="text" name="'.bp_get_the_profile_field_input_name().'" id="'.bp_get_the_profile_field_input_name().'" value="'.bp_get_the_profile_field_edit_value().'" />';	
				endif; 
				if ( 'textarea' == bp_get_the_profile_field_type() ) :
					$return.='<label for="'.bp_get_the_profile_field_input_name().'">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) : 
						$return.=' (required)';
					endif;
					$return.='</label>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					$return.='<textarea rows="5" cols="40" name="'.bp_get_the_profile_field_input_name().'" id="'.bp_get_the_profile_field_input_name().'">'.bp_get_the_profile_field_edit_value();
					$return.='</textarea>';
				endif;
				if ( 'selectbox' == bp_get_the_profile_field_type() ) :
					$return.='<label for="'.bp_get_the_profile_field_input_name().'">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) :
						$return.=' (required)';
					endif;
					$return.='</label>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					//WDS ADDED $$$ 
					
					if(bp_get_the_profile_field_name()=="Account Type"){
						$onchange=' onchange="wds_load_account_type(\''.bp_get_the_profile_field_input_name().'\',\'\');"';
					}else{
						$onchange="";	
					}
					$return.='<select name="'.bp_get_the_profile_field_input_name().'" id="'.bp_get_the_profile_field_input_name().'" '.$onchange.'>';
						 $return.=bp_get_the_profile_field_options();
					$return.='</select>';
				
				endif;
				if ( 'multiselectbox' == bp_get_the_profile_field_type() ) :
					$return.='<label for="'.bp_get_the_profile_field_input_name().'">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) :
						$return.=' (required)';
					endif;
					$return.='</label>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					$return.='<select name="'.bp_get_the_profile_field_input_name().'" id="'.bp_get_the_profile_field_input_name().'" multiple="multiple">';
						$return.=bp_get_the_profile_field_options();
					$return.='</select>';
				endif;
				if ( 'radio' == bp_get_the_profile_field_type() ) :
					$return.='<div class="radio">';
					$return.='<span class="label">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) :
						$return.=' (required)';
					endif;
					$return.='</span>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					$return.=bp_get_the_profile_field_options();
					if ( !bp_get_the_profile_field_is_required() ) :
						//$return.='<a class="clear-value" href="javascript:clear( \''.bp_get_the_profile_field_input_name().'\' );">'._e( 'Clear', 'buddypress' ).'</a>';
					endif;
					$return.='</div>';
				endif;
				if ( 'checkbox' == bp_get_the_profile_field_type() ) :
					$return.='<div class="checkbox">';
					$return.='<span class="label">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) :
						$return.=' (required)';
					endif;
					$return.='</span>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					$return.=bp_get_the_profile_field_options();
					$return.='</div>';
				endif;
				if ( 'datebox' == bp_get_the_profile_field_type() ) :
					$return.='<div class="datebox">';
					$return.='<label for="'.bp_get_the_profile_field_input_name().'_day">'.bp_get_the_profile_field_name();
					if ( bp_get_the_profile_field_is_required() ) :
						$return.=' (required)';
					endif;
					$return.='</label>';
					$return.=do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
					$return.='<select name="'.bp_get_the_profile_field_input_name().'_day" id="'.bp_get_the_profile_field_input_name().'_day">';
						$return.=bp_get_the_profile_field_options( 'type=day' );
					$return.='</select>';
					$return.='<select name="'.bp_get_the_profile_field_input_name().'_month" id="'.bp_get_the_profile_field_input_name().'_month">';
						$return.=bp_get_the_profile_field_options( 'type=month' );
					$return.='</select>';
					$return.='<select name="'.bp_get_the_profile_field_input_name().'_year" id="'.bp_get_the_profile_field_input_name().'_year">';
						$return.=bp_get_the_profile_field_options( 'type=year' );
					$return.='</select>';
					$return.='</div>';
				endif;
				$return.=do_action( 'bp_custom_profile_edit_fields' );
				$return.='<p class="description">'.bp_get_the_profile_field_description().'</p>';
				$return.='</div>';
			endwhile;
			if($group_id!=1){
				$return.='<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="3,7,241,'.bp_get_the_profile_group_field_ids().'" />';
			}
			endwhile; endif; endif;
			return $return;
	}
?>