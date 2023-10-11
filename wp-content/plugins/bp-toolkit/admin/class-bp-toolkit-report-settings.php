<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/class-bp-toolkit-wp-osa.php';
/**
 * The class that looks after the Toolkit's Report settings page.
 *
 * @since      3.0.0
 *
 */
class BPTK_Report_Settings
{
    /**
     * An array of IDs from which new settings will be created.
     *
     * @since    3.0.0
     * @access   protected
     * @var      array $advanced_settings An array of IDs.
     */
    public  $osa ;
    /**
     * An array of IDs from which new settings will be created.
     *
     * @since    3.0.0
     * @access   protected
     * @var      array $advanced_settings An array of IDs.
     */
    protected  $advanced_settings = array( '' ) ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    3.0.0
     *
     */
    public function __construct()
    {
        $this->osa = new WP_OSA();
        $this->create_sections();
        $this->create_fields();
    }
    
    /**
     * Create each of our settings sections.
     *
     * @since    3.0.0
     *
     */
    public function create_sections()
    {
        $this->osa->add_section( array(
            'id'    => 'report_section',
            'title' => __( '', 'bp-toolkit' ),
            'name'  => __( 'Report Settings', 'bp-toolkit' ),
            'tab'   => __( 'General', 'bp-toolkit' ),
        ) );
        $this->osa->add_section( array(
            'id'    => 'report_user_section',
            'title' => __( '', 'bp-toolkit' ),
            'name'  => __( 'Report Settings', 'bp-toolkit' ),
            'tab'   => __( 'User', 'bp-toolkit' ),
        ) );
        $this->osa->add_section( array(
            'id'    => 'report_emails_section',
            'title' => __( '', 'bp-toolkit' ),
            'name'  => __( 'Report Settings', 'bp-toolkit' ),
            'tab'   => __( 'Emails', 'bp-toolkit' ),
        ) );
    }
    
    /**
     * Create each of our settings section fields.
     *
     * @since    3.0.0
     *
     */
    public function create_fields()
    {
        if ( bptk_fs()->is_not_paying() ) {
            $this->osa->add_field( 'report_section', array(
                'type' => 'html',
                'desc' => '<a class="bptk-promo-message" href="' . bptk_fs()->get_upgrade_url() . '">' . __( 'Upgrade Now - to enable premium features such as reporting of activities, auto-moderation, temporary restrictions and whitelisting.', 'bp-toolkit' ) . '</a>',
            ) );
        }
        $this->osa->add_field( 'report_section', array(
            'id'      => 'bptk_report_activity_types',
            'type'    => 'multicheck',
            'name'    => __( 'Select Reportable Content', 'bp-toolkit' ),
            'desc'    => __( 'The type of content that can been reported.', 'bp-toolkit' ),
            'options' => $this->get_activities(),
        ) );
        $this->osa->add_field( 'report_section', array(
            'id'   => 'bptk_report_once_only',
            'type' => 'checkbox',
            'name' => __( 'Turn Off Unique Reporting', 'bp-toolkit' ),
            'desc' => __( 'Users can only report the same item once. Check to switch this behaviour off. We recommend you leave unique reporting on, to prevent the same user triggering auto-moderation.', 'bp-toolkit' ),
        ) );
        $this->osa->add_field( 'report_user_section', array(
            'id'   => 'bptk_report_user_toggle_automod',
            'type' => 'checkbox',
            'name' => __( 'Auto-Suspend Users?', 'bp-toolkit' ),
            'desc' => __( 'Check this box to auto-suspend users.', 'bp-toolkit' ),
        ) );
        $this->osa->add_field( 'report_user_section', array(
            'id'      => 'bptk_report_user_automod_threshold',
            'type'    => 'number',
            'name'    => __( 'Auto-Suspension Threshold', 'bp-toolkit' ),
            'desc'    => __( 'Set the threshold for number of reports before auto-suspension is activated.', 'bp-toolkit' ),
            'default' => '5',
        ) );
        $name1 = __( 'Notify Admin on Auto-Suspension', 'bp-toolkit' );
        $desc1 = __( 'Check this box to send an email to the administrator when a user is auto-suspended.', 'bp-toolkit' );
        $name2 = __( 'Notify User on Suspension', 'bp-toolkit' );
        $desc2 = __( 'Check this box to send an email to the user when they are suspended via automatic or manual moderation.', 'bp-toolkit' );
        $name3 = __( 'Notify User on Unsuspension', 'bp-toolkit' );
        $desc3 = __( 'Check this box to send an email to the user when they are unsuspended.', 'bp-toolkit' );
        $name4 = __( 'Notify Reporter on Suspension', 'bp-toolkit' );
        $desc4 = __( 'Sends an email to the person who reported the user that they have been suspended.', 'bp-toolkit' );
        $name5 = __( 'Notify Reporter on Unsuspension', 'bp-toolkit' );
        $desc5 = __( 'Sends an email to the person who reported the user that they have been unsuspended.', 'bp-toolkit' );
        $email_intro_string = sprintf(
            /* translators: 1: GLOBAL link to plugin support page */
            __( 'Use this page to enable a range of different emails that can be sent, based on different events. To find out more about these emails, see our <a href="%1$s">documentation</a>.', 'bp-toolkit' ),
            BP_TOOLKIT_SUPPORT
        );
        $this->osa->add_field( 'report_emails_section', array(
            'type' => 'html',
            'desc' => $email_intro_string,
        ) );
        $email_multiple_string = __( 'Enter an email address to send administrator emails to, or leave blank to send to the default site administrator. This is useful where a particular administrator or other site user handles your reports and moderation. <span style="color: red;">Upgrade to send to multiple addresses.</span>', 'bp-toolkit' );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_admin_list',
            'type' => 'text',
            'name' => __( 'Admin email Addresses', 'bp-toolkit' ),
            'desc' => $email_multiple_string,
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_new_report',
            'type' => 'checkbox',
            'name' => __( 'Notify Admin on New Reports', 'bp-toolkit' ),
            'desc' => __( 'Check this box to send an email to the administrator when a new report is received.', 'bp-toolkit' ),
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_automod_admin',
            'type' => 'checkbox',
            'name' => $name1,
            'desc' => $desc1,
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_automod_user',
            'type' => 'checkbox',
            'name' => $name2,
            'desc' => $desc2,
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_restored_user',
            'type' => 'checkbox',
            'name' => $name3,
            'desc' => $desc3,
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_automod_reporter',
            'type' => 'checkbox',
            'name' => $name4,
            'desc' => $desc4,
        ) );
        $this->osa->add_field( 'report_emails_section', array(
            'id'   => 'bptk_report_emails_restored_reporter',
            'type' => 'checkbox',
            'name' => $name5,
            'desc' => $desc5,
        ) );
    }
    
    /**
     * Create an array of activity types.
     *
     * @since    3.0.0
     *
     */
    public function get_activities()
    {
        $activities = array(
            'members' => __( 'Members', 'bp-toolkit' ),
        );
        return $activities;
    }
    
    /**
     * Create an array of roles.
     *
     * @since    3.0.0
     *
     */
    public function get_roles()
    {
        // require_once ABSPATH . 'wp-admin/includes/user.php';
        $roles_obj = new WP_Roles();
        $roles_names_array = $roles_obj->get_names();
        // Unset administrators as they cannot report or be reported anyway
        $key = array_search( 'Administrator', $roles_names_array );
        unset( $roles_names_array[$key] );
        return $roles_names_array;
    }
    
    /**
     * Create an array of administrators and site moderators.
     *
     * @since    3.1.0
     *
     */
    public function get_mods()
    {
        $users = array();
        $args = array(
            'role__in' => array( 'Administrator', 'bptk_moderator' ),
        );
        $users_raw = get_users( $args );
        foreach ( $users_raw as $user ) {
            $users[$user->ID] = $user->display_name;
        }
        return $users;
    }
    
    /**
     * Generate HTML content.
     *
     * @since    3.0.0
     *
     */
    public function create_page()
    {
        echo  '<div class="bptk-box"><h3 class="bptk-box-header">' . __( 'Report Settings', 'bp-toolkit' ) . '</h3>' ;
        $this->osa->show_navigation();
        $this->osa->show_forms();
        echo  '</div>' ;
        
        if ( bptk_fs()->is_not_paying() ) {
            ?>

			<div class="bptk-box preapproval-teaser">
                <div class="preapproval-teaser-heading">
                    <h2>New in <a href="<?php 
            echo  bptk_fs()->get_upgrade_url() ;
            ?>">Pro Version 3.5.0</a></h2>
                    <p>Require approval for all new activities or group posts. If enabled, all new activities will be held in moderation and only admin's or mod's approval will make them visible.</p>
                </div>
				<img src="<?php 
            echo  plugin_dir_url( __DIR__ ) . 'admin/assets/images/preapproval.png' ;
            ?>">
			</div>

		<?php 
        }
    
    }

}