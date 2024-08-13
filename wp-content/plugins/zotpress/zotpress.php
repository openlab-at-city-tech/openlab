<?php

/*

    Plugin Name: Zotpress
    Plugin URI: http://katieseaborn.com/plugins
    Description: Bringing Zotero and scholarly blogging to your WordPress website.
    Author: Katie Seaborn
    Version: 7.3.10
    Author URI: http://katieseaborn.com
    Text Domain: zotpress
    Domain Path: /languages/

*/

/*

    Copyright 2024 Katie Seaborn

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

        http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.

*/


// DESIGN ---------------------------------------------------------------------------------------
/*

For requests:

- Check the database for cached items with PHP:
    - If using the cache, indicate with `used_cache` class
    - If no cache, switch to JS and save to cache
- Format with JS
- Check for updates with JS
- Format again with JS

To-do:
* Zotero-API-Key` rather than `key=`.
* qmode=titleCreatorYear&q=2000,2001 -> v3 doesn't work
* multiple itemtype

*/
// DESIGN ---------------------------------------------------------------------------------------



// DIRECT ACESS ---------------------------------------------------------------------------------

// Thanks to Jörg Mechnich (jmechnich@github)

if ( ! defined( 'ABSPATH' ) ) {

    exit; // Don't access directly.
};

// DIRECT ACESS ---------------------------------------------------------------------------------



// GLOBAL VARS ----------------------------------------------------------------------------------

    define('ZOTPRESS_PLUGIN_FILE',  __FILE__ );
    define('ZOTPRESS_PLUGIN_URL', plugin_dir_url( ZOTPRESS_PLUGIN_FILE ));
    define('ZOTPRESS_PLUGIN_DIR', dirname( __FILE__ ));
    define('ZOTPRESS_VERSION', '7.3.10' );

    // NOTE: Remember to set to TRUE after dev and before version release
    define('ZOTPRESS_LIVEMODE', true );

    $GLOBALS['zp_is_shortcode_displayed'] = false;
    $GLOBALS['zp_shortcode_instances'] = array();

    // 5.2.6: NOTE: Only change if the db needs updating 
    $GLOBALS['Zotpress_update_db_by_version'] = '7.1.4';

// GLOBAL VARS ----------------------------------------------------------------------------------



// LOCALIZATION ----------------------------------------------------------------------------------

    // TODO: Apply localization to the entire plugin.
    // TODO: Don't forget JS files, which have a special procedure.

    function Zotpress_load_plugin_textdomain() {
      load_plugin_textdomain( 'zotpress', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    add_action( 'plugins_loaded', 'Zotpress_load_plugin_textdomain' );

// LOCALIZATION ----------------------------------------------------------------------------------



// INSTALL -----------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/admin/admin.install.php' );

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -------------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/admin/admin.php' );

    add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'zotpress_add_plugin_page_settings_link');
    function zotpress_add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="' .
        admin_url( 'admin.php?page=Zotpress' ) .
        '">' . __('Explore','zotpress') . '</a>';
        return $links;
    }

// END ADMIN --------------------------------------------------------------------------------------



// SHORTCODE -------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/shortcode/shortcode.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intext.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intextbib.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.lib.php' );

// SHORTCODE -------------------------------------------------------------------------------------



// WIDGET & METABOX -----------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/widget/widget.sidebar.php' );
	include( dirname(__FILE__) . '/lib/widget/widget.php' );

    function Zotpress_format_script_register()
    {
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

        wp_register_script(
            'zotpress.gutenberg'.$minify.'.js',
            ZOTPRESS_PLUGIN_URL . 'js/zotpress.gutenberg'.$minify.'.js',
            array( 'wp-rich-text', 'wp-element', 'wp-editor', 'jquery' )
        );
    }
    add_action( 'init', 'Zotpress_format_script_register' );

    function Zotpress_format_enqueue_assets_editor()
    {
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'zotpress.gutenberg'.$minify.'.js' );
        wp_localize_script(
			'zotpress.gutenberg'.$minify.'.js',
			'zpTranslate',
			array(
                'txt_insertsc' => __('Insert Shortcode','zotpress'),
                'txt_generatesc' => __('Generate Shortcode','zotpress')
			)
		);
    }
    add_action( 'enqueue_block_editor_assets', 'Zotpress_format_enqueue_assets_editor' );

// WIDGET & METABOX -----------------------------------------------------------------------------



// REGISTER ACTIONS -----------------------------------------------------------------------------

    /**
    * Admin scripts and styles
    */
    function Zotpress_admin_scripts_css($hook)
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		if ( isset($_GET['page']) && ($_GET['page'] == 'Zotpress') )
		{
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();
			wp_enqueue_script( 'jquery.dotimeout.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array( 'jquery' ) );

			if ( !in_array( $hook, array('post.php', 'post-new.php') ) )
			{
				wp_enqueue_script( 'jquery.livequery.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.min.js', array( 'jquery' ) );
			}

			if ( isset($_GET['help']) && ($_GET['help'] == 'true') )
			{
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_style( 'zotpress.help'.$minify.'.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.help'.$minify.'.css' );
				wp_enqueue_script( 'zotpress.help.min.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.help.min.js', array( 'jquery' ) );
			}

            wp_enqueue_style( 'zotpress.shortcode'.$minify.'.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.shortcode'.$minify.'.css' );
            wp_enqueue_style( 'zotpress.admin'.$minify.'.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.admin'.$minify.'.css' );
		}
    }
    add_action( 'admin_enqueue_scripts', 'Zotpress_admin_scripts_css' );


	function Zotpress_enqueue_admin_ajax( $hook )
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		if ( stripos( $hook, "zotpress" ) !== false )
		{
			wp_enqueue_script( 'zotpress.admin'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.admin'.$minify.'.js', array( 'jquery','media-upload','thickbox' ) );
			wp_localize_script(
				'zotpress.admin'.$minify.'.js',
				'zpAccountsAJAX',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'zpAccountsAJAX_nonce' => wp_create_nonce( 'zpAccountsAJAX_nonce_val' ),
					'action' => 'zpAccountsViaAJAX',
                    'txt_success' => __('Success','zotpress'),
                    'txt_chooseimg' => __('Choose Image','zotpress'),
                    'txt_accvalid' => __('Your Zotero account has been validated.','zotpress'),
                    'txt_sureremove' => __('Are you sure you want to remove this account?','zotpress'),
                    'txt_surecache' => __('Are you sure you want to clear the cache for this account?','zotpress'),
                    'txt_cachecleared' => __('Cache cleared!','zotpress'),
                    'txt_oops' => __('Oops!','zotpress'),
                    'txt_changeimg' => __( 'Change Image', 'zotpress' ),
                    'txt_setimg' => __( 'Set Image', 'zotpress' ),
                    'txt_removeimg' => __( 'Remove Image', 'zotpress' ),
                    'txt_surereset' => __('Are you sure you want to reset Zotpress? This cannot be undone.','zotpress'),
                    'txt_default' => __('Default','zotpress')
				)
			);
		} // Zotpress pages only

        wp_enqueue_script( 'zotpress.admin.notices'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.admin.notices'.$minify.'.js', array( 'jquery' ) );
        wp_localize_script(
        	'zotpress.admin.notices'.$minify.'.js',
        	'zpNoticesAJAX',
        	array(
        		'ajaxurl' => admin_url( 'admin-ajax.php' ),
        		'zpNoticesAJAX_nonce' => wp_create_nonce( 'zpNoticesAJAX_nonce_val' ),
        		'action' => 'zpNoticesViaAJAX'
        	)
        );
	}
    add_action( 'admin_enqueue_scripts', 'Zotpress_enqueue_admin_ajax' );


    /**
    * Add Zotpress to admin menu
    */
    function Zotpress_admin_menu()
    {
        add_menu_page( "Zotpress", "Zotpress", "edit_posts", "Zotpress", "Zotpress_options", ZOTPRESS_PLUGIN_URL."images/icon-menu.svg" );
		add_submenu_page( "Zotpress", "Zotpress", __('Browse','zotpress'), "edit_posts", "Zotpress" );
		add_submenu_page( "Zotpress", "Accounts", __('Accounts','zotpress'), "edit_posts", "admin.php?page=Zotpress&accounts=true" );
		add_submenu_page( "Zotpress", "Options", __('Options','zotpress'), "edit_posts", "admin.php?page=Zotpress&options=true" );
		add_submenu_page( "Zotpress", "Help", __('Help','zotpress'), "edit_posts", "admin.php?page=Zotpress&help=true" );
    }
    add_action( 'admin_menu', 'Zotpress_admin_menu' );

	function Zotpress_admin_menu_submenu($parent_file)
	{
		global $submenu_file;

		if ( isset($_GET['accounts']) || isset($_GET['selective'])  || isset($_GET['import']) ) $submenu_file = 'admin.php?page=Zotpress&accounts=true';
		if ( isset($_GET['options']) ) $submenu_file = 'admin.php?page=Zotpress&options=true';
		if ( isset($_GET['help']) ) $submenu_file = 'admin.php?page=Zotpress&help=true';

		return $parent_file;
	}
	add_filter('parent_file', 'Zotpress_admin_menu_submenu');


    /**
    * Add shortcode styles to user's theme
    * Note that this always displays: There's no way to conditionally include it,
    * because the existence of shortcodes is checked after CSS is included.
    */
    function Zotpress_theme_includes()
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

        wp_register_style('zotpress.shortcode'.$minify.'.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.shortcode'.$minify.'.css');
        wp_enqueue_style('zotpress.shortcode'.$minify.'.css');
    }
    add_action('wp_print_styles', 'Zotpress_theme_includes');


    /**
    * Change HTTP request timeout
    * Changed in 7.3.7
    */
    // function Zotpress_change_timeout($time): int { return 60; /* second */ }
    function Zotpress_change_timeout($time) { return 60; /* second */ }
    add_filter('http_request_timeout', 'Zotpress_change_timeout');



    // Enqueue jQuery in theme if it isn't already enqueued
    // Thanks to WordPress user "eceleste"
    function Zotpress_enqueue_scripts()
    {
        if ( ! isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] ) )
            wp_enqueue_script("jquery");
    }
    add_action( 'wp_enqueue_scripts' , 'Zotpress_enqueue_scripts' );

    // Add shortcodes and sidebar widget
    add_shortcode( 'zotpress', 'Zotpress_func' );
    add_shortcode( 'zotpressInText', 'Zotpress_zotpressInText' );
    add_shortcode( 'zotpressInTextBib', 'Zotpress_zotpressInTextBib' );
    add_shortcode( 'zotpressLib', 'Zotpress_zotpressLib' );
    add_action( 'widgets_init', 'ZotpressSidebarWidgetInit' );

    // Conditionally serve shortcode scripts
    function Zotpress_theme_conditional_scripts_footer()
    {
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

        if ( $GLOBALS['zp_is_shortcode_displayed'] === true )
        {
            if ( ! is_admin() ) wp_enqueue_script('jquery');
            wp_register_script('jquery.livequery.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.min.js', array('jquery'));
            wp_enqueue_script('jquery.livequery.min.js');

			wp_enqueue_script("jquery-effects-core");
			wp_enqueue_script("jquery-effects-highlight");

            wp_enqueue_script( 'zotpress.default'.$minify.'.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.default'.$minify.'.js', array( 'jquery' ) );
        }
    }
    add_action('wp_footer', 'Zotpress_theme_conditional_scripts_footer');


	function Zotpress_enqueue_shortcode_bib()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		wp_register_script( 'zotpress.shortcode.bib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.shortcode.bib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'zotpress.shortcode.bib'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
                'action' => 'zpRetrieveViaShortcode',
                'txt_removeimg' => __('Remove Image', 'zotpress'),
                'txt_zperror' => __('There was a Zotpress error:', 'zotpress'),
                'txt_noitemsfound' => __( 'No items found.', 'zotpress' )
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'Zotpress_enqueue_shortcode_bib' );


	function Zotpress_enqueue_shortcode_intext()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		wp_register_script( 'zotpress.shortcode.intext'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.shortcode.intext'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'zotpress.shortcode.intext'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode',
                'txt_zperror' => __('There was a Zotpress error:', 'zotpress'),
                'txt_noitemsfound' => __( 'No items found.', 'zotpress' )
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'Zotpress_enqueue_shortcode_intext' );


	function Zotpress_enqueue_lib_dropdown()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		wp_register_script( 'zotpress.lib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.lib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_register_script( 'zotpress.lib.dropdown'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.lib.dropdown'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'zotpress.lib.dropdown'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode',
                'txt_loading' => __( 'Loading', 'zotpress' ),
                'txt_items' => __( 'items', 'zotpress' ),
                'txt_subcoll' => __( 'subcollections', 'zotpress' ),
                'txt_changeimg' => __( 'Change Image', 'zotpress' ),
                'txt_setimg' => __( 'Set Image', 'zotpress' ),
                'txt_itemkey' => __( 'Item Key', 'zotpress' ),
                'txt_nocitations' => __( 'There are no citations to display.', 'zotpress' ),
                'txt_toplevel' => __( 'Top Level', 'zotpress' ),
                'txt_nocollsel' => __( 'No Collection Selected', 'zotpress' ),
                'txt_backtotop' => __( 'Back', 'zotpress' ),
                'txt_unsettag' => __( 'Unset Tag', 'zotpress' ),
                'txt_notagsel' => __( 'No Tag Selected', 'zotpress' ),
                'txt_notags' => __( 'No tags to display', 'zotpress' )
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'Zotpress_enqueue_lib_dropdown' );
	add_action( 'admin_enqueue_scripts', 'Zotpress_enqueue_lib_dropdown' );


	function Zotpress_enqueue_lib_searchbar()
	{
        // Turn on/off minified versions if testing/live
        $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

		wp_register_script( 'zotpress.lib'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.lib'.$minify.'.js', array( 'jquery' ), false, true );
		wp_register_script( 'zotpress.lib.searchbar'.$minify.'.js', plugin_dir_url( __FILE__ ) . 'js/zotpress.lib.searchbar'.$minify.'.js', array( 'jquery' ), false, true );
		wp_localize_script(
			'zotpress.lib.searchbar'.$minify.'.js',
			'zpShortcodeAJAX',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpShortcode_nonce' => wp_create_nonce( 'zpShortcode_nonce_val' ),
				'action' => 'zpRetrieveViaShortcode',
                'txt_typetosearch' => __('Type to search','zotpress')
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'Zotpress_enqueue_lib_searchbar' );
	add_action( 'admin_enqueue_scripts', 'Zotpress_enqueue_lib_searchbar' );

// REGISTER ACTIONS 	--------------------------------------------------------------------------------


// SECURITY 	----------------------------------------------------------------------------------------

	function zp_nonce_life() {
		return 24 * HOUR_IN_SECONDS;
	}
	add_filter( 'nonce_life', 'zp_nonce_life' );

// SECURITY 	----------------------------------------------------------------------------------------


// ZOTPRESS NOTIFICATIONS 	------------------------------------------------------------------------

    if ( in_array( ZOTPRESS_VERSION, array( "6.2.1", "6.2.2", "7.1.1", "7.1.2", "7.1.3", "7.1.4" ) ) )
    {
        function Zotpress_plugin_update_message( $data, $response ) {
        	printf(
        		'<span style="display:block;font-weight:bold;margin:0.7em 0;"><span class="dashicons dashicons-warning" style="margin-right:6px;"></span>%s</span>',
        		__( 'Be sure to clear the Zotpress cache for each account after updating!', 'zotpress' )
        	);
        }
        add_action( 'in_plugin_update_message-zotpress/zotpress.php', 'Zotpress_plugin_update_message', 10, 2 );
    }

    if ( in_array( ZOTPRESS_VERSION, array( "6.2.1", "6.2.2", "7.1.4" ) ) )
    {
        if ( zp_get_total_accounts() > 0
                && ! get_option( 'Zotpress_update_notice_dismissed' ) )
            add_action( 'admin_notices', 'Zotpress_update_notice' );

        function Zotpress_update_notice()
        {
        ?>
            <div class="notice update-nag Zotpress_update_notice is-dismissible" >
                <p><?php echo __( '<strong>Warning:</strong> Due to major updates in this version of Zotpress, you may need to clear the cache of each synced Zotero account.', 'zotpress' ); ?></p>
                <p>&raquo; <a href="admin.php?page=Zotpress&accounts=true"><?php echo __( 'Acccounts'); ?></a></p>
            </div>
        <?php
        }

        function Zotpress_dismiss_update_notice()
        {
            if ( ! get_option( 'Zotpress_update_notice_dismissed' )
                    || get_option( 'Zotpress_update_notice_dismissed' ) == 0 )
                update_option( 'Zotpress_update_notice_dismissed', 1 );
        }
        add_action( 'wp_ajax_zpNoticesViaAJAX', 'Zotpress_dismiss_update_notice' );
    }

// ZOTPRESS NOTIFICATIONS 	------------------------------------------------------------------------


?>
