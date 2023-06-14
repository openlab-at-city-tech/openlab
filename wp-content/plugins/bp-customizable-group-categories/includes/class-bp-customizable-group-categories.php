<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://early-adopter.com/
 * @since      1.0.0
 *
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 * @author     Joe Unander <joe@early-adopter.com>
 */
class Bp_Customizable_Group_Categories {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Bp_Customizable_Group_Categories_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Required BuddyPress version
     *
     * @package BP Customizable Group Categories
     * @since   BP Customizable Group Categories (1.0.0)
     *
     * @var      string
     */
    public static $required_version = array(
        'wp' => 4.3,
        'bp' => 2.5,
    );

    /**
     *
     * @package BP Customizable Group Categories
     * @since   BP Customizable Group Categories (1.0.0)
     *
     * @var      string
     */
    public static $bp_version_fixed = '';

    /**
     * Some params to customize the plugin
     *
     * @package BP Customizable Group Categories
     * @since   BP Customizable Group Categories (1.0.0)
     *
     * @var      array
     */
    public $params;

	public $domain;
	public $file;
	public $basename;
	public $wp_version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->setup_globals();

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Sets some globals for the plugin
     *
     * @package BP Customizable Group Categories
     * @access private
     * @since BP Customizable Group Categories (1.0.0)
     */
    private function setup_globals() {
        $this->plugin_name = 'bp-customizable-group-categories';
        $this->version = '1.0.0';
        $this->domain = 'bp-custocg';
        $this->file = __FILE__;
        $this->basename = plugin_basename($this->file);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Bp_Customizable_Group_Categories_Loader. Orchestrates the hooks of the plugin.
     * - Bp_Customizable_Group_Categories_i18n. Defines internationalization functionality.
     * - Bp_Customizable_Group_Categories_Admin. Defines all hooks for the admin area.
     * - Bp_Customizable_Group_Categories_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bp-customizable-group-categories-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bp-customizable-group-categories-i18n.php';

        if (bp_is_active('groups')) {
            /**
             * Class for managing group taxonomies
             * Ported from BP Groups Taxo
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/imported/class-bp-groups-taxonomy.php';

            /**
             * Class for managing group categories
             * Ported from BP Groups Taxo
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/imported/class-bp-groups-category.php';
        }

        /**
         * Supporting term metadata in WordPress installs < 4.4
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/term-metadata/term-metadata.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bp-customizable-group-categories-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-bp-customizable-group-categories-public.php';

        /**
         * Utility functions
         */
        require_once plugin_dir_path(dirname(__FILE__)).'includes/utility/bp-customizable-group-utility-functions.php';

        $this->loader = new Bp_Customizable_Group_Categories_Loader();
    }

    /**
     * Checks BuddyPress & WordPress versions
     *
     * @package BP Customizable Group Categories
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     */
    public function version_check() {
        // taking no risk
        if (!defined('BP_VERSION')) {
            return false;
        }

        $return = version_compare(BP_VERSION, self::$required_version['bp'], '>=');

        $this->wp_version = 0;
        if (isset($GLOBALS['wp_version'])) {
            $this->wp_version = $GLOBALS['wp_version'];
        }

        if ($return) {
            $return = !empty($this->wp_version) && version_compare($this->wp_version, self::$required_version['wp'], '>=');
        }

        if (!empty(self::$bp_version_fixed) && version_compare(BP_VERSION, self::$bp_version_fixed, '>=')) {
            $return = false;
        }

        return $return;
    }

    /**
     * Checks if current blog is the one where BuddyPress is activated
     *
     * @package BP Customizable Group Categories
     * @access public
     * @since BP Customizable Group Categories (1.0.0)
     */
    public function root_blog_check() {

        if (!function_exists('bp_get_root_blog_id'))
            return false;

        if (get_current_blog_id() != bp_get_root_blog_id())
            return false;

        return true;
    }

    /**
     * Make sure the "tag" or "category" slug will not be use by a group
     *
     * @package BP Customizable Group Categories
     * @since 1.0.0
     */
    public function restrict_slug($groups_forbidden_names = array()) {
        $groups_forbidden_names[] = 'tag';
        $groups_forbidden_names[] = 'category';
        return $groups_forbidden_names;
    }

    /**
     * Display a message to admin in case config is not as expected
     *
     * @package BP Customizable Group Categories
     * @since 1.0.0
     */
    public function admin_warning() {
        $warnings = array();

        if (!$this->version_check()) {
            $warnings[] = sprintf(__('BP Customizable Group Categories requires at least version %1$s of BuddyPress and version %2$s of WordPress.', 'bp-custocg'), self::$required_version['bp'], self::$required_version['wp']);
        }

        if (!bp_core_do_network_admin() && !$this->root_blog_check()) {
            $warnings[] = __('BP Customizable Group Categories requires to be activated on the blog where BuddyPress is activated.', 'bp-custocg');
        }

        if (!empty($warnings)) :
            ?>
            <div id="message" class="error">
                <?php foreach ($warnings as $warning) : ?>
                    <p><?php echo esc_html($warning); ?>
                    <?php endforeach; ?>
            </div>
            <?php
        endif;
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Bp_Customizable_Group_Categories_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Bp_Customizable_Group_Categories_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        if ($this->version_check() && $this->root_blog_check()) {
            $plugin_admin = new Bp_Customizable_Group_Categories_Admin($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('admin_menu', $plugin_admin, 'bp_groups_admin_menu', 11);
            $this->loader->add_action('current_screen', $plugin_admin, 'set_current_screen', 10);
            $this->loader->add_action('admin_init', $plugin_admin, 'register_post_type', 10);

            $this->loader->add_filter('get_edit_term_link', $plugin_admin, 'edit_term_link', 10, 4);

            $this->loader->add_action('bp_groups_admin_load', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('bp_groups_admin_load', $plugin_admin, 'enqueue_scripts');

            //ajax
            $this->loader->add_action('wp_ajax_add-bp-customizable-category', $plugin_admin,'add_bp_customizable_category');

            // Filters
            add_filter('groups_forbidden_names', array($this, 'restrict_slug'), 1, 1);
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        if ($this->version_check() && $this->root_blog_check()) {
            add_action('bp_init', array($this, 'register_taxonomy'), 10);

            $plugin_public = new Bp_Customizable_Group_Categories_Public($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        }
    }

    /**
     * Regiter the taxonomy
     *
     * @package BP Groups Taxo
     * @access private
     * @since BP Groups Taxo (1.0.0)
     */
    public function register_taxonomy() {
        if (!bp_is_root_blog() || !bp_is_active('groups')) {
            return;
        }

        $labels = array(
            'name' => _x('Group Categories', 'taxonomy general name', 'bp-custocg'),
            'singular_name' => _x('Group Tag', 'taxonomy singular name', 'bp-custocg'),
        );

        $bp = buddypress();
        $group_slug = bp_get_groups_slug();

        if (!empty($bp->pages->groups->slug)) {
            $group_slug = $bp->pages->groups->slug;
        }

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_admin_column' => false,
            'query_var' => false,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => $group_slug . '/tag', 'with_front' => false),
            'update_count_callback' => array('BPCGC_Groups_Terms', 'update_term_count'),
        );

        register_taxonomy('bp_group_categories', array('bp_group'), $args);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Bp_Customizable_Group_Categories_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
