<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Option' ) ) {

	/**
	 * Class ezTOC_Option
	 *
	 * Credit: Adapted from Easy Digital Downloads.
	 */
	final class ezTOC_Option {

		/**
		 * Register the plugins core settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function register() {

			if ( false === get_option( 'ez-toc-settings' ) ) {

				add_option( 'ez-toc-settings', self::getDefaults() );
			}

			foreach ( self::getRegistered() as $section => $settings ) {

				add_settings_section(
					'ez_toc_settings_' . $section,
					__return_null(),
					'__return_false',
					'ez_toc_settings_' . $section
				);

				foreach ( $settings as $option ) {

					$name = isset( $option['name'] ) ? $option['name'] : '';

					add_settings_field(
						'ez-toc-settings[' . $option['id'] . ']',
						$name,
						method_exists( __CLASS__, $option['type'] ) ? array( __CLASS__, $option['type'] ) : array( __CLASS__, 'missingCallback' ),
						'ez_toc_settings_' . $section,
						'ez_toc_settings_' . $section,
						array(
							'section'     => $section,
							'id'          => isset( $option['id'] ) ? $option['id'] : null,
							'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
							'name'        => isset( $option['name'] ) ? $option['name'] : null,
							'size'        => isset( $option['size'] ) ? $option['size'] : null,
							'options'     => isset( $option['options'] ) ? $option['options'] : '',
							'default'     => isset( $option['default'] ) ? $option['default'] : '',
							'min'         => isset( $option['min'] ) ? $option['min'] : null,
							'max'         => isset( $option['max'] ) ? $option['max'] : null,
							'step'        => isset( $option['step'] ) ? $option['step'] : null,
							'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : null,
							'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null,
							'allow_blank' => isset( $option['allow_blank'] ) ? $option['allow_blank'] : true,
							'readonly'    => isset( $option['readonly'] ) ? $option['readonly'] : false,
							'faux'        => isset( $option['faux'] ) ? $option['faux'] : false,
							'without_hr'        => isset( $option['without_hr'] ) ? $option['without_hr'] : true,
							'allowedHtml'       => isset( $option['allowedHtml'] ) ? $option['allowedHtml'] : [],
						)
					);
				}

			}

			// Creates our settings in the options table
			register_setting( 'ez-toc-settings', 'ez-toc-settings', array( __CLASS__, 'sanitize' ) );
		}

		/**
		 * Callback for settings sanitization.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param array $input The value inputted in the field.
		 *
		 * @return string $input Sanitized value.
		 */
		public static function sanitize( $input = array() ) {

			$options = self::getOptions();

			if ( empty( $_POST['_wp_http_referer'] ) ) {

				return $input;
			}

			$registered = self::getRegistered();

			foreach ( $registered as $sectionID => $sectionOptions ) {

				$input = $input ? $input : array();
				$input = apply_filters( 'ez_toc_settings_' . $sectionID . '_sanitize', $input );

				// Loop through each setting being saved and pass it through a sanitization filter
				foreach ( $input as $key => $value ) {

					// Get the setting type (checkbox, select, etc)
					$type = isset( $registered[ $sectionID ][ $key ]['type'] ) ? $registered[ $sectionID ][ $key ]['type'] : false;

					if ( $type ) {

						// Field type specific filter
						$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize_' . $type, $value, $key );
					}

					// General filter
					$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize', $input[ $key ], $key );
				}

				// Loop through the registered options.
				foreach ( $sectionOptions as $optionID => $optionProperties ) {

					// Unset any that are empty for the section being saved.
					if ( empty( $input[ $optionID ] ) ) {

						unset( $options[ $optionID ] );
					}

					// Check for the checkbox option type.
					if ( array_key_exists( 'type', $optionProperties ) && 'checkbox' == $optionProperties['type'] ) {

						// If it does not exist in the options values being saved, add the option ID and set its value to `0`.
						// This matches WP core behavior for saving checkbox option values.
						if ( ! array_key_exists( $optionID, $input ) ) {

							$input[ $optionID ] = '0';
						}
					}
				}

			}

			// Merge our new settings with the existing
			$output = array_merge( $options, $input );

			return $output;
		}

		/**
		 * The core registered settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getRegistered() {
			$eztoc_latest_link=home_url();
			$args = array( 'numberposts' => '1');
			$recent_posts = wp_get_recent_posts( $args );
			foreach( $recent_posts as $recent ){
			 $eztoc_latest_link= add_query_arg( 'eztoc-edit-position', '', get_permalink($recent["ID"] ));
			}
			$options = array(
				'general' => apply_filters(
					'ez_toc_settings_general',
					array(
						'enabled_post_types' => array(
							'id' => 'enabled_post_types',
							'name' => __( 'Enable Support', 'easy-table-of-contents' ),
							'desc' => __( 'Select the post types to enable the support for table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'auto_insert_post_types' => array(
							'id' => 'auto_insert_post_types',
							'name' => __( 'Auto Insert', 'easy-table-of-contents' ),
							'desc' => __( 'Select the post types which will have the table of contents automatically inserted.', 'easy-table-of-contents' ) .
							          '<br><span class="description">' . __( 'NOTE: The table of contents will only be automatically inserted on post types for which it has been enabled.', 'easy-table-of-contents' ) . '<span>',
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'position' => array(
							'id' => 'position',
							'name' => __( 'Position', 'easy-table-of-contents' ),
							'desc' => __( 'Choose where where you want to display the table of contents.', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'before' => __( 'Before first heading (default)', 'easy-table-of-contents' ),
								'after' => __( 'After first heading', 'easy-table-of-contents' ),
								'afterpara' => __( 'After first paragraph', 'easy-table-of-contents' ),
								'top' => __( 'Top', 'easy-table-of-contents' ),
								'bottom' => __( 'Bottom', 'easy-table-of-contents' ),
								//'placeholder' => __( 'Replace [toc] placeholder. For backwards compatibility with Table of Content Plus.', 'easy-table-of-contents' ),
							),
							'default' => 1,
						),
						'start' => array(
							'id' => 'start',
							'name' => __( 'Show when', 'easy-table-of-contents' ),
							'desc' => __( 'or more headings are present', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array_combine( range( 1, 10 ), range( 1, 10 ) ),
							'default' => 2,
						),
						'show_heading_text' => array(
							'id' => 'show_heading_text',
							'name' => __( 'Display Header Label', 'easy-table-of-contents' ),
							'desc' => __( 'Show header text above the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						'heading_text' => array(
							'id' => 'heading_text',
							'name' => __( 'Header Label', 'easy-table-of-contents' ),
							'desc' => __( 'Eg: Contents, Table of Contents, Page Contents', 'easy-table-of-contents' ),
							'type' => 'text',
							'default' => __( 'Contents', 'easy-table-of-contents' ),
						),
						'visibility' => array(
							'id' => 'visibility',
							'name' => __( 'Toggle View', 'easy-table-of-contents' ),
							'desc' => __( 'Allow the user to toggle the visibility of the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						//'visibility_show' => array(
						//	'id' => 'visibility_show',
						//	'name' => __( 'Show Label', 'easy-table-of-contents' ),
						//	'desc' => __( 'Eg: show', 'easy-table-of-contents' ),
						//	'type' => 'text',
						//	'default' => __( 'show', 'easy-table-of-contents' ),
						//),
						//'visibility_hide' => array(
						//	'id' => 'visibility_hide',
						//	'name' => __( 'Hide Label', 'easy-table-of-contents' ),
						//	'desc' => __( 'Eg: hide', 'easy-table-of-contents' ),
						//	'type' => 'text',
						//	'default' => __( 'hide', 'easy-table-of-contents' ),
						//),
						'visibility_hide_by_default' => array(
							'id' => 'visibility_hide_by_default',
							'name' => __( 'Initial View', 'easy-table-of-contents' ),
							'desc' => __( 'Initially hide the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'show_hierarchy' => array(
							'id' => 'show_hierarchy',
							'name' => __( 'Show as Hierarchy', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'checkbox',
							'default' => true,
						),
						'counter' => array(
							'id' => 'counter',
							'name' => __( 'Counter', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'select',
							'options' => self::getCounterList(),
							'default' => 'decimal',
						),
						'smooth_scroll' => array(
							'id' => 'smooth_scroll',
							'name' => __( 'Smooth Scroll', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'checkbox',
							'default' => true,
						),
						'toc_loading' => array(
							'id' => 'toc_loading',
							'name' => __( 'TOC Loading Method', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'select',
							'options' => array(
								'js' => __( 'JavaScript (default)', 'easy-table-of-contents' ),
								'css' => __( 'Pure CSS', 'easy-table-of-contents' ),
								 
							),
							'default' => 'js',
						),
						'sticky-toggle-above-header'      => array(
							'id'   => 'sticky-toggle-above-header',
							'name' => '<strong>' . __( 'Sticky Toggle Options', 'easy-table-of-contents' ) . '</strong>',
//                                                        'desc' => __( '', 'easy-table-of-contents' ),
							'type' => 'header',
						),
//                                                'sticky-toggle-above-hr'          => array(
//                                                        'id'   => 'sticky-toggle-above-hr',
//                                                        'type' => 'hr',
//                                                ),
						'sticky-toggle'                   => array(
							'id'      => 'sticky-toggle',
							'name'    => __( 'On/Off', 'easy-table-of-contents' ),
							'desc'    => '',
							'type'    => 'checkbox',
							'default' => false,
						),
						'sticky-toggle-width'             => array(
							'id'      => 'sticky-toggle-width',
							'name'    => __( 'Width', 'easy-table-of-contents' ),
							'desc'    => '',
							'type'    => 'select',
							'options' => array(
								'auto'   => __( 'Auto', 'easy-table-of-contents' ),
								'custom' => __( 'User Defined', 'easy-table-of-contents' ),
							),
							'default' => 'auto',
						),
						'sticky-toggle-width-custom'      => array(
							'id'          => 'sticky-toggle-width-custom',
							'name'        => __( 'Custom Width', 'easy-table-of-contents' ),
							'desc'        => '',
							'type'        => 'text',
							'default'     => false,
							'placeholder' => __( 'Enter sticky toggle custom width here..', 'easy-table-of-contents' )
						),
						'sticky-toggle-height'            => array(
							'id'      => 'sticky-toggle-height',
							'name'    => __( 'Height', 'easy-table-of-contents' ),
							'desc'    => '',
							'type'    => 'select',
							'options' => array(
								'auto'   => __( 'Auto', 'easy-table-of-contents' ),
								'custom' => __( 'User Defined', 'easy-table-of-contents' ),
							),
							'default' => 'auto',
						),
						'sticky-toggle-height-custom'     => array(
							'id'          => 'sticky-toggle-height-custom',
							'name'        => __( 'Custom Height', 'easy-table-of-contents' ),
							'desc'        => '',
							'type'        => 'text',
							'default'     => false,
							'placeholder' => __( 'Enter sticky toggle custom height here..', 'easy-table-of-contents' )
						),
						'sticky-toggle-open-button-text'     => array(
							'id'          => 'sticky-toggle-open-button-text',
							'name'        => __( 'Open Button Text', 'easy-table-of-contents' ),
							'desc'        => '',
							'type'        => 'text',
							'default'     => false,
							'placeholder' => __( 'Enter sticky toggle open button text here..', 'easy-table-of-contents' )
						),
//                                                'sticky-toggle-position'          => array(
//                                                        'id'      => 'sticky-toggle-position',
//                                                        'name'    => __( 'Sticky Toggle Position', 'easy-table-of-contents' ),
//                                                        'desc'    => '',
//                                                        'type'    => 'radio',
//                                                        'options'   => array(
//                                                                'left'  => __( 'Left', 'easy-table-of-position' ),
//                                                                'right' => __( 'Right', 'easy-table-of-position' ),
//                                                        ),
//                                                        'default' => 'left',
//                                                ),
					)
				),
				'appearance' => apply_filters(
					'ez_toc_settings_appearance',
					array(
						'width' => array(
							'id' => 'width',
							'name' => __( 'Width', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'selectgroup',
							'options' => array(
								'fixed' => array(
									'name' => __( 'Fixed', 'easy-table-of-contents' ),
									'options' => array(
										'200px' => '200px',
										'225px' => '225px',
										'250px' => '250px',
										'275px' => '275px',
										'300px' => '300px',
										'325px' => '325px',
										'350px' => '350px',
										'375px' => '375px',
										'400px' => '400px',
									),
								),
								'relative' => array(
									'name' => __( 'Relative', 'easy-table-of-contents' ),
									'options' => array(
										'auto' => 'Auto',
										'25%' => '25%',
										'33%' => '33%',
										'50%' => '50%',
										'66%' => '66%',
										'75%' => '75%',
										'100%' => '100%',
									),
								),
								'other' => array(
									'name' => __( 'Custom', 'easy-table-of-contents' ),
									'options' => array(
										'custom' => __( 'User Defined', 'easy-table-of-contents' ),
									),
								),
							),
							'default' => 'auto',
						),
						'width_custom' => array(
							'id' => 'width_custom',
							'name' => __( 'Custom Width', 'easy-table-of-contents' ),
							'desc' => __( 'Select the User Defined option from the Width option to utilitze the custom width.', 'easy-table-of-contents' ),
							'type' => 'custom_width',
							'default' => 275,
						),
						'wrapping' => array(
							'id' => 'wrapping',
							'name' => __( 'Alignment', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'select',
							'options' => array(
								'none' => __( 'None (Default)', 'easy-table-of-contents' ),
								'left' => __( 'Left', 'easy-table-of-contents' ),
								'right' => __( 'Right', 'easy-table-of-contents' ),
								'center' => __( 'Center', 'easy-table-of-contents' ),
							),
							'default' => 'none',
						),
						'font_options_header' => array(
							'id' => 'font_options',
							'name' => '<strong>' . __( 'Font Option', 'easy-table-of-contents' ) . '</strong>',
							//'desc' => __( 'For the following settings to apply, select the Custom Font option.', 'easy-table-of-contents' ),
							'type' => 'header',
						),
						'title_font_size' => array(
							'id' => 'title_font_size',
							'name' => __( 'Title Font Size', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'font_size',
							'default' => 120,
						),
						'title_font_weight' => array(
							'id' => 'title_font_weight',
							'name' => __( 'Title Font Weight', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'select',
							'options' => array(
								'100' => __( 'Thin', 'easy-table-of-contents' ),
								'200' => __( 'Extra Light', 'easy-table-of-contents' ),
								'300' => __( 'Light', 'easy-table-of-contents' ),
								'400' => __( 'Normal', 'easy-table-of-contents' ),
								'500' => __( 'Medium', 'easy-table-of-contents' ),
								'600' => __( 'Semi Bold', 'easy-table-of-contents' ),
								'700' => __( 'Bold', 'easy-table-of-contents' ),
								'800' => __( 'Extra Bold', 'easy-table-of-contents' ),
								'900' => __( 'Heavy', 'easy-table-of-contents' ),
							),
							'default' => '500',
						),
						'font_size' => array(
							'id' => 'font_size',
							'name' => __( 'Font Size', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'font_size',
							'default' => 95,
						),
						'child_font_size' => array(
							'id' => 'child_font_size',
							'name' => __( 'Child Font Size', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'child_font_size',
							'default' => 90,
						),
						'theme_option_header' => array(
							'id' => 'theme_option_header',
							'name' => '<strong>' . __( 'Theme Options', 'easy-table-of-contents' ) . '</strong>',
							//'desc' => __( 'For the following settings to apply, select the Custom Theme option.', 'easy-table-of-contents' ),
							'type' => 'header',
						),
						'theme' => array(
							'id' => 'theme',
							'name' => __( 'Theme', 'easy-table-of-contents' ),
							'desc' => __( 'The theme is only applied to the table of contents which is auto inserted into the post. The Table of Contents widget will inherit the theme widget styles.', 'easy-table-of-contents' ),
							'type' => 'radio',
							'options' => array(
								'grey' => __( 'Grey', 'easy-table-of-contents' ),
								'light-blue' => __( 'Light Blue', 'easy-table-of-contents' ),
								'white' => __( 'White', 'easy-table-of-contents' ),
								'black' => __( 'Black', 'easy-table-of-contents' ),
								'transparent' => __( 'Transparent', 'easy-table-of-contents' ),
								'custom' => __( 'Custom', 'easy-table-of-contents' ),
							),
							'default' => 'grey',
						),
						'custom_theme_header' => array(
							'id' => 'custom_theme_header',
							'name' => '<strong>' . __( 'Custom Theme', 'easy-table-of-contents' ) . '</strong>',
							'desc' => __( 'For the following settings to apply, select the Custom Theme option.', 'easy-table-of-contents' ),
							'type' => 'header',
						),
						'custom_background_colour' => array(
							'id' => 'custom_background_colour',
							'name' => __( 'Background Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#fff',
						),
						'custom_border_colour' => array(
							'id' => 'custom_border_colour',
							'name' => __( 'Border Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#ddd',
						),
						'custom_title_colour' => array(
							'id' => 'custom_title_colour',
							'name' => __( 'Title Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#999',
						),
						'custom_link_colour' => array(
							'id' => 'custom_link_colour',
							'name' => __( 'Link Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#428bca',
						),
						'custom_link_hover_colour' => array(
							'id' => 'custom_link_hover_colour',
							'name' => __( 'Link Hover Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#2a6496',
						),
						'custom_link_visited_colour' => array(
							'id' => 'custom_link_visited_colour',
							'name' => __( 'Link Visited Color', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#428bca',
						),
						'heading-text-direction' => array(
                            'id' => 'heading-text-direction',
                            'name' => __( 'Heading Text Direction', 'easy-table-of-contents' ),
                            'desc' => '',
                            'type' => 'radio',
                            'options' => array(
                                'ltr' => __( 'Left to Right (LTR)', 'easy-table-of-contents' ),
                                'rtl' => __( 'Right to Left (RTL)', 'easy-table-of-contents' ),
                            ),
                            'default' => 'ltr',
                        ),
					)
				),
				'advanced' => apply_filters(
					'ez_toc_settings_advanced',
					array(
						'lowercase' => array(
							'id' => 'lowercase',
							'name' => __( 'Lowercase', 'easy-table-of-contents' ),
							'desc' => __( 'Ensure anchors are in lowercase.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'hyphenate' => array(
							'id' => 'hyphenate',
							'name' => __( 'Hyphenate', 'easy-table-of-contents' ),
							'desc' => __( 'Use - rather than _ in anchors.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_homepage' => array(
							'id' => 'include_homepage',
							'name' => __( 'Homepage', 'easy-table-of-contents' ),
							'desc' => __( 'Show the table of contents for qualifying items on the homepage.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'exclude_css' => array(
							'id' => 'exclude_css',
							'name' => __( 'CSS', 'easy-table-of-contents' ),
							'desc' => __( "Prevent the loading the core CSS styles. When selected, the appearance options from above will be ignored.", 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'inline_css' => array(
							'id' => 'inline_css',
							'name' => __( 'Inline CSS', 'easy-table-of-contents' ),
							'desc' => __( "Improve your  website performance by inlining your CSS.", 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						//'bullet_spacing' => array(
						//	'id' => 'bullet_spacing',
						//	'name' => __( 'Theme Bullets', 'easy-table-of-contents' ),
						//	'desc' => __( 'If your theme includes background images for unordered list elements, enable this option to support them.', 'easy-table-of-contents' ),
						//	'type' => 'checkbox',
						//	'default' => false,
						//),
						'heading_levels' => array(
							'id' => 'heading_levels',
							'name' => __( 'Headings:', 'easy-table-of-contents' ),
							'desc' => __( 'Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.', 'easy-table-of-contents' ),
							'type' => 'checkboxgroup',
							'options' => array(
								'1' => __( 'Heading 1 (h1)', 'easy-table-of-contents' ),
								'2' => __( 'Heading 2 (h2)', 'easy-table-of-contents' ),
								'3' => __( 'Heading 3 (h3)', 'easy-table-of-contents' ),
								'4' => __( 'Heading 4 (h4)', 'easy-table-of-contents' ),
								'5' => __( 'Heading 5 (h5)', 'easy-table-of-contents' ),
								'6' => __( 'Heading 6 (h6)', 'easy-table-of-contents' ),
							),
							'default' => array( '1', '2', '3', '4', '5', '6' ),
						),
						'exclude' => array(
							'id' => 'exclude',
							'name' => __( 'Exclude Headings', 'easy-table-of-contents' ),
							'desc' => __( 'Specify headings to be excluded from appearing in the table of contents. Separate multiple headings with a pipe <code>|</code>. Use an asterisk <code>*</code> as a wildcard to match other text.', 'easy-table-of-contents' ),
							'type' => 'text',
							'size' => 'large',
							'default' => '',
						),
						'exclude_desc' => array(
							'id' => 'exclude_desc',
							'name' => '',
							'desc' => '<p><strong>' . __( 'Examples:', 'easy-table-of-contents' ) . '</strong></p>' .
							          '<ul>' .
							          '<li>' . __( '<code>Fruit*</code> Ignore headings starting with "Fruit".', 'easy-table-of-contents' ) . '</li>' .
							          '<li>' . __( '<code>*Fruit Diet*</code> Ignore headings with "Fruit Diet" somewhere in the heading.', 'easy-table-of-contents' ) . '</li>' .
							          '<li>' . __( '<code>Apple Tree|Oranges|Yellow Bananas</code> Ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas".', 'easy-table-of-contents' ) . '</li>' .
							          '</ul>' .
							          '<p>' . __( '<strong>Note:</strong> This is not case sensitive.', 'easy-table-of-contents' ) . '</p>',
							'type' => 'descriptive_text',
						),
						'smooth_scroll_offset' => array(
							'id' => 'smooth_scroll_offset',
							'name' => __( 'Smooth Scroll Offset', 'easy-table-of-contents' ),
							'desc' => 'px<br/>' . __( 'If you have a consistent menu across the top of your site, you can adjust the top offset to stop the headings from appearing underneath the top menu. A setting of 30 accommodates the WordPress admin bar. This setting only has an effect after you have enabled Smooth Scroll option.', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 30
						),
						'mobile_smooth_scroll_offset' => array(
							'id' => 'mobile_smooth_scroll_offset',
							'name' => __( 'Mobile Smooth Scroll Offset', 'easy-table-of-contents' ),
							'desc' => 'px<br/>' . __( 'This provides the same function as the Smooth Scroll Offset option above but applied when the user is visiting your site on a mobile device.', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 0
						),
						'restrict_path' => array(
							'id' => 'restrict_path',
							'name' => __( 'Limit Path', 'easy-table-of-contents' ),
							'desc' => '<br/>' . __( 'Restrict generation of the table of contents to pages that match the required path. This path is from the root of your site and always begins with a forward slash.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . __( 'Eg: /wiki/, /corporate/annual-reports/', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
						),
						'fragment_prefix' => array(
							'id' => 'fragment_prefix',
							'name' => __( 'Default Anchor Prefix', 'easy-table-of-contents' ),
							'desc' => '<br/>' . __( 'Anchor targets are restricted to alphanumeric characters as per HTML specification (see readme for more detail). The default anchor prefix will be used when no characters qualify. When left blank, a number will be used instead.', 'easy-table-of-contents' ) .
							          '<br/>' . __( 'This option normally applies to content written in character sets other than ASCII.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . __( 'Eg: i, toc_index, index, _', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
							'default' => 'i',
						),
						'widget_affix_selector' => array(
							'id' => 'widget_affix_selector',
							'name' => __( 'Widget Affix Selector', 'easy-table-of-contents' ),
							'desc' => '<br/>' . __( 'To enable the option to affix or pin the Table of Contents widget enter the theme\'s sidebar class or id.', 'easy-table-of-contents' ) .
							          '<br/>' . __( 'Since every theme is different, this can not be determined automatically. If you are unsure how to find the sidebar\'s class or id, please ask the theme\'s support persons.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . __( 'Eg: .widget-area or #sidebar', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
							'default' => '',
						),
						'remove_special_chars_from_title' => array(
							'id' => 'remove_special_chars_from_title',
							'name' => __( 'Remove \':\' from TOC Title', 'easy-table-of-contents' ),
							'desc' => '',
							'type' => 'checkbox',
							'default' => false,
						),
						
					)
				),
                'shortcode' => apply_filters(
                    'ez_toc_settings_shortcode',
                    array(
//                        'shortcode-heading-paragraph'      => array(
//                            'id'   => 'shortcode-heading-paragraph',
//                            'name' => '',
//                            'desc' => __( 'There are several ways to have the easy table of contents display on your website.', 'easy-table-of-contents' ),
//                            'type' => 'paragraph',
//                        ),
                        'shortcode-first-paragraph'      => array(
                            'id'   => 'shortcode-first-paragraph',
                            'name' => __( 'Manual Adding the shortcode', 'easy-table-of-contents' ),
                            'desc' => __( 'You can use the following shortcode to `Easy Table of Contents` display in your particular post or page:<br/><input type="text" id="ez-toc-clipboard-apply" value="[ez-toc]" disabled />&nbsp;<span class="ez-toc-tooltip"><button type="button"  onclick="ez_toc_clipboard(\'ez-toc-clipboard-apply\', \'ez-toc-myTooltip\', this, event)" onmouseout="ez_toc_outFunc(\'ez-toc-myTooltip\', this, event)"><span class="ez-toc-tooltiptext ez-toc-myTooltip">Copy to clipboard</span>Copy shortcode  </button></span>', 'easy-table-of-contents' ),
                            'type' => 'paragraph',
                            'allowedHtml' => array(
								'br' => array(),
								'input' => array(
					               'type' => true,
					               'id' => true,
					               'value' => true,
					               'readonly' => true,
					               'disabled' => true,
					               'class' => true,
					           ),
					           '&nbsp;' => array(),
					           'span' => array(
					               'class' => true,
					               'id' => true,
					           ),
					           'button' => array(
					               'type' => true,
					               'onclick' => true,
					               'onmouseout' => true,
					               'id' => true,
					               'class' => true,
					           ),
				           ),
                        ),
                        'shortcode-second-paragraph'      => array(
                            'id'   => 'shortcode-second-paragraph',
                            'name' => __( 'Auto Insert', 'easy-table-of-contents' ),
                            'desc' => __( 'You can add `Easy Table of Contents` without using shortcode from `Auto Insert` option in General Setting so then there is no need to add shortcode while post, page or any post type editing.', 'easy-table-of-contents' ),
                            'type' => 'paragraph',
                        ),
                    )
                ),
				'prosettings' => apply_filters(
					'ez_toc_settings_prosettings', array()
				),
			);

			return apply_filters( 'ez_toc_registered_settings', $options );
		}

        /**
         * getCounterListBasic Method
         * @since 2.0.33
         * @scope protected
         * @static
         * @return array
        */
        protected static function getCounterList() {
            return array_merge( self::getCounterListBasic(), self::getCounterListDecimal(), self::getCounterList_i18n() );
        }

        /**
         * getCounterListBasic Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterListBasic() {
            return array(
                'none' => __( 'None', 'easy-table-of-contents' ),
                'disc' => __( 'Disc', 'easy-table-of-contents' ),
                'circle' => __( 'Circle', 'easy-table-of-contents' ),
                'square' => __( 'Square', 'easy-table-of-contents' ),
                '- ' => __( 'Hyphen', 'easy-table-of-contents' ),
                'cjk-earthly-branch' => __( 'Earthly Branch', 'easy-table-of-contents' ),
                'disclosure-open' => __( 'Disclosure Open', 'easy-table-of-contents' ),
                'disclosure-closed' => __( 'Disclosure Closed', 'easy-table-of-contents' ),
                'numeric' => __( 'Numeric', 'easy-table-of-contents' ),
            );
        }

        /**
         * getCounterListDecimal Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterListDecimal() {
            return array(
				'decimal' => __( 'Decimal (default)', 'easy-table-of-contents' ),
                'decimal-leading-zero' => __( 'Decimal Leading Zero', 'easy-table-of-contents' ),
                'cjk-decimal' => __( 'CJK Decimal', 'easy-table-of-contents' ),
            );
        }

        /**
         * getCounterList_i18n Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterList_i18n() {
            return array(
                'upper-roman' => __( 'Upper Roman', 'easy-table-of-contents' ),
                'lower-roman' => __( 'Lower Roman', 'easy-table-of-contents' ),
//                'upper-greek' => __( 'Upper Greek', 'easy-table-of-contents' ),
                'lower-greek' => __( 'Lower Greek', 'easy-table-of-contents' ),
                'upper-alpha' => __( 'Upper Alpha/Latin', 'easy-table-of-contents' ),
                'lower-alpha' => __( 'Lower Alpha/Latin', 'easy-table-of-contents' ),
                'armenian' => __( 'Armenian', 'easy-table-of-contents' ),
                'lower-armenian' => __( 'Lower Armenian', 'easy-table-of-contents' ),
                'arabic-indic' => __( 'Arabic', 'easy-table-of-contents' ),
                'bengali' => __( 'Bengali', 'easy-table-of-contents' ),
                'cambodian' => __( 'Cambodian/Khmer', 'easy-table-of-contents' ),
                'cjk-heavenly-stem' => __( 'Heavenly Stem', 'easy-table-of-contents' ),
                'cjk-ideographic' => __( 'CJK Ideographic/trad-chinese-informal', 'easy-table-of-contents' ),
                'devanagari' => __( 'Hindi (Devanagari)', 'easy-table-of-contents' ),
                'ethiopic-numeric' => __( 'Ethiopic', 'easy-table-of-contents' ),
                'georgian' => __( 'Georgian', 'easy-table-of-contents' ),
                'gujarati' => __( 'Gujarati', 'easy-table-of-contents' ),
                'gurmukhi' => __( 'Gurmukhi', 'easy-table-of-contents' ),
                'hebrew' => __( 'Hebrew', 'easy-table-of-contents' ),
                'hiragana' => __( 'Hiragana', 'easy-table-of-contents' ),
                'hiragana-iroha' => __( 'Hiragana-Iroha', 'easy-table-of-contents' ),
                'japanese-formal' => __( 'Japanese Formal', 'easy-table-of-contents' ),
                'japanese-informal' => __( 'Japanese Informal', 'easy-table-of-contents' ),
                'kannada' => __( 'Kannada', 'easy-table-of-contents' ),
                'katakana' => __( 'Katakana', 'easy-table-of-contents' ),
                'katakana-iroha' => __( 'Katakana-Iroha', 'easy-table-of-contents' ),
                'korean-hangul-formal' => __( 'Korean Hangul Formal', 'easy-table-of-contents' ),
                'korean-hanja-formal' => __( 'Korean Hanja Formal', 'easy-table-of-contents' ),
                'korean-hanja-informal' => __( 'Korean Hanja Informal', 'easy-table-of-contents' ),
                'lao' => __( 'Laotian', 'easy-table-of-contents' ),
                'malayalam' => __( 'Malayalam', 'easy-table-of-contents' ),
                'mongolian' => __( 'Mongolian', 'easy-table-of-contents' ),
                'myanmar' => __( 'Myanmar', 'easy-table-of-contents' ),
                'oriya' => __( 'Oriya', 'easy-table-of-contents' ),
                'persian' => __( 'Persian', 'easy-table-of-contents' ),
                'simp-chinese-formal' => __( 'Simplified Chinese Formal', 'easy-table-of-contents' ),
                'simp-chinese-informal' => __( 'Simplified Chinese Informal', 'easy-table-of-contents' ),
                'tamil' => __( 'Tamil', 'easy-table-of-contents' ),
                'telugu' => __( 'Telugu', 'easy-table-of-contents' ),
                'thai' => __( 'Thai', 'easy-table-of-contents' ),
                'tibetan' => __( 'Tibetan', 'easy-table-of-contents' ),
                'trad-chinese-formal' => __( 'Traditional Chinese Formal', 'easy-table-of-contents' ),
                'trad-chinese-informal' => __( 'Traditional Chinese Informal', 'easy-table-of-contents' ),
                'hangul' => __( 'Hangul', 'easy-table-of-contents' ),
                'hangul-consonant' => __( 'Hangul Consonant', 'easy-table-of-contents' ),
                'urdu' => __( 'Urdu', 'easy-table-of-contents' ),
            );
        }

		/**
		 * The default values for the registered settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getDefaults() {

			$defaults = array(
				'fragment_prefix'                    => 'i',
				'position'                           => 'before',
				'start'                              => 2,
				'show_heading_text'                  => true,
				'heading_text'                       => 'Table of Contents',
				'enabled_post_types'                 => array( 'post','page' ),
				'auto_insert_post_types'             => array( 'post','page' ),
				'show_hierarchy'                     => true,
				'counter'                            => 'decimal',
				'smooth_scroll'                      => true,
				'smooth_scroll_offset'               => 30,
				'mobile_smooth_scroll_offset'        => 0,
				'visibility'                         => true,
				'toc_loading'                        => 'js',
				'remove_special_chars_from_title'    => false,
				//'visibility_show'                    => 'show',
				//'visibility_hide'                    => 'hide',
				'visibility_hide_by_default'         => false,
				'width'                              => 'auto',
				'width_custom'                       => 275,
				'width_custom_units'                 => 'px',
				'wrapping'                           => 'none',
				'title_font_size'                    => 120,
				'title_font_size_units'              => '%',
				'title_font_weight'                  => 500,
				'font_size'                          => 95,
				'child_font_size'					 => 90,
				'font_size_units'                    => '%',
				'theme'                              => 'grey',
				'custom_background_colour'           => '#fff',
				'custom_border_colour'               => '#ddd',
				'custom_title_colour'                => '#999',
				'custom_link_colour'                 => '#428bca',
				'custom_link_hover_colour'           => '#2a6496',
				'custom_link_visited_colour'         => '#428bca',
				'lowercase'                          => false,
				'hyphenate'                          => false,
				//'bullet_spacing'                     => false,
				'include_homepage'                   => false,
				'exclude_css'                        => false,
				'inline_css'                        => false,
				'exclude'                            => '',
				'heading_levels'                     => array( '1', '2', '3', '4', '5', '6' ),
				'restrict_path'                      => '',
				'css_container_class'                => '',
				//'show_toc_in_widget_only'            => false,
				//'show_toc_in_widget_only_post_types' => array(),
				'widget_affix_selector'              => '',
				'heading-text-direction'              => 'ltr',
			);

			return apply_filters( 'ez_toc_get_default_options', $defaults );
		}

		/**
		 * Get the default options array.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getOptions() {

			$defaults = self::getDefaults();
			$options  = get_option( 'ez-toc-settings', $defaults );

			//return apply_filters( 'ez_toc_get_options', wp_parse_args( $options, $defaults ) );
			return apply_filters( 'ez_toc_get_options', $options );
		}

		/**
		 * Get option value by key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string     $key
		 * @param bool|false $default
		 *
		 * @return mixed
		 */
		public static function get( $key, $default = false ) {

			$options = self::getOptions();

			$value = array_key_exists( $key, $options ) ? $options[ $key ] : $default;
			$value = apply_filters( 'ez_toc_get_option', $value, $key, $default );

			return apply_filters( 'ez_toc_get_option_' . $key, $value, $key, $default );
		}

		/**
		 * Set an option value by key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string     $key
		 * @param bool|false $value
		 *
		 * @return bool
		 */
		public static function set( $key, $value = false ) {

			if ( empty( $value ) ) {

				$remove_option = self::delete( $key );

				return $remove_option;
			}

			$options = self::getOptions();

			$options[ $key ] = apply_filters( 'ez_toc_update_option', $value, $key );

			return update_option( 'ez-toc-settings', $options );
		}

		/**
		 * Delete an option from the options table by option key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public static function delete( $key ) {

			// First let's grab the current settings
			$options = get_option( 'ez-toc-settings' );

			// Next let's try to update the value
			if ( array_key_exists( $key, $options ) ) {

				unset( $options[ $key ] );
			}

			return update_option( 'ez-toc-settings', $options );
		}

		/**
		 * Sanitize a hex color from user input.
		 *
		 * Tries to convert $string into a valid hex colour.
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param string $string
		 * @param string $default
		 *
		 * @return mixed|string
		 */
		private static function hex_value( $string = '', $default = '#' ) {

			$return = $default;

			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );

				switch ( strlen( $return ) ) {
					case 3:    // do next
					case 6:
						$return = '#' . $return;
						break;

					default:
						if ( strlen( $return ) > 6 ) {
							$return = '#' . substr( $return, 0, 6 );
						}    // if > 6 chars, then take the first 6
						elseif ( strlen( $return ) > 3 && strlen( $return ) < 6 ) {
							$return = '#' . substr( $return, 0, 3 );
						}    // if between 3 and 6, then take first 3
						else {
							$return = $default;
						}                        // not valid, return $default
				}
			}

			return $return;
		}

		/**
		 * Get the registered post types minus excluded core types.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		public static function getPostTypes() {

			$exclude    = apply_filters( 'ez_toc_exclude_post_types', array( 'attachment', 'revision', 'nav_menu_item', 'safecss' ) );
			$registered = get_post_types( array(), 'objects' );
			$types      = array();

			foreach ( $registered as $post ) {

				if ( in_array( $post->name, $exclude ) ) {

					continue;
				}

				$types[ $post->name ] = $post->label;
			}

			return $types;
		}

		/**
		 * Missing Callback
		 *
		 * If a settings field type callback is not callable, alert the user.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function missingCallback( $args ) {

			printf(
				__( 'The callback function used for the <strong>%s</strong> setting is missing.', 'easy-table-of-contents' ),
				$args['id']
			);
		}

		/**
		 * HR Callback
		 *
		 * Renders hr html tag.
		 *
		 * @access public
		 *
		 * @param array $args Arguments passed by the setting
		 *
		 * @since  1.0
		 * @static
		 *
		 */
		public static function hr( array $args ) {
			$class = '';
			if ( isset( $args['class'] ) && true === $args['class'] ) {
				$class = self::get( $args['class'], $args['default'] );
			}
			echo <<<HR_TAG
                        <hr class='$class' />
HR_TAG;
		}

		/**
		 * Text Callback
		 *
		 * Renders text fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function text( $args, $value = null ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name             = '';

			} else {

				$name = ' name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$placeholder = '';
			if ( isset( $args['placeholder'] ) && ! empty( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = '<input type="text" class="' . $size . '-text" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . ' placeholder="' .
			        $placeholder . '" />';


			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Textarea Callback.
		 *
		 * Renders a textarea.
		 *
		 * @access public
		 * @since  1.1
		 * @static
		 *
		 * @param array $args  Arguments passed by the setting
		 * @param null  $value
		 */
		public static function textarea( $args, $value = null ) {

			$html = '';

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name             = '';

			} else {

				$name = ' name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			$html .= '<textarea rows="10" cols="50" class="' . $size . '-text" id="ez-toc-settings[' . $args['id'] . ']"' . $name .  $readonly . '/>' . esc_textarea( $value ) . '</textarea>';

			echo $html;
		}

		/**
		 * Number Callback
		 *
		 * Renders number fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args        Arguments passed by the setting
		 */
		public static function number( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name             = '';

			} else {

				$name = ' name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = '<input type="number" class="' . $size . '-text" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Checkbox Callback
		 *
		 * Renders checkboxes.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkbox( $args, $value = null ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$name = '';

			} else {

				$name = ' name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$checked = $value ? checked( 1, $value, false ) : '';

			$html = '<input type="checkbox" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="1" ' . $checked . '/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Multicheck Callback
		 *
		 * Renders multiple checkboxes.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkboxgroup( $args, $value = null ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( ! empty( $args['options'] ) ) {

				foreach ( $args['options'] as $key => $option ):

					if ( in_array( $key, $value ) ) {

						$enabled = $option;

					} else {

						$enabled = null;
					}

					echo '<input name="ez-toc-settings[' . $args['id'] . '][' . $key . ']" id="ez-toc-settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $key . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
					echo '<label for="ez-toc-settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';

				endforeach;

				if ( 0 < strlen( $args['desc'] ) ) {

					echo '<p class="description">' . $args['desc'] . '</p>';
				}
			}
		}

		/**
		 * Radio Callback
		 *
		 * Renders radio groups.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function radio( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			foreach ( $args['options'] as $key => $option ) {

				echo '<input name="ez-toc-settings[' . $args['id'] . ']"" id="ez-toc-settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( $key, $value, false ) . '/>&nbsp;';
				echo '<label for="ez-toc-settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<p class="description">' . $args['desc'] . '</p>';
			}
		}

		/**
		 * Select Callback
		 *
		 * Renders select fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function select( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}

			$html = '<select id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" ' . $chosen . 'data-placeholder="' . $placeholder . '" />';

			foreach ( $args['options'] as $option => $name ) {
				$selected = selected( $option, $value, false );
				$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
			}

			$html .= '</select>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Select Drop Down Callback
		 *
		 * Renders select with option group fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function selectgroup( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}

			$html = '<select id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" ' . $chosen . 'data-placeholder="' . $placeholder . '" />';

			foreach ( $args['options'] as $group ) {

				$html .= sprintf( '<optgroup label="%1$s">', $group['name'] );

				foreach ( $group['options'] as $option => $name ) {

					$selected = selected( $option, $value, false );
					$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
				}

				$html .= '</optgroup>';
			}

			$html .= '</select>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Header Callback
		 *
		 * Renders the header.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function header( $args ) {

            if( !isset( $args['without_hr'] ) || ( isset( $args['without_hr'] ) && $args['without_hr']) )
			    echo '<hr/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<p>' . wp_kses_post( $args['desc'] ) . '</p>';
			}
		}

        /**
		 * Paragraph Callback
		 *
		 * Renders the paragraph.
		 *
		 * @access public
		 * @since  2.0.33
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
         * @return void
		 */
		public static function paragraph( $args ) {

			if ( 0 < strlen( $args['desc'] ) ) {

				$allowed_html = [];
				if( is_array( $args['allowedHtml'] ) && count( $args['allowedHtml'] ) > 0 ) {
					$allowed_html = $args['allowedHtml'];
				}
				echo '<p>' . wp_kses( $args['desc'] , $allowed_html ) . '</p>';
			}
		}

		/**
		 * Descriptive text callback.
		 *
		 * Renders descriptive text onto the settings field.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function descriptive_text( $args ) {

			echo wp_kses_post( $args['desc'] );
		}

		/**
		 * Color picker Callback
		 *
		 * Renders color picker fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function color( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			$default = isset( $args['default'] ) ? $args['default'] : '';

			$html  = '<input type="text" class="ez-toc-color-picker" id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Custom table of contents width.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
		public static function custom_width( $args ) {

			//$value = self::get( $args['id'], $args['default'] );

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => 'px',
				)
			);

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}
		}

		/**
		 * Custom font size callback.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
		public static function font_size( $args ) {

			//$value = self::get( $args['id'], $args['default'] );

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'pt' => 'pt',
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => '%',
				)
			);

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}
		}
/**
		 * Custom font size callback.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
public static function child_font_size( $args ) {

			//$value = self::get( $args['id'], $args['default'] );

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'pt' => 'pt',
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => '%',
				)
			);

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}
		}

		/**
         * reset_options_to_default Method
         * to reset options
         * @since 2.0.37
         * @return bool|string
        */
        public static function eztoc_reset_options_to_default() {
            if( !wp_verify_nonce( sanitize_text_field( $_POST['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) )
            {
                return esc_attr__('Security Alert: nonce not verified!', 'easy-table-of-contents' );
            }

            delete_option('ez-toc-settings');
            return add_option( 'ez-toc-settings', self::getDefaults() );
        }
	}

	add_action( 'admin_init', array( 'ezTOC_Option', 'register' ) );

	add_action( 'wp_ajax_eztoc_reset_options_to_default', array( 'ezTOC_Option', 'eztoc_reset_options_to_default' ) );


}
