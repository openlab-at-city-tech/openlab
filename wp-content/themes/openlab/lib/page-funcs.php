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

	if ( is_user_logged_in() ) {

		echo '<div id="open-lab-login" class="log-box links-lighter-hover">';
		echo '<h2 class="title inline-element semibold hyphenate truncate-on-the-fly" data-basewidth="60" data-basevalue="60">Welcome, ' . esc_html( bp_core_get_user_displayname( bp_loggedin_user_id() ) ) . '</h2>';
		do_action( 'bp_before_sidebar_me' )
		?>

		<div id="sidebar-me" class="clearfix">
			<div id="user-info">
				<a class="avatar" href="<?php echo esc_attr( bp_loggedin_user_domain() ); ?>">
					<img class="img-responsive" src="
					<?php
					bp_loggedin_user_avatar(
						array(
							'type' => 'full',
							'html' => false,
						)
					);
					?>
					" alt="Avatar for <?php echo esc_attr( bp_core_get_user_displayname( bp_loggedin_user_id() ) ); ?>" />
				</a>

				<span class="user-links">
					<span class="my-profile">
						<a href="<?php echo esc_url( bp_loggedin_user_domain() ); ?>">My Profile</a>
					</span>

					<ul class="content-list">
						<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo esc_attr( wp_logout_url( bp_get_root_domain() ) ); ?>">Not <?php echo esc_html( bp_core_get_username( bp_loggedin_user_id() ) ); ?>?</a></li>
						<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo esc_html( wp_logout_url( bp_get_root_domain() ) ); ?>">Log Out</a></li>
					</ul>
				</span><!--user-info-->
			</div>
			<?php do_action( 'bp_sidebar_me' ); ?>
		</div><!--sidebar-me-->

		<?php do_action( 'bp_after_sidebar_me' ); ?>

		<?php echo '</div>'; ?>

		<div id="login-help" class="log-box">
			<h2 class="title">Need Help?</h2>
			<p class="font-size font-14">Visit the <a class="roll-over-loss" href="<?php echo esc_attr( site_url() ); ?>/blog/help/openlab-help/">Help section</a> or <a class="roll-over-loss" href='<?php echo esc_attr( site_url() ); ?>/about/contact-us/'>contact us</a> with a question.</p>
		</div><!--login-help-->
		<?php

	} else {

		?>

		<?php echo '<div id="open-lab-join" class="log-box links-lighter-hover">'; ?>
		<?php echo '<h2 class="title"><span class="fa fa-plus-circle flush-left"></span> Join the OpenLab</h2>'; ?>
		<?php echo '<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="' . esc_attr( site_url() ) . '/register/">Sign up</a> <span class="font-size font-14">Need an account?<br />Sign Up to become a member!</span></p>'; ?>
		<?php echo '</div>'; ?>

		<?php echo '<div id="open-lab-login" class="log-box">'; ?>
		<?php do_action( 'bp_after_sidebar_login_form' ); ?>
		<?php echo '</div>'; ?>

		<div id="user-login" class="log-box">

			<?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
			<?php do_action( 'bp_before_sidebar_login_form' ); ?>

			<form name="login-form" class="standard-form" action="<?php echo esc_attr( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
				<label class="sr-only" for="sidebar-user-login">Username</label>
				<input class="form-control input" type="text" name="log" id="sidebar-user-login" value="" placeholder="Username" tabindex="0" />

				<label class="sr-only" for="sidebar-user-pass">Password</label>
				<input class="form-control input" type="password" name="pwd" id="sidebar-user-pass" value="" placeholder="Password" tabindex="0" />

				<div id="keep-logged-in" class="small-text clearfix">
					<div class="password-wrapper">
						<a class="forgot-password-link small-text roll-over-loss" href="<?php echo esc_attr( site_url( 'wp-login.php?action=lostpassword', 'login' ) ); ?>">Forgot Password?</a>
						<span class="keep-logged-in-checkbox"><input class="no-margin no-margin-top" name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="0" /><label class="regular no-margin no-margin-bottom" for="sidebar-rememberme"><?php esc_html_e( 'Keep me logged in', 'buddypress' ); ?></label></span>
					</div>
					<input class="btn btn-default btn-primary link-btn pull-right semibold" type="submit" name="wp-submit" id="sidebar-wp-submit" value="Log In" tabindex="0" />
				</div>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( bp_get_root_domain() ); ?>" />

				<?php do_action( 'bp_sidebar_login_form' ); ?>

			</form>
		</div>
	<?php
	}
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
					<?php
					$avatar_url = bp_core_fetch_avatar(
						array(
							'item_id' => $user_id,
							'object'  => 'member',
							'type'    => 'full',
							'html'    => false,
						)
					);
					?>
					<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $avatar_url ); ?>" alt="<?php echo esc_attr( $firstname ); ?>"/></a>
				</div>
				<div class="home-new-member-info">
					<h2 class="truncate-on-the-fly load-delay" data-basevalue="16" data-minvalue="11" data-basewidth="164"><?php echo esc_html( $firstname ); ?></h2>
					<span class="original-copy hidden"><?php echo esc_html( $firstname ); ?></span>
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
		echo $cached; // WPCS: XSS ok.
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
						<?php
						$avatar_url = bp_core_fetch_avatar(
							array(
								'item_id' => bp_get_member_user_id(),
								'object'  => 'member',
								'type'    => 'full',
								'html'    => false,
							)
						);
						?>
						<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $avatar_url ); ?>" alt="<?php bp_member_name(); ?>"/></a>
					</div>
					<div class="cuny-member-info">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a><br />
						<?php
						do_action( 'bp_directory_members_item' );
						echo openlab_get_user_member_type_label( bp_get_member_user_id() );
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

	echo $html; // WPCS: XSS ok.
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
			'avatar'      => 'https://openlab.citytech.cuny.edu/wp-content/uploads/group-avatars/2038/1702317797-bpfull.png',
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
						<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
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
	global $wpdb, $bp, $groups_template;

	$cached = get_transient( 'openlab_home_square_' . $type );
	if ( $cached ) {
		echo $cached; // WPCS: XSS ok.
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	$i = 1;

	$groups_args = array(
		'max'         => 4,
		'type'        => 'active',
		'user_id'     => 0,
		'show_hidden' => false,
		'meta_query'  => [
			[
				'key'   => 'wds_group_type',
				'value' => $type,
			]
		],
	);

	ob_start();

	if ( bp_has_groups( $groups_args ) ) : ?>

		<div class="col-sm-6 activity-list <?php echo esc_attr( $type ); ?>-list">
			<div class="activity-wrapper">
				<div class="title-wrapper">
					<h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr( site_url() ) . '/' . esc_attr( strtolower( $type ) ); ?>s"><?php echo esc_html( ucfirst( $type ) ); ?>s<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
				</div><!--title-wrapper-->
				<?php
				while ( bp_groups() ) :
					bp_the_group();
					$group = $groups_template->group;

					// Showing descriptions for now. http://openlab.citytech.cuny.edu/redmine/issues/291
					// $activity = !empty( $group_activity_items[$group->id] ) ? $group_activity_items[$group->id] : stripslashes( $group->description );
					$activity = stripslashes( $group->description );
					echo '<div class="clickable-card box-1 row-' . esc_attr( $i ) . ' activity-item type-' . esc_attr( $type ) . '">';
					?>
					<div class="item-avatar">
						<?php
						$avatar_url = bp_core_fetch_avatar(
							array(
								'item_id' => $group->id,
								'object'  => 'group',
								'type'    => 'full',
								'html'    => false,
							)
						);
						?>
						<img class="img-responsive" src="<?php echo esc_attr( $avatar_url ); ?>" alt="<?php echo esc_attr( $group->name ); ?>"/>
					</div>
					<div class="item-content-wrapper">
						<h3 class="item-title group-title overflow-hidden">
							<a class="no-deco truncate-on-the-fly hyphenate" href="<?php echo esc_attr( bp_get_group_permalink() ); ?>" data-basevalue="40" data-minvalue="15" data-basewidth="145"><?php echo esc_html( bp_get_group_name() ); ?></a>
							<span class="original-copy hidden"><?php echo esc_html( bp_get_group_name() ); ?></span>
						</h3>

						<p class="hyphenate overflow-hidden">
							<?php
							echo bp_create_excerpt(
								$activity,
								150,
								array(
									'ending' => __( '&hellip;', 'buddypress' ),
									'html'   => false,
								)
							); // WPCS: XSS ok
							?>
						</p>
						<p class="see-more">
							<span class="semibold" href="<?php echo esc_attr( bp_get_group_permalink() ); ?>">See More<span class="sr-only"> <?php echo esc_html( bp_get_group_name() ); ?></span></span>
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

	if ( $html ) {
		set_transient( 'openlab_home_square_' . $type, $html, 5 * 60 );
	}

	echo $html; // WPCS: XSS ok
}

/**
 * Busts the transient HTML cache for home page squares.
 *
 * Called when a group is created, deleted, or updated, or when the avatar is updated.
 */
function openlab_bust_home_square_cache() {
	delete_transient( 'openlab_home_square_project' );
	delete_transient( 'openlab_home_square_course' );
	delete_transient( 'openlab_home_square_club' );
	delete_transient( 'openlab_home_square_portfolio' );
}
add_action( 'groups_created_group', 'openlab_bust_home_square_cache' );
add_action( 'groups_delete_group', 'openlab_bust_home_square_cache' );
add_action( 'groups_group_after_save', 'openlab_bust_home_square_cache' );
add_action( 'bp_core_delete_existing_avatar', 'openlab_bust_home_square_cache' );
add_action( 'groups_avatar_uploaded', 'openlab_bust_home_square_cache' );

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
