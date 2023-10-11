<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * The class that looks after the Toolkit's Report function
 *
 * @since      1.0.0
 * @package    BP_Toolkit
 * @subpackage BP_Toolkit/includes
 * @author     Ben Roberts
 */
class BPTK_Report
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $bp_toolkit The ID of this plugin.
     */
    private  $bp_toolkit ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @param string $bp_toolkit The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
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
                array( $this, 'after_created' ),
                10,
                3
            );
            add_action( 'wp_ajax_reset_moderated__premium_only', array( $this, 'reset_moderated__premium_only' ) );
            if ( in_array( 'members', $types ) ) {
                
                if ( class_exists( 'Youzify' ) ) {
                    add_action( 'youzify_after_header_cover_head_content', array( $this, 'add_profile_report_button' ) );
                } else {
                    add_action( 'bp_member_header_actions', array( $this, 'add_profile_report_button' ) );
                }
            
            }
        }
    
    }
    
    /**
     * Check user has the required level to report content.
     *
     * @param string $user_id The id of the user.
     *
     * @return     boolean   The result of the check.
     * @since 2.0
     *
     */
    public function has_required_level( $user_id )
    {
        $result = true;
        return $result;
    }
    
    /**
     * Checks to see if current user can report.
     *
     * @since 3.0.0
     *
     */
    public function can_report()
    {
        // Admins cannot report
        if ( current_user_can( BPTK_ADMIN_CAP ) ) {
            return false;
        }
        // You must be logged in to report
        if ( !is_user_logged_in() ) {
            return false;
        }
        return true;
    }
    
    /**
     * Checks to see if activity can be reported.
     *
     * @since 3.0.0
     *
     */
    public function can_be_reported( $member_id )
    {
        // Deleted users cannot be reported
        $user = get_userdata( $member_id );
        if ( $user === false ) {
            return false;
        }
        // Admins cannot be reported
        if ( user_can( $member_id, BPTK_ADMIN_CAP ) ) {
            return false;
        }
        // Your own activities cannot be reported
        if ( $member_id == bp_loggedin_user_id() ) {
            return false;
        }
        return true;
    }
    
    /**
     * Checks to see if an item has already been reported by that user.
     *
     * @since 3.0.0
     *
     */
    public function has_been_reported( $item_id, $activity_type )
    {
        // Firstly, check unique reporting hasn't been disabled. If it has, return false, as the rest of this method is redundant.
        $options = get_option( 'report_section' );
        if ( isset( $options['bptk_report_once_only'] ) && $options['bptk_report_once_only'] == 'on' ) {
            return false;
        }
        $current_user = bp_loggedin_user_id();
        // Build a query to check user hasn't reported this item before
        $args = array(
            'post_type'  => 'report',
            'meta_query' => array(
            'relation' => 'AND',
            array(
            'key'   => '_bptk_item_id',
            'value' => $item_id,
        ),
            array(
            'key'   => '_bptk_activity_type',
            'value' => $activity_type,
        ),
            array(
            'key'   => '_bptk_reported_by',
            'value' => $current_user,
        ),
        ),
        );
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    /**
     * Add Report Button to Member profiles.
     * @since 1.0
     */
    public function add_profile_report_button()
    {
        $member_id = bp_displayed_user_id();
        if ( $this->can_report() == false ) {
            return;
        }
        if ( $this->can_be_reported( $member_id ) == false ) {
            return;
        }
        
        if ( $this->has_been_reported( $member_id, 'member' ) == true ) {
            echo  '<li title="' . __( 'You cannot make multiple reports about the same thing', 'bp-toolkit' ) . '" class="generic-button bptk-report-profile bptk-report-button-disabled bptk-report-member-button"><a href="" class="activity-button">' . __( 'Report', 'bp-toolkit' ) . '</a></li>' ;
            return;
        }
        
        $link = bp_core_get_user_domain( $member_id );
        
        if ( class_exists( 'Youzify' ) ) {
            echo  '<li style="cursor: pointer;" class="bptk-report-button bptk-report-member-button youzify-name" data-link="' . $link . '" data-reported="' . $member_id . '"><i class="fa fa-flag" aria-hidden="true"></i><span>' . __( 'Report User', 'bp-toolkit' ) . '</span></li>' ;
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
                <input type="hidden" id="bptk-item-id">
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
                <textarea rows="5"
                          placeholder="<?php 
        echo  __( 'Please give as much detail as possible', 'bp-toolkit' ) ;
        ?>"
                          name="bptk-desc" id="bptk-desc"></textarea>

                <button class="button" id="bptk-report-submit" name="submit"
                        data-nonce="<?php 
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
        $item_id = $data['item_id'];
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
        // Add our post details
        $post = array(
            'post_title'   => $title,
            'post_content' => $details,
            'post_author'  => $reporter,
            'meta_input'   => array(
            '_bptk_member_reported' => $reported,
            '_bptk_reported_by'     => $reporter,
            '_bptk_link'            => $link,
            '_bptk_meta'            => $meta,
            '_bptk_item_id'         => $item_id,
            '_bptk_activity_type'   => $activity_type,
            '_bptk_user_report'     => true,
            'is_upheld'             => 1,
            'is_read'               => 0,
        ),
            'post_status'  => 'publish',
            'post_type'    => 'report',
        );
        $post_id = wp_insert_post( $post );
        wp_set_object_terms( $post_id, $report_type, 'report-type' );
        wp_send_json_success( __( 'Your report was successfully submitted.', 'bp-toolkit' ) );
    }
    
    /**
     * Do stuff once the post has been created
     *
     * @since 2.0
     *
     */
    public function after_created( $new_status, $old_status, $post )
    {
        
        if ( 'report' === $post->post_type && in_array( $new_status, array( 'publish', 'future' ), true ) && !in_array( $old_status, array( 'publish', 'future' ), true ) ) {
            $options = get_option( 'report_emails_section' );
            
            if ( isset( $options['bptk_report_emails_new_report'] ) && $options['bptk_report_emails_new_report'] == "on" ) {
                $item_id = $post->_bptk_item_id;
                $activity_type = $post->_bptk_activity_type;
                $post_id = $post->ID;
                bptk_send_email(
                    'bptk-admin-new-report',
                    $item_id,
                    $activity_type,
                    $post_id
                );
            }
            
            bptk_do_automod( $post->ID );
        }
    
    }
    
    /**
     * Break into comment reply link code, so we can add a report button.
     *
     * @param $comment_reply_link
     * @param $args
     * @param $comment
     * @param $post
     *
     * @return mixed|void
     */
    public function add_flagging_link_to_reply_link(
        $comment_reply_link,
        $args,
        $comment,
        $post
    )
    {
        $comment_id = $comment->comment_ID;
        $class = 'bptk-comments-report-link';
        $pattern = '#(<a.+class=.+comment-(reply|login)-l(i|o)(.*)[^>]+>)(.+)(</a>)#msiU';
        $replacement = "\$0 " . '<span class="' . $class . '">' . $this->get_flagging_link() . '</span>';
        // $0 is the matched pattern.
        $comment_reply_link = preg_replace( $pattern, $replacement, $comment_reply_link );
        return apply_filters( 'bptk_report_comments_comment_reply_link', $comment_reply_link );
    }
    
    /**
     * For unthreaded comments, add a button at the end of comment text.
     *
     * @param $comment_content
     * @param $comment
     * @param $args
     *
     * @return mixed|string
     */
    public function add_flagging_link_to_content( $comment_content, $comment, $args )
    {
        
        if ( get_option( 'thread_comments' ) ) {
            return $comment_content;
            // threaded, don't add it to the content.
        }
        
        $comment_id = $comment->comment_ID;
        $flagging_link = $this->get_flagging_link();
        if ( $flagging_link ) {
            $comment_content .= '<br /><span class="bptk-unthreaded-comments-report-link">' . $flagging_link . '</span>';
        }
        return $comment_content;
    }
    
    /**
     * Add a report button to WordPress Comments.
     *
     * @param string $comment_id
     * @param string $result_id
     * @param string $text
     *
     * @return mixed|string|void
     */
    public function get_flagging_link( $comment_id = '', $result_id = '', $text = '' )
    {
        global  $in_comment_loop ;
        if ( empty($comment_id) && !$in_comment_loop ) {
            return esc_html__( 'Something went wrong.', 'bp-toolkit' );
        }
        
        if ( empty($comment_id) ) {
            $comment_id = get_comment_ID();
        } else {
            $comment_id = (int) $comment_id;
        }
        
        $comment = get_comment( $comment_id );
        if ( !$comment ) {
            return esc_html__( 'This comment does not exist.', 'bp-toolkit' );
        }
        $link = get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id;
        $author_id = intval( $comment->user_id );
        if ( $this->can_report() == false ) {
            return;
        }
        if ( $this->can_be_reported( $author_id ) == false ) {
            return;
        }
        if ( bptk_is_item_moderated( $comment_id, 'comment' ) ) {
            return;
        }
        $text = esc_html__( 'Report', 'bp-toolkit' );
        
        if ( get_option( 'thread_comments' ) ) {
            $class = 'comment-reply-link';
        } else {
            $class = '';
        }
        
        
        if ( $this->has_been_reported( $comment_id, 'comment' ) == true ) {
            $title = esc_html__( 'You cannot make multiple reports', 'bp-toolkit' );
            return apply_filters( 'bptk_report_comments_flagging_link_disabled', '
				<a title="' . $title . '" onclick="return false;" style="text-decoration: none; opacity: 50%; cursor: not-allowed;" class="hide-if-no-js disabled bptk-report-comment-button ' . $class . '" href="" data-link="' . $link . '" data-reported="' . $author_id . '" data-id="' . $comment_id . '" rel="nofollow">' . $text . '</a>
			' );
        }
        
        return apply_filters( 'bptk_report_comments_flagging_link', '
				<a class="hide-if-no-js bptk-report-button bptk-report-comment-button ' . $class . '" href="#" data-link="' . $link . '" data-reported="' . $author_id . '" data-id="' . $comment_id . '" rel="nofollow">' . $text . '</a>
			' );
    }

}