<?php
/**
*	Home page functionality
*
*/

/**
*	Home page layout
*
*/

function cuny_build_homepage() {
	echo '<div id="home-left">';
		echo '<div id="cuny_openlab_jump_start">';
			cuny_home_login();
		echo '</div>';
			dynamic_sidebar('cac-featured');
			echo '<div class="box-1" id="whos-online">';
			echo '<h3 class="title">Who\'s Online?</h3>';
		    cuny_whos_online();
		echo '</div>'; ?>
			<?php cuny_home_new_members(); ?>
	<?php echo '</div>';
	echo '<div id="home-right">';
		dynamic_sidebar('pgw-gallery');
		echo '<div id="home-group-list-wrapper">';
			cuny_home_square('course');
			cuny_home_square('project');
			cuny_home_square('club');
			cuny_home_square('portfolio');
			echo '<div class="clearfloat"></div>';
			echo "<script type='text/javascript'>(function($){ $('.activity-list').css('visibility','hidden'); })(jQuery);</script>";
		echo '</div>';
 	echo '</div>';
}

/**
*	Home page login box layout
*
*/

function cuny_home_login() {

		 if ( is_user_logged_in() ) :

        echo '<div id="open-lab-login" class="box-1">';
        echo '<h3 class="title">Welcome...</h3>';
		do_action( 'bp_before_sidebar_me' ) ?>

		<div id="sidebar-me">
			<a class="alignleft avatar" href="<?php echo bp_loggedin_user_domain() ?>">
				<?php bp_loggedin_user_avatar( 'type=thumb&width=80&height=80' ) ?>
			</a>

			<div id="user-info">
            <h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
            <p><a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>">Not <?php echo bp_core_get_username(bp_loggedin_user_id()); ?>?</a></p>
			<p><a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a></p>
            </div><!--user-info-->
            <div class="clearfloat"></div>

			<?php do_action( 'bp_sidebar_me' ) ?>
		</div><!--sidebar-me-->

		<?php do_action( 'bp_after_sidebar_me' ) ?>

        <?php echo '</div>'; ?>

        <div id="login-help" class="home-box red-box">
        	 <h3 class="title">Need HELP?</h3>
		<p>Visit the <a href="<?php echo site_url(); ?>/blog/help/openlab-help/">Help</a> section or <a href='"<?php site_url(); ?>"/about/contact-us/'>contact us</a> with a question.</p>
        </div><!--login-help-->

	<?php else : ?>
    	<?php echo '<div id="open-lab-join" class="home-box red-box">'; ?>
    	<?php echo '<h3 class="title">JOIN OpenLab</h3>'; ?>
		<?php _e( '<p>Need an account? <b><a href="'.site_url().'/register/">Sign Up</a></b> to become a member!</p>', 'buddypress' ) ?>
        <?php echo '</div>'; ?>

		<?php echo '<div id="open-lab-login" class="box-1">'; ?>
		<?php do_action( 'bp_after_sidebar_login_form' ) ?>

		<?php echo '<h3 class="title">Log in to OpenLab</h3>'; ?>
		 <?php do_action( 'bp_before_sidebar_login_form' ) ?>

		<form name="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'buddypress' ) ?>
			<input type="text" name="log" id="sidebar-user-login" class="input" value="" tabindex="97" /></label>

			<label><?php _e( 'Password', 'buddypress' ) ?>
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<div id="below-login-form">
            <a class="forgot-password-link" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>">Forgot Password?</a>
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" /></div>
            <div id="keep-logged-in">
            <input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Keep me logged in', 'buddypress' ) ?>
            </div>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="hidden" name="testcookie" value="1" />
		</form>
        <?php echo '</div>'; ?>
	<?php endif;

}

/**
*	Home page new members box
*
*/


function cuny_home_new_members() {
	global $wpdb, $bp;
	echo '<div id="new-members" class="box-1 last">';
		echo '<h3 class="title">New OpenLab Members</h3>'; ?>
        	<div id="new-members-top-wrapper">
            <div id="new-members-text">
            	<p>Browse through and say "Hello!" to the newest members of OpenLab.</p>
            </div>
        	<div class="new-member-navigation">
				<button class="prev">&lt;&lt;</button>
				<button class="next">&gt;&gt;</button>
			</div>
            <div class="clearfloat"></div>
            </div><!--members-top-wrapper-->
		<?php if ( bp_has_members( 'type=newest&max=5' ) ) :
			$avatar_args = array (
				'type' => 'full',
				'width' => 121,
				'height' => 121,
				'class' => 'avatar',
				'id' => false,
				'alt' => __( 'Member avatar', 'buddypress' )
			);
			echo '<div id="home-new-member-wrap"><ul>';
				while ( bp_members() ) : bp_the_member();
					$user_id=bp_get_member_user_id();
					$firstname = xprofile_get_field_data( 'Name' , $user_id);
//					$lastname = xprofile_get_field_data( 'Last Name' , $user_id);?>
					<li class="home-new-member">
		        		<div class="home-new-member-avatar">
								<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
		                <div class="home-new-member-info">
		                    <?php echo "<h2>" . $firstname ."</h2>"; ?>
		                    <div class="registered"><?php bp_member_registered() ?></div>
		                </div>
		            </li>
	        	<?php endwhile;
        	echo '</ul></div>';
		endif;
	echo '</div>';
}

/**
*	Home page Who's Online box
*
*/


function cuny_whos_online() {
global $wpdb, $bp;
	$avatar_args = array (
			'type' => 'full',
			'width' => 45,
			'height' => 45,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);

	$sql = "SELECT user_id FROM wp_usermeta where meta_key='last_activity' and meta_value >= DATE_SUB( UTC_TIMESTAMP(), INTERVAL 1 HOUR ) order by meta_value desc limit 20";

	$rs = $wpdb->get_results( $sql );
	//print_r($rs);
	$ids="9999999";
	foreach ( (array)$rs as $r ) $ids.= ",".$r->user_id;
	$x = 0;
	if ( bp_has_members( 'type=active&include=' . $ids ) ) :
		$x+=1;?>

			<div class="avatar-block">
				<?php while ( bp_members() ) : bp_the_member(); ?>

					<?php
					 ?>
					<div class="cuny-member">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
						<div class="cuny-member-info">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a><br />
							<?php do_action( 'bp_directory_members_item' ); bp_member_profile_data( 'field=Account Type' ); ?>,
							<?php bp_member_last_active() ?>
						</div>
					</div>

				<?php endwhile; ?>
					<div style="clear:both"></div>
			</div>
		<?php endif;

}

/**
*	Home page latest group columns
*
*/


function cuny_home_square($type){
	global $wpdb, $bp;

	$meta_filter = new BP_Groups_Meta_Filter( array(
		'wds_group_type' => $type
	) );

	$i = 1;
	$column_class = "column";

	$groups_args = array(
		'max'         => 4,
		'type'        => 'active',
		'user_id'     => 0,
		'show_hidden' => false
	);

	if ( bp_has_groups( $groups_args ) ) : ?>

	  	<?php
	  	/* Let's save some queries and get the most recent activity in one fell swoop */

	  	global $groups_template;

	  	$group_ids = array();
	  	foreach( $groups_template->groups as $g ) {
	  		$group_ids[] = $g->id;
	  	}
	  	$group_ids_sql = implode( ',', $group_ids );

	  	$activity = $wpdb->get_results( $wpdb->prepare( "
	  		SELECT
	  			content, item_id
	  		FROM
	  			{$bp->activity->table_name}
	  		WHERE
	  			component = 'groups'
	  			AND
	  			type IN ('new_forum_post', 'new_forum_reply', 'new_blog_post', 'new_blog_comment')
	  			AND
	  			item_id IN ({$group_ids_sql})
	  		ORDER BY
	  			date_recorded DESC" ) );

	  	// Now walk down the list and try to match with a group. Once one is found, remove
	  	// that group from the stack
	  	$group_activity_items = array();
	  	foreach( (array)$activity as $act ) {
	  		if ( !empty( $act->content ) && in_array( $act->item_id, $group_ids ) && !isset( $group_activity_items[$act->item_id] ) ) {
	  			$group_activity_items[$act->item_id] = $act->content;
				$key = array_search( $act->item_id, $group_ids );
				unset( $group_ids[$key] );
	  		}
	  	}

	  	?>


      <div class="activity-list <?php echo $type; ?>-list">
      	<div class="title-wrapper">
	  	<h3 class="title"><a href="<?php echo site_url().'/'.strtolower($type); ?>s"><?php echo ucfirst($type); ?>s</a></h3>
		<div class="see-all"><a href="<?php echo site_url().'/'.strtolower($type); ?>s">See All</a></div>
        <div class="clearfloat"></div>
        </div><!--title-wrapper-->
		<?php while ( bp_groups() ) : bp_the_group();
		global $groups_template;
		$group = $groups_template->group;

		// Showing descriptions for now. http://openlab.citytech.cuny.edu/redmine/issues/291
		// $activity = !empty( $group_activity_items[$group->id] ) ? $group_activity_items[$group->id] : stripslashes( $group->description );
		$activity = stripslashes( $group->description );
			 echo '<div class="box-1 row row-'.$i.' type-'.$type.'">'; ?>
			 <div class="item-avatar">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 141, 'height' => 141 )) ?></a>
				</div>
			  <?php echo '<h2 class="green-title"><a href="'.bp_get_group_permalink().'">'.bp_get_group_name().'</a></h2>';
			  ?>
              <div class="byline"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ) ?></div>
              <?php
			  //echo '<div class="byline">Author Name | Date</div>';

			  echo bp_create_excerpt( $activity, 125, array( 'html' => false ) ) . '<p><a href="' . bp_get_group_permalink() . '">See More</a></p>';
			  echo '</div>';
			  $i++;
		  endwhile; ?>
	  	<div class="clearfloat"></div>
        </div><!--activity-list-->

      <?php endif;

      $meta_filter->remove_filters();
}

/**
*	openlab_groups_filter_clause()
*
*/


function openlab_groups_filter_clause( $sql ) {
	global $openlab_group_type, $bp;

	// Join to groupmeta table for group type
	$ex = explode( " WHERE ", $sql );
	$ex[0] .= ", " . $bp->groups->table_name_groupmeta . " gt";
	$ex = implode( " WHERE ", $ex );

	// Add the necessary where clause
	$ex = explode( " AND ", $ex );
	array_splice( $ex, 1, 0, "g.status = 'public' AND gt.group_id = g.id AND gt.meta_key = 'wds_group_type' AND ( gt.meta_value = '" . ucwords( $openlab_group_type ) . "' OR gt.meta_value = '" . strtolower( $openlab_group_type ) . "' )" );
	$ex = implode( " AND ", $ex );

	return $ex;
}

/**
*	Registration page layout
*
*/

function openlab_registration_page() {
		do_action( 'bp_before_register_page' ) ?>

		<div class="page" id="register-page">

			<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<h1 class="entry-title"><?php _e( 'Create an Account', 'buddypress' ) ?></h1>

				<?php do_action( 'template_notices' ) ?>

				<p><?php _e( 'Registering for the City Tech OpenLab is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress' ) ?></p>
				<p>Because the OpenLab is a space for collaboration between members of the City Tech community, a City Tech email address is required to use the site.</p> 
				<?php do_action( 'bp_before_account_details_fields' ) ?>

				<div class="register-section" id="basic-details-section">

					<?php /***** Basic Account Details ******/ ?>

					<h4><?php _e( 'Account Details', 'buddypress' ) ?></h4>

					<label for="signup_username"><?php _e( 'Username', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?> (lowercase & no special characters)</label>
					<?php do_action( 'bp_signup_username_errors' ) ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />

					<label for="signup_email"><?php _e( 'Email Address (required) <div class="email-requirements">Please use your City Tech email address to register</div>', 'buddypress' ) ?> </label>
					<?php do_action( 'bp_signup_email_errors' ) ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />

					<label for="signup_email_confirm">Confirm Email Address (required)</label>
					<input type="text" name="signup_email_confirm" id="signup_email_confirm" value="" />

					<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label>
					<?php do_action( 'bp_signup_password_errors' ) ?>
					<input type="password" name="signup_password" id="signup_password" value="" />

					<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label>
					<?php do_action( 'bp_signup_password_confirm_errors' ) ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />

				</div><!-- #basic-details-section -->

				<?php do_action( 'bp_after_account_details_fields' ) ?>

				<?php /***** Extra Profile Details ******/ ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<?php do_action( 'bp_before_signup_profile_fields' ) ?>

					<div class="register-section" id="profile-details-section">

						<h4><?php _e( 'Public Profile Details', 'buddypress' ) ?></h4>

						<p>Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.</p>

						<?php echo wds_get_register_fields();?>

                        <?php do_action( 'bp_after_signup_profile_fields' ) ?>

					</div><!-- #profile-details-section -->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ) ?>

				<div class="submit">
					<input style="display:none;" type="submit" name="signup_submit" id="signup_submit" value="<?php _e( 'Complete Sign Up', 'buddypress' ) ?> &rarr;" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ) ?>

				<?php wp_nonce_field( 'bp_new_signup' ) ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<h2><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></h2>

				<?php do_action( 'template_notices' ) ?>

				<?php if ( bp_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
				<?php endif; ?>

				<!--<?php if ( bp_is_active( 'xprofile' ) && !(int)bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

					<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

						<h4><?php _e( 'Your Current Avatar', 'buddypress' ) ?></h4>
						<p><?php _e( "We've fetched an avatar for your new account. If you'd like to change this, why not upload a new one?", 'buddypress' ) ?></p>

						<div id="signup-avatar">
							<?php bp_signup_avatar() ?>
						</div>

						<p>
							<input type="file" name="file" id="file" />
							<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
							<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							<input type="hidden" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />
							<input type="hidden" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />
						</p>

						<?php wp_nonce_field( 'bp_avatar_upload' ) ?>

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Your New Avatar', 'buddypress' ) ?></h3>

						<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ) ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ) ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

						<input type="hidden" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />
						<input type="hidden" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />
						<input type="hidden" name="signup_avatar_dir" id="signup_avatar_dir" value="<?php bp_signup_avatar_dir_value() ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

						<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

					<?php endif; ?>

				<?php endif; ?> -->

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ) ?>

			</form>

		</div>

		<?php do_action( 'bp_after_register_page' ) ?>

	<?php do_action( 'bp_after_directory_activity_content' ) ?>
	
	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
<?php }

/**
*	Registration page sidebar
*
*/

function openlab_buddypress_register_actions() {
		global $bp;?>
		<h2 class="sidebar-title">&nbsp;</h2>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
	<?php
	}