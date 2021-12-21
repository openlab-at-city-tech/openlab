<?php
class S2_Ajax {
	/**
	 * Constructor
	 */
	public function __construct() {
		// maybe use dev scripts
		$this->script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		if ( is_admin() ) {
			add_action( 'wp_ajax_nopriv_subscribe2_form', array( &$this, 's2_ajax_form_handler' ) );
			add_action( 'wp_ajax_nopriv_subscribe2_submit', array( &$this, 's2_ajax_submit_handler' ) );
			add_filter( 's2_ajax_form', array( &$this, 's2_ajax_form_class' ), 1 );
			add_filter( 'safe_style_css', array( &$this, 's2_safe_css' ) );

			global $s2_frontend;
			require_once S2PATH . 'classes/class-s2-core.php';
			require_once S2PATH . 'classes/class-s2-frontend.php';
			$s2_frontend = new S2_Frontend();

			$s2_frontend->subscribe2_options = get_option( 'subscribe2_options' );
		} else {
			// add actions for ajax form if enabled
			add_action( 'wp_enqueue_scripts', array( &$this, 'add_ajax' ) );
		}
	}

	/**
	 * Add jQuery code and CSS to front pages for ajax form
	 */
	public function add_ajax() {
		// enqueue the jQuery script we need and let WordPress handle the dependencies
		wp_enqueue_script( 'jquery-ui-dialog' );
		$css = apply_filters( 's2_jqueryui_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/ui-darkness/jquery-ui.css' );
		if ( is_ssl() ) {
			$css = str_replace( 'http:', 'https:', $css );
		}
		wp_register_style( 'jquery-ui-style', $css, array(), '1.11.4' );
		wp_enqueue_style( 'jquery-ui-style' );
		wp_register_script( 's2-ajax', S2URL . 'include/s2-ajax' . $this->script_debug . '.js', array(), '1.3', true );
		$translation_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'title'   => __( 'Subscribe to this blog', 'subscribe2' ),
			'nonce'   => wp_create_nonce( 's2_ajax_form_nonce' ),
		);
		wp_localize_script( 's2-ajax', 's2AjaxScriptStrings', $translation_array );
		wp_enqueue_script( 's2-ajax' );
	}

	/**
	 * Ajax form handler
	 */
	public function s2_ajax_form_handler() {
		require_once ABSPATH . '/wp-includes/shortcodes.php';

		$response = str_replace( ':', '&', $_POST['data'] );
		$response = str_replace( '-', '=', $response );
		wp_parse_str( $response, $atts );

		global $s2_frontend;
		$content = $s2_frontend->widget_shortcode( $atts );
		$content = apply_filters( 's2_ajax_form', $content );

		$allowed_tags = array(
			'form'  => array(
				'action' => true,
				'id'     => true,
				'method' => true,
				'name'   => true,
			),
			'span'  => array(
				'style' => true,
			),
			'label' => array(
				'for' => true,
			),
			'input' => array(
				'id'      => true,
				'name'    => true,
				'onblur'  => true,
				'onfocus' => true,
				'size'    => true,
				'type'    => true,
				'value'   => true,
			),
			'p'     => true,
			'br'    => true,
		);

		echo wp_kses( $content, $allowed_tags );
		exit();
	}

	/**
	 * Ajax submit handler
	 */
	public function s2_ajax_submit_handler() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 's2_ajax_form_nonce' ) ) {
			echo '<p>' . esc_html__( 'There was an error validating your request. Please try again later.', 'subscribe2' ) . '</p>';
			wp_die();
		}
		$data = $_POST['data'];
		if ( ( isset( $data['firstname'] ) && '' !== $data['firstname'] ) || ( isset( $data['lastname'] ) && '' !== $data['lastname'] ) || ( isset( $data['uri'] ) && 'http://' !== $data['uri'] ) ) {
			// looks like some invisible-to-user fields were changed; falsely report success
			echo '<p>' . esc_html__( 'A confirmation message is on its way!', 'subscribe2' ) . '</p>';
			wp_die();
		}

		global $s2_frontend, $wpdb;
		$s2_frontend->email = $s2_frontend->sanitize_email( $data['email'] );
		$s2_frontend->ip    = $data['ip'];
		if ( false === $s2_frontend->validate_email( $s2_frontend->email ) ) {
			echo '<p>' . esc_html__( 'Sorry, but that does not look like an email address to me.', 'subscribe2' ) . '</p>';
		} elseif ( $s2_frontend->is_barred( $s2_frontend->email ) ) {
			echo '<p>' . esc_html__( 'Sorry, email addresses at that domain are currently barred due to spam, please use an alternative email address.', 'subscribe2' ) . '</p>';
		} else {
			if ( is_int( $s2_frontend->lockout ) && $s2_frontend->lockout > 0 ) {
				$date = gmdate( 'H:i:s.u', $s2_frontend->lockout );
				$ips  = $wpdb->get_col( $wpdb->prepare( "SELECT ip FROM $wpdb->subscribe2 WHERE date = CURDATE() AND time > SUBTIME(CURTIME(), %s)", $date ) );
				if ( in_array( $s2_frontend->ip, $ips, true ) ) {
					echo '<p>' . esc_html__( 'Slow down, you move too fast.', 'subscribe2' ) . '</p>';
				}
			}
			$check = $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM $wpdb->users WHERE user_email = %s", $s2_frontend->email ) );
			if ( null !== $check ) {
				// Translators: Link to login page
				printf( wp_kses_post( __( 'To manage your subscription options please <a href="%1$s">login.</a>', 'subscribe2' ) ), esc_url( get_option( 'siteurl' ) . '/wp-login.php' ) );
			}
			if ( 'subscribe' === $data['button'] ) {
				if ( '1' !== $s2_frontend->is_public( $s2_frontend->email ) ) {
					// the user is unknown or inactive
					$s2_frontend->add( $s2_frontend->email );
					$status = $s2_frontend->send_confirm( 'add' );
					if ( $status ) {
						echo '<p>' . esc_html__( 'A confirmation message is on its way!', 'subscribe2' ) . '</p>';
					} else {
						echo '<p>' . esc_html__( 'Sorry, there seems to be an error on the server. Please try again later.', 'subscribe2' ) . '</p>';
					}
				} else {
					// they're already subscribed
					echo '<p>' . esc_html__( 'That email address is already subscribed.', 'subscribe2' ) . '</p>';
				}
			} elseif ( 'unsubscribe' === $data['button'] ) {
				if ( false === $s2_frontend->is_public( $s2_frontend->email ) ) {
					echo '<p>' . esc_html__( 'That email address is not subscribed.', 'subscribe2' ) . '</p>';
				} else {
					$status = $s2_frontend->send_confirm( 'del' );
					if ( $status ) {
						echo '<p>' . esc_html__( 'A confirmation message is on its way!', 'subscribe2' ) . '</p>';
					} else {
						echo '<p>' . esc_html__( 'Sorry, there seems to be an error on the server. Please try again later.', 'subscribe2' ) . '</p>';
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Filter to add ajax id to form
	 */
	public function s2_ajax_form_class( $content ) {
		$content = str_replace( '<form', '<form id="s2ajaxform"', $content );
		$content = str_replace( 'wp-login.php"', 'wp-login.php" style="text-decoration: underline;"', $content );
		return $content;
	}

	/**
	 * Add display attribute to safe CSS
	 */
	public function s2_safe_css( $style_attributes ) {
		$style_attributes[] = 'display';
		return $style_attributes;
	}
}
