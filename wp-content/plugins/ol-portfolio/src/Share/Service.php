<?php
/**
 * "Add to Portfolio" feature service.
 */

namespace OpenLab\Portfolio\Share;

use const OpenLab\Portfolio\ROOT_DIR;
use const OpenLab\Portfolio\ROOT_FILE;
use OpenLab\Portfolio\Contracts\Registerable;

class Service implements Registerable {

	const ID = 'share';

	/**
	 * Register portfolio share service.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_meta' ] );

		// @todo Change to 'template_redirect'
		add_action( 'wp_enqueue_scripts', [ $this, 'init' ] );
		add_action( 'template_redirect',  [ $this, 'source_init' ] );
		add_action( 'add_meta_boxes', [ $this, 'meta_boxes' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_meta' ] );
	}

	/**
	 * Register "Portfolio" post meta.
	 *
	 * @return void
	 */
	public function register_meta() {
		register_meta( 'post', 'portfolio_post_id', [
			'type' => 'integer',
			'single' => true,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
		] );

		register_meta( 'comment', 'portfolio_post_id', [
			'type' => 'integer',
			'single' => true,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
		] );

		register_meta( 'post', 'source_id', [
			'type' => 'integer',
			'single' => true,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
		] );

		register_meta( 'post', 'source_type', [
			'type' => 'string',
			'single' => true,
			'sanitize_callback' => function( $value ) {
				if ( in_array( $value, [ 'post', 'page', 'comment' ], true ) ) {
					return $value;
				}

				return 'posts';
			},
			'show_in_rest' => true,
		] );

		register_meta( 'post', 'source_site_id', [
			'type' => 'integer',
			'single' => true,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
		] );

		register_meta( 'post', 'source_date', [
			'type' => 'string',
			'single' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest' => true,
		] );

		register_meta( 'post', 'portfolio_annotation', [
			'type' => 'string',
			'single' => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'show_in_rest' => true,
		] );
	}

	/**
	 * Initialize "Add to Portfolio" feature.
	 *
	 * @return void
	 */
	public function init() {
		$user = wp_get_current_user();

		wp_register_script(
			'a11y-dialog',
			plugins_url( 'assets/js/a11y-dialog.min.js', ROOT_FILE ),
			[],
			'5.2.0'
		);

		if ( ! $user->exists() ) {
			return;
		}

		// Bail, if user doesn't have portfolio.
		$portfolio_group_id = openlab_get_user_portfolio_id( $user->ID );
		$portfolio_site_id  = openlab_get_site_id_by_group_id( $portfolio_group_id );

		if ( empty( $portfolio_site_id ) ) {
			return;
		}

		// Bail, if we don't support site type. Supported: 'course' and 'project'.
		$type = openlab_get_site_type( get_current_blog_id() );

		if ( ! in_array( $type, [ 'course', 'project' ] ) ) {
			return;
		}

		wp_enqueue_style(
			'add-to-portfolio-styles',
			plugins_url( 'assets/css/share.css', ROOT_FILE ),
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'add-to-portfolio',
			plugins_url( 'assets/js/share.js', ROOT_FILE ),
			[ 'a11y-dialog', 'wp-util' ],
			'1.0.0',
			true
		);

		$settings = [
			'root'          => esc_url_raw( get_rest_url() ),
			'portfolioRoot' => esc_url_raw( get_rest_url( $portfolio_site_id ) ),
			'nonce'         => wp_create_nonce( 'wp_rest' ),
		];

		wp_localize_script( 'add-to-portfolio', 'portfolioSettings', $settings );

		// Render required markup.
		add_filter( 'the_content', [ $this, 'post_button' ], 200 );
		add_filter( 'comment_text', [ $this, 'comment_button' ], 200 );
		add_action( 'wp_footer', [ $this, 'dialog_template' ] );
	}

	/**
	 * Register "Added to Portfolio" content front-end hooks.
	 *
	 * @return void
	 */
	public function source_init() {
		$type = openlab_get_site_type( get_current_blog_id() );

		if ( 'portfolio' !== $type ) {
			return;
		}

		add_filter( 'the_content', [ $this, 'entry_source_note' ] );
		add_action( 'wp_footer', [ $this, 'entry_source_styles' ] );
	}

	/**
	 * Render "Add to Portfolio" button for posts/pages.
	 *
	 * @param string $content
	 * @return string $content
	 */
	public function post_button( $content ) {
		if ( ! in_the_loop() ) {
			return $content;
		}

		$post = get_post();
		if ( ! in_array( $post->post_type, [ 'post', 'page'] ) ) {
			return $content;
		}

		if ( get_current_user_id() !== (int) $post->post_author ) {
			return $content;
		}

		ob_start();
		include ROOT_DIR . '/views/share/button/post.php';
		$button = ob_get_clean();

		$content .= $button;

		return $content;
	}

	/**
	 * Render "Add to Portfolio" button for comments.
	 *
	 * @param string $text
	 * @return string $text
	 */
	public function comment_button( $text ) {
		$comment = get_comment();

		if ( get_current_user_id() !== (int) $comment->user_id ) {
			return $text;
		}

		ob_start();
		include ROOT_DIR . '/views/share/button/comment.php';
		$button = ob_get_clean();

		$text .= $button;

		return $text;
	}

	/**
	 * Render "Add to Portfolio" dialog markup.
	 */
	public function dialog_template() {
		require_once ROOT_DIR . '/views/share/dialog.php';
	}

	/**
	 * Render "Citation" and "Annotation" notes before the content.
	 *
	 * @param string $content
	 * @return string $output
	 */
	public function entry_source_note( $content ) {
		$_post = get_post();
		$data  = $this->get_citation_data( $_post );

		// Check if the entry was added from other site.
		if ( empty( $data['source_id'] ) ) {
			return $content;
		}

		$data['annotation'] = get_post_meta( $_post->ID, 'portfolio_annotation', true );

		extract( [ 'data' => $data ], EXTR_SKIP );
		ob_start();

		include ROOT_DIR . '/views/share/entry-source.php';

		$output = ob_get_clean();
		$output .= $content;

		return $output;
	}

	/**
	 * Default styles for "Portfolio" entry source.
	 *
	 * @return void
	 */
	public function entry_source_styles( ) {
		?>
		<style type="text/css">
			.entry-source-note {
				color: #444;
				background-color: #f0f0f0;
				margin-bottom: 24px;
				padding: 24px;
			}
			.entry-source-note a,
			.entry-source-note a:visited {
				color: #444;
			}
			.entry-source-note .entry__annotation {
				margin-top: 24px;
			}
		</style>
		<?php
	}

	/**
	 * Register metaboxes for "Portfolio" post/pages.
	 *
	 * @param string $post_type
	 * @param WP_Post $post
	 * @return void
	 */
	public function meta_boxes( $post_type, $post ) {
		$type = openlab_get_site_type( get_current_blog_id() );

		if ( 'portfolio' !== $type ) {
			return;
		}

		// Check if the entry was added from other site.
		$source_id = get_post_meta( (int) $post->ID, 'source_id', true );
		if ( empty( $source_id ) ) {
			return;
		}

		add_meta_box(
			'ol-portfolio-citation',
			'Citation',
			[ $this, 'render_citation_meta' ],
			[ 'post', 'page' ],
			'normal'
		);

		add_meta_box(
			'ol-portfolio-annotation',
			'Annotation',
			[ $this, 'render_annotation_meta' ],
			[ 'post', 'page' ],
			'normal'
		);
	}

	/**
	 * Save "Portfolio" entry annotation.
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function save_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! isset( $_POST['portfolio_annotation_nonce'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['portfolio_annotation_nonce'], 'portfolio_annotation' ) ) {
			return $post_id;
		}

		$annotation = $_POST['portfolio_annotation'] ?? '';

		// Value is sanitized via register_meta().
		update_post_meta( $post_id, 'portfolio_annotation', $annotation );
	}

	/**
	 * Render "Portfolio" entry citation.
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function render_citation_meta( $post ) {
		$data = $this->get_citation_data( $post );

		extract( [ 'data' => $data ], EXTR_SKIP );

		include ROOT_DIR . '/views/share/citation.php';
	}

	/**
	 * Render "Portfolio" entry annotation.
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function render_annotation_meta( $post ) {
		$annotation = get_post_meta( $post->ID, 'portfolio_annotation', true );
		?>
		<textarea id="annotation" class="large-text" name="portfolio_annotation" rows="5"><?php echo esc_textarea( $annotation ); ?></textarea>
		<?php
		wp_nonce_field( 'portfolio_annotation', 'portfolio_annotation_nonce' );
	}

	/**
	 * Generate "Portfolio" citation data.
	 *
	 * @param WP_Post $post
	 * @return array $data
	 */
	protected function get_citation_data( $post ) {
		$source_id = get_post_meta( $post->ID, 'source_id', true );
		$site_id   = get_post_meta( $post->ID, 'source_site_id', true );
		$date      = get_post_meta( $post->ID, 'source_date', true );
		$type      = get_post_meta( $post->ID, 'source_type', true );

		$data = [
			'source_id' => $source_id,
			'date'      => mysql2date( 'F j, Y', $date ),
		];

		// @todo cache this.
		switch_to_blog( $site_id );
		$data['site_name'] = get_option( 'blogname' );
		$data['url']       = ( 'comment' === $type ) ? get_comment_link( $source_id ) : get_permalink( $source_id );
		restore_current_blog();

		return $data;
	}
}
