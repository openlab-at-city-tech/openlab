<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * The class that looks after the Toolkit's Block function
 *
 * @since      1.0.0
 * @package    BP_Toolkit
 * @subpackage BP_Toolkit/includes
 * @author     Ben Roberts
 */
class BPTK_Block
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
    public function __construct()
    {
        
        if ( class_exists( 'BuddyPress' ) ) {
            add_action( 'bp_init', array( $this, 'bptk_toggle_blocking' ) );
            
            if ( class_exists( 'Youzify' ) ) {
                add_action( 'youzify_after_header_cover_head_content', array( $this, 'add_profile_block_button' ) );
            } else {
                add_action( 'bp_member_header_actions', array( $this, 'add_profile_block_button' ) );
            }
            
            add_action( 'bp_directory_members_actions', array( $this, 'add_list_block_button' ) );
            add_action( 'bp_setup_nav', array( $this, 'setup_nav' ), 100 );
            add_action( 'admin_bar_menu', array( $this, 'setup_admin_bar' ), 300 );
            add_action( 'buddyboss_theme_after_bb_setting_menu', array( $this, 'setup_buddyboss_profile_menu' ), 9999 );
            add_action( 'bp_after_has_members_parse_args', array( $this, 'adjust_query' ) );
            add_filter( 'bp_get_total_member_count', array( $this, 'adjust_count' ) );
            add_filter( 'bp_get_member_latest_update', array( $this, 'redo_update' ) );
            add_action( 'bp_members_screen_display_profile', array( $this, 'display_block_screen' ) );
            add_filter( 'bp_activity_mentioned_users', array( $this, 'filter_mentions' ) );
            add_filter( 'bp_members_suggestions_query_args', array( $this, 'disable_suggestions_list' ) );
            add_filter( 'bp_groups_member_suggestions_query_args', array( $this, 'disable_suggestions_list' ) );
            add_filter( 'bp_activity_get', array( $this, 'filter_comments' ), 999 );
            add_filter( 'bp_activity_set_public_scope_args', array( $this, 'override_scope' ), 99 );
            add_filter( 'bp_activity_set_friends_scope_args', array( $this, 'override_scope' ), 99 );
            add_filter( 'bp_activity_set_groups_scope_args', array( $this, 'override_scope' ), 99 );
            add_filter( 'bp_activity_set_mentions_scope_args', array( $this, 'override_scope' ), 99 );
            add_filter( 'bp_activity_set_following_scope_args', array( $this, 'override_scope' ), 99 );
            add_filter( 'bp_after_has_activities_parse_args', array( $this, 'filter_activities' ), 99 );
            if ( bptk_is_buddyboss() ) {
                add_action( 'bp_member_members_list_item', array( $this, 'add_BB_list_block_button' ) );
            }
            if ( bp_is_active( 'messages' ) ) {
                //				add_filter( 'bp_messages_recipients', array( $this, 'check_recipients' ) );
                add_action( 'messages_message_before_save', array( $this, 'check_conversations' ) );
            }
            if ( bp_is_active( 'media' ) ) {
                add_filter( 'bp_before_has_media_parse_args', array( $this, 'filter_media' ), 999 );
            }
            
            if ( bp_is_active( 'friends' ) ) {
                add_filter(
                    'bp_is_friend',
                    array( $this, 'friend_check' ),
                    10,
                    2
                );
                add_action(
                    'bptk_user_blocked',
                    array( $this, 'remove_friendship' ),
                    10,
                    2
                );
            }
            
            
            if ( bp_is_active( 'document' ) ) {
                add_filter( 'bp_document_get_join_sql_document', array( $this, 'filter_documents' ), 100 );
                add_filter( 'bp_document_get_join_sql_folder', array( $this, 'filter_folders' ), 100 );
            }
        
        }
    
    }
    
    /**
     * Check user has the required level to block others.
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
     * Constructs link to pass when blocking.
     * @since 1.0
     */
    public function bptk_block_link( $list_id = 0, $user_id = 0 )
    {
        return apply_filters(
            'bptk_block_link',
            add_query_arg( array(
            'action'        => 'block',
            'blocking-user' => $list_id,
            'user-to-block' => $user_id,
            'token'         => wp_create_nonce( 'block-' . $list_id ),
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
            'action'        => 'unblock',
            'blocking-user' => $list_id,
            'user-to-block' => $user_id,
            'token'         => wp_create_nonce( 'unblock-' . $list_id ),
        ) ),
            $list_id,
            $user_id
        );
    }
    
    /**
     * Removes a user from a block list.
     * @since 1.0
     */
    public function remove_user( $list_id = null, $id_to_remove = null )
    {
        $current = $this->get_blocked_users( $list_id );
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
        $user_id = get_current_user_id();
        $member_id = bp_displayed_user_id();
        $options = get_option( 'block_section' );
        $placements = ( isset( $options['bptk_block_placement'] ) ? (array) $options['bptk_block_placement'] : [] );
        if ( !is_user_logged_in() || bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) || user_can( $member_id, 'suspend_users' ) || user_can( $user_id, BPTK_ADMIN_CAP ) ) {
            return;
        }
        if ( !$this->has_required_level( $user_id ) ) {
            return;
        }
        if ( in_array( 'profiles', $placements ) ) {
            return;
        }
        
        if ( class_exists( 'Youzify' ) ) {
            echo  '<li style="cursor: pointer;" class="bptk-block-profile youzify-name"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button"><i class="fa fa-ban" aria-hidden="true"></i>' . __( 'Block User', 'bp-toolkit' ) . '</a></li>' ;
        } else {
            
            if ( bp_get_theme_package_id() == 'nouveau' ) {
                echo  '<li class="generic-button bptk-block-profile"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></li>' ;
            } else {
                echo  '<div class="generic-button bptk-block-profile"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
            }
        
        }
    
    }
    
    /**
     * Add Block Button to Member lists.
     * @since 1.0
     */
    public function add_list_block_button()
    {
        $user_id = get_current_user_id();
        $member_id = bp_get_member_user_id();
        $options = get_option( 'block_section' );
        $placements = ( isset( $options['bptk_block_placement'] ) ? (array) $options['bptk_block_placement'] : [] );
        if ( in_array( 'directory', $placements ) ) {
            return;
        }
        
        if ( class_exists( 'Youzify' ) ) {
            if ( !is_user_logged_in() || user_can( $user_id, BPTK_ADMIN_CAP ) ) {
                return;
            }
            
            if ( user_can( $member_id, BPTK_ADMIN_CAP ) || user_can( $member_id, 'suspend_users' ) || bp_loggedin_user_id() == bp_get_member_user_id() ) {
                echo  '<div style="opacity: 50%; pointer-events: none;" class="generic-button bptk-block-list"><a href="">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
                return;
            } elseif ( !$this->has_required_level( $user_id ) ) {
                echo  '<div style="opacity: 50%; pointer-events: none;" class="generic-button bptk-block-list"><a href="">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
                return;
            } else {
                echo  '<div class="generic-button bptk-block-list"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
                return;
            }
        
        }
        
        if ( !is_user_logged_in() || user_can( $member_id, BPTK_ADMIN_CAP ) || user_can( $member_id, 'suspend_users' ) || user_can( $user_id, BPTK_ADMIN_CAP ) || bp_loggedin_user_id() == bp_get_member_user_id() ) {
            return;
        }
        if ( !$this->has_required_level( $user_id ) ) {
            return;
        }
        
        if ( bp_get_theme_package_id() == 'nouveau' ) {
            echo  '<li class="generic-button bptk-block-list"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></li>' ;
        } else {
            echo  '<div class="generic-button bptk-block-list"><a href="' . $this->bptk_block_link( $user_id, $member_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
        }
    
    }
    
    /**
     * Add Block Button to Member lists on BuddyBoss.
     */
    public function add_BB_list_block_button()
    {
        $blocker_id = get_current_user_id();
        $blocked_id = bp_get_member_user_id();
        $options = get_option( 'block_section' );
        $placements = ( isset( $options['bptk_block_placement'] ) ? (array) $options['bptk_block_placement'] : [] );
        if ( in_array( 'directory', $placements ) ) {
            return;
        }
        if ( !is_user_logged_in() || $blocked_id == $blocker_id || user_can( $blocker_id, 'manage_options' ) || user_can( $blocked_id, 'manage_options' ) ) {
            return;
        }
        echo  '<div class="generic-button bptk-block-list"><a data-balloon-pos="down" data-balloon="' . __( 'Block this member', 'bp-toolkit' ) . '" href="' . esc_url( $this->bptk_block_link( $blocker_id, $blocked_id ) ) . '">' . __( 'Block', 'bp-toolkit' ) . '</a></div>' ;
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
        if ( !isset( $_REQUEST['action'] ) || !isset( $_REQUEST['blocking-user'] ) || !isset( $_REQUEST['token'] ) || !isset( $_REQUEST['user-to-block'] ) ) {
            return;
        }
        switch ( $_REQUEST['action'] ) {
            case 'unblock':
                
                if ( wp_verify_nonce( $_REQUEST['token'], 'unblock-' . $_REQUEST['blocking-user'] ) ) {
                    $unblocking_user = intval( $_REQUEST['blocking-user'] );
                    $blocked_user = intval( $_REQUEST['user-to-block'] );
                    bptk_unblock_user( $blocked_user, $unblocking_user );
                }
                
                break;
            case 'block':
                
                if ( wp_verify_nonce( $_REQUEST['token'], 'block-' . $_REQUEST['blocking-user'] ) ) {
                    $blocking_user = intval( $_REQUEST['blocking-user'] );
                    $blocked_user = intval( $_REQUEST['user-to-block'] );
                    bptk_block_user( $blocked_user, $blocking_user );
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
        if ( !is_user_logged_in() || current_user_can( 'manage_options' ) || get_current_user_id() != bp_displayed_user_id() ) {
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
     * Sets up the 'blocked' list under our admin bar profile link.
     */
    public function setup_admin_bar()
    {
        global  $wp_admin_bar, $bp ;
        if ( !is_user_logged_in() || current_user_can( 'manage_options' ) ) {
            return;
        }
        $user_domain = bp_loggedin_user_domain();
        $item_link = trailingslashit( $user_domain . 'settings' );
        // add submenu item
        $wp_admin_bar->add_menu( array(
            'parent' => 'my-account-settings',
            'id'     => $bp->user_admin_menu_id . '-blocked-members',
            'title'  => __( 'Blocked Members', 'bp-toolkit' ),
            'href'   => trailingslashit( $item_link ) . 'bptk_blocked',
        ) );
    }
    
    /**
     * Sets up the 'blocked' list under our admin bar profile link.
     */
    public function setup_buddyboss_profile_menu()
    {
        if ( !is_user_logged_in() || current_user_can( 'manage_options' ) ) {
            return;
        }
        // Setup the logged in user variables.
        $blocked_members_link = trailingslashit( bp_loggedin_user_domain() . 'settings/bptk_blocked' );
        ?>
        <li id="wp-admin-bar-my-account-blocked-members" class="menupop parent">
            <a class="ab-item" aria-haspopup="true" href="<?php 
        echo  esc_url( $blocked_members_link ) ;
        ?>">
                <span class="wp-admin-bar-arrow" aria-hidden="true"></span><?php 
        _e( 'Blocked Members', 'bsrbb' );
        ?>
            </a>
        </li>
		<?php 
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
            $nav_title = __( 'Members this user blocks', 'bp-toolkit' );
        } else {
            $nav_title = __( 'Members you currently block', 'bp-toolkit' );
        }
        
        echo  $nav_title ;
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
		<?php 
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
                    $this->remove_user( $profile_id, $user_id );
                    continue;
                }
                
                ?>

                    <tr>
                        <td class="user"><?php 
                echo  $user->display_name . ' (@' . $user->user_login . ')' ;
                ?></td>
                        <td class="actions"><a href="<?php 
                echo  $this->bptk_unblock_link( $profile_id, $user->ID ) ;
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
        ?>
		<?php 
    }
    
    /**
     * Get a list of users a given user_id has blocked
     * @since 1.0
     */
    public function get_blocked_users( $user_id = null )
    {
        if ( $user_id === null ) {
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
     * Get a list of users who have blocked a member
     *
     * @param int $user_id The User's ID
     *
     * @return array
     * @since 3.0
     *
     */
    public function get_blocked_by_users( $user_id )
    {
        $list = get_user_meta( $user_id, 'bptk_blocked_by', true );
        return ( $list ? (array) $list : array() );
    }
    
    /**
     * Returns whether member is blocked.
     *
     * @param int $blocker The current user
     * @param int $blocked The user to test to see if blocked
     *
     * @since 2.0.2
     *
     */
    public function is_blocked( $blocker, $blocked )
    {
        $list = $this->get_blocked_users( $blocker );
        
        if ( in_array( $blocked, $list ) ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    /**
     * Adjusts the member query so blocked/blocking users aren't shown.
     * @since 1.0
     */
    public function adjust_query( $args )
    {
        if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
            return $args;
        }
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all activities
        if ( empty($my_list) ) {
            return $args;
        }
        $excluded = ( isset( $args['exclude'] ) ? $args['exclude'] : array() );
        if ( !is_array( $excluded ) ) {
            $excluded = explode( ',', $excluded );
        }
        $excluded = array_merge( $excluded, $my_list );
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
            $template = locate_template( 'buddypress/members/single/blocked.php', false );
            
            if ( empty($template) ) {
                load_template( BPTK_TEMPLATES . '/blocked.php' );
            } else {
                require_once $template;
            }
            
            exit;
        }
        
        // Step 2 - Check if the current profile is blocking the current user
        $_list = $this->get_blocked_users( bp_displayed_user_id() );
        
        if ( !empty($_list) && in_array( get_current_user_id(), $_list ) ) {
            $template = locate_template( 'buddypress/members/single/blocking.php', false );
            
            if ( empty($template) ) {
                load_template( BPTK_TEMPLATES . '/blocking.php' );
            } else {
                require_once $template;
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
    
    /**
     * Remove activities if blocked/blocking. Only relates to loops without scope, so this is not used in BuddyBoss.
     *
     * @param array $args Activities.
     *
     * @return array
     * @since 3.0
     *
     */
    public function filter_activities( $args )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all activities
        if ( empty($my_list) ) {
            return $args;
        }
        $filter_query = ( empty($args['filter_query']) ? array() : $args['filter_query'] );
        $filter_query[] = array(
            'column'  => 'user_id',
            'compare' => 'NOT IN',
            'value'   => $my_list,
        );
        $args['filter_query'] = $filter_query;
        return $args;
    }
    
    /**
     * Filter activities, taking into account BuddyBoss's scope precedence policy.
     *
     * @param $r array Scope arguments
     *
     * @return array
     */
    public function override_scope( $r )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all activities
        if ( empty($my_list) ) {
            return $r;
        }
        $filter_query = ( empty($r['filter_query']) ? array() : $r['filter_query'] );
        $filter_query[] = array(
            'column'  => 'user_id',
            'compare' => 'NOT IN',
            'value'   => $my_list,
        );
        $r['filter_query'] = $filter_query;
        return $r;
    }
    
    /**
     * Prepare a list of blocked/blocking, and pass to our comment looping function.
     *
     * @param array $results [activities, total].
     *
     * @return array
     * @since 3.0
     *
     */
    public function filter_comments( $results )
    {
        // Logged-in check
        if ( !is_user_logged_in() ) {
            return $results;
        }
        // Create a new list
        $my_list = array();
        // Get parent activities
        $activities = $results['activities'];
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all comments
        if ( empty($my_list) ) {
            return $results;
        }
        // Loop through each parent activity
        foreach ( $activities as $key => $activity ) {
            // If the activity doesn't have any comments, move on
            if ( empty($activity->children) ) {
                continue;
            }
            // If it does, call our looping function
            $activities[$key]->children = $this->filter_looped_comments( $activities[$key]->children, $my_list );
        }
        $results['activities'] = $activities;
        return $results;
    }
    
    /**
     * Loop through each comment in tree, and remove if blocked/blocking.
     *
     * @param array $comments comments.
     * @param array $my_list hidden user ids.
     *
     * @return array
     * @since 3.0
     *
     */
    private function filter_looped_comments( $comments, $my_list )
    {
        // If empty, return
        if ( empty($comments) ) {
            return $comments;
        }
        // If not empty, hide the comment if author is in our list, or see if it has a child
        foreach ( $comments as $key => $comment ) {
            
            if ( in_array( $comment->user_id, $my_list ) ) {
                unset( $comments[$key] );
                continue;
            }
            
            // Next, if the comment has another comment below it, restart the magic
            if ( !empty($comments[$key]->children) ) {
                $comments[$key]->children = $this->filter_looped_comments( $comments[$key]->children, $my_list );
            }
        }
        return $comments;
    }
    
    /**
     * Filter any mentions if blocked/blocking.
     *
     * @param array $names usernames.
     *
     * @return array
     * @since 3.0
     *
     */
    public function filter_mentions( $names )
    {
        // Hide mentions from members you block
        $blocked_users = $this->get_blocked_users( bp_loggedin_user_id() );
        foreach ( $blocked_users as $blocked_user ) {
            unset( $names[$blocked_user] );
        }
        // Hide mentions from members blocking you
        $blocking_users = $this->get_blocked_by_users( bp_loggedin_user_id() );
        foreach ( $blocking_users as $blocking_user ) {
            unset( $names[$blocking_user] );
        }
        return $names;
    }
    
    /**
     * Remove blocked/blocking from the member suggestions list.
     *
     * @param array $user_query User query.
     *
     * @return array
     * @since 3.0
     *
     */
    public function disable_suggestions_list( $user_query )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all suggestions
        if ( empty($my_list) ) {
            return $user_query;
        }
        // Else, exclude any suggestions featuring those in our list
        if ( $my_list ) {
            $user_query['exclude'] = array_unique( $my_list );
        }
        return $user_query;
    }
    
    /**
     * Prevent reply button from displaying on activity comments if blocked or blocking.
     *
     * @since 2.0.0
     *
     * @deprecated 3.0 No longer required as comments are now completely hidden.
     * @see filter_comments()
     */
    public function block_comment_replies( $can_comment, $comment )
    {
        $my_list = $this->get_blocked_users( get_current_user_id() );
        $their_list = $this->get_blocked_users( $comment->user_id );
        
        if ( in_array( $comment->user_id, $my_list ) ) {
            return false;
        } elseif ( in_array( get_current_user_id(), $their_list ) ) {
            return false;
        } else {
            return $can_comment;
        }
    
    }
    
    /**
     * Remove the Add Friend button if the user is blocking.
     *
     * @since 1.0.4
     */
    public function friend_check( $status, $user_id )
    {
        $list = $this->get_blocked_users( $user_id );
        if ( in_array( bp_loggedin_user_id(), (array) $list ) ) {
            return false;
        }
        return $status;
    }
    
    /**
     * Handle pending or current connections when a block takes place.
     *
     * @param $blocker_id
     * @param $blocked_id
     */
    public function remove_friendship( $blocker_id, $blocked_id )
    {
        $options = get_option( 'block_section' );
        $arg = ( isset( $options['bptk_block_cancel_friendship'] ) ? $options['bptk_block_cancel_friendship'] : false );
        /**
         * Filter whether the plugin should cancel friendships if one side blocks. Default: false.
         */
        $cancel_friendship = apply_filters( 'bsr_cancel_friendship_on_block', $arg );
        // Get the friendship
        $friendship_id = BP_Friends_Friendship::get_friendship_id( $blocked_id, $blocker_id );
        $friendship = new BP_Friends_Friendship( $friendship_id, false, false );
        //  Check friendship status, and take action based on result
        switch ( friends_check_friendship_status( $blocker_id, $blocked_id ) ) {
            case 'is_friend':
                if ( $cancel_friendship == true ) {
                    $result = friends_remove_friend( $blocked_id, $blocker_id );
                }
                break;
            case 'pending':
            case 'awaiting_response':
                error_log( BP_Friends_Friendship::reject( $friendship_id ) );
                error_log( BP_Friends_Friendship::withdraw( $friendship_id ) );
                break;
            case 'not_friends':
                break;
        }
    }
    
    /**
     * Remove documents if blocked/blocking.
     *
     * @param string $join_sql_document Documents.
     *
     * @return string
     * @since 3.1.6
     *
     */
    public function filter_documents( $join_sql_document )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        
        if ( !empty($my_list) ) {
            $block_prepped = implode( ',', wp_parse_id_list( $my_list ) );
            $join_sql_document .= "AND d.user_id NOT IN ({$block_prepped})";
        }
        
        return $join_sql_document;
    }
    
    /**
     * Remove folders if blocked/blocking.
     *
     * @param string $join_sql_folder Folders.
     *
     * @return string
     * @since 3.1.6
     *
     */
    public function filter_folders( $join_sql_folder )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        
        if ( !empty($my_list) ) {
            $block_prepped = implode( ',', wp_parse_id_list( $my_list ) );
            $join_sql_folder .= "AND f.user_id NOT IN ({$block_prepped})";
        }
        
        return $join_sql_folder;
    }
    
    /**
     * Remove media if blocked/blocking.
     *
     * @param array $args Media.
     *
     * @return array
     * @since 3.0
     *
     */
    public function filter_media( $args )
    {
        // Create a new list
        $my_list = array();
        // First, add everyone we are blocking to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_users( bp_loggedin_user_id() ) );
        // Next, add everyone blocking us to $my_list
        $my_list = array_merge( $my_list, $this->get_blocked_by_users( bp_loggedin_user_id() ) );
        // If neither side is blocking, enable all activities
        if ( empty($my_list) ) {
            return $args;
        }
        // Create an empty array to hold excluded media IDs
        $exclude_list = array();
        // Loop through each blocked/blocking member
        foreach ( $my_list as $key => $value ) {
            // Grab all media from each member
            $medias = bp_media_get( array(
                'user_id' => $value,
                'fields'  => 'ids',
            ) );
            // Loop through each of these media
            foreach ( $medias as $media => $value ) {
                if ( empty($value) ) {
                    break;
                }
                // Get the media ID and if unique, add to the array
                foreach ( $value as $media_id ) {
                    if ( !in_array( $media_id, $exclude_list ) ) {
                        $exclude_list[] = $media_id;
                    }
                }
            }
        }
        $args['exclude'] = $exclude_list;
        return $args;
    }

}