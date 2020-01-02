<?php
/**
* Block, Suspend, Report Blank Slate Class
*
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       2.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BPTK_Blank_Slate {
	/**
	* The current screen ID.
	*
	* @since  2.0
	* @var string
	*/
	public $screen = '';

	/**
	* Whether at least one report exists.
	*
	* @since  2.0
	* @var bool
	*/
	private $form = false;

	/**
	* Constructs the BPTK_Blank_Slate class.
	*
	* @since 2.0
	*/
	public function __construct() {
		$this->screen = get_current_screen()->id;
	}

	/**
	* Initializes the class and hooks into WordPress.
	*
	* @since 2.0
	*/
	public function init() {
		// Bail early if screen cannot be detected.
		if ( empty( $this->screen ) ) {
			return null;
		}

		$this->form = $this->post_exists( 'report' );

		// Check we are on the report list page
		if ( $this->screen == 'edit-report') {
			if ( $this->form ) {
				// Form exists. Bail out.
				return false;
			}

			add_action( 'manage_posts_extra_tablenav', array( $this, 'render' ) );

			// Hide non-essential UI elements.
			add_action( 'admin_head', array( $this, 'hide_ui' ) );
		}
	}

	/**
	* Renders the blank slate message.
	*
	* @since 2.0
	*
	* @param string $which The location of the list table hook: 'top' or 'bottom'.
	*/
	public function render( $which = 'bottom' ) {
		// Bail out to prevent content from rendering twice.
		if ( 'top' === $which ) {
			return null;
		}

		$template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/blank-slate.php';

		include $template_path;
	}

	/**
	* Hides non-essential UI elements when blank slate content is on screen.
	*
	* @since 2.0
	*/
	function hide_ui() {
		?>
		<style type="text/css">
		.search-box,
		.subsubsub,
		.wp-list-table,
		.tablenav.top,
		.tablenav-pages {
			display: none;
		}
		</style>
		<?php
	}

	/**
	* Determines if at least one post of a given post type exists.
	*
	* @since 2.0
	*
	* @param string $post_type Post type used in the query.
	* @return bool True if post exists, otherwise false.
	*/
	private function post_exists( $post_type ) {
		// Attempt to get a single post of the post type.
		$query = new WP_Query( array(
			'post_type'              => $post_type,
			'posts_per_page'         => 1,
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'post_status'            => array( 'any', 'trash' ),
		) );

		return $query->have_posts();
	}
}
