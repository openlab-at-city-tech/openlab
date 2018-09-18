<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class CMTooltipGlossaryShared {

    protected static $instance = NULL;
    public static $calledClassName;

    public static function instance() {
        $class = __CLASS__;
        if ( !isset( self::$instance ) && !( self::$instance instanceof $class ) ) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct() {
        if ( empty( self::$calledClassName ) ) {
            self::$calledClassName = __CLASS__;
        }

        self::setupConstants();
        self::setupOptions();
        self::loadClasses();
        self::registerActions();

        if ( get_option( 'cmtt_afterActivation', 0 ) == 1 ) {
            add_action( 'admin_notices', array( self::$calledClassName, '_showProMessage' ) );
        }
    }

    /**
     * Shows the message about Pro versions on activate
     */
    public static function _showProMessage() {
        /*
         * Only show to admins
         */
        if ( current_user_can( 'manage_options' ) ) {
            ?>
             <?php
            delete_option( 'cmtt_afterActivation' );
        }
    }

    /**
     * Register the plugin's shared actions (both backend and frontend)
     */
    private static function registerActions() {
        add_action( 'init', array( self::$calledClassName, 'cmtt_create_post_types' ) );
        add_action( 'init', array( self::$calledClassName, 'cmtt_create_taxonomies' ) );
        add_action( 'ava_after_main_title', array( __CLASS__, 'enfoldFix' ) );
    }

    public static function enfoldFix() {
        global $post;
        if ( empty( $post ) ) {
            return;
        }

        if ( class_exists( 'AviaHelper' ) && method_exists( 'AviaHelper', 'builder_status' ) && 'active' == AviaHelper::builder_status( $post->ID ) ) {
            remove_filter( 'the_content', array( 'CMTooltipGlossaryFrontend', 'cmtt_glossary_parse' ), 9999 );
            add_filter( 'av_complete_content', array( 'CMTooltipGlossaryFrontend', 'cmtt_glossary_parse' ), 9999 );
        }
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupConstants() {
        define( 'CMTT_MENU_OPTION', 'cmtt_menu_option' );
        define( 'CMTT_ABOUT_OPTION', 'cmtt_about' );
        define( 'CMTT_EXTENSIONS_OPTION', 'cmtt_extensions' );
        define( 'CMTT_PRO_OPTION', 'cmtt_pro' );
        define( 'CMTT_SETTINGS_OPTION', 'cmtt_settings' );
        define( 'CMTT_IMPORTEXPORT_OPTION', 'cmtt_importexport' );
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupOptions() {
        /*
         * General settings
         */
        add_option( 'cmtt_glossaryOnPages', 1 ); //Show on Pages?
        add_option( 'cmtt_glossaryOnPosts', 1 ); //Show on Posts?
        add_option( 'cmtt_glossaryID', -1 ); //The ID of the main Glossary Page
        add_option( 'cmtt_glossaryPermalink', 'glossary' ); //Set permalink name
        add_option( 'cmtt_glossaryOnlySingle', 0 ); //Show on Home and Category Pages or just single post pages?
        add_option( 'cmtt_glossaryFirstOnly', 0 ); //Search for all occurances in a post or only one?
        add_option( 'cmtt_glossaryCaseSensitive', 0 ); //Should the terms be case sensitive
        add_option( 'cmtt_glossaryOnlySpaceSeparated', 1 ); //Search only for words separated by spaces
        add_option( 'cmtt_glossaryOnMainQuery', 1 ); //Analyze only on "main" WP query?
        add_option( 'cmtt_removeGlossaryCreateListFilter', 0 ); //Remove the create list filter after it's outputted?

        /*
         * Referral
         */
        add_option( 'cmtt_glossaryReferral', false );
        add_option( 'cmtt_glossaryAffiliateCode', '' );

        /*
         * Glossary Index Page
         */
        add_option( 'cmtt_glossaryDiffLinkClass', 0 ); //Use different class to style glossary list
        add_option( 'cmtt_glossaryListTiles', 0 ); // Display glossary terms list as tiles

        /*
         * Glossary Term
         */
        add_option( 'cmtt_glossaryListTermLink', 0 ); //Remove links from glossary index to glossary page
        add_option( 'cmtt_showTitleAttribute', 0 ); //show HTML title attribute
        add_option( 'cmtt_glossaryInNewPage', 0 ); //In New Page?

        /*
         * Tooltip
         */
        add_option( 'cmtt_glossaryTooltip', 1 ); //Use tooltips on glossary items?
        add_option( 'cmtt_glossaryLimitTooltip', 0 ); // Limit the tooltip length  ?
        add_option( 'cmtt_glossaryFilterTooltip', 0 ); //Clean the tooltip text from uneeded chars?
        add_option( 'cmtt_glossaryProtectedTags', 1 ); //Aviod the use of Glossary in Protected tags?
        add_option( 'cmtt_glossaryExcerptHover', 0 ); //Search for all occurances in a post or only one?

        /*
         * Adding additional options
         */
        do_action( 'cmtt_setup_options' );
    }

    /**
     * Create custom post type
     */
    public static function cmtt_create_post_types() {
        $glossaryPermalink = get_option( 'cmtt_glossaryPermalink' );
        global $wp_rewrite;
        // First, we "add" the custom post type via the above written function.

        $args = array(
            'label'               => 'Glossary',
            'labels'              => array(
                'add_new_item'  => 'Add New Glossary Item',
                'add_new'       => 'Add Glossary Item',
                'edit_item'     => 'Edit Glossary Item',
                'view_item'     => 'View Glossary Item',
                'singular_name' => 'Glossary Item',
                'name'          => CMTT_NAME,
                'menu_name'     => 'Glossary'
            ),
            'description'         => '',
            'map_meta_cap'        => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_admin_bar'   => true,
            'show_in_menu'        => CMTT_MENU_OPTION,
            '_builtin'            => false,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'has_archive'         => false,
            'rewrite'             => array( 'slug' => $glossaryPermalink, 'with_front' => false, 'feeds' => true, 'feed' => true ),
            'query_var'           => true,
            'supports'            => array( 'title', 'editor', 'author', 'comments', 'excerpt', 'revisions',
                'custom-fields', 'page-attributes' ) );

        register_post_type( 'glossary', apply_filters( 'cmtt_post_type_args', $args ) );

        $wp_rewrite->extra_permastructs[ 'glossary' ] = array();
        $args                                       = (object) $args;

        $post_type    = 'glossary';
        $archive_slug = $args->rewrite[ 'slug' ];
        if ( $args->rewrite[ 'with_front' ] ) {
            $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
        } else {
            $archive_slug = $wp_rewrite->root . $archive_slug;
        }
        if ( $args->rewrite[ 'feeds' ] && $wp_rewrite->feeds ) {
            $feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
            add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
            add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
        }

        $permastruct_args         = $args->rewrite;
        $permastruct_args[ 'feed' ] = $permastruct_args[ 'feeds' ];
        add_permastruct( $post_type, "{$args->rewrite[ 'slug' ]}/%$post_type%", $permastruct_args );
    }

    /**
     * Create taxonomies
     */
    public static function cmtt_create_taxonomies() {
        return;
    }

    /**
     * Load plugin's required classes
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function loadClasses() {
        /*
         * Load the file with shared global functions
         */
        include_once CMTT_PLUGIN_DIR . "shared/functions.php";
    }

    public function registerShortcodes() {
        return;
    }

    public function registerFilters() {
        return;
    }

    public static function initSession() {
        if ( !session_id() ) {
            session_start();
        }
    }

    /**
     * Function tries to generate the new Glossary Index Page
     */
    public static function tryGenerateGlossaryIndexPage() {
        $glossaryIndexId = get_option( 'cmtt_glossaryID', -1 );
        if ( $glossaryIndexId == -1 ) {
            $id = wp_insert_post( array(
                'post_author' => get_current_user_id(),
                'post_status' => 'publish',
                'post_title'  => 'Glossary',
                'post_type'   => 'page'
            ) );

            if ( is_numeric( $id ) ) {
                update_option( 'cmtt_glossaryID', $id );
            }
        }
    }

    /**
     * Function seaches for the options with prefix "red_" (old tooltip options were prefixed that way) and applies their values to the new options
     */
    public static function tryResetOldOptions() {
        $all_options = wp_load_alloptions();
        foreach ( $all_options as $name => $value ) {
            if ( stripos( $name, 'red_' ) === 0 ) {
                $realOptionName    = 'cmtt_' . str_ireplace( 'red_', '', $name );
                $unserialisedValue = maybe_unserialize( $value );
                update_option( $realOptionName, $unserialisedValue );
                delete_option( $name );
            }
        }
    }

}
