<?php
namespace ElementsKit_Lite;

defined( 'ABSPATH' ) || exit;

/**
 * Global helper class.
 *
 * @since 1.0.0
 */

class Utils {

	/**
	 * Auto generate classname from path.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function make_classname( $dirname ) {
		$dirname    = pathinfo( $dirname, PATHINFO_FILENAME );
		$class_name = explode( '-', $dirname );
		$class_name = array_map( 'ucfirst', $class_name );
		$class_name = implode( '_', $class_name );

		return $class_name;
	}

	public static function google_fonts( $font_families = array() ) {
		$fonts_url = '';
		if ( $font_families ) {
			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

	public static function get_kses_array(){
		return array(
			'a'                             => array(
				'class'  => array(),
				'href'   => array(),
				'rel'    => array(),
				'title'  => array(),
				'target' => array(),
				'style'  => array(),
			),
			'abbr'                          => array(
				'title' => array(),
			),
			'b'                             => array(
                'class' => array(),
            ),
			'blockquote'                    => array(
				'cite' => array(),
			),
			'cite'                          => array(
				'title' => array(),
			),
			'code'                          => array(),
			'pre'                           => array(),
			'del'                           => array(
				'datetime' => array(),
				'title'    => array(),
			),
			'dd'                            => array(),
			'div'                           => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'dl'                            => array(),
			'dt'                            => array(),
			'em'                            => array(),
			'strong'                        => array(),
			'h1'                            => array(
				'class' => array(),
			),
			'h2'                            => array(
				'class' => array(),
			),
			'h3'                            => array(
				'class' => array(),
			),
			'h4'                            => array(
				'class' => array(),
			),
			'h5'                            => array(
				'class' => array(),
			),
			'h6'                            => array(
				'class' => array(),
			),
			'i'                             => array(
				'class' => array(),
			),
			'img'                           => array(
				'alt'		=> array(),
				'class'		=> array(),
				'height'	=> array(),
				'src'		=> array(),
				'width'		=> array(),
				'style'		=> array(),
				'title'		=> array(),
				'srcset'	=> array(),
				'loading'	=> array(),
				'sizes'		=> array(),
			),
			'figure'                        => array(
				'class'		=> array(),
			),
			'li'                            => array(
				'class' => array(),
			),
			'ol'                            => array(
				'class' => array(),
			),
			'p'                             => array(
				'class' => array(),
			),
			'q'                             => array(
				'cite'  => array(),
				'title' => array(),
			),
			'span'                          => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'iframe'                        => array(
				'width'       => array(),
				'height'      => array(),
				'scrolling'   => array(),
				'frameborder' => array(),
				'allow'       => array(),
				'src'         => array(),
			),
			'strike'                        => array(),
			'br'                            => array(),
			'table'                         => array(),
			'thead'                         => array(),
			'tbody'                         => array(),
			'tfoot'                         => array(),
			'tr'                            => array(),
			'th'                            => array(),
			'td'                            => array(),
			'colgroup'                      => array(),
			'col'                           => array(),
			'strong'                        => array(),
			'data-wow-duration'             => array(),
			'data-wow-delay'                => array(),
			'data-wallpaper-options'        => array(),
			'data-stellar-background-ratio' => array(),
			'ul'                            => array(
				'class' => array(),
			),
			'svg'                           => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true, // <= Must be lower case!
                'preserveaspectratio' => true,
			),
			'g'                             => array( 'fill' => true ),
			'title'                         => array( 'title' => true ),
			'path'                          => array(
				'd'    => true,
				'fill' => true,
			),
			'input'							=> array(
				'class'		=> array(), 
				'type'		=> array(), 
				'value'		=> array()
			)
		);
	}

	public static function kses( $raw ) {

		$allowed_tags = self::get_kses_array();

		if ( function_exists( 'wp_kses' ) ) { // WP is here
			return wp_kses( $raw, $allowed_tags );
		} else {
			return $raw;
		}
	}

	public static function kspan( $text ) {
		return str_replace( array( '{', '}' ), array( '<span>', '</span>' ), $text );
	}

	public static function ekit_get__forms( $post_type ) {
		$wpuf_form_list = get_posts(
			array(
				'post_type' => $post_type,
				'showposts' => 999,
			)
		);

		$options = array();

		if ( ! empty( $wpuf_form_list ) && ! is_wp_error( $wpuf_form_list ) ) {
			$options[0] = esc_html__( 'Select Form', 'elementskit-lite' );
			foreach ( $wpuf_form_list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		} else {
			$options[0] = esc_html__( 'Create a form first', 'elementskit-lite' );
		}

		return $options;
	}
	
	public static function ekit_get_ninja_form() {
		$options = array();

		if ( class_exists( 'Ninja_Forms' ) ) {
			$contact_forms = Ninja_Forms()->form()->get_forms();

			if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {

				$options[0] = esc_html__( 'Select Ninja Form', 'elementskit-lite' );

				foreach ( $contact_forms as $form ) {
					$options[ $form->get_id() ] = $form->get_setting( 'title' );
				}
			}
		} else {
			$options[0] = esc_html__( 'Create a Form First', 'elementskit-lite' );
		}

		return $options;
	}

	public static function tablepress_table_list() {
		$table_options = array();

		if ( class_exists( 'TablePress' ) ) {
			$table_ids        = \TablePress::$model_table->load_all( false );
			$table_options[0] = esc_html__( 'Select Table', 'elementskit-lite' );

			foreach ( $table_ids as $table_id ) {
				// Load table, without table data, options, and visibility settings.
				$table = \TablePress::$model_table->load( $table_id, false, false );
	
				if ( '' === trim( $table['name'] ) ) {
					$table['name'] = __( '(no name)', 'elementskit-lite' );
				}
				
				$table_options[ $table['id'] ] = $table['name'];
			}
		} else {
			$table_options[0] = esc_html__( 'Create a Table First', 'elementskit-lite' );
		}

		return $table_options;
	}
	
	public static function ekit_do_shortcode( $tag, array $atts = array(), $content = null ) {
		global $shortcode_tags;
		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}
		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}

	public static function trim_words( $text, $num_words ) {
		return wp_trim_words( $text, $num_words, '' );
	}

	public static function array_push_assoc( $array, $key, $value ) {
		$array[ $key ] = $value;
		return $array;
	}

	public static function render_elementor_content_css( $content_id ) {
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $content_id );
			$css_file->enqueue();
		}
	}

	public static function render_elementor_content( $content_id ) {
		$elementor_instance = \Elementor\Plugin::instance();
		$has_css            = false;

		/**
		 * CSS Print Method Internal and Exteral option support for Header and Footer Builder.
		 */
		if ( ( 'internal' === get_option( 'elementor_css_print_method' ) ) || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$has_css = true;
		}
		
		return $elementor_instance->frontend->get_builder_content_for_display( $content_id, $has_css );
	}
	
	public static function render( $content ) {
		if ( stripos( $content, 'elementskit-has-lisence' ) !== false ) {
			return null;
		}

		return $content;
	}
	
	public static function render_tab_content( $content, $id ) {
		return str_replace( '.elementor-' . $id . ' ', '#elementor .elementor-' . $id . ' ', $content );
	}

	public static function img_meta( $id ) {
		$attachment = get_post( $id );
		if ( $attachment == null || $attachment->post_type != 'attachment' ) {
			return null;
		}
		return array(
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href'        => get_permalink( $attachment->ID ),
			'src'         => $attachment->guid,
			'title'       => $attachment->post_title,
		);
	}

	public static function esc_options( $str, $options = array(), $default = '' ) {
		if ( ! in_array( $str, $options ) ) {
			return $default;
		}

		return $str;
	}

	public static function get_attachment_image_html( $settings, $image_key, $image_size_key = null, $image_attr = array() ) {
		if ( ! $image_key ) {
			$image_key = $image_size_key;
		}

		$image = $settings[ $image_key ];

		$size = $image_size_key;

		$html = '';
		if ( ! empty( $image['id'] ) && $image['id'] != '-1' ) {
			$html .= wp_get_attachment_image( $image['id'], $size, false, $image_attr );
		} else {
			$html .= sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image['url'] ), \Elementor\Control_Media::get_image_title( $image ), \Elementor\Control_Media::get_image_alt( $image ) );
		}

		$html = preg_replace( array( '/max-width:[^"]*;/', '/width:[^"]*;/', '/height:[^"]*;/' ), '', $html );

		return $html;
	}

	public static function swiper_class() {
		$swiper_class = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		return 'ekit-main-swiper ' . $swiper_class;
	}

	public static function get_page_by_title( $page_title, $post_type = 'page' ) {
		$query = new \WP_Query(
			array(
				'post_type' => $post_type,
				'title' => $page_title,
			)
		);

		if (!empty($query->post)) {
			$page_got_by_title = $query->post;
		} else {
			$page_got_by_title = null;
		}

		return $page_got_by_title;
	}
}
