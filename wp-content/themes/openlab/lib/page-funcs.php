<?php
/**
 *  Home page functionality
 *
 */

/**
 *  Home page login box layout
 *
 */
function cuny_home_login() {

	if ( is_user_logged_in() ) :

		echo '<div id="open-lab-login" class="log-box links-lighter-hover">';
		echo '<h1 class="title inline-element semibold hyphenate truncate-on-the-fly" data-basewidth="60" data-basevalue="60">Welcome, ' . bp_core_get_user_displayname( bp_loggedin_user_id() ) . '</h1>';
		do_action( 'bp_before_sidebar_me' )
		?>

		<div id="sidebar-me" class="clearfix">
			<div id="user-info">
				<a class="avatar" href="<?php echo bp_loggedin_user_domain(); ?>">
					<img class="img-responsive" src="
					<?php
					bp_loggedin_user_avatar(
						array(
							'type' => 'full',
							'html' => false,
						)
					);
					?>
														" alt="Avatar for <?php echo bp_core_get_user_displayname( bp_loggedin_user_id() ); ?>" />
				</a>

				<ul class="content-list">
					<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ); ?>">Not <?php echo bp_core_get_username( bp_loggedin_user_id() ); ?>?</a></li>
					<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ); ?>"><?php _e( 'Log Out', 'buddypress' ); ?></a></li>
				</ul>
				</span><!--user-info-->
			</div>
			<?php do_action( 'bp_sidebar_me' ); ?>
		</div><!--sidebar-me-->

		<?php do_action( 'bp_after_sidebar_me' ); ?>

		<?php echo '</div>'; ?>

		<div id="login-help" class="log-box">
			<h4 class="title">Need Help?</h4>
			<p class="font-size font-14">Visit the <a class="roll-over-loss" href="<?php echo site_url(); ?>/blog/help/openlab-help/">Help section</a> or <a class="roll-over-loss" href='<?php echo site_url(); ?>/about/contact-us/'>contact us</a> with a question.</p>
		</div><!--login-help-->

	<?php else : ?>
		<?php echo '<div id="open-lab-join" class="log-box links-lighter-hover">'; ?>
		<?php echo '<h1 class="title"><span class="fa fa-plus-circle flush-left"></span> Join the OpenLab</h1>'; ?>
		<?php _e( '<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="' . site_url() . '/register/">Sign up</a> <span class="font-size font-14">Need an account?<br />Sign Up to become a member!</span></p>', 'buddypress' ); ?>
		<?php echo '</div>'; ?>

		<?php echo '<div id="open-lab-login" class="log-box">'; ?>
		<?php do_action( 'bp_after_sidebar_login_form' ); ?>
		<?php echo '</div>'; ?>

		<div id="user-login" class="log-box">

			<?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
			<?php do_action( 'bp_before_sidebar_login_form' ); ?>

			<form name="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" method="post">
				<label class="sr-only" for="sidebar-user-login">Username</label>
				<input class="form-control input" type="text" name="log" id="sidebar-user-login" value="" placeholder="Username" tabindex="0" />

				<label class="sr-only" for="sidebar-user-pass">Password</label>
				<input class="form-control input" type="password" name="pwd" id="sidebar-user-pass" value="" placeholder="Password" tabindex="0" />

				<div id="keep-logged-in" class="small-text clearfix">
					<div class="password-wrapper">
						<a class="forgot-password-link small-text roll-over-loss" href="<?php echo site_url( 'wp-login.php?action=lostpassword', 'login' ); ?>">Forgot Password?</a>
						<span class="keep-logged-in-checkbox"><input class="no-margin no-margin-top" name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="0" /><label class="regular no-margin no-margin-bottom" for="sidebar-rememberme"><?php _e( 'Keep me logged in', 'buddypress' ); ?></label></span>
					</div>
					<input class="btn btn-default btn-primary link-btn pull-right semibold" type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e( 'Log In' ); ?>" tabindex="0" />
				</div>
				<input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain(); ?>" />

				<?php do_action( 'bp_sidebar_login_form' ); ?>

			</form>
		</div>
	<?php
	endif;
}

/**
 *  Home page new members box
 *
 */
function cuny_home_new_members() {
	global $wpdb, $bp;
	echo '<div id="new-members" class="box-1 left-box last">';
	echo '<h2 class="title uppercase">New OpenLab Members</h2>';
	echo '<div class="left-block-content new-members-wrapper">'
	?>
	<div id="new-members-top-wrapper">
		<div id="new-members-text">
			<p><span class="new-member-navigation pull-right">
					<button class="prev btn btn-link">
						<i class="fa fa-chevron-circle-left" aria-hidden="true"></i><span class="sr-only">Previous New Members</span></button>
					<button class="next btn btn-link" href="#">
						<i class="fa fa-chevron-circle-right" aria-hidden="true"></i><span class="sr-only">Next New Members</span></button>
				</span>
				Browse through and say "Hello!" to the<br />newest members of the OpenLab.</p>
		</div>
		<div class="clearfloat"></div>
	</div><!--members-top-wrapper-->
	<?php
	if ( bp_has_members( 'type=newest&max=5' ) ) :
		$avatar_args = array(
			'type'   => 'full',
			'width'  => 121,
			'height' => 121,
			'class'  => 'avatar',
			'id'     => false,
			'alt'    => __( 'Member avatar', 'buddypress' ),
		);
		echo '<div id="home-new-member-wrap"><ul>';
		while ( bp_members() ) :
			bp_the_member();
			$user_id   = bp_get_member_user_id();
			$firstname = xprofile_get_field_data( 'Name', $user_id );
			?>
			<li class="home-new-member">
				<div class="home-new-member-avatar">
					<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="
														  <?php
															echo bp_core_fetch_avatar(
																array(
																	'item_id' => $user_id,
																	'object'  => 'member',
																	'type'    => 'full',
																	'html'    => false,
																)
															);
															?>
																								" alt="<?php echo $firstname; ?>"/></a>
				</div>
				<div class="home-new-member-info">
					<h2 class="truncate-on-the-fly load-delay" data-basevalue="16" data-minvalue="11" data-basewidth="164"><?php echo $firstname; ?></h2>
					<span class="original-copy hidden"><?php echo $firstname; ?></span>
					<div class="registered timestamp"><?php bp_member_registered(); ?></div>
				</div>
			</li>
			<?php
		endwhile;
		echo '</ul></div>';
	endif;
	echo '</div></div>';
}

/**
 *  Home page Who's Online box
 *
 */
function cuny_whos_online() {
	global $wpdb, $bp;

	$cached = get_transient( 'openlab_whos_online' );
	if ( $cached ) {
		echo $cached;
		return;
	}

	$rs = wp_cache_get( 'whos_online', 'openlab' );
	if ( ! $rs ) {
		$sql = "SELECT user_id FROM {$bp->activity->table_name} where component = 'members' AND type ='last_activity' and date_recorded >= DATE_SUB( NOW(), INTERVAL 1 HOUR ) order by date_recorded desc limit 12";
		$rs  = $wpdb->get_col( $sql );
		wp_cache_set( 'whos_online', $rs, 'openlab', 5 * 60 );
	}

	$ids = '9999999';
	foreach ( (array) $rs as $r ) {
		$ids .= ',' . intval( $r );
	}

	ob_start();

	if ( bp_has_members( 'type=active&include=' . $ids ) ) :

		?>

		<div class="avatar-block left-block-content clearfix">
			<?php
			while ( bp_members() ) :
				bp_the_member();
				?>

				<div class="cuny-member">
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="
															  <?php
																echo bp_core_fetch_avatar(
																	array(
																		'item_id' => bp_get_member_user_id(),
																		'object'  => 'member',
																		'type'    => 'full',
																		'html'    => false,
																	)
																);
																?>
																									" alt="<?php bp_member_name(); ?>"/></a>
					</div>
					<div class="cuny-member-info">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a><br />
						<?php
						do_action( 'bp_directory_members_item' );
						bp_member_profile_data( 'field=Account Type' );
						?>
						,
						<?php bp_member_last_active(); ?>
					</div>
				</div>

			<?php endwhile; ?>
		</div>
		<?php
	endif;

	$html = ob_get_clean();

	set_transient( 'openlab_whos_online', $html, 5 * 60 );

	echo $html;
}

function openlab_stay_up_to_date() {
	$links = array(
		array(
			'title'       => 'The Open Road',
			'url'         => 'https://openlab.citytech.cuny.edu/groups/the-open-road/',
			'description' => 'For all things OpenLab: news, workshops, events, community, and support!',
			'avatar'      => 'https://openlab.citytech.cuny.edu/wp-content/uploads/group-avatars/351/2ece6cb872c2ea3a17fd9248e5ff9f8c-bpfull.png',
		),
		array(
			'title'       => 'The Buzz',
			'url'         => 'https://openlab.citytech.cuny.edu/groups/the-buzz/',
			'description' => 'Follow our student bloggers as they post about life at City Tech and beyond!',
			'avatar'      => 'https://openlab.citytech.cuny.edu/wp-content/uploads/group-avatars/2038/70c40887176f540c50463c79c3c8e667-bpfull.jpg',
		),
		array(
			'title'       => 'Open Pedagogy on the OpenLab',
			'url'         => 'https://openlab.citytech.cuny.edu/groups/open-pedagogy-on-the-openlab/',
			'description' => 'Share and discuss resources about open digital pedagogy!',
			'avatar'      => 'https://openlab.citytech.cuny.edu/wp-content/uploads/group-avatars/1705/5a7b192cdc151-bpfull.jpg',
		),
	);

	?>
	<div class="activity-list item-list">
		<?php foreach ( $links as $link ) : ?>
			<div class="sidebar-block">
				<div class="clearfix">
					<div class="activity-avatar pull-left">
						<a href="<?php echo esc_url( $link['url'] ); ?>"><img class="img-responsive" src="<?php echo esc_url( $link['avatar'] ); ?>" alt="Avatar of <?php echo esc_attr( $link['title'] ); ?>" /></a>
					</div>

					<div class="up-to-date-site-title">
						<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo $link['title']; ?></a>
					</div>

					<div class="up-to-date-site-description">
						<?php echo esc_html( $link['description'] ); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 *  Home page latest group columns
 *
 */
function cuny_home_square( $type ) {
	global $wpdb, $bp;

	$cached = get_transient( 'openlab_home_square_' . $type );
	if ( $cached ) {
		echo $cached;
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	$meta_filter = new BP_Groups_Meta_Filter(
		array(
			'wds_group_type' => $type,
		)
	);

	$i = 1;

	$groups_args = array(
		'max'         => 4,
		'type'        => 'active',
		'user_id'     => 0,
		'show_hidden' => false,
	);

	if ( bp_has_groups( $groups_args ) ) :
		?>

		<?php
		/* Let's save some queries and get the most recent activity in one fell swoop */

		global $groups_template;

		$group_ids = array();
		foreach ( $groups_template->groups as $g ) {
			$group_ids[] = $g->id;
		}
		$group_ids_sql = implode( ',', $group_ids );

		ob_start();
		?>


		<div class="col-sm-6 activity-list <?php echo $type; ?>-list">
			<div class="activity-wrapper">
				<div class="title-wrapper">
					<h2 class="title activity-title"><a class="no-deco" href="<?php echo site_url() . '/' . strtolower( $type ); ?>s"><?php echo ucfirst( $type ); ?>s<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
				</div><!--title-wrapper-->
				<?php
				while ( bp_groups() ) :
					bp_the_group();
					$group = $groups_template->group;

					// Showing descriptions for now. http://openlab.citytech.cuny.edu/redmine/issues/291
					// $activity = !empty( $group_activity_items[$group->id] ) ? $group_activity_items[$group->id] : stripslashes( $group->description );
					$activity = stripslashes( $group->description );
					echo '<div class="box-1 row-' . $i . ' activity-item type-' . $type . '">';
					?>
					<div class="item-avatar">
						<a href="<?php bp_group_permalink(); ?>"><img class="img-responsive" src ="
															 <?php
																echo bp_core_fetch_avatar(
																	array(
																		'item_id' => $group->id,
																		'object'  => 'group',
																		'type'    => 'full',
																		'html'    => false,
																	)
																);
																?>
																									" alt="<?php echo $group->name; ?>"/></a>
					</div>
					<div class="item-content-wrapper">
						<h4 class="group-title overflow-hidden">
							<a class="no-deco truncate-on-the-fly hyphenate" href="<?php echo bp_get_group_permalink(); ?>" data-basevalue="40" data-minvalue="15" data-basewidth="145"><?php echo bp_get_group_name(); ?></a>
							<span class="original-copy hidden"><?php echo bp_get_group_name(); ?></span>
						</h4>

						<p class="hyphenate overflow-hidden">
							<?php
							echo bp_create_excerpt(
								$activity,
								150,
								array(
									'ending' => __( '&hellip;', 'buddypress' ),
									'html'   => false,
								)
							);
							?>
						</p>
						<p class="see-more">
							<a class="semibold" href="<?php echo bp_get_group_permalink(); ?>">See More<span class="sr-only"> <?php echo bp_get_group_name(); ?></span></a>
						</p>
					</div>
				</div>
					<?php
					$i++;
			endwhile;
				?>
		</div>
		</div><!--activity-list-->

		<?php
	endif;

	$html = ob_get_clean();

	set_transient( 'openlab_home_square_' . $type, $html, 5 * 60 );

	echo $html;

	$meta_filter->remove_filters();
}

/**
 *  openlab_groups_filter_clause()
 *
 */
function openlab_groups_filter_clause( $sql ) {
	global $openlab_group_type, $bp;

	// Join to groupmeta table for group type
	$ex     = explode( ' WHERE ', $sql );
	$ex[0] .= ', ' . $bp->groups->table_name_groupmeta . ' gt';
	$ex     = implode( ' WHERE ', $ex );

	// Add the necessary where clause
	$ex = explode( ' AND ', $ex );
	array_splice( $ex, 1, 0, "g.status = 'public' AND gt.group_id = g.id AND gt.meta_key = 'wds_group_type' AND ( gt.meta_value = '" . ucwords( $openlab_group_type ) . "' OR gt.meta_value = '" . strtolower( $openlab_group_type ) . "' )" );
	$ex = implode( ' AND ', $ex );

	return $ex;
}

/**
 *  Registration page layout
 *
 */
function openlab_registration_page() {
	do_action( 'bp_before_register_page' );

	wp_enqueue_script( 'openlab-academic-units' );

	$ajaxurl = bp_core_ajax_url();

	$first_name_field_id = openlab_get_xprofile_field_id( 'First Name' );
	$last_name_field_id  = openlab_get_xprofile_field_id( 'Last Name' );

	$first_name_submitted = isset( $_POST[ 'field_' . $first_name_field_id ] ) ? $_POST[ 'field_' . $field_name_field_id ] : '';
	$last_name_submitted  = isset( $_POST[ 'field_' . $last_name_field_id ] ) ? $_POST[ 'field_' . $last_name_field_id ] : '';
	?>

	<div class="page" id="register-page">

		<div id="openlab-main-content"></div>

		<h1 class="entry-title"><?php _e( 'Create an Account', 'buddypress' ); ?></h1>

		<form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data" data-parsley-trigger="blur">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold">Account Details</div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<p><?php _e( 'Registering for the City Tech OpenLab is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress' ); ?></p>
						<p>Because the OpenLab is a space for collaboration between members of the City Tech community, a City Tech email address is required to use the site.</p>
						<?php do_action( 'bp_before_account_details_fields' ); ?>

						<div class="register-section" id="basic-details-section">

							<?php /* Basic Account Details */ ?>

							<div class="form-group">
								<label class="control-label" for="signup_username"><?php _e( 'Username', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?> (lowercase & no special characters)</label>
								<div id="signup_username_error" class="error-container"></div>
								<?php do_action( 'bp_signup_username_errors' ); ?>
								<input
									class="form-control"
									type="text"
									name="signup_username"
									id="signup_username"
									value="<?php bp_signup_username_value(); ?>"
									data-parsley-lowercase
									data-parsley-nospecialchars
									data-parsley-required
									data-parsley-required-message="Username is required."
									data-parsley-minlength="4"
									data-parsley-remote="
									<?php
									echo add_query_arg(
										array(
											'action' => 'openlab_unique_login_check',
											'login'  => '{value}',
										),
										$ajaxurl
									);
									?>
									"
									data-parsley-remote-message="That username is already taken."
									data-parsley-errors-container="#signup_username_error"
									/>
							</div>

							<div class="form-group">
								<label class="control-label" for="field_<?php echo intval( $first_name_field_id ); ?>">First Name (required, but not displayed on Public Profile)</label>
								<div id="field_<?php echo esc_attr( $first_name_field_id ); ?>_error" class="error-container"></div>
								<?php do_action( 'bp_field_' . $first_name_field_id . '_errors' ); ?>
								<input
									class="form-control"
									type="text"
									name="field_<?php echo esc_attr( $first_name_field_id ); ?>"
									id="field_<?php echo esc_attr( $first_name_field_id ); ?>"
									data-parsley-required
									data-parsley-required-message="First Name is required."
									data-parsley-errors-container="#field_<?php echo esc_attr( $first_name_field_id ); ?>_error"
									value="<?php echo esc_attr( $first_name_submitted ); ?>"
								/>
							</div>

							<div class="form-group">
								<label class="control-label" for="field_<?php echo intval( $last_name_field_id ); ?>">Last Name (required, but not displayed on Public Profile)</label>
								<div id="field_<?php echo esc_attr( $last_name_field_id ); ?>_error" class="error-container"></div>
								<?php do_action( 'bp_field_' . $last_name_field_id . '_errors' ); ?>
								<input
									class="form-control"
									type="text"
									name="field_<?php echo esc_attr( $last_name_field_id ); ?>"
									id="field_<?php echo esc_attr( $last_name_field_id ); ?>"
									data-parsley-required
									data-parsley-required-message="Last Name is required."
									data-parsley-errors-container="#field_<?php echo esc_attr( $last_name_field_id ); ?>_error"
									value="<?php echo esc_attr( $last_name_submitted ); ?>"
								/>
							</div>

							<div class="form-group">
								<label class="control-label" for="signup_email"><?php _e( 'Email Address (required) <div class="email-requirements">Please use your City Tech email address to register</div>', 'buddypress' ); ?> </label>
								<div id="signup_email_error" class="error-container"></div>
								<?php do_action( 'bp_signup_email_errors' ); ?>
								<input
									class="form-control"
									type="text"
									name="signup_email"
									id="signup_email"
									value="<?php echo openlab_post_value( 'signup_email' ); ?>"
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Email is required."
									data-parsley-type="email"
									data-parsley-group="email"
									data-parsley-iff="#signup_email_confirm"
									data-parsley-iff-message=""
									data-parsley-errors-container="#signup_email_error"
									/>

								<label class="control-label" for="signup_email_confirm">Confirm Email Address (required)</label>
								<div id="signup_email_confirm_error" class="error-container"></div>
								<input
									class="form-control"
									type="text"
									name="signup_email_confirm"
									id="signup_email_confirm"
									value="<?php echo openlab_post_value( 'signup_email_confirm' ); ?>"
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Confirming your email is required."
									data-parsley-type="email"
									data-parsley-iff="#signup_email"
									data-parsley-iff-message="Email addresses must match."
									data-parsley-group="email"
									data-parsley-errors-container="#signup_email_confirm_error"
									/>
							</div>

							<div data-parsley-children-should-match class="form-group">
								<label class="control-label" for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
								<div id="signup_password_error" class="error-container"></div>
								<?php do_action( 'bp_signup_password_errors' ); ?>
								<div class="password-field">
									<input
										class="form-control"
										type="password"
										name="signup_password"
										id="signup_password"
										value=""
										data-parsley-trigger="blur"
										data-parsley-required
										data-parsley-required-message="Password is required."
										data-parsley-group="password"
										data-parsley-iff="#signup_password_confirm"
										data-parsley-iff-message=""
										data-parsley-errors-container="#signup_password_error"
										/>

									<div id="password-strength-notice" class="password-strength-notice"></div>
								</div>

								<label class="control-label" for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
								<div id="signup_password_confirm_error" class="error-container"></div>
								<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
								<input
									class="form-control password-field"
									type="password"
									name="signup_password_confirm"
									id="signup_password_confirm"
									value=""
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Confirming your password is required."
									data-parsley-group="password"
									data-parsley-iff="#signup_password"
									data-parsley-iff-message="Passwords must match."
									data-parsley-errors-container="#signup_password_confirm_error"
									/>
							</div>

						</div><!-- #basic-details-section -->
					</div>
				</div><!--.panel-->

				<?php do_action( 'bp_after_account_details_fields' ); ?>

				<?php /*                 * *** Extra Profile Details ***** */ ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading semibold">Public Profile Details</div>
						<div class="panel-body">

							<?php do_action( 'bp_before_signup_profile_fields' ); ?>

							<div class="register-section" id="profile-details-section">

								<p>Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.</p>

								<?php echo wds_get_register_fields( 'Base' ); ?>

								<?php do_action( 'bp_after_signup_profile_fields' ); ?>

							</div><!-- #profile-details-section -->
						</div>
					</div><!--.panel-->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>

				<p class="sign-up-terms">
					By clicking "Complete Sign Up", I agree to the <a class="underline" href="<?php echo home_url( 'about/terms-of-service' ); ?>" target="_blank">OpenLab Terms of Use</a> and <a class="underline" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a>.
				</p>

				<p id="submitSrMessage" class="sr-only submit-alert" aria-live="polite"></p>

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary btn-disabled" value="<?php _e( 'Please Complete Required Fields', 'buddypress' ); ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'bp_new_signup' ); ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php _e( 'Sign Up Complete!', 'buddypress' ); ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<?php if ( bp_registration_needs_activation() ) : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
						<?php else : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
						<?php endif; ?>

					</div>
				</div><!--.panel-->

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ); ?>

	<?php do_action( 'bp_after_directory_activity_content' ); ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
			if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
				jQuery('div#blog-details').toggle();

			jQuery('input#signup_with_blog').click(function () {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
	<?php
}

function openlab_primary_skip_link() {
	$skip_link_out = '';

	$content_target = '#openlab-main-content';
	$content_text   = 'main content';

	if ( is_user_logged_in() ) {
		$adminbar_target = '#wp-admin-bar-my-openlab';
		$adminbar_text   = 'admin bar';
	} else {
		$adminbar_target = '#wp-admin-bar-bp-login';
		$adminbar_text   = 'log in';
	}

	$skip_link_out = <<<HTML
            <a id="skipToContent" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$content_target}">Skip to {$content_text}</a>
            <a id="skipToAdminbar" tabindex="0" class="sr-only sr-only-focusable skip-link" href="{$adminbar_target}">Skip to {$adminbar_text}</a>
HTML;

	return $skip_link_out;
}
