<?php
/*
Plugin Name: Captcha
Plugin URI: https://wordpress.org/plugins/captcha/
Description: This plugin allows you to implement super security captcha form into web forms.
Author: simplywordpress
Text Domain: captcha
Domain Path: /languages
Version: 4.4.5
Author URI: https://wordpress.org/plugins/captcha/
License: GPLv2 or later
*/

/*  © Copyright 2017
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
function hctpc_enqueue_backend_script() {
        wp_register_script( 'hctpc_backend_script', plugin_dir_url( __FILE__ ) . 'js/back_end_script.js', false, '1.0.0' );
		wp_enqueue_script( 'hctpc_backend_script' );
}
add_action( 'admin_enqueue_scripts', 'hctpc_enqueue_backend_script' );

if ( ! function_exists( 'cptch_admin_menu' ) ) {
	function cptch_admin_menu() {
	add_menu_page( __( 'Captcha Settings', 'captcha' ), 'Captcha Dashboard', 'manage_options', 'cptc_dashboard', 'cptch_dashboard_page' );
	add_submenu_page('cptc_dashboard','Dashboard','Settings','manage_options','captcha.php','cptch_settings_page');
	add_submenu_page('cptc_dashboard','Dashboard','Custom Request','manage_options','cptc_dashboard&amp;action=custom_requests','cptch_dashboard_page');

	}
}

// dashboard
if ( ! function_exists( 'cptch_dashboard_page' ) ) {
	function cptch_dashboard_page()
	{
		$plugin_basename  = plugin_basename( __FILE__ );
		$is_network       = is_network_admin();
		require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
		require_once( dirname( __FILE__ ) . '/includes/dashboard_page.php' );

		$page = new Cptch_dashboard($plugin_basename);
		?>
            <div class="wrap cptch_dash_page">
                <h1><?php _e( '', 'captcha' ); // do not remove this ?></h1>
                <?php
                require_once( dirname( __FILE__ ) . '/includes/dashboard_html.php' );
                if ( isset( $_GET['action'] ) ) {
                    switch( $_GET['action'] ) {
                        case 'simply_secure':
                        break;
                        case 'custom_requests':
                                ?>
                                    <script>window.location.href="https://mysimplewp.com/contact-us/"</script>
                                <?php
                                break;
                        default:
                        break;
                    }
                } else {
                    $page = new Cptch_dashboard( $plugin_basename );
                    if (isset($page) and  is_object( $page ) )
                    $page->display_content_dashboard_tab();
                }
                 ?>
		  </div>
	<?php
	}
}
/* add help tab */
if ( ! function_exists( 'cptch_add_tabs' ) ) {
	function cptch_add_tabs() {
		$args = array(
			'id'      => 'cptch',
			'section' => '200538879'
		);
		bws_help_tab( get_current_screen(), $args );
	}
}

if ( ! function_exists( 'cptch_plugins_loaded' ) ) {
	function cptch_plugins_loaded() {
		/* Internationalization */
		load_plugin_textdomain( 'captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'cptch_init' ) ) {
	function cptch_init() {
		global $cptch_plugin_info, $cptch_ip_in_whitelist, $cptch_options;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( ! $cptch_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cptch_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $cptch_plugin_info, '3.8' );

		$is_admin = is_admin() && ! defined( 'DOING_AJAX' );

		/* Call register settings function */
		if ( ! $is_admin || ( isset( $_GET['page'] ) && "captcha.php" == $_GET['page'] ) )
			cptch_settings();

		if ( $is_admin )
			return;

		$user_loggged_in       = is_user_logged_in();
		$cptch_ip_in_whitelist = cptch_whitelisted_ip();

		/*
		 * Add the CAPTCHA to the WP login form
		 */
		if ( $cptch_options['forms']['wp_login']['enable'] ) {
			add_action( 'login_form', 'cptch_login_form' );
			if ( ! $cptch_ip_in_whitelist )
				add_filter( 'authenticate', 'cptch_login_check', 21, 1 );
		}

		/*
		 * Add the CAPTCHA to the WP register form
		 */
		if ( $cptch_options['forms']['wp_register']['enable'] ) {
			add_action( 'register_form', 'cptch_register_form' );
			add_action( 'signup_extra_fields', 'wpmu_cptch_register_form' );
			add_action( 'signup_blogform', 'wpmu_cptch_register_form' );

			if ( ! $cptch_ip_in_whitelist ) {
				add_filter( 'registration_errors', 'cptch_register_check', 9, 1 );
				if ( is_multisite() ) {
					add_filter( 'wpmu_validate_user_signup', 'cptch_register_validate' );
					add_filter( 'wpmu_validate_blog_signup', 'cptch_register_validate' );
				}
			}
		}

		/*
		 * Add the CAPTCHA into the WP lost password form
		 */
		if ( $cptch_options['forms']['wp_lost_password']['enable'] ) {
			add_action( 'lostpassword_form', 'cptch_lostpassword_form' );
			if ( ! $cptch_ip_in_whitelist )
				add_filter( 'allow_password_reset', 'cptch_lostpassword_check' );
		}

		/*
		 * Add the CAPTCHA to the WP comments form
		 */
		if ( cptch_captcha_is_needed( 'wp_comments', $user_loggged_in ) ) {
			global $wp_version;
			/*
			 * Common hooks to add necessary actions for the WP comment form,
			 * but some themes don't contain these hooks in their comments form templates
			 */
			add_action( 'comment_form_after_fields', 'cptch_comment_form_wp3', 1 );
			add_action( 'comment_form_logged_in_after', 'cptch_comment_form_wp3', 1 );
			/*
			 * Try to display the CAPTCHA before the close tag </form>
			 * in case if hooks 'comment_form_after_fields' or 'comment_form_logged_in_after'
			 * are not included to the theme comments form template
			 */
			add_action( 'comment_form', 'cptch_comment_form' );
			if ( ! $cptch_ip_in_whitelist )
				add_filter( 'preprocess_comment', 'cptch_comment_post' );
		}

		/*
		 * Add the CAPTCHA to the Contact Form by mysimplewp plugin forms
		 */
		if ( $cptch_options['forms']['bws_contact']['enable'] ) {
			add_filter( 'cntctfrmpr_display_captcha', 'cptch_custom_form', 10, 2 );
			add_filter( 'cntctfrm_display_captcha', 'cptch_custom_form', 10, 2 );
			if ( ! $cptch_ip_in_whitelist ) {
				add_filter( 'cntctfrm_check_form', 'cptch_check_bws_contact_form' );
				add_filter( 'cntctfrmpr_check_form', 'cptch_check_bws_contact_form' );
			}
		}
	}
}

if ( ! function_exists ( 'cptch_admin_init' ) ) {
	function cptch_admin_init() {
		global $bws_plugin_info, $cptch_plugin_info, $bws_shortcode_list;
		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '75', 'version' => $cptch_plugin_info["Version"] );

		/**
		 * add CAPTCHA to global $bws_shortcode_list
		 * @since 4.2.3
		 */
		$bws_shortcode_list['cptch'] = array( 'name' => 'Captcha' );
	}
}

if ( ! function_exists( 'cptch_create_table' ) ) {
	function cptch_create_table() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}cptch_whitelist` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`ip` CHAR(31) NOT NULL,
			`ip_from_int` BIGINT,
			`ip_to_int` BIGINT,
			`add_time` DATETIME,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}cptch_images` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` CHAR(100) NOT NULL,
			`package_id` INT NOT NULL,
			`number` INT NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}cptch_packages` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` CHAR(100) NOT NULL,
			`folder` CHAR(100) NOT NULL,
			`settings` LONGTEXT NOT NULL,
			`user_settings` LONGTEXT NOT NULL,
			`add_time` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		/* remove unnecessary columns from 'whitelist' table */
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}cptch_whitelist` LIKE 'ip_from'" );
		if ( 0 < $column_exists )
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}cptch_whitelist` DROP `ip_from`;" );
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}cptch_whitelist` LIKE 'ip_to'" );
		if ( 0 < $column_exists )
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}cptch_whitelist` DROP `ip_to`;" );
		/* add new columns to 'whitelist' table */
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}cptch_whitelist` LIKE 'add_time'" );
		if ( 0 == $column_exists )
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}cptch_whitelist` ADD `add_time` DATETIME;" );
		/* add unique key */
		if ( 0 == $wpdb->query( "SHOW KEYS FROM `{$wpdb->prefix}cptch_whitelist` WHERE Key_name='ip'" ) )
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}cptch_whitelist` ADD UNIQUE(`ip`);" );
		/* remove not necessary indexes */
		$indexes = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}cptch_whitelist` WHERE Key_name Like '%ip_%'" );
		if ( ! empty( $indexes ) ) {
			$query = "ALTER TABLE `{$wpdb->prefix}cptch_whitelist`";
			$drop = array();
			foreach( $indexes as $index )
				$drop[] = " DROP INDEX {$index->Key_name}";
			$query .= implode( ',', $drop );
			$wpdb->query( $query );
		}

		/**
		 * add new columns to the 'cptch_packages' table
		 * @since 4.2.3
		 */
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->base_prefix}cptch_packages` LIKE 'settings'" );
		if ( 0 == $column_exists ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->base_prefix}cptch_packages` ADD (`settings` LONGTEXT NOT NULL, `user_settings` LONGTEXT NOT NULL, `add_time` DATETIME NOT NULL );" );
			$wpdb->update(
				"{$wpdb->base_prefix}cptch_packages",
				array( 'add_time' => current_time( 'mysql' ) ),
				array( 'add_time' => '0000-00-00 00:00:00' )
			);
		}
	}
}

/**
 * Activation plugin function
 */
if ( ! function_exists( 'cptch_plugin_activate' ) ) {
	function cptch_plugin_activate( $networkwide ) {
		global $wpdb;
		/* Activation function for network, check if it is a network activation - if so, run the activation function for each blog id */
		if ( function_exists( 'is_multisite' ) && is_multisite() && $networkwide ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				cptch_create_table();
				cptch_settings();
			}
			switch_to_blog( $old_blog );
			return;
		}
		cptch_create_table();
		cptch_settings();
	}
}

/* Register settings function */
if ( ! function_exists( 'cptch_settings' ) ) {
	function cptch_settings() {
		global $cptch_options, $cptch_plugin_info, $wpdb;

		if ( empty( $cptch_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cptch_plugin_info = get_plugin_data( dirname(__FILE__) . '/captcha.php' );
		}

		$db_version = '1.4';
		$need_update = false;

		$cptch_options = get_option( 'cptch_options' );

		if ( empty( $cptch_options ) ) {
			if ( ! function_exists( 'cptch_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			$cptch_options = cptch_get_default_options();
			update_option( 'cptch_options', $cptch_options );
		}

		if (
			empty( $cptch_options['plugin_option_version'] ) ||
			$cptch_options['plugin_option_version'] != $cptch_plugin_info["Version"]
		) {
			$need_update = true;

			if ( ! function_exists( 'cptch_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			$default_options = cptch_get_default_options();

			$cptch_options = cptch_parse_options( $cptch_options, $default_options );

			/* Enabling notice about possible conflict with W3 Total Cache */
			if ( version_compare( $cptch_options['plugin_option_version'], '4.2.7', '<=' ) ) {
				$cptch_options['w3tc_notice'] = 1;
			}
		}

		/* Update tables when update plugin and tables changes*/
		if ( empty( $cptch_options['plugin_db_version'] ) || $cptch_options['plugin_db_version'] != $db_version ) {
			$need_update = true;
			cptch_create_table();

			if ( empty( $cptch_options['plugin_db_version'] ) ) {
				if ( ! class_exists( 'Cptch_Package_Loader' ) )
					require_once( dirname( __FILE__ ) . '/includes/package_loader.php' );
				$package_loader = new Cptch_Package_Loader();
				$package_loader->save_packages( dirname( __FILE__ ) . '/images/package', false );
			}

			$cptch_options['plugin_db_version'] = $db_version;
		}

		if ( $need_update )
			update_option( 'cptch_options', $cptch_options );
	}
}

/* Generate key */
if ( ! function_exists( 'cptch_generate_key' ) ) {
	function cptch_generate_key( $lenght = 15 ) {
		global $cptch_options;
		/* Under the string $simbols you write all the characters you want to be used to randomly generate the code. */
		$simbols = get_bloginfo( "url" ) . time();
		$simbols_lenght = strlen( $simbols );
		$simbols_lenght--;
		$str_key = NULL;
		for ( $x = 1; $x <= $lenght; $x++ ) {
			$position = rand( 0, $simbols_lenght );
			$str_key .= substr( $simbols, $position, 1 );
		}

		$cptch_options['str_key']['key']  = md5( $str_key );
		$cptch_options['str_key']['time'] = time();
		update_option( 'cptch_options', $cptch_options );
	}
}

if ( ! function_exists( 'cptch_whitelisted_ip' ) ) {
	function cptch_whitelisted_ip() {
		global $cptch_options, $wpdb;
		$checked = false;
		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );
		$table = 1 == $cptch_options['use_limit_attempts_whitelist'] ? 'lmtttmpts_whitelist' : 'cptch_whitelist';
		$whitelist_exist = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}{$table}'" );
		if ( ! empty( $whitelist_exist ) ) {
			$ip = '';
			if ( isset( $_SERVER ) ) {
				$sever_vars = array( 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
				foreach( $sever_vars as $var ) {
					if ( isset( $_SERVER[ $var ] ) && ! empty( $_SERVER[ $var ] ) ) {
						if ( filter_var( $_SERVER[ $var ], FILTER_VALIDATE_IP ) ) {
							$ip = $_SERVER[ $var ];
							break;
						} else { /* if proxy */
							$ip_array = explode( ',', $_SERVER[ $var ] );
							if ( is_array( $ip_array ) && ! empty( $ip_array ) && filter_var( $ip_array[0], FILTER_VALIDATE_IP ) ) {
								$ip = $ip_array[0];
								break;
							}
						}
					}
				}
			}

			if ( ! empty( $ip ) ) {
				$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}{$table}` LIKE 'ip_from_int'" );
				/* LimitAttempts Free hasn't `ip_from_int`, `ip_to_int` COLUMNS */
				if ( 0 == $column_exists ) {
					$result = $wpdb->get_var(
						"SELECT `id`
						FROM `{$wpdb->prefix}{$table}`
						WHERE `ip` = '{$ip}' LIMIT 1;"
					);
				} else {
					$ip_int = sprintf( '%u', ip2long( $ip ) );
					$result = $wpdb->get_var(
						"SELECT `id`
						FROM `{$wpdb->prefix}{$table}`
						WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} ) OR `ip` LIKE '{$ip}' LIMIT 1;"
					);
				}
				$checked = is_null( $result ) || ! $result ? false : true;
			}
		}
		return $checked;
	}
}

/* Function for display captcha settings page in the admin area */
if ( ! function_exists( 'cptch_settings_page' ) ) {
	function cptch_settings_page() {
		global $cptch_plugin_info, $cptch_options, $wpdb;
		$is_multisite     = is_multisite();
		$is_network       = is_network_admin();
		$plugin_basename  = plugin_basename( __FILE__ );
		$page             = false;

		if ( ! function_exists( 'cptch_get_default_options' ) )
			require_once( dirname( __FILE__ ) . '/includes/helpers.php' );

		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );

		if ( isset( $_POST['bws_hide_premium_options'] ) && check_admin_referer( $plugin_basename, 'cptch_nonce_name' ) ) {
			$result        = bws_hide_premium_options( $cptch_options );
			$cptch_options = $result['options'];
			update_option( 'cptch_options', $cptch_options );
		}

		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			//	$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'cptch_options' );
			$cptch_options = get_option( 'cptch_options' );
		}

		/* Display form on the setting page */ ?>
		<div class="wrap cptch_settings_page">
			<h1><?php _e( 'Captcha Settings', 'captcha' ); ?></h1>
			<!--<ul class="subsubsub cptch_how_to_use">
				<li><a href="https://docs.google.com/document/d/11_TUSAjMjG7hLa53lmyTZ1xox03hNlEA4tRmllFep3I/edit" target="_blank"><?php _e( 'How to Use Step-by-step Instruction', 'captcha' ); ?></a></li>
			</ul>-->
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=captcha.php"><?php _e( 'Settings', 'captcha' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'packages' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=captcha.php&amp;action=packages" title="<?php _e( 'This setting is available in Pro version', 'captcha' ); ?>"><?php _e( 'Packages', 'captcha' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'whitelist' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=captcha.php&amp;action=whitelist"><?php _e( 'Whitelist', 'captcha' ); ?></a>


			</h2>

			<?php if ( ! empty( $go_pro_result['error'] ) ) { ?>
				<div class="error below-h2"><p><strong><?php echo $go_pro_result['error']; ?></strong></p></div>
			<?php }
			if ( ! empty( $go_pro_result['message'] ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $go_pro_result['message']; ?></strong></p></div>
			<?php }

			if ( isset( $_GET['action'] ) ) {
				switch( $_GET['action'] ) {

					case 'whitelist':
						if ( ! function_exists( 'get_plugins' ) )
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						$limit_attempts_info = cptch_get_plugin_status( array( 'limit-attempts/limit-attempts.php', 'limit-attempts-pro/limit-attempts-pro.php' ), get_plugins(), $is_network );
						require_once( dirname( __FILE__ ) . '/includes/whitelist.php' );
						$page = new Cptch_Whitelist( $plugin_basename, $limit_attempts_info );
						break;
					case 'packages': ?>
						<form class="bws_form" method="post" action="">
							<?php cptch_pro_block( 'cptch_packages_banner' );
							$date = date_i18n( get_option( 'date_format' ), strtotime( '1.06.2016' ) );
							$package_list = $wpdb->get_results(
								"SELECT
									`{$wpdb->base_prefix}cptch_packages`.`id`,
									`{$wpdb->base_prefix}cptch_packages`.`name`,
									`{$wpdb->base_prefix}cptch_packages`.`folder`,
									`{$wpdb->base_prefix}cptch_packages`.`settings`,
									`{$wpdb->base_prefix}cptch_images`.`name` AS `image`
								FROM
									`{$wpdb->base_prefix}cptch_packages`
								LEFT JOIN
									`{$wpdb->base_prefix}cptch_images`
								ON
									`{$wpdb->base_prefix}cptch_images`.`package_id`=`{$wpdb->base_prefix}cptch_packages`.`id`
								GROUP BY `{$wpdb->base_prefix}cptch_packages`.`id`
								ORDER BY `name` ASC;",
								ARRAY_A
							);
							$src  = plugins_url( 'images/package/', __FILE__ ); ?>
							<table id="cptch_packages_list" class="wp-list-table widefat striped">
								<thead>
									<tr>
										<th scope="col" id="name" class="manage-column column-name column-primary">
											<span><?php _e( 'Package', 'captcha' ); ?></span>
										</th>
										<th scope="col" id="add_time" class="manage-column column-add_time desc">
											<span><?php _e( 'Date', 'captcha' ); ?></span>
										</th>
									</tr>
								</thead>
								<tbody id="the-list">
									<?php foreach ( $package_list as $pack ) { ?>
										<tr>
											<td class="name column-name has-row-actions column-primary">
												<div class="has-media-icon">
													<span class="media-icon image-icon"><img src="<?php echo $src . '/' . $pack['folder'] . '/' . $pack['image']; ?>"></span> <?php echo $pack['name']; ?>
												</div>
											</td>
											<td class="add_time column-add_time"><?php echo $date; ?></td>
										</tr>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<th scope="col" id="name" class="manage-column column-name column-primary desc">
											<span><?php _e( 'Package', 'captcha' ); ?></span>
										</th>
										<th scope="col" id="add_time" class="manage-column column-add_time desc">
											<span><?php _e( 'Date', 'captcha' ); ?></span>
										</th>
									</tr>
								</tfoot>
							</table>
							<?php wp_nonce_field( $plugin_basename, 'cptch_nonce_name' ); ?>
						</form>
						<?php break;

					default:
						break;
				}
			} else {
				require_once( dirname( __FILE__ ) . '/includes/settings_page.php' );
				$page = new Cptch_Basic_Settings( $plugin_basename, $is_multisite, $is_network );
			}

			if ( is_object( $page ) )
				$page->display_content();

			bws_plugin_reviews_block( $cptch_plugin_info['Name'], 'captcha' ); ?>
		</div><!-- .cptch_settings_page -->
	<?php }
}


/************** WP LOGIN FORM HOOKS ********************/

if ( ! function_exists( 'cptch_login_form' ) ) {
	function cptch_login_form() {
		global $cptch_options, $cptch_ip_in_whitelist;
		if ( ! $cptch_ip_in_whitelist ) {
			if ( "" == session_id() )
			@session_start();

			if ( isset( $_SESSION["cptch_login"] ) )
				unset( $_SESSION["cptch_login"] );
		}

		echo cptch_display_captcha_custom( 'wp_login', 'cptch_wp_login' ) . '<br />';
		return true;
	}
}

if ( ! function_exists( 'cptch_login_check' ) ) {
	function cptch_login_check( $user ) {
		global $cptch_options;

		if ( ! isset( $_POST['wp-submit'] ) )
			return $user;

		if ( ! isset( $cptch_options['str_key'] ) )
			$cptch_options = get_option( 'cptch_options' );
		$str_key = $cptch_options['str_key']['key'];

		if ( ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( "" == session_id() )
			@session_start();

		if ( isset( $_SESSION["cptch_login"] ) && true === $_SESSION["cptch_login"] )
			return $user;

		/* Delete errors, if they set */
		if ( isset( $_SESSION['cptch_error'] ) )
			unset( $_SESSION['cptch_error'] );

		if ( is_plugin_active( 'limit-login-attempts/limit-login-attempts.php' ) ) {
			if ( isset( $_REQUEST['loggedout'] ) && isset( $_REQUEST['cptch_number'] ) && "" ==  $_REQUEST['cptch_number'] ) {
				return $user;
			}
		}
		if ( cptch_limit_exhausted() ) {
			$_SESSION['cptch_login'] = false;
			$error = new WP_Error();
			$error->add( 'cptch_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:' . '&nbsp;' . $cptch_options['time_limit_off'] );
			return $error;
		}
		/* Add error if captcha is empty */
		if ( ( ! isset( $_REQUEST['cptch_number'] ) || "" ==  $_REQUEST['cptch_number'] ) && isset( $_REQUEST['loggedout'] ) ) {
			$error = new WP_Error();
			$error->add( 'cptch_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $cptch_options['no_answer'] );
			wp_clear_auth_cookie();
			return $error;
		}

		if ( isset( $_REQUEST['cptch_result'] ) && isset( $_REQUEST['cptch_number'] ) && isset( $_REQUEST['cptch_time'] ) ) {
			if ( 0 === strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $str_key, $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] ) ) {
				/* Captcha was matched */
				$_SESSION['cptch_login'] = true;
				return $user;
			} else {
				$_SESSION['cptch_login'] = false;
				wp_clear_auth_cookie();
				/* Add error if captcha is incorrect */
				$error = new WP_Error();
				if ( "" == $_REQUEST['cptch_number'] )
					$error->add( 'cptch_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $cptch_options['no_answer'] );
				else
					$error->add( 'cptch_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $cptch_options['wrong_answer'] );
				return $error;
			}
		} else {
			/* Captcha was matched */
			if ( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) ) {
				/* captcha was not found in _REQUEST */
				$_SESSION['cptch_login'] = false;
				$error = new WP_Error();
				$error->add( 'cptch_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $cptch_options['no_answer'] );
				return $error;
			} else {
				/* it is not a submit */
				return $user;
			}
		}
	}
}

/************** WP REGISTER FORM HOOKS ********************/

if ( ! function_exists( 'cptch_register_form' ) ) {
	function cptch_register_form() {
		echo cptch_display_captcha_custom( 'wp_register', 'cptch_wp_register' ) . '<br />';
		return true;
	}
}

if ( ! function_exists ( 'wpmu_cptch_register_form' ) ) {
	function wpmu_cptch_register_form( $errors ) {
		global $cptch_options, $cptch_ip_in_whitelist;

		/* the captcha html - register form */
		echo '<div class="cptch_block">';
		if ( "" != $cptch_options['title'] )
			echo '<span class="cptch_title">' . $cptch_options['title'] . '<span class="required"> ' . $cptch_options['required_symbol'] . '</span></span>';
		if ( ! $cptch_ip_in_whitelist ) {
			if ( is_wp_error( $errors ) ) {
				$error_codes = $errors->get_error_codes();
				if ( is_array( $error_codes ) && ! empty( $error_codes ) ) {
					foreach ( $error_codes as $error_code ) {
						if ( "captcha_" == substr( $error_code, 0, 8 ) ) {
							$error_message = $errors->get_error_message( $error_code );
							echo '<p class="error">' . $error_message . '</p>';
						}
					}
				}
			}
			echo cptch_display_captcha( 'wp_register' );
		} else
			echo '<label class="cptch_whitelist_message">' . $cptch_options['whitelist_message'] . '</label>';
		echo '</div><br />';
	}
}

if ( ! function_exists ( 'cptch_register_check' ) ) {
	function cptch_register_check( $error ) {
		global $cptch_options;
		$str_key = $cptch_options['str_key']['key'];

		if ( cptch_limit_exhausted() ) {
			if ( ! is_wp_error( $error ) )
				$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['time_limit_off'] );
		} elseif ( isset( $_REQUEST['cptch_number'] ) && "" == $_REQUEST['cptch_number'] ) {
			if ( ! is_wp_error( $error ) )
				$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['no_answer'] );
		} elseif (
			! isset( $_REQUEST['cptch_result'] ) ||
			! isset( $_REQUEST['cptch_number'] ) ||
			! isset( $_REQUEST['cptch_time'] ) ||
			0 !== strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $str_key, $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] )
		) {
			if ( ! is_wp_error( $error ) )
				$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['wrong_answer'] );
		}
		return $error;
	}
}

if ( ! function_exists( 'cptch_register_validate' ) ) {
	function cptch_register_validate( $results ) {
		global $current_user, $cptch_options;

		if ( empty( $current_user->data->ID ) ) {
			$str_key = $cptch_options['str_key']['key'];
			$time_limit_exhausted = cptch_limit_exhausted();
			if ( $time_limit_exhausted ) {
				$error_slug    = 'captcha_time_limit';
				$error_message = $cptch_options['time_limit_off'];
			} else {
				$error_slug    = 'captcha_blank';
				$error_message = $cptch_options['no_answer'];
			}

			/* If captcha is blank - add error */
			if ( ( isset( $_REQUEST['cptch_number'] ) && "" ==  $_REQUEST['cptch_number'] ) || $time_limit_exhausted ) {
				$results['errors']->add( $error_slug, '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $error_message );
				return $results;
			}

			if (
				! isset( $_REQUEST['cptch_result'] ) ||
				! isset( $_REQUEST['cptch_number'] ) ||
				! isset( $_REQUEST['cptch_time'] ) ||
				0 !== strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $str_key, $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] )
			)
				$results['errors']->add( 'captcha_wrong', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>: ' . $cptch_options['wrong_answer'] );
			return $results;
		} else {
			return $results;
		}
	}
}

/************** WP LOST PASSWORD FORM HOOKS ********************/

if ( ! function_exists ( 'cptch_lostpassword_form' ) ) {
	function cptch_lostpassword_form() {
		echo cptch_display_captcha_custom( 'wp_lost_password', 'cptch_wp_lost_password' ) . '<br />';
		return true;
	}
}

if ( ! function_exists ( 'cptch_lostpassword_check' ) ) {
	function cptch_lostpassword_check( $allow ) {
		global $cptch_options;
		$str_key = $cptch_options['str_key']['key'];
		$error   = '';

		if ( cptch_limit_exhausted() ) {
			$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['time_limit_off'] );
		} elseif ( isset( $_REQUEST['cptch_number'] ) && "" == $_REQUEST['cptch_number'] ) {
			$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['no_answer'] );
		} elseif (
			! isset( $_REQUEST['cptch_result'] ) ||
			! isset( $_REQUEST['cptch_number'] ) ||
			! isset( $_REQUEST['cptch_time'] ) ||
			0 !== strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $str_key, $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] )
		) {
			$error = new WP_Error();
			$error->add( 'captcha_error', '<strong>' . __( 'ERROR', 'captcha' ) . '</strong>:&nbsp;' . $cptch_options['wrong_answer'] );
		}
		return is_wp_error( $error ) ? $error : $allow;
	}
}

/************** WP COMMENT FORM HOOKS ********************/

if ( ! function_exists( 'cptch_comment_form' ) ) {
	function cptch_comment_form() {
		echo cptch_display_captcha_custom( 'wp_comments', 'cptch_wp_comments' );
		return true;
	}
}

if ( ! function_exists( 'cptch_comment_form_wp3' ) ) {
	function cptch_comment_form_wp3() {
		remove_action( 'comment_form', 'cptch_comment_form' );
		echo cptch_display_captcha_custom( 'wp_comments', 'cptch_wp_comments' );
		return true;
	}
}

if ( ! function_exists( 'cptch_comment_post' ) ) {
	function cptch_comment_post( $comment ) {
		global $cptch_options;

		if ( is_user_logged_in() && 1 == $cptch_options['cptch_hide_register'] )
			return $comment;

		$str_key = $cptch_options['str_key']['key'];

		$time_limit_exhausted = cptch_limit_exhausted();
		$error_message = $time_limit_exhausted ? $cptch_options['time_limit_off'] : $cptch_options['no_answer'];


		/* Added for compatibility with WP Wall plugin */
		/* This does NOT add CAPTCHA to WP Wall plugin, */
		/* It just prevents the "Error: You did not enter a Captcha phrase." when submitting a WP Wall comment */
		if ( function_exists( 'WPWall_Widget' ) && isset( $_REQUEST['wpwall_comment'] ) ) {
			/* Skip capthca */
			return $comment;
		}

		/* Skip captcha for comment replies from the admin menu */
		if ( isset( $_REQUEST['action'] ) && 'replyto-comment' == $_REQUEST['action'] &&
		( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false ) ) ) {
			return $comment;
		}

		/* Skip captcha for trackback or pingback */
		if ( '' != $comment['comment_type'] && 'comment' != $comment['comment_type'] ) {
			return $comment;
		}

		/* If captcha is empty */
		if ( ( isset( $_REQUEST['cptch_number'] ) && "" ==  $_REQUEST['cptch_number'] ) || $time_limit_exhausted )
			wp_die( __( 'Error', 'captcha' ) . ':&nbsp' . $error_message . ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? '' : ' ' . __( "Click the BACK button on your browser, and try again.", 'captcha' ) ) );

		if ( isset( $_REQUEST['cptch_result'] ) && isset( $_REQUEST['cptch_number'] ) && isset( $_REQUEST['cptch_time'] ) && 0 === strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $str_key, $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] ) ) {
			/* Captcha was matched */
			return( $comment );
		} else {
			wp_die( __( 'Error', 'captcha' ) . ':&nbsp' . $cptch_options['wrong_answer'] . ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? '' : ' ' . __( "Click the BACK button on your browser, and try again.", 'captcha' ) ) );
		}
	}
}

/************** BWS CONTACT FORM ********************/
if ( ! function_exists ( 'cptch_custom_form' ) ) {
	function cptch_custom_form( $content = "", $form_slug = 'general' ) {
		return
			( is_string( $content ) ? $content : '' ) .
			cptch_display_captcha_custom( $form_slug );
	}
}

/**
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_check_bws_contact_form' ) ) {
	function cptch_check_bws_contact_form( $allow ) {
		if ( true !== $allow )
			return $allow;
		return cptch_check_custom_form( true, 'wp_error' );
	}
}

/************** DISPLAY CAPTCHA VIA SHORTCODE ********************/

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_display_captcha_shortcode' ) ) {
	function cptch_display_captcha_shortcode( $args ) {
		global $cptch_options;

		if ( ! is_array( $args ) || empty( $args ) )
			return cptch_display_captcha_custom( 'general', 'cptch_shortcode' );

		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );

		$form_slug  = empty( $args["form_slug"] ) ? 'general' : $args["form_slug"];
		$form_slug  = esc_attr( $form_slug );
		$form_slug  = empty( $form_slug ) || ! array_key_exists( $form_slug, $cptch_options['forms'] ) ? 'general' : $form_slug;
		$class_name = empty( $args["class_name"] ) ? 'cptch_shortcode' : esc_attr( $args["class_name"] );

		return
				'general' == $form_slug ||
				$cptch_options['forms'][ $form_slug ]['enable']
			?
				cptch_display_captcha_custom( $form_slug, $class_name)
			:
				'';
	}
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_shortcode_button_content' ) ) {
	function cptch_shortcode_button_content( $content ) { ?>
		<div id="cptch" style="display:none;">
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_captcha]" />
		</div>
	<?php }
}

/************** DISPLAY CAPTCHA VIA FILTER HOOK ********************/
/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_display_filter' ) ) {
	function cptch_display_filter( $content = '', $form_slug = 'general', $class_name = "" ) {
		$args = array(
			'form_slug'  => $form_slug,
			'class_name' => $class_name
		);
		return $content . cptch_display_captcha_shortcode( $args );
	}
}

/* Functionality of the captcha logic work for custom form */
if ( ! function_exists( 'cptch_display_captcha_custom' ) ) {
	function cptch_display_captcha_custom( $form_slug = 'general', $class_name = "", $input_name = 'cptch_number' ) {
		global $cptch_options, $cptch_ip_in_whitelist;

		if ( empty( $cptch_ip_in_whitelist ) )
			$cptch_ip_in_whitelist = cptch_whitelisted_ip();

		if ( empty( $class_name ) ) {
			$label = $tag_open = $tag_close = '';
		} else {
			$label =
					"" != $cptch_options['title']
				?
					'<span class="cptch_title">' . $cptch_options['title'] .'<span class="required"> ' . $cptch_options['required_symbol'] . '</span></span>'
				:
					'';
			$tag_open  = '<p class="cptch_block">';
			$tag_close = '</p>';
		}

		$content = $cptch_ip_in_whitelist ? '<label class="cptch_whitelist_message">' . $cptch_options['whitelist_message'] . '</label>' : cptch_display_captcha( $form_slug, $class_name, $input_name );
		return $tag_open . $label . $content . $tag_close;
	}
}

/**
 * Checks the answer for the CAPTCHA
 * @param  mixed   $allow          The result of the pevious checking
 * @param  string  $return_format  The type of the cheking result. Can be set as 'string' or 'wp_error
 * @return mixed                   boolean(true) - in case when the CAPTCHA answer is right, or user`s IP is in the whitelist,
 *                                 string or WP_Error object ( depending on the $return_format variable ) - in case when the CAPTCHA answer is wrong
 */
if ( ! function_exists( 'cptch_check_custom_form' ) ) {
	function cptch_check_custom_form( $allow = true, $return_format = 'string' ) {
		global $cptch_options, $cptch_ip_in_whitelist;

		/*
		 * Whether the user's IP is in the whitelist
		 */
		if ( is_null( $cptch_ip_in_whitelist ) )
			$cptch_ip_in_whitelist = cptch_whitelisted_ip();

		if ( $cptch_ip_in_whitelist )
			return $allow;

		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );

		$error_code = '';

		/* The time limit is exhausted */
		if ( cptch_limit_exhausted() )
			$error_code = 'time_limit_off';
		/* Not enough data to verify the CAPTCHA answer */
		elseif (
			! isset( $_REQUEST['cptch_result'] ) ||
			! isset( $_REQUEST['cptch_number'] ) ||
			! isset( $_REQUEST['cptch_time'] )
		)
			$error_code = 'no_answer';
		/* The CAPTCHA answer is wrong */
		elseif (
			0 !== strcasecmp( trim( cptch_decode( $_REQUEST['cptch_result'], $cptch_options['str_key']['key'], $_REQUEST['cptch_time'] ) ), $_REQUEST['cptch_number'] )
		)
			$error_code = 'wrong_answer';

		/* The CAPTCHA answer is right */
		if ( empty( $error_code ) )
			return $allow;

		/* Fetch the error message */
		if ( 'string' == $return_format ) {
			$allow = $cptch_options[ $error_code ];
		} else {
			if ( ! is_wp_error( $allow ) )
				$allow = new WP_Error();
			$allow->add( "cptch_error_{$error_code}", $cptch_options[ $error_code ] );
		}

		return $allow;
	}
}

/* Functionality of the captcha logic work */
if ( ! function_exists( 'cptch_display_captcha' ) ) {
	function cptch_display_captcha( $form_slug = 'general', $class_name = "", $input_name = 'cptch_number' ) {
		global $cptch_options;

		if ( ! isset( $cptch_options['str_key'] ) )
			$cptch_options = get_option( 'cptch_options' );
		if ( empty( $cptch_options['str_key']['key'] ) || $cptch_options['str_key']['time'] < time() - ( 24 * 60 * 60 ) )
			cptch_generate_key();
		$str_key = $cptch_options['str_key']['key'];

		/**
		 * Escaping function parameters
		 * @since 4.2.3
		 */
		$form_slug  = esc_attr( $form_slug );
		$form_slug  = empty( $form_slug ) ? 'general' : $form_slug;
		$class_name = esc_attr( $class_name );
		$input_name = esc_attr( $input_name );
		$input_name = empty( $input_name ) ? 'cptch_number' : $input_name;

		/**
		 * In case when the CAPTCHA uses in the custom form
		 * and there is no saved settings for this form
		 * making an attempt to get default settings
		 * @since 4.2.3
		 */
		if ( ! array_key_exists( $form_slug, $cptch_options['forms'] ) ) {
			if ( ! function_exists( 'cptch_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			$default_options = cptch_get_default_options();
			/* prevent the need to get default settings on the next displaying of the CAPTCHA */
			if ( array_key_exists( $form_slug, $default_options['forms'] ) ) {
				$cptch_options['forms'][ $form_slug ] = $default_options['forms'][ $form_slug ];
				update_option( 'cptch_options' );
			} else {
				$form_slug = 'general';
			}
		}

		/**
		 * Display only the CAPTCHA container to replace it with the CAPTCHA
		 * after the whole page loading via AJAX
		 * @since 4.2.3
		 */
		if ( $cptch_options['load_via_ajax'] && ! defined( 'CPTCH_RELOAD_AJAX' ) ) {
			return cptch_add_scripts() .
				'<span
				class="cptch_wrap cptch_ajax_wrap"
				data-cptch-form="' . $form_slug . '"
				data-cptch-input="' . $input_name . '"
				data-cptch-class="' . $class_name . '">
					<noscript>' .
					__( 'In order to pass the CAPTCHA please enable JavaScript', 'captcha' ) .
					'</noscript>
				</span>';
		}

		$id_postfix = rand( 0, 100 );
		$hidden_result_name = $input_name == 'cptch_number' ? 'cptch_result' : $input_name . '-cptch_result';
		$time = time();

		if ( 'recognition' == $cptch_options['type'] ) {
			$string = '';
			$captcha_content = '<span class="cptch_images_wrap">';
			$count = $cptch_options['images_count'];
			while ( $count != 0 ) {
				/*
				 * get element
				 */
				$image = rand( 1, 9 );
				$array_key = mt_rand( 0, abs( count( $cptch_options['used_packages'] ) - 1 ) );
				$operand =
						empty( $cptch_options['used_packages'][ $array_key ] )
					?
						cptch_generate_value( $image, false )
					:
						cptch_get_image( $image, '', $cptch_options['used_packages'][ $array_key ], false );

				$captcha_content .= '<span class="cptch_span">' . $operand . '</span>';
				$string .= $image;
				$count--;
			}
			$captcha_content .= '</span>
				<input id="cptch_input_' . $id_postfix . '" class="cptch_input ' . $class_name . '" type="text" autocomplete="off" name="' . $input_name . '" value="" maxlength="' . $cptch_options['images_count'] . '" size="' . $cptch_options['images_count'] . '" aria-required="true" required="required" style="margin-bottom:0;font-size: 12px;max-width:100%;" />
				<input type="hidden" name="' . $hidden_result_name . '" value="' . cptch_encode( $string, $str_key, $time ) . '" />';
		} else {
			/*
			 * array of math actions
			 */
			$math_actions = array();
			if ( in_array( 'plus', $cptch_options['math_actions'] ) )
				$math_actions[] = '&#43;';
			if ( in_array( 'minus', $cptch_options['math_actions'] ) )
				$math_actions[] = '&minus;';
			if ( in_array( 'multiplications', $cptch_options['math_actions'] ) )
				$math_actions[] = '&times;';
			/* current math action */
			$rand_math_action = rand( 0, count( $math_actions) - 1 );

			/*
			 * get elements of mathematical expression
			 */
			$array_math_expression    = array();
			$array_math_expression[0] = rand( 1, 9 ); /* first part */
			$array_math_expression[1] = rand( 1, 9 ); /* second part */
			/* Calculation of the result */
			switch( $math_actions[ $rand_math_action ] ) {
				case "&#43;":
					$array_math_expression[2] = $array_math_expression[0] + $array_math_expression[1];
					break;
				case "&minus;":
					/* Result must not be equal to the negative number */
					if ( $array_math_expression[0] < $array_math_expression[1] ) {
						$number = $array_math_expression[0];
						$array_math_expression[0] = $array_math_expression[1];
						$array_math_expression[1] = $number;
					}
					$array_math_expression[2] = $array_math_expression[0] - $array_math_expression[1];
					break;
				case "&times;":
					$array_math_expression[2] = $array_math_expression[0] * $array_math_expression[1];
					break;
			}

			/*
			 * array of allowed formats
			 */
			$allowed_formats = array();
			$use_words = $use_numbeers = false;
			if ( in_array( 'numbers', $cptch_options["operand_format"] ) ) {
				$allowed_formats[] = 'number';
				$use_words         = true;
			}
			if ( in_array( 'words', $cptch_options["operand_format"] ) ) {
				$allowed_formats[] = 'word';
				$use_numbeers      = true;
			}
			if ( in_array( 'images', $cptch_options["operand_format"] ) )
				$allowed_formats[] = 'image';
			$use_only_words = ( $use_words && ! $use_numbeers ) || ! $use_words;
			/* number of field, which will be as <input type="number"> */
			$rand_input = rand( 0, 2 );

			/*
			 * get current format for each operand
			 * for example array( 'text', 'input', 'number' )
			 */
			$operand_formats = array();
			$max_rand_value = count( $allowed_formats ) - 1;
			for ( $i = 0; $i < 3; $i ++ )
				$operand_formats[] = $rand_input == $i ? 'input' : $allowed_formats[ mt_rand( 0, $max_rand_value ) ];

			/*
			 * get value of each operand
			 */
			$operand    = array();

			foreach ( $operand_formats as $key => $format ) {
				switch ( $format ) {
					case 'input':
						$operand[] = '<input id="cptch_input_' . $id_postfix . '" class="cptch_input ' . $class_name . '" type="text" autocomplete="off" name="' . $input_name . '" value="" maxlength="2" size="2" aria-required="true" required="required" style="margin-bottom:0;display:inline;font-size: 12px;width: 40px;" />';
						break;
					case 'word':
						$operand[] = cptch_generate_value( $array_math_expression[ $key ] );
						break;
					case 'image':
						$array_key = mt_rand( 0, abs( count( $cptch_options['used_packages'] ) - 1 ) );
						$operand[] =
								empty( $cptch_options['used_packages'][ $array_key ] )
							?
								cptch_generate_value( $array_math_expression[ $key ] )
							:
								cptch_get_image( $array_math_expression[ $key ], $key, $cptch_options['used_packages'][ $array_key ], $use_only_words );
						break;
					case 'number':
					default:
						$operand[] = $array_math_expression[ $key ];
						break;
				}
			}
			$captcha_content = '<span class="cptch_span">' . $operand[0] . '</span>
					<span class="cptch_span">&nbsp;' . $math_actions[ $rand_math_action ] . '&nbsp;</span>
					<span class="cptch_span">' . $operand[1] . '</span>
					<span class="cptch_span">&nbsp;=&nbsp;</span>
					<span class="cptch_span">' . $operand[2] . '</span>
					<input type="hidden" name="' . $hidden_result_name . '" value="' . cptch_encode( $array_math_expression[ $rand_input ], $str_key, $time ) . '" />';
		}

		return
			cptch_add_time_limit_notice( $id_postfix ) .
			cptch_add_scripts() .
			'<span class="cptch_wrap cptch_' . $cptch_options['type'] . '">
				<label class="cptch_label" for="cptch_input_' . $id_postfix . '">' .
					$captcha_content .
					'<input type="hidden" name="cptch_time" value="' . $time . '" />
					<input type="hidden" name="cptch_form" value="' . $form_slug . '" />
				</label>' .
				cptch_add_reload_button( !! $cptch_options['display_reload_button'] ) .
			'</span>';
	}
}

/**
 * Add necessary js scripts
 * @uses     for including necessary scripts on the pages witn the CAPTCHA only
 * @since    4.2.0
 * @param    void
 * @return   string   empty string - if the form has been loaded by PHP or the CAPTCHA has been reloaded, inline javascript - if the form has been loaded by AJAX
 */
if ( ! function_exists( 'cptch_add_scripts' ) ) {
	function cptch_add_scripts () {
		global $cptch_options;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return
					defined( 'Cptch_RELOAD_AJAX' )
				?
					''
				:
					/*
					 * this script will be included if the from was loaded via AJAX only
					 * but not during the CAPTCHA reloading
					 */
					'<script class="cptch_to_remove" type="text/javascript">
						(function( d, tag, id ) {
							var script = d.getElementById( id );
							if ( script )
								return;
							add_script( "", "", id );

							if ( typeof( cptch_vars ) == "undefined" ) {
								var local = {
									nonce:     "' . wp_create_nonce( 'cptch', 'cptch_nonce' ) . '",
									ajaxurl:   "' . admin_url( 'admin-ajax.php' ) . '",
									enlarge:   "' . $cptch_options['enlarge_images'] . '"
								};
								add_script( "", "/* <![CDATA[ */var cptch_vars=" + JSON.stringify( local ) + "/* ]]> */" );
							}

							d.addEventListener( "DOMContentLoaded", function() {
								var scripts         = d.getElementsByTagName( tag ),
									captcha_script  = /' . addcslashes( plugins_url( 'js/front_end_script.js' , __FILE__ ), '/' ) . '/,
									include_captcha = true;
								if ( scripts ) {
									for ( var i = 0; i < scripts.length; i++ ) {
										if ( scripts[ i ].src.match( captcha_script ) ) {
											include_captcha = false;
											break;
										}
									}
								}
								if ( typeof jQuery == "undefined" ) {
									var siteurl = "' . get_option( 'siteurl' ) . '";
									add_script( siteurl + "/wp-includes/js/jquery/jquery.js" );
									add_script( siteurl + "/wp-includes/js/jquery/jquery-migrate.min.js" );
								}
								if ( include_captcha )
									add_script( "' . plugins_url( 'js/front_end_script.js' , __FILE__ ) . '" );
							});

							function add_script( url, content, js_id ) {
								url     = url     || "";
								content = content || "";
								js_id   = js_id   || "";
								var script = d.createElement( tag );
								if ( url )
									script.src = url;
								if ( content )
									script.appendChild( d.createTextNode( content ) );
								if ( js_id )
									script.id = js_id;
								script.setAttribute( "type", "text/javascript" );
								d.body.appendChild( script );
							}
						})( document, "script", "cptch_script_loader" );
					</script>';
		} elseif ( ! wp_script_is( 'cptch_front_end_script', 'registered' ) ) {
			wp_register_script( 'cptch_front_end_script', plugins_url( 'js/front_end_script.js' , __FILE__ ), array( 'jquery' ), false, $cptch_options['plugin_option_version'] );
			add_action( 'wp_footer', 'cptch_front_end_scripts' );
			if (
				$cptch_options['forms']['wp_login']['enable'] ||
				$cptch_options['forms']['wp_register']['enable'] ||
				$cptch_options['forms']['wp_lost_password']['enable']
			)
				add_action( 'login_footer', 'cptch_front_end_scripts' );
		}
		return '';
	}
}

/**
 * Adds a notice about the time expiration
 * @since     4.2.0
 * @param     int        $id_postfix    to generate an unique css ID on the page if there are more then one CAPTCHA
 * @return    string                    the message about the exhaustion of time limit and inline script for the displaying of this message
 */
if ( ! function_exists( 'cptch_add_time_limit_notice' ) ) {
	function cptch_add_time_limit_notice( $id_postfix ) {
		global $cptch_options;

		if ( ! $cptch_options['enable_time_limit'] || ! $cptch_options['time_limit'] )
			return '';

		$id = "cptch_time_limit_notice_{$id_postfix}";
		return
			'<script class="cptch_to_remove">
				(function( timeout ) {
					setTimeout(
						function() {
							var notice = document.getElementById("' . $id . '");
							if ( notice )
								notice.style.display = "block";
						},
						timeout
					);
				})(' . $cptch_options['time_limit'] . '000);
			</script>
			<span id="' . $id . '" class="cptch_time_limit_notice cptch_to_remove">' . $cptch_options['time_limit_off_notice'] . '</span>';
	}
}

/**
 * Add a reload button to the CAPTCHA block
 * @since     4.2.0
 * @param     boolean     $add_button  if 'true' - the button will be added
 * @return    string                   the button`s HTML-content
 */
if ( ! function_exists( 'cptch_add_reload_button' ) ) {
	function cptch_add_reload_button( $add_button ) {
		return
				$add_button
			?
				'<span class="cptch_reload_button_wrap hide-if-no-js">
					<noscript>
						<style type="text/css">
							.hide-if-no-js {
								display: none !important;
							}
						</style>
					</noscript>
					<span class="cptch_reload_button dashicons dashicons-update"></span>
				</span>'
			:
				'';
	}
}

/**
 * Display image in CAPTCHA
 * @param    int     $value       value of element of mathematical expression
 * @param    int     $place       which is an element in the mathematical expression
 * @param    array   $package_id  what package to use in current CAPTCHA ( if it is '-1' then all )
 * @return   string               html-structure of element
 */
if ( ! function_exists( 'cptch_get_image' ) ) {
	function cptch_get_image( $value, $place, $package_id, $use_only_words ) {
		global $wpdb, $cptch_options;

		$result = array();
		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );

		if ( empty( $cptch_options['used_packages'] ) )
			return cptch_generate_value( $value, $use_only_words );

		$where = -1 == $package_id ? ' IN (' . implode( ',', $cptch_options['used_packages'] ) . ')' : '=' . $package_id;
		$images = $wpdb->get_results(
			"SELECT
				`{$wpdb->base_prefix}cptch_images`.`name` AS `file`,
				`{$wpdb->base_prefix}cptch_packages`.`folder` AS `folder`
			FROM
				`{$wpdb->base_prefix}cptch_images`
			LEFT JOIN
				`{$wpdb->base_prefix}cptch_packages`
			ON
				`{$wpdb->base_prefix}cptch_packages`.`id`=`{$wpdb->base_prefix}cptch_images`.`package_id`
			WHERE
				`{$wpdb->base_prefix}cptch_images`.`package_id` {$where}
				AND
				`{$wpdb->base_prefix}cptch_images`.`number`={$value};",
			ARRAY_N
		);
		if ( empty( $images ) )
			return cptch_generate_value( $value, $use_only_words );

		if ( is_multisite() ) {
			switch_to_blog( 1 );
			$upload_dir = wp_upload_dir();
			restore_current_blog();
		} else {
			$upload_dir = wp_upload_dir();
		}
		$current_image = $images[ mt_rand( 0, count( $images ) - 1 ) ];
		$src = $upload_dir['basedir'] . '/bws_captcha_images/' . $current_image[1] . '/' . $current_image[0];
		if ( file_exists( $src ) ) {
			if ( 1 == $cptch_options['enlarge_images'] ) {
				switch( $place ) {
					case 0:
						$class = 'cptch_left';
						break;
					case 1:
						$class = 'cptch_center';
						break;
					case 2:
						$class = 'cptch_right';
						break;
					default:
						$class = '';
						break;
				}
			} else {
				$class = '';
			}
			$src = $upload_dir['basedir'] . '/bws_captcha_images/' . $current_image[1] . '/' . $current_image[0];
			$image_data = getimagesize( $src );
			return isset( $image_data['mime'] ) && ! empty( $image_data['mime'] ) ? '<img class="cptch_img ' . $class . '" src="data:' . $image_data['mime'] . ';base64,'. base64_encode( file_get_contents( $src ) ) . '" alt="image"/>' :  cptch_generate_value( $value, $use_only_words );
		} else {
			return cptch_generate_value( $value, $use_only_words );
		}
	}
}

if ( ! function_exists( 'cptch_generate_value' ) ) {
	function cptch_generate_value( $value, $use_only_words = true ) {
		$random = $use_only_words  ? 1 : mt_rand( 0, 1 );
		if ( 1 == $random ) {
			$number_string = array(
				0 => __( 'zero', 'captcha' ),
				1 => __( 'one', 'captcha' ),
				2 => __( 'two', 'captcha' ),
				3 => __( 'three', 'captcha' ),
				4 => __( 'four', 'captcha' ),
				5 => __( 'five', 'captcha' ),
				6 => __( 'six', 'captcha' ),
				7 => __( 'seven', 'captcha' ),
				8 => __( 'eight', 'captcha' ),
				9 => __( 'nine', 'captcha' ),

				10 => __( 'ten', 'captcha' ),
				11 => __( 'eleven', 'captcha' ),
				12 => __( 'twelve', 'captcha' ),
				13 => __( 'thirteen', 'captcha' ),
				14 => __( 'fourteen', 'captcha' ),
				15 => __( 'fifteen', 'captcha' ),
				16 => __( 'sixteen', 'captcha' ),
				17 => __( 'seventeen', 'captcha' ),
				18 => __( 'eighteen', 'captcha' ),
				19 => __( 'nineteen', 'captcha' ),

				20 => __( 'twenty', 'captcha' ),
				21 => __( 'twenty one', 'captcha' ),
				22 => __( 'twenty two', 'captcha' ),
				23 => __( 'twenty three', 'captcha' ),
				24 => __( 'twenty four', 'captcha' ),
				25 => __( 'twenty five', 'captcha' ),
				26 => __( 'twenty six', 'captcha' ),
				27 => __( 'twenty seven', 'captcha' ),
				28 => __( 'twenty eight', 'captcha' ),
				29 => __( 'twenty nine', 'captcha' ),

				30 => __( 'thirty', 'captcha' ),
				31 => __( 'thirty one', 'captcha' ),
				32 => __( 'thirty two', 'captcha' ),
				33 => __( 'thirty three', 'captcha' ),
				34 => __( 'thirty four', 'captcha' ),
				35 => __( 'thirty five', 'captcha' ),
				36 => __( 'thirty six', 'captcha' ),
				37 => __( 'thirty seven', 'captcha' ),
				38 => __( 'thirty eight', 'captcha' ),
				39 => __( 'thirty nine', 'captcha' ),

				40 => __( 'forty', 'captcha' ),
				41 => __( 'forty one', 'captcha' ),
				42 => __( 'forty two', 'captcha' ),
				43 => __( 'forty three', 'captcha' ),
				44 => __( 'forty four', 'captcha' ),
				45 => __( 'forty five', 'captcha' ),
				46 => __( 'forty six', 'captcha' ),
				47 => __( 'forty seven', 'captcha' ),
				48 => __( 'forty eight', 'captcha' ),
				49 => __( 'forty nine', 'captcha' ),

				50 => __( 'fifty', 'captcha' ),
				51 => __( 'fifty one', 'captcha' ),
				52 => __( 'fifty two', 'captcha' ),
				53 => __( 'fifty three', 'captcha' ),
				54 => __( 'fifty four', 'captcha' ),
				55 => __( 'fifty five', 'captcha' ),
				56 => __( 'fifty six', 'captcha' ),
				57 => __( 'fifty seven', 'captcha' ),
				58 => __( 'fifty eight', 'captcha' ),
				59 => __( 'fifty nine', 'captcha' ),

				60 => __( 'sixty', 'captcha' ),
				61 => __( 'sixty one', 'captcha' ),
				62 => __( 'sixty two', 'captcha' ),
				63 => __( 'sixty three', 'captcha' ),
				64 => __( 'sixty four', 'captcha' ),
				65 => __( 'sixty five', 'captcha' ),
				66 => __( 'sixty six', 'captcha' ),
				67 => __( 'sixty seven', 'captcha' ),
				68 => __( 'sixty eight', 'captcha' ),
				69 => __( 'sixty nine', 'captcha' ),

				70 => __( 'seventy', 'captcha' ),
				71 => __( 'seventy one', 'captcha' ),
				72 => __( 'seventy two', 'captcha' ),
				73 => __( 'seventy three', 'captcha' ),
				74 => __( 'seventy four', 'captcha' ),
				75 => __( 'seventy five', 'captcha' ),
				76 => __( 'seventy six', 'captcha' ),
				77 => __( 'seventy seven', 'captcha' ),
				78 => __( 'seventy eight', 'captcha' ),
				79 => __( 'seventy nine', 'captcha' ),

				80 => __( 'eighty', 'captcha' ),
				81 => __( 'eighty one', 'captcha' ),
				82 => __( 'eighty two', 'captcha' ),
				83 => __( 'eighty three', 'captcha' ),
				84 => __( 'eighty four', 'captcha' ),
				85 => __( 'eighty five', 'captcha' ),
				86 => __( 'eighty six', 'captcha' ),
				87 => __( 'eighty seven', 'captcha' ),
				88 => __( 'eighty eight', 'captcha' ),
				89 => __( 'eighty nine', 'captcha' ),

				90 => __( 'ninety', 'captcha' ),
				91 => __( 'ninety one', 'captcha' ),
				92 => __( 'ninety two', 'captcha' ),
				93 => __( 'ninety three', 'captcha' ),
				94 => __( 'ninety four', 'captcha' ),
				95 => __( 'ninety five', 'captcha' ),
				96 => __( 'ninety six', 'captcha' ),
				97 => __( 'ninety seven', 'captcha' ),
				98 => __( 'ninety eight', 'captcha' ),
				99 => __( 'ninety nine', 'captcha' )
			);
			$value = cptch_converting( $number_string[ $value ] );
		}
		return $value;
	}
}

if ( ! function_exists ( 'cptch_converting' ) ) {
	function cptch_converting( $number_string ) {
		global $cptch_options;

		if ( in_array( 'words', $cptch_options['operand_format'] ) && 'en-US' == get_bloginfo( 'language' ) ) {
			/* Array of htmlspecialchars for numbers and english letters */
			$htmlspecialchars_array			=	array();
			$htmlspecialchars_array['a']	=	'&#97;';
			$htmlspecialchars_array['b']	=	'&#98;';
			$htmlspecialchars_array['c']	=	'&#99;';
			$htmlspecialchars_array['d']	=	'&#100;';
			$htmlspecialchars_array['e']	=	'&#101;';
			$htmlspecialchars_array['f']	=	'&#102;';
			$htmlspecialchars_array['g']	=	'&#103;';
			$htmlspecialchars_array['h']	=	'&#104;';
			$htmlspecialchars_array['i']	=	'&#105;';
			$htmlspecialchars_array['j']	=	'&#106;';
			$htmlspecialchars_array['k']	=	'&#107;';
			$htmlspecialchars_array['l']	=	'&#108;';
			$htmlspecialchars_array['m']	=	'&#109;';
			$htmlspecialchars_array['n']	=	'&#110;';
			$htmlspecialchars_array['o']	=	'&#111;';
			$htmlspecialchars_array['p']	=	'&#112;';
			$htmlspecialchars_array['q']	=	'&#113;';
			$htmlspecialchars_array['r']	=	'&#114;';
			$htmlspecialchars_array['s']	=	'&#115;';
			$htmlspecialchars_array['t']	=	'&#116;';
			$htmlspecialchars_array['u']	=	'&#117;';
			$htmlspecialchars_array['v']	=	'&#118;';
			$htmlspecialchars_array['w']	=	'&#119;';
			$htmlspecialchars_array['x']	=	'&#120;';
			$htmlspecialchars_array['y']	=	'&#121;';
			$htmlspecialchars_array['z']	=	'&#122;';

			$simbols_lenght = strlen( $number_string );
			$simbols_lenght--;
			$number_string_new	=	str_split( $number_string );
			$converting_letters	=	rand( 1, $simbols_lenght );
			while ( $converting_letters != 0 ) {
				$position = rand( 0, $simbols_lenght );
				$number_string_new[ $position ] = isset( $htmlspecialchars_array[ $number_string_new[ $position ] ] ) ? $htmlspecialchars_array[ $number_string_new[ $position ] ] : $number_string_new[ $position ];
				$converting_letters--;
			}
			$number_string = '';
			foreach ( $number_string_new as $key => $value ) {
				$number_string .= $value;
			}
			return $number_string;
		} else
			return $number_string;
	}
}

/* Function for encodinf number */
if ( ! function_exists( 'cptch_encode' ) ) {
	function cptch_encode( $String, $Password, $timestamp ) {
		/* Check if key for encoding is empty */
		if ( ! $Password ) die ( __( "Encryption password is not set", 'captcha' ) );

		$Salt   = md5( $timestamp, true );
		$String = substr( pack( "H*", sha1( $String ) ), 0, 1 ) . $String;
		$StrLen = strlen( $String );
		$Seq    = $Password;
		$Gamma  = '';

		while ( strlen( $Gamma ) < $StrLen ) {
			$Seq = pack( "H*", sha1( $Seq . $Gamma . $Salt ) );
			$Gamma .=substr( $Seq, 0, 8 );
		}

		return base64_encode( $String ^ $Gamma );
	}
}

/* Function for decoding number */
if ( ! function_exists( 'cptch_decode' ) ) {
	function cptch_decode( $String, $Key, $timestamp ) {
		/* Check if key for encoding is empty */
		if ( ! $Key ) die ( __( "Decryption password is not set", 'captcha' ) );

		$Salt   = md5( $timestamp, true );
		$StrLen = strlen( $String );
		$Seq    = $Key;
		$Gamma  = '';

		while ( strlen( $Gamma ) < $StrLen ) {
			$Seq = pack( "H*", sha1( $Seq . $Gamma . $Salt ) );
			$Gamma.= substr( $Seq, 0, 8 );
		}

		$String = base64_decode( $String );
		$String = $String ^ $Gamma;
		$DecodedString = substr( $String, 1 );
		$Error         = ord( substr( $String, 0, 1 ) ^ substr( pack( "H*", sha1( $DecodedString ) ), 0, 1 ) );
		return $Error ? false : $DecodedString;
	}
}

/**
 * Check CAPTCHA life time
 * @return boolean
 */
if ( ! function_exists( 'cptch_limit_exhausted' ) ) {
	function cptch_limit_exhausted() {
		global $cptch_options;
		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );
		return
				1 == $cptch_options['enable_time_limit'] &&       /* if 'Enable time limit' option is enabled */
				isset( $_REQUEST['cptch_time'] ) &&            /* if form was sended */
				$cptch_options['time_limit'] < time() - $_REQUEST['cptch_time'] /* if time limit is exhausted */
			?
				true
			:
				false;
	}
}

if ( ! function_exists( 'cptch_front_end_styles' ) ) {
	function cptch_front_end_styles() {
		if ( ! is_admin() ) {
			global $cptch_options;
			if ( empty( $cptch_options ) )
				$cptch_options = get_option( 'cptch_options' );

			wp_enqueue_style( 'cptch_stylesheet', plugins_url( 'css/front_end_style.css', __FILE__ ), array(), $cptch_options['plugin_option_version'] );
			wp_enqueue_style( 'dashicons' );

			$device_type = isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Windows Phone|Opera Mini/i', $_SERVER['HTTP_USER_AGENT'] ) ? 'mobile' : 'desktop';
			wp_enqueue_style( "cptch_{$device_type}_style", plugins_url( "css/{$device_type}_style.css", __FILE__ ), array(), $cptch_options['plugin_option_version'] );
		}
	}
}

if ( ! function_exists( 'cptch_front_end_scripts' ) ) {
	function cptch_front_end_scripts() {
		global $cptch_options;

		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );

		if (
			wp_script_is( 'cptch_front_end_script', 'registered' ) &&
			! wp_script_is( 'cptch_front_end_script', 'enqueued' )
		) {
			wp_enqueue_script( 'cptch_front_end_script' );
			$args = array(
				'nonce'   => wp_create_nonce( 'cptch', 'cptch_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'enlarge' => $cptch_options['enlarge_images']
			);
			wp_localize_script( 'cptch_front_end_script', 'cptch_vars', $args );
		}
	}
}

if ( ! function_exists ( 'cptch_admin_head' ) ) {
	function cptch_admin_head() {
		if ( isset( $_REQUEST['page'] ) && ('captcha.php' == $_REQUEST['page'] || 'cptc_dashboard' == $_REQUEST['page'] ) ) {
			global $cptch_options;
			wp_enqueue_style( 'cptch_stylesheet', plugins_url( 'css/style.css', __FILE__ ), array(), $cptch_options['plugin_option_version'] );
			wp_enqueue_style( 'cptch_dash_stylesheet', plugins_url( 'css/dashboard_style.css', __FILE__ ), array(), $cptch_options['plugin_option_version'] );
			wp_enqueue_style( 'cptch_slick_css', plugins_url( 'css/slick.css', __FILE__ ), array(), $cptch_options['plugin_option_version'] );
			wp_enqueue_script( 'cptch_slick', plugins_url( 'js/slick.min.js' , __FILE__ ), array( 'jquery' ), $cptch_options['plugin_option_version'] );
			wp_enqueue_script( 'cptch_script', plugins_url( 'js/script.js' , __FILE__ ), array( 'jquery', 'jquery-ui-resizable', 'jquery-ui-tabs' ), $cptch_options['plugin_option_version'] );
			$args = array(
				'start_tab' => isset( $_REQUEST['cptch_active_tab'] ) ? absint( $_REQUEST['cptch_active_tab'] ) : 0
			);
			wp_localize_script( 'cptch_script', 'cptch_vars', $args );
			if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] )
				bws_plugins_include_codemirror();
		}
	}
}

if ( ! function_exists( 'cptch_reload' ) ) {
	function cptch_reload() {
		check_ajax_referer( 'cptch', 'cptch_nonce' );

		if ( ! defined( 'CPTCH_RELOAD_AJAX' ) )
			define( 'CPTCH_RELOAD_AJAX', true );

		$form_slug  = isset( $_REQUEST['cptch_form_slug'] )   ? esc_attr( $_REQUEST['cptch_form_slug'] )   : 'general';
		$class      = isset( $_REQUEST['cptch_input_class'] ) ? esc_attr( $_REQUEST['cptch_input_class'] ) : '';
		$input_name = isset( $_REQUEST['cptch_input_name'] )  ? esc_attr( $_REQUEST['cptch_input_name'] )  : '';

		echo cptch_display_captcha_custom( $form_slug, $class, $input_name );
		die();
	}
}

if ( ! function_exists( 'cptch_plugin_action_links' ) ) {
	function cptch_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=captcha.php">' . __( 'Settings', 'captcha' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'cptch_register_plugin_links' ) ) {
	function cptch_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=captcha.php">' . __( 'Settings', 'captcha' ) . '</a>';
			$links[]	=	'<a href="https://wordpress.org/plugins/captcha/" target="_blank">' . __( 'FAQ', 'captcha' ) . '</a>';
			$links[]	=	'<a href="https://wordpress.org/plugins/captcha/">' . __( 'Support', 'captcha' ) . '</a>';
		}
		return $links;
	}
}

/* Notice on the settings page about possible conflict with W3 Total Cache plugin */
if ( ! function_exists( 'cptch_w3tc_notice' ) ) {
	function cptch_w3tc_notice() {
		global $cptch_options, $cptch_plugin_info;
		if ( ! is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			return;
		}

		if ( empty( $cptch_options ) )
			$cptch_options = is_network_admin() ? get_site_option( 'cptch_options' ) : get_option( 'cptch_options' );

		if ( empty( $cptch_options['w3tc_notice'] ) )
			return '';

		if( isset( $_GET['cptch_nonce'] ) && wp_verify_nonce( $_GET['cptch_nonce'], 'cptch_clean_w3tc_notice' ) ) {
			unset( $cptch_options['w3tc_notice'] );
			if ( is_network_admin() ) {
				update_site_option( 'cptch_options', $cptch_options );
			} else {
				update_option( 'cptch_options', $cptch_options );
			}
			return '';
		}

		$url = add_query_arg(
			array(
				'cptch_clean_w3tc_notice'	=> '1',
				'cptch_nonce'				=> wp_create_nonce( 'cptch_clean_w3tc_notice' )
			),
			( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		);
		$close_link = "<a href=\"{$url}\" class=\"close_icon notice-dismiss\"></a>";
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'admin.php?page=captcha.php#cptch_load_via_ajax' ),
			__( 'settings page', 'captcha' )
		);
		$message = sprintf(
			__( 'You\'re using W3 Total Cache plugin. If %1$s doesn\'t work properly, please clear the cache in W3 Total Cache plugin and turn on \'%2$s\' option on the plugin %3$s.', 'captcha' ),
			$cptch_plugin_info['Name'],
			__( 'Show CAPTCHA after the end of the page loading', 'captcha' ),
			$settings_link
		);
		return
			"<style>
				.cptch_w3tc_notice {
					position: relative;
				}
				.cptch_w3tc_notice a {
					text-decoration: none;
				}
			</style>
			<div class=\"cptch_w3tc_notice error\"><p>{$message}</p>{$close_link}</div>";
	}
}

if ( ! function_exists ( 'cptch_plugin_banner' ) ) {
	function cptch_plugin_banner() {
		global $hook_suffix, $cptch_plugin_info;

		/* Displays notice about possible conflict with W3 Total Cache plugin */
		echo cptch_w3tc_notice();

		if ( 'plugins.php' == $hook_suffix )
			bws_plugin_banner_to_settings( $cptch_plugin_info, 'cptch_options', 'captcha', 'admin.php?page=captcha.php' );

		if ( isset( $_GET['page'] ) && 'captcha.php' == $_GET['page'] )
			bws_plugin_suggest_feature_banner( $cptch_plugin_info, 'cptch_options', 'captcha' );
	}
}


/* Function for delete delete options */
if ( ! function_exists ( 'cptch_delete_options' ) ) {
	function cptch_delete_options() {
		global $wpdb;
		$all_plugins        = get_plugins();
		$is_another_captcha = array_key_exists( 'captcha-plus/captcha-plus.php', $all_plugins ) || array_key_exists( 'captcha-pro/captcha_pro.php', $all_plugins );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );

		/* do nothing more if Plus or Pro BWS CAPTCHA are installed */
		if ( $is_another_captcha )
			return;

		if ( is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'cptch_options' );
				$prefix = 1 == $blog_id ? $wpdb->base_prefix : $wpdb->base_prefix . $blog_id . '_';
				$wpdb->query( "DROP TABLE `{$prefix}cptch_whitelist`;" );
			}
			switch_to_blog( 1 );
			$upload_dir = wp_upload_dir();
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'cptch_options' );
			$wpdb->query( "DROP TABLE `{$wpdb->prefix}cptch_whitelist`;" );
			$upload_dir = wp_upload_dir();
		}

		/* delete images */
		$wpdb->query( "DROP TABLE `{$wpdb->base_prefix}cptch_images`, `{$wpdb->base_prefix}cptch_packages`;" );
		$images_dir = $upload_dir['basedir'] . '/bws_captcha_images';
		$packages   = scandir( $images_dir );
		if ( is_array( $packages ) ) {
			foreach ( $packages as $package ) {
				if ( ! in_array( $package, array( '.', '..' ) ) ) {
					/* remove all files from package */
					array_map( 'unlink', glob( "{$images_dir}/{$package}/*.*" ) );
					/* remove package */
					rmdir( "{$images_dir}/{$package}" );
				}
			}
		}
		rmdir( $images_dir );
	}
}

/**
 *
 * @since 4.2.3
 */
if( ! function_exists( 'cptch_captcha_is_needed' ) ) {
	function cptch_captcha_is_needed( $form_slug, $user_loggged_in ) {
		global $cptch_options;
		return
			$cptch_options['forms'][ $form_slug ]['enable'] &&
			(
				! $user_loggged_in ||
				! $cptch_options['forms'][ $form_slug ]['hide_from_registered']
			);
	}
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_deprecated_message' ) ) {
	function cptch_deprecated_message( $args = '' ) {
		global $hook_suffix;

		$defaults = array(
			/* string; desc: plugin`s basename; format: '{plugin_folder}/{mani_plugin_file.php}'; example: 'captcha/captcha.php'  */
			'plugin'       => '',
			/* string; desc: min. plugin`s version that is fully compatible with the current plugin; example: '1.1.1' */
			'version'      => '',
			/* string; desc: the date after which the current plugin will no longer work with  $defaults['plugin']; example: '12/18/2016', '31.1.2020' */
			'date'         => '',
			/* string/array; page slug; example: 'plugins.php' or array( 'plugins.php', 'captcha_pro.php', 'bws-plugins_page_sbscrbrpr_settings_page' ) */
			'show_on'      => '',
			/* string; desc: name of current plugin */
			'current_name' => '',
			/* string; desc: message status; values: 'deprecated' or 'removed' */
			'status'       => 'deprecated'
		);

		$param = wp_parse_args( $args, $defaults );
		$path  = ABSPATH . 'wp-content/plugins/' . $param['plugin'];
		if (
			empty( $param['plugin'] ) ||
			empty( $param['version'] ) ||
			! file_exists( $path ) ||
			! is_plugin_active( $param['plugin'] )
		)
			return false;

		$old_plugin = get_plugin_data( $path );

		if ( 0 <= version_compare( $old_plugin['Version'], $param['version'] ) )
			return false;

		if ( empty( $param['current_name'] ) ) {
			$current_plugin = get_plugin_data( __FILE__ );
			$param['current_name'] = $current_plugin['Name'];
		}

		if ( empty( $param['show_on'] ) )
			$param['show_on'] = 'plugins.php';

		$show_on = (array)$param['show_on'];
		if (
			in_array( $hook_suffix, $show_on ) ||
			( isset( $_GET['page'] ) && in_array( $_GET['page'], $show_on ) )
		) {
			$message = '<strong>' . __( 'Warning', 'captcha' ) . ':</strong>&nbsp;' . $old_plugin['Name'] . '&nbsp;';
			switch( $param['status'] ) {
				case 'deprecated':
					$message .=
						__( 'plugin contains deprecated functionality that will be removed', 'captcha' ) . '&nbsp;' .
						( empty( $param['date'] ) ? __( 'in the future', 'captcha' ) : __( 'after', 'captcha' ) . '&nbsp;' . date_i18n( get_option( 'date_format' ), strtotime( $param['date'] ) ) );
					break;
				case 'removed':
					$message .= __( 'has old version', 'captcha' );
					break;
				default:
					$message .= __( 'has compatibility problems with', 'captcha' ) . '&nbsp;' . $param['current_name'];
					break;
			}
			return
				'<div class="error">
					<p>' .
						$message . '.' . '<br/>' .
						__( 'You need to update this plugin for correct work with', 'captcha' ) . '&nbsp;' . $param['current_name'] . '.' .
					'</p>
				</div>';
		}
	}
}


register_activation_hook( __FILE__, 'cptch_plugin_activate' );

add_action( 'admin_menu', 'cptch_admin_menu' );

add_action( 'init', 'cptch_init' );
add_action( 'admin_init', 'cptch_admin_init' );

add_action( 'plugins_loaded', 'cptch_plugins_loaded' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'cptch_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'cptch_register_plugin_links', 10, 2 );

add_action( 'admin_notices', 'cptch_plugin_banner' );

add_action( 'admin_enqueue_scripts', 'cptch_admin_head' );
add_action( 'wp_enqueue_scripts', 'cptch_front_end_styles' );
add_action( 'login_enqueue_scripts', 'cptch_front_end_styles' );

add_action( 'wp_ajax_cptch_reload', 'cptch_reload' );
add_action( 'wp_ajax_nopriv_cptch_reload', 'cptch_reload' );

add_filter( 'cptch_display', 'cptch_display_filter', 10, 3 );
add_filter( 'cptch_verify', 'cptch_check_custom_form', 10, 2 );

add_shortcode( 'bws_captcha', 'cptch_display_captcha_shortcode' );
add_filter( 'bws_shortcode_button_content', 'cptch_shortcode_button_content' );

register_uninstall_hook( __FILE__, 'cptch_delete_options' );
include "hcptch-contact-form-integration.php";


function captcha_shortcode_custom($tag){

	$captcha =  cptch_display_filter();
	return $captcha;

}
add_shortcode('wpcaptcha', 'captcha_shortcode_custom');