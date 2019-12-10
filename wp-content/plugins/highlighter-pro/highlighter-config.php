<?php

if ( ! class_exists( 'Redux' ) ) {
    return;
}

// This is your option name where all the Redux data is stored.
$opt_name = "highlighter_settings";

/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */

$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
    // TYPICAL -> Change these values as you need/desire
    'opt_name'             => $opt_name,
    // This is where your data is stored in the database and also becomes your global variable name.
    'display_name'         => __( 'Highlighter Pro', 'highlighter' ),
    // Name that appears at the top of your panel
    'display_version'      => '1.2',
    // Version that appears at the top of your panel
    'menu_type'            => 'menu',
    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
    'allow_sub_menu'       => true,
    // Show the sections below the admin menu item or not
    'menu_title'           => __( 'Highlighter Pro', 'highlighter' ),
    'page_title'           => __( 'Highlighter Pro', 'highlighter' ),
    // You will need to generate a Google API key to use this feature.
    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
    'google_api_key'       => '',
    // Set it you want google fonts to update weekly. A google_api_key value is required.
    'google_update_weekly' => false,
    // Must be defined to add google fonts to the typography module
    'async_typography'     => true,
    // Use a asynchronous font on the front end or font string
    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
    'admin_bar'            => false,
    // Show the panel pages on the admin bar
    'admin_bar_icon'       => 'dashicons-admin-customizer',
    // Choose an icon for the admin bar menu
    'admin_bar_priority'   => 50,
    // Choose an priority for the admin bar menu
    'global_variable'      => '',
    // Set a different name for your global variable other than the opt_name
    'dev_mode'             => false,
    // Show the time the page took to load, etc
    'update_notice'        => true,
    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
    'customizer'           => true,
    // Enable basic customizer support
    //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
    //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

    // OPTIONAL -> Give you extra features
    'page_priority'        => null,
    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_parent'          => 'themes.php',
    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
    'page_permissions'     => 'manage_options',
    // Permissions needed to access the options panel.
    'menu_icon'            => 'dashicons-admin-customizer',
    // Specify a custom URL to an icon
    'last_tab'             => '',
    // Force your panel to always open to a specific tab (by id)
    'page_icon'            => 'icon-themes',
    // Icon displayed in the admin panel next to your menu_title
    'page_slug'            => 'highlighter_options',
    // Page slug used to denote the panel
    'save_defaults'        => true,
    // On load save the defaults to DB before user clicks save or not
    'default_show'         => false,
    // If true, shows the default value next to each field that is not the default value.
    'default_mark'         => '',
    // What to print by the field's title if the value shown is default. Suggested: *
    'show_import_export'   => true,
    // Shows the Import/Export panel when not used as a field.

    // CAREFUL -> These options are for advanced use only
    'transient_time'       => 60 * MINUTE_IN_SECONDS,
    'output'               => true,
    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
    'output_tag'           => true,
    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
    // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
    'database'             => '',
    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!

    'use_cdn'              => true,
    // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

    //'compiler'             => true,

    // HINTS
    'hints'                => array(
        'icon'          => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color'    => 'lightgray',
        'icon_size'     => 'normal',
        'tip_style'     => array(
            'color'   => 'light',
            'shadow'  => true,
            'rounded' => false,
            'style'   => '',
        ),
        'tip_position'  => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect'    => array(
            'show' => array(
                'effect'   => 'fade',
                'duration' => '200',
                'event'    => 'mouseover',
            ),
            'hide' => array(
                'effect'   => 'fade',
                'duration' => '500',
                'event'    => 'click mouseleave',
            ),
        ),
    )
);

// ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
/*
$args['admin_bar_links'][] = array(
    'id'    => 'redux-docs',
    'href'  => 'http://docs.reduxframework.com/',
    'title' => __( 'Documentation', 'highlighter' ),
);

$args['admin_bar_links'][] = array(
    //'id'    => 'redux-support',
    'href'  => 'https://github.com/ReduxFramework/redux-framework/issues',
    'title' => __( 'Support', 'highlighter' ),
);

$args['admin_bar_links'][] = array(
    'id'    => 'redux-extensions',
    'href'  => 'reduxframework.com/extensions',
    'title' => __( 'Extensions', 'highlighter' ),
);
*/

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
/*
$args['share_icons'][] = array(
    'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
    'title' => 'Visit us on GitHub',
    'icon'  => 'el el-github'
    //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
);
*/

// Panel Intro text -> before the form
/*
if ( ! isset( $args['global_variable'] ) || $args['global_variable'] !== false ) {
    if ( ! empty( $args['global_variable'] ) ) {
        $v = $args['global_variable'];
    } else {
        $v = str_replace( '-', '_', $args['opt_name'] );
    }
    $args['intro_text'] = sprintf( __( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'highlighter' ), $v );
} else { */
    $args['intro_text'] = __( 'Documentation available in the plugin docs folder. Support located here: <a href="http://industrialthemes.ticksy.com" target="_blank">industrialthemes.ticksy.com</a>', 'highlighter' );
/*}

// Add content after the form.
$args['footer_text'] = __( '<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'highlighter' );
*/

Redux::setArgs( $opt_name, $args );

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */

/*
$tabs = array(
    array(
        'id'      => 'redux-help-tab-1',
        'title'   => __( 'Theme Information 1', 'highlighter' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'highlighter' )
    ),
    array(
        'id'      => 'redux-help-tab-2',
        'title'   => __( 'Theme Information 2', 'highlighter' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'highlighter' )
    )
);
Redux::setHelpTab( $opt_name, $tabs );

// Set the help sidebar
$content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'highlighter' );
Redux::setHelpSidebar( $opt_name, $content );
*/

/*
 * <--- END HELP TABS
 */


/*
 *
 * ---> START SECTIONS
 *
 */

/*

    As of Redux 3.5+, there is an extensive API. This API can be used in a mix/match mode allowing for


 */

// -> START Functionality
Redux::setSection( $opt_name, array(
    'title'      => __( 'Functionality', 'highlighter' ),
    'id'         => 'general-settings',
    'desc'		 => __( 'Main settings that apply to the functionality of the highlighter.', 'highlighter' ),
    'fields'     => array(
        array(
            'id'       => 'highlighter_enable',
            'type'     => 'button_set',
            'title'    => __( 'Enable Highlighter', 'highlighter' ),
            'subtitle' => __( 'The highlighter will be available on the selected pages', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
                'archive' => 'Archives',
                'home' => 'Home',
                'blog' => 'Blog',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'highlighter_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Custom Post Types', 'highlighter' ),
            'subtitle' => __( 'You can allow highlighting on custom post types, too', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'highlighter_cpts_manual',
            'type'     => 'text',
            'title'    => __( 'Custom Post Types', 'highlighter' ),
            'subtitle' => __( 'Manually enter the slug of your custom post types if they were not detected above, in a comma-separated list.', 'highlighter' ),
            'desc'     => __( 'Example: "events, game_reviews, another_cpt_slug, as_many_as_you_want" (do not include the quotes)', 'highlighter' ),
            'default'  => '',
        ),
        array(
            'id'       => 'category_disable',
            'type'     => 'select',
            'multi'    => true,
            'title'    => __( 'Category Disable', 'highlighter' ),
            'subtitle' => __( 'Disable the highlighter from working for selected categories.', 'highlighter' ),
            'desc'     => __( 'You can also disable the highlighter on a per-post and per-page basis by editing the post/page directly.', 'highlighter' ),
            'data'     => 'categories',
            'default'  => array()
        ),
        array(
            'id'       => 'viewing_enabled',
            'type'     => 'switch',
            'title'    => __( 'Highlight Viewing', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want users to be able to click existing highlights to view them in a docked panel.', 'highlighter' ),
            'default'  => true
        ),
        array(
            'id'       => 'comments_enabled',
            'type'     => 'switch',
            'title'    => __( 'Highlighter Notes', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want users to be able to add notes with their highlights.', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'       => 'comments_view',
            'type'     => 'button_set',
            'title'    => __( 'Highlighter Note View', 'highlighter' ),
            'subtitle' => __( 'View notes in a docked panel, or scroll the user down to the comments section to view.', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'dock' => __( 'Docked Panel', 'highlighter' ),
                'scroll' => __( 'Scroll to Comments', 'highlighter' ),
            ),
            'default'  => 'dock',
        ),
        array(
            'id'       => 'highlight_display',
            'type'     => 'button_set',
            'title'    => __( 'Highlight Display', 'highlighter' ),
            'subtitle' => __( 'Choose which highlights are visible to users.', 'highlighter' ),
            'desc' => __( 'If you select Top Only you need to also make sure Top Highlight is turned on in the Label Display option in order to see the top highlight.', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'all' => __( 'All', 'highlighter' ),
                'yours' => __( 'Yours Only', 'highlighter' ),
                'top' => __( 'Top Only', 'highlighter' ),
            ),
            'default'  => 'all',
        ),
        array(
            'id'       => 'label_display',
            'type'     => 'button_set',
            'title'    => __( 'Label Display', 'highlighter' ),
            'subtitle' => __( 'Choose which highlight labels are visible to users.', 'highlighter' ),
            'desc' => __( 'If you completely disable these labels, turn off Highlight Viewing and Highlighter Notes, and set Highlight Display to Yours Only, you will essentially switch your site into private mode, meaning each user will only ever see their own highlights.', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'yours' => __( 'Your Highlights', 'highlighter' ),
                'notes' => __( 'Note Counts', 'highlighter' ),
                'top' => __( 'Top Highlight', 'highlighter' ),
            ),
            'default'  => array('yours', 'notes', 'top'),
        ),
    	array(
            'id'       => 'login_type',
            'type'     => 'button_set',
            'title'    => __( 'Login Type', 'highlighter' ),
            'subtitle' => __( 'Users are required to be logged in to use the highlighter.', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'redirect' => __( 'Standard', 'highlighter' ),
                'ajax' => __( 'Ajax', 'highlighter' )
            ),
            'default'  => 'ajax'
        ),
        array(
            'id'       => 'twitter_enabled',
            'type'     => 'switch',
            'title'    => __( 'Twitter', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want users to be able to Tweet selected text.', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'       => 'twitter_highlights',
            'type'     => 'switch',
            'title'    => __( 'Twitter Highlights', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want text to become highlighted after a user Tweets it.', 'highlighter' ),
            'default'  => true,
            'required' => array('twitter_enabled', 'equals', '1')
        ),
        array(
            'id'       => 'facebook_enabled',
            'type'     => 'switch',
            'title'    => __( 'Facebook', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want users to be able to post selected text to Facebook.', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'       => 'facebook_highlights',
            'type'     => 'switch',
            'title'    => __( 'Facebook Highlights', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want text to become highlighted after a user posts it to Facebook.', 'highlighter' ),
            'default'  => true,
            'required' => array('facebook_enabled', 'equals', '1')
        ),
    )
) );

// -> START Style
Redux::setSection( $opt_name, array(
    'title'      => __( 'Style', 'highlighter' ),
    'id'         => 'style-settings',
    'desc'       => __( 'The look, feel, and placement of highlighter UI components.', 'highlighter' ),
    'icon'  => 'el el-tint',
    'fields'     => array(
        array(
            'id'       => 'highlight_color',
            'type'     => 'select',
            'title'    => __( 'Highlight Color', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                '#5cffa0' => __('Green', 'highlighter' ),
                '#f8ff61' => __('Yellow', 'highlighter' ),
                '#64fffb' => __('Blue', 'highlighter' ),
                '#daa1ff' => __('Purple', 'highlighter' ),
                '#ff99e2' => __('Pink', 'highlighter' ),
                '#ffc07b' => __('Orange', 'highlighter' ),
                '#ffb6b6' => __('Red', 'highlighter' ),
                '#c0c0c0' => __('Gray', 'highlighter' ),
                'custom' => __('Custom', 'highlighter' ),
            ),
            'default'  => '#5cffa0'
        ),
        array(
            'id'       => 'highlight_color_custom',
            'type'     => 'color',
            'title'    => __( 'Custom Color', 'highlighter' ),
            'subtitle' => __( 'The selected color will have a 70% transparency applied when in use.', 'highlighter'),
            'default'  => '#5cffa0',
            'transparent' => false,
            'required' => array('highlight_color', 'equals', 'custom' )
        ),
        array(
            'id'       => 'label_placement',
            'type'     => 'radio',
            'title'    => __( 'Label Placement', 'wtr' ),
            'subtitle' => __( 'Labels display next to your post content to show top highlight, your own highlights, and where notes have been added.', 'highlighter'),
            'options'  => array(
                'right' => 'Right of Content',
                'left' => 'Left of Content'
            ),
            'default'  => 'right'
        ),
        array(
            'id'       => 'label_compact',
            'type'     => 'switch',
            'title'    => __( 'Compact Labels', 'highlighter' ),
            'subtitle' => __( 'Turn this on if you want the icon-only mobile view to be the default for desktop too.', 'highlighter' ),
            'default'  => false,
        ),
        array(
            'id'            => 'label_offset',
            'type'          => 'slider',
            'title'         => __( 'Label Offset', 'highlighter' ),
            'subtitle'      => __( 'Extra padding to the right or left of the label, depending on the placement setting.', 'highlighter' ),
            'default'       => 40,
            'min'           => 0,
            'step'          => 1,
            'max'           => 100,
            'display_value' => 'text',
            'required'      => array('label_placement', '!=', 'hidden' )
        ),
        array(
            'id'       => 'label_zindex',
            'type'     => 'spinner', 
            'title'    => __('Label Z-Index', 'highlighter'),
            'subtitle' => __('Fine tune the z-index of highlighter labels so they do not show above or beneath certain elements.','highlighter'),
            'default'  => '97',
            'min'      => '-1',
            'step'     => '1',
            'max'      => '9999999',
        ),
        array(
            'id'       => 'highlighter_css',
            'type'     => 'ace_editor',
            'title'    => __( 'Custom CSS', 'highlighter' ),
            'subtitle' => __( 'You can overwrite the plugin css with your own custom css.', 'highlighter' ),
            'mode'     => 'css',
            'theme'    => 'monokai',
            'default'  => "
/* this selector applies to all highlighted text spans */
.highlighted-text { 
    
    text-transform:none; /* just an example */

    /* more attributes here */
    /* ... */

}

/* more selectors here */
/* ... */

"
        ),

    )
) );


// -> START Stats Settings
Redux::setSection( $opt_name, array(
    'title' => __( 'Stats', 'highlighter' ),
    'id'    => 'stats-settings',
    'desc'  => __( 'Highlighter statistics collecting engine settings. If enabled, a WordPress cron job runs according to your desired schedule and scans each post for highlighter statistics, storing them in post custom fields. It also keeps track of site totals and stores them in the options table.', 'highlighter' ),
    'icon'  => 'el el-graph',
    'fields' => array(
        array(
            'id'       => 'stats_enabled',
            'type'     => 'switch',
            'title'    => __( 'Highlighter Stats', 'highlighter' ),
            'subtitle' => __( 'Enable highlighter stats collecting engine', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'       => 'stats_schedule',
            'type'     => 'select',
            'title'    => __( 'Highlighter Stats Schedule', 'highlighter' ),
            'subtitle' => __( 'How often the cron job should run to calculate highlighter stats', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'everyfiveminutes' => __('Every 5 Minutes', 'highlighter' ),
                'everyfifteenminutes' => __('Every 15 Minutes', 'highlighter' ),
                'everythirtyminutes' => __('Every 30 Minutes', 'highlighter' ),
                'everyfortyfiveminutes' => __('Every 45 Minutes', 'highlighter' ),
                'hourly' => __('Hourly', 'highlighter' ),
                'twicedaily' => __('Twice A Day', 'highlighter' ),
                'daily' => __('Daily', 'highlighter' ),
                'everytwodays' => __('Every 2 Days', 'highlighter' ),
                'everythreedays' => __('Every 3 Days', 'highlighter' ),
                'everyfourdays' => __('Every 4 Days', 'highlighter' ),
                'weekly' => __('Weekly', 'highlighter' ),
            ),
            'default'  => 'daily',
            'required' => array('stats_enabled', 'equals', '1' )
        ),
        array(
            'id'       => 'stats_limit',
            'type'     => 'button_set',
            'title'    => __( 'Highlight Scanner', 'highlighter' ),
            'subtitle' => __( 'You can limit the number of posts that are scanned each time the scheduled cron job runs.', 'highlighter' ),
            'desc'     => __( "Useful if you have lots of posts and you don't care about updating really old ones, helps conserve server resources. However, keep in mind that if you limit the scanner your total site stats will not include all of your posts.", 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'limit' => __( 'Limit the Scanner', 'highlighter' ),
                'all' => __( 'Scan All Posts', 'highlighter' )
            ),
            'default'  => 'limit',
            'required' => array('stats_enabled', 'equals', '1' )
        ),
        array(
            'id'            => 'stats_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'subtitle'      => __( 'Starting with the most recent post, how far back should the scanner go to find highlights?', 'highlighter' ),
            'default'       => 100,
            'min'           => 0,
            'step'          => 1,
            'max'           => 999,
            'display_value' => 'text',
            'required'      => array('stats_limit', 'equals', 'limit' )
        ),
        array(
            'id'       => 'stats_placement',
            'type'     => 'radio',
            'title'    => __( 'Highlighter Stats Placement', 'wtr' ),
            'subtitle' => __( 'You can enable highlighter stats on every post without having to use a shortcode.', 'highlighter'),
            'desc' => __( 'The shortcode will still work even if you select a placement.', 'highlighter'),
            'options'  => array(
                'before_title' => 'Before Title',
                'after_title' => 'After Title',
                'before_content' => 'Before Content',
                'after_content' => 'After Content',
                'none' => 'Shortcode Only',
            ),
            'default'  => 'after_content',
            'required' => array('stats_enabled', 'equals', '1' )
        ),

    )
) );

// -> START Shortcodes
Redux::setSection( $opt_name, array(
    'title' => __( 'Shortcodes', 'highlighter' ),
    'id'    => 'shortcode-settings',
    'desc'  => __( 'This is where you adjust the default settings for the various shortcodes provided with Highlighter. Highlighter Stats must be enabled for shortcodes to work. Use the options on this page to set the default settings for each shortcode. Settings can be overridden by specifying an attribute when adding the shortcode. In other words, using shortcodes and ommitting attributes will result in the settings selected on this page.', 'highlighter' ),
    'icon'  => 'el el-chevron-right',
    'fields' => array(
        array(
            'id'       => 'enable_widget_shortcodes',
            'type'     => 'switch',
            'title'    => __( 'Widget Shortcodes', 'highlighter' ),
            'subtitle' => __( 'Enable shortcodes to work in widgets. By default WordPress does not render shortcodes in widgets.', 'highlighter' ),
            'default'  => true,
        ),
        array(
           'id' => 'stats_start',
           'type' => 'section',
           'title' => __('[highlighter-stats]'),
           'subtitle' => __('Default settings for the stats shortcode, which displays highlighter statistics for a single post or for your entire site. Example usage [highlighter-stats context="site"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ),
        ),
        array(
            'id'       => 'stats_defaults',
            'type'     => 'checkbox',
            'title'    => __( 'Highlight Stats Defaults', 'highlighter' ),
            'subtitle' => __( 'Show these stats by default unless overridden.', 'highlighter' ),
            'desc'     => __( "If you don't use any shortcode parameters these are the options that will be shown by default wherever this shortcode is used.", 'highlighter' ),
            'options'  => array(
                'total_highlights' => __( 'Highlights', 'highlighter' ),
                'total_highlighters' => __( 'Highlighters', 'highlighter' ),
                'total_commenters' => __( 'Commenters', 'highlighter' ),
                'top_highlight' => __( 'Top Highlight', 'highlighter' ),
                'top_comment' => __( 'Top Comment', 'highlighter' ),
                'top_highlight_text' => __( 'Top Highlight Text', 'highlighter' ),
                'top_comment_text' => __( 'Top Comment Text', 'highlighter' ),
            ),
            'default'  => array(
                'total_highlights' => 1,
                'total_highlighters' => 0,
                'total_commenters' => 0,
                'top_highlight' => 0,
                'top_comment' => 0,
                'top_highlight_text' => 0,
                'top_comment_text' => 0
            ),
            'required' => array('stats_enabled', 'equals', '1' )
        ),
        array(
            'id'       => 'stats_context',
            'type'     => 'button_set',
            'title'    => __( 'Context', 'highlighter' ),
            'subtitle' => __( 'The scope of the stats that should be used by default, which can be overridden.', 'highlighter' ),
            'multi'    => false,
            'options'  => array(
                'single' => 'Single Post',
                'site' => 'Entire Site',
            ),
            'default'  => 'single'
        ),
        array(
            'id'       => 'stats_toggled',
            'type'     => 'switch',
            'title'    => __( 'Toggled', 'highlighter' ),
            'subtitle' => __( 'Turn this on to enable clicking of the stats title to show/hide the stats', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'stats_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'most_noted_start',
           'type' => 'section',
           'title' => __('[highlighter-most-noted]'),
           'subtitle' => __('Default settings for the most noted selection shortcode, which displays the single most noted highlight across your site. Example usage [highlighter-most-noted title="write title here" linked="true"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ),
        ),
        array(
            'id'       => 'most_noted_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'All time most noted selection', 'highlighter' )
        ),
        array(
            'id'       => 'most_noted_linked',
            'type'     => 'switch',
            'title'    => __( 'Linked', 'highlighter' ),
            'subtitle' => __( 'Clicking the most noted selection will link to the containing post', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'most_noted_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'most_highlighted_start',
           'type' => 'section',
           'title' => __('[highlighter-most-highlighted]', 'highlighter'),
           'subtitle' => __('Default settings for the most highlighted selection shortcode, which displays the single most highlighted selection across your site. Example usage [highlighter-most-highlighted title="write title here" linked="true"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ), 
        ),
        array(
            'id'       => 'most_highlighted_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'All time most highlighted selection', 'highlighter' )
        ),
        array(
            'id'       => 'most_highlighted_linked',
            'type'     => 'switch',
            'title'    => __( 'Linked', 'highlighter' ),
            'subtitle' => __( 'Clicking the most highlighted selection will link to the containing post', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'most_highlighted_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'inked_start',
           'type' => 'section',
           'title' => __('[highlighter-inked]', 'highlighter'),
           'subtitle' => __('Default settings for the inked shortcode, which displays a list of the most highlighted posts (posts containing the greatest number of highlights). Example usage [highlighter-inked types="post,page,etc..." title="write title here" num="10"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ),
        ),
        array(
            'id'       => 'inked_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'Inked Posts (most highlighted)', 'highlighter' )
        ),
        array(
            'id'            => 'inked_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'default'       => 10,
            'min'           => 0,
            'step'          => 1,
            'max'           => 50,
            'display_value' => 'text',
        ),
        array(
            'id'       => 'inked_enable',
            'type'     => 'button_set',
            'title'    => __( 'Include', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'inked_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Also Include', 'highlighter' ),
            'subtitle' => __( 'You can also include custom post types in this shortcode.', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'inked_counts',
            'type'     => 'switch',
            'title'    => __( 'Show Counts', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'inked_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'noteworthy_start',
           'type' => 'section',
           'title' => __('[highlighter-noteworthy]', 'highlighter'),
           'subtitle' => __('Default settings for the noteworthy shortcode, which displays a list of the most noted posts (posts with the greatest number of notes). Example usage [highlighter-noteworthy types="post,page,etc..." title="write title here" num="10"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ), 
        ),
        array(
            'id'       => 'noteworthy_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'Noteworthy Posts (most noted)', 'highlighter' )
        ),
        array(
            'id'            => 'noteworthy_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'default'       => 10,
            'min'           => 0,
            'step'          => 1,
            'max'           => 50,
            'display_value' => 'text',
        ),
        array(
            'id'       => 'noteworthy_enable',
            'type'     => 'button_set',
            'title'    => __( 'Include', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'noteworthy_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Also Include', 'highlighter' ),
            'subtitle' => __( 'You can also include custom post types in this shortcode.', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'noteworthy_counts',
            'type'     => 'switch',
            'title'    => __( 'Show Counts', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'noteworthy_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'trending_start',
           'type' => 'section',
           'title' => __('[highlighter-trending]', 'highlighter'),
           'subtitle' => __('Default settings for the trending shortcode, which displays a list of posts with the most individual users highlighting (not necessarily the most highlights since multiple users can highlight the same selection). Example usage [highlighter-trending types="post,page,etc..." title="write title here" num="10"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ), 
        ),
        array(
            'id'       => 'trending_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'Trending Posts (most highlighters)', 'highlighter' )
        ),
        array(
            'id'            => 'trending_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'default'       => 10,
            'min'           => 0,
            'step'          => 1,
            'max'           => 50,
            'display_value' => 'text',
        ),
        array(
            'id'       => 'trending_enable',
            'type'     => 'button_set',
            'title'    => __( 'Include', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'trending_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Also Include', 'highlighter' ),
            'subtitle' => __( 'You can also include custom post types in this shortcode.', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'trending_counts',
            'type'     => 'switch',
            'title'    => __( 'Show Counts', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'trending_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'bold_start',
           'type' => 'section',
           'title' => __('[highlighter-bold]', 'highlighter'),
           'subtitle' => __('Default settings for the bold shortcode, which displays a list of posts with the single most noted highlight. Example usage [highlighter-bold types="post,page,etc..." title="write title here" num="10"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ), 
        ),
        array(
            'id'       => 'bold_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'Bold Posts (top note)', 'highlighter' )
        ),
        array(
            'id'            => 'bold_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'default'       => 10,
            'min'           => 0,
            'step'          => 1,
            'max'           => 50,
            'display_value' => 'text',
        ),
        array(
            'id'       => 'bold_enable',
            'type'     => 'button_set',
            'title'    => __( 'Include', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'bold_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Also Include', 'highlighter' ),
            'subtitle' => __( 'You can also include custom post types in this shortcode.', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'bold_counts',
            'type'     => 'switch',
            'title'    => __( 'Show Counts', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'bold_end',
            'type'   => 'section',
            'indent' => false,
        ),
        
        array(
           'id' => 'memorable_start',
           'type' => 'section',
           'title' => __('[highlighter-memorable]', 'highlighter'),
           'subtitle' => __('Default settings for the memorable shortcode, which displays a list of posts with the single most highlighted selection. Example usage [highlighter-memorable types="post,page,etc..." title="write title here" num="10"]', 'highlighter'),
           'indent' => true,
           'required' => array('stats_enabled', 'equals', '1' ), 
        ),
        array(
            'id'       => 'memorable_title',
            'type'     => 'text',
            'title'    => __( 'Title', 'highlighter' ),
            'default'  => __( 'Memorable Posts (top highlight)', 'highlighter' )
        ),
        array(
            'id'            => 'memorable_num',
            'type'          => 'slider',
            'title'         => __( 'Number of Posts', 'highlighter' ),
            'default'       => 10,
            'min'           => 0,
            'step'          => 1,
            'max'           => 50,
            'display_value' => 'text',
        ),
        array(
            'id'       => 'memorable_enable',
            'type'     => 'button_set',
            'title'    => __( 'Include', 'highlighter' ),
            'multi'    => true,
            'options'  => array(
                'post' => 'Posts',
                'page' => 'Pages',
            ),
            'default'  => array('post')
        ),
        array(
            'id'       => 'memorable_cpts',
            'type'     => 'button_set',
            'multi'    => true,
            'title'    => __( 'Also Include', 'highlighter' ),
            'subtitle' => __( 'You can also include custom post types in this shortcode.', 'highlighter' ),
            'desc' => __( 'If there are any custom post types detected they will display here (these are generally added by your theme and/or plugins).', 'highlighter' ),
            'data'     => 'post_types',
            'args'     => array(
                            'public' => true, 
                            '_builtin' => false
                        ),
        ),
        array(
            'id'       => 'memorable_counts',
            'type'     => 'switch',
            'title'    => __( 'Show Counts', 'highlighter' ),
            'default'  => true,
        ),
        array(
            'id'     => 'memorable_end',
            'type'   => 'section',
            'indent' => false,
        ),
        array(
           'id' => 'view_start',
           'type' => 'section',
           'title' => __('[highlighter-view]', 'highlighter'),
           'subtitle' => __('There are no default options to change for this shortcode. Use it to display a list of all highlights for the current user, a specified user by ID, or all users (a list of all site highlights). Example usage [highlighter-view userid="all"]. If you are specifying a user ID manually, make sure to use the actual numeric unique ID for the user, not the username.', 'highlighter'),
           'indent' => true,
        ),
        
        
        array(
            'id'     => 'view_end',
            'type'   => 'section',
            'indent' => false,
        ),


    )
) );


// -> START Messages Settings
Redux::setSection( $opt_name, array(
    'title' => __( 'Messages', 'highlighter' ),
    'id'    => 'messages-settings',
    'desc'  => __( 'You can customize the various messages that are displayed to the user on the front-end right from this panel instead of having to dig into the code. There are defaults in place if you do not fill these options out, so you only need to tinker with these settings if you want to overwrite the defaults.', 'highlighter' ),
    'icon'  => 'el el-comment',
    'fields' => array(
        array(
            'id'       => 'msg_add_highlight',
            'type'     => 'text',
            'title'    => __( 'Add Highlight', 'highlighter' ),
            'subtitle'  => __( 'Confirmation message that appears when a user clicks add highlight on an existing highlight.', 'highlighter' ),
            'default' => __( 'Highlight this selection?', 'highlighter')
        ),
        array(
            'id'       => 'msg_remove_highlight',
            'type'     => 'text',
            'title'    => __( 'Remove Highlight', 'highlighter' ),
            'subtitle'  => __( 'Confirmation message that appears when a user clicks the remove highlight button.', 'highlighter' ),
            'default' => __( 'Remove your highlight?', 'highlighter')
        ),
        array(
            'id'       => 'msg_add_new_comment',
            'type'     => 'text',
            'title'    => __( 'Add New Comment', 'highlighter' ),
            'subtitle'  => __( 'Confirmation message that appears when a user clicks the comment button on a new selection.', 'highlighter' ),
            'default' => __( 'Comment on this selection?', 'highlighter')
        ),
        array(
            'id'       => 'msg_add_comment',
            'type'     => 'text',
            'title'    => __( 'Add Comment', 'highlighter' ),
            'subtitle'  => __( 'Confirmation message that appears when a user clicks the comment button on an existing highlight.', 'highlighter' ),
            'default' => __( 'Comment on this highlight?', 'highlighter')
        ),
        array(
            'id'       => 'msg_highlighted',
            'type'     => 'text',
            'title'    => __( 'You Highlighted', 'highlighter' ),
            'subtitle'  => __( "Label that appears next to the user's own highlights", 'highlighter' ),
            'default' => __( 'You highlighted', 'highlighter')
        ),
        array(
            'id'       => 'msg_commented',
            'type'     => 'text',
            'title'    => __( 'You Commented', 'highlighter' ),
            'subtitle'  => __( "Label that appears next to the user's own notes (inline comments)", 'highlighter' ),
            'default' => __( 'You highlighted and commented', 'highlighter')
        ),
        array(
            'id'       => 'msg_facebook_confirm',
            'type'     => 'text',
            'title'    => __( 'Post to Facebook', 'highlighter' ),
            'subtitle'  => __( "Confirmation message that appears when a user clicks the post to facebook button on a highlighted selection.", 'highlighter' ),
            'default' => __( 'Post this to Facebook?', 'highlighter')
        ),
        array(
            'id'       => 'msg_twitter_confirm',
            'type'     => 'text',
            'title'    => __( 'Tweet This', 'highlighter' ),
            'subtitle'  => __( "Confirmation message that appears when a user clicks the Twitter button on a highlighted selection.", 'highlighter' ),
            'default' => __( 'Tweet this?', 'highlighter')
        ),
        array(
            'id'       => 'msg_redirect_to_post',
            'type'     => 'text',
            'title'    => __( 'Redirect To Post', 'highlighter' ),
            'subtitle'  => __( "Confirmation message that appears when a user is not on a single post or page (i.e. on the homepage or archive listing) and clicks the comment button on a highlight.", 'highlighter' ),
            'default' => __( 'Go to this post?', 'highlighter')
        ),
        array(
            'id'       => 'custom_message',
            'type'     => 'textarea',
            'title'    => __( 'Custom Login Message', 'highlighter' ),
            'subtitle'  => __( 'Displays at the top of the login form and register forms', 'highlighter' ),
            'rows'     => 3,
            'required' => array( 'login_type', 'equals', 'ajax' )
        ),

    )
) );


// -> START Selectors Settings
Redux::setSection( $opt_name, array(
    'title' => __( 'Advanced', 'highlighter' ),
    'id'    => 'advanced-settings',
    'desc'  => __( "You probably don't need to touch any of these settings. If you notice any weirdness while using Highlighter on your site, refer to the plugin documentation and then have a look at these advanced settings in case you need to tweak something.", 'highlighter' ),
    'icon'  => 'el el-cogs',
    'fields' => array(
        
        array(
           'id' => 'selectors_start',
           'type' => 'section',
           'title' => __('Selectors', 'highlighter'),
           'subtitle' => __("Most themes use standard WordPress selectors (CSS classes), but some themes deviate from the standards. The default selectors are pre-populated below, but you can overwrite them. Make sure you include the selector type at the beginning (. for class, # for id). We recommend going with the defaults unless something isn't working right. This is mostly used for the built-in AJAX comment system, because Highlighter needs to know where to append the new comment indicator.", 'highlighter'),
           'indent' => true,
        ),
        array(
            'id'       => 'selector_comment_list',
            'type'     => 'text',
            'title'    => __( 'Comment List', 'highlighter' ),
            'subtitle'  => __( 'The container that holds the list of comments at the end of a post.', 'highlighter' ),
            'default' => __( '.comment-list', 'highlighter')
        ),
        array(
            'id'       => 'selector_respond',
            'type'     => 'text',
            'title'    => __( 'Respond', 'highlighter' ),
            'subtitle'  => __( 'The container that holds the respond form that comes after the comment list.', 'highlighter' ),
            'default' => __( '#respond', 'highlighter')
        ),
        array(
            'id'       => 'selector_cancel_reply',
            'type'     => 'text',
            'title'    => __( 'Cancel Reply', 'highlighter' ),
            'subtitle'  => __( 'The button that cancels and hides the comment reply.', 'highlighter' ),
            'default' => __( '#cancel-comment-reply-link', 'highlighter')
        ),
        array(
            'id'     => 'selectors_end',
            'type'   => 'section',
            'indent' => false,
        ),

        array(
           'id' => 'hooks_start',
           'type' => 'section',
           'title' => __('Hooks', 'highlighter'),
           'subtitle' => __("Sometimes themes add custom action hooks to default WordPress functions which can conflict with Highlighter, and obviously Highlighter needs to ignore them without affecting the rest of your theme. Use the options below to tell Highlighter which hooks to disregard. Unless you are a WordPress guru you probably won't have any idea what these are, but your theme author will. You can ask the theme author if the theme has any custom actions added for any of the hooks below, and then add them here.", 'highlighter'),
           'indent' => true,
        ),
        array(
            'id'       => 'hook_comment_form_top',
            'type'     => 'text',
            'title'    => __( 'comment_form_top', 'highlighter' ),
            'subtitle'  => __( 'Enter the name of the custom hook into the text field.', 'highlighter' ),
            'desc' => __( 'Example: my_comment_form_top_action', 'highlighter' ),
        ),
        array(
            'id'       => 'hook_comment_form',
            'type'     => 'text',
            'title'    => __( 'comment_form', 'highlighter' ),
        ),

        array(
            'id'     => 'hooks_end',
            'type'   => 'section',
            'indent' => false,
        ),
    )
) );



/*
 * <--- END SECTIONS
 */


?>