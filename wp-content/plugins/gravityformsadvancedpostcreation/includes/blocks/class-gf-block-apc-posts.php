<?php

// If Gravity Forms Block Manager is not available, do not run.
if ( ! class_exists( 'GF_Blocks' ) || ! defined( 'ABSPATH' ) ) {
	exit;
}

class GF_Block_APC_Posts extends GF_Block {

	/**
	 * Contains an instance of this block, if available.
	 *
	 * @since  1.6.0
	 * @var    GF_Block $_instance If available, contains an instance of this block.
	 */
	private static $_instance = null;

	/**
	 * Block type.
	 *
	 * @since 1.6.0
	 * @var   string
	 */
	public $type = 'gravityforms/apc-posts';

	/**
	 * Handle of primary block script.
	 *
	 * @since 1.6.0
	 * @var   string
	 */
	public $script_handle = 'gform_editor_block_apc_posts';

	/**
	 * Handle of primary block style.
	 *
	 * @since 2.5.6
	 * @var   string
	 */
	public $style_handle = 'gform_editor_block_apc_posts';

	public function __construct() {
		$this->assign_attributes();
	}

	private function assign_attributes() {
		$default_attributes = GFForms::get_service_container()->get( \Gravity_Forms\Gravity_Forms_APC\Blocks\GF_APC_Blocks_Service_Provider::APC_POSTS_BLOCK_ATTRIBUTES );
		$this->attributes   = $default_attributes;
	}

	/**
	 * Get instance of this class.
	 *
	 * @since  1.6.0
	 *
	 * @return GF_Block_Form
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	// # BLOCK RENDER -------------------------------------------------------------------------------------------------

	/**
	 * Display block contents on frontend.
	 *
	 * @since  1.6.0
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return string
	 */
	public function render_block( $attributes = array() ) {
		if ( ! is_user_logged_in() ) {
			return sprintf(
				'<div>%s</div>',
				wp_kses( rgar( $attributes, 'loginMessage' ), array( 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ) )
			);
		}

		gf_advancedpostcreation()->get_shared_data();

		if ( empty( $attributes['formId'] ) ) {
			$attributes['formId'] = 0;
		}
		$form_id        = rgar( $attributes, 'formId' ) ? $attributes['formId'] : '0';
		$form_to_show   = rgar( $attributes, 'formToShow' ) ? $attributes['formToShow'] : '';
		$posts_per_page = rgar( $attributes, 'postsPerPage' ) ? $attributes['postsPerPage'] : 5;
		$block_id       = rgar( $attributes, 'clientId' ) ? 'apc-posts-' . $attributes['clientId'] : 'apc-posts-' . wp_generate_uuid4();
		$grid_title     = rgar( $attributes, 'gridTitle' );

		$posts_list_handler = gf_advancedpostcreation()->get_posts_list_handler();
		$posts_from_handler = $posts_list_handler->get_user_posts( $form_to_show, $posts_per_page );

		$posts_array = array();

		foreach ( rgar( $posts_from_handler, 'posts', [] ) as $post_item ) {
			$post_id  = $post_item['id'];
			$entry_id = $post_item['entry_id'];
			$post_obj = get_post( $post_id );

			if ( ! $post_obj ) {
				continue;
			}

			// Format date for display
			$post_date = date_i18n( get_option( 'date_format' ), strtotime( $post_obj->post_date ) );

			$post_title = (string) get_the_title( $post_obj );

			// View link
			$view_markup = sprintf(
				'<a class="gform-button gform-button--size-height-s gform-button--icon-white gform-spacing gform-spacing--top-0 gform-spacing--right-2 gform-spacing--bottom-0 gform-spacing--left-0 gform-data-grid__action" href="%s" title="%s %s"><span class="dashicons dashicons-visibility"></span></a>',
				esc_url( get_permalink( $post_id ) ),
				esc_attr__( 'View', 'gravityformsadvancedpostcreation' ),
				esc_attr( $post_title )
			);

			// Edit link (or disabled markup)
			$edit_link = \GF_Advanced_Post_Creation::get_instance()->post_update_handler->get_edit_entry_link( $entry_id );
			if ( $edit_link ) {
				$edit_markup = sprintf(
					'<a class="gform-button gform-button--size-height-s gform-button--icon-white gform-spacing gform-spacing--top-0 gform-spacing--right-2 gform-spacing--bottom-0 gform-spacing--left-0 gform-data-grid__action" href="%s" title="%s %s"><span class="dashicons dashicons-edit"></span></a>',
					$edit_link,
					esc_attr__( 'Edit', 'gravityformsadvancedpostcreation' ),
					esc_attr( $post_title )
				);
			} else {
				$edit_markup = sprintf(
					'<span class="disabled-edit-link gform-button gform-button--size-height-s gform-button--icon-white gform-spacing gform-spacing--top-0 gform-spacing--right-2 gform-spacing--bottom-0 gform-spacing--left-0 gform-data-grid__action" title="%s for %s"><span class="dashicons dashicons-edit" style="opacity: 0.5;"></span></span>',
					esc_attr__( 'Editing not available', 'gravityformsadvancedpostcreation' ),
					esc_attr( $post_title )
				);
			}

			$post          = array(
				'ID'          => (string) $post_id,
				'entry_id'    => (string) $entry_id,
				'post_title'  => $post_title,
				'post_status' => (string) $post_obj->post_status,
				'post_date'   => (string) $post_date,
				'actions'     => sprintf( '%s %s', $view_markup, $edit_markup ),
			);
			$posts_array[] = $post;
		}

		if ( empty( $posts_array ) ) {
			$posts_array[] = array(
				'ID'          => (string) 1,
				'entry_id'    => (string) 1,
				'post_title'  => (string) esc_html__( 'No editable posts found.', 'gravityformsadvancedpostcreation' ),
				'post_status' => (string) '',
				'post_date'   => (string) '',
				'actions'     => (string) '',
			);
		}

		$styles = $this->generate_styles( $attributes, $block_id );

		$style_class = $this->generate_style_classes( $attributes );

		// Create the data object
		$total_pages = $posts_per_page ? ceil( rgar( $posts_from_handler, 'total_count', 0 ) / $posts_per_page ) : 1;
		$script_data = array(
			'data'         => $posts_array,
			'postsPerPage' => $posts_per_page,
			'currentPage'  => rgar( $posts_from_handler, 'current_page', 0 ),
			'form_id'      => $form_id,
			'totalPages'   => $total_pages,
			'instance'     => $posts_list_handler::$instance_count,
			'gridTitle'    => $grid_title,
		);

		$header = '';
		if ( ! empty( $grid_title ) ) {
			$header = sprintf(
				'<header class="gform-data-grid__header">
					<h3 class="gform-heading gform-typography--size-text-lg gform-typography--weight-medium gform-heading--regular gform-data-grid__title">%1$s</h3>
				</header>',
				esc_html( $grid_title )
			);
		}

		$gridData = '';
		// loop through the posts array and build the grid data
		foreach ( $posts_array as $post ) {

			$post_status = ( rgar( $post, 'post_status' ) === 'publish' ) ? esc_html__( 'Published', 'gravityformsadvancedpostcreation' ) : esc_html__( 'Draft', 'gravityformsadvancedpostcreation' );
			if ( empty( rgar( $post, 'post_status' ) ) ) {
				// post_status would be empty only if there was only the faked no records found post item.
				$post_status = '';
			}

			$gridData .= sprintf(
				'<div class="gform-data-grid__data-row" style="min-height: 71px;">
					<div class="gform-data-grid__column gform-data-grid__column-0">
						<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-regular">%s</div>
					</div>
					<div class="gform-data-grid__column gform-data-grid__column-1">
						<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-regular">%s</div>
					</div>
					<div class="gform-data-grid__column gform-data-grid__column-2">
						<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-regular">%s</div>
					</div>
					<div class="gform-data-grid__column gform-data-grid__column-3">
						<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-regular">
							%s
						</div>
					</div>
				</div>',
				esc_html( $post['post_title'] ),
				esc_html( $post_status ),
				esc_html( $post['post_date'] ),
				$post['actions']
			);
		}

		$pagination = '';
		if ( $total_pages > 1 ) {
			$pagination_links = '';
			if ( $total_pages > 4 ) {
				$pagination_links = sprintf(
					'<li class="gform-pagination__item--break">
						<a class="gform-pagination__link--break" role="button" tabindex="0" aria-label="Jump forward">...</a>
					</li>
					<li class="gform-pagination__item">
						<a role="button" class="gform-pagination__link" tabindex="0" aria-label="Page %1$s">%1$s</a>
					</li>',
					esc_html( $total_pages )
				);
			} else {
				for ( $i = 3; $i <= $total_pages; $i++ ) {
					$pagination_links .= sprintf(
						'<li class="gform-pagination__item">
							<a role="button" class="gform-pagination__link" tabindex="0" aria-label="Page %1$s">%1$s</a>
						</li>',
						esc_html( $i )
					);
				}
			}

			$pagination = sprintf(
				'<div class="gform-data-grid__pagination" data-testid="data-grid-pagination">
					<ul class="gform-pagination" role="navigation" aria-label="Pagination">
						<li class="gform-pagination__item--previous gform-pagination__item--disabled">
							<a class="gform-pagination__link--previous gform-pagination__link--disabled" tabindex="-1" role="button" aria-disabled="true" aria-label="Previous Page" rel="prev">Prev</a>
						</li>
						<li class="gform-pagination__item gform-pagination__item--selected">
							<a rel="canonical" role="button" class="gform-pagination__link gform-pagination__link--selected" tabindex="-1" aria-label="Page 1 is your current page" aria-current="page">1</a>
						</li>
						<li class="gform-pagination__item">
							<a rel="next" role="button" class="gform-pagination__link" tabindex="0" aria-label="Page 2">2</a>
						</li>
						%1$s
						<li class="gform-pagination__item--next">
							<a class="gform-pagination__link--next" tabindex="0" role="button" aria-disabled="false" aria-label="Next Page" rel="next">Next</a>
						</li>
					</ul>
				</div>',
				$pagination_links
			);
		}

		// We duplicate the HTML from the React component here so that this will behave smoothly in the block editor.
		// After the page loads, this HTML gets replaced by the React component.
		return sprintf(
			'<style> %1$s </style><div id="%2$s" class="apc-posts-container gform-admin %3$s" data-apc-posts="%4$s" data-client-id="%5$s">
				<article class="gform-data-grid gform-data-grid--highlight-hover gform-data-grid--highlight-selected">
					%6$s
					<div class="gform-data-grid__column-row gform-data-grid__column-row--header">
						<div class="gform-data-grid__column gform-data-grid__column-0">
							<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-medium">%7$s</div>
						</div>
						<div class="gform-data-grid__column gform-data-grid__column-1">
							<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-medium">%8$s</div>
						</div>
						<div class="gform-data-grid__column gform-data-grid__column-2">
							<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-medium">%9$s</div>
						</div>
						<div class="gform-data-grid__column gform-data-grid__column-3">
							<div class="gform-text gform-text--color-port gform-typography--size-text-sm gform-typography--weight-medium">%10$s</div>
						</div>
					</div>
					<div class="gform-data-grid__data">
						%11$s
					</div>
					%12$s
				</article>
			</div>',
			$styles,
			esc_attr( $block_id ),
			$style_class,
			esc_attr( wp_json_encode( $script_data ) ),
			esc_attr( rgar( $attributes, 'clientId' ) ),
			$header,
			esc_html__( 'Title', 'gravityformsadvancedpostcreation' ),
			esc_html__( 'Status', 'gravityformsadvancedpostcreation' ),
			esc_html__( 'Date', 'gravityformsadvancedpostcreation' ),
			esc_html__( 'Actions', 'gravityformsadvancedpostcreation' ),
			$gridData,
			$pagination
		);
	}

	/**
	 * Register styles for the block.
	 *
	 * @since  1.6.0
	 *
	 * @return array
	 */
	public function styles() {
		$plugin    = gf_advancedpostcreation();
		$asset_min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = [
			[
				'handle'  => $this->style_handle,
				'src'     => $plugin->get_base_url() . "/assets/css/dist/theme{$asset_min}.css",
				'version' => GF_ADVANCEDPOSTCREATION_VERSION,
			],
			[
				'handle' => 'dashicons',
			],
		];

		return $styles;
	}

	/*
	 * Generate an inline style block based on the block/shortcode attributes
	 *
	 * @since 1.6.0
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $client_id  The client ID for the block instance.
	 * @return string The generated styles.
	 */
	public function generate_styles( $attributes, $client_id ) {
		$styles = '';

		if ( rgar( $attributes, 'buttonColor' ) ) {
			$styles .= sprintf(
				'#%s.gform-admin .gform-pagination__link--selected { background: %s; }',
				esc_attr( $client_id ),
				esc_attr( $attributes['buttonColor'] )
			);
		}

		return $styles;
	}

	/*
	 * Generate a string of style classes based on the block/shortcode attributes.
	 *
	 * @since 1.6.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string The generated style classes.
	 */
	public function generate_style_classes( $attributes ) {
		$style_classes = array();

		if ( 'dark' === rgar( $attributes, 'gridStyle' ) ) {
			$style_classes[] = 'gform-admin--dark';
		}

		if ( rgar( $attributes, 'transparentBg' ) ) {
			$style_classes[] = 'gform-admin--transparent-bg';
		}

		if ( rgar( $attributes, 'hideDate' ) ) {
			$style_classes[] = 'gform-admin--hide-date';
		}

		if ( rgar( $attributes, 'hideStatus' ) ) {
			$style_classes[] = 'gform-admin--hide-status';
		}

		return trim( implode( ' ', $style_classes ) );
	}
}

// Register block.
$registered = GF_Blocks::register( GF_Block_APC_Posts::get_instance() );

if ( true !== $registered && is_wp_error( $registered ) ) {
	// Log that block could not be registered.
	gf_advancedpostcreation()->log_error( 'Unable to register block; ' . $registered->get_error_message() );

}
