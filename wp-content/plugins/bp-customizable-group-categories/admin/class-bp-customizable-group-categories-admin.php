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
        $this->version = $version;
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
    public function bp_groups_admin_menu() {
        $this->admin_menu = add_menu_page(_x('Group Categories', 'admin page title', 'bp-custocg'), _x('Group Categories', 'admin menu title', 'bp-custocg'), 'bp_moderate', 'bp-group-categories', array($this, 'admin_tags'), 'dashicons-yes', 42);
        $this->admin_submenu = add_submenu_page('bp-group-categories', _x('All Categories', 'admin page title', 'bp-custocg'), _x('All Categories', 'admin menu title', 'bp-custocg'), 'bp_moderate', 'bp-group-categories');

        //if sorting plugin Category Order and Taxonomy Terms Order is available
        if (function_exists('tto_info_box')) {
            $this->admin_submenu_sort = add_submenu_page('bp-group-categories', _x('Sort Group Categories', 'admin page title', 'bp-custocg'), _x('Sort Group Categories', 'admin menu title', 'bp-custocg'), 'bp_moderate', 'bpcgc-sorting', array($this, 'sorting_tags'));
            add_action('admin_head', array($this, 'admin_head_actions'));
        }

        add_action("load-{$this->admin_menu}", array($this, 'admin_tags_load'));
        add_action("bp_group_categories_add_form_fields", array($this, "admin_category_extra_fields"));
        add_action("bp_group_categories_edit_form", array($this, "admin_category_form_extra_fields"));
    }

    /**
     * Adds some query params to the sorting submenu page
     * Need these for the sorting functionality to properly work
     * Hooks into admin_head, because any earlier causes issues
     * @global array $submenu
     */
    function admin_head_actions() {
        global $submenu;

        $submenu['bp-group-categories'][1][2] = 'admin.php?page=bpcgc-sorting&post_type=bp_group&taxonomy=bp_group_categories';
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

        if (empty($this->admin_menu) || false === strpos($this->admin_menu, $current_screen->id)) {
            return;
        }

        if ($current_screen->id !== $this->admin_menu && is_null($page_hook)) {
            $current_screen->id = $this->admin_menu;
            $current_screen->base = $this->admin_menu;
            $page_hook = $this->admin_menu;
        }

        $current_screen->post_type = 'bp_group';
        $current_screen->taxonomy = 'bp_group_categories';
    }

    /**
     * Register a fake post type for the groups component
     *
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     *
     * @global $wp_post_types the list of available post types
     */
    function register_post_type() {
        global $wp_post_types;

        if (empty($_GET['page']) || 'bp-group-categories' != $_GET['page']) {
            return;
        }

        $post_type = 'bp_group';

        // Set needed properties
        $wp_post_types[$post_type] = new stdClass;
        $wp_post_types[$post_type]->show_ui = true;
        $wp_post_types[$post_type]->show_in_menu = false;
        $wp_post_types[$post_type]->show_admin_column = false;
        $wp_post_types[$post_type]->labels = new stdClass;
        $wp_post_types[$post_type]->labels->name = __('Groups', 'bp-custocg');
        $wp_post_types[$post_type]->name = $post_type;
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
        $taxnow = $taxonomy = 'bp_group_categories';

        $redirect_to = add_query_arg('page', 'bp-group-categories', get_admin_url('admin.php'));
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

                    $inserted = $this->add_tag();

                    if (!empty($inserted) && !is_wp_error($inserted)) {
                        $redirect_to = add_query_arg('message', 1, $redirect_to);
                        //term meta
                        $this->process_term_meta($_POST, $inserted);
                    } else {
                        $redirect_to = add_query_arg('message', 4, $redirect_to);
                    }
                    wp_redirect($redirect_to);
                    exit;

                case 'delete':
                case 'bulk-delete':
                    $tag_IDs = array();
                    $query_args = array();

                    if (empty($_REQUEST['tag_ID']) && empty($_REQUEST['delete_tags'])) {
                        wp_redirect($redirect_to);
                        exit;
                    } else if (!empty($_REQUEST['tag_ID'])) {
                        $tag_ID = absint($_REQUEST['tag_ID']);
                        check_admin_referer('delete-tag_' . $tag_ID);
                        $tag_IDs = array($tag_ID);
                        $query_args['message'] = 2;
                    } else {
                        check_admin_referer('bulk-tags');
                        $tag_IDs = wp_parse_id_list($_REQUEST['delete_tags']);
                        $query_args['message'] = 6;
                    }

                    if (!bp_current_user_can($bp_group_categories_tax->cap->delete_terms)) {
                        wp_die($cheating);
                    }

                    foreach ($tag_IDs as $tag_ID) {
                        BPCGC_Groups_Terms::delete_term($tag_ID, $bp_group_categories_tax->name);
                    }

                    $redirect_to = add_query_arg($query_args, $redirect_to);
                    wp_redirect($redirect_to);
                    exit;

                case 'edit':

                    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/bp-customizable-group-categories-admin.js', array('jquery'), $this->version, false);
                    wp_localize_script($this->plugin_name, 'BP_CustoCG_Admin', array(
                        'edit_action' => $redirect_to,
                    ));

                    $tax = get_taxonomy($taxnow);
                    $title = $tax->labels->edit_item;

                    $tag_ID = (int) $_REQUEST['tag_ID'];

                    $tag = get_term($tag_ID, $taxonomy, OBJECT, 'edit');
                    if (!$tag)
                        wp_die(__('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?'));

                    require_once( ABSPATH . 'wp-admin/admin-header.php' );
                    include( ABSPATH . 'wp-admin/edit-tag-form.php' );
                    include( ABSPATH . 'wp-admin/admin-footer.php' );
                    exit;

                case 'editedtag':
                    $tag_ID = (int) $_POST['tag_ID'];
                    check_admin_referer('update-tag_' . $tag_ID);

                    if (!bp_current_user_can($bp_group_categories_tax->cap->edit_terms))
                        wp_die($cheating);

                    $tag = BPCGC_Groups_Terms::get_term($tag_ID, $bp_group_categories_tax->name);
                    if (!$tag) {
                        wp_die(__('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?', 'bp-custocg'));
                    }

                    $ret = BPCGC_Groups_Terms::update_term($tag_ID, $bp_group_categories_tax->name, $_POST);
                    
                    if (!empty($ret) && !is_wp_error($ret)) {
                        //term meta
                        $this->process_term_meta($_POST, $tag);
                        $redirect_to = add_query_arg('message', 5, $redirect_to);
                    } else {
                        $redirect_to = add_query_arg('message', 3, $redirect_to);
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
     * Sorting tags
     */
    public function sorting_tags() {

        //first we have to clear the term cache, otherwise the ordering is loaded incorrectly
        $args = array(
            'taxonomy' => 'bp_group_categories',
            'fields' => 'ids',
        );
        $term_ids = get_terms($args);
        clean_term_cache($term_ids, 'bp_group_categories');

        include(WP_PLUGIN_DIR . '/taxonomy-terms-order/include/interface.php');
        TOPluginInterface();
    }

    /**
     * Not used, everything is done in BP_Groups_Tag_Admin->admin_tags_load()
     *
     * @access public
     * @since BP Customizable Group Categories(1.0.0)
     */
    public function admin_tags() {
        
    }

    public function admin_category_extra_fields() {
        $fields_out = '';

        $edit_form = false;

        ob_start();
        include(CUSTOCG_BASE_DIR . '/parts/category-extra-fields.php');
        $fields_out = ob_get_clean();

        echo $fields_out;
    }

    public function admin_category_form_extra_fields() {
        $fields_out = '';

        $edit_form = true;

        $possible_groups = array('club', 'project');
        $values = array();

        $tag_ID = (int) $_REQUEST['tag_ID'];

        $tag = get_term($tag_ID, 'bp_group_categories', OBJECT, 'edit');

        foreach ($possible_groups as $group) {

            $key = 'bpcgc_group_' . $group;
            $term_value = get_term_meta($tag->term_id, $key, true);
            if ($term_value) {
                $values[$group] = $term_value;
            }
        }

        ob_start();
        include(CUSTOCG_BASE_DIR . '/parts/category-extra-fields.php');
        $fields_out = ob_get_clean();

        echo $fields_out;
    }

    private function process_term_meta($data, $tag) {

        if (isset($data['group']) && !empty($data['group'])) {

            foreach ($data['group'] as $group) {

                $key = 'bpcgc_group_' . $group;

                $term_update = add_term_meta($tag->term_id, $key, true, true);
            }
        }
    }

    /**
     * Make sure the edit term link for the group tags
     * will point to our custom edit-tags administration
     *
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     */
    public function edit_term_link($link = '', $term_id = 0, $taxonomy = '', $object_type = '') {
        if (empty($taxonomy) || 'bp_group_categories' != $taxonomy) {
            return $link;
        }

        $query_args = array(
            'page' => 'bp-group-categories',
            'action' => 'edit',
            'tag_ID' => $term_id,
        );

        return add_query_arg($query_args, get_admin_url('admin.php'));
    }

    public function add_bp_customizable_category() {
        $taxonomy = 'bp_group_categories';
        $x = new WP_Ajax_Response();

        $tag = $this->add_tag();

        if (!$tag || is_wp_error($tag) || (!$tag = get_term($tag['term_id'], $taxonomy))) {
            $message = __('An error has occurred. Please reload the page and try again.');
            if (is_wp_error($tag) && $tag->get_error_message())
                $message = $tag->get_error_message();

            $x->add(array(
                'what' => 'taxonomy',
                'data' => new WP_Error('error', $message)
            ));
            $x->send();
        }

        //term meta
        $this->process_term_meta($_POST, $tag);

        $wp_list_table = _get_list_table('WP_Terms_List_Table', array('screen' => $_POST['screen']));

        $level = 0;
        if (is_taxonomy_hierarchical($taxonomy)) {
            $level = count(get_ancestors($tag->term_id, $taxonomy, 'taxonomy'));
            ob_start();
            $wp_list_table->single_row($tag, $level);
            $noparents = ob_get_clean();
        }

        ob_start();
        $wp_list_table->single_row($tag);
        $parents = ob_get_clean();

        $x->add(array(
            'what' => 'taxonomy',
            'supplemental' => compact('parents', 'noparents')
        ));
        $x->add(array(
            'what' => 'term',
            'position' => $level,
            'supplemental' => (array) $tag
        ));
        $x->send();
    }

    private function add_tag() {

        $bp_group_categories_tax = get_taxonomy('bp_group_categories');

        check_admin_referer('add-tag', '_wpnonce_add-tag');

        if (!bp_current_user_can($bp_group_categories_tax->cap->edit_terms)) {
            wp_die($cheating);
        }

        $inserted = BPCGC_Groups_Terms::insert_term($_POST['tag-name'], $bp_group_categories_tax->name, $_POST);

        return $inserted;
    }

}
