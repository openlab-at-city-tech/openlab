<?php

namespace WeBWorK;

/**
 * Client.
 *
 * @since 1.0.0
 */
class Client {
	public function __construct() {
		$this->rewrites = new \WeBWorK\Client\Rewrites();

		add_shortcode( 'webwork', array( $this, 'shortcode_cb' ) );

		add_filter( 'login_message', array( $this, 'filter_login_message' ) );
	}

	public function shortcode_cb() {
		$deps = array();
		if ( is_user_logged_in() ) {
			$plupload_settings_filter = function( $params ) {
				$extensions                                       = array( 'jpg', 'jpeg', 'jpe', 'png', 'gif' );
				$params['filters']['mime_types'][0]['extensions'] = implode( ',', $extensions );
				return $params;
			};

			add_filter( 'plupload_default_settings', $plupload_settings_filter );

			wp_enqueue_media();

			remove_filter( 'plupload_default_settings', $plupload_settings_filter );
		}

		wp_enqueue_script( 'webwork-scaffold', WEBWORK_PLUGIN_URL . 'assets/js/webwork-scaffold.js', array( 'jquery' ), WEBWORK_PLUGIN_VER, true );
		wp_enqueue_script( 'webwork-app', WEBWORK_PLUGIN_URL . 'build/index.js', $deps, WEBWORK_PLUGIN_VER, true );
		wp_set_script_translations( 'webwork-app', 'webworkqa' );

		$route_base = get_option( 'home' );
		$route_base = preg_replace( '|https?://[^/]+/|', '', $route_base );

		$ww_problem = false;
		if ( is_page( 'webwork' ) ) {
			$ww_problem = true;
		} else {
			$ww_problem = get_query_var( 'ww_problem' );
		}

		// @todo Centralize this logic.
		$main_site_url     = apply_filters( 'webwork_server_site_base', get_option( 'home' ) );
		$rest_api_endpoint = set_url_scheme( trailingslashit( $main_site_url ) . 'wp-json/webwork/v1/' );

		if ( is_multisite() ) {
			$server_site_id = apply_filters( 'webwork_server_site_id', get_current_blog_id() );
		}

		// @todo Abstract.
		$post_data       = null;
		$ww_problem_text = '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['post_data_key'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_data_key = sanitize_text_field( wp_unslash( $_GET['post_data_key'] ) );
			if ( is_multisite() ) {
				$post_data = get_blog_option( $server_site_id, $post_data_key );
			} else {
				$post_data = get_option( $post_data_key );
			}
		}

		$user_is_admin = webwork_user_is_admin();

		// @todo Truly awful.
		$switched = false;
		if ( is_multisite() && get_current_blog_id() !== $server_site_id ) {
			switch_to_blog( $server_site_id );
			$switched = true;
		}

		$q              = new \WeBWorK\Server\Question\Query();
		$filter_options = $q->get_all_filter_options();

		if ( $switched ) {
			restore_current_blog();
		}

		/**
		 * Filters the "intro text" at the top of the directory.
		 *
		 * Leave empty to use the default.
		 *
		 * @param string
		 */
		$intro_text = apply_filters( 'webwork_intro_text', '' );

		/**
		 * Filters the sidebar intro text.
		 *
		 * @param string
		 */
		$sidebar_intro_text = apply_filters( 'webwork_sidebar_intro_text', 'Use the filters below to navigate the questions that have been posted. You can select questions by course, section, or a specific WeBWorK problem set.' );

		/**
		 * Filters the moment.js "format'.
		 *
		 * Defaults to 'MMMM D, YYYY'.
		 *
		 * See https://momentjs.com/docs/.
		 *
		 * @param string
		 */
		$moment_format = apply_filters( 'webwork_moment_format', 'MMMM D, YYYY' );

		/**
		 * Filters the "Incomplete" message content.
		 *
		 * @param string
		 */
		$incomplete_text = apply_filters( 'webwork_incomplete_question_text', 'This question does not contain enough detail for a useful response to be provided.' );

		wp_localize_script(
			'webwork-app',
			'WWData',
			array(
				'client_name'            => get_option( 'blogname' ),
				'filter_options'         => $filter_options,
				'incompleteQuestionText' => $incomplete_text,
				'introText'              => $intro_text,
				'loginURL'               => wp_login_url(),
				'momentFormat'           => $moment_format,
				'page_base'              => trailingslashit( set_url_scheme( get_option( 'home' ) ) ),
				'problem_id'             => $ww_problem,
				'rest_api_nonce'         => wp_create_nonce( 'wp_rest' ),
				'rest_api_endpoint'      => $rest_api_endpoint,
				'sidebarIntroText'       => $sidebar_intro_text,
				'route_base'             => trailingslashit( $route_base ),
				'user_can_ask_question'  => is_user_logged_in(), // todo
				'user_can_post_response' => is_user_logged_in(), // todo
				'user_can_subscribe'     => is_user_logged_in(), // todo
				'user_can_vote'          => is_user_logged_in(), // todo
				'user_id'                => get_current_user_id(),
				'user_is_admin'          => $user_is_admin,
			)
		);

		wp_enqueue_style( 'font-awesome', WEBWORK_PLUGIN_URL . 'lib/font-awesome/css/font-awesome.min.css', [], WEBWORK_PLUGIN_VER );
		wp_enqueue_style( 'webwork-app', WEBWORK_PLUGIN_URL . 'assets/css/app.css', array( 'font-awesome' ), WEBWORK_PLUGIN_VER );
		wp_enqueue_style( 'webwork-react-select', WEBWORK_PLUGIN_URL . 'assets/css/select.css', [], WEBWORK_PLUGIN_VER );

		wp_register_script( 'webwork-mathjax-loader', WEBWORK_PLUGIN_URL . 'assets/js/webwork-mathjax-loader.js', [], WEBWORK_PLUGIN_VER, false );

		/**
		 * Filters the URL of the MathJax loader file.
		 *
		 * webworkqa ships with a pared-down version of MathJax 2.7.x, which contains
		 * only the STIX web font. If you would like to use a CDN or another installation
		 * that has a broader variety of fonts, use this filter. Be sure that you
		 * use version 2.7.x of MathJax; version 3.x is not yet supported.
		 *
		 * @since 1.0.0
		 *
		 * @param string $mathjax_url
		 */
		$mathjax_url = apply_filters( 'webwork_mathjax_url', WEBWORK_PLUGIN_URL . 'lib/MathJax/MathJax.js?config=TeX-MML-AM_HTMLorMML-full' );

		/**
		 * Filters the default MathJax configuration.
		 *
		 * See http://docs.mathjax.org/en/v2.7-latest/configuration.html#using-in-line-configuration-options
		 * for more details.
		 *
		 * Note that you cannot specify more fonts than STIX unless you are using
		 * a different version of MathJax than the one that ships with the plugin.
		 * See the 'webwork_mathjax_url' above.
		 *
		 * @since 1.0.0
		 *
		 * @param array $mathjax_config
		 */
		$mathjax_config = apply_filters(
			'webwork_mathjax_config',
			[
				'HTML-CSS' => [
					'availableFonts' => [ 'TeX', 'STIX' ],
					'preferredFont'  => 'TeX',
					'webFont'        => 'STIX',
				],
				'MathMenu' => [
					'showContext' => true,
				],
			]
		);

		$webwork_mathjax_loader_strings = [
			'mathjax_src'    => esc_url( $mathjax_url ),
			'mathjax_config' => $mathjax_config,
		];

		wp_localize_script( 'webwork-mathjax-loader', 'WeBWorK_MathJax', $webwork_mathjax_loader_strings );

		wp_enqueue_script( 'webwork-mathjax-loader' );

		$markup  = '<div class="wrapper section-inner">';
		$markup .= '<div id="webwork-app" class="webwork-app">';
		$markup .= __( 'Loading...', 'webworkqa' );
		$markup .= '</div><!-- .content-area -->';
		$markup .= '</div>';

		return $markup;
	}

	public function filter_login_message( $message ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['is-webwork-redirect'] ) ) {
			return $message;
		}

		$site_name = get_option( 'blogname' );
		$message   = sprintf(
			// translators: 1. site name, 2. site name
			esc_html__( 'You have been directed to %1$s from WeBWorK. Before posting a question, you must log in using your %2$s credentials.' ),
			esc_html( $site_name ),
			esc_html( $site_name )
		);

		/**
		 * Filters the WeBWorK login redirect message.
		 *
		 * @param string $message
		 */
		$message = apply_filters( 'webwork_login_redirect_message', $message );

		$retval = '<p class="message">' . $message . '</p>';

		return $retval;
	}
}
