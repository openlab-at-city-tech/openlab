<?php

/**
 * OpenLab implementation of the WP toolbar
 */

/**
 * Ensure that the toolbar always shows
 */
add_filter( 'show_admin_bar', '__return_true', 999999 );

/**
 * Removing the default WP admin bar styles; a customized version of the default styles can now be found
 * in the mu-plugins folder
 * This is done for two reasons:
 *
 *  1) The current default css uses a an all selector ('*') to apply a number of default styles that completely
 *     inhibit the ability to inject Bootstrap into the admin, and make custom responsive styling an immense challenge
 *  2) Because this admin bar is now highly customized, we do not want future releases of WP to upset those customizations
 *     without proper vetting
 *
 * Note: in addition to removing the all selector styles, there are also a number of customizations to the custom default admin
 * bar css, to reduce overall styling overhead
 *
 * @param type $styles
 */
function openlab_remove_admin_bar_default_css($styles) {

    $styles->remove('admin-bar');

}

add_action('wp_default_styles', 'openlab_remove_admin_bar_default_css', 99999);

/**
 * Bootstrap
 */
add_action( 'add_admin_bar_menus', array( 'OpenLab_Admin_Bar', 'init' ) );

class OpenLab_Admin_Bar {
	public static function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Admin_Bar;
		}
	}

	function __construct() {
		// Bail if BP is not present
		if ( !class_exists( 'BP_Core' ) ) {
			return;
		}

                //remove BP admin bar styling too
                add_filter('bp_core_register_common_styles',array($this,'remove_bp_admin_bar_styles'));

		// Add a body style to distinguish between sites
		add_action( 'body_class', array( &$this, 'body_class' ), 999 );
		add_action( 'admin_body_class', array( &$this, 'admin_body_class' ), 999 );

		// Enqueue styles
		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );
		add_action( 'admin_print_styles', array( &$this, 'enqueue_styles' ) );

		// Removes the rude WP logo menu item
		remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );

		// Removes the Search menu item
		remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );

                //restricting network menu to group sites only
                if(get_current_blog_id() !== 1 || is_admin()){
                    add_action( 'admin_bar_menu', array( $this, 'add_network_menu' ), 1 );
                    add_filter('body_class',array($this,'adminbar_special_body_class'));
                }

                if(get_current_blog_id() === 1){
                    //adjust the padding at the top of the page
                    add_action('wp_head',array($this,'admin_bar_html_update'),99999);
                } else {
                    //adjust the padding at the top of the page - group sites
                    add_action('wp_head',array($this,'admin_bar_group_sites_html_update'),99999);
                    //add meta tag for viewport (some of the themes lack this)
                    add_action('wp_head',array($this,'groups_sites_fix_for_mobile'));
                }

                //for top padding in admin
                add_action('admin_footer',array($this,'admin_bar_padding_in_admin'));

                //for hamburger menu on mobile
                add_action('admin_bar_menu',array($this,'openlab_hamburger_menu'),1);

		// Logged-in only
		if ( is_user_logged_in() ) {

                        //hamburger mol menu
                        add_action('admin_bar_menu',array($this,'openlab_hamburger_mol_menu'),1);

                        //remove the default mobile dashboard toggle, we need a custom one for this for styling purposes
                        remove_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );
                        add_action('admin_bar_menu',array($this,'custom_admin_bar_sidebar_toggle'),0);

                        if(get_current_blog_id() === 1 && !is_admin()){
                            add_action( 'admin_bar_menu', array( $this,'add_middle_group_for_mobile'), 200 );
                            add_action( 'admin_bar_menu', array( $this, 'add_mobile_mol_link' ), 9999 );
                        }

			add_action( 'admin_bar_menu', array( $this, 'add_my_openlab_menu' ), 2 );
			add_action( 'admin_bar_menu', array( $this, 'change_howdy_to_hi' ), 7 );
			add_action( 'admin_bar_menu', array( $this, 'prepend_my_to_my_openlab_items' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'remove_notifications_hook' ), 5 );

			// Don't show the My Sites menu
			remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );

			// Don't show the Edit Group or Edit Member menus
			remove_action( 'admin_bar_menu', 'bp_groups_group_admin_menu', 99 );
			remove_action( 'admin_bar_menu', 'bp_members_admin_bar_user_admin_menu', 99 );

			// Don't show the My Achievements menu item.
			remove_action( 'admin_bar_menu', 'dpa_admin_bar_menu' );

			// Add the notification menus
			add_action( 'admin_bar_menu', array( $this, 'add_invites_menu' ), 22 );
			add_action( 'admin_bar_menu', array( $this, 'add_messages_menu' ), 24 );
			add_action( 'admin_bar_menu', array( $this, 'add_activity_menu' ), 26 );

                        //customizations for site menu
                        remove_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
                        add_action('admin_bar_menu',array($this,'openlab_custom_admin_bar_site_menu'),30);

			add_action( 'admin_bar_menu', array( $this, 'maybe_remove_thisblog' ), 99 );

			add_action( 'admin_bar_menu', array( $this, 'remove_adduser' ), 9999 );

                        //removing the default account information item and menu so we can a custom Bootstrap-style one
                        remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
                        add_action('admin_bar_menu',array($this,'openlab_custom_my_account_item'),7);
                        remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
                        add_action('admin_bar_menu',array($this,'openlab_custom_my_account_menu'),0);

			add_action( 'admin_bar_menu', array( $this, 'add_logout_item' ), 9999 );
//			add_action( 'admin_bar_menu', array( $this, 'fix_logout_redirect' ), 10000 );
                        //creating custom menus for comments, new content, and editing

                        add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 40 );
                        add_action('admin_bar_menu',array($this,'add_custom_updates_menu'), 40);

                        if (!is_network_admin() && !is_user_admin()) {
                            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
                            remove_action('admin_bar_menu', 'wp_admin_bar_new_content_menu', 70);
                            add_action('admin_bar_menu',array($this,'add_dashboard_link'), 50);
                            add_action('admin_bar_menu',array($this,'add_custom_comments_menu'), 60);
                            add_action('admin_bar_menu',array($this,'add_custom_content_menu'), 70);
                        }

                        remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
                        add_action('admin_bar_menu',array($this,'add_custom_edit_menu'),80);
        } else {
			add_action( 'admin_bar_menu', array( $this, 'add_signup_item' ), 30 );
			add_action( 'admin_bar_menu', array( $this, 'fix_tabindex' ), 999 );
		}
	}

        function remove_bp_admin_bar_styles($styles){

            return array();
        }

        /**
         * Custom dashboard toggle on mobile
         */
        function custom_admin_bar_sidebar_toggle($wp_admin_bar){
            if ( is_admin() ) {

                $sr_text = __( 'Menu' );

                $hamburger = <<<HTML
                    <button type="button" class="navbar-toggle mobile-toggle">
                        <span class="sr-only">{$sr_text}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
HTML;

		$wp_admin_bar->add_menu( array(
			'id'    => 'menu-toggle',
			'title' => $hamburger,
			'href'  => '#',
		) );
            }
        }

 	/**
 	 * Add the main OpenLab menu
 	 */
 	function add_network_menu( $wp_admin_bar ) {
 		$wp_admin_bar->add_node( array(
 			/*'parent' => 'top-secondary',*/
			'id'     => 'openlab',
			'title'  => '<span class="openlab-open">Open</span>Lab', // Span is here in case you want to bold 'OPEN'
			'href'   => bp_get_root_domain(),
			'meta'	 => array(
				'tabindex' => 90,
                                'class' => 'admin-bar-menu hidden-xs',
			)
 		) );
                $this->openlab_menu_items('openlab');
 	}

        function openlab_menu_items($parent){
            global $wp_admin_bar;

                $wp_admin_bar->add_node(array(
                    'parent' => $parent,
                    'id' => 'home-'.$parent,
                    'title' => 'Home',
                    'href' => bp_get_root_domain(),
                    'meta' => array(
                        'class' => 'mobile-no-hover',
                    ),
                ));

                $wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'about-'.$parent,
			'title'  => 'About',
			'href'   => trailingslashit( bp_get_root_domain() . '/about' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'people-'.$parent,
			'title'  => 'People',
			'href'   => trailingslashit( bp_get_root_domain() . '/people' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'courses-'.$parent,
			'title'  => 'Courses',
			'href'   => trailingslashit( bp_get_root_domain() . '/courses' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'projects-'.$parent,
			'title'  => 'Projects',
			'href'   => trailingslashit( bp_get_root_domain() . '/projects' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'clubs-'.$parent,
			'title'  => 'Clubs',
			'href'   => trailingslashit( bp_get_root_domain() . '/clubs' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

 		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'portfolios-'.$parent,
			'title'  => 'Portfolios',
			'href'   => trailingslashit( bp_get_root_domain() . '/portfolios' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );

		$wp_admin_bar->add_node( array(
			'parent' => $parent,
			'id'     => 'help-'.$parent,
			'title'  => 'Help',
			'href'   => trailingslashit( bp_get_root_domain() . '/blog/help/openlab-help' ),
                        'meta' => array(
                            'class' => 'mobile-no-hover',
                        ),
 		) );
        }

         /**
         * The MOL link on mobile needs to sit between the hamburger menus and the logout link
         * So we'll need a third group for this (makes styling easier)
         */
        function add_middle_group_for_mobile($wp_admin_bar) {
            $wp_admin_bar->add_group(array(
                'id' => 'mobile-centered',
                'meta' => array(
                    'class' => 'ab-mobile-centered',
                ),
            ));
        }

    /**
         * Mol link on mobile
         */
        function add_mobile_mol_link($wp_admin_bar){
            $current_user = wp_get_current_user();

                //truncating to be on the safe side
                $username = $current_user->display_name;
                if(mb_strlen($username) > 50){
                    $username = substr($username,0,50).'...';
                }
                if(mb_strlen($username) > 12){
                    $username_small = substr($username,0,12).'...';
                } else {
                    $username_small = $username;
                }

                $howdy = '<span class="small-size">'.sprintf(__('Hi, %1$s'), $username).'</span>';
                $howdy_small = '<span class="very-small-size">'.sprintf(__('Hi, %1$s'), $username_small).'</span>';

 		$wp_admin_bar->add_menu( array(
                        'parent' => 'mobile-centered',
			'id'    => 'my-openlab-mobile',
			'title' => $howdy.$howdy_small,
			'href'  => bp_loggedin_user_domain(),
                        'meta'  => array(
                            'class' => 'visible-xs',
                        ),
		) );
        }

 	/**
 	 * Adds 'My OpenLab' menu
 	 */
 	function add_my_openlab_menu( $wp_admin_bar ) {

                $current_user = wp_get_current_user();

                $howdy = sprintf(__('Hi, %1$s'), $current_user->display_name);

 		$wp_admin_bar->add_node( array(
			'id'    => 'my-openlab',
			'title' => 'My OpenLab <span class="fa fa-caret-down"></span>',
			'href'  => bp_loggedin_user_domain(),
                        'meta'  => array(
                            'class' => 'admin-bar-menu',
                        ),
		) );
 	}

        /**
         * Hamurger menu (mobile only)
         */
        function openlab_hamburger_menu($wp_admin_bar){

            $hamburger = <<<HTML
                    <button type="button" class="navbar-toggle mobile-toggle direct-toggle network-menu" data-target="#wp-admin-bar-network-menu-mobile .ab-sub-wrapper" data-plusheight="19">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
HTML;
            $wp_admin_bar->add_node( array(
			'id'    => 'my-hamburger',
			'title' => $hamburger,
                        'meta'  => array(
                            'class' => 'visible-xs hamburger',
                        ),
		) );
//
            $wp_admin_bar->add_node( array(
			'id'    => 'network-menu-mobile',
			'title' => 'My OpenLab <span class="fa fa-caret-down"></span>',
                        'meta'  => array(
                            'class' => 'visible-xs mobile-menu admin-bar-menu',
                        ),
		) );

            $this->openlab_menu_items('network-menu-mobile');

        }

        /**
         * Hamurger menu (mobile only)
         */
        function openlab_hamburger_mol_menu($wp_admin_bar){

            $hamburger = <<<HTML
                    <button type="button" class="navbar-toggle mobile-toggle direct-toggle mol-menu" data-target="#wp-admin-bar-my-openlab .ab-sub-wrapper" data-plusheight="19">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
HTML;
            $wp_admin_bar->add_node( array(
			'id'    => 'my-hamburger-mol',
			'title' => $hamburger,
                        'meta'  => array(
                            'class' => 'visible-xs hamburger',
                        ),
		) );

        }

	/**
	 * Change 'Howdy' message to 'Hi'
	 */
	function change_howdy_to_hi( $wp_admin_bar ) {
		global $bp;
		$wp_admin_bar->add_node( array(
			'id'    => 'my-account',
			'title' => sprintf( "Hi, %s", $bp->loggedin_user->userdata->display_name ),
			'meta'	=> array()
		) );
	}

	/**
	 * Removes BP default "My" items, and builds our own
	 */
	function prepend_my_to_my_openlab_items( $wp_admin_bar ) {
		$nodes = $wp_admin_bar->get_nodes();
		$my_openlab_nodes = array();

		foreach( $nodes as $id => $node ) {
			if ( 'my-account-buddypress' == $node->parent ) {
				$wp_admin_bar->remove_node( $id );
				$my_openlab_nodes[] = $id;
			}
		}

		// Loop through one more time and remove submenus (those with a parent that is a
		// child of my-openlab)
		unset( $nodes );
		$nodes = $wp_admin_bar->get_nodes();
		foreach( $nodes as $id => $node ) {
			if ( in_array( $node->parent, $my_openlab_nodes ) ) {
				$wp_admin_bar->remove_node( $id );
			}
		}

		// Now add our menus
		// profile, courses, projects, clubs, portfolio, friends, messages, invitations, dashboard
		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-profile',
			'title'  => 'My Profile',
			'href'   => bp_loggedin_user_domain(),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item mobile-no-hover'
                        )
		) );

                $wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-settings',
			'title'  => 'My Settings',
			'href'   => trailingslashit( bp_loggedin_user_domain() . 'settings' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item mobile-no-hover'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-courses',
			'title'  => 'My Courses',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-courses' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item mobile-no-hover'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-projects',
			'title'  => 'My Projects',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-projects' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item mobile-no-hover'
                        )
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'my-openlab',
			'id'     => 'my-clubs',
			'title'  => 'My Clubs',
			'href'   => trailingslashit( bp_get_root_domain() . '/my-clubs' ),
                        'meta' => array(
                            'class' => 'admin-bar-menu-item mobile-no-hover'
                        )
		) );

		if ( bp_is_active( 'friends' ) ) {
			$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
			$request_count = openlab_admin_bar_counts(count( $request_ids ));
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-friends',
				'title'  => 'My Friends '.$request_count,
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item mobile-no-hover'
                                )
			) );
		}

		if ( bp_is_active( 'messages' ) ) {
                        $messages_count = openlab_admin_bar_counts(bp_get_total_unread_messages_count());
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-messages',
				'title'  => sprintf( 'My Messages %s', $messages_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item mobile-no-hover'
                                )
			) );
		}

		if ( bp_is_active( 'groups' ) ) {
			$invites = groups_get_invites_for_user();
			$invite_count = openlab_admin_bar_counts(isset( $invites['total'] ) ? (int) $invites['total'] : 0);
			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-invitations',
				'title'  => sprintf( 'My Invitations %s', $invite_count ),
				'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/invites' ),
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item mobile-no-hover'
                                )
			) );

                }

		// My Dashboard points to the my-sites.php Dashboard panel for this user. However,
		// this panel only works if looking at a site where the user has Dashboard-level
		// permissions. So we have to find a valid site for the logged in user.
		$primary_site_id = get_user_meta( bp_loggedin_user_id(), 'primary_blog', true );
		$primary_site_url = set_url_scheme( get_blog_option( $primary_site_id, 'siteurl' ) );

		if ( !empty( $primary_site_id ) && !empty( $primary_site_url ) ) {

			$wp_admin_bar->add_node( array(
				'parent' => 'my-openlab',
				'id'     => 'my-dashboard',
				'title'  => 'My Dashboard',
				'href'   => $primary_site_url . '/wp-admin/my-sites.php',
                                'meta' => array(
                                    'class' => 'admin-bar-menu-item mobile-no-hover'
                                )
			) );
		}
	}

	/**
	 * Remove the Notifications menu
	 *
	 * We have to do it in a function like this because of the way BP adds the menu in the first
	 * place
	 */
	function remove_notifications_hook( $wp_admin_bar ) {
		remove_action( 'admin_bar_menu', 'bp_members_admin_bar_notifications_menu', 90 );
	}

	/**
	 * Maybe remove the current blog menu
	 */
	function maybe_remove_thisblog( $wp_admin_bar ) {
		if ( !current_user_can( 'publish_posts' ) ) {
			$wp_admin_bar->remove_node( 'site-name' );
		}
	}

	/**
	 * Add the Invites menu (Friend Requests + Group Invitations)
	 */
	function add_invites_menu( $wp_admin_bar ) {
		if ( ! bp_is_active( 'friends' ) || ! bp_is_active( 'groups' ) ) {
			return;
		}

		// We need this data up front so we can provide counts
		$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
		$request_count = count( (array) $request_ids );

		$invites = groups_get_invites_for_user();
		$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;

		$total_count = openlab_admin_bar_counts($request_count + $invite_count,' sub-count');

		$wp_admin_bar->add_menu( array(
			'id' => 'invites',
			'title' => '<span class="toolbar-item-icon fa fa-user"></span>' . $total_count,
                        'meta' => array(
                          'class' => 'hidden-xs',
                        ),
		) );

		/**
		 * FRIEND REQUESTS
		 */

		// "Friend Requests" title
		$wp_admin_bar->add_node(array(
                    'parent' => 'invites',
                    'id' => 'friend-requests-title',
                    'title' => 'Friend Requests',
                    'meta' => array(
                        'class' => 'submenu-title bold'
                    )
                ));

        $members_args = array(
			'max'     => 0
		);

		$members_args['include'] = ! empty( $request_ids ) ? implode( ',', array_slice( $request_ids, 0, 3 ) ) : '0';

		if ( bp_has_members( $members_args ) ) {
			while ( bp_members() ) {
				bp_the_member();

				// avatar
				$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_member_link() . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.bp_get_member_name().'"/></a></div></div>';

				// name link
				$title .= '<div class="col-sm-18"><p class="item"><a class="bold" href="' . bp_get_member_link() . '">' . bp_get_member_name() . '</a></p>';

				// accept/reject buttons
				$title .= '<p class="actions clearfix"><a class="btn btn-primary link-btn accept" href="' . bp_get_friend_accept_request_link() . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="btn btn-default link-btn reject" href="' . bp_get_friend_reject_request_link() . '">' . __( 'Reject', 'buddypress' ) . '</a></p></div></div>';

				$wp_admin_bar->add_node( array(
					'parent' => 'invites',
					'id'     => 'friendship-' . bp_get_friend_friendship_id(),
					'title'  => $title,
					'meta'   => array(
						'class' => 'nav-content-item nav-friendship-request'
					)
				) );
			}

		} else {
			// The user has no friend requests
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'friend-requests-none',
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new friendship requests.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-no-items nav-content-item'
				)
			) );
		}

		/**
		 * INVITATIONS
		 */

                $title = 'Invitations';
                if ( !empty( $invites['groups'] )){
                    $title .= '<span class="see-all pull-right"><a class="regular" href="'.trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug()) . '/invites">See All Invites</a></span>';
                }
		// "Invitations" title
		$wp_admin_bar->add_node( array(
			'parent' => 'invites',
			'id'     => 'invitations-title',
			'title'  => $title,
                        'meta' => array(
                            'class' => 'submenu-title bold'
                        )
		) );

		$groups_args = array(
			'type'    => 'invites',
			'user_id' => bp_loggedin_user_id()
		);

		if ( !empty( $invites['groups'] ) ) {
			$group_counter = 0;
			foreach ( (array) $invites['groups'] as $group ) {
				if ( $group_counter < 3 ) {
					// avatar
					$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_group_permalink( $group ) . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => $group->id, 'object' => 'group', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '. stripslashes($group->name).'"/></a></div></div>' ;

					// name link
					$title .= '<div class="col-sm-18"><p class="item-title"><a class="bold" href="' . bp_get_group_permalink( $group ) . '">' . stripslashes($group->name) . '</a></p>';

					// accept/reject buttons
					$title .= '<p class="actions clearfix"><a class="btn btn-primary link-btn accept" href="' . bp_get_group_accept_invite_link( $group ) . '">' . __( 'Accept', 'buddypress' ) . '</a> &nbsp; <a class="btn btn-default link-btn reject" href="' . bp_get_group_reject_invite_link( $group ) . '">' . __( 'Reject', 'buddypress' ) . '</a></p></div></div>';

					$wp_admin_bar->add_node( array(
						'parent' => 'invites',
						'id'     => 'invitation-' . $group->id,
						'title'  => $title,
						'meta'   => array(
							'class' => 'nav-content-item nav-invitation'
						)
					) );
				}

				$group_counter++;
			}
		} else {
			// The user has no group invites
			$wp_admin_bar->add_node( array(
				'parent' => 'invites',
				'id'     => 'group-invites-none',
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new invitations.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-no-items nav-content-item'
				)
			) );
		}

	}

	/**
	 * Add the Messages menu
	 */
	function add_messages_menu( $wp_admin_bar ) {
		if ( ! bp_is_active( 'messages' ) ) {
			return;
		}

		$total_count = openlab_admin_bar_counts(bp_get_total_unread_messages_count(),' sub-count');

		$wp_admin_bar->add_menu( array(
			'id' => 'messages',
			'title' => '<span class="toolbar-item-icon fa fa-envelope"></span>' . $total_count,
                        'meta' => array(
                          'class' => 'hidden-xs',
                        ),
		) );

		// Only show the first 5
		$messages_counter = 0;

		$messages_args = array(
			'type' => 'unread'
		);
		if ( bp_has_message_threads( $messages_args ) ) {
			global $messages_template;

			while ( bp_message_threads() ) {
				bp_message_thread();

				if ( $messages_counter < 5 ) {
					// avatar
					$title = '<div class="row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => $messages_template->thread->last_sender_id, 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.$messages_template->thread->last_sender_id.'"/></a></div></div>';

					// subject
					$title .= '<div class="col-sm-18"><p class="item"><a class="bold" href="' . bp_get_message_thread_view_link() . '">' . bp_create_excerpt(bp_get_message_thread_subject(), 30) . '</a>';

					// last sender
					$title .= '<span class="last-sender"><a href="' . bp_core_get_user_domain( $messages_template->thread->last_sender_id ) . '">' . bp_core_get_user_displayname( $messages_template->thread->last_sender_id ) . '</a></span></p>';

					// date and time
					$title .= '<p class="message-excerpt">' .bp_format_time( strtotime( $messages_template->thread->last_message_date ) ).'<br />';

					// Message excerpt
					$title .= strip_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 75 ) ) . ' <a class="message-excerpt-see-more" href="' . bp_get_message_thread_view_link() . '">See More</a></p></div></div>';

					$wp_admin_bar->add_node( array(
						'parent' => 'messages',
						'id'     => 'message-' . bp_get_message_thread_id(),
						'title'  => $title,
						'meta'   => array(
							'class' => 'nav-content-item nav-message'
						)
					) );
				}

				$messages_counter++;

			}

		} else {
			// The user has no unread messages
			$wp_admin_bar->add_node( array(
				'parent' => 'messages',
				'id'     => 'messages-none',
				'title'  => '<div class="row"><div class="col-sm-24"><p>No new messages.</p></div></div>',
				'meta'   => array(
					'class' => 'nav-content-item nav-no-items'
				)
			) );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'messages',
			'id'     => 'messages-more',
			'title'  => '<span>See All Messages</span>',
			'href'   => trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() ),
                        'meta' => array(
                            'class' => 'menu-bottom-link'
                        )
		) );
	}

	/**
	 * Add the Activity menu (My Group activity)
	 */
	function add_activity_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id' => 'activity',
			'title' => '<span class="toolbar-item-name fa fa-bullhorn"></span>',
                        'meta' => array(
                          'class' => 'hidden-xs',
                        ),
		) );

		$activity_args = array(
			'user_id' => bp_loggedin_user_id(),
			'scope'   => 'groups',
			'max'     => 5
		);

		if ( bp_has_activities( $activity_args ) ) {
                    global $activities_template;
			while ( bp_activities() ) {
				bp_the_activity();

				// avatar
				$title = '<div class="row activity-row"><div class="col-sm-6"><div class="item-avatar"><a href="' . bp_get_activity_user_link() . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => bp_get_activity_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '.  bp_get_activity_user_id().'"/></a></div></div>';

				// action
				$title .= '<div class="col-sm-18">';

                                //the things we do...
                                $action_output = '';
                                $action_output_raw = $activities_template->activity->action;
                                $action_output_ary = explode('<a',$action_output_raw);
                                $count = 0;
                                foreach ($action_output_ary as $action_redraw){
                                    if(!ctype_space($action_redraw)){
                                        $class = ($count == 0 ? 'activity-user' : 'activity-action');
                                        $action_output .= '<a class="'.$class.'"'.$action_redraw;
                                        $count++;
                                    }
                                }

                                $title .= '<p class="item inline-links hyphenate">'.$action_output.'</p>';
                                $title .= '<p class="item">'.bp_insert_activity_meta('').'</p>';
                                $title .= '</div></div>';

				$wp_admin_bar->add_node( array(
					'parent' => 'activity',
					'id'     => 'activity-' . bp_get_activity_id(),
					'title'  => $title,
					'meta'   => array(
						'class' => 'nav-content-item nav-activity'
					)
				) );
			}
		}

		$link = trailingslashit( bp_loggedin_user_domain() . bp_get_activity_slug() );
		if ( bp_is_active( 'groups' ) ) {
			$link .= trailingslashit( bp_get_groups_slug() );
		}

		// "Go to Inbox" Makes sense that users should always see this
		$wp_admin_bar->add_node( array(
			'parent' => 'activity',
			'id'     => 'activity-more',
			'title'  => '<span>See All Activity</span>',
			'href'   => $link,
                        'meta' => array(
                                'class' => 'menu-bottom-link'
                            )
		) );
	}

        function openlab_custom_admin_bar_site_menu($wp_admin_bar){
            // Don't show for logged out users.
            if ( ! is_user_logged_in() )
                    return;

            // Show only when the user is a member of this site, or they're a super admin.
            if ( ! is_user_member_of_blog() && ! is_super_admin() )
                    return;

            $blogname = get_bloginfo('name');

            if ( empty( $blogname ) )
                    $blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );

            if ( is_network_admin() ) {
                    $blogname = sprintf( __('Network Admin: %s'), esc_html( get_current_site()->site_name ) );
            } elseif ( is_user_admin() ) {
                    $blogname = sprintf( __('Global Dashboard: %s'), esc_html( get_current_site()->site_name ) );
            }

            $title = wp_html_excerpt( $blogname, 40, '&hellip;' );
            $title_short = wp_html_excerpt( $blogname, 15, '&hellip;' );

            $wp_admin_bar->add_menu( array(
                    'id'    => 'site-name',
                    'title' => '<span class="hidden-sm hidden-md">'.$title.' <span class="fa fa-caret-down"></span></span><span class="hidden-sm visible-md">'.$title_short.' <span class="fa fa-caret-down"></span></span><span class="fa fa-desktop visible-sm"></span>',
                    'href'  => is_admin() ? home_url( '/' ) : admin_url(),
                    'meta' => array(
                        'class' => 'admin-bar-menu hidden-xs',
                    ),
            ) );

            // Create submenu items.

            if ( is_admin() ) {
                    // Add an option to visit the site.
                    $wp_admin_bar->add_menu( array(
                            'parent' => 'site-name',
                            'id'     => 'view-site',
                            'title'  => __( 'Visit Site' ),
                            'href'   => home_url( '/' ),
                    ) );

                    if ( is_blog_admin() && is_multisite() && current_user_can( 'manage_sites' ) ) {
                            $wp_admin_bar->add_menu( array(
                                    'parent' => 'site-name',
                                    'id'     => 'edit-site',
                                    'title'  => __( 'Edit Site' ),
                                    'href'   => network_admin_url( 'site-info.php?id=' . get_current_blog_id() ),
                            ) );
                    }

            } else {
                    // We're on the front end, link to the Dashboard.
                    $wp_admin_bar->add_menu( array(
                            'parent' => 'site-name',
                            'id'     => 'dashboard',
                            'title'  => __( 'Dashboard' ),
                            'href'   => admin_url(),
                    ) );

                    // Add the appearance submenu items.
                    wp_admin_bar_appearance_menu( $wp_admin_bar );
            }
        }

        function add_custom_edit_menu( $wp_admin_bar ) {
                global $tag, $wp_the_query;

                if ( is_admin() ) {
                        $current_screen = get_current_screen();
                        $post = get_post();

                        if ( 'post' == $current_screen->base
                                && 'add' != $current_screen->action
                                && ( $post_type_object = get_post_type_object( $post->post_type ) )
                                && current_user_can( 'read_post', $post->ID )
                                && ( $post_type_object->public )
                                && ( $post_type_object->show_in_admin_bar ) )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'view',
                                        'title' => '<span class="fa fa-eye "></span>',
                                        'href' => get_permalink( $post->ID )
                                ) );
                        } elseif ( 'edit-tags' == $current_screen->base
                                && isset( $tag ) && is_object( $tag )
                                && ( $tax = get_taxonomy( $tag->taxonomy ) )
                                && $tax->public )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'view',
                                        'title' => '<span class="fa fa-eye "></span>',
                                        'href' => get_term_link( $tag )
                                ) );
                        }
                } else {
                        $current_object = $wp_the_query->get_queried_object();

                        if ( empty( $current_object ) )
                                return;

                        if ( ! empty( $current_object->post_type )
                                && ( $post_type_object = get_post_type_object( $current_object->post_type ) )
                                && current_user_can( 'edit_post', $current_object->ID )
                                && $post_type_object->show_ui && $post_type_object->show_in_admin_bar )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'edit',
                                        'title' => '<span class="fa fa-pencil"></span>',
                                        'href' => get_edit_post_link( $current_object->ID ),
                                        'meta' => array(
                                            'class' => 'hidden-xs',
                                        )
                                ) );
                        } elseif ( ! empty( $current_object->taxonomy )
                                && ( $tax = get_taxonomy( $current_object->taxonomy ) )
                                && current_user_can( $tax->cap->edit_terms )
                                && $tax->show_ui )
                        {
                                $wp_admin_bar->add_menu( array(
                                        'id' => 'edit',
                                        'title' => '<span class="fa fa-pencil"></span>',
                                        'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy ),
                                        'meta' => array(
                                            'class' => 'hidden-xs',
                                        )
                                ) );
                        }
                }
        }

    /**
     * Custom content menu
     * @param type $wp_admin_bar
     * @return type
     */
    function add_custom_content_menu($wp_admin_bar) {
        $actions = array();

	$cpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );

	if ( isset( $cpts['post'] ) && current_user_can( $cpts['post']->cap->create_posts ) )
		$actions[ 'post-new.php' ] = array( $cpts['post']->labels->name_admin_bar, 'new-post' );

	if ( isset( $cpts['attachment'] ) && current_user_can( 'upload_files' ) )
		$actions[ 'media-new.php' ] = array( $cpts['attachment']->labels->name_admin_bar, 'new-media' );

	if ( current_user_can( 'manage_links' ) )
		$actions[ 'link-add.php' ] = array( _x( 'Link', 'add new from admin bar' ), 'new-link' );

	if ( isset( $cpts['page'] ) && current_user_can( $cpts['page']->cap->create_posts ) )
		$actions[ 'post-new.php?post_type=page' ] = array( $cpts['page']->labels->name_admin_bar, 'new-page' );

	unset( $cpts['post'], $cpts['page'], $cpts['attachment'] );

	// Add any additional custom post types.
	foreach ( $cpts as $cpt ) {
		if ( ! current_user_can( $cpt->cap->create_posts ) )
			continue;

		$key = 'post-new.php?post_type=' . $cpt->name;
		$actions[ $key ] = array( $cpt->labels->name_admin_bar, 'new-' . $cpt->name );
	}
	// Avoid clash with parent node and a 'content' post type.
	if ( isset( $actions['post-new.php?post_type=content'] ) )
		$actions['post-new.php?post_type=content'][1] = 'add-new-content';

	if ( current_user_can( 'create_users' ) || current_user_can( 'promote_users' ) )
		$actions[ 'user-new.php' ] = array( _x( 'User', 'add new from admin bar' ), 'new-user' );

	if ( ! $actions )
		return;

	$title = '<span class="fa fa-plus-circle hidden-xs"></span><span class="ab-icon dashicon-icon visible-xs"></span>';

	$wp_admin_bar->add_menu( array(
		'id'    => 'new-content',
		'title' => $title,
		'href'  => admin_url( current( array_keys( $actions ) ) ),
		'meta'  => array(
			'title' => _x( 'Add New', 'admin bar menu group label' ),
                        'class' => 'mobile-no-hover admin-bar-menu',
		),
	) );

	foreach ( $actions as $link => $action ) {
		list( $title, $id ) = $action;

		$wp_admin_bar->add_menu( array(
			'parent'    => 'new-content',
			'id'        => $id,
			'title'     => $title,
			'href'      => admin_url( $link ),
                        'meta' => array(
                                'class' => 'admin-bar-menu-item'
                            )
		) );
	}
    }

    function add_dashboard_link($wp_admin_bar) {
        global $bp;

        $current_screen = new stdClass();
        $current_screen->base = '';

        if (is_admin()) {
            $current_screen = get_current_screen();
        }

        if (current_user_can('edit_published_posts') && $current_screen->base !== 'my-sites') {

            $title = (is_admin() ? '<span class="ab-icon dashicon-icon dashicons dashicons-admin-home"></span>' : '<span class="ab-icon dashicon-icon dashicons dashicons-dashboard"></span>');

            $href = (is_admin() ? get_site_url() : admin_url());

            $wp_admin_bar->add_menu(array(
                'id' => 'dashboard-link',
                'title' => $title,
                'href' => $href,
                'meta' => array(
                    'title' => _x('Dashboard', 'admin bar menu group label'),
                    'class' => 'mobile-no-hover visible-xs',
                ),
            ));
        }
    }

    function add_custom_updates_menu($wp_admin_bar) {
        $update_data = wp_get_update_data();

        if (!$update_data['counts']['total'])
            return;

        $title = '<span> ' . number_format_i18n($update_data['counts']['total']) . '</span>';
        $title .= '<span class="screen-reader-text">' . $update_data['title'] . '</span>';

        $icon = '<span class="fa fa fa-cogs"></span>';

        $wp_admin_bar->add_menu(array(
            'id' => 'updates',
            'title' => $icon.$title,
            'href' => network_admin_url('update-core.php'),
            'meta' => array(
                'title' => $update_data['title'],
                'class' => 'mobile-no-hover',
            ),
        ));
    }

    /**
     * Custom comments menu
     * @param type $wp_admin_bar
     * @return type
     */
    function add_custom_comments_menu($wp_admin_bar) {
        if (!current_user_can('edit_posts'))
            return;

        $awaiting_mod = wp_count_comments();
        $awaiting_mod = $awaiting_mod->moderated;
        $awaiting_count = openlab_admin_bar_counts(number_format_i18n($awaiting_mod),' sub-count');
        $awaiting_title = esc_attr(sprintf(_n('%s comment awaiting moderation', '%s comments awaiting moderation', $awaiting_mod), number_format_i18n($awaiting_mod)));

        $icon = '<span class="fa fa-comment hidden-xs"></span><span class="ab-icon dashicon-icon visible-xs"></span>';
        $wp_admin_bar->add_menu(array(
            'id' => 'comments',
            'title' => $icon,
            'href' => admin_url('edit-comments.php'),
            'meta' => array(
                'title' => $awaiting_title,
                'class' => 'mobile-no-hover',
                    ),
        ));
    }

    /**
	 * Remove + > User
	 */
	public function remove_adduser( $wp_admin_bar ) {
		$wp_admin_bar->remove_menu( 'new-user' );
	}

	/**
	 * Add a 'Log Out' link to the far right
	 */
	function add_logout_item( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'top-secondary',
			'id'     => 'top-logout',
			'href'   => add_query_arg( 'redirect_to', bp_get_root_domain(), wp_logout_url() ),
			'title'  => 'Log Out',
                        'meta'   => array(
                            'class' => 'bold',
                        ),
		) );
	}

        function openlab_custom_my_account_item($wp_admin_bar) {

                $user_id = get_current_user_id();
                $current_user = wp_get_current_user();
                $profile_url = get_edit_profile_url($user_id);

                if (!$user_id)
                    return;

                $howdy = sprintf(__('Hi, %1$s'), $current_user->display_name);
                $howdy_short = sprintf(__('Hi, %1$s'), wp_html_excerpt( $current_user->display_name, 10, '&hellip;' ));

                $wp_admin_bar->add_menu(array(
                    'id' => 'my-account',
                    'parent' => 'top-secondary',
                    'title' => '<span class="hidden-sm">'.$howdy.'</span><span class="visible-sm">'.$howdy_short.'</span>',
                    'href' => $profile_url,
                    'meta' => array(
                        'class' => 'hidden-xs',
                        'title' => __('My Account'),
                    ),
                ));
        }

        /**
         * Custom account menu
         * @param type $wp_admin_bar
         * @return type
         */
        function openlab_custom_my_account_menu($wp_admin_bar){
            $user_login = '';
            $user_id      = get_current_user_id();
            $current_user = wp_get_current_user();
            $profile_url  = get_edit_profile_url( $user_id );

            if ( ! $user_id )
                    return;

            if ( $current_user->display_name !== $current_user->user_login )
                    $user_login = "<br /><span class='username'>{$current_user->user_login}</span>";

            // avatar
            $user_info = '<div class="row"><div class="col-sm-8"><div class="item-avatar"><a href="' . $profile_url . '"><img class="img-responsive" src ="'.bp_core_fetch_avatar(array('item_id' => $user_id, 'object' => 'member', 'type' => 'full', 'html' => false)).'" alt="Profile picture of '. $current_user->display_name.'"/></a></div></div>' ;

            // name link
            $user_info .= '<div class="col-sm-16"><p class="item-title"><a href="' . $profile_url . '"><span class="display-name">'.$current_user->display_name.$user_login.'</a></p>';

            // accept/reject buttons
            $user_info .= '<p class="actions clearfix inline-links"><a href="' . $profile_url . '">' . __( 'Edit My Profile' ) . '</a> | <a href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></p></div></div>';

            $wp_admin_bar->add_node( array(
                    'parent' => 'my-account',
                    'id'     => 'user-listing',
                    'title'  => $user_info,
                    'meta'   => array(
                            'class' => 'nav-content-item',
                    ),
            ) );
        }

    /**
	 * Fix the logout redirect
	 */
	function fix_logout_redirect( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id'     => 'logout',
			'href'   => add_query_arg( 'redirect_to', bp_get_root_domain(), wp_logout_url() )
		) );
	}

	/**
	 * Adds the Sign Up item
	 */
	function add_signup_item( $wp_admin_bar ) {
		// Remove so we can replace in the right order
		$signup = $wp_admin_bar->get_node( 'bp-register' );
		$login  = $wp_admin_bar->get_node( 'bp-login' );

		$wp_admin_bar->remove_node( 'bp-register' );
		$wp_admin_bar->remove_node( 'bp-login' );

		// Change the title of the signup node
		$signup->title = 'Sign Up';

		// Move them both to top-secondary, to appear at the right
		$signup->parent = 'top-secondary';
		$login->parent  = 'top-secondary';

		// Re-add
		$wp_admin_bar->add_node( (array) $signup );
		$wp_admin_bar->add_node( (array) $login );
	}

	function fix_tabindex( $wp_admin_bar ) {
		//var_Dump( $wp_admin_bar );
		$wp_admin_bar->add_menu( array(
			'id'    => 'bp-login',
			'meta'	=> array(
				'tabindex' => 101
			)
		) );

		$wp_admin_bar->add_menu( array(
			'id'    => 'bp-register',
			'meta'	=> array(
				'tabindex' => 100
			)
		) );
	}

	function body_class( $body_class ) {
		if ( bp_is_root_blog() ) {
			$body_class[] = 'openlab-main';
		} else {
			$body_class[] = 'openlab-member';
		}

		return $body_class;
	}

	function admin_body_class( $body_class ) {
		if ( bp_is_root_blog() ) {
			$body_class .= ' openlab-main ';
		} else {
			$body_class .= ' openlab-member ';
		}

		return $body_class;
	}

	function enqueue_styles() {
            global $wpdb;

            //getting the theme folder for the main site
            $main_site_theme = $wpdb->get_var('SELECT option_value FROM wp_options WHERE option_name = "template"');

            wp_register_style('google-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic', array(), '2014', 'all');
            wp_enqueue_style('google-open-sans');

            $openlab_theme_link = get_site_url(1,'wp-content/themes/'). $main_site_theme . '/css/font-awesome.min.css';
            $openlab_theme_link = set_url_scheme($openlab_theme_link);

            //making sure dashicons fire up for front end
            if(!is_admin()){
                wp_register_style('dashicons','/wp-includes/css/dashicons.min.css');
                wp_enqueue_style('dashicons');
            }

            //registering font-awesome here so it can be used on the admin bar and on the main site
            wp_register_style('font-awesome', $openlab_theme_link, array(), '20130604', 'all');
            wp_enqueue_style('font-awesome');
            //custom admin bar styles

            $adminbar_custom_url = WP_CONTENT_URL . '/mu-plugins/css/admin-bar-custom.css';
            $adminbar_custom_url = set_url_scheme( $adminbar_custom_url );
            $openlab_toolbar_url = WP_CONTENT_URL . '/mu-plugins/css/openlab-toolbar.css';
            $openlab_toolbar_url = set_url_scheme( $openlab_toolbar_url );

            wp_enqueue_style( 'admin-bar-custom', $adminbar_custom_url,array('font-awesome') );
            wp_enqueue_style( 'openlab-toolbar', $openlab_toolbar_url,array('font-awesome') );
        }

        function adminbar_special_body_class($classes){

            $classes[] = 'adminbar-special';

            return $classes;

        }

        function admin_bar_html_update(){
            ?>

            <style type="text/css" media="screen">
                    html { margin-top: 0px !important; }
                    * html body { margin-top: 0px !important; }
                    @media screen and ( max-width: 782px ) {
                            html { margin-top: 0px !important; }
                            * html body { margin-top: 0px !important; }
                    }
            </style>

            <?php
        }

        function admin_bar_group_sites_html_update(){
         ?>

            <style type="text/css" media="screen">
                    html { margin-top: 0px !important; }
                    * html body { margin-top: 0px !important; }
                    @media screen and ( max-width: 782px ) {
                            html { margin-top: 0px !important; }
                            * html body { margin-top: 0px !important; }
                    }
            </style>

            <?php
        }

        function admin_bar_padding_in_admin() {
        ?>

                    <style type="text/css" media="screen">
                            html.wp-toolbar {
                                padding-top: 0;
                            }
                            html.wp-toolbar #wpcontent,
                            html.wp-toolbar #adminmenuwrap{
                                    padding-top: 80px;
                                }
                                @media (max-width: 767px){
                                            html.wp-toolbar #wpcontent,
                                            html.wp-toolbar #adminmenuwrap{
                                                padding-top: 120px;
                                            }
                                            html.wp-toolbar #wpbody{
                                                padding-top: 0;
                                            }
                                        }
                    </style>

        <?php
    }

    function groups_sites_fix_for_mobile(){
        ?>

            <meta name="viewport" content="width=device-width">

        <?php
    }

}

function openlab_admin_bar_counts($count, $pull_right = ' pull-right'){

    if($count < 1){
        return '';
    }else{
        return '<span class="toolbar-item-count count-' . $count .$pull_right. '">'.$count.'</span>';
    }

}

// Themes like TwentyTen don't use jQuery by default, so let's enqueue it!
// added by r-a-y (05.16.11)
function cac_adminbar_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'cac_adminbar_enqueue_scripts' );

/**
 * JS to toggle adminbar login form
 *
 * JS is inline to reduce a server request
 */
function cac_adminbar_js() {
	$request_uri = $_SERVER['REQUEST_URI'];
?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {

		var loginform = '<div class="ab-sub-wrapper"><div class="ab-submenu"><form name="login-form" style="display:none;" id="sidebar-login-form" class="standard-form form" action="<?php echo site_url( "wp-login.php", "login_post" ) ?>" method="post"><label><?php _e( "Username", "buddypress" ) ?><br /><input type="text" name="log" id="sidebar-user-login" class="input form-control" value="" /></label><br /><label><?php _e( "Password", "buddypress" ) ?><br /><input class="form-control" type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label><p class="forgetmenot checkbox"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( "Keep Me Logged In", "buddypress" ) ?></label></p><input type="hidden" name="redirect_to" value="<?php echo bp_get_root_domain() . $request_uri; ?>" /><input type="submit" name="wp-submit" id="sidebar-wp-submit" class="btn btn-primary" value="<?php _e("Log In"); ?>" tabindex="100" /><a href="<?php echo wp_lostpassword_url(); ?>" class="lost-pw">Forgot Password?</a></form></div></div>';

		$("#wp-admin-bar-bp-login").append(loginform);

		$("#wp-admin-bar-bp-login > a").click(function(){
			$(".ab-submenu #sidebar-login-form").toggle(400,function(){
                            $(".ab-submenu #sidebar-user-login").focus();
                        });
			$(this).toggleClass("login-click");
			return false;
		});
	});
	</script>

<?php
}
add_action( 'wp_footer', 'cac_adminbar_js', 999 );

/**
 * The following functions wrap the admin bar in an 'oplb-bs' class to isolate bootstrap styles from the rest of the page
 * This is to avoid styling conflicts on the admin pages and group sites
 * FYI: due to an undiagnosed issue in the LESS compilation, the class has to be wrapped twice to work; definitely will try to fix this in the future
 */
function openlab_wrap_adminbar_top(){
    if(get_current_blog_id() !== 1 || is_admin()):

        $admin_class = (is_admin() ? ' admin-area' : '');
    ?>
        <div class="oplb-bs adminbar-manual-bootstrap<?php echo $admin_class ?>"><div class="oplb-bs adminbar-manual-bootstrap<?= $admin_class ?>">
    <?php else : ?>
        <div class="oplb-bs"><div class="oplb-bs">
    <?php endif;
}

add_action('wp_before_admin_bar_render','openlab_wrap_adminbar_top');

function openlab_wrap_adminbar_bottom(){
    ?>
        </div></div><!--oplb-bs-->
        <div id="behind_menu_background"></div>
    <?php
}

add_action('wp_after_admin_bar_render','openlab_wrap_adminbar_bottom');
