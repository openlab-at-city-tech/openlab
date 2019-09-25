<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
* The class that looks after the Toolkit's Report function
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BPTK_Report
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $bp_toolkit    The ID of this plugin.
     */
    private  $bp_toolkit ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $bp_toolkit       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $bp_toolkit, $version )
    {
        $this->bp_toolkit = $bp_toolkit;
        $this->version = $version;
        add_action( 'bp_init', array( $this, 'report' ) );
        add_action( 'bp_member_header_actions', array( $this, 'add_profile_report_button' ) );
    }
    
    /**
     * Constructs link to pass when reporting.
     * @since 1.0
     */
    public function report_link( $member_id = 0, $user_id = 0 )
    {
        return apply_filters( 'report_link', add_query_arg( array(
            'action' => 'report',
            'member' => $member_id,
            'user'   => $user_id,
            'nonce'  => wp_create_nonce( 'report-' . $member_id ),
        ) ) );
    }
    
    /**
     * Add Report Button to Member profiles.
     * @since 1.0
     */
    public function add_profile_report_button()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        $user_id = get_current_user_id();
        $member_id = bp_displayed_user_id();
        if ( bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) ) {
            return;
        }
        
        if ( bp_get_theme_package_id() == 'nouveau' ) {
            echo  '<li class="generic-button bptk-report-profile"><a href="' . $this->report_link( $member_id, $user_id ) . '" class="activity-button">' . __( 'Report', 'bp-toolkit' ) . '</a></li>' ;
        } else {
            echo  '<div class="generic-button bptk-report-profile"><a href="' . $this->report_link( $member_id, $user_id ) . '" class="activity-button">' . __( 'Report', 'bp-toolkit' ) . '</a></div>' ;
        }
    
    }
    
    /**
     * Do the magic. Catch a 'report button' press, send an email to admin, and notify user of the succesful report.
     * @since 1.0
     */
    public function report()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        if ( !isset( $_REQUEST['action'] ) || !isset( $_REQUEST['member'] ) || !isset( $_REQUEST['user'] ) || !isset( $_REQUEST['nonce'] ) ) {
            return;
        }
        if ( $_REQUEST['action'] == 'report' ) {
            
            if ( wp_verify_nonce( $_REQUEST['nonce'], 'report-' . $_REQUEST['member'] ) ) {
                $site_admin_email = get_option( 'admin_email' );
                $member = (int) $_REQUEST['member'];
                $user = (int) $_REQUEST['user'];
                $user_name = bp_core_get_username( $user );
                $member_name = bp_core_get_username( $member );
                $subject = __( 'A member has been reported', 'bp-toolkit' );
                $message = $user_name . __( ' has reported ', 'bp-toolkit' ) . $member_name . "\r\n\r\n";
                $message .= __( 'They have been advised an administrator will contact them as soon as possible.', 'bp-toolkit' ) . "\r\n\r\n";
                wp_mail( $site_admin_email, $subject, $message );
                bp_core_add_message( __( 'User successfully reported. An administrator will contact you for further details.', 'bp-toolkit' ) );
            }
        
        }
        wp_safe_redirect( wp_get_referer() );
        exit;
    }

}