<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
* The class that looks after the Toolkit's Suspend function
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BPTK_Suspend
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
        add_action( 'bp_init', array( $this, 'toggle_suspension' ) );
        
        if ( class_exists( 'Youzer' ) ) {
            add_action( 'yz_after_header_cover_head_content', array( $this, 'add_profile_suspend_button' ) );
        } else {
            add_action( 'bp_member_header_actions', array( $this, 'add_profile_suspend_button' ) );
        }
        
        add_action( 'bp_directory_members_actions', array( $this, 'add_list_suspend_button' ) );
        add_filter( 'authenticate', array( $this, 'prevent_login' ), 40 );
    }
    
    /**
     * Add suspend Button to Member profiles.
     * @since 1.0
     */
    public function add_profile_suspend_button()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        $user_id = get_current_user_id();
        $member_id = bp_displayed_user_id();
        $status = $this->is_suspended( $member_id );
        if ( bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) || !user_can( $user_id, BPTK_ADMIN_CAP ) ) {
            return;
        }
        
        if ( empty($status) || $status == 0 ) {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                
                if ( class_exists( 'Youzer' ) ) {
                    echo  '<li style="cursor: pointer;" class="bptk-suspend-profile yz-name"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-lock" aria-hidden="true"></i><span>' . __( 'Suspend User', 'bp-toolkit' ) . '</span></a></li>' ;
                } else {
                    echo  '<div class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend', 'bp-toolkit' ) . '</a></div>' ;
                }
            
            }
        
        } else {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                
                if ( class_exists( 'Youzer' ) ) {
                    echo  '<li style="cursor: pointer;" class="bptk-suspend-profile yz-name"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-unlock" aria-hidden="true"></i><span>' . __( 'Unsuspend User', 'bp-toolkit' ) . '</span></a></li>' ;
                } else {
                    echo  '<div class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend', 'bp-toolkit' ) . '</a></div>' ;
                }
            
            }
        
        }
    
    }
    
    /**
     * Add suspend Button to Member lists.
     * @since 1.0
     */
    public function add_list_suspend_button()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        $user_id = get_current_user_id();
        $member_id = bp_get_member_user_id();
        $status = $this->is_suspended( $member_id );
        if ( bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) || !user_can( $user_id, BPTK_ADMIN_CAP ) ) {
            return;
        }
        
        if ( empty($status) || $status == 0 ) {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-suspend-list"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                echo  '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend', 'bp-toolkit' ) . '</a></div>' ;
            }
        
        } else {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-suspend-list"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                echo  '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend', 'bp-toolkit' ) . '</a></div>' ;
            }
        
        }
    
    }
    
    /**
     * Constructs link to pass when blocking.
     * @since 1.0
     */
    public function bptk_suspend_link( $member_id = 0 )
    {
        return apply_filters( 'bptk_suspend_link', add_query_arg( array(
            'action' => 'suspend',
            'member' => $member_id,
            'token'  => wp_create_nonce( 'suspend-' . $member_id ),
        ) ), $member_id );
    }
    
    /**
     * Constructs link to pass when unblocking.
     * @since 1.0
     */
    public function bptk_unsuspend_link( $member_id = 0 )
    {
        return apply_filters( 'bptk_unsuspend_link', add_query_arg( array(
            'action' => 'unsuspend',
            'member' => $member_id,
            'token'  => wp_create_nonce( 'unsuspend-' . $member_id ),
        ) ), $member_id );
    }
    
    /**
     * Suspends member.
     * @since 1.0
     */
    public function suspend( $member_id = 0 )
    {
        update_user_meta( $member_id, 'bptk_suspend', 1 );
        if ( $this->is_suspended( $member_id ) == 1 ) {
            $this->destroy_sessions( $member_id );
        }
    }
    
    /**
     * Unsuspends member.
     * @since 1.0
     */
    public function unsuspend( $member_id = 0 )
    {
        update_user_meta( $member_id, 'bptk_suspend', 0 );
    }
    
    /**
     * Returns whether member is suspended.
     * @since 1.0
     */
    public function is_suspended( $member_id = 0 )
    {
        $status = get_user_meta( $member_id, 'bptk_suspend', true );
        
        if ( $status == 0 || empty($status) ) {
            return false;
        } else {
            return true;
        }
    
    }
    
    /**
     * This is where the magic happens. Suspend/unsuspend depending
     * on what arguments were passed via our links above.
     * @since 1.0
     */
    public function toggle_suspension()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        if ( !isset( $_REQUEST['action'] ) || !isset( $_REQUEST['member'] ) || !isset( $_REQUEST['token'] ) ) {
            return;
        }
        $tatus = $this->is_suspended( (int) $_REQUEST['member'] );
        switch ( $_REQUEST['action'] ) {
            case 'suspend':
                if ( wp_verify_nonce( $_REQUEST['token'], 'suspend-' . $_REQUEST['member'] ) ) {
                    
                    if ( empty($status) || $status == 0 ) {
                        $this->suspend( (int) $_REQUEST['member'] );
                        bp_core_add_message( __( 'User successfully suspended', 'bp-toolkit' ) );
                    }
                
                }
                break;
            case 'unsuspend':
                
                if ( wp_verify_nonce( $_REQUEST['token'], 'unsuspend-' . $_REQUEST['member'] ) ) {
                    $this->unsuspend( (int) $_REQUEST['member'] );
                    bp_core_add_message( __( 'User successfully unsuspended', 'bp-toolkit' ) );
                }
                
                break;
                break;
        }
        wp_safe_redirect( wp_get_referer() );
        exit;
    }
    
    /**
     * Stop a suspended member from logging in, and display an error.
     * @since 1.0
     */
    public function prevent_login( $member = null )
    {
        // If login already failed, get out
        if ( is_wp_error( $member ) || empty($member->ID) ) {
            return $member;
        }
        // Set the user id.
        $member_id = (int) $member->ID;
        // If the user is blocked, set the wp-login.php error message.
        
        if ( $this->is_suspended( $member_id ) ) {
            $options = get_option( 'suspend_section' );
            // Set the message, or a dfeault message if none specified.
            $message = ( isset( $options['bptk_suspend_login_message'] ) ? $options['bptk_suspend_login_message'] : '' );
            // Set an error object to short-circuit the authentication process.
            $member = new WP_Error( 'bptk_suspended_user', $message );
        }
        
        return apply_filters( 'prevent_login', $member, $member_id );
    }
    
    /**
     * Destroys all the user sessions for the specified user.
     * @since 1.0
     */
    public static function destroy_sessions( $member_id = 0 )
    {
        // Bail if no member id.
        if ( empty($member_id) ) {
            return;
        }
        // Get the user's sessions object and destroy all sessions.
        WP_Session_Tokens::get_instance( $member_id )->destroy_all();
    }

}