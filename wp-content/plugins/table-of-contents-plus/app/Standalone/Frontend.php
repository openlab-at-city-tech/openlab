<?php
namespace AIOSEO\TableOfContents\Standalone;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend {

	private $path;      // eg /wp-content/plugins/toc
	private $options;
	private $show_toc;  // allows to override the display (eg through [no_toc] shortcode)
	private $exclude_post_types;
	private $collision_collector;  // keeps a track of used anchors for collision detecting
	private $defaults;

	public function __construct() {
		$this->show_toc            = true;
		$this->exclude_post_types  = [ 'attachment', 'revision', 'nav_menu_item', 'safecss' ];
		$this->collision_collector = [];

		// get options
		$this->defaults = [  // default options
			'fragment_prefix'                    => 'i',
			'position'                           => TOC_POSITION_BEFORE_FIRST_HEADING,
			'start'                              => 3,
			'show_heading_text'                  => true,
			'heading_text'                       => 'Contents',
			'heading_alignment'                  => 'left',
			'auto_insert_post_types'             => [ 'post', 'page' ],
			'show_heirarchy'                     => true,
			'ordered_list'                       => true,
			'numbering_style'                    => 'hierarchical',
			'smooth_scroll'                      => true,
			'smooth_scroll_offset'               => TOC_SMOOTH_SCROLL_OFFSET,
			'visibility'                         => true,
			'visibility_show'                    => 'show',
			'visibility_hide'                    => 'hide',
			'toggle_style'                       => 'plus_minus',
			'visibility_hide_by_default'         => false,
			'width'                              => 'Auto',
			'width_custom'                       => '275',
			'width_custom_units'                 => 'px',
			'wrapping'                           => TOC_WRAPPING_NONE,
			'font_size'                          => '95',
			'font_size_units'                    => '%',
			'theme'                              => TOC_THEME_GREY,
			'custom_background_colour'           => TOC_DEFAULT_BACKGROUND_COLOUR,
			'custom_border_colour'               => TOC_DEFAULT_BORDER_COLOUR,
			'custom_title_colour'                => TOC_DEFAULT_TITLE_COLOUR,
			'custom_links_colour'                => TOC_DEFAULT_LINKS_COLOUR,
			'custom_links_hover_colour'          => TOC_DEFAULT_LINKS_HOVER_COLOUR,
			'custom_links_visited_colour'        => TOC_DEFAULT_LINKS_VISITED_COLOUR,
			'lowercase'                          => true,
			'hyphenate'                          => true,
			'bullet_spacing'                     => false,
			'include_homepage'                   => false,
			'exclude_css'                        => false,
			'exclude'                            => '', // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			'heading_levels'                     => [ 1, 2, 3, 4, 5, 6 ],
			'restrict_path'                      => '',
			'css_container_class'                => '',
			'sitemap_show_page_listing'          => true,
			'sitemap_show_category_listing'      => true,
			'sitemap_heading_type'               => 3,
			'sitemap_pages'                      => 'Pages',
			'sitemap_categories'                 => 'Categories',
			'show_toc_in_widget_only'            => false,
			'show_toc_in_widget_only_post_types' => [ 'page' ],
			'rest_toc_output'                    => false,
		];

		$options       = get_option( 'toc-options', $this->defaults );
		$this->options = wp_parse_args( $options, $this->defaults );

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
		add_action( 'delete_widget', [ $this, 'sidebar_admin_setup' ], 10, 3 );
		add_action( 'init', [ $this, 'init' ] );

		add_filter( 'the_content', [ $this, 'the_content' ], 100 );  // run after shortcodes are interpreted (level 10)
		add_filter( 'widget_text', 'do_shortcode' );

		add_shortcode( 'toc', [ $this, 'shortcode_toc' ] );
		add_shortcode( 'no_toc', [ $this, 'shortcode_no_toc' ] );
		add_shortcode( 'sitemap', [ $this, 'shortcode_sitemap' ] );
		add_shortcode( 'sitemap_pages', [ $this, 'shortcode_sitemap_pages' ] );
		add_shortcode( 'sitemap_categories', [ $this, 'shortcode_sitemap_categories' ] );
		add_shortcode( 'sitemap_posts', [ $this, 'shortcode_sitemap_posts' ] );
	}


	public function __destruct() {}


	public function get_options() {
		return $this->options;
	}


	public function set_option( $options ) {
		$this->options = array_merge( $this->options, $options );
	}


	/**
	 * Allows the developer to disable TOC execution
	 */
	public function disable() {
		$this->show_toc = false;
	}


	/**
	 * Allows the developer to enable TOC execution
	 */
	public function enable() {
		$this->show_toc = true;
	}


	public function set_show_toc_in_widget_only( $value = false ) {
		if ( $value ) {
			$this->options['show_toc_in_widget_only'] = true;
		} else {
			$this->options['show_toc_in_widget_only'] = false;
		}

		update_option( 'toc-options', $this->options );
	}


	public function set_show_toc_in_widget_only_post_types( $value = false ) {
		if ( $value ) {
			$this->options['show_toc_in_widget_only_post_types'] = $value;
		} else {
			$this->options['show_toc_in_widget_only_post_types'] = [];
		}

		update_option( 'toc-options', $this->options );
	}


	public function get_exclude_post_types() {
		return $this->exclude_post_types;
	}


	public function plugin_action_links( $links, $file ) {
		if ( 'table-of-contents-plus/toc.php' === $file ) {
			$settings_link = '<a href="options-general.php?page=toc">' . __( 'Settings', 'table-of-contents-plus' ) . '</a>';
			$links         = array_merge( [ $settings_link ], $links );
		}
		return $links;
	}


	public function shortcode_toc( $attributes ) {
		$atts = shortcode_atts(
			[
				'label'          => $this->options['heading_text'],
				'label_show'     => $this->options['visibility_show'],
				'label_hide'     => $this->options['visibility_hide'],
				'no_label'       => false,
				'class'          => false,
				'wrapping'       => $this->options['wrapping'],
				'heading_levels' => $this->options['heading_levels'],
				'exclude'        => $this->options['exclude'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'collapse'       => false,
				'no_numbers'     => false,
				'start'          => $this->options['start'],
			],
			$attributes
		);

		$re_enqueue_scripts = false;

		if ( $atts['no_label'] ) {
			$this->options['show_heading_text'] = false;
		}
		if ( $atts['label'] ) {
			$this->options['heading_text'] = wp_kses_post( html_entity_decode( $atts['label'] ) );
		}
		if ( $atts['label_show'] ) {
			$this->options['visibility_show'] = wp_strip_all_tags( $atts['label_show'] );
			$re_enqueue_scripts               = true;
		}
		if ( $atts['label_hide'] ) {
			$this->options['visibility_hide'] = wp_strip_all_tags( $atts['label_hide'] );
			$re_enqueue_scripts               = true;
		}
		if ( $atts['class'] ) {
			$this->options['css_container_class'] = wp_kses_post( html_entity_decode( $atts['class'] ) );
		}
		if ( $atts['wrapping'] ) {
			switch ( strtolower( trim( $atts['wrapping'] ) ) ) {
				case 'left':
					$this->options['wrapping'] = TOC_WRAPPING_LEFT;
					break;

				case 'right':
					$this->options['wrapping'] = TOC_WRAPPING_RIGHT;
					break;

				default:
					// do nothing
			}
		}

		if ( $atts['exclude'] ) {
			$this->options['exclude'] = $atts['exclude']; // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
		}
		if ( $atts['collapse'] ) {
			$this->options['visibility_hide_by_default'] = true;
			$re_enqueue_scripts                          = true;
		}

		if ( $atts['no_numbers'] ) {
			$this->options['ordered_list'] = false;
		}

		if ( is_numeric( $atts['start'] ) ) {
			$this->options['start'] = $atts['start'];
		}

		if ( $re_enqueue_scripts ) {
			wp_deregister_script( 'toc-front' );
			do_action( 'wp_enqueue_scripts' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}

		// if $atts['heading_levels'] is an array, then it came from the global options
		// and wasn't provided by per instance
		if ( $atts['heading_levels'] && ! is_array( $atts['heading_levels'] ) ) {
			// make sure they are numbers between 1 and 6 and put into
			// the $clean_heading_levels array if not already
			$clean_heading_levels = [];
			foreach ( explode( ',', $atts['heading_levels'] ) as $heading_level ) {
				if ( is_numeric( $heading_level ) ) {
					$heading_level = (int) $heading_level;
					if ( 1 <= $heading_level && $heading_level <= 6 ) {
						if ( ! in_array( $heading_level, $clean_heading_levels, true ) ) {
							$clean_heading_levels[] = (int) $heading_level;
						}
					}
				}
			}

			if ( count( $clean_heading_levels ) > 0 ) {
				$this->options['heading_levels'] = $clean_heading_levels;
			}
		}

		if ( ! is_search() && ! is_archive() && ! is_feed() ) {
			return '<!--TOC-->';
		} else {
			return '';
		}
	}


	public function shortcode_no_toc( $atts ) {
		$this->show_toc = false;

		return '';
	}


	public function shortcode_sitemap( $atts ) {
		$html = '';

		// only do the following if enabled
		if ( $this->options['sitemap_show_page_listing'] || $this->options['sitemap_show_category_listing'] ) {
			$html = '<div class="toc_sitemap">';
			if ( $this->options['sitemap_show_page_listing'] ) {
				$html .=
					'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_pages">' . htmlentities( $this->options['sitemap_pages'], ENT_COMPAT, 'UTF-8' ) . '</h' . $this->options['sitemap_heading_type'] . '>' .
					'<ul class="toc_sitemap_pages_list">' .
						wp_list_pages(
							[
								'title_li' => '',
								'echo'     => false,
							]
						) .
					'</ul>';
			}
			if ( $this->options['sitemap_show_category_listing'] ) {
				$html .=
					'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_categories">' . htmlentities( $this->options['sitemap_categories'], ENT_COMPAT, 'UTF-8' ) . '</h' . $this->options['sitemap_heading_type'] . '>' .
					'<ul class="toc_sitemap_categories_list">' .
						wp_list_categories(
							[
								'title_li' => '',
								'echo'     => false,
							]
						) .
					'</ul>';
			}
			$html .= '</div>';
		}

		return $html;
	}


	public function shortcode_sitemap_pages( $attributes ) {
		$atts = shortcode_atts(
			[
				'heading'      => $this->options['sitemap_heading_type'],
				'label'        => $this->options['sitemap_pages'],
				'no_label'     => false,
				'exclude'      => '', // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'exclude_tree' => '',
				'child_of'     => 0,
				'post_type'    => 'page',
			],
			$attributes
		);

		$atts['heading'] = intval( $atts['heading'] );  // make sure it's an integer

		if ( $atts['heading'] < 1 || $atts['heading'] > 6 ) {  // h1 to h6 are valid
			$atts['heading'] = $this->options['sitemap_heading_type'];
		}

		if ( ! post_type_exists( $atts['post_type'] ) ) {
			$atts['post_type'] = 'page';
		}

		if ( 'current' === strtolower( $atts['child_of'] ) ) {
			$atts['child_of'] = get_the_ID();
		} elseif ( is_numeric( $atts['child_of'] ) ) {
			$atts['child_of'] = intval( $atts['child_of'] );
		} else {
			$atts['child_of'] = 0;
		}

		$html = '<div class="toc_sitemap">';
		if ( ! $atts['no_label'] ) {
			$html .= '<h' . $atts['heading'] . ' class="toc_sitemap_pages">' . htmlentities( $atts['label'], ENT_COMPAT, 'UTF-8' ) . '</h' . $atts['heading'] . '>';
		}
		$html .=
				'<ul class="toc_sitemap_pages_list">' .
					wp_list_pages(
						[
							'title_li'     => '',
							'echo'         => false,
							'exclude'      => $atts['exclude'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
							'exclude_tree' => $atts['exclude_tree'],
							'hierarchical' => true,
							'child_of'     => $atts['child_of'],
							'post_type'    => $atts['post_type'],
						]
					) .
				'</ul>' .
			'</div>';

		return $html;
	}


	public function shortcode_sitemap_categories( $attributes ) {
		$atts = shortcode_atts(
			[
				'heading'      => $this->options['sitemap_heading_type'],
				'label'        => $this->options['sitemap_categories'],
				'no_label'     => false,
				'exclude'      => '', // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'exclude_tree' => '',
			],
			$attributes
		);

		$atts['heading'] = intval( $atts['heading'] );  // make sure it's an integer

		if ( $atts['heading'] < 1 || $atts['heading'] > 6 ) {  // h1 to h6 are valid
			$atts['heading'] = $this->options['sitemap_heading_type'];
		}

		$html = '<div class="toc_sitemap">';
		if ( ! $atts['no_label'] ) {
			$html .= '<h' . $atts['heading'] . ' class="toc_sitemap_categories">' . htmlentities( $atts['label'], ENT_COMPAT, 'UTF-8' ) . '</h' . $atts['heading'] . '>';
		}
		$html .=
				'<ul class="toc_sitemap_categories_list">' .
					wp_list_categories(
						[
							'title_li'     => '',
							'echo'         => false,
							'exclude'      => $atts['exclude'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
							'exclude_tree' => $atts['exclude_tree'],
						]
					) .
				'</ul>' .
			'</div>';

		return $html;
	}


	public function shortcode_sitemap_posts( $attributes ) {
		$atts = shortcode_atts(
			[
				'order'     => 'ASC',
				'orderby'   => 'title',
				'separate'  => true,
				'post_type' => 'post',
			],
			$attributes
		);

		if ( ! post_type_exists( $atts['post_type'] ) ) {
			$atts['post_type'] = 'post';
		}

		$articles = new \WP_Query(
			[
				'post_type'      => $atts['post_type'],
				'post_status'    => 'publish',
				'order'          => $atts['order'],
				'orderby'        => $atts['orderby'],
				'posts_per_page' => -1,
			]
		);

		$html   = '';
		$letter = '';

		$atts['separate'] = strtolower( $atts['separate'] );
		if ( 'false' === $atts['separate'] || 'no' === $atts['separate'] ) {
			$atts['separate'] = false;
		}

		while ( $articles->have_posts() ) {
			$articles->the_post();
			$title = wp_strip_all_tags( get_the_title() );

			if ( $atts['separate'] ) {
				if ( strtolower( $title[0] ) !== $letter ) {
					if ( $letter ) {
						$html .= '</ul></div>';
					}

					$html  .= '<div class="toc_sitemap_posts_section"><p class="toc_sitemap_posts_letter">' . strtolower( $title[0] ) . '</p><ul class="toc_sitemap_posts_list">';
					$letter = strtolower( $title[0] );
				}
			}

			$html .= '<li><a href="' . get_permalink( $articles->post->ID ) . '">' . $title . '</a></li>';
		}

		if ( $html ) {
			if ( $atts['separate'] ) {
				$html .= '</div>';
			} else {
				$html = '<div class="toc_sitemap_posts_section"><ul class="toc_sitemap_posts_list">' . $html . '</ul></div>';
			}
		}

		wp_reset_postdata();

		return $html;
	}


	/**
	 * Register and load CSS and javascript files for frontend.
	 */
	public function wp_enqueue_scripts() {
		$js_vars = [];

		wp_register_style( 'toc-screen', TOC_PLUGIN_PATH . '/assets/screen.min.css', [], TOC_VERSION );
		wp_register_script( 'toc-front', TOC_PLUGIN_PATH . '/assets/front.min.js', [ 'jquery' ], TOC_VERSION, true );

		// enqueue them!
		if ( ! $this->options['exclude_css'] ) {
			wp_enqueue_style( 'toc-screen' );

			// add any admin GUI customisations
			$custom_css = $this->get_custom_css();
			if ( $custom_css ) {
				wp_add_inline_style( 'toc-screen', $custom_css );
			}
		}

		if ( $this->options['smooth_scroll'] ) {
			$js_vars['smooth_scroll'] = true;
		}
		wp_enqueue_script( 'toc-front' );
		if ( $this->options['show_heading_text'] && $this->options['visibility'] ) {
			$width = ( 'User defined' !== $this->options['width'] ) ? $this->options['width'] : $this->options['width_custom'] . $this->options['width_custom_units'];

			// Plain-text labels: WP_Scripts::localize() entity-decodes before JSON-encoding,
			// so esc_js() here would only hand the frontend back the quotes it neutralised.
			$js_vars['visibility_show'] = wp_strip_all_tags( $this->options['visibility_show'] );
			$js_vars['visibility_hide'] = wp_strip_all_tags( $this->options['visibility_hide'] );
			$js_vars['toggle_style']    = esc_js( $this->options['toggle_style'] );
			if ( $this->options['visibility_hide_by_default'] ) {
				$js_vars['visibility_hide_by_default'] = true;
			}
			$js_vars['width'] = esc_js( $width );
		}
		if ( TOC_SMOOTH_SCROLL_OFFSET !== $this->options['smooth_scroll_offset'] ) {
			$js_vars['smooth_scroll_offset'] = esc_js( $this->options['smooth_scroll_offset'] );
		}

		if ( count( $js_vars ) > 0 ) {
			wp_localize_script(
				'toc-front',
				'tocplus',
				$js_vars
			);
		}
	}


	public function plugins_loaded() {}


	public function widgets_init() {
		register_widget( Widget::class );
	}


	/**
	 * Remove widget options on widget deletion
	 */
	public function sidebar_admin_setup( $widget_id, $sidebar_id, $id_base ) {
		// If we aren't trying to delete a TOC widget, return early.
		if ( 'toc-widget' !== $id_base ) {
			return;
		}

		// this action is loaded at the start of the widget screen
		// so only do the following when a form action has been initiated
		$this->set_show_toc_in_widget_only( false );
		$this->set_show_toc_in_widget_only_post_types( [ 'page' ] );
	}


	public function init() {
		// Add compatibility with Rank Math SEO
		if ( class_exists( 'RankMath' ) ) {
			add_filter(
				'rank_math/researches/toc_plugins',
				function ( $toc_plugins ) {
					$toc_plugins['table-of-contents-plus/toc.php'] = 'Table of Contents Plus';
					return $toc_plugins;
				}
			);
		}
	}


	public function get_defaults() {
		return $this->defaults;
	}


	/**
	 * Tries to convert $input into a valid hex colour.
	 * Returns $default if $input is not a hex value, otherwise returns verified hex.
	 */
	public function hex_value( $input = '', $default = '#' ) {
		$return = $default;

		if ( $input ) {
			// strip out non hex chars
			$return = preg_replace( '/[^a-fA-F0-9]*/', '', $input );

			switch ( strlen( $return ) ) {
				case 3:  // do next
				case 6:
					$return = '#' . $return;
					break;

				default:
					if ( strlen( $return ) > 6 ) {
						$return = '#' . substr( $return, 0, 6 );  // if > 6 chars, then take the first 6
					} elseif ( strlen( $return ) > 3 && strlen( $return ) < 6 ) {
						$return = '#' . substr( $return, 0, 3 );  // if between 3 and 6, then take first 3
					} else {
						$return = $default;  // not valid, return $default
					}
			}
		}

		return $return;
	}


	/**
	 * Returns a string of custom CSS based on appearance options the
	 * user selected in the admin GUI.
	 */
	private function get_custom_css() {
		$css = '';

		if ( ! $this->options['exclude_css'] ) {
			if ( TOC_THEME_CUSTOM === $this->options['theme'] || 'Auto' !== $this->options['width'] ) {
				$css .= 'div#toc_container {';
				if ( TOC_THEME_CUSTOM === $this->options['theme'] ) {
					$css .= 'background: ' . $this->options['custom_background_colour'] . ';border: 1px solid ' . $this->options['custom_border_colour'] . ';';
				}
				if ( 'Auto' !== $this->options['width'] ) {
					$css .= 'width: ';
					if ( 'User defined' !== $this->options['width'] ) {
						$css .= $this->options['width'];
					} else {
						$css .= $this->options['width_custom'] . $this->options['width_custom_units'];
					}
					$css .= ';';
				}
				$css .= '}';
			}

			if ( '95%' !== $this->options['font_size'] . $this->options['font_size_units'] ) {
				$css .= 'div#toc_container ul li {font-size: ' . $this->options['font_size'] . $this->options['font_size_units'] . ';}';
			}

			if ( TOC_THEME_CUSTOM === $this->options['theme'] ) {
				if ( TOC_DEFAULT_TITLE_COLOUR !== $this->options['custom_title_colour'] ) {
					$css .= 'div#toc_container .toc_title {color: ' . $this->options['custom_title_colour'] . ';}';
				}
				if ( TOC_DEFAULT_LINKS_COLOUR !== $this->options['custom_links_colour'] ) {
					$css .= 'div#toc_container .toc_title a,div#toc_container ul.toc_list a {color: ' . $this->options['custom_links_colour'] . ';}';
				}
				if ( TOC_DEFAULT_LINKS_HOVER_COLOUR !== $this->options['custom_links_hover_colour'] ) {
					$css .= 'div#toc_container .toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
				}
				if ( TOC_DEFAULT_LINKS_HOVER_COLOUR !== $this->options['custom_links_hover_colour'] ) {
					$css .= 'div#toc_container .toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
				}
				if ( TOC_DEFAULT_LINKS_VISITED_COLOUR !== $this->options['custom_links_visited_colour'] ) {
					$css .= 'div#toc_container .toc_title a:visited,div#toc_container ul.toc_list a:visited {color: ' . $this->options['custom_links_visited_colour'] . ';}';
				}
			}
		}

		return $css;
	}


	/**
	 * Returns a clean url to be used as the destination anchor target
	 */
	private function url_anchor_target( $title ) {
		$return = false;

		if ( $title ) {
			$return = trim( wp_strip_all_tags( $title ) );

			// convert accented characters to ASCII
			$return = remove_accents( $return );

			// replace newlines with spaces (eg when headings are split over multiple lines)
			$return = str_replace( [ "\r", "\n", "\n\r", "\r\n" ], ' ', $return );

			// remove &amp;
			$return = str_replace( '&amp;', '', $return );

			// remove non alphanumeric chars
			$return = preg_replace( '/[^a-zA-Z0-9 \-_]*/', '', $return );

			// convert spaces to _
			$return = str_replace(
				[ '  ', ' ' ],
				'_',
				$return
			);

			// remove trailing - and _
			$return = rtrim( $return, '-_' );

			// lowercase everything?
			if ( $this->options['lowercase'] ) {
				$return = strtolower( $return );
			}

			// if blank, then prepend with the fragment prefix
			// blank anchors normally appear on sites that don't use the latin charset
			if ( ! $return ) {
				$return = ( $this->options['fragment_prefix'] ) ? wp_kses_post( $this->options['fragment_prefix'] ) : '_';
			}

			// hyphenate?
			if ( $this->options['hyphenate'] ) {
				$return = str_replace( '_', '-', $return );
				$return = str_replace( '--', '-', $return );
			}
		}

		if ( array_key_exists( $return, $this->collision_collector ) ) {
			$this->collision_collector[ $return ]++;
			$return .= '-' . $this->collision_collector[ $return ];
		} else {
			$this->collision_collector[ $return ] = 1;
		}

		return apply_filters( 'toc_url_anchor_target', $return ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	}


	private function build_hierarchy( &$matches ) {
		$current_depth      = 100;  // headings can't be larger than h6 but 100 as a default to be sure
		$html               = '';
		$numbered_items     = [];
		$numbered_items_min = null;
		$count_matches      = count( $matches );

		// reset the internal collision collection
		$this->collision_collector = [];

		// find the minimum heading to establish our baseline
		for ( $i = 0; $i < $count_matches; $i++ ) {
			if ( $current_depth > $matches[ $i ][2] ) {
				$current_depth = (int) $matches[ $i ][2];
			}
		}

		$numbered_items[ $current_depth ] = 0;
		$numbered_items_min               = $current_depth;

		for ( $i = 0; $i < $count_matches; $i++ ) {

			if ( $current_depth === (int) $matches[ $i ][2] ) {
				$html .= '<li>';
			}

			// start lists
			if ( $current_depth !== (int) $matches[ $i ][2] ) {
				for ( $current_depth; $current_depth < (int) $matches[ $i ][2]; $current_depth++ ) {
					$numbered_items[ $current_depth + 1 ] = 0;
					$html                                .= '<ul><li>';
				}
			}

			// list item
			if ( in_array( (int) $matches[ $i ][2], $this->options['heading_levels'], true ) ) {
				$html .= '<a href="#' . $this->url_anchor_target( $matches[ $i ][0] ) . '">';
				if ( $this->options['ordered_list'] ) {
					// attach leading numbers when lower in hierarchy
					$html .= '<span class="toc_number toc_depth_' . ( $current_depth - $numbered_items_min + 1 ) . '">';

					$style          = $this->options['numbering_style'];
					$current_number = $numbered_items[ $current_depth ] + 1;

					if ( 'hierarchical' === $style ) {
						// Prefix each item with its ancestors, e.g. 1.2.3.
						for ( $j = $numbered_items_min; $j < $current_depth; $j++ ) {
							$ancestor = ( $numbered_items[ $j ] ) ? $numbered_items[ $j ] : 0;
							$html    .= $ancestor . '.';
						}
						$html .= $current_number;
					} else {
						// The remaining styles restart the count within each level,
						// formatted as decimal, Roman numerals or letters.
						$html .= $this->format_number( $current_number, $style );
					}

					// Trailing period, matching native ordered-list / outline numbering.
					$html .= '.</span> ';
					$numbered_items[ $current_depth ]++;
				}
				$html .= wp_strip_all_tags( $matches[ $i ][0] ) . '</a>';
			}

			// end lists
			if ( count( $matches ) - 1 !== $i ) {
				if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {
					for ( $current_depth; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth-- ) {
						$html                            .= '</li></ul>';
						$numbered_items[ $current_depth ] = 0;
					}
				}

				if ( (int) @$matches[ $i + 1 ][2] === $current_depth ) {
					$html .= '</li>';
				}
			} else {
				// this is the last item, make sure we close off all tags
				for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {
					$html .= '</li>';
					if ( $current_depth !== $numbered_items_min ) {
						$html .= '</ul>';
					}
				}
			}
		}

		return $html;
	}


	/**
	 * Returns a string with all items from the $find array replaced with their matching
	 * items in the $replace array.  This does a one to one replacement (rather than
	 * globally).
	 *
	 * This function is multibyte safe.
	 *
	 * $find and $replace are arrays, $string is the haystack.  All variables are
	 * passed by reference.
	 */
	private function mb_find_replace( &$find = false, &$replace = false, &$string = '' ) {
		if ( is_array( $find ) && is_array( $replace ) && $string ) {
			$count_find = count( $find );
			// check if multibyte strings are supported
			if ( function_exists( 'mb_strpos' ) ) {
				for ( $i = 0; $i < $count_find; $i++ ) {
					$string =
						mb_substr( $string, 0, mb_strpos( $string, $find[ $i ] ) ) . // everything before $find
						$replace[ $i ] . // its replacement
						mb_substr( $string, mb_strpos( $string, $find[ $i ] ) + mb_strlen( $find[ $i ] ) ); // everything after $find
				}
			} else {
				for ( $i = 0; $i < $count_find; $i++ ) {
					$string = substr_replace(
						$string,
						$replace[ $i ],
						strpos( $string, $find[ $i ] ),
						strlen( $find[ $i ] )
					);
				}
			}
		}

		return $string;
	}


	/**
	 * This function extracts headings from the html formatted $content.  It will pull out
	 * only the required headings as specified in the options.  For all qualifying headings,
	 * this function populates the $find and $replace arrays (both passed by reference)
	 * with what to search and replace with.
	 *
	 * Returns a html formatted string of list items for each qualifying heading.  This
	 * is everything between and NOT including <ul> and </ul>
	 */
	public function extract_headings( &$find, &$replace, $content = '' ) {
		$matches = [];
		$anchor  = '';
		$items   = false;

		// reset the internal collision collection as the_content may have been triggered elsewhere
		// eg by themes or other plugins that need to read in content such as metadata fields in
		// the head html tag, or to provide descriptions to twitter/facebook
		$this->collision_collector = [];

		if ( is_array( $find ) && is_array( $replace ) && $content ) {
			// filter the content
			$content = apply_filters( 'toc_extract_headings', $content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			// get all headings
			// the html spec allows for a maximum of 6 heading depths
			if ( preg_match_all( '/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER ) ) {

				// remove undesired headings (if any) as defined by heading_levels
				if ( count( $this->options['heading_levels'] ) !== 6 ) {
					$new_matches   = [];
					$count_matches = count( $matches );
					for ( $i = 0; $i < $count_matches; $i++ ) {
						if ( in_array( (int) $matches[ $i ][2], $this->options['heading_levels'], true ) ) {
							$new_matches[] = $matches[ $i ];
						}
					}
					$matches = $new_matches;
				}

				// remove specific headings if provided via the 'exclude' property
				if ( $this->options['exclude'] ) {
					$excluded_headings       = explode( '|', $this->options['exclude'] );
					$count_excluded_headings = count( $excluded_headings );
					if ( $count_excluded_headings > 0 ) {
						for ( $j = 0; $j < $count_excluded_headings; $j++ ) {
							// escape regular expression characters, but keep * as a wildcard
							$excluded_headings[ $j ] = str_replace(
								'\*',
								'.*',
								preg_quote( trim( $excluded_headings[ $j ] ), '/' )
							);
						}

						$new_matches   = [];
						$count_matches = count( $matches );
						for ( $i = 0; $i < $count_matches; $i++ ) {
							$found                   = false;
							$count_excluded_headings = count( $excluded_headings );
							for ( $j = 0; $j < $count_excluded_headings; $j++ ) {
								// decode entities so e.g. an exclusion with & matches &amp; in the content
								if ( @preg_match( '/^' . $excluded_headings[ $j ] . '$/imUu', wp_specialchars_decode( wp_strip_all_tags( $matches[ $i ][0] ), ENT_QUOTES ) ) ) {
									$found = true;
									break;
								}
							}
							if ( ! $found ) {
								$new_matches[] = $matches[ $i ];
							}
						}
						if ( count( $matches ) !== count( $new_matches ) ) {
							$matches = $new_matches;
						}
					}
				}

				// remove empty headings
				$new_matches   = [];
				$count_matches = count( $matches );
				for ( $i = 0; $i < $count_matches; $i++ ) {
					if ( '' !== trim( wp_strip_all_tags( $matches[ $i ][0] ) ) ) {
						$new_matches[] = $matches[ $i ];
					}
				}
				if ( count( $matches ) !== count( $new_matches ) ) {
					$matches = $new_matches;
				}

				// check minimum number of headings
				if ( count( $matches ) >= $this->options['start'] ) {
					$count_matches = count( $matches );
					for ( $i = 0; $i < $count_matches; $i++ ) {
						// get anchor and add to find and replace arrays
						$anchor    = $this->url_anchor_target( $matches[ $i ][0] );
						$find[]    = $matches[ $i ][0];
						$replace[] = str_replace(
							[
								$matches[ $i ][1], // start of heading
								'</h' . $matches[ $i ][2] . '>', // end of heading
							],
							[
								$matches[ $i ][1] . '<span id="' . $anchor . '">',
								'</span></h' . $matches[ $i ][2] . '>',
							],
							$matches[ $i ][0]
						);

						// assemble flat list
						if ( ! $this->options['show_heirarchy'] ) {
							$items .= '<li><a href="#' . $anchor . '">';
							if ( $this->options['ordered_list'] ) {
								$items .= count( $replace ) . ' ';
							}
							$items .= wp_strip_all_tags( $matches[ $i ][0] ) . '</a></li>';
						}
					}

					// build a hierarchical toc?
					// we could have tested for $items but that var can be quite large in some cases
					if ( $this->options['show_heirarchy'] ) {
						$items = $this->build_hierarchy( $matches );
					}
				}
			}
		}

		return $items;
	}


	/**
	 * Returns true if the table of contents is eligible to be printed, false otherwise.
	 * $has_shortcode can be passed in when the shortcode was already detected in the filtered
	 * content, which catches [toc] inside reusable blocks where the raw post content only
	 * contains a block reference.
	 */
	public function is_eligible( $has_shortcode = false ) {
		global $post;

		$custom_toc_position = $has_shortcode || ( isset( $post->post_content ) ? has_shortcode( $post->post_content, 'toc' ) : false );

		// Do not trigger the TOC on REST Requests unless explicitly enabled.
		// This ensures that the TOC is not included in REST API responses by default.
		// If the TOC inclusion in REST API responses is desired,
		// it must be specifically activated via the plugin settings.
		if ( ! $this->options['rest_toc_output'] ) {
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return false;
			}
		}

		// do not trigger the TOC when displaying an XML/RSS feed
		if ( is_feed() ) {
			return false;
		}

		// if the shortcode was used, this bypasses many of the global options
		if ( false !== $custom_toc_position ) {
			// shortcode is used, make sure it adheres to the exclude from
			// homepage option if we're on the homepage
			if ( ! $this->options['include_homepage'] && is_front_page() ) {
				return false;
			} else {
				return true;
			}
		} else {
			if (
				( in_array( get_post_type( $post ), $this->options['auto_insert_post_types'], true ) && $this->show_toc && ! is_search() && ! is_archive() && ! is_home() && ! is_front_page() ) ||
				( $this->options['include_homepage'] && is_front_page() )
			) {
				if ( $this->options['restrict_path'] ) {
					$requestUri = ! empty( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

					// The restrict path is relative to the site root, but REQUEST_URI is relative to the domain root.
					// Strip the home path prefix so the option also works on subdirectory installs.
					$homePath = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
					if ( $homePath && strpos( $requestUri, $homePath . '/' ) === 0 ) {
						$requestUri = substr( $requestUri, strlen( $homePath ) );
					}

					if ( strpos( $requestUri, $this->options['restrict_path'] ) === 0 ) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			} else {
				return false;
			}
		}
	}


	/**
	 * Formats a per-level counter for the given numbering style.
	 *
	 * @since 202607.1
	 *
	 * @param  int    $number The 1-based counter for the current level.
	 * @param  string $style  The numbering style.
	 * @return string         The formatted number.
	 */
	private function format_number( $number, $style ) {
		switch ( $style ) {
			case 'lower-roman':
				return strtolower( $this->to_roman( $number ) );
			case 'upper-roman':
				return $this->to_roman( $number );
			case 'lower-alpha':
				return strtolower( $this->to_alpha( $number ) );
			case 'upper-alpha':
				return $this->to_alpha( $number );
			case 'decimal':
			default:
				return (string) (int) $number;
		}
	}

	/**
	 * Converts a positive integer to an uppercase Roman numeral.
	 *
	 * @since 202607.1
	 *
	 * @param  int    $number The number to convert.
	 * @return string         The Roman numeral, or the number as-is when out of range.
	 */
	private function to_roman( $number ) {
		$number = (int) $number;
		if ( $number < 1 || $number > 3999 ) {
			return (string) $number;
		}

		$map    = [ 'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1 ];
		$result = '';
		foreach ( $map as $roman => $value ) {
			while ( $number >= $value ) {
				$result .= $roman;
				$number -= $value;
			}
		}

		return $result;
	}

	/**
	 * Converts a positive integer to an uppercase letter sequence (A, B, …, Z, AA, AB, …).
	 *
	 * @since 202607.1
	 *
	 * @param  int    $number The number to convert.
	 * @return string         The letter sequence, or the number as-is when out of range.
	 */
	private function to_alpha( $number ) {
		$number = (int) $number;
		if ( $number < 1 ) {
			return (string) $number;
		}

		$result = '';
		while ( $number > 0 ) {
			$number--;
			$result  = chr( 65 + ( $number % 26 ) ) . $result;
			$number  = intdiv( $number, 26 );
		}

		return $result;
	}

	/**
	 * Inserts the table of contents before or after the first paragraph in the content.
	 * Falls back to the top (before) or bottom (after) of the content when no paragraph is found.
	 */
	private function insert_relative_to_first_paragraph( $content, $html, $after ) {
		if ( ! preg_match( '/<p(\s[^>]*)?>/i', $content, $matches, PREG_OFFSET_CAPTURE ) ) {
			return $after ? $content . $html : $html . $content;
		}

		if ( ! $after ) {
			return substr_replace( $content, $html, $matches[0][1], 0 );
		}

		$closing = stripos( $content, '</p>', $matches[0][1] );
		if ( false === $closing ) {
			return $content . $html;
		}

		return substr_replace( $content, $html, $closing + 4, 0 );
	}


	public function the_content( $content ) {
		global $post;

		// Auto-generated excerpts run the content through this filter; the TOC does not belong there.
		if ( doing_filter( 'get_the_excerpt' ) ) {
			return $content;
		}
		$items               = '';
		$css_classes         = '';
		$anchor              = '';
		$find                = [];
		$replace             = [];
		$custom_toc_position = strpos( $content, '<!--TOC-->' );

		if ( $this->is_eligible( false !== $custom_toc_position ) ) {

			$items = $this->extract_headings( $find, $replace, $content );

			if ( $items ) {
				// do we display the toc within the content or has the user opted
				// to only show it in the widget?  if so, then we still need to
				// make the find/replace call to insert the anchors
				if ( $this->options['show_toc_in_widget_only'] && ( in_array( get_post_type(), $this->options['show_toc_in_widget_only_post_types'], true ) ) ) {
					$content = $this->mb_find_replace( $find, $replace, $content );
				} else {

					// wrapping css classes
					switch ( $this->options['wrapping'] ) {
						case TOC_WRAPPING_LEFT:
							$css_classes .= ' toc_wrap_left';
							break;

						case TOC_WRAPPING_RIGHT:
							$css_classes .= ' toc_wrap_right';
							break;

						case TOC_WRAPPING_NONE:
						default:
							// do nothing
					}

					// colour themes
					switch ( $this->options['theme'] ) {
						case TOC_THEME_LIGHT_BLUE:
							$css_classes .= ' toc_light_blue';
							break;

						case TOC_THEME_WHITE:
							$css_classes .= ' toc_white';
							break;

						case TOC_THEME_BLACK:
							$css_classes .= ' toc_black';
							break;

						case TOC_THEME_TRANSPARENT:
							$css_classes .= ' toc_transparent';
							break;

						case TOC_THEME_GREY:
						default:
							// do nothing
					}

					// bullets?
					if ( $this->options['bullet_spacing'] ) {
						$css_classes .= ' have_bullets';
					} else {
						$css_classes .= ' no_bullets';
					}

					// Numbered lists show their own numbers, so suppress the list bullets.
					if ( $this->options['ordered_list'] ) {
						$css_classes .= ' toc_numbered';
					}

					// Title alignment. Left is the stylesheet default, so only the
					// center/right variants need a class.
					if ( in_array( $this->options['heading_alignment'], [ 'center', 'right' ], true ) ) {
						$css_classes .= ' toc_title_' . $this->options['heading_alignment'];
					}

					if ( $this->options['css_container_class'] ) {
						$css_classes .= ' ' . $this->options['css_container_class'];
					}

					$css_classes = trim( $css_classes );

					// an empty class="" is invalid markup!
					if ( ! $css_classes ) {
						$css_classes = ' ';
					}

					// add container, toc title and list items
					// data-nosnippet prevents search engines from using the TOC as the result snippet
					$html = '<div id="toc_container" role="navigation" aria-label="' . esc_attr__( 'Table of Contents', 'table-of-contents-plus' ) . '" data-nosnippet class="' . esc_attr( $css_classes ) . '">';
					if ( $this->options['show_heading_text'] ) {
						$toc_title = $this->options['heading_text'];
						if ( false !== strpos( $toc_title, '%PAGE_TITLE%' ) ) {
							$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
						}
						if ( false !== strpos( $toc_title, '%PAGE_NAME%' ) ) {
							$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
						}
						// The title can carry intentional inline formatting from the [toc label] shortcode;
						// wp_kses_post renders it while stripping scripts and event handlers (XSS-safe).
						$toc_title = wp_kses_post( $toc_title );
						$title_tag = tag_escape( apply_filters( 'toc_title_tag', 'p' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
						$html     .= '<' . $title_tag . ' class="toc_title">' . $toc_title . '</' . $title_tag . '>';
					}
					$html .= '<ul class="toc_list">' . $items . '</ul></div>' . "\n";

					if ( false !== $custom_toc_position ) {
						$find[]    = '<!--TOC-->';
						$replace[] = $html;
						$content   = $this->mb_find_replace( $find, $replace, $content );
					} else {
						if ( count( $find ) > 0 ) {
							switch ( $this->options['position'] ) {
								case TOC_POSITION_TOP:
									$content = $html . $this->mb_find_replace( $find, $replace, $content );
									break;

								case TOC_POSITION_BOTTOM:
									$content = $this->mb_find_replace( $find, $replace, $content ) . $html;
									break;

								case TOC_POSITION_AFTER_FIRST_HEADING:
									$replace[0] = $replace[0] . $html;
									$content    = $this->mb_find_replace( $find, $replace, $content );
									break;

								case TOC_POSITION_BEFORE_FIRST_PARAGRAPH:
								case TOC_POSITION_AFTER_FIRST_PARAGRAPH:
									$content = $this->mb_find_replace( $find, $replace, $content );
									$content = $this->insert_relative_to_first_paragraph( $content, $html, TOC_POSITION_AFTER_FIRST_PARAGRAPH === $this->options['position'] );
									break;

								case TOC_POSITION_BEFORE_FIRST_HEADING:
								default:
									$replace[0] = $html . $replace[0];
									$content    = $this->mb_find_replace( $find, $replace, $content );
							}
						}
					}
				}
			}
		} else {
			// remove <!--TOC--> (inserted from shortcode) from content
			$content = str_replace( '<!--TOC-->', '', $content );
		}

		return $content;
	}

} // end class