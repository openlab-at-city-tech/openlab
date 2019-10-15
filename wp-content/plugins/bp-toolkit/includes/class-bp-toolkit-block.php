<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
* The class that looks after the Toolkit's Block function
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BPTK_Block
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
        add_action( 'bp_init', array( $this, 'bptk_toggle_blocking' ) );
        add_action( 'bp_member_header_actions', array( $this, 'add_profile_block_button' ) );
        add_action( 'bp_directory_members_actions', array( $this, 'add_list_block_button' ) );
        add_action( 'bp_setup_nav', array( $this, 'setup_nav' ), 100 );
        add_action( 'bp_after_has_members_parse_args', array( $this, 'adjust_query' ) );
        add_filter( 'bp_get_total_member_count', array( $this, 'adjust_count' ) );
        add_filter( 'bp_get_member_latest_update', array( $this, 'redo_update' ) );
        add_action( 'bp_members_screen_display_profile', array( $this, 'display_block_screen' ) );
        
        if ( bp_is_active( 'messages' ) ) {
            add_filter( 'bp_messages_recipients', array( $this, 'check_recipients' ) );
            add_action( 'messages_message_before_save', array( $this, 'check_conversations' ) );
        }
    
    }
    
    /**
     * Constructs link to pass when blocking.
     * @since 1.0
     */
    public function bptk_block_link( $list_id = 0, $user_id = 0 )
    {
        return apply_filters(
            'bptk_block_link',
            add_query_arg( array(
            'action' => 'block',
            'list'   => $list_id,
            'num'    => $user_id,
            'token'  => wp_create_nonce( 'block-' . $list_id ),
        ) ),
            $list_id,
            $user_id
        );
    }
    
    /**
     * Constructs link to pass when unblocking.
     * @since 1.0
     */
    public function bptk_unblock_link( $list_id = 0, $user_id = 0 )
    {
        return apply_filters(
            'bptk_unblock_link',
            add_query_arg( array(
            'action' => 'unblock',
            'list'   => $list_id,
            'num'    => $user_id,
            'token'  => wp_create_nonce( 'unblock-' . $list_id ),
        ) ),
            $list_id,
            $user_id
        );
    }
    
    /**
     * Removes a user from a block list.
     * @since 1.0
     */
    public function remove_user( $list_id = NULL, $id_to_remove = NULL )
    {
        $current = get_blocked_users( $list_id );
        $new = array();
        foreach ( (array) $current as $user_id ) {
            if ( $user_id != $id_to_remove ) {
                $new[] = $user_id;
            }
        }
        update_user_meta( $list_id, 'bptk_block', apply_filters(
            'remove_user',
            $new,
            $list_id,
            $id_to_remove
        ) );
    }
    
    /**
     * Add Block Button to Member profiles.
     * @since 1.0
     */
    public function add_profile_block_button()
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
            echo  '<li class="generic-button bptk-block-profile"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></li>' ;
        } else {
            echo  '<div class="generic-button bptk-block-profile"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
        }
    
    }
    
    /**
     * Add Block Button to Member lists.
     * @since 1.0
     */
    public function add_list_block_button()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        $user_id = get_current_user_id();
        $member_id = bp_get_member_user_id();
        if ( user_can( $member_id, BPTK_ADMIN_CAP ) || bp_loggedin_user_id() == bp_get_member_user_id() ) {
            return;
        }
        
        if ( bp_get_theme_package_id() == 'nouveau' ) {
            echo  '<li class="generic-button bptk-block-list"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></li>' ;
        } else {
            echo  '<div class="generic-button bptk-block-list"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
        }
    
    }
    
    /**
     * This is where the magic happens. Block/unblock depending
     * on what arguments were passed via our links above.
     * @since 1.0
     */
    public function bptk_toggle_blocking()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        if ( !isset( $_REQUEST['action'] ) || !isset( $_REQUEST['list'] ) || !isset( $_REQUEST['token'] ) || !isset( $_REQUEST['num'] ) ) {
            return;
        }
        switch ( $_REQUEST['action'] ) {
            case 'unblock':
                
                if ( wp_verify_nonce( $_REQUEST['token'], 'unblock-' . $_REQUEST['list'] ) ) {
                    $current = $this->get_blocked_users( (int) $_REQUEST['list'] );
                    
                    if ( isset( $current[$_REQUEST['num']] ) ) {
                        unset( $current[$_REQUEST['num']] );
                        update_user_meta( (int) $_REQUEST['list'], 'bptk_block', $current );
                        bp_core_add_message( __( 'User successfully unblocked', 'bp-toolkit' ) );
                    }
                
                }
                
                break;
            case 'block':
                
                if ( wp_verify_nonce( $_REQUEST['token'], 'block-' . $_REQUEST['list'] ) ) {
                    $current = $this->get_blocked_users( (int) $_REQUEST['list'] );
                    
                    if ( user_can( (int) $_REQUEST['num'], BPTK_ADMIN_CAP ) ) {
                        bp_core_add_message( __( 'You cannot block administrators or moderators', 'bp-toolkit' ), 'error' );
                    } else {
                        $current[] = (int) $_REQUEST['num'];
                        update_user_meta( (int) $_REQUEST['list'], 'bptk_block', $current );
                        bp_core_add_message( __( 'User successfully blocked.', 'bp-toolkit' ) );
                    }
                
                }
                
                break;
        }
        wp_safe_redirect( wp_get_referer() );
        exit;
    }
    
    /**
     * Sets up the 'blocked' list under our profiles.
     * @since 1.0
     */
    public function setup_nav()
    {
        global  $bp ;
        if ( !is_user_logged_in() || !current_user_can( BPTK_ADMIN_CAP ) && get_current_user_id() != bp_displayed_user_id() ) {
            return;
        }
        bp_core_new_subnav_item( array(
            'name'                    => __( 'Blocked Members', 'bp-toolkit' ),
            'slug'                    => 'bptk_blocked',
            'parent_url'              => trailingslashit( bp_displayed_user_domain() . 'settings/' ),
            'parent_slug'             => 'settings',
            'screen_function'         => array( $this, 'load_nav' ),
            'show_for_displayed_user' => false,
        ) );
    }
    
    /**
     * Loads the profile nav elements.
     * @since 1.0
     */
    public function load_nav()
    {
        add_action( 'bp_template_title', array( $this, 'load_nav_title' ) );
        add_action( 'bp_template_content', array( $this, 'load_nav_content' ) );
        bp_core_load_template( 'members/single/plugins' );
    }
    
    /**
     * Loads the profile nav title.
     * @since 1.0
     */
    public function load_nav_title()
    {
        
        if ( current_user_can( BPTK_ADMIN_CAP ) && get_current_user_id() != bp_displayed_user_id() ) {
            echo  __( 'Members this user blocks', 'bp-toolkit' ) ;
        } else {
            echo  __( 'Members you currently block', 'bp-toolkit' ) ;
        }
    
    }
    
    /**
     * Loads the profile nav content.
     * @since 1.0
     */
    public function load_nav_content()
    {
        $profile_id = bp_displayed_user_id();
        $token = wp_create_nonce( 'unblock-' . $profile_id );
        $list = $this->get_blocked_users( $profile_id );
        if ( empty($list) ) {
            $list[] = 0;
        }
        ?>

    <table class="users-blocked">
      <thead>
        <th class="user" style="width:70%;"><?php 
        _e( 'User', 'bp-toolkit' );
        ?></th>
        <th class="actions" style="width:30%;"><?php 
        _e( 'Actions', 'bp-toolkit' );
        ?></th>
      </thead>
      <tbody>
        <?php 
        // Loop though our block list
        foreach ( (array) $list as $num => $user_id ) {
            // Zero means list is empty
            
            if ( $user_id == 0 ) {
                ?>

            <tr>
              <td colspan="2"><?php 
                _e( 'No users found', 'bp-toolkit' );
                ?></td>
            </tr>
            <?php 
            } else {
                $user = get_user_by( 'id', $user_id );
                // If user has been removed, remove it from our list as well
                
                if ( $user === false ) {
                    remove_user( $profile_id, $user_id );
                    continue;
                }
                
                ?>

            <tr>
              <td class="user"><?php 
                echo  $user->display_name ;
                ?></td>
              <td class="actions"><a href="<?php 
                echo  $this->bptk_unblock_link( $profile_id, $num ) ;
                ?>"><?php 
                _e( 'Unblock', 'bp-toolkit' );
                ?></a></td>
            </tr>
            <?php 
            }
        
        }
        ?>

      </tbody>
    </table>
    <?php 
    }
    
    /**
     * Get a list of blocked users
     * @since 1.0
     */
    public function get_blocked_users( $user_id = NULL )
    {
        if ( $user_id === NULL ) {
            return;
        }
        $list = get_user_meta( $user_id, 'bptk_block', true );
        if ( empty($list) ) {
            $list = array();
        }
        $_list = apply_filters( 'get_blocked_users', $list, $user_id );
        return array_filter( $_list );
    }
    
    /**
     * Adjusts the member query so blocked users aren't shown.
     * @since 1.0
     */
    public function adjust_query( $args )
    {
        if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
            return $args;
        }
        $excluded = ( isset( $args['exclude'] ) ? $args['exclude'] : array() );
        if ( !is_array( $excluded ) ) {
            $excluded = explode( ',', $excluded );
        }
        $blocked_ids = $this->get_blocked_users( get_current_user_id() );
        $excluded = array_merge( $excluded, $blocked_ids );
        $args['exclude'] = $excluded;
        return $args;
    }
    
    /**
     * Adjusts the member count to reflect the function above.
     * @since 1.0
     */
    public function adjust_count( $count )
    {
        if ( !is_user_logged_in() ) {
            return $count;
        }
        $list = count( $this->get_blocked_users( get_current_user_id() ) );
        if ( $list === 0 ) {
            return $count;
        }
        return $count - $list;
    }
    
    /**
     * Make sure that we don't see blocked member's updates
     * @since 1.0.1
     */
    public function redo_update( $update_content )
    {
        
        if ( is_user_logged_in() ) {
            $list = $this->get_blocked_users( bp_get_member_user_id() );
            if ( in_array( get_current_user_id(), $list ) ) {
                return '';
            }
        }
        
        return $update_content;
    }
    
    /**
     * Display warning that you are trying to access a blocked user, or they have blocked you.
     * @since 1.0
     */
    function display_block_screen()
    {
        if ( !is_user_logged_in() ) {
            return;
        }
        // Step 1 - Check if the current user is blocking the profile viewed
        $list = $this->get_blocked_users( get_current_user_id() );
        
        if ( !empty($list) && in_array( bp_displayed_user_id(), $list ) ) {
            $template = locate_template( 'members/single/blocked.php', false );
            
            if ( empty($template) ) {
                load_template( BPTK_TEMPLATES . '/blocked.php' );
            } else {
                bp_core_load_template( 'members/single/blocked' );
            }
            
            exit;
        }
        
        // Step 2 - Check if the current profile is blocking the current user
        $_list = $this->get_blocked_users( bp_displayed_user_id() );
        
        if ( !empty($_list) && in_array( get_current_user_id(), $_list ) ) {
            $template = locate_template( 'members/single/blocking.php', false );
            
            if ( empty($template) ) {
                load_template( BPTK_TEMPLATES . '/blocking.php' );
            } else {
                bp_core_load_template( 'members/single/blocking' );
            }
            
            exit;
        }
    
    }
    
    /**
     * Check recipients before sending message.
     * @since 1.0
     */
    function check_recipients( $recipients )
    {
        $cui = bp_loggedin_user_id();
        $_list = $this->get_blocked_users( $cui );
        // Loop though receipients and convert them into a list of user IDs
        // Based on messages_new_message()
        $recipient_ids = array();
        foreach ( (array) $recipients as $recipient ) {
            $recipient = trim( $recipient );
            if ( empty($recipient) ) {
                continue;
            }
            $recipient_id = false;
            // input was numeric
            
            if ( is_numeric( $recipient ) ) {
                // do a check against the user ID column first
                
                if ( bp_core_get_core_userdata( (int) $recipient ) ) {
                    $recipient_id = (int) $recipient;
                } else {
                    
                    if ( bp_is_username_compatibility_mode() ) {
                        $recipient_id = bp_core_get_userid( (int) $recipient );
                    } else {
                        $recipient_id = bp_core_get_userid_from_nicename( (int) $recipient );
                    }
                
                }
            
            } else {
                
                if ( bp_is_username_compatibility_mode() ) {
                    $recipient_id = bp_core_get_userid( $recipient );
                } else {
                    $recipient_id = bp_core_get_userid_from_nicename( $recipient );
                }
            
            }
            
            // Make sure we are not trying to send a message to someone we are blocking
            if ( $recipient_id && !in_array( $recipient_id, $_list ) ) {
                $recipient_ids[] = (int) $recipient_id;
            }
        }
        // Remove duplicates
        $recipient_ids = array_unique( (array) $recipient_ids );
        // Loop though the user IDs and check for blocks
        $filtered = array();
        foreach ( (array) $recipient_ids as $user_id ) {
            $list = $this->get_blocked_users( $user_id );
            if ( !in_array( $cui, (array) $list ) ) {
                $filtered[] = $user_id;
            }
        }
        return $filtered;
    }
    
    /**
     * Check existing messages and suspend replies if recipient on block list.
     * @since 1.0
     */
    function check_conversations( $message )
    {
        if ( empty($message->recipients) ) {
            return;
        }
        $cui = bp_loggedin_user_id();
        $recipients = $message->recipients;
        // First make sure we are not sending a new message to someone we selected to block
        $_list = $this->get_blocked_users( $cui );
        foreach ( $_list as $_user_id ) {
            if ( array_key_exists( $_user_id, $recipients ) ) {
                unset( $recipients[$_user_id] );
            }
        }
        // Second make sure that the message receipients are not blocking us
        $filtered = array();
        foreach ( $recipients as $user_id => $receipient ) {
            $list = $this->get_blocked_users( $user_id );
            if ( !in_array( $cui, (array) $list ) ) {
                $filtered[$user_id] = $receipient;
            }
        }
        $message->recipients = $filtered;
    }

}