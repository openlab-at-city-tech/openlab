<?php

if ( ! class_exists( 'WPChill_Welcome' ) ) {

	class WPChill_Welcome {

		/**
		 * Contains the instance of the Class
		 *
		 * @since 1.0.0
		 * @param WPChill_Welcome $instance
		 */
		private static $instance = null;

		/**
		 * @since 1.0.0
		 * @param string $textdomain - wpchill textdomain
		 */
		public $textdomain = 'wpchill';

		private function __construct() {
			add_action( 'admin_footer', array( $this, 'welcome_style' ) );
		}

		/**
		 * @since 1.0.0
		 * Singleton
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPChill_Welcome ) ) {
				self::$instance = new WPChill_Welcome();
			}

			return self::$instance;
		}

		/**
		 * @since 1.0.0
		 * Enqueue admin Wellcome style
		 */
		public function welcome_style() {
			wp_register_style( 'wpchill-welcome-style', plugins_url( '/assets/css/wpchill-welcome.css', __FILE__ ), null, '1.0.0' );
			wp_print_styles( array( 'wpchill-welcome-style' ) );
		}

		/**
		 * @since 1.0.0
		 * Renders buttons
		 *
		 * @param string $text
		 *
		 * @param string $url
		 *
		 * @param bool   $fill
		 *
		 * @param string $color
		 */
		public function display_button( $text, $link, $fill = true, $color = '#5D3CE4' ) {
			$style = 'style="background-color:' . sanitize_hex_color( $color ) . ';border-color:' . sanitize_hex_color( $color ) . ';color:#fff;"';
			echo '<a href="' . esc_attr( $link ) . '" ' . ( $fill ? $style : '' ) . ' class="wpmtst-btn wpmtst-btn-block wpmtst-btn-lg">' . esc_html( $text ) . '</a>';
		}

		/**
		 * @since 1.0.0
		 * Renders extension html
		 *
		 * @param string $title
		 *
		 * @param string $description
		 *
		 * @param string $icon (icon URL)
		 *
		 * @param bool   $pro
		 */
		public function display_extension( $title, $description = '', $icon = '', $pro = false, $pro_color = '#5333ED' ) {

			echo '<div class="feature-block">';
			if ( '' != $icon ) {
				echo '<img src="' . esc_attr( $icon ) . '">';
			}
			echo '<h5>' . esc_html( $title ) . ( ( $pro ) ? '<div style="background-color:' . esc_attr( $pro_color ) . '" class="pro-label">PRO</div>' : '' ) . '</h5>';
			echo '<p>' . esc_html( $description ) . '</p>';
			echo '</div>';
		}

		/**
		 * @since 1.0.0
		 * Displays h1 heading
		 *
		 * @param string $text
		 *
		 * @param string $position
		 */
		public function display_heading( $text, $position = 'center' ) {
			echo '<h1 style="text-align: ' . esc_attr( $position ) . ';" >' . esc_html( $text ) . '</h1>';
		}

		/**
		 * @since 1.0.0
		 * Displays h6 subheading
		 *
		 * @param string $text
		 *
		 * @param string $position
		 */
		public function display_subheading( $text, $position = 'center' ) {
			echo '<h6 style="text-align: ' . esc_attr( $position ) . '" >' . esc_html( $text ) . '</h6>';
		}


		/**
		 * @since 1.0.0
		 * Renders testimonial block
		 *
		 * @param string $text
		 *
		 * @param string $icon
		 *
		 * @param string $name
		 *
		 * @param string $job (reviewer's job or company)
		 */
		public function display_testimonial( $text, $icon = '', $name = '', $job = '', $star_color = '' ) {

			echo '<div class="testimonial-block">';
			if ( '' != $icon ) {
				echo '<img src=" ' . esc_url( $icon ) . ' "/>';
			}
			echo '<p>' . esc_html( $text ) . '</p>';

			$this->display_stars( $star_color );

			if ( '' !== $name || '' !== $job ) {
				echo '<p>';

				if ( '' !== $name ) {
					echo '<strong>' . esc_html( $name ) . '</strong><br/>';
				}
				if ( '' !== $job ) {
					echo esc_html( $job );
				}
				echo '</p>';
			}

			echo '</div>';
		}

		/**
		 * @since 1.0.0
		 * Renders a UL list
		 *
		 * @param array $items - array of list items
		 */
		public function display_listing( $items ) {
			echo '<ul>';

			foreach ( $items as $item ) {
				echo '<li>' . esc_html( $item ) . '</li>';
			}

			echo '</ul>';
		}


		/**
		 * @since 1.0.0
		 * Renders a UL list
		 *
		 * @param string $url - youtube.com url
		 */
		public function display_video( $url ) {
			parse_str( wp_parse_url( esc_url( $url ), PHP_URL_QUERY ), $video_vars );
			echo '<div class="container"><iframe src="https://www.youtube.com/embed/' . esc_attr( $video_vars['v'] ) . '" frameborder="0" allowfullscreen class="video"></iframe></div>';
		}

		/**
		 * @since 1.0.0
		 * Renders rating stars block
		 *
		 * @param string $color - code of the star color fill
		 */
		public function display_stars( $color ) {
			$color = ( '' === $color ) ? '#FFD700' : sanitize_hex_color( $color );
			$id    = wp_rand( 0, 9999999 );
			$star  = '<svg version="1.1" class="svg-' . absint( $id ) . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-274 399.8 53 43.1" style="enable-background:new -274 399.8 53 43.1;" xml:space="preserve">
                    <g>
                        <path class="st0" d="M-234.8,415h-11.5l-3.6-11c-1.4-4.3-3.7-4.3-5.1,0l-3.6,11h-11.5c-4.5,0-5.3,2.2-1.6,4.8l9.3,6.8l-3.6,11
                            c-1.4,4.3,0.4,5.7,4.1,3l9.3-6.8l9.3,6.8c3.7,2.7,5.5,1.3,4.1-3l-3.6-11l9.3-6.8C-229.6,417.1-230.3,415-234.8,415z"/>
                    </g>
                </svg>';

				$svg_args = array(
					'svg'   => array(
						'class'           => true,
						'aria-hidden'     => true,
						'aria-labelledby' => true,
						'role'            => true,
						'xmlns'           => true,
						'width'           => true,
						'height'          => true,
						'viewbox'         => true, // <= Must be lower case!
						'id'              => true,
					),
					'g'     => array( 'fill' => true ),
					'title' => array( 'title' => true ),
					'path'  => array(
						'd'    => true,
						'fill' => true,
					),
					'style' => array( 'type' => true ),
				);

				echo '<style>';
				echo '.svg-' . absint( $id ) . '{ fill:' . sanitize_hex_color( $color ) . ';}';
				echo '</style>';
				echo '<div class="stars_wrapper">' . wp_kses( $star . $star . $star . $star . $star, $svg_args ) . '</div>';
		}

		/**
		 * @since 1.0.0
		 * Columns wrapper start
		 *
		 * @param int $cols - # of columns the contained objects should be displayed as. (1/2/3)
		 */
		public function layout_start( $cols = 2, $class = '' ) {
			echo '<div class="' . esc_attr( $class ) . ' block-row block-row-' . absint( $cols ) . '">';
		}

		/**
		 * @since 1.0.0
		 * Columns wrapper end
		 */
		public function layout_end() {
			echo '</div>';
		}

		/**
		 * @since 1.0.0
		 * Renders empty space block
		 *
		 * @param int $height - height(px) of space
		 */
		public function display_empty_space( $height = 25 ) {

			echo '<div class="wpchill_empty_space" style="height:' . esc_attr( $height ) . 'px;"></div>';
		}

		/**
		 * Horizontal delimiter
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function horizontal_delimiter() {
			echo '<hr class="wpchill_horizontal_delimiter">';
		}

	}
}
