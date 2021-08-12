<?php
/**
 * Enqueue JS and CSS files for the Guten Blocks
 *
 * @package Dropr_main
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dropr_main_Guten_blocks {
	private static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_dynamic_block' ) );

		// Hook: Frontend assets.
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );

		// Hook: Editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
	}

	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register dynamic block
	 */
	public function register_dynamic_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'dropr-for-dropbox/dropr',
			array(
				'attributes'      => array(
					'className'   => array(
						'type' => 'string',
					),
					'contentType' => array(
						'type' => 'string',
					),
					'link'        => array(
						'type' => 'object',
					),
					'document'    => array(
						'type' => 'object',
					),
					'media'       => array(
						'type' => 'object',
					),
					'image'       => array(
						'type' => 'object',
					),
				),
				'render_callback' => array( $this, 'block_render_callback' ),
			)
		);
	}

	/**
	 * Server side rendering
	 */
	public function block_render_callback( $atts ) {
		$output       = '';
		$content_type = isset( $atts['contentType'] ) ? $atts['contentType'] : '';
		if ( ! empty( $content_type ) ) {
			$main_class       = sprintf( ' class="wp-dropr-block-wrapper%s"', isset( $atts['className'] ) ? ' ' . esc_attr( $atts['className'] ) : '' );
			$block_class_name = sprintf( 'wp-dropr-block wp-dropr-%s-block', $content_type );
			if ( $content_type !== 'link' && $content_type !== 'image' ) {
				if ( isset( $atts['document'] ) && is_array( $atts['document'] ) && $content_type === 'document' ) {
					$dropr  = Dropr_main::get_instance();
					$output = $dropr->embed_shortcode( $atts['document'] );
				}
				if ( isset( $atts['media'] ) && is_array( $atts['media'] ) && ( $content_type === 'audio' || $content_type === 'video' ) ) {
					$media_content = '';
					if ( $content_type === 'audio' ) {
						$media_content = wp_audio_shortcode( $atts['media'] );
					} else {
						$media_content = wp_video_shortcode( $atts['media'] );
					}
					$output = $media_content;
				}
			} else {
				if ( isset( $atts['link'] ) && $content_type === 'link' ) {
					$url       = isset( $atts['link']['src'] ) ? esc_url( $atts['link']['src'] ) : '';
					$text      = isset( $atts['link']['text'] ) ? esc_html( $atts['link']['text'] ) : '';
					$link_atts = isset( $atts['link']['class'] ) ? sprintf( ' class="%s"', esc_attr( $atts['link']['class'] ) ) : '';
					if ( isset( $atts['link']['target'] ) && $atts['link']['target'] ) {
						$link_atts .= ' target="_blank" rel="noopener noreferrer"';
					}

					$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>', $url, $text, $link_atts );
				}
				if ( isset( $atts['image'] ) && is_array( $atts['image'] ) && $content_type === 'image' ) {
					$img_atts = $atts['image'];
					// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$img_content   = $html_atts = '';
					$class         = isset( $img_atts['class'] ) ? esc_attr( $img_atts['class'] ) : '';
					$wrapper_class = sprintf( ' class="dropr-img-%s"', ! empty( $class ) ? $class : 'alignnone' );
					$html_atts    .= ! empty( $class ) ? sprintf( ' class="%s"', $class ) : '';
					$html_atts    .= isset( $img_atts['alt'] ) ? sprintf( ' alt="%s"', esc_attr( $img_atts['alt'] ) ) : '';
					$html_atts    .= isset( $img_atts['title'] ) ? sprintf( ' title="%s"', esc_attr( $img_atts['title'] ) ) : '';
					$html_atts    .= isset( $img_atts['width'] ) ? sprintf( ' width="%s"', esc_attr( $img_atts['width'] ) ) : '';
					$html_atts    .= isset( $img_atts['height'] ) ? sprintf( ' height="%s"', esc_attr( $img_atts['height'] ) ) : '';
					if ( isset( $img_atts['src'] ) ) {
						$img_content .= sprintf( '<img src="%s"%s />', esc_url( $img_atts['src'] ), $html_atts );
					}
					if ( isset( $img_atts['customURL'] ) ) {
						$img_content = sprintf( '<a href="%2$s" class="dropr-img-block-link">%1$s</a>', $img_content, esc_url( $img_atts['customURL'] ) );
					}
					if ( isset( $img_atts['caption'] ) ) {
						$allowed_tags = wp_kses_allowed_html( 'post' );
						$caption      = wp_kses( $img_atts['caption'], $allowed_tags );
						$img_content .= sprintf( '<figcaption>%s</figcaption>', $caption );
					}
					$img_content = sprintf( '<figure%2$s>%1$s</figure>', $img_content, $wrapper_class );
					$output      = $img_content;
				}
			}
			$output = sprintf( '<div%3$s><div class="%2$s">%1$s</div></div>', $output, esc_attr( $block_class_name ), $main_class );
		}
		return $output;
	}

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 */
	public function block_assets() {
		// Styles.
		wp_enqueue_style(
			'dropr-block-style-css',
			plugins_url( 'blocks/dropr/style/style.css', dirname( __FILE__ ) ),
			array( 'wp-edit-blocks' ),
			AWSM_DROPR_VERSION
		);
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 */
	public function block_editor_assets() {
		// Style
		wp_enqueue_style( 'dropr-block-editor-style', plugins_url( 'blocks/dropr/style/editor.css', dirname( __FILE__ ) ), array( 'wp-edit-blocks', 'awsmdrop-embed' ), AWSM_DROPR_VERSION );

		// Scripts.
		wp_enqueue_script(
			'dropr-block-editor-js',
			plugins_url( 'blocks/dropr/dropr-block.js', dirname( __FILE__ ) ),
			array( 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-hooks', 'dropr-main' ),
			AWSM_DROPR_VERSION,
			true
		);
	}
}

Dropr_main_Guten_blocks::get_instance();
