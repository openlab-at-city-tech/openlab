<?php
/**
 * gillian Theme Customizer.
 *
 * @package gillian
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function gillian_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	
/**
* Add custom color options.
*
*/

	/*--------------------------------------------------------------
	1.0 Add Settings
	--------------------------------------------------------------*/
	/*--------------------------------------------------------------
	1.1 Top Menu Settings
	--------------------------------------------------------------*/

	// Top Menu: Link text color
	$wp_customize->add_setting( 'top_menu_links', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_menu_links', array(
			'label' => __( 'Top Menu: Link text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'top_menu_links',
	) ) );
	
	// Top Menu: Background color
	$wp_customize->add_setting( 'top_menu_background', array(
			'default' => '#908692',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_menu_background', array(
			'label' => __( 'Top Menu: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'top_menu_background',
	) ) );
	
	// Top Menu: Link background color on hover
	$wp_customize->add_setting( 'top_menu_hover', array(
			'default' => '#a599a7',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'top_menu_hover', array(
			'label' => __( 'Top Menu: Link background color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'top_menu_hover',
	) ) );
	
	/*--------------------------------------------------------------
	1.2 Header Settings
	--------------------------------------------------------------*/
	
	// Header: Background color
	$wp_customize->add_setting( 'header_background', array(
			'default' => '#7e7380',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background', array(
			'label' => __( 'Header: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'header_background',
	) ) );
	
	// Header: Search bar background color
	$wp_customize->add_setting( 'header_search_background', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_search_background', array(
			'label' => __( 'Header: Search bar background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'header_search_background',
	) ) );
	
	/*--------------------------------------------------------------
	1.3 Bottom Menu Settings
	--------------------------------------------------------------*/
	
	// Bottom/Primary Menu: Link text color
	$wp_customize->add_setting( 'bottom_menu_links', array(
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_menu_links', array(
			'label' => __( 'Bottom/Primary Menu: Link text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'bottom_menu_links',
	) ) );
	
	// Bottom/Primary Menu: Background color
	$wp_customize->add_setting( 'bottom_menu_background', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_menu_background', array(
			'label' => __( ' Bottom/Primary Menu: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'bottom_menu_background',
	) ) );
	
	// Bottom/Primary Menu: Link background color on hover
	$wp_customize->add_setting( 'bottom_menu_hover', array(
			'default' => '#0e2d41',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'bottom_menu_hover', array(
			'label' => __( 'Bottom/Primary Menu: Link background color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'bottom_menu_hover',
	) ) );
	
	/*--------------------------------------------------------------
	1.4 Content Settings
	--------------------------------------------------------------*/
	
	// Content: Text color
	$wp_customize->add_setting( 'content_text', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_text', array(
			'label' => __( 'Content: Main text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_text',
	) ) );
	
	// Content: Link color
	$wp_customize->add_setting( 'content_links', array(
			'default' => '#cd4444',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_links', array(
			'label' => __( 'Content: Link color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_links',
	) ) );
	
	// Content: Link color on hover
	$wp_customize->add_setting( 'content_links_hover', array(
			'default' => '#000000',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_links_hover', array(
			'label' => __( 'Content: Link color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_links_hover',
	) ) );
	
	// Content: Link underline color
	$wp_customize->add_setting( 'content_underline', array(
			'default' => '#eeeeee',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_underline', array(
			'label' => __( 'Content: Link underline color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_underline',
	) ) );
	
	// Content: Link underline color on hover
	$wp_customize->add_setting( 'content_underline_hover', array(
			'default' => '#cccccc',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_underline_hover', array(
			'label' => __( 'Content: Link underline color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_underline_hover',
	) ) );
	
	// Content: Post title link color
	$wp_customize->add_setting( 'content_title_links', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_title_links', array(
			'label' => __( 'Content: Post title link color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_title_links',
	) ) );
	
	// Content: Accent color (separator lines)
	$wp_customize->add_setting( 'content_accent', array(
			'default' => '#eeeeee',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_accent', array(
			'label' => __( 'Content: Accent color (separator lines)', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_accent',
	) ) );
	
	// Content: Entry meta (date, time, comments etc. beneath post title) text color
	$wp_customize->add_setting( 'content_entry_meta', array(
			'default' => '#59495c',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_entry_meta', array(
			'label' => __( 'Content: Entry meta (date, time, comments etc. beneath post title) text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_entry_meta',
	) ) );
	
	// Content: Entry meta icons color
	$wp_customize->add_setting( 'content_entry_meta_icons', array(
			'default' => '#7e7380',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_entry_meta_icons', array(
			'label' => __( 'Content: Entry meta icons color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'content_entry_meta_icons',
	) ) );
	
	/*--------------------------------------------------------------
	1.5 Comments Settings
	--------------------------------------------------------------*/
	
	// Comments: Background color
	$wp_customize->add_setting( 'comments_background', array(
			'default' => '#f9f9f9',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_background', array(
			'label' => __( 'Comments: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_background',
	) ) );
	
	// Comments: Border color
	$wp_customize->add_setting( 'comments_border', array(
			'default' => '#e9e9e9',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_border', array(
			'label' => __( 'Comments: Border color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_border',
	) ) );
	
	// Comments: Input field text color
	$wp_customize->add_setting( 'comments_input_text', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_input_text', array(
			'label' => __( 'Comments: Input field text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_input_text',
	) ) );
	
	// Comments: Input field background on focus
	$wp_customize->add_setting( 'comments_input_focus', array(
			'default' => '#f1f1f1',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_input_focus', array(
			'label' => __( 'Comments: Input field background on focus', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_input_focus',
	) ) );
	
	// Comments: Link color
	$wp_customize->add_setting( 'comments_links', array(
			'default' => '#b43c3c',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_links', array(
			'label' => __( 'Comments: Link color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_links',
	) ) );
	
	// Comments: Reply link text color
	$wp_customize->add_setting( 'comments_reply_text', array(
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_reply_text', array(
			'label' => __( 'Comments: Reply link text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_reply_text',
	) ) );
	
	// Comments: Reply link background color
	$wp_customize->add_setting( 'comments_reply_background', array(
			'default' => '#c03546',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_reply_background', array(
			'label' => __( 'Comments: Reply link background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_reply_background',
	) ) );
	
	// Comments: Reply link background color on hover
	$wp_customize->add_setting( 'comments_reply_hover', array(
			'default' => '#0e3a6b',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'comments_reply_hover', array(
			'label' => __( 'Comments: Reply link background color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'comments_reply_hover',
	) ) );
	
	/*--------------------------------------------------------------
	1.6 Buttons Settings
	--------------------------------------------------------------*/
	
	// Buttons (reset/submit/etc.): Text color
	$wp_customize->add_setting( 'buttons_text', array(
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'buttons_text', array(
			'label' => __( 'Buttons (reset/submit/etc.): Text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'buttons_text',
	) ) );
	
	// Buttons (reset/submit/etc.): Background color
	$wp_customize->add_setting( 'buttons_background', array(
			'default' => '#7e7380',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'buttons_background', array(
			'label' => __( 'Buttons (reset/submit/etc.): Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'buttons_background',
	) ) );
	
	// Buttons (reset/submit/etc.): Background color on hover
	$wp_customize->add_setting( 'buttons_hover', array(
			'default' => '#5b4d5d',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'buttons_hover', array(
			'label' => __( 'Buttons (reset/submit/etc.): Background color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'buttons_hover',
	) ) );
	
	/*--------------------------------------------------------------
	1.7 Sidebar Settings
	--------------------------------------------------------------*/
	
	// Sidebar: Background color
	$wp_customize->add_setting( 'sidebar_background', array(
			'default' => '#15467c',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_background', array(
			'label' => __( 'Sidebar: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_background',
	) ) );
	
	// Sidebar: Accent #1 (Widget titles, select & search field input text color, etc.)
	$wp_customize->add_setting( 'sidebar_accent_one', array(
			'default' => '#a6c1dd',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_accent_one', array(
			'label' => __( 'Sidebar: Accent #1 (Widget titles, select & search field input text color, etc.)', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_accent_one',
	) ) );
	
	// Sidebar: Accent #2 (bottom border, border under Archives lists, Categories lists, etc.)
	$wp_customize->add_setting( 'sidebar_accent_two', array(
			'default' => '#0e3a6b',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_accent_two', array(
			'label' => __( 'Sidebar: Accent #2 (bottom border, border under Archives lists, Categories lists, etc.)', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_accent_two',
	) ) );
	
	// Sidebar: Text color
	$wp_customize->add_setting( 'sidebar_text', array(
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_text', array(
			'label' => __( 'Sidebar: Text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_text',
	) ) );
	
	// Sidebar: Link color
	$wp_customize->add_setting( 'sidebar_links', array(
			'default' => '#cdeafe',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_links', array(
			'label' => __( 'Sidebar: Link color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_links',
	) ) );
	
	// Sidebar: Link color on hover
	$wp_customize->add_setting( 'sidebar_hover', array(
			'default' => '#edef90',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_hover', array(
			'label' => __( 'Sidebar: Link color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_hover',
	) ) );
	
	// Sidebar: Link underline color
	$wp_customize->add_setting( 'sidebar_underline', array(
			'default' => '#6488ac',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_underline', array(
			'label' => __( 'Sidebar: Link underline color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_underline',
	) ) );
	
	// Sidebar: Link underline color on hover
	$wp_customize->add_setting( 'sidebar_underline_hover', array(
			'default' => '#cdeafe',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_underline_hover', array(
			'label' => __( 'Sidebar: Link underline color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'sidebar_underline_hover',
	) ) );
	
	/*--------------------------------------------------------------
	1.8 Footer Settings
	--------------------------------------------------------------*/
	
	// Footer: Background color
	$wp_customize->add_setting( 'footer_background', array(
			'default' => '#071f2e',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_background', array(
			'label' => __( 'Footer: Background color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_background',
	) ) );
	
	// Footer: Text color
	$wp_customize->add_setting( 'footer_text', array(
			'default' => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_text', array(
			'label' => __( 'Footer: Text color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_text',
	) ) );
	
	// Footer: Link color
	$wp_customize->add_setting( 'footer_links', array(
			'default' => '#cdeafe',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_links', array(
			'label' => __( 'Footer: Link color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_links',
	) ) );
	
	// Footer: Link color on hover
	$wp_customize->add_setting( 'footer_hover', array(
			'default' => '#edef90',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_hover', array(
			'label' => __( 'Footer: Link color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_hover',
	) ) );
	
	// Footer: Link underline color
	$wp_customize->add_setting( 'footer_underline', array(
			'default' => '#6488ac',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_underline', array(
			'label' => __( 'Footer: Link underline color', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_underline',
	) ) );
	
	// Footer: Link underline color on hover
	$wp_customize->add_setting( 'footer_underline_hover', array(
			'default' => '#cdeafe',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_underline_hover', array(
			'label' => __( 'Footer: Link underline color on hover', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_underline_hover',
	) ) );
	
	// Footer: Accent #1 (Widget titles, select & search field input text color, etc.)
	$wp_customize->add_setting( 'footer_accent_one', array(
			'default' => '#a6c1dd',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_accent_one', array(
			'label' => __( 'Footer: Accent #1 (Widget titles, select & search field input text color, etc.)', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_accent_one',
	) ) );
	
	// Footer: Accent #2 (Bottom border, border under Archives lists, Categories lists, etc.)
	$wp_customize->add_setting( 'footer_accent_two', array(
			'default' => '#0e3a6b',
			'sanitize_callback' => 'sanitize_hex_color'
	) );
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_accent_two', array(
			'label' => __( 'Footer: Accent #2 (Back to top link, bottom border, border under Archives lists, Categories lists, etc.)', 'gillian' ),
			'section' => 'colors',
			'settings' => 'footer_accent_two',
	) ) );

}
add_action( 'customize_register', 'gillian_customize_register' );

function gillian_customizer_head_styles() {
	
	/*--------------------------------------------------------------
	2.0 Styles
	--------------------------------------------------------------*/
	
	?>
	<style type="text/css">
		
		<?php 
		// Top Menu: Link text color
		
		$top_menu_links = get_theme_mod( 'top_menu_links' );
		if ( ! empty( $top_menu_links ) && '#071f2e' != $top_menu_links ) :
		?>
		/* Top Menu: Link text color */
	
		.top-navigation, .top-navigation a {
			color: <?php echo esc_html( $top_menu_links ); ?>;
		}
		
		.top-navigation button:before {
			color: <?php echo esc_html( $top_menu_links ); ?>;
		}
		
		@media all and (max-width: 767px) {
			
			.top-menu a:hover, .top-menu a:focus {
				border-bottom: 5px solid <?php echo esc_html( $top_menu_links ); ?>;
			}
			
		}
		<?php endif;
		
		// Top Menu: Background color
		
		$top_menu_background = get_theme_mod( 'top_menu_background' );
		if ( ! empty( $top_menu_background ) && '#908692' != $top_menu_background ) :
		?>
		
		/* Top Menu: Background color */
		
		.top-navigation, .top-navigation ul ul, .top-menu button {
			background-color: <?php echo esc_html( $top_menu_background ); ?>;
		}
		
		<?php endif;
		
		// Top Menu: Link background color on hover
		
		$top_menu_hover = get_theme_mod( 'top_menu_hover' );
		if ( ! empty( $top_menu_hover ) && '#a599a7' != $top_menu_hover ) :
		?>
		
		/* Top Menu: Link background color on hover */
	
		.top-menu a:hover, .top-menu a:focus,
		.menu-social li a:hover, .menu-social li a:focus,
		.top-menu button:hover, .top-menu button:focus	{
			background-color: <?php echo esc_html( $top_menu_hover ); ?>;
		}
		
		<?php endif;
		
		// Header: Background color
		
		$header_background = get_theme_mod( 'header_background' );
		if ( ! empty( $header_background ) && '#7e7380' != $header_background ) :
		?>
		
		/* Header: Background color */
	
		.site-header {
			background-color: <?php echo esc_html( $header_background ); ?>;
		}
		
		@media all and (min-width: 768px) {
		
			.site-header {
				background-color: <?php echo esc_html( $header_background ); ?>;
			}
			
		}
		
		<?php endif;
		
		// Header: Search bar background color
		
		$header_search_background = get_theme_mod('header_search_background' );
		if ( ! empty( $header_search_background ) && '#071f2e' != $header_search_background ) :
		?>
		
		/* Header: Search bar background color */

		.header-search .search-field,
		.header-search .search-field:focus,
		.header-search .search-submit {
			background-color: <?php echo esc_html( $header_search_background ); ?>!important;
		}
		
		<?php endif;
		
		// Bottom/Primary Menu: Link text color
		
		$bottom_menu_links = get_theme_mod( 'bottom_menu_links' );
		if ( ! empty( $bottom_menu_links ) && '#ffffff' != $bottom_menu_links ) :
		?>
		
		/* Bottom/Primary Menu: Link text color */
	
		.bottom-navigation, .bottom-navigation a,
		.bottom-navigation .menu-item-has-children:hover,
		.bottom-navigation .menu-item-has-children:focus	{
			color: <?php echo esc_html( $bottom_menu_links ); ?>;
		}
		
		.bottom-navigation a:hover, .bottom-navigation a:focus {
			border-bottom: 5px solid <?php echo esc_html( $bottom_menu_links ); ?>;
		}
		
		@media all and (max-width: 767px) {
			
			.bottom-menu button {
				color: <?php echo esc_html( $bottom_menu_links ); ?>;
			}
		
		}
		
		<?php endif;
		
		// Bottom/Primary Menu: Background color
		
		$bottom_menu_background = get_theme_mod( 'bottom_menu_background' );
		if ( ! empty( $bottom_menu_background ) && '#071f2e' != $bottom_menu_background ) :
		?>
		
		/* Bottom/Primary Menu: Background color */
	
		.bottom-navigation, .bottom-navigation ul ul,
		.bottom-menu button:hover, .button-menu button:focus {
			background-color: <?php echo esc_html( $bottom_menu_background ); ?>;
		}
		
		.bottom-navigation a {
			border-bottom: 5px solid <?php echo esc_html( $bottom_menu_background ); ?>;
		}
		
		@media all and (max-width: 767px) {
			
			.bottom-navigation.toggled {
				background-color: <?php echo esc_html( $bottom_menu_background ); ?>;
			}
		
		}
		
		<?php endif;
		
		// Bottom/Primary Menu: Link background color on hover
		
		$bottom_menu_hover = get_theme_mod( 'bottom_menu_hover' );
		if ( ! empty( $bottom_menu_hover ) && '#0e2d41' != $bottom_menu_hover ) :
		?>
		
		/* Bottom/Primary Menu: Link background color on hover */
	
		.bottom-navigation a:hover, .bottom-navigation a:focus,
		.bottom-menu button, .bottom-menu button:focus {
			background-color: <?php echo esc_html( $bottom_menu_hover ); ?>;
		}
		
		.bottom-menu button, .bottom-menu button:hover, .bottom-menu button:focus {
			border: 2px solid <?php echo esc_html( $bottom_menu_hover ); ?>;
		}
		
		<?php endif;
		
		// Content: Text color
		
		$content_text = get_theme_mod( 'content_text' );
		if ( ! empty( $content_text ) && '#071f2e' != $content_text ) :
		?>
		
		/* Content: Text color */
	
		.site-main {
			color: <?php echo esc_html( $content_text ); ?>;
		}
		
		<?php endif;
		
		// Content: Link color
		
		$content_links = get_theme_mod( 'content_links' );
		if ( ! empty( $content_links ) && '#cd4444' != $content_links ) :
		?>
		
		/* Content: Link color */
		
		.site-main a:link, .site-main a:visited {
			color: <?php echo esc_html( $content_links ); ?>;
		}
		
		a.page-numbers:hover, a.page-numbers:focus,
		.nav-links a:hover, .nav-links a:focus {
			color: <?php echo esc_html( $content_links ); ?>!important;
		}
		
		<?php endif;
		
		// Content: Link color on hover
		
		$content_links_hover = get_theme_mod( 'content_links_hover' );
		if ( ! empty( $content_links_hover ) && '#000000' != $content_links_hover ) :
		?>
		
		/* Content: Link color on hover */
		
		.site-main a:hover, .site-main a:focus,
		.comment-body a:hover, .comment-body a:focus	{
			color: <?php echo esc_html( $content_links_hover ); ?>;
		}
		
		<?php endif;
		
		// Content: Link underline color
		
		$content_underline = get_theme_mod( 'content_underline' );
		if ( ! empty( $content_underline ) && '#eeeeee' != $content_underline ) :
		?>
		
		/* Content: Link underline color */
		
		.entry-content a:link, .entry-content a:visited {
			border-bottom: 2px solid <?php echo esc_html( $content_underline ); ?>;
		}
		
		<?php endif;
		
		// Content: Link underline color on hover
		
		$content_underline_hover = get_theme_mod( 'content_underline_hover' );
		if ( ! empty( $content_underline_hover ) && '#cccccc' != $content_underline_hover ) :
		?>
		
		/* Content: Link underline color on hover */
		
		.site-main a:hover, .site-main a:focus {
			border-bottom: 2px solid <?php echo esc_html( $content_underline_hover ); ?>;
		}
		
		<?php endif;
		
		// Content: Post title link color
		
		$content_title_links = get_theme_mod( 'content_title_links' );
		if ( ! empty( $content_title_links ) && '#071f2e' != $content_title_links ) :
		?>
		
		/* Content: Post title link color */
	
		.entry-title a:link, .entry-title a:visited {
			color: <?php echo esc_html( $content_title_links ); ?>;
		}
		
		<?php endif;
		
		// Content: Accent color (separator lines)
		
		$content_accent = get_theme_mod( 'content_accent' );
		if ( ! empty( $content_accent ) && '#eeeeee' != $content_accent ) :
		?>
		
		/* Content: Accent color (separator lines) */
		
		.hentry:after {
			border-bottom: 4px solid <?php echo esc_html( $content_accent ); ?>;
		}
		
		.entry-meta {
			border-bottom: 2px solid <?php echo esc_html( $content_accent ); ?>;
		}
		
		.comments-area {
			border-top: 4px solid <?php echo esc_html( $content_accent ); ?>;
		}
		
		blockquote, pre {
			background-color: <?php echo esc_html( $content_accent ); ?>;
		}
		
		<?php endif;
		
		// Content: Entry meta (date, time, comments etc. beneath post title) text color
		
		$content_entry_meta = get_theme_mod( 'content_entry_meta' );
		if ( ! empty( $content_entry_meta ) && '#59495c' != $content_entry_meta ) :
		?>
		
		/* Content: Entry meta (date, time, comments etc. beneath post title) text color */
		
		.entry-meta {
			color: <?php echo esc_html( $content_entry_meta ); ?>;
		}
		
		<?php endif;
		
		// Content: Entry meta icons color
		
		$content_entry_meta_icons = get_theme_mod('content_entry_meta_icons' );
		if ( ! empty( $content_entry_meta_icons ) && '#a199a2' != $content_entry_meta_icons ) :
		?>
		
		/* Content: Entry meta icons color */
		
		.fa, .sticky .entry-title:before {
			color: <?php echo esc_html( $content_entry_meta_icons ); ?>;
		}
		
		<?php endif;
		
		// Comments: Background color
		
		$comments_background = get_theme_mod( 'comments_background' );
		if ( ! empty( $comments_background ) && '#f9f9f9' != $comments_background ) :
		?>
		
		/* Comments: Background color */
		
		.comment-body {
			background-color: <?php echo esc_html( $comments_background ); ?>;
		}
		
		input[type='text'],
		input[type='email'],
		input[type='url'],
		input[type='password'],
		input[type='search'],
		input[type='number'],
		input[type='tel'],
		input[type='range'],
		input[type='date'],
		input[type='month'],
		input[type='week'],
		input[type='time'],
		input[type='datetime'],
		input[type='datetime-local'],
		input[type='color'],
		textarea {
			background-color: <?php echo esc_html( $comments_background ); ?>;
		}
		
		<?php endif;
		
		// Comments: Border color
		
		$comments_border = get_theme_mod( 'comments_border' );
		if ( ! empty( $comments_border ) && '#e9e9e9' != $comments_border ) :
		?>
		
		/* Comments: Border color */
		
		.comment-body {
			border: 1px solid <?php echo esc_html( $comments_border ); ?>;
		}
		input[type='text'],
		input[type='email'],
		input[type='url'],
		input[type='password'],
		input[type='search'],
		input[type='number'],
		input[type='tel'],
		input[type='range'],
		input[type='date'],
		input[type='month'],
		input[type='week'],
		input[type='time'],
		input[type='datetime'],
		input[type='datetime-local'],
		input[type='color'],
		textarea {
			border: 1px solid <?php echo esc_html( $comments_border ); ?>;
		}
		
		<?php endif;
		
		// Comments: Input field text color
		
		$comments_input_text = get_theme_mod( 'comments_input_text' );
		if ( ! empty( $comments_input_text ) && '#071f2e' != $comments_input_text ) :
		?>
		
		/* Comments: Input field text color */
		
		input[type='text'],
		input[type='email'],
		input[type='url'],
		input[type='password'],
		input[type='search'],
		input[type='number'],
		input[type='tel'],
		input[type='range'],
		input[type='date'],
		input[type='month'],
		input[type='week'],
		input[type='time'],
		input[type='datetime'],
		input[type='datetime-local'],
		input[type='color'],
		textarea {
			color: <?php echo esc_html( $comments_input_text ); ?>;
		}
		
		<?php endif;
		
		// Comments: Input field background on focus
		
		$comments_input_focus = get_theme_mod( 'comments_input_focus' );
		if ( ! empty( $comments_input_focus ) && '#f1f1f1' != $comments_input_focus ) :
		?>
		
		/* Comments: Input field background on focus */
		
		input[type='text']:focus,
		input[type='email']:focus,
		input[type='url']:focus,
		input[type='password']:focus,
		input[type='search']:focus,
		input[type='number']:focus,
		input[type='tel']:focus,
		input[type='range']:focus,
		input[type='date']:focus,
		input[type='month']:focus,
		input[type='week']:focus,
		input[type='time']:focus,
		input[type='datetime']:focus,
		input[type='datetime-local']:focus,
		input[type='color']:focus,
		textarea:focus {
			background-color: <?php echo esc_html( $comments_input_focus ); ?>;
		}
		
		<?php endif;
		
		// Comments: Link color
		
		$comments_links = get_theme_mod( 'comments_links' );
		if ( ! empty( $comments_links ) && '#b43c3c' != $comments_links ) :
		?>
		
		/* Comments: Link color */
		
		.comment-body a:link, .comment-body a:visited {
			color: <?php echo esc_html( $comments_links ); ?>;
		}
		
		<?php endif;
		
		// Comments: Reply link text color
		
		$comments_reply_text = get_theme_mod( 'comments_reply_text' );
		if ( ! empty( $comments_reply_text ) && '#ffffff' != $comments_reply_text ) :
		?>
		
		/* Comments: Reply link text color */
		
		.reply a:link, .reply a:visited {
			color: <?php echo esc_html( $comments_reply_text ); ?>;
		}
		
		<?php endif;
		
		// Comments: Reply link background color
		
		$comments_reply_background = get_theme_mod( 'comments_reply_background' );
		if ( ! empty( $comments_reply_background ) && '#c03546' != $comments_reply_background ) :
		?>
		
		/* Comments: Reply link background color */
		
		.reply a:link, .reply a:visited {
			background-color: <?php echo esc_html( $comments_reply_background ); ?>;
		}
		
		<?php endif;
		
		// Comments: Reply link background color on hover
		
		$comments_reply_hover = get_theme_mod( 'comments_reply_hover' );
		if ( ! empty( $comments_reply_hover ) && '#0e3a6b' != $comments_reply_hover ) :
		?>
		
		/* Comments: Reply link background color on hover */
		
		.reply a:hover, .reply a:focus {
			background-color: <?php echo esc_html( $comments_reply_hover ); ?>;
		}
		
		<?php endif;
		
		// Buttons (reset/submit/etc.): Text color
		
		$buttons_text = get_theme_mod( 'buttons_text' );
		if ( ! empty( $buttons_text ) && '#ffffff' != $buttons_text ) :
		?>
		
		/* Buttons (reset/submit/etc.): Text color */
		
		button,
		input[type='button'],
		input[type='reset'],
		input[type='submit'] {
			color: <?php echo esc_html( $buttons_text ); ?>;
		}
		
		<?php endif;
		
		// Buttons (reset/submit/etc.): Background color
		
		$buttons_background = get_theme_mod( 'buttons_background' );
		if ( ! empty( $buttons_background ) && '#7e7380' != $buttons_background ) :
		?>
		
		/* Buttons (reset/submit/etc.): Background color */
		
		button,
		input[type='button'],
		input[type='reset'],
		input[type='submit'] {
			background-color: <?php echo esc_html( $buttons_background ); ?>;
		}
		
		a.page-numbers, .nav-links a {
			color: <?php echo esc_html( $buttons_background ); ?>!important;
		}
		
		<?php endif;
		
		// Buttons (reset/submit/etc.): Background color on hover
		
		$buttons_hover = get_theme_mod( 'buttons_hover' );
		if ( ! empty( $buttons_hover ) && '#5b4d5d' != $buttons_hover ) :
		?>
		
		/* Buttons (reset/submit/etc.): Background color on hover */
		
		button:hover,
		input[type='button']:hover,
		input[type='reset']:hover,
		input[type='submit']:hover,
		button:focus,
		input[type='button']:focus,
		input[type='reset']:focus,
		input[type='submit']:focus,
		button:active,
		input[type='button']:active,
		input[type='reset']:active,
		input[type='submit']:active {
			background-color: <?php echo esc_html( $buttons_hover ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Background color
		
		$sidebar_background = get_theme_mod( 'sidebar_background' );
		if ( ! empty( $sidebar_background ) && '#15467c' != $sidebar_background ) :
		?>
		
		/* Sidebar: Background color */
		
		.widget-area {
			background-color: <?php echo esc_html( $sidebar_background ); ?>;
		}
		
		blockquote:before {
			color: <?php echo esc_html( $sidebar_background ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Accent #1 (Widget titles, select & search field input text color, etc.)
		
		$sidebar_accent_one = get_theme_mod( 'sidebar_accent_one' );
		if ( ! empty( $sidebar_accent_one ) && '#a6c1dd' != $sidebar_accent_one ) :
		?>
		
		/*  Sidebar: Accent #1 (Widget titles, select & search field input text color, etc.) */
	
		.sidebar .widget-title {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}

		.sidebar .widget select, .sidebar .widget_search .search-field {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}
		
		.sidebar .widget_search .search-field::-webkit-input-placeholder {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}

		.sidebar .widget_search .search-field:-moz-placeholder,
		.sidebar .widget_search .search-field:-ms-input-placeholder {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}

		.sidebar .widget_search .search-field::-moz-placeholder {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}
		
		.sidebar .post-count {
			color: <?php echo esc_html( $sidebar_accent_one ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Accent #2 (Bottom border, border under Archives lists, Categories lists, etc.)
		
		$sidebar_accent_two = get_theme_mod( 'sidebar_accent_two' );
		if ( ! empty( $sidebar_accent_two ) && '#0e3a6b' != $sidebar_accent_two ) :
		?>
		
		/*  Sidebar: Accent #2 (Bottom border, border under Archives lists, Categories lists, etc.) */
		
		.sidebar {
			border-bottom: 10px solid <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		.sidebar .widget select, .sidebar .widget_search .search-field {
			background-color: <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		.sidebar .widget_search .search-submit .fa {
			background-color: <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		.sidebar .widget_categories ul ul li,
		.sidebar .widget_pages ul ul li,
		.sidebar .widget_nav_menu ul ul li {
			border-top: 2px solid <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		.sidebar .widget_archive li, .sidebar .widget_categories li,
		.sidebar .widget_pages li, .sidebar .widget_meta li,
		.sidebar .widget_recent_comments li, .sidebar .widget_recent_entries li,
		.sidebar .widget_rss li, .sidebar .widget_nav_menu li {
			border-bottom: 2px solid <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		.sidebar .widget_categories ul ul li,
		.sidebar .widget_pages ul ul li,
		.sidebar .widget_nav_menu ul ul li {
			border-bottom: none!important;
		}
		
		.sidebar .post-count {
			background-color: <?php echo esc_html( $sidebar_accent_two ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Text color
		
		$sidebar_text = get_theme_mod( 'sidebar_text' );
		if ( ! empty( $sidebar_text ) && '#ffffff' != $sidebar_text ) :
		?>
		
		/* Sidebar: Text color */
		
		.sidebar .widget {
			color: <?php echo esc_html( $sidebar_text ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Link color
		
		$sidebar_links = get_theme_mod( 'sidebar_links' );
		if ( ! empty( $sidebar_links ) && '#cdeafe' != $sidebar_links ) :
		?>
		
		/* Sidebar: Link color */
		
		.sidebar .widget a:link, .sidebar .widget a:visited,
		.sidebar .widget_search .search-submit	{
			color: <?php echo esc_html( $sidebar_links ); ?>;
		}
		
		.sidebar .widget_search .search-submit .fa {
			color: <?php echo esc_html( $sidebar_links ); ?>!important;
		}
		
		<?php endif;
		
		// Sidebar: Link color on hover
		
		$sidebar_hover = get_theme_mod( 'sidebar_hover' );
		if ( ! empty( $sidebar_hover ) && '#edef90' != $sidebar_hover ) :
		?>
		
		/* Sidebar: Link color on hover */
		
		.sidebar .widget a:hover, .sidebar .widget a:focus,
		.sidebar .post-count:hover, .sidebar .post-count:focus	{
			color: <?php echo esc_html( $sidebar_hover ); ?>;
		}
		
		.sidebar .widget_search .search-submit .fa:hover, .sidebar .widget_search .search-submit .fa:focus {
			color: <?php echo esc_html( $sidebar_hover ); ?>!important;
		}
		
		<?php endif;
		
		// Sidebar: Link underline color
		
		$sidebar_underline = get_theme_mod( 'sidebar_underline' );
		if ( ! empty( $sidebar_underline ) && '#6488ac' != $sidebar_underline ) :
		?>
		
		/* Sidebar: Link underline color */
		
		.sidebar .widget a:link, .sidebar .widget a:visited {
			border-bottom: 2px solid <?php echo esc_html( $sidebar_underline ); ?>;
		}
		
		.sidebar .widget_search .search-submit .fa {
			border: 2px solid <?php echo esc_html( $sidebar_underline ); ?>;
		}
		
		<?php endif;
		
		// Sidebar: Link underline color on hover
		
		$sidebar_underline_hover = get_theme_mod( 'sidebar_underline_hover' );
		if ( ! empty( $sidebar_underline_hover ) && '#cdeafe' != $sidebar_underline_hover ) :
		?>
		
		/* Sidebar: Link underline color on hover */
		
		.sidebar .widget a:hover, .sidebar .widget a:focus {
			border-bottom: 2px solid <?php echo esc_html( $sidebar_underline_hover ); ?>;
		}
		
		.sidebar .widget_archive ul a:hover, .sidebar .widget_archive ul a:focus,
		.sidebar .widget_categories ul a:hover, .sidebar .widget_categories a:focus,
		.sidebar .widget_pages ul a:hover, .sidebar .widget_pages ul a:focus,
		.sidebar .widget_meta ul a:hover, .sidebar .widget_meta ul a:focus,
		.sidebar .widget_recent_comments ul a:hover, .sidebar .widget_recent_comments ul a:focus,
		.sidebar .widget_recent_entries ul a:hover, .sidebar .widget_recent_entries ul a:focus,
		.sidebar .widget_nav_menu ul a:hover, .sidebar .widget_nav_menu ul a:focus {
			border-bottom: 2px solid <?php echo esc_html( $sidebar_underline_hover ); ?>!important;
		}
		
		<?php endif;
		
		// Footer: Background color
		
		$footer_background = get_theme_mod( 'footer_background' );
		if ( ! empty( $footer_background ) && '#071f2e' != $footer_background ) :
		?>
		
		/* Footer: Background color */
		
		.site-footer, #footer-sidebar {
			background-color: <?php echo esc_html( $footer_background ); ?>;
		}
		
		<?php endif;
		
		// Footer: Text color
		
		$footer_text = get_theme_mod( 'footer_text' );
		if ( ! empty( $footer_text ) && '#ffffff' != $footer_text ) :
		?>
		
		/* Footer: Text color */
		
		.site-footer, #footer-sidebar,
		.site-info {
			color: <?php echo esc_html( $footer_text ); ?>;
		}
		
		<?php endif;
		
		// Footer: Link color
		
		$footer_links = get_theme_mod( 'footer_links' );
		if ( ! empty( $footer_links ) && '#cdeafe' != $footer_links ) :
		?>
		
		/* Footer: Link color */
		
		.site-info a:link, .site-info a:visited,
		.site-footer .widget-area a:link, .site-footer .widget-area a:visited,
		.back-to-top .fa {
			color: <?php echo esc_html( $footer_links ); ?>;
		}
		
		<?php endif;
		
		// Footer: Link color on hover
		
		$footer_hover = get_theme_mod( 'footer_hover' );
		if ( ! empty( $footer_hover ) && '#edef90' != $footer_hover ) :
		?>
		
		/* Footer: Link color on hover */
		
		.site-info a:hover, .site-info a:focus,
		.site-footer .widget-area a:hover, .site-footer .widget-area a:focus,
		.back-to-top .fa:hover, .back-to-top .fa:focus {
			color: <?php echo esc_html( $footer_hover ); ?>;
		}
		
		<?php endif;
		
		// Footer: Link underline color
		
		$footer_underline = get_theme_mod( 'footer_underline' );
		if ( ! empty( $footer_underline ) && '#6488ac' != $footer_underline ) :
		?>
		
		/* Footer: Link underline color */
		
		.site-info a:link, .site-info a:visited,
		.site-footer .widget-area a:link, .site-footer .widget-area a:visited {
			border-bottom: 2px solid <?php echo esc_html( $footer_underline ); ?>;
		}
		
		<?php endif;
		
		// Footer: Link underline color on hover
		
		$footer_underline_hover = get_theme_mod( 'footer_underline_hover' );
		if ( ! empty( $footer_underline_hover ) && '#cdeafe' != $footer_underline_hover ) :
		?>
		
		/* Footer: Link underline color on hover */
		
		.site-info a:hover, .site-info a:focus,
		.site-footer .widget-area a:hover, .site-footer .widget-area a:focus {
			border-bottom: 2px solid <?php echo esc_html( $footer_underline_hover ); ?>;
		}
		
		.site-footer .widget_archive ul a:hover, .site-footer .widget_archive ul a:focus,
		.site-footer .widget_categories ul a:hover, .site-footer .widget_categories a:focus,
		.site-footer .widget_pages ul a:hover, .site-footer .widget_pages ul a:focus,
		.site-footer .widget_meta ul a:hover, .site-footer .widget_meta ul a:focus,
		.site-footer .widget_recent_comments ul a:hover, .site-footer .widget_recent_comments ul a:focus,
		.site-footer .widget_recent_entries ul a:hover, .site-footer .widget_recent_entries ul a:focus,
		.site-footer .widget_nav_menu ul a:hover, .site-footer .widget_nav_menu ul a:focus {
			border-bottom: 2px solid <?php echo esc_html( $footer_underline_hover ); ?>!important;
		}
		
		<?php endif;
		
		// Footer: Accent #1 (Widget titles, select & search field input text color, etc.)
		
		$footer_accent_one = get_theme_mod( 'footer_accent_one' );
		if ( ! empty( $footer_accent_one ) && '#a6c1dd' != $footer_accent_one ) :
		?>
		
		/* Footer: Accent #1 (Widget titles, select & search field input text color, etc.) */

		.site-footer .widget-title {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}

		.site-footer .widget select, .site-footer .widget_search .search-field {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}
		
		.site-footer .widget_search .search-field::-webkit-input-placeholder {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}

		.site-footer .widget_search .search-field:-moz-placeholder,
		.site-footer .widget_search .search-field:-ms-input-placeholder {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}

		.site-footer .widget_search .search-field::-moz-placeholder {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}
		
		.site-footer .post-count {
			color: <?php echo esc_html( $footer_accent_one ); ?>;
		}
		
		<?php endif;
		
		// Footer: Accent #2 (Back to top link, bottom border, border under Archives lists, Categories lists, etc.)
		
		$footer_accent_two = get_theme_mod( 'footer_accent_two' );
		if ( ! empty( $footer_accent_two ) && '#0e3a6b' != $footer_accent_two ) :
		?>
		
		/* Footer: Accent #2 (Back to top link, bottom border, border under Archives lists, Categories lists, etc.) */
		
		.site-footer {
			border-bottom: 10px solid <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		.site-footer .widget select, .site-footer .widget_search .search-field {
			background-color: <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		.site-footer .widget_search .search-submit .fa {
			background-color: <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		.site-footer .widget_categories ul ul li,
		.site-footer .widget_pages ul ul li,
		.site-footer .widget_nav_menu ul ul li {
			border-top: 2px solid <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		.site-footer .widget_archive li, .site-footer .widget_categories li,
		.site-footer .widget_pages li, .site-footer .widget_meta li,
		.site-footer .widget_recent_comments li, .site-footer .widget_recent_entries li,
		.site-footer .widget_rss li, .site-footer .widget_nav_menu li {
			border-bottom: 2px solid <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		.site-footer .widget_categories ul ul li,
		.site-footer .widget_pages ul ul li,
		.site-footer .widget_nav_menu ul ul li {
			border-bottom: none!important;
		}
		
		.site-footer .post-count {
			background-color: <?php echo esc_html( $footer_accent_two ); ?>;
		}
		
		<?php endif; ?>
	</style>
	<?php
}
add_action( 'wp_head', 'gillian_customizer_head_styles' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function gillian_customize_preview_js() {
	wp_enqueue_script( 'gillian_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'gillian_customize_preview_js' );
