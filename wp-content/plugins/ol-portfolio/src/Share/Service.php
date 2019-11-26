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
	 * Current user's Portfolio ID.
	 *
	 * @var int
	 */
	protected $portfolio_id = 0;

	/**
	 * Register portfolio share service.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_route' ], 20 );

		add_action( 'template_redirect', [ $this, 'init' ] );
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
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
		] );

		register_meta( 'comment', 'portfolio_post_id', [
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
		] );

		register_meta( 'post', 'portfolio_citation', [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'wp_kses_post',
			'show_in_rest'      => true,
		] );

		register_meta( 'post', 'portfolio_annotation', [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'show_in_rest'      => true,
		] );
	}

	/**
	 * Register custom routes.
	 *
	 * @return void
	 */
	public function register_route() {
		( new RestController() )->register_routes();
	}

	/**
	 * Initialize the feature.
	 *
	 * @return void
	 */
	public function init() {
		$type = openlab_get_site_type( get_current_blog_id() );

		switch ( $type ) {
			case 'club':
			case 'course':
			case 'project':
					$this->source_init();
					break;

			case 'portfolio':
					$this->portfolio_init();
					break;
		}
	}

	/**
	 * Handles "Course" and "Project" type sites.
	 *
	 * @return void
	 */
	public function source_init() {
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

		$group_id = openlab_get_user_portfolio_id( $user->ID );
		$enabled  = groups_get_groupmeta( $group_id, 'enable_portfolio_sharing' );

		if ( ! $enabled ) {
			return;
		}

		$this->portfolio_id = openlab_get_site_id_by_group_id( $group_id );

		// Bail, if user doesn't have portfolio.
		if ( empty( $this->portfolio_id ) ) {
			return;
		}

		// Render required markup.
		add_filter( 'the_content', [ $this, 'post_button' ], 200 );
		add_filter( 'comment_text', [ $this, 'comment_button' ], 200 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_footer', [ $this, 'dialog_template' ] );
	}

	/**
	 * Handles "Portfolio" type sites.
	 *
	 * @return void
	 */
	public function portfolio_init() {
		add_filter( 'the_content', [ $this, 'entry_source_note' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'entry_source_styles' ] );
	}

	/**
	 * Enqueue assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style(
			'add-to-portfolio-styles',
			plugins_url( 'assets/css/share.css', ROOT_FILE ),
			[],
			'20191105'
		);

		wp_enqueue_script(
			'add-to-portfolio',
			plugins_url( 'assets/js/share.js', ROOT_FILE ),
			[ 'a11y-dialog', 'wp-util' ],
			'20191118',
			true
		);

		$settings = [
			'root'           => esc_url_raw( get_rest_url() ),
			'portfolioRoot'  => esc_url_raw( get_rest_url( $this->portfolio_id ) ),
			'portfolioAdmin' => esc_url_raw( get_admin_url( $this->portfolio_id ) ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
		];

		wp_localize_script( 'add-to-portfolio', 'portfolioSettings', $settings );
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

		$endpoits = [ 'post' => 'posts', 'page' => 'pages' ];
		$entry = [
			'id'        => (int) $post->ID,
			'url'       => get_the_permalink( $post->ID ),
			'type'      => isset( $endpoits[ $post->post_type ] ) ? $endpoits[ $post->post_type ] : 'posts',
			'date'      => get_the_date( '', $post ),
			'site_id'   => get_current_blog_id(),
			'site_name' => get_option( 'blogname' ),
			'added'     => get_post_meta( $post->ID, 'portfolio_post_id', true ),
		];

		if ( ! empty( $entry['added'] ) ) {
			$entry['edit_link'] = get_admin_url(
				$this->portfolio_id,
				sprintf( 'post.php?post=%d&action=edit', $entry['added'] )
			);
		}

		ob_start();
		extract( [ 'data' => $entry ], EXTR_SKIP );
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

		$entry = [
			'id'        => (int) $comment->comment_ID,
			'url'       => get_comment_link( $comment ),
			'type'      => 'comments',
			'date'      => get_comment_date( '', $comment ),
			'site_id'   => get_current_blog_id(),
			'site_name' => get_option( 'blogname' ),
			'added'     => get_comment_meta( $comment->comment_ID, 'portfolio_post_id', true ),
		];

		if ( ! empty( $entry['added'] ) ) {
			$entry['edit_link'] = get_admin_url(
				$this->portfolio_id,
				sprintf( 'post.php?post=%d&action=edit', $entry['added'] )
			);
		}

		ob_start();
		extract( [ 'data' => $entry ], EXTR_SKIP );
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
		$post = get_post();
		$data = [
			'citation'   => get_post_meta( $post->ID, 'portfolio_citation', true ),
			'annotation' => get_post_meta( $post->ID, 'portfolio_annotation', true ),
		];

		// Check if the entry was added from other site.
		if ( empty( $data['citation'] ) ) {
			return $content;
		}

		ob_start();
		extract( [ 'data' => $data ], EXTR_SKIP );
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
	public function entry_source_styles() {
		wp_enqueue_style(
			'entry-source-styles',
			plugins_url( 'assets/css/portfolio.css', ROOT_FILE ),
			[],
			'20190727'
		);

		wp_enqueue_script(
			'show-more',
			plugins_url( 'assets/js/show-more.js', ROOT_FILE ),
			[ 'jquery' ],
			'20190727',
			true
		);
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
		$citation = get_post_meta( (int) $post->ID, 'portfolio_citation', true );
		if ( empty( $citation ) ) {
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
		$citation = get_post_meta( $post->ID, 'portfolio_citation', true );

		extract( [ 'data' => $citation ], EXTR_SKIP );
		include ROOT_DIR . '/views/share/meta/citation.php';
	}

	/**
	 * Render "Portfolio" entry annotation.
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function render_annotation_meta( $post ) {
		$annotation = get_post_meta( $post->ID, 'portfolio_annotation', true );

		extract( [ 'data' => $annotation ], EXTR_SKIP );
		include ROOT_DIR . '/views/share/meta/annotation.php';
	}
}
