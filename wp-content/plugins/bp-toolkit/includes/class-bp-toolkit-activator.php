<?php

/**
* Fired during plugin activation
*
* @link       https://www.bouncingsprout.com
* @since      1.0.0
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
*/
/**
* Fired during plugin activation.
*
* This class defines all code necessary to run during the plugin's activation.
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts
*/
class BP_Toolkit_Activator
{
    /**
     *
     * @since    1.0.0
     *
     */
    public static function activate()
    {
        if ( defined( 'BP_TOOLKIT_VERSION' ) ) {
            $version = BP_TOOLKIT_VERSION;
        }
        $bp_toolkit = 'bp_toolkit';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-toolkit-admin.php';
        $plugin_admin = new BP_Toolkit_Admin( $bp_toolkit, $version );
        $plugin_admin->setup_report_post_type();
        wp_insert_term(
            'Spam',
            // the term
            'report-type'
        );
        wp_insert_term(
            'Offensive',
            // the term
            'report-type'
        );
        wp_insert_term(
            'Misleading or scam',
            // the term
            'report-type'
        );
        wp_insert_term(
            'Violent or abusive',
            // the term
            'report-type'
        );
        $plugin_admin->force_add_report_caps();
        // ATTENTION: This is *only* done during plugin activation hook in this example!
        // You should *NEVER EVER* do this on every page load!!
        flush_rewrite_rules();
        // Install our emails
        bptk_install_emails();
        // set_transient( 'bptk-admin-notice-activation', true, 5 );
        // ToDo - make this better (removed due to memory issues on large user count sites)
        // Check to see if not done previously, otherwise rebuild blocks database
        //		if ( get_option( 'bptk_blocks_already_rebuilt' ) == false ) {
        //			$users = get_users();
        //			foreach($users as $user){
        //				$block_list = get_user_meta( $user->ID, 'bptk_block', true );
        //				if ($block_list) {
        //					foreach ($block_list as $key => $blocked_user) {
        //						$list = get_user_meta( $blocked_user, 'bptk_blocked_by', true );
        //
        //						if ( $list ) {
        //							$key = array_search( $user->ID, $list );
        //							if ( $key === false ) {
        //								$list[] = $user->ID;
        //								update_user_meta( $blocked_user, 'bptk_blocked_by', $list );
        //							}
        //						} else {
        //							$list = array();
        //							$list[] = $user->ID;
        //							update_user_meta( $blocked_user, 'bptk_blocked_by', $list );
        //						}
        //					}
        //				}
        //			}
        //			update_option( 'bptk_blocks_already_rebuilt', 1 );
        //		}
    }

}