<?php
/**
 * Display content of "Dashboard" tab on dashboard page
 * @package Captcha
 * @since   4.1.4
 * @version 1.0.2
 */

if ( ! class_exists( 'Cptch_dashboard' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) )
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	class Cptch_dashboard extends WP_List_Table {
		private	$basename;

		/*

		* Constructor of class

		*/

		function __construct( $plugin_basename ) {
			global $cptch_options;
			if ( empty( $cptch_options ) )
				$cptch_options = get_option('cptch_options');
			$this->basename     = $plugin_basename;
		}

		/**
		 * Display content dashboard tab
		 * @return void
		 */
		function display_content_dashboard_tab() {
		global  $cptch_options;
		?>
        <div class="cptch-dash-inner-area">
            <div class="videoWrapper"><iframe width="600" height="315"
            src="https://www.youtube.com/embed/hniFiIUhAqc">
            </iframe>
            </div>



		</div>
		<?php
		}

		/**
		 * Display content simply secure
		 * @return void
		 */

		function cptch_display_content_secure_tab() {
		global  $cptch_options;
		?>
        <div class="cptch-dash-sub-tab">
        <h2 class="cptch-dash-nav-tab-wrapper">
			<ul class="cptch-dash-nav child_list">
				<li><a class="<?php if ( isset( $_GET['action']) &&  'whitelist' == $_GET['stab'] ) echo 'nav-tab-active';?>" href="admin.php?page=cptc_dashboard&amp;action=simply_secure&amp;stab=whitelist"><?php _e( 'White List IP', 'captcha' ); ?></a></li>
                <li><a class="<?php if ( isset( $_GET['action'] ) && 'blacklist' == $_GET['stab'] ) echo 'nav-tab-active'; ?>" href="admin.php?page=cptc_dashboard&amp;action=simply_secure&amp;stab=blacklist"><?php _e( 'Black List IP', 'captcha' ); ?></a>
                </li>
				<li><a class=" <?php if ( isset( $_GET['action'] ) && 'livetraffic' == $_GET['stab'] ) echo 'nav-tab-active'; ?>" href="admin.php?page=cptc_dashboard&amp;action=simply_secure&amp;stab=livetraffic"><?php _e( 'Live Traffic', 'captcha' ); ?></a>
                </li>
             </ul>
			</h2>

        </div>

		<?php
		}
		// static text
		function cpcth_get_static_text()
		{
			?>

			<div class="cptch_dash_simply_message"><p>Welcome to Simply Secure Beta our plugin is growing and constantly improving and as one of our users you get the chance to test our patented simply secure service for free.

We will be adding to the secure side of this plugin weekly so please feel free to let us know if you have an idea to make it better!</p></div>

		<?php }

	}
}