<?php

/**
 *
 * Dashboard Settings
 * @package Dashboard
 *
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if ( !is_admin() ) {
	return;
}

function sydney_dashboard_settings()
{

	$settings = array();

	//
	// General.
	//
	$settings['menu_slug']           = 'sydney-dashboard';
	$settings['starter_plugin_slug'] = 'athemes-starter-sites';
	$settings['starter_plugin_path'] = 'athemes-starter-sites/athemes-starter-sites.php';
	$settings['has_pro']			 = defined( 'SYDNEY_PRO_VERSION' ) ? true : false;
	$settings['website_link']        = 'https://athemes.com/';

	//
	// Hero.
	//
	$settings['hero_title'] = esc_html__('Welcome to Sydney', 'sydney');
	$settings['hero_desc']  = esc_html__('Sydney is now installed and ready to go. To help you with the next step, we’ve gathered together on this page all the resources you might need. We hope you enjoy using Sydney.', 'sydney');
	$settings['hero_image'] = get_template_directory_uri() . '/inc/dashboard/assets/images/welcome-banner@2x.png';

	//
	// Documentation.
	//
	$settings['documentation_link'] = 'https://docs.athemes.com/documentation/sydney/';

	//
	// Upgrade to Pro.
	//
	$settings['upgrade_pro'] = 'https://athemes.com/sydney-upgrade?utm_source=theme_info&utm_medium=link&utm_campaign=Sydney';

	//
	// Promo.
	//
	$settings['promo_title']  = esc_html__('Upgrade to Pro', 'sydney');
	$settings['promo_desc']   = esc_html__('Take Sydney to a whole other level by upgrading to the Pro version.', 'sydney');
	$settings['promo_button'] = esc_html__('Discover Sydney Pro', 'sydney');
	$settings['promo_link']   = 'https://athemes.com/sydney-upgrade?utm_source=theme_info&utm_medium=link&utm_campaign=Sydney';

	//
	// Review.
	//
	$settings['review_link']       = 'https://wordpress.org/support/theme/sydney/reviews/';
	$settings['suggest_idea_link'] = 'https://athemes.com/feature-request/';

	//
	// Knowledge Base.
	//
	$settings['knowledge_base_link'] = 'https://docs.athemes.com/documentation/sydney/';

	//
	// Support.
	//
	$settings['support_link']     = 'https://athemes.com/support/';

	$settings['support_pro_link'] = 'https://athemes.com/sydney-upgrade?utm_source=theme_support&utm_medium=button&utm_campaign=Sydney';

	//
	// Community.
	//
	$settings['community_link'] = 'https://www.facebook.com/groups/athemes/';

	//
	// Tutorial.
	//
	$settings['tutorial_link'] = 'https://athemes.com/video-tutorials/sydney/';

	//
	// Changelog.
	//
	$theme = wp_get_theme();
	$settings['changelog_version'] = $theme->version;
	$settings['changelog_link']    = 'https://athemes.com/changelog/sydney/';

	//
	// Social Links.
	//
	$settings['facebook_link'] = 'https://www.facebook.com/groups/athemes/';
	$settings['twitter_link']  = 'https://twitter.com/athemesdotcom';
	$settings['youtube_link']  = 'https://www.youtube.com/@Athemes';

	//
	// Tabs.
	//
	$settings['tabs']  = array(
		'home'           => esc_html__('Home', 'sydney'),
		'starter-sites'  => esc_html__('Starter Sites', 'sydney'),
		'settings'       => esc_html__('License', 'sydney'),
		'free-vs-pro'    => esc_html__('Free vs Pro', 'sydney')
	);

	if ( ( isset( $settings['has_pro'] ) && $settings['has_pro'] && Sydney_Modules::is_module_active( 'templates' ) ) || !$settings['has_pro'] ) {
		$settings['tabs'] = array_merge(
			array_slice( $settings['tabs'], 0, 2 ),
			array( 'builder' => esc_html__( 'Template Builder', 'sydney' ) ),
			array_slice( $settings['tabs'], 2 )
		);
	}
	

	//
	// Settings.
	//
	$settings['settings'] = array(
		'general'     => esc_html__('License', 'sydney'),
	);

	//
	// Notifications.
	//
	
	if ( isset( $settings['has_pro'] ) && $settings['has_pro'] ) {
		$theme_id = '4672';
	} else {
		$theme_id = '4671';
	}

	$notifications_response    = wp_remote_get( 'https://athemes.com/wp-json/wp/v2/notifications?theme=' . $theme_id . '&per_page=3' );
	$settings['notifications'] = ! is_wp_error( $notifications_response ) || wp_remote_retrieve_response_code( $notifications_response ) === 200 ? json_decode( wp_remote_retrieve_body( $notifications_response ) ) : false;
	$settings['notifications_tabs'] = false;

	//
	// Demos.
	//
	$ettings['demos'] = array();

	//
	// Plugins.
	//
	$settings['plugins'] = array();

	$settings['plugins'][] = array(
		'slug'   => 'athemes-blocks',
		'path'   => 'athemes-blocks/athemes-blocks.php',
		'icon'   => 'https://plugins.svn.wordpress.org/athemes-blocks/assets/icon-256x256.png',
		'banner' => 'https://plugins.svn.wordpress.org/athemes-blocks/assets/banner-772x250.png',
		'title'  => esc_html__('aThemes Blocks', 'sydney'),
		'desc'   => esc_html__('Extend the Gutenberg Block Editor with additional functionality.', 'sydney'),
	);

	$settings['plugins'][] = array(
		'slug'   => 'wpforms-lite',
		'path'   => 'wpforms-lite/wpforms.php',
		'icon'   => 'https://plugins.svn.wordpress.org/wpforms-lite/assets/icon-256x256.png',
		'banner' => 'https://plugins.svn.wordpress.org/wpforms-lite/assets/banner-772x250.png',
		'title'  => esc_html__('WPForms', 'sydney'),
		'desc'   => esc_html__('The best WordPress contact form plugin. Drag & Drop online form builder that helps you create beautiful contact forms + custom forms in minutes.', 'sydney'),
	);

	$settings['plugins'][] = array(
		'slug'   => 'leadin',
		'path'   => 'leadin/leadin.php',
		'icon'   => 'https://plugins.svn.wordpress.org/leadin/assets/icon-256x256.png',
		'banner' => 'https://plugins.svn.wordpress.org/leadin/assets/banner-772x250.png',
		'title'  => esc_html__('HubSpot', 'sydney'),
		'desc'   => esc_html__('HubSpot is a platform with all the tools and integrations you need for marketing, sales, and customer service.', 'sydney'),
	);

	//
	// Features.
	//
	$settings['features'] = array();

	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Site Title and Logo', 'sydney'),
		'desc'       => esc_html__('Set the title and upload logo.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[control]', 'blogname', admin_url('customize.php')),
	);

	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Typography', 'sydney'),
		'desc'       => esc_html__('Set the global font size, style and library.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[panel]', 'sydney_panel_typography', admin_url('customize.php'))
	);

	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Color Options', 'sydney'),
		'desc'       => esc_html__('Change the colors for various elements.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[section]', 'colors', admin_url('customize.php'))
	);

	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Header Options', 'sydney'),
		'desc'       => esc_html__('Customize the header options for your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[panel]', 'sydney_panel_header', admin_url('customize.php'))
	);
	
	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Buttons', 'sydney'),
		'desc'       => esc_html__('Customize the buttons in your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[section]', 'sydney_section_buttons', admin_url('customize.php'))
	);
	
	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Blog Options', 'sydney'),
		'desc'       => esc_html__('Customize the blog options for your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[panel]', 'sydney_panel_blog', admin_url('customize.php'))
	);
	
	$settings['features'][] = array(
		'type'       => 'free',
		'title'      => esc_html__('Footer Credits', 'sydney'),
		'desc'       => esc_html__('Customize the footer credits for your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[section]', 'sydney_section_footer_credits', admin_url('customize.php'))
	);
	//Start Pro Features

	$settings['features'][] = array(
		'category'   => 'header',
		'type'       => 'pro',
		'title'      => esc_html__('Top bar', 'sydney'),
		'desc'       => esc_html__('Customize the top bar of your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/pro-how-to-configure-the-top-bar/',
		'link_url'   => add_query_arg('autofocus[section]', 'sydney_contact_info', admin_url('customize.php'))
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'templates',
		'title'        => esc_html__('Template Builder', 'sydney'),
		'type'        => 'pro',
		//'link_url'    => add_query_arg('post_type', 'athemes_hf', admin_url('edit.php')),
		//'link_label'  => esc_html__('Build templates', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/435-templates-system-overview',
		'desc'        => __('Build headers, footers etc. with Elementor.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'quick-links',
		'title'        => esc_html__('Quick Links Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_quicklinks', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/443-pro-quick-links-module',
		'desc'        => __('Floating quick links bar (contact, social etc.)', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'modal',
		'title'        => esc_html__('Modal', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_modal_popup', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/modal-in-sydney-pro/',
		'desc'        => __('Modal with custom content', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'custom-fonts',
		'title'        => esc_html__('Custom Fonts', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_typography_general', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/pro-custom-fonts-in-sydney-pro/',
		'desc'        => __('Add custom fonts to your site.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'offcanvas-content',
		'title'        => esc_html__('Offcanvas Content', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_offcanvas_content', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/offcanvas-content-module/',
		'desc'        => __('Offcanvas sidebars, Elementor templates or custom content', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'browser-tools',
		'title'        => esc_html__('Browser Tools', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_browser_tools', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/browser-tools/',
		'desc'        => __('Scrollbar, mobile theme color, prevent text copy', 'sydney'),
	);	
	$settings['features'][] = array(
		'category'    => 'header',
		'module'      => 'ext-header',
		'title'        => esc_html__('Extended Header Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[panel]', 'sydney_panel_header', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/436-pro-extended-header-module',
		'desc'        => __('New features for your header area.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'header',
		'module'      => 'mega-menu',
		'title'        => esc_html__('Mega Menu', 'sydney'),
		'type'        => 'pro',
		//'link_url' 		=> admin_url( '/customize.php?autofocus[section]=sydney_mega_menu' ),
		//'link_label'	=> esc_html__( 'Customize', 'sydney' ),
		'docs_link'   => 'https://docs.athemes.com/article/how-to-build-a-mega-menu-with-sydney-pro/',
		'desc'        => __('Mega menu with Elementor support', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'footer',
		'module'      => 'ext-footer',
		'title'        => esc_html__('Extended Footer Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[panel]', 'sydney_panel_footer', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/442-pro-extended-footer-module',
		'desc'        => __('Extra features for your footer', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'blog',
		'module'      => 'ext-blog',
		'title'        => esc_html__('Extended Blog Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[panel]', 'sydney_panel_blog', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/438-pro-extended-blog-module',
		'desc'        => __('Extra features for your blog.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'blog',
		'module'      => 'page-headers',
		'title'        => esc_html__('Page Headers', 'sydney'),
		'type'        => 'pro',
		//'link_url' 		=> admin_url( '/customize.php?autofocus[section]=sydney_breadcrumbs' ),
		//'link_label'	=> esc_html__( 'Customize', 'sydney' ),
		'docs_link'   => 'https://docs.athemes.com/article/how-to-customize-page-headers-in-sydney-pro',
		'desc'        => __('Page Header options for posts, pages, archives etc.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'blog',
		'module'      => 'breadcrumbs',
		'title'        => esc_html__('Breadcrumbs Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_breadcrumbs', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/440-pro-breadcrumbs',
		'desc'        => __('Breadcrumbs functionality.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'blog',
		'module'      => 'sidebar',
		'title'        => esc_html__('Sidebar Module', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_sidebar', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/pro-sidebar-module',
		'desc'        => __('Extended sidebar options.', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'integrations',
		'module'      => 'ext-woocommerce',
		'title'        => esc_html__('Extended WooCommerce', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[panel]', 'woocommerce', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/444-pro-extended-woocommerce-module',
		'desc'        => __('Extra features for WooCommerce', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'integrations',
		'module'      => 'elementor-tools',
		'title'        => esc_html__('Elementor Tools', 'sydney'),
		'type'        => 'pro',
		//'link_url' 			=> admin_url( '/customize.php?autofocus[section]=sydney_section_modal_popup' ),
		//'link_label'	=> esc_html__( 'Customize', 'sydney' ),
		'docs_link'   => 'https://docs.athemes.com/article/elementor-toolbox-module/',
		'desc'        => __('Custom CSS and other tools for Elementor', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'integrations',
		'module'      => 'live-chat',
		'title'        => esc_html__('Live Chat (WhatsApp)', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_live_chat', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/live-chat-in-sydney/',
		'desc'        => __('Live chat floating icon', 'sydney'),
	);
	$settings['features'][] = array(
		'category'    => 'general',
		'module'      => 'html-designer',
		'title'        => esc_html__('Forms &amp; HTML Designer', 'sydney'),
		'type'        => 'pro',
		'link_url'    => add_query_arg('autofocus[section]', 'sydney_section_html_designer', admin_url('customize.php')),
		'link_label'  => esc_html__('Customize', 'sydney'),
		'docs_link'   => 'https://docs.athemes.com/article/forms-html-module/',
		'desc'        => __('Design options for HTML elements and forms.', 'sydney'),
	);		
	$settings['features'][] = array(
		'type'       => 'pro',
		'title'      => esc_html__('Extra Widget Area', 'sydney'),
		'desc'       => esc_html__('Add an extra widget area to your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[section]', 'sydney_extra_widget_area', admin_url('customize.php'))
	);
	
	$settings['features'][] = array(
		'type'       => 'pro',
		'title'      => esc_html__('Google Maps', 'sydney'),
		'desc'       => esc_html__('Customize Google Maps integration in your theme.', 'sydney'),
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[section]', 'sydney_pro_maps', admin_url('customize.php'))
	);

	$settings['features'][] = array(
		'type'       => 'pro',
		'title'      => esc_html__('Hooks', 'sydney'),
		'desc'       => esc_html__('Add your custom code to various hooks', 'sydney'),
		'module' 	 => 'hooks',
		'link_label' => esc_html__('Customize', 'sydney'),
		'link_url'   => add_query_arg('autofocus[panel]', 'sydney_hooks_panel', admin_url('customize.php'))
	);

	$settings['features'][] = array(
		'type'       => 'pro',
		'title'      => esc_html__('White Label (Agency)', 'sydney'),
		'desc'       => esc_html__('Rename and present Sydney as your own.', 'sydney'),
		'docs_link'  => 'https://docs.athemes.com/article/pro-white-label-sydney/',
		'link_label' => esc_html__('Learn More', 'sydney'),
	);

	//Register the Block Templates module only if the function exists
	if ( function_exists( 'block_template_part' ) ) {
		$settings['features'][] = array(
			'category'    => 'general',
			'module'      => 'block-templates',
			'title'        => esc_html__('Block Templates', 'sydney'),
			'type'        => 'free',
			'link_url'    => add_query_arg('autofocus[section]', 'sydney_block_templates', admin_url('customize.php')),
			'link_label'  => esc_html__('Customize', 'sydney'),
			'docs_link'   => 'https://docs.athemes.com/article/block-templates-module/',
			'desc'        => __('Build headers, footers etc. with the site editor.', 'sydney'),
		);	
	}

	return $settings;
}
add_filter('sydney_dashboard_settings', 'sydney_dashboard_settings');

/**
 * Get all modules ids
 * 
 */
function sydney_get_modules_ids() {
	$settings = sydney_dashboard_settings();

	$modules = array();

	foreach ( $settings[ 'features' ] as $feature ) {
		if( ! isset( $feature[ 'module' ] ) ) {
			continue;
		}

		$modules[] = $feature[ 'module' ];
	}
	
	return $modules;
}

/**
 * Demos Settings
 * 
 */
function sydney_demos_settings($settings) {

	// Categories.
	$settings['categories'] = array(
		'business'  => 'Business',
		'portfolio' => 'Portfolio',
		'ecommerce' => 'eCommerce',
		'event'     => 'Events',
	);

	// Builders.
	$settings['builders'] = array(
		'gutenberg' => 'Gutenberg',
		'elementor' => 'Elementor',
	);

	// Pro.
	$settings['has_pro']   		= defined( 'SYDNEY_PRO_VERSION' ) ? true : false;
	$settings['pro_status']   	= defined( 'SYDNEY_PRO_VERSION' ) ? true : false; //for backward compatibility
	$settings['pro_label'] 		= esc_html__('Get Pro', 'sydney');
	$settings['pro_link']  		= 'https://athemes.com/theme/sydney?utm_source=theme_table&utm_medium=button&utm_campaign=Sydney';

	return $settings;
}
add_filter( 'atss_register_demos_settings', 'sydney_demos_settings' );

/**
 * Get setting icon
 * 
 */
function sydney_dashboard_get_setting_icon( $slug ) {
	$icon = '';

	switch ( $slug ) {
		case 'general':
			$icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M11.9287 18C15.2424 18 17.9287 15.3137 17.9287 12C17.9287 8.68629 15.2424 6 11.9287 6C8.615 6 5.92871 8.68629 5.92871 12C5.92871 15.3137 8.615 18 11.9287 18ZM11.9287 15C13.5856 15 14.9287 13.6569 14.9287 12C14.9287 10.3431 13.5856 9 11.9287 9C10.2719 9 8.92871 10.3431 8.92871 12C8.92871 13.6569 10.2719 15 11.9287 15Z" fill="#1E1E1E"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M11.2758 4C10.787 4 10.3698 4.35341 10.2894 4.8356L9.92871 7H13.9287L13.568 4.8356C13.4876 4.35341 13.0704 4 12.5816 4H11.2758ZM12.5816 20C13.0704 20 13.4876 19.6466 13.568 19.1644L13.9287 17H9.92871L10.2894 19.1644C10.3698 19.6466 10.787 20 11.2758 20H12.5816Z" fill="#1E1E1E"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M18.53 7.43471C18.2856 7.01137 17.7709 6.82677 17.3132 6.99827L15.2584 7.76807L17.2584 11.2322L18.9524 9.83756C19.3298 9.52687 19.4273 8.98887 19.1829 8.56552L18.53 7.43471ZM5.32647 16.5655C5.57089 16.9889 6.08555 17.1735 6.54332 17.002L8.59811 16.2322L6.59811 12.7681L4.90406 14.1627C4.52665 14.4734 4.42918 15.0114 4.6736 15.4347L5.32647 16.5655Z" fill="#1E1E1E"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M4.67454 8.56553C4.43012 8.98888 4.52759 9.52688 4.90499 9.83757L6.59905 11.2322L8.59905 7.76808L6.54426 6.99828C6.08649 6.82678 5.57183 7.01138 5.32741 7.43472L4.67454 8.56553ZM19.1838 15.4347C19.4282 15.0114 19.3308 14.4734 18.9534 14.1627L17.2593 12.7681L15.2593 16.2322L17.3141 17.002C17.7719 17.1735 18.2865 16.9889 18.5309 16.5655L19.1838 15.4347Z" fill="#1E1E1E"/>
			</svg>';
			break;

		case 'performance':
			$icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14.1109 6.79335L15.5547 7.62693L12.9157 13.0315L10.75 11.7811L14.1109 6.79335Z" fill="#1E1E1E"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M16.9497 16.9497C18.2165 15.683 19 13.933 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 13.933 5.7835 15.683 7.05025 16.9497L5.98959 18.0104C4.45139 16.4722 3.5 14.3472 3.5 12C3.5 7.30558 7.30558 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 14.3472 19.5486 16.4722 18.0104 18.0104L16.9497 16.9497Z" fill="#1E1E1E"/>
			</svg>';
			break;

		case 'info':
			$icon = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9 1.6875C7.55373 1.6875 6.13993 2.11637 4.9374 2.91988C3.73486 3.72339 2.7976 4.86544 2.24413 6.20163C1.69067 7.53781 1.54586 9.00811 1.82801 10.4266C2.11017 11.8451 2.80661 13.148 3.82928 14.1707C4.85196 15.1934 6.15492 15.8898 7.57341 16.172C8.99189 16.4541 10.4622 16.3093 11.7984 15.7559C13.1346 15.2024 14.2766 14.2651 15.0801 13.0626C15.8836 11.8601 16.3125 10.4463 16.3125 9C16.3105 7.06123 15.5394 5.20246 14.1685 3.83154C12.7975 2.46063 10.9388 1.68955 9 1.6875ZM8.71875 5.0625C8.88563 5.0625 9.04876 5.11198 9.18752 5.2047C9.32627 5.29741 9.43441 5.42919 9.49828 5.58336C9.56214 5.73754 9.57885 5.90719 9.54629 6.07086C9.51373 6.23453 9.43337 6.38487 9.31537 6.50287C9.19737 6.62087 9.04703 6.70123 8.88336 6.73379C8.71969 6.76634 8.55004 6.74963 8.39586 6.68577C8.24169 6.62191 8.10991 6.51377 8.0172 6.37501C7.92449 6.23626 7.875 6.07313 7.875 5.90625C7.875 5.68247 7.9639 5.46786 8.12213 5.30963C8.28037 5.15139 8.49498 5.0625 8.71875 5.0625ZM9.5625 12.9375C9.26413 12.9375 8.97799 12.819 8.76701 12.608C8.55603 12.397 8.4375 12.1109 8.4375 11.8125V9C8.28832 9 8.14525 8.94074 8.03976 8.83525C7.93427 8.72976 7.875 8.58668 7.875 8.4375C7.875 8.28832 7.93427 8.14524 8.03976 8.03975C8.14525 7.93426 8.28832 7.875 8.4375 7.875C8.73587 7.875 9.02202 7.99353 9.233 8.2045C9.44398 8.41548 9.5625 8.70163 9.5625 9V11.8125C9.71169 11.8125 9.85476 11.8718 9.96025 11.9773C10.0657 12.0827 10.125 12.2258 10.125 12.375C10.125 12.5242 10.0657 12.6673 9.96025 12.7727C9.85476 12.8782 9.71169 12.9375 9.5625 12.9375Z" fill="#2271b1"/>
			</svg>';
			break;

		case 'arrow':
			$icon = '<svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M5.0301 6.45275C4.95061 6.37327 4.91246 6.27722 4.91564 6.16462C4.91882 6.05201 4.96028 5.95597 5.04003 5.87648L6.16278 4.75374H1.73142C1.61881 4.75374 1.52436 4.71558 1.44805 4.63928C1.37174 4.56297 1.33372 4.46864 1.33399 4.3563C1.33399 4.2437 1.37214 4.14924 1.44845 4.07294C1.52475 3.99663 1.61908 3.95861 1.73142 3.95887H6.16278L5.0301 2.82619C4.95061 2.74671 4.91087 2.65225 4.91087 2.54283C4.91087 2.4334 4.95061 2.33908 5.0301 2.25985C5.10958 2.18037 5.20404 2.14062 5.31347 2.14062C5.42289 2.14062 5.51722 2.18037 5.59644 2.25985L7.41468 4.0781C7.45443 4.11785 7.48265 4.1609 7.49934 4.20727C7.51603 4.25363 7.52424 4.30331 7.52398 4.3563C7.52398 4.4093 7.51563 4.45897 7.49894 4.50534C7.48225 4.55171 7.45416 4.59476 7.41468 4.63451L5.5865 6.46269C5.51364 6.53555 5.42263 6.57198 5.31347 6.57198C5.2043 6.57198 5.10985 6.53224 5.0301 6.45275Z" fill="#6D7175"/>
			</svg>';
			break;
		
	}

	if( empty( $icon ) ) {
		return '';
	}

	return wp_kses( //From TwentTwenty. Keeps only allowed tags and attributes
		$icon,
		array(
			'svg'     => array(
				'class'       => true,
				'xmlns'       => true,
				'width'       => true,
				'height'      => true,
				'viewbox'     => true,
				'aria-hidden' => true,
				'role'        => true,
				'focusable'   => true,
				'fill'      => true,
			),
			'path'    => array(
				'fill'      => true,
				'fill-rule' => true,
				'd'         => true,
				'transform' => true,
				'stroke'	=> true,
				'stroke-width' => true,
				'stroke-linejoin' => true
			),
			'polygon' => array(
				'fill'      => true,
				'fill-rule' => true,
				'points'    => true,
				'transform' => true,
				'focusable' => true,
			),
			'rect'    => array(
				'x'      => true,
				'y'      => true,
				'width'  => true,
				'height' => true,
				'transform' => true
			),				
		)
	);
}