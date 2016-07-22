<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://early-adopter.com/
 * @since      1.0.0
 *
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/admin
 * @author     Joe Unander <joe@early-adopter.com>
 */
class Bp_Customizable_Group_Categories_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bp_Customizable_Group_Categories_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bp_Customizable_Group_Categories_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/bp-customizable-group-categories-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bp_Customizable_Group_Categories_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bp_Customizable_Group_Categories_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
    }

    /**
     * Add a submenu to Group administration.
     *
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     */
    public function bp_groups_admin_submenu() {

        $this->admin_screen = add_menu_page(_x('Group Categories', 'admin page title', 'bp-custocg'), _x('Group Categories', 'admin menu title', 'bp-custocg'), 'bp_moderate', 'bp-group-categories', array($this, 'admin_tags'), 'dashicons-yes', 42);

        add_action("load-{$this->admin_screen}", array($this, 'admin_tags_load'));
    }

    /**
     * Make sure the BP Group Categories Screen includes the post type and taxonomy properties
     *
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     *
     * @param WP_Screen $current_screen
     */
    function set_current_screen($current_screen = OBJECT) {
        global $page_hook;

        if (empty($this->admin_screen) || false === strpos($this->admin_screen, $current_screen->id)) {
            return;
        }

        if ($current_screen->id !== $this->admin_screen && is_null($page_hook)) {
            $current_screen->id   = $this->admin_screen;
            $current_screen->base = $this->admin_screen;
            $page_hook            = $this->admin_screen;
        }

        $current_screen->post_type = 'bp_group';
        $current_screen->taxonomy  = 'bp_group_categories';
    }

    /**
     * Get the admin current ation
     *
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     */
    function current_action() {
        $action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

        // If the bottom is set, let it override the action
        if (!empty($_REQUEST['action2']) && $_REQUEST['action2'] != "-1") {
            $action = $_REQUEST['action2'];
        }

        return $action;
    }

    /**
     * Set up the Group Categories admin page.
     *
     *
     * @access public
     * @since BP Customizable Group Categories(1.0.0)
     *
     * @global $wp_post_types
     */
    public function admin_tags_load() {
        global $wp_http_referer;

        $cheating = __('Cheatin&#8217; uh?', 'bp-custocg');

        if (!bp_current_user_can('bp_moderate')) {
            wp_die($cheating);
        }

        $post_type = 'bp_group';
        $taxnow    = $taxonomy  = 'bp_group_categories';

        $redirect_to = add_query_arg('page', 'bp-group-categories', bp_get_admin_url('admin.php'));
        // Filter the updated messages
        add_filter('term_updated_messages', array($this, 'admin_updated_message'), 10, 1);

        $doaction = $this->current_action();

        /**
         * Eventually deal with actions before including the edit-tags.php file
         */
        if (!empty($doaction)) {
            $bp_group_categories_tax = get_taxonomy($taxonomy);

            if (!$bp_group_categories_tax) {
                wp_die(__('Invalid taxonomy', 'bp-custocg'));
            }

            switch ($doaction) {
                case 'add-tag':

                    check_admin_referer('add-tag', '_wpnonce_add-tag');

                    if (!bp_current_user_can($bp_group_categories_tax->cap->edit_terms)) {
                        wp_die($cheating);
                    }

                    $inserted = BP_Groups_Terms::insert_term($_POST['tag-name'], $bp_group_categories_tax->name, $_POST);

                    if (!empty($inserted) && !is_wp_error($inserted)) {
                        $redirect_to = add_query_arg('message', 1, $redirect_to);
                    } else {
                        $redirect_to = add_query_arg('message', 4, $redirect_to);
                    }
                    wp_redirect($redirect_to);
                    exit;

                case 'delete':
                case 'bulk-delete':
                    $tag_IDs    = array();
                    $query_args = array();

                    if (empty($_REQUEST['tag_ID']) && empty($_REQUEST['delete_tags'])) {
                        wp_redirect($redirect_to);
                        exit;
                    } else if (!empty($_REQUEST['tag_ID'])) {
                        $tag_ID                = absint($_REQUEST['tag_ID']);
                        check_admin_referer('delete-tag_' . $tag_ID);
                        $tag_IDs               = array($tag_ID);
                        $query_args['message'] = 2;
                    } else {
                        check_admin_referer('bulk-tags');
                        $tag_IDs               = wp_parse_id_list($_REQUEST['delete_tags']);
                        $query_args['message'] = 6;
                    }

                    if (!bp_current_user_can($bp_group_categories_tax->cap->delete_terms)) {
                        wp_die($cheating);
                    }

                    foreach ($tag_IDs as $tag_ID) {
                        BP_Groups_Terms::delete_term($tag_ID, $bp_group_categories_tax->name);
                    }

                    $redirect_to = add_query_arg($query_args, $redirect_to);
                    wp_redirect($redirect_to);
                    exit;

                case 'edit':
                    // We need to reset the action of the edit form
                    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/bp-customizable-group-categories-admin.js', array('jquery'), $this->version, false);
                    wp_localize_script($this->plugin_name, 'BP_CustoCG_Admin', array(
                        'edit_action' => $redirect_to,
                    ));

                    require_once( ABSPATH . 'wp-admin/term.php' );
                    exit;

                case 'editedtag':
                    $tag_ID = (int) $_POST['tag_ID'];
                    check_admin_referer('update-tag_' . $tag_ID);

                    if (!bp_current_user_can($bp_group_categories_tax->cap->edit_terms))
                        wp_die($cheating);

                    $tag = BP_Groups_Terms::get_term($tag_ID, $bp_group_categories_tax->name);
                    if (!$tag) {
                        wp_die(__('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?', 'bp-custocg'));
                    }

                    $ret = BP_Groups_Terms::update_term($tag_ID, $bp_group_categories_tax->name, $_POST);

                    if (!empty($ret) && !is_wp_error($ret)) {
                        $redirect_to = add_query_arg('message', 3, $redirect_to);
                    } else {
                        $redirect_to = add_query_arg('message', 5, $redirect_to);
                    }

                    wp_redirect($redirect_to);
                    exit;
            }

            /**
             * Make sure to "javascript change" some form attributes
             * in edit-tags.php
             */
        } else {

            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/bp-customizable-group-categories-admin.js', array('jquery'), $this->version, false);
            wp_localize_script($this->plugin_name, 'BP_CustoCG_Admin', array(
                'edit_action' => $redirect_to,
                'ajax_screen' => 'edit-' . $taxonomy,
                'search_page' => 'bp-group-categories',
            ));
        }

        require_once( ABSPATH . 'wp-admin/edit-tags.php' );
        exit();
    }

    /**
     * Not used, everything is done in BP_Groups_Tag_Admin->admin_tags_load()
     *
     * @access public
     * @since BP Customizable Group Categories(1.0.0)
     */
    public function admin_tags() {
        
    }

}
