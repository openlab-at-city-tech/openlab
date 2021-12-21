<?php
/**
 * Default theme options.
 *
 * @package ePortfolio
 */

if ( ! function_exists( 'eportfolio_get_default_theme_options' ) ) :

	/**
	 * Get default theme options
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
	function eportfolio_get_default_theme_options() {

		$defaults = array();

        $defaults['short_description_details'] = '';
        $defaults['button_url_link'] = '';
        $defaults['button_text'] = esc_html__( 'About Me', 'eportfolio' );

		// Latest Post / Blog Post Settings
		$defaults['show_slider_on_blog']					= 1;
		$defaults['select_category_for_blog_slider']		= 1;
		$defaults['blog_page_slider_number']				= 4;
		$defaults['enable_blog_layout_switch']				= 1;
		$defaults['blog_layout_style']						= 'list-post-layout';
		$defaults['blog_layout_grid_column']				= '3-column';

		// portfolio Template Settings
		$defaults['enable_portfolio_widget_sidebar']		= 1;
		$defaults['enable_portfolio_masonry_section']		= 1;
		$defaults['enable_portfolio_page_title']			= 1;
		$defaults['select_category_for_portfolio_section']	= 1;
		$defaults['portfolio_section_post_number']			= 15;

		// Photography Template Settings
		$defaults['enable_photography_slider_overlay']		= 0;
		$defaults['select_category_for_photography_slider']	= 1;
		$defaults['photography_page_slider_number']			= 7;
		$defaults['enable_background_on_text_details']		= 1;

		//Layout options.
		$defaults['enable_archive_layout_switch']	= 1;
		$defaults['archive_layout_style']			= 'list-post-layout';
		$defaults['archive_layout_grid_column']		= '3-column-arc';
		$defaults['site_date_layout_option']		= 'in-time-span';
		$defaults['global_layout']					= 'no-sidebar';
		$defaults['pagination_type']				= 'numeric';
		$defaults['enable_copyright_credit']     	= 1;
		$defaults['copyright_text']					= esc_html__( 'Copyright All right reserved', 'eportfolio' );
		$defaults['enable_preloader']				= 1;

		// Pass through filter.
		$defaults = apply_filters( 'eportfolio_filter_default_theme_options', $defaults );

		return $defaults;

	}

endif;
