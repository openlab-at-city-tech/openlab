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
        
        if ( class_exists( 'BuddyPress' ) ) {
            $options = get_option( 'report_section' );
            $types = ( isset( $options['bptk_report_activity_types'] ) ? (array) $options['bptk_report_activity_types'] : [] );
            add_action( 'wp_footer', array( $this, 'create_form' ) );
            add_action( 'wp_ajax_process_form', array( $this, 'process_form' ) );
            add_action(
                'transition_post_status',
                array( $this, 'send_email' ),
                10,
                3
            );
            if ( in_array( 'members', $types ) ) {
                
                if ( class_exists( 'Youzer' ) ) {
                    add_action( 'yz_after_header_cover_head_content', array( $this, 'add_profile_report_button' ) );
                } else {
                    add_action( 'bp_member_header_actions', array( $this, 'add_profile_report_button' ) );
                }
            
            }
        }
    
    }
    
    /**
     * Check user has the required level to report content.
     *
     * @since 2.0
     *
     * @param      string    $user_id       The id of the user.
     * @return     boolean   The result of the check.
     */
    public function has_required_level( $user_id )
    {
        $result = true;
        return $result;
    }
    
    /**
     * Add Report Button to Member profiles.
     * @since 1.0
     */
    public function add_profile_report_button()
    {
        $member_id = bp_displayed_user_id();
        $user_id = get_current_user_id();
        if ( !is_user_logged_in() || bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) || user_can( $user_id, BPTK_ADMIN_CAP ) ) {
            return;
        }
        if ( !$this->has_required_level( $user_id ) ) {
            return;
        }
        $link = bp_core_get_user_domain( $member_id );
        
        if ( class_exists( 'Youzer' ) ) {
            echo  '<li style="cursor: pointer;" class="bptk-report-button bptk-report-member-button yz-name" data-link="' . $link . '" data-reported="' . $member_id . '"><i class="fa fa-flag" aria-hidden="true"></i><span>' . __( 'Report User', 'bp-toolkit' ) . '</span></li>' ;
        } else {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-report-profile bptk-report-button bptk-report-member-button" data-link="' . $link . '" data-reported="' . $member_id . '"><a href="" class="activity-button">' . __( 'Report', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                echo  '<div class="generic-button bptk-report-profile bptk-report-button bptk-report-member-button" data-link="' . $link . '" data-reported="' . $member_id . '"><a href="" class="activity-button">' . __( 'Report', 'bp-toolkit' ) . '</a></div>' ;
            }
        
        }
    
    }
    
    /**
     * Create a modal to be hooked into the footer.
     * @since 1.0.1
     */
    public function create_form()
    {
        $reporter_id = get_current_user_id();
        $nonce = wp_create_nonce( 'report_nonce_' . $reporter_id );
        ?>

    <div class="bptk-report-modal bptk-report-closed" id="bptk-report-modal">



      <div class="bptk-report-modal-inner" id="bptk-report-modal-inner">
        <div class="bptk-report-modal-inner-header">
          <h4><?php 
        echo  __( 'New Report', 'bp-toolkit' ) ;
        ?></h4>
          <h4 class="bptk-report-close-button" id="bptk-report-close-button"><?php 
        echo  __( 'Close', 'bp-toolkit' ) ;
        ?></h4>
        </div>
        <input type="hidden" id="bptk-reported-id" value="">
        <input type="hidden" id="bptk-reporter-id" value="<?php 
        echo  $reporter_id ;
        ?>">
        <input type="hidden" id="bptk-activity-type">
        <input type="hidden" id="bptk-link">
        <input type="hidden" id="bptk-meta">
        <?php 
        $args = array(
            'show_option_none' => __( 'What type of report is this?', 'bp-toolkit' ),
            'orderby'          => 'name',
            'hierarchical'     => true,
            'hide_empty'       => 0,
            'taxonomy'         => 'report-type',
            'id'               => 'bptk-report-type',
            'name'             => 'bptk-report-type',
        );
        wp_dropdown_categories( $args );
        ?>
        <textarea rows="5" placeholder="<?php 
        echo  __( 'Please give as much detail as possible', 'bp-toolkit' ) ;
        ?>" name="bptk-desc" id="bptk-desc"></textarea>

        <button class="button" id="bptk-report-submit" name="submit" data-nonce="<?php 
        echo  $nonce ;
        ?>"><?php 
        echo  __( 'Send', 'bp-toolkit' ) ;
        ?></button>
        <p id="bptk-report-modal-response"></p>

      </div>
    </div>
    <div class="bptk-report-modal-overlay bptk-report-closed" id="bptk-report-modal-overlay">
    </div>

  <?php 
    }
    
    /**
     * Do the magic. Catch an advanced 'report button' press, send an email to admin, and notify user of the succesful report.
     * @since 2.0
     */
    public function process_form()
    {
        $data = $_REQUEST;
        $reporter = (int) $data['reporter'];
        $reported = (int) $data['reported'];
        $nonce = $data['nonce'];
        $link = $data['link'];
        $meta = ( $data['meta'] ? $data['meta'] : '' );
        $reported_member = get_user_by( 'ID', $reported );
        $activity_type = $data['activity_type'];
        $report_type = (int) $data['report_type'];
        $details = sanitize_textarea_field( $data['details'] );
        $title = $reported_member->display_name . "'s account has been reported";
        // check the nonce
        if ( check_ajax_referer( 'report_nonce_' . $data['reporter'], 'nonce', false ) == false ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        // Add out post details
        $post = array(
            'ID'           => $post_id,
            'post_title'   => $title,
            'post_content' => $details,
            'post_author'  => $reporter,
            'tax_input'    => array( $report_type ),
            'meta_input'   => array(
            '_bptk_member_reported' => $reported,
            '_bptk_reported_by'     => $reporter,
            '_bptk_link'            => $link,
            '_bptk_meta'            => $meta,
            '_bptk_activity_type'   => $activity_type,
            '_bptk_user_report'     => true,
        ),
            'post_status'  => 'publish',
            'post_type'    => 'report',
        );
        $post_id = wp_insert_post( $post );
        wp_set_object_terms( $post_id, $report_type, 'report-type' );
        wp_send_json_success( __( 'Your report was successfully submitted.', 'bp-toolkit' ) );
    }
    
    /**
     * Send an email if notifications enabled.
     * @since 2.0
     */
    public function send_email( $new_status, $old_status, $post )
    {
        
        if ( 'report' === $post->post_type && in_array( $new_status, array( 'publish', 'future' ), true ) && !in_array( $old_status, array( 'publish', 'future' ), true ) ) {
            $options = get_option( 'report_section' );
            if ( !isset( $options['bptk_report_toggle_emails'] ) ) {
                return;
            }
            
            if ( !get_post_meta( $post->ID, 'emailed_to_admin', true ) ) {
                $a = get_user_by( 'ID', $post->_bptk_reported_by );
                $reporter = $a->display_name;
                $raw_recipients = ( !empty($options['bptk_report_emails']) ? $options['bptk_report_emails'] : get_option( 'admin_email' ) );
                $recipients = str_replace( array( " ", "\r\n" ), '', $raw_recipients );
                $to = $recipients;
                $subject = __( 'New Report Submitted', 'bp-toolkit' );
                $body = sprintf(
                    __( '%1$s has reported a %2$s. You can reach the reported content <a href="%3$s">here</a>.', 'bp-toolkit' ),
                    $reporter,
                    $post->_bptk_activity_type,
                    $post->_bptk_link
                );
                $headers = array( 'Content-Type: text/html; charset=UTF-8' );
                wp_mail(
                    $to,
                    $subject,
                    $body,
                    $headers
                );
                // Flag email as having been sent now.
                update_post_meta( $post->ID, 'emailed_to_admin', time() );
            }
        
        }
    
    }

}