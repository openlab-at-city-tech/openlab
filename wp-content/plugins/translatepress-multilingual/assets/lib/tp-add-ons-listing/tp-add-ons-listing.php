<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TRP_Addons_List_Table extends WP_List_Table {
    public $header;
    public $section_header;
    public $section_versions;
    public $sections;

    public $images_folder;
    public $text_domain;

    public $all_addons;
    public $all_plugins;

    public $current_version;

    public $tooltip_header;
    public $tooltip_content;

    function __construct(){

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'add-on',     //singular name of the listed records
            'plural' => 'add-ons',    //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));

        //this is necessary because of WP_List_Table
        $this->prepare_items();

        //enqueue scripts and styles
        add_action('admin_footer', array($this, 'trp_print_assets'));

    }

    /**
     * Print js and css
     * @param $hook
     */
    function trp_print_assets(){
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('wp-pointer');
        wp_localize_script( 'wp-pointer', 'trp_add_ons_pointer', array( 'tooltip_header' => $this->tooltip_header, 'tooltip_content' => $this->tooltip_content ) );

        wp_enqueue_style('trp-add-ons-listing-css', plugin_dir_url(__FILE__) . '/assets/css/tp-add-ons-listing.css', false);
        wp_enqueue_script('trp-add-ons-listing-js', plugin_dir_url(__FILE__) . '/assets/js/tp-add-ons-listing.js', array('jquery'));
    }

    /**
     * Define the columns here and their headers
     * @return array
     */
    function get_columns(){
        $columns = array(
            'icon'     	=> '',
            'add_on'    => __('Add-On', 'translatepress-multilingual' ), //phpcs:ignore
            'actions'     => '',
        );
        return $columns;
    }



    /**
     * The icon column
     * @param $item
     * @return string
     */
    function column_icon($item){
        return '<img src="'.$this->images_folder. $item['icon'] .'" width="64" height="64" alt="'. $item['name'] .'">';
    }

    /**
     * The column where we display the addon name and description
     * @param $item
     * @return string
     */
    function column_add_on($item){
        return '<strong class="trp-add-ons-name trp-accent-text-bold">'.
                    $item['name'] .
                '</strong><br/>' .

                '<span class="trp-primary-text trp-addon-description">' .
                    $item['description'] .
                '</span>';
    }

    /**
     * The actions column for the addons
     * @param $item
     * @return string
     */
    function column_actions($item){

        $action = '';
        //for plugins we can do something general
        if ( $item['type'] === 'plugin' ) {
            if ( isset( $item['action'] ) && $item['action'] === 'deactivate' ) {
                // Plugin is active, show deactivate button
                $deactivate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg([
                            'trp_add_ons_action' => 'deactivate',
                            'trp_plugins'        => $item['slug'],
                            'page'               => sanitize_text_field( $_REQUEST['page'] ?? '' )
                        ], admin_url('admin.php')),
                        'trp_add_ons_action'
                    )
                );

                $action = '<a class="right button trp-button-secondary" href="' . $deactivate_url . '">' . esc_html__('Deactivate', 'translatepress-multilingual') . '</a>';
            }

            elseif ( isset( $item['action'] ) && $item['action'] === 'activate') {
                // Plugin is installed but not active, show activate button
                $activate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg([
                            'trp_add_ons_action' => 'activate',
                            'trp_plugins'        => $item['slug'],
                            'page'               => sanitize_text_field($_REQUEST['page'] ?? '')
                        ], admin_url('admin.php')),
                        'trp_add_ons_action'
                    )
                );

                $action = '<a class="right button trp-submit-btn" href="' . $activate_url . '">' . esc_html__('Activate', 'translatepress-multilingual') . '</a>';
            }

            else {
                // Plugin is not installed or not active, show install and activate button
                $action = '<a class="trp-submit-btn button-primary right trp-recommended-plugin-buttons trp-install-and-activate" 
                        data-trp-plugin-slug="' . esc_attr($item['short-slug']) . '" 
                        data-trp-action-performed="' . esc_html__('Installing...', 'translatepress-multilingual') . '" 
                        ' . esc_html($item['disabled']) . '>'
                    . wp_kses_post($item['install_button']) .
                    '</a>';
            }
        }

        elseif ( $item['type'] === 'add-on' ){//this is more complicated as there are multiple cases, I think it should be done through filters in each plugin
            //get license status
            $license_status = get_option( 'trp_license_status' );
            //set some default values
            in_array( $this->current_version, $this->section_versions ) ? $disabled = '' : $disabled = 'disabled'; //add disabled if the current version isn't eligible
            $onclick = '';

            //construct the action URL based on the add-on status
            $base_url = admin_url( 'admin.php?page='. sanitize_text_field( $_REQUEST['page'] ) );
            if ( $this->is_add_on_active( $item['slug'] ) )
                $base_url .= '&trp_add_ons_action=deactivate';
            else
                $base_url .= '&trp_add_ons_action=activate';
            $action_url = esc_url( wp_nonce_url( add_query_arg( 'trp_add_ons', $item['slug'], $base_url ), 'trp_add_ons_action' ) );

            //if we don't have a valid license we can't activate the add-on and throw a confirmation dialog
            if ( $this->is_add_on_active( $item['slug'] ) ) {
                if ( $license_status !== 'valid' ) {
                    $onclick = 'onclick="return confirm(\'' . esc_js( __( 'This add-on can not be activated back without a valid license. Are you sure you want to deactivate it?', 'translatepress-multilingual' ) ) . '\')"';
                }
                $action = '<a class="right button trp-button-secondary" data-slug="' . $item['slug'] . '" href="'. $action_url .'" '. $onclick .'>' . __('Deactivate', 'translatepress-multilingual') . '</a>';//phpcs:ignore
            } else{
                if ( $license_status !== 'valid' ) {
                    $disabled = 'disabled'; //if the license is not valid, disable the button
                    $action_url = '';
                }
                $action = '<a class="right button trp-submit-btn" '.$disabled.' href="'. $action_url .'">' . __('Activate', 'translatepress-multilingual') . '</a>';//phpcs:ignore
            }
        }


        $documentation = '<a target="_blank" class="right trp-docs-btn" href="'. trp_add_affiliate_id_to_link( $item['doc_url'] ) . '">' . __( 'Documentation', 'translatepress-multilingual' ) . '</a>';//phpcs:ignore

        return $action . $documentation;
    }


    //don't generate a bulk actions dropdown by returning an empty array
    function get_bulk_actions() {
        return array( );//don't show bulk actions
    }



    /**
     * Function that initializez the object properties
     */
    function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns, array(), array());//the two empty arrays are hidden and sortable
        $this->set_pagination_args( array( ) ); //we do not need pagination
    }


    /** Here start the customizations for multiple tables and our custom html **/


    /**

    /**
     * This is the function that adds more sections (tables) to the listing
     */
    function add_section(){
        ob_start();

        $license_status = get_option( 'trp_license_status' );
        ?>
        <div class="trp-add-ons-section trp-settings-container">
            <?php if( !empty( $this->section_header ) ): ?>

                <h2 class="trp-settings-primary-heading"><?php echo esc_html( $this->section_header['title'] );?></h2>
                <?php if( !empty( $this->section_header ) ): ?>
                    <p class="trp-primary-text"><?php echo wp_kses_post( $this->section_header['description'] ); ?></p>
                <?php endif; ?>


            <?php endif; ?>

            <?php
                foreach( $this->items as $item ) {
                    if( $item['type'] === 'add-on' )
                        $this->all_addons[] = $item['slug'];
                    elseif( $item['type'] === 'plugin' )
                        $this->all_plugins[] = $item['slug'];
                }

                $activate_args = [
                    'trp_add_ons_action' => 'activate_all',
                    'page' => sanitize_text_field($_REQUEST['page'] ?? '')
                ];

                if (!empty($this->all_addons)) {
                    $activate_args['trp_add_ons'] = implode('|', $this->all_addons);
                }

                if (!empty($this->all_plugins)) {
                    $activate_args['trp_plugins'] = implode('|', $this->all_plugins);
                }

                $activate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg($activate_args, admin_url('admin.php')),
                        'trp_add_ons_action'
                    )
                );

                $deactivate_args = [
                    'trp_add_ons_action' => 'deactivate_all',
                    'page' => sanitize_text_field($_REQUEST['page'] ?? '')
                ];

                if (!empty($this->all_addons)) {
                    $deactivate_args['trp_add_ons'] = implode('|', $this->all_addons);
                }

                if (!empty($this->all_plugins)) {
                    $deactivate_args['trp_plugins'] = implode('|', $this->all_plugins);
                }

                $deactivate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg($deactivate_args, admin_url('admin.php')),
                        'trp_add_ons_action'
                    )
                );
            ?>
            <div class="trp-bulk-actions__wrapper">
                <?php if( $license_status !== 'valid' ): ?>
                    <span class="trp-activate-all" title="<?php esc_attr_e('A valid license is required to activate add-ons.', 'translatepress-multilingual') ?>"><?php esc_html_e('Activate all', 'translatepress-multilingual'); ?></span>
                <?php else: ?>
                    <a href="<?php echo $activate_url; // phpcs:ignore ?>" class="trp-activate-all"><?php esc_html_e('Activate all', 'translatepress-multilingual'); ?></a>
                <?php endif; ?>
                <span>/</span>
                <?php if( $license_status !== 'valid' ): ?>
                    <a href="<?php echo $deactivate_url; // phpcs:ignore ?>" class="trp-deactivate-all" onclick="return confirm('<?php echo esc_js( __( 'Add-ons can not be activated back without a valid license. Are you sure you want to deactivate them?', 'translatepress-multilingual' ) ) ?> ')"><?php esc_html_e('Deactivate all', 'translatepress-multilingual'); ?></a>
                <?php else: ?>
                    <a href="<?php echo $deactivate_url; // phpcs:ignore ?>" class="trp-deactivate-all"><?php esc_html_e('Deactivate all', 'translatepress-multilingual'); ?></a>
                <?php endif; ?>
            </div>

            <?php
                $this->display(); /* this is the function from the table listing class */
            ?>

        </div>
        <?php

        $output = ob_get_contents();

        ob_end_clean();

        $this->sections[] = $output;
    }


    /**
     * The function that actually displays all the tables and the surrounding HTML
     */
    function display_addons(){
        ?>
        <div class="wrap" id="trp-settings__wrap">
            <form id="trp-addons" method="post">
                <?php

                if( !empty( $this->sections ) ){
                    foreach ( $this->sections as $section ){
                        echo $section;/* phpcs:ignore */ /* escaped inside the variable */
                    }
                }
                ?>

                <?php wp_nonce_field('trp_add_ons_action'); ?>
            </form>
        </div>

        <?php

    }

    static function is_add_on_active( $slug ){
        return apply_filters( 'trp_add_on_is_active', false, $slug );
    }
}

/**
 * process the actions for the Add-ons page
 */
add_action( 'admin_init', 'trp_add_ons_listing_process_actions', 1 );

/**
 * Return the plugin and add-on slugs that can be managed from the Add-ons page.
 *
 * @return array
 */
function trp_add_ons_listing_get_allowed_items() {
    return apply_filters(
        'trp_add_ons_listing_allowed_items',
        array(
            'plugins' => array(
                'profile-builder/index.php',
                'paid-member-subscriptions/index.php',
                'wp-webhooks/wp-webhooks.php',
            ),
            'add_ons' => array(
                'tp-add-on-seo-pack/tp-seo-pack.php',
                'tp-add-on-extra-languages/tp-extra-languages.php',
                'tp-add-on-deepl/index.php',
                'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
                'tp-add-on-translator-accounts/index.php',
                'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
                'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
                'tp-add-on-multiple-domains/tp-multiple-domains.php',
            ),
        )
    );
}

/**
 * Read a pipe-delimited list from the request and discard slugs not managed by TranslatePress.
 *
 * @param string $request_key  Request parameter name.
 * @param array  $allowed_items Allowed slugs.
 * @return array
 */
function trp_add_ons_listing_get_requested_items( $request_key, $allowed_items ) {
    if ( empty( $_GET[ $request_key ] ) || ! is_string( $_GET[ $request_key ] ) ) {
        return array();
    }

    $requested_items = explode( '|', sanitize_text_field( wp_unslash( $_GET[ $request_key ] ) ) );

    return array_values( array_intersect( $requested_items, $allowed_items ) );
}

function trp_add_ons_listing_process_actions(){
    if ( ! current_user_can( 'manage_options' ) || empty( $_GET['trp_add_ons_action'] ) || empty( $_GET['_wpnonce'] ) ) {
        return;
    }

    $action = sanitize_key( wp_unslash( $_GET['trp_add_ons_action'] ) );
    $page   = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
    $nonce  = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

    if (
        'trp_addons_page' !== $page ||
        ! in_array( $action, array( 'activate', 'activate_all', 'deactivate', 'deactivate_all' ), true ) ||
        ! wp_verify_nonce( $nonce, 'trp_add_ons_action' )
    ) {
        return;
    }

    $allowed_items = trp_add_ons_listing_get_allowed_items();
    $plugins       = current_user_can( 'activate_plugins' ) && ! empty( $allowed_items['plugins'] )
        ? trp_add_ons_listing_get_requested_items( 'trp_plugins', $allowed_items['plugins'] )
        : array();
    $add_ons       = ! empty( $allowed_items['add_ons'] )
        ? trp_add_ons_listing_get_requested_items( 'trp_add_ons', $allowed_items['add_ons'] )
        : array();

    if ( in_array( $action, array( 'activate', 'activate_all' ), true ) ) {
        foreach ( $plugins as $plugin ) {
            if ( ! is_plugin_active( $plugin ) && ( ! is_multisite() || ! is_network_only_plugin( $plugin ) ) ) {
                activate_plugin( $plugin, '', false );
            }
        }

        foreach ( $add_ons as $add_on ) {
            do_action( 'trp_add_ons_activate', $add_on );
        }
    } else {
        foreach ( $plugins as $plugin ) {
            if ( is_plugin_active( $plugin ) ) {
                // Explicitly limit deactivation to the current site on multisite.
                deactivate_plugins( $plugin, false, false );
            }
        }

        foreach ( $add_ons as $add_on ) {
            do_action( 'trp_add_ons_deactivate', $add_on );
        }
    }

    wp_safe_redirect( add_query_arg( 'trp_add_ons_listing_success', 'true', admin_url( 'admin.php?page=' . $page ) ) );
    exit;
}

/**
 * Add a notice on the add-ons page if the save was successful
 */
if ( isset($_GET['trp_add_ons_listing_success']) ){
    if( class_exists('TRP_Add_General_Notices') ) {
        new TRP_Add_General_Notices('trp_add_ons_listing_success',
            sprintf(__('%1$sAdd-ons settings saved successfully%2$s', 'translatepress-multilingual'), "<p>", "</p>"),
            'updated notice is-dismissible');
    }
}
