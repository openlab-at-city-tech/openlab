<?php

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\DataStorage\Sanitizer;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\{ Router, Security };

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery management class.
 */
class nggManageGallery {

	/**
	 * Mode.
	 *
	 * @var string
	 */
	public $mode = 'main';

	/**
	 * Gallery ID.
	 *
	 * @var int|false
	 */
	public $gid = false;

	/**
	 * Gallery object.
	 *
	 * @var object|null
	 */
	public $gallery = null;

	/**
	 * Picture ID.
	 *
	 * @var int|false
	 */
	public $pid = false;

	/**
	 * Base page URL.
	 *
	 * @var string
	 */
	public $base_page = 'admin.php?page=nggallery-manage-gallery';

	/**
	 * Search result.
	 *
	 * @var bool|array
	 */
	public $search_result = false;

	/**
	 * Gallery ID => author ID, memoized for the search-results branch of
	 * image_belongs_to_gallery(). Per-request only.
	 *
	 * @var array
	 */
	private $gallery_author_cache = [];

	// initiate the manage page.
	public function __construct() {

		// GET variables.
		if ( isset( $_GET['gid'] ) ) {
			$this->gid     = (int) $_GET['gid'];
			$this->gallery = GalleryMapper::get_instance()->find( $this->gid, true );
		}

		if ( isset( $_GET['pid'] ) ) {
			$this->pid = (int) $_GET['pid'];
		}

		if ( isset( $_GET['mode'] ) ) {
			$this->mode = trim( sanitize_text_field( wp_unslash( $_GET['mode'] ) ) );
		}

		// Check for pagination request, avoid post process of other submit button, exclude search results.
		// Nonce verification not necessary here: we are only determining which page to view, whose ID is always
		// cast to an integer. NextGEN has historically used POST in places it shouldn't, such as pagination here.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['post_paged'] ) && ! isset( $_GET['s'] ) ) {
			if ( isset( $_GET['paged'] ) && $_GET['paged'] != $_POST['post_paged'] ) {
				$_GET['paged'] = absint( $_POST['post_paged'] );
				return;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Should be only called via manage galleries overview.
		if ( isset( $_POST['nggpage'] ) && 'manage-galleries' === $_POST['nggpage'] && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'ngg_bulkgallery' ) ) {
			$this->post_processor_galleries();
		}

		// Should be only called via a edit single gallery page.
		if ( isset( $_POST['nggpage'] ) && 'manage-images' === $_POST['nggpage'] && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'ngg_updategallery' ) ) {
			$this->post_processor_images();
		}

		// Look for other POST process.
		if ( ! empty( $_POST ) || ! empty( $_GET ) ) {
			$this->processor();
		}

		M_NextGen_Admin::emit_do_notices_action();
	}

	public function controller() {

		switch ( $this->mode ) {
			case 'sort':
				// nggallery_sortorder() verifies the 'ngg_updatesortorder' nonce but never
				// resolved the owner of the gallery it was handed, so the sort screen - and its
				// write - was reachable for any gallery id by any holder of
				// 'NextGEN Manage gallery'. Gated only when there is gallery context, matching
				// post_processor_images(): with no gid there is no author, so
				// can_user_manage_gallery() would answer on the cross-author capability alone
				// and deny every owner-only caller. Nothing is left reachable by that, because
				// nggallery_sortorder() returns on gid 0 before its own check_admin_referer()
				// and before the UPDATE. See issue #1093 (audit #7.7).
				if ( (int) $this->gid && ! $this->can_user_manage_gallery() ) {
					nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
					break;
				}
				include_once __DIR__ . '/manage-sort.php';
				nggallery_sortorder( $this->gid );
				break;
			case 'edit':
				$this->setup_gallery_fields();
				$this->setup_image_rows();
				include_once __DIR__ . '/manage-images.php';
				nggallery_picturelist( $this );
				break;
			case 'main':
			default:
				include_once __DIR__ . '/manage-galleries.php';
				nggallery_manage_gallery_main();
				break;
		}
	}

	public function processor() {
		global $ngg, $nggdb;

		// Delete a picture.
		if ( $this->mode == 'delpic' ) {

			// TODO:Remove also Tag reference.
			check_admin_referer( 'ngg_delpicture' );

			// Verify the image belongs to the gallery named in the request.
			if ( ! $this->image_belongs_to_gallery( $this->pid ) ) {
				nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
				return;
			}

			$image = $nggdb->find_image( $this->pid );
			if ( $image ) {
				do_action( 'ngg_delete_picture', $this->pid, $image );
				if ( $ngg->options['deleteImg'] ) {
					$storage = StorageManager::get_instance();
					$storage->delete_image( $this->pid );
				}
				$mapper = ImageMapper::get_instance();
				$result = $mapper->destroy( $this->pid );

				if ( $result ) {
					nggGallery::show_message( __( 'Picture', 'nggallery' ) . ' \'' . $this->pid . '\' ' . __( 'deleted successfully', 'nggallery' ) );
				}
			}

			$this->mode = 'edit'; // show pictures.

		}

		// Recover picture from backup.
		if ( $this->mode == 'recoverpic' ) {

			check_admin_referer( 'ngg_recoverpicture' );

			// Verify the image belongs to the gallery named in the request.
			if ( ! $this->image_belongs_to_gallery( $this->pid ) ) {
				nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
				return;
			}

			// bring back the old image.
			nggAdmin::recover_image( $this->pid );

			nggGallery::show_message( __( 'Operation successful. Please clear your browser cache.', 'nggallery' ) );

			$this->mode = 'edit'; // show pictures.

		}

		// will be called after a ajax operation.
		if ( isset( $_POST['ajax_callback'] ) ) {
			if ( $_POST['ajax_callback'] == 1 ) {
				nggGallery::show_message( __( 'Operation successful. Please clear your browser cache.', 'nggallery' ) );
			}
		}

		// show sort order.
		if ( isset( $_POST['sortGallery'] ) ) {
			$this->mode = 'sort';
		}

		if ( isset( $_GET['s'] ) ) {
			$this->search_images();
		}
	}

	public function setup_image_rows() {
		add_filter( 'ngg_manage_images_row', [ &$this, 'render_image_row' ], 10, 2 );
		add_filter( 'ngg_manage_images_column_1_header', [ &$this, 'render_image_column_1_header' ] );
		add_filter( 'ngg_manage_images_column_1_content', [ &$this, 'render_image_column_1' ], 10, 2 );

		add_filter( 'ngg_manage_images_column_2_header', [ &$this, 'render_image_column_2_header' ] );
		add_filter( 'ngg_manage_images_column_2_content', [ &$this, 'render_image_column_2' ], 10, 2 );

		add_filter( 'ngg_manage_images_column_3_header', [ &$this, 'render_image_column_3_header' ] );
		add_filter( 'ngg_manage_images_column_3_content', [ &$this, 'render_image_column_3' ], 10, 2 );

		add_filter( 'ngg_manage_images_column_4_header', [ &$this, 'render_image_column_4_header' ] );
		add_filter( 'ngg_manage_images_column_4_content', [ &$this, 'render_image_column_4' ], 10, 2 );

		add_filter( 'ngg_manage_images_column_5_header', [ &$this, 'render_image_column_5_header' ] );
		add_filter( 'ngg_manage_images_column_5_content', [ &$this, 'render_image_column_5' ], 10, 2 );

		add_filter( 'ngg_manage_images_column_6_header', [ &$this, 'render_image_column_6_header' ] );
		add_filter( 'ngg_manage_images_column_6_content', [ &$this, 'render_image_column_6' ], 10, 2 );
	}

	public function render_image_column_1_header() {
		return '<input type="checkbox" id="cb-select-all-1" onclick="checkAll(document.getElementById(\'updategallery\'));">';
	}

	public function render_image_column_2_header() {
		return __( 'ID', 'nggallery' );
	}

	public function render_image_column_3_header() {
		return __( 'Thumbnail', 'nggallery' );
	}

	public function render_image_column_4_header() {
		return __( 'Filename', 'nggallery' );
	}

	public function render_image_column_5_header() {
		return __( 'Alt & Title Text / Description', 'nggallery' );
	}

	public function render_image_column_6_header() {
		return __( 'Tags', 'nggallery' );
	}

	public function render_image_column_1( $output = '', $picture = [] ) {
		return "<input type='checkbox' name='doaction[]' value='{$picture->pid}'/>";
	}

	public function render_image_column_2( $output = '', $picture = [] ) {
		return $picture->pid;
	}

	public function render_image_column_3( $output = '', $picture = [] ) {
		$image_url = add_query_arg( 'i', wp_rand(), $picture->imageURL );
		$thumb_url = add_query_arg( 'i', wp_rand(), $picture->thumbURL );
		$filename  = esc_attr( $picture->filename );

		$output = [];

		$output[] = "<a href='{$image_url}' class='thickbox' title='{$filename}'>";
		$output[] = "<img class='thumb' src='{$thumb_url}' id='thumb{$picture->pid}'/>";
		$output[] = '</a>';

		$output = implode( "\n", $output );
		return $output;
	}

	public function render_image_column_4( $output = '', $picture = [] ) {
		$image_url     = Router::esc_url( $picture->imageURL );
		$filename      = esc_attr( $picture->filename );
		$caption       = esc_html( ( empty( $picture->alttext ) ? $picture->filename : $picture->alttext ) );
		$data_caption  = esc_attr( $caption );
		$caption       = \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $caption );
		$caption       = esc_attr( $caption );
		$date          = mysql2date( get_option( 'date_format' ), $picture->imagedate );
		$width         = $picture->meta_data['width'] ?? null;
		$height        = $picture->meta_data['height'] ?? null;
		$pixels        = "{$width} x {$height} pixels";
		$excluded      = checked( $picture->exclude, 1, false );
		$exclude_label = __( 'Exclude ?', 'nggallery' );

		$output = [];

		$output[] = "<div><strong><a href='{$image_url}' class='thickbox' title='{$caption}' data-view-title='{$data_caption}'>{$filename}</a></strong></div>";
		$output[] = '<div class="meta">' . esc_html( $date ) . '</div>';
		$output[] = "<div class='meta'>{$pixels}</div>";
		$output[] = "<label for='exclude_{$picture->pid}'>";
		$output[] = "<input type='checkbox' id='exclude_{$picture->pid}' value='1' name='images[{$picture->pid}][exclude]' {$excluded}/> {$exclude_label}";
		$output[] = '</label>';

		$output = implode( "\n", $output );
		return $output;
	}

	public function render_image_column_5( $output = '', $picture = [] ) {
		$alttext = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $picture->alttext );
		$desc    = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $picture->description );

		$output = [];

		$output[] = "<input title='Alt/Title Text' type='text' name='images[{$picture->pid}][alttext]' value='{$alttext}'/>";
		$output[] = "<textarea title='Description' rows='3' name='images[$picture->pid][description]'>{$desc}</textarea>";

		$output = implode( "\n", $output );
		return $output;
	}

	public function render_image_column_6( $output = '', $picture = [] ) {
		global $wp_version;
		$fields = version_compare( $wp_version, '4.6', '<=' ) ? 'fields=names' : [ 'fields' => 'names' ];
		$tags   = wp_get_object_terms( $picture->pid, 'ngg_tag', $fields );
		if ( is_array( $tags ) ) {
			$tags = implode( ', ', $tags );
		}
		$tags = esc_html( $tags );

		return "<textarea rows='4' name='images[{$picture->pid}][tags]'>{$tags}</textarea>";
	}

	public function render_image_row( $picture, $counter ) {
		// Get number of columns.
		$class   = ! ( $counter % 2 == 0 ) ? '' : 'alternate';
		$columns = apply_filters( 'ngg_manage_images_number_of_columns', 6 );

		// Get the valid row actions.
		$actions     = [];
		$row_actions = apply_filters(
			'ngg_manage_images_row_actions',
			[
				'view'         => [ &$this, 'render_view_action_link' ],
				'meta'         => [ &$this, 'render_meta_action_link' ],
				'custom_thumb' => [ &$this, 'render_custom_thumb_action_link' ],
				'rotate'       => [ &$this, 'render_rotate_action_link' ],
				'recover'      => [ &$this, 'render_recover_action_link' ],
				'delete'       => [ &$this, 'render_delete_action_link' ],
			]
		);
		foreach ( $row_actions as $id => $callback ) {
			if ( is_callable( $callback ) ) {
				$result = call_user_func( $callback, $id, $picture );
				if ( $result ) {
					$actions[] = $result;
				}
			}
		}

		// Output row columns.
		echo '<tr class="' . esc_attr( $class ) . ' iedit" valign="top">';
		for ( $i = 1; $i <= $columns; $i++ ) {
			$rowspan = $i > 4 ? "rowspan='2'" : '';
			echo '<td class="column column-' . esc_attr( $i ) . '" ' . esc_attr( $rowspan ) . '>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- apply_filters() returns safe HTML for column content
			echo apply_filters( "ngg_manage_images_column_{$i}_content", '', $picture );
			echo '</td>';
		}
		echo '</tr>';

		// Actions row.
		echo '<tr class="' . esc_attr( $class ) . ' row_actions">';
		echo '<td colspan="2"></td>';
		echo '<td colspan="' . esc_attr( $columns - 2 ) . '">';
		echo "<div class='row-actions'>";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $actions contains safe HTML for row actions
		echo implode( ' | ', $actions );
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}


	public function render_view_action_link( $id, $picture ) {
		$image_url  = Router::esc_url( $picture->imageURL );
		$label      = esc_html__( 'View', 'nggallery' );
		$alt_text   = empty( $picture->alttext ) ? $picture->filename : $picture->alttext;
		$data_title = esc_attr( __( 'View', 'nggallery' ) ) . ' "' . esc_attr( $alt_text ) . '"';
		$alt_text   = \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $alt_text );
		$title      = esc_attr( __( 'View', 'nggallery' ) ) . ' "' . esc_attr( $alt_text ) . '"';

		return "<a href='{$image_url}' class='thickbox' title='{$title}' data-view-title='{$data_title}'>{$label}</a>";
	}

	public function render_meta_action_link( $id, $picture ) {
		$url   = Router::esc_url( NGGALLERY_URLPATH . 'admin/showmeta.php?id=' . $picture->pid . '&nonce=' . \wp_create_nonce( 'ngg_meta_popup' ) );
		$title = esc_attr__( 'Show meta data', 'nggallery' );
		$label = esc_html__( 'Meta', 'nggallery' );

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	public function render_custom_thumb_action_link( $id, $picture ) {
		$url   = Router::esc_url( NGGALLERY_URLPATH . 'admin/edit-thumbnail.php?id=' . $picture->pid . '&nonce=' . \wp_create_nonce( 'ngg_edit_thumbnail' ) );
		$title = esc_attr__( 'Customize thumbnail', 'nggallery' );
		$label = esc_html__( 'Edit thumb', 'nggallery' );

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	public function render_rotate_action_link( $id, $picture ) {
		$url   = Router::esc_url( NGGALLERY_URLPATH . 'admin/rotate.php?id=' . $picture->pid . '&nonce=' . \wp_create_nonce( 'ngg_edit_rotation' ) );
		$title = esc_attr__( 'Rotate', 'nggallery' );
		$label = esc_html__( 'Rotate', 'nggallery' );

		return "<a href='{$url}' class='ngg-dialog' title='{$title}'>{$label}</a>";
	}

	public function render_recover_action_link( $id, $picture ) {
		if ( ! file_exists( $picture->imagePath . '_backup' ) ) {
			return false;
		}

		$url      = wp_nonce_url( "admin.php?page=nggallery-manage-gallery&amp;mode=recoverpic&amp;gid={$picture->galleryid}&amp;pid={$picture->pid}", 'ngg_recoverpicture' );
		$title    = esc_attr__( 'Recover image from backup', 'nggallery' );
		$label    = esc_html__( 'Recover', 'nggallery' );
		$question = __( 'Recover', 'nggallery' );

		$alttext = empty( $picture->alttext ) ? $picture->filename : $picture->alttext;
		$alttext = Sanitizer::strip_html( html_entity_decode( $alttext ), true );
		$alttext = htmlentities( $alttext, ENT_QUOTES | ENT_HTML401 );
		$alttext = \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $alttext );
		$alttext = esc_attr( $alttext );

		// Event handler is found in nextgen_admin_page.js.
		return "<a href='{$url}'
                   class='confirmrecover'
                   data-question='{$question}'
                   data-text='{$alttext}'
                   title='{$title}'>{$label}</a>";
	}

	public function render_delete_action_link( $id, $picture ) {
		$url      = wp_nonce_url( "admin.php?page=nggallery-manage-gallery&amp;mode=delpic&amp;gid={$picture->galleryid}&amp;pid={$picture->pid}", 'ngg_delpicture' );
		$title    = esc_attr__( 'Delete image', 'nggallery' );
		$label    = esc_html__( 'Delete', 'nggallery' );
		$question = __( 'Delete', 'nggallery' );

		$alttext = empty( $picture->alttext ) ? $picture->filename : $picture->alttext;
		$alttext = Sanitizer::strip_html( html_entity_decode( $alttext ), true );
		$alttext = htmlentities( $alttext, ENT_QUOTES | ENT_HTML401 );
		$alttext = \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $alttext );
		$alttext = esc_attr( $alttext );

		// Event handler is found in nextgen_admin_page.js.
		return "<a href='{$url}'
                   class='submitdelete delete'
                   data-question='{$question}'
                   data-text='{$alttext}'
                   title='{$title}'>{$label}</a>";
	}

	public function render_image_row_header() {
		$columns = apply_filters( 'ngg_manage_images_number_of_columns', 6 );
		echo '<tr>';
		for ( $i = 1; $i <= $columns; $i++ ) {
			echo '<th class="column column-' . esc_attr( $i ) . '">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- apply_filters() returns safe HTML for column header
			echo apply_filters( 'ngg_manage_images_column_' . $i . '_header', "Column #{$i}" );
			echo '</th>';
		}
		echo '</tr>';
	}

	public function setup_gallery_fields() {
		add_filter( 'ngg_manage_gallery_fields', [ &$this, 'default_gallery_fields' ], 10, 2 );
	}

	public function default_gallery_fields( $fields = [], $gallery = null ) {
		$fields['left'] = [
			'title'          => [
				'callback' => [ &$this, 'render_gallery_title_field' ],
				'label'    => __( 'Title:', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_title',
			],
			'description'    => [
				'callback' => [ &$this, 'render_gallery_desc_field' ],
				'label'    => __( 'Description:', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_desc',
			],
			'path'           => [
				'callback' => [ &$this, 'render_gallery_path_field' ],
				'label'    => __( 'Gallery path:', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_path',
			],
			'gallery_author' => [
				'callback' => [ &$this, 'render_gallery_author_field' ],
				'label'    => __( 'Author', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_author',
			],
		];

		$fields['right'] = [
			'page_link_to'  => [
				'callback' => [ &$this, 'render_gallery_link_to_page_field' ],
				'label'    => __( 'Link to page:', 'nggallery' ),
				'tooltip'  => __( 'Albums will link this gallery to the selected page', 'nggallery' ),
				'id'       => 'gallery_page_link_to',
			],
			'preview_image' => [
				'callback' => [ &$this, 'render_gallery_preview_image_field' ],
				'label'    => __( 'Preview image:', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_preview_image',
			],
			'create_page'   => [
				'callback' => [ &$this, 'render_gallery_create_page_field' ],
				'label'    => __( 'Create new page:', 'nggallery' ),
				'tooltip'  => null,
				'id'       => 'gallery_create_new_page',
			],
		];

		return $fields;
	}

	public function render_gallery_field_label_column( $text, $for_attribute, $tooltip = null ) {
		$for_attribute = esc_attr( $for_attribute );

		if ( ! empty( $tooltip ) ) {
			$tooltip = "title='{$tooltip}' class='tooltip'";
		}

		echo '<td><label ' . esc_attr( $tooltip ) . ' for="' . esc_attr( $for_attribute ) . '">' . esc_html( $text ) . '</label></td>';
	}

	public function render_gallery_fields() {
		// Get the gallery entity.
		$gallery = GalleryMapper::get_instance()->find( $this->gid );

		// Get fields.
		$fields = apply_filters( 'ngg_manage_gallery_fields', [], $gallery );
		$left   = isset( $fields['left'] ) ? $fields['left'] : [];
		$right  = isset( $fields['right'] ) ? $fields['right'] : [];

		// Output table.
		echo '<table id="gallery_fields">';
		$number_of_fields = max( count( $left ), count( $right ) );
		$left_keys        = array_keys( $left );
		$right_keys       = array_keys( $right );
		for ( $i = 0; $i < $number_of_fields; $i++ ) {
			// Start row.
			echo '<tr>';

			// Left column.
			if ( isset( $left_keys[ $i ] ) ) {
				extract( $left[ $left_keys[ $i ] ] );

				// Label.
				$this->render_gallery_field_label_column( $label, $id, $tooltip );

				// Input field.
				if ( is_callable( $callback ) ) {
					echo '<td>';
					call_user_func( $callback, $gallery );
					echo '</td>';
				} elseif ( WP_DEBUG ) {
					echo '<p>Could not render ' . esc_html( $left_keys[ $i ] ) . ' field. No callback exists</p>';
				}
			} else {
				$output[] = '<td colspan="2"></td>';
			}

			// Right column.
			if ( isset( $right_keys[ $i ] ) ) {
				extract( $right[ $right_keys[ $i ] ] );
				// Label.
				$this->render_gallery_field_label_column( $label, $id, $tooltip );

				// Input field..
				if ( is_callable( $callback ) ) {
					echo '<td>';
					call_user_func( $callback, $gallery );
					echo '</td>';
				} elseif ( WP_DEBUG ) {
					echo '<p>Could not render ' . esc_html( $right_keys[ $i ] ) . ' field. No callback exists</p>';
				}
			} else {
				$output[] = '<td colspan="2"></td>';
			}

			// End.
			echo '</tr>';
		}
		echo '</table>';
	}

	public function render_gallery_title_field( $gallery ) {
		include 'templates/manage_gallery/gallery_title_field.php';
	}

	public function render_gallery_desc_field( $gallery ) {
		include 'templates/manage_gallery/gallery_desc_field.php';
	}

	public function render_gallery_path_field( $gallery ) {
		include 'templates/manage_gallery/gallery_path_field.php';
	}

	public function render_gallery_author_field( $gallery ) {
		$user   = get_userdata( $gallery->author );
		$author = isset( $user->display_name ) ? $user->display_name : ( $user->user_nicename ?? null );
		include 'templates/manage_gallery/gallery_author_field.php';
	}

	public function render_gallery_link_to_page_field( $gallery ) {
		$pages = get_pages();
		include 'templates/manage_gallery/gallery_link_to_page_field.php';
	}

	public function render_gallery_preview_image_field( $gallery ) {
		$images = [];
		foreach ( ImageMapper::get_instance()->find_all( [ 'galleryid = %s', $gallery->{$gallery->id_field} ] ) as $image ) {
			$images[ $image->{$image->id_field} ] = "[{$image->{$image->id_field}}] {$image->filename}";
		}
		include 'templates/manage_gallery/gallery_preview_image_field.php';
	}

	public function render_gallery_create_page_field( $gallery ) {
		$pages = get_pages();
		include 'templates/manage_gallery/gallery_create_page_field.php';
	}

	public function post_processor_galleries() {
		global $ngg;

		check_admin_referer( 'ngg_bulkgallery' );

		// bulk update in a single gallery.
		if ( isset( $_POST['bulkaction'] ) && isset( $_POST['doaction'] ) ) {

			switch ( $_POST['bulkaction'] ) {
				case 'no_action':
					// No action.
					break;
				case 'recover_images':
					// Recover images from backup.
					// A prefix 'gallery_' will first fetch all ids from the selected galleries.
					nggAdmin::do_ajax_operation( 'gallery_recover_image', isset( $_POST['doaction'] ) ? sanitize_text_field( wp_unslash( $_POST['doaction'] ) ) : '', __( 'Recover from backup', 'nggallery' ) );
					break;
				case 'set_watermark':
					// Set watermark
					// A prefix 'gallery_' will first fetch all ids from the selected galleries.
					nggAdmin::do_ajax_operation( 'gallery_set_watermark', isset( $_POST['doaction'] ) ? sanitize_text_field( wp_unslash( $_POST['doaction'] ) ) : '', __( 'Set watermark', 'nggallery' ) );
					break;
				case 'import_meta':
					// Import Metadata
					// A prefix 'gallery_' will first fetch all ids from the selected galleries.
					nggAdmin::do_ajax_operation( 'gallery_import_metadata', isset( $_POST['doaction'] ) ? sanitize_text_field( wp_unslash( $_POST['doaction'] ) ) : '', __( 'Import metadata', 'nggallery' ) );
					break;
				case 'delete_gallery':
					// Delete gallery.
					if ( is_array( $_POST['doaction'] ) ) {
						$deleted = false;
						$mapper  = GalleryMapper::get_instance();
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_unslash only removes slashes, values are sanitized on line 607
						foreach ( wp_unslash( $_POST['doaction'] ) as $id ) {
							$id = sanitize_text_field( wp_unslash( $id ) );

							$gallery = $mapper->find( $id );
							if ( ! $gallery ) {
								continue;
							}
							// Per-row ownership: can_manage_this_gallery() requires the gallery-management capability, then accepts the gallery author or a holder of 'NextGEN Manage others gallery'.
							if ( ! nggAdmin::can_manage_this_gallery( $gallery->author ) ) {
								continue;
							}
							if ( $gallery->path == '../' || false !== strpos( $gallery->path, '/../' ) ) {
								/* translators: %s: gallery ID */
								nggGallery::show_message( sprintf( __( 'One or more "../" in Gallery paths could be unsafe and NextGen Gallery will not delete gallery %s automatically', 'nggallery' ), $gallery->{$gallery->id_field} ) );
							} elseif ( $mapper->destroy( $id, true ) ) {
									$deleted = true;
							}
						}

						if ( $deleted ) {
							nggGallery::show_message( esc_html__( 'Gallery deleted successfully ', 'nggallery' ) );
						}
					}
					break;
			}
		}
		if ( isset( $_POST['addgallery'] ) && isset( $_POST['galleryname'] ) ) {

			check_admin_referer( 'ngg_bulkgallery' );

			if ( ! nggGallery::current_user_can( 'NextGEN Add new gallery' ) ) {
				wp_die( esc_html_e( 'Cheatin&#8217; uh?', 'nggallery' ) );
			}

			// get the default path for a new gallery.
			$newgallery = isset( $_POST['galleryname'] ) ? sanitize_text_field( wp_unslash( $_POST['galleryname'] ) ) : '';
			if ( ! empty( $newgallery ) ) {
				$gallery_mapper = GalleryMapper::get_instance();
				$gallery        = $gallery_mapper->create( [ 'title' => $newgallery ] );
				if ( $gallery->save() && ! isset( $_REQUEST['attach_to_post'] ) ) {
					$url = admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery->gid;
					/* translators: %s: gallery management URL */
					$message = sprintf( __( 'Gallery successfully created. <a href="%s" target="_blank">Manage gallery</a>', 'nggallery' ), $url );
					nggGallery::show_message( $message, 'gallery_created_msg' );
				}
			}

			do_action( 'ngg_update_addgallery_page' );
		}

		if ( isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_ResizeImages'] ) ) {

			// Writing site-wide image dimensions requires the options capability (issue #966).
			// Only the write is gated: the resize itself is authorized on NextGEN Manage
			// gallery in ngg_ajax_operation() and re-reads the saved dimensions, so denying
			// the write must not deny the operation to a delegated gallery manager.
			if ( ! Security::is_allowed( 'NextGEN Change options' ) ) {
				nggGallery::show_error(
					sprintf(
						/* translators: 1: image width in pixels, 2: image height in pixels. */
						__( 'Sorry, you have no access here. Changing the site-wide image dimensions needs the "NextGEN Change options" capability, so the images will be resized at the saved dimensions instead (%1$d x %2$d).', 'nggallery' ),
						(int) $ngg->options['imgWidth'],
						(int) $ngg->options['imgHeight']
					)
				);
			} else {
				// save the new values for the next operation.
				$ngg->options['imgWidth']  = isset( $_POST['imgWidth'] ) ? (int) $_POST['imgWidth'] : 0;
				$ngg->options['imgHeight'] = isset( $_POST['imgHeight'] ) ? (int) $_POST['imgHeight'] : 0;
				update_option( 'ngg_options', $ngg->options );
			}

			$gallery_ids = explode( ',', isset( $_POST['TB_imagelist'] ) ? sanitize_text_field( wp_unslash( $_POST['TB_imagelist'] ) ) : '' );
			// A prefix 'gallery_' will first fetch all ids from the selected galleries.
			nggAdmin::do_ajax_operation( 'gallery_resize_image', $gallery_ids, __( 'Resize images', 'nggallery' ) );
		}

		if ( isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_NewThumbnail'] ) ) {

			// Writing site-wide thumbnail dimensions requires the options capability (issue #966).
			// Only the write is gated - see the note on TB_ResizeImages above.
			if ( ! Security::is_allowed( 'NextGEN Change options' ) ) {
				nggGallery::show_error(
					sprintf(
						/* translators: 1: thumbnail width in pixels, 2: thumbnail height in pixels. */
						__( 'Sorry, you have no access here. Changing the site-wide thumbnail dimensions needs the "NextGEN Change options" capability, so the thumbnails will be created at the saved dimensions instead (%1$d x %2$d).', 'nggallery' ),
						(int) Settings::get_instance()->get( 'thumbwidth' ),
						(int) Settings::get_instance()->get( 'thumbheight' )
					)
				);
			} else {
				// save the new values for the next operation.
				$settings = Settings::get_instance();
				$settings->set( 'thumbwidth', isset( $_POST['thumbwidth'] ) ? (int) $_POST['thumbwidth'] : 0 );
				$settings->set( 'thumbheight', isset( $_POST['thumbheight'] ) ? (int) $_POST['thumbheight'] : 0 );
				$settings->set( 'thumbfix', isset( $_POST['thumbfix'] ) );
				$settings->save();
				ngg_refreshSavedSettings();
			}

			$gallery_ids = explode( ',', isset( $_POST['TB_imagelist'] ) ? sanitize_text_field( wp_unslash( $_POST['TB_imagelist'] ) ) : '' );
			// A prefix 'gallery_' will first fetch all ids from the selected galleries.
			nggAdmin::do_ajax_operation( 'gallery_create_thumbnail', $gallery_ids, __( 'Create new thumbnails', 'nggallery' ) );
		}
	}

	public function post_processor_images() {
		global $wpdb, $ngg, $nggdb;

		check_admin_referer( 'ngg_updategallery' );

		// Every branch in this dispatcher mutates a specific gallery or its images.
		// Gate on gallery ownership up-front so a nonce obtained from one gallery
		// cannot be replayed against another user's gallery (see issue #963).
		//
		// Only when there is gallery context: $this->gid is 0 on the image-search screen,
		// whose form (manage-images.php:151) posts no gid, so with $this->gallery null
		// can_user_manage_gallery() answers on the cross-author capability alone and denies
		// every owner-only caller. Gating unconditionally here would
		// reinstate the guard f2cd6c9e removed to fix #816/#821 and make the gid == 0 branch
		// in update_pictures() unreachable. Search-results mode is covered by that branch's
		// capability gate plus its per-image ownership checks.
		if ( (int) $this->gid && ! $this->can_user_manage_gallery() ) {
			nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
			return;
		}

		// bulk update in a single gallery. The action select and the row checkboxes live in the
		// main form, so require the Apply button that confirms them rather than any submit.
		if ( isset( $_POST['showThickbox'] ) && isset( $_POST['bulkaction'] ) && isset( $_POST['doaction'] ) ) {

			check_admin_referer( 'ngg_updategallery' );

			// The checkbox form posts doaction[] as an array of image ids. sanitize_text_field()
			// returns '' for any array (formatting.php:5633), so passing it through that made
			// do_ajax_operation() bail on its ! is_array() guard and every branch below except
			// delete_images silently did nothing - issue #925, and the reason the scoping below
			// never reached them. The ids are absint-ed here instead, which is stricter.
			//
			// Normalise before scoping, and scope unconditionally: gating the scoping call on
			// is_array() left a bypass, because a forged request can post doaction=<victim pid>
			// as a scalar. is_array() was then false, scope_images_to_gallery() never ran, and
			// the (array) cast still handed the unscoped id to every branch below - the very
			// thing issue #965 is about.
			$requested_ids = isset( $_POST['doaction'] )
				? array_values( array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['doaction'] ) ) ) ) )
				: [];

			// Scope image IDs to the current gallery so IDs from other galleries
			// cannot be acted upon via a forged request (issue #965).
			$doaction_ids = array_values( $this->scope_images_to_gallery( $requested_ids ) );

			// Keep $_POST in sync for anything downstream that still reads it.
			$_POST['doaction'] = $doaction_ids;

			// Report the pruned IDs. Every other guard in this handler surfaces its refusal;
			// without this the five direct-op branches below hand an empty (or shortened) list
			// to do_ajax_operation(), which returns '' for an empty array (functions.php:622) -
			// no progress bar, no message, nothing. That silent-no-op-after-an-authorization-
			// filter shape is exactly what issues #816 and #821 were reported as.
			// delete_images reports its own shortfall against the selected count, and no_action
			// changes nothing, so neither needs this notice.
			$bulkaction       = is_scalar( $_POST['bulkaction'] ) ? sanitize_key( wp_unslash( $_POST['bulkaction'] ) ) : '';
			$doaction_skipped = count( $requested_ids ) - count( $doaction_ids );

			if ( $doaction_skipped > 0 && ! in_array( $bulkaction, [ 'delete_images', 'no_action' ], true ) ) {
				nggGallery::show_error(
					sprintf(
						/* translators: %s: number of images that were skipped. */
						_n(
							'%s selected image does not belong to this gallery and was skipped.',
							'%s selected images do not belong to this gallery and were skipped.',
							$doaction_skipped,
							'nggallery'
						),
						number_format_i18n( $doaction_skipped )
					)
				);
			}

			switch ( $_POST['bulkaction'] ) {
				case 'no_action':
					break;
				case 'rotate_cw':
					nggAdmin::do_ajax_operation( 'rotate_cw', $doaction_ids, __( 'Rotate images', 'nggallery' ) );
					break;
				case 'rotate_ccw':
					nggAdmin::do_ajax_operation( 'rotate_ccw', $doaction_ids, __( 'Rotate images', 'nggallery' ) );
					break;
				case 'recover_images':
					nggAdmin::do_ajax_operation( 'recover_image', $doaction_ids, __( 'Recover from backup', 'nggallery' ) );
					break;
				case 'set_watermark':
					nggAdmin::do_ajax_operation( 'set_watermark', $doaction_ids, __( 'Set watermark', 'nggallery' ) );
					break;
				case 'strip_orientation_tag':
					nggAdmin::do_ajax_operation( 'strip_orientation_tag', $doaction_ids, __( 'Remove EXIF Orientation', 'nggallery' ) );
					break;
				case 'delete_images':
					// Gated on the normalised list rather than on is_array( $_POST['doaction'] ),
					// so this branch no longer behaves differently depending on the raw request
					// shape (a scalar doaction used to skip it entirely).
					if ( [] !== $requested_ids ) {
						// Counted rather than tracked in a single $delete_pic flag: that flag held
						// only the last destroy() result, so a partial failure still reported
						// success, and with the gallery scoping above the list can now be empty,
						// leaving it read while never assigned.
						//
						// $requested is what the user selected. Counting attempts only over the
						// post-scoping survivors made the denominator meaningless: selecting five
						// images where three belonged to another gallery reported "2 pictures
						// deleted successfully." and no error at all, so a destructive bulk action
						// silently did less than it was asked to.
						$requested = count( $requested_ids );
						$deleted   = 0;
						$attempts  = 0;

						foreach ( $doaction_ids as $imageID ) {
							$image = $nggdb->find_image( $imageID );
							if ( $image ) {
								++$attempts;
								do_action( 'ngg_delete_picture', $image->pid, $image );
								if ( $ngg->options['deleteImg'] ) {
									$storage = StorageManager::get_instance();
									$storage->delete_image( $image->pid );
								}

								$mapper = ImageMapper::get_instance();
								$mapper->destroy( $image->pid );

								// Success is "the row is gone", not destroy()'s return value.
								// StorageManager::delete_image() already calls destroy() itself,
								// and the ngg_delete_picture listeners may too, so by the time the
								// call above runs the DELETE often affects 0 rows and returns a
								// falsy value for an image that was in fact deleted. Trusting that
								// return reported a failure for every successful deletion.
								$mapper->flush_query_cache();
								if ( ! $mapper->find( $image->pid ) ) {
									++$deleted;
								}
							}
						}

						if ( $deleted > 0 ) {
							nggGallery::show_message(
								sprintf(
									/* translators: %s: number of pictures deleted. */
									_n( '%s picture deleted successfully.', '%s pictures deleted successfully.', $deleted, 'nggallery' ),
									number_format_i18n( $deleted )
								)
							);
						}

						// Measured against $requested, so IDs dropped by the gallery scoping and
						// IDs whose row no longer resolves are both reported instead of vanishing.
						if ( $deleted < $requested ) {
							nggGallery::show_error(
								sprintf(
									/* translators: 1: number of pictures that could not be deleted, 2: number selected. */
									__( 'Could not delete %1$s of the %2$s selected picture(s).', 'nggallery' ),
									number_format_i18n( $requested - $deleted ),
									number_format_i18n( $requested )
								)
							);
						}

						if ( 0 === $attempts ) {
							nggGallery::show_error( __( 'No pictures from this gallery were selected, so nothing was deleted.', 'nggallery' ) );
						}
					}
					break;
				case 'import_meta':
					nggAdmin::do_ajax_operation( 'import_metadata', $doaction_ids, __( 'Import metadata', 'nggallery' ) );
					break;
			}
		}

		// Scope TB_imagelist to the current gallery for all TB_* branches (issue #965).
		$tb_pic_ids    = [];
		$tb_has_images = false;

		if ( isset( $_POST['TB_imagelist'] ) && isset( $_POST['TB_bulkaction'] ) ) {
			$tb_pic_ids = array_values(
				$this->scope_images_to_gallery(
					array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['TB_imagelist'] ) ) ) ) )
				)
			);

			$_POST['TB_imagelist'] = implode( ',', $tb_pic_ids );
			$tb_has_images         = ! empty( $tb_pic_ids );

			// An empty result has to stop the TB_* branches below rather than fall through.
			// explode( ',', '' ) returns array( '' ), which is not empty, so do_ajax_operation()
			// would run for image 0 and wp_set_object_terms( '', ... ) would create the tags and
			// attach them to object 0 - and then the screen would report success. The branches
			// below consume $tb_pic_ids (already absint-ed) rather than re-exploding, for the
			// same reason.
			if ( ! $tb_has_images ) {
				nggGallery::show_error( __( 'None of the selected images belong to this gallery, so no changes were made.', 'nggallery' ) );
			}
		}

		if ( $tb_has_images && isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_ResizeImages'] ) ) {

			// Writing site-wide image dimensions requires the options capability,
			// not just the gallery-management capability (issue #966).
			if ( ! Security::is_allowed( 'NextGEN Change options' ) ) {
				nggGallery::show_error(
					sprintf(
						/* translators: 1: image width in pixels, 2: image height in pixels. */
						__( 'Sorry, you have no access here. Changing the site-wide image dimensions needs the "NextGEN Change options" capability, so the images will be resized at the saved dimensions instead (%1$d x %2$d).', 'nggallery' ),
						(int) $ngg->options['imgWidth'],
						(int) $ngg->options['imgHeight']
					)
				);
			} else {
				// save the new values for the next operation.
				$ngg->options['imgWidth']  = isset( $_POST['imgWidth'] ) ? (int) $_POST['imgWidth'] : 0;
				$ngg->options['imgHeight'] = isset( $_POST['imgHeight'] ) ? (int) $_POST['imgHeight'] : 0;

				update_option( 'ngg_options', $ngg->options );
			}

			// Only the write above is gated: ngg_ajax_operation() authorizes the resize on
			// NextGEN Manage gallery and re-reads the saved dimensions, so a delegated gallery
			// manager keeps bulk resize with the existing settings.
			$pic_ids = explode( ',', isset( $_POST['TB_imagelist'] ) ? sanitize_text_field( wp_unslash( $_POST['TB_imagelist'] ) ) : '' );
			nggAdmin::do_ajax_operation( 'resize_image', $pic_ids, __( 'Resize images', 'nggallery' ) );
		}

		if ( $tb_has_images && isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_NewThumbnail'] ) ) {

			// Writing site-wide thumbnail dimensions requires the options capability,
			// not just the gallery-management capability (issue #966).
			if ( ! Security::is_allowed( 'NextGEN Change options' ) ) {
				nggGallery::show_error(
					sprintf(
						/* translators: 1: thumbnail width in pixels, 2: thumbnail height in pixels. */
						__( 'Sorry, you have no access here. Changing the site-wide thumbnail dimensions needs the "NextGEN Change options" capability, so the thumbnails will be created at the saved dimensions instead (%1$d x %2$d).', 'nggallery' ),
						(int) Settings::get_instance()->get( 'thumbwidth' ),
						(int) Settings::get_instance()->get( 'thumbheight' )
					)
				);
			} else {
				// save the new values for the next operation.
				$settings = Settings::get_instance();
				$settings->set( 'thumbwidth', isset( $_POST['thumbwidth'] ) ? (int) $_POST['thumbwidth'] : 0 );
				$settings->set( 'thumbheight', isset( $_POST['thumbheight'] ) ? (int) $_POST['thumbheight'] : 0 );
				$settings->set( 'thumbfix', isset( $_POST['thumbfix'] ) );
				$settings->save();
				ngg_refreshSavedSettings();
			}

			// Only the write above is gated - see the note on TB_ResizeImages.
			$pic_ids = explode( ',', isset( $_POST['TB_imagelist'] ) ? sanitize_text_field( wp_unslash( $_POST['TB_imagelist'] ) ) : '' );
			nggAdmin::do_ajax_operation( 'create_thumbnail', $pic_ids, __( 'Create new thumbnails', 'nggallery' ) );
		}

		if ( $tb_has_images && isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_SelectGallery'] ) ) {

			$pic_ids  = $tb_pic_ids;
			$dest_gid = isset( $_POST['dest_gid'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['dest_gid'] ) ) : 0;

			switch ( isset( $_POST['TB_bulkaction'] ) ? sanitize_text_field( wp_unslash( $_POST['TB_bulkaction'] ) ) : '' ) {
				case 'copy_to':
					$destination = GalleryMapper::get_instance()->find( $dest_gid );

					// $dest_gid comes from $_POST and is only cast to an int; copy_images() and
					// move_images() never consult the destination gallery's author. Without this
					// the source list is scoped but the target is not, so images could be placed
					// into someone else's gallery. The null check also stops the
					// $destination->title dereference below from warning on an unknown dest_gid.
					if ( ! $destination || ! nggAdmin::can_manage_this_gallery( $destination->author ) ) {
						nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
						break;
					}

					$new_ids = StorageManager::get_instance()->copy_images( $pic_ids, $dest_gid );

					if ( ! empty( $new_ids ) ) {
						$admin_url = admin_url();
						$title     = esc_html( $destination->title );
						$link      = "<a href='{$admin_url}admin.php?page=nggallery-manage-gallery&mode=edit&gid={$destination->gid}'>{$title}</a>";
						nggGallery::show_message(
							/* translators: 1: number of pictures, 2: gallery link */
							sprintf( __( 'Copied %1$s picture(s) to gallery: %2$s .', 'nggallery' ), count( $new_ids ), $link )
						);
					} else {
						nggGallery::show_error(
							__( 'Failed to copy images', 'nggallery' )
						);
					}

					break;
				case 'move_to':
					$destination = GalleryMapper::get_instance()->find( $dest_gid );

					// Same as copy_to above - and move_images() deletes the sources after
					// copying, so an unauthorized destination also destroys the originals.
					if ( ! $destination || ! nggAdmin::can_manage_this_gallery( $destination->author ) ) {
						nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
						break;
					}

					$new_ids = StorageManager::get_instance()->move_images( $pic_ids, $dest_gid );

					if ( ! empty( $new_ids ) ) {
						$admin_url = admin_url();
						$title     = esc_html( $destination->title );
						$link      = "<a href='{$admin_url}admin.php?page=nggallery-manage-gallery&mode=edit&gid={$destination->gid}'>{$title}</a>";
						nggGallery::show_message(
							/* translators: 1: number of pictures, 2: gallery link */
							sprintf( __( 'Moved %1$s picture(s) to gallery: %2$s .', 'nggallery' ), count( $new_ids ), $link )
						);
					} else {
						nggGallery::show_error(
							__( 'Failed to move images', 'nggallery' )
						);
					}
					break;
			}
		}

		if ( $tb_has_images && isset( $_POST['TB_bulkaction'] ) && isset( $_POST['TB_EditTags'] ) ) {
			// do tags update.

			// get the images list.
			$pic_ids = $tb_pic_ids;
			$taglist = explode( ',', isset( $_POST['taglist'] ) ? sanitize_text_field( wp_unslash( $_POST['taglist'] ) ) : '' );
			$taglist = array_map( 'trim', $taglist );

			if ( is_array( $pic_ids ) ) {

				foreach ( $pic_ids as $pic_id ) {

					// which action should be performed ?
					switch ( $_POST['TB_bulkaction'] ) {
						case 'no_action':
							// No action.
							break;
						case 'overwrite_tags':
							// Overwrite tags.
							wp_set_object_terms( $pic_id, $taglist, 'ngg_tag' );
							break;
						case 'add_tags':
							// Add / append tags.
							wp_set_object_terms( $pic_id, $taglist, 'ngg_tag', true );
							break;
						case 'delete_tags':
							// Delete tags.
							$oldtags = wp_get_object_terms( $pic_id, 'ngg_tag', 'fields=names' );
							// get the slugs, to vaoid  case sensitive problems.
							$slugarray = array_map( 'sanitize_title', $taglist );
							$oldtags   = array_map( 'sanitize_title', $oldtags );
							// compare them and return the diff.
							$newtags = array_diff( $oldtags, $slugarray );
							wp_set_object_terms( $pic_id, $newtags, 'ngg_tag' );
							break;
					}
				}

				nggGallery::show_message( __( 'Tags changed', 'nggallery' ) );
			}
		}

		if ( isset( $_POST['updatepictures'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ngg_updategallery' ) ) {
			// Update pictures.
			$success = false;

			// nggGallery::current_user_can( 'NextGEN Edit gallery options' ) aliases to the global
			// 'NextGEN Manage gallery' cap (lib/core.php:230) - it carries no gallery context, and
			// $this->gallery is loaded from (int) $_GET['gid'] with no author test. So a user with
			// 'NextGEN Manage gallery' but not 'NextGEN Manage others gallery' - the exact role
			// split can_manage_this_gallery() exists to enforce, and the threat model this change
			// is written against - could POST updatepictures with ?gid=<victim gallery> and rewrite
			// another author's title, galdesc, storage path, pageid and pricelist_id. That is
			// WPScan report #963. The sibling gallery-row writers in this same method (scanfolder,
			// addnewpage) and update_pictures() all gate on can_user_manage_gallery() already;
			// this was the one that did not.
			//
			// It also closes a hole in the previewpic containment check below: that check is
			// guarded by `$value &&`, so previewpic=0 skipped image_belongs_to_gallery() entirely
			// and still cleared the preview thumbnail on an unowned gallery.
			$can_edit_gallery_fields = false;

			if ( nggGallery::current_user_can( 'NextGEN Edit gallery options' ) && ! isset( $_GET['s'] ) ) {
				$can_edit_gallery_fields = $this->can_user_manage_gallery();

				if ( ! $can_edit_gallery_fields ) {
					nggGallery::show_error( __( 'Sorry, you have no access here.', 'nggallery' ) );
				}
			}

			if ( $can_edit_gallery_fields ) {
				$tags   = [ '<a>', '<abbr>', '<acronym>', '<address>', '<b>', '<base>', '<basefont>', '<big>', '<blockquote>', '<br>', '<br/>', '<caption>', '<center>', '<cite>', '<code>', '<col>', '<colgroup>', '<dd>', '<del>', '<dfn>', '<dir>', '<div>', '<dl>', '<dt>', '<em>', '<fieldset>', '<font>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<hr>', '<i>', '<img>', '<ins>', '<label>', '<legend>', '<li>', '<menu>', '<noframes>', '<noscript>', '<ol>', '<optgroup>', '<option>', '<p>', '<pre>', '<q>', '<s>', '<samp>', '<select>', '<small>', '<span>', '<strike>', '<strong>', '<sub>', '<sup>', '<table>', '<tbody>', '<td>', '<tfoot>', '<th>', '<thead>', '<tr>', '<tt>', '<u>', '<ul>' ];
				$fields = [ 'title', 'galdesc' ];

				// Sanitize fields.
				foreach ( $fields as $field ) {
					$html            = isset( $_POST[ $field ] ) ? stripslashes( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) ) : '';
					$html            = preg_replace( '/\\s+on\\w+=(["\']).*?\\1/i', '', $html );
					$html            = preg_replace( '/(<\/[^>]+?>)(<[^>\/][^>]*?>)/', '$1 $2', $html );
					$html            = strip_tags( $html, implode( '', $tags ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
					$_POST[ $field ] = $html;
				}

				$mapper = GalleryMapper::get_instance();

				// Update the gallery.
				if ( ! $this->gallery ) {
					$this->gallery = $mapper->find( $this->gid, true );
				}

				if ( $this->gallery ) {
					// Allowlist matches the editable inputs in templates/manage_gallery/gallery_*_field.php.
					// Iterating $_POST blindly would let columns like author/gid/extras_post_id/slug be set.
					$editable_gallery_fields = [ 'title', 'galdesc', 'previewpic', 'path', 'pageid', 'pricelist_id' ];

					foreach ( $editable_gallery_fields as $field ) {
						if ( ! isset( $_POST[ $field ] ) ) {
							continue;
						}

						if ( $field === 'path' ) {
							// IIS hack: gallery paths can be mangled into \\wp-content\\blah\\ which causes later errors when validating the gallery path.
							$value = str_replace( '\\\\', '/', sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
						} elseif ( $field === 'previewpic' || $field === 'pageid' || $field === 'pricelist_id' ) {
							$value = (int) wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to int.

							// The preview <select> is built only from this gallery's images
							// (render_gallery_preview_image_field()), so containment lived in the
							// template and not in the save path: a forged post could point the
							// public preview thumbnail at an image from another gallery. An
							// unresolvable id also blocks every later album save - see issue #875.
							//
							// The rejection is reported rather than dropped with a bare continue.
							// Every other guard added here surfaces a message; a silent continue
							// left the old previewpic in place, saved the remaining fields, and
							// still printed "Updated successfully" below - so an admin whose chosen
							// thumbnail no longer resolves (deleted image, image moved to another
							// gallery) got a success message and a frontend that kept showing the
							// previous thumbnail, with nothing to explain why. That is the reported
							// symptom of #816 and #821, and #875's stale-preview-ID state.
							if ( $field === 'previewpic' && $value && ! $this->image_belongs_to_gallery( $value ) ) {
								nggGallery::show_error( __( 'The selected preview image does not belong to this gallery and was not saved.', 'nggallery' ) );
								continue;
							}
						} else {
							// title/galdesc are pre-sanitized higher in this branch (sanitize_text_field + strip_tags allowlist) and written back into $_POST; read that processed value.
							$value = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Only title/galdesc reach here, already sanitized and written back into $_POST above.
						}

						$this->gallery->$field = $value;
					}

					$mapper->save( $this->gallery );

					// Mirror the REST save so the ecommerce requirement check (which reads this post meta) sees it.
					// Only when the gallery is valid, so an invalid save does not mark the requirement complete.
					if ( isset( $_POST['pricelist_id'] ) && ! empty( $this->gallery->extras_post_id ) && $this->gallery->is_valid() ) {
						update_post_meta( $this->gallery->extras_post_id, 'pricelist_id', (int) wp_unslash( $_POST['pricelist_id'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to int.
					}

					if ( ! $this->gallery->is_valid() ) {
						foreach ( $this->gallery->validation() as $property => $errors ) {
							foreach ( $errors as $error ) {
								nggGallery::show_error( $error );
							}
						}
					}

					wp_cache_delete( $this->gid, 'ngg_gallery' );
					$success = $this->gallery->is_valid();
				}
			}

			$pictures_updated = $this->update_pictures();
			if ( $success || $pictures_updated >= 1 ) {
				// Hook for other plugin to update the fields.
				do_action( 'ngg_update_gallery', $this->gid, $_POST );
				nggGallery::show_message( __( 'Updated successfully', 'nggallery' ) );
			}
		}

		// Rescan folder.
		if ( isset( $_POST['scanfolder'] ) ) {

			// Outer nonce + page-level cap is not enough; folder import targets a specific gallery row.
			if ( ! $this->can_user_manage_gallery() ) {
				return;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$gallerypath = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `path` FROM `{$wpdb->nggallery}` WHERE `gid` = %d",
					[
						$this->gid,
					]
				)
			);

			nggAdmin::import_gallery( $gallerypath, $this->gid );
		}

		// Add a new page.
		if ( isset( $_POST['addnewpage'] ) ) {

			// Branch mutates a specific gallery and publishes a page — gate on gallery ownership and on publish_pages
			// because wp_insert_post() does not enforce capabilities when called directly.
			if ( ! $this->can_user_manage_gallery() ) {
				return;
			}
			if ( ! current_user_can( 'publish_pages' ) ) {
				return;
			}

			$parent_id     = isset( $_POST['parent_id'] ) ? (int) wp_unslash( $_POST['parent_id'] ) : 0; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cast to int.
			$gallery_title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
			$mapper        = GalleryMapper::get_instance();
			$gallery       = $mapper->find( $this->gid );
			$gallery_name  = $gallery->name;

			// Create a WP page.
			global $user_ID;

			$page['post_type']    = 'page';
			$page['post_content'] = apply_filters( 'ngg_add_page_shortcode', '[ngg src="galleries" display="basic_thumbnail" ids="' . $this->gid . '"]' );
			$page['post_parent']  = $parent_id;
			$page['post_author']  = $user_ID;
			$page['post_status']  = 'publish';
			$page['post_title']   = $gallery_title == '' ? $gallery_name : $gallery_title;
			$page                 = apply_filters( 'ngg_add_new_page', $page, $this->gid );

			$gallery_pageid = wp_insert_post( $page );
			if ( $gallery_pageid != 0 ) {
				$gallery->pageid = $gallery_pageid;
				$mapper->save( $gallery );
				nggGallery::show_message( __( 'New gallery page ID', 'nggallery' ) . ' ' . $gallery_pageid . ' -> <strong>' . $gallery_title . '</strong> ' . __( 'created', 'nggallery' ) );
			}

			do_action( 'ngg_gallery_addnewpage', $this->gid );
		}
	}

	/**
	 * Verify that the given image belongs to the gallery currently being managed.
	 *
	 * Prevents cross-gallery operations where an attacker passes image IDs from
	 * another gallery in the request body while addressing their own gallery.
	 *
	 * In search-results mode there is no gallery context ($this->gid is falsy), so the image
	 * is authorized against its own gallery instead - the same split update_pictures() makes.
	 *
	 * @param int $image_id Image ID to check.
	 * @return bool True if the current user may operate on this image.
	 */
	public function image_belongs_to_gallery( $image_id ) {
		$image = ImageMapper::get_instance()->find( (int) $image_id );
		if ( ! $image ) {
			return false;
		}

		if ( ! $this->gid ) {
			// The image-search form (manage-images.php:151) posts no gid, so denying every ID
			// here silently emptied doaction[]/TB_imagelist on that screen - the #816/#821
			// regression class. Authorize per image against its own gallery instead.
			if ( ! Security::is_allowed( 'nextgen_edit_gallery' ) ) {
				return false;
			}

			$image_gallery_id = (int) $image->galleryid;
			// Memoized because a search can return images from many galleries and
			// scope_images_to_gallery() calls this once per ID. Bounded by the number of
			// distinct galleries named in the request, and discarded with the request.
			if ( ! array_key_exists( $image_gallery_id, $this->gallery_author_cache ) ) {
				$image_gallery = GalleryMapper::get_instance()->find( $image_gallery_id );

				$this->gallery_author_cache[ $image_gallery_id ] = $image_gallery ? $image_gallery->author : null;
			}

			$author = $this->gallery_author_cache[ $image_gallery_id ];

			return null !== $author && nggAdmin::can_manage_this_gallery( $author );
		}

		// The galleryid comparison below only proves both sides agree, and both sides are
		// requester-supplied: $this->gid is (int) $_GET['gid']. Without an ownership check
		// first, mode=delpic&gid=<victim gallery>&pid=<victim image> satisfies the guard.
		// See issue #965.
		if ( ! $this->can_user_manage_gallery() ) {
			return false;
		}

		return (int) $image->galleryid === (int) $this->gid;
	}

	/**
	 * Filter an array of image IDs to only those belonging to the current gallery.
	 *
	 * @param array $image_ids Array of image IDs.
	 * @return array Filtered array of IDs belonging to $this->gid.
	 */
	public function scope_images_to_gallery( $image_ids ) {
		if ( ! is_array( $image_ids ) ) {
			$image_ids = array_filter( array_map( 'absint', explode( ',', (string) $image_ids ) ) );
		}
		return array_filter(
			$image_ids,
			function ( $id ) {
				return $this->image_belongs_to_gallery( $id );
			}
		);
	}

	public function can_user_manage_gallery() {
		// No gallery in context means no author, so an id of 0 leaves the gate on the
		// cross-author cap - as before, now with the base capability required alongside it.
		return Security::can_manage_owned( $this->gallery ? $this->gallery->author : 0, 'nextgen_edit_gallery' );
	}

	public function update_pictures() {
		$updated = 0;

		// $this->gid is set from (int) $_GET['gid'] in the constructor and is the authoritative
		// gallery ID. It is 0 (falsy) in search-results mode where no single gallery is selected.
		$current_gallery_id = (int) $this->gid;

		if ( $current_gallery_id ) {
			// Normal gallery-edit mode: verify the current user can manage this specific gallery.
			if ( ! $this->can_user_manage_gallery() ) {
				return $updated;
			}
		} elseif ( ! Security::is_allowed( 'nextgen_edit_gallery' ) ) {
			// Search-results mode (no gallery context): require at least basic gallery-management
			// capability as a first gate. Per-image ownership is enforced inside the loop below.
			return $updated;
		}

		if ( isset( $_POST['images'] )
			&& is_array( $_POST['images'] )
			&& isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ngg_updategallery' ) ) {
			$image_mapper = ImageMapper::get_instance();

			// Memoize gallery lookups for the search-results branch: a search can return images from many
			// distinct galleries, and GalleryMapper::find() hits the DB on every call. Cache by galleryid
			// so each gallery is loaded at most once per request.
			$gallery_cache = [];

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_unslash only removes slashes, values are sanitized on line 976 and later
			foreach ( wp_unslash( $_POST['images'] ) as $pid => $data ) {
				$pid = sanitize_text_field( wp_unslash( $pid ) );
				if ( ! isset( $data['exclude'] ) ) {
					$data['exclude'] = 0;
				}
				$image = $image_mapper->find( $pid );
				if ( $image ) {
					if ( $current_gallery_id ) {
						// Normal gallery-edit mode: cross-gallery IDOR guard.
						// can_user_manage_gallery() verifies ownership of $this->gallery, but without
						// this check a user managing gallery A could craft images[<pid>][...] entries
						// that rewrite fields on images belonging to gallery B.
						if ( (int) $image->galleryid !== $current_gallery_id ) {
							continue;
						}
					} else {
						// Search-results mode: no single gallery context — verify per-image that the
						// current user can manage the gallery this image belongs to.
						$image_gallery_id = (int) $image->galleryid;
						if ( ! array_key_exists( $image_gallery_id, $gallery_cache ) ) {
							$gallery_cache[ $image_gallery_id ] = GalleryMapper::get_instance()->find( $image_gallery_id );
						}
						$image_gallery = $gallery_cache[ $image_gallery_id ];
						if ( ! $image_gallery || ! nggAdmin::can_manage_this_gallery( $image_gallery->author ) ) {
							continue;
						}
					}
					// Strip slashes from title/description/alttext fields.
					if ( isset( $data['description'] ) ) {
						$data['description'] = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $data['description'] );
					}
					if ( isset( $data['alttext'] ) ) {
						$data['alttext'] = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $data['alttext'] );
					}
					if ( isset( $data['title'] ) ) {
						$data['title'] = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $data['title'] );
					}

					// Generate new slug if the alttext has changed.
					if ( isset( $data['alttext'] ) && $image->alttext != $data['alttext'] ) {
						$data['image_slug'] = null; // will cause a new slug to be generated.
					}

					// image_slug is included because the alttext-change branch above sets it to null to trigger regeneration.
					// pricelist_id is the per-image Pricelist select Pro injects into the Ecommerce column.
					// Iterating $data blindly would allow columns like galleryid/meta_data/post_id/extras_post_id/imagedate to be overwritten via crafted images[<pid>][...] POST.
					$editable_image_fields = [ 'alttext', 'description', 'title', 'exclude', 'tags', 'image_slug', 'pricelist_id' ];

					foreach ( $editable_image_fields as $field ) {
						if ( array_key_exists( $field, $data ) ) {
							// pricelist_id: 0 = inherit gallery, -1 = none, >0 = a pricelist. Cast with (int), not absint, so -1 survives.
							$image->$field = ( 'pricelist_id' === $field ) ? (int) $data[ $field ] : $data[ $field ];
						}
					}
					if ( $image_mapper->save( $image ) ) {
						++$updated;

						// Update the tags for the image.
						if ( isset( $data['tags'] ) ) {
							$tags = $data['tags'];
							if ( ! is_array( $tags ) ) {
								$tags = explode( ',', $tags );
							}
							foreach ( $tags as &$tag ) {
								$tag = trim( $tag );
							}
							wp_set_object_terms( $image->{$image->id_field}, $tags, 'ngg_tag' );
						}

						// remove from cache.
						wp_cache_delete( $image->pid, 'ngg_image' );

						// hook for other plugins after image is updated.
						do_action( 'ngg_image_updated', $image );
					}
				}
			}

			// Determine if any WP terms have been orphaned and clean them up.
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results = $wpdb->get_col(
				"SELECT t.`term_id` FROM `{$wpdb->term_taxonomy}` tt
                                       LEFT JOIN `{$wpdb->terms}` t ON tt.`term_id` = t.`term_id`
                                       WHERE tt.`taxonomy` = 'ngg_tag' AND tt.`count` <= 0"
			);
			if ( ! empty( $results ) ) {
				foreach ( $results as $term_id ) {
					$term_id = apply_filters( 'ngg_pre_delete_unused_term_id', $term_id );
					if ( ! empty( $term_id ) ) {
						wp_delete_term( $term_id, 'ngg_tag' );
					}
				}
			}
		}

		return $updated;
	}

	public function search_images() {
		global $nggdb;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for search
		if ( empty( $_GET['s'] ) ) {
			return;
		}
		// on what ever reason I need to set again the query var.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for search
		set_query_var( 's', isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '' );
		$request = get_search_query();

		// look now for the images.
		$search_for_images = (array) $nggdb->search_for_images( $request );
		$search_for_tags   = (array) nggTags::find_images_for_tags( $request, 'ASC' );

		// Merge the two arrays and deduplicate.
		$merged              = array_merge( $search_for_images, $search_for_tags );
		$this->search_result = [];
		foreach ( $merged as $result ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $result, $this->search_result ) ) {
				$this->search_result[] = $result;
			}
		}

		// TODO: Currently we didn't support a proper pagination.
		$nggdb->paged['objects_per_page']     = count( $this->search_result );
		$nggdb->paged['total_objects']        = $nggdb->paged['objects_per_page'];
		$nggdb->paged['max_objects_per_page'] = 1;

		// show pictures page.
		$this->mode = 'edit';
	}

	/**
	 * Display the pagination.
	 *
	 * @since 1.8.0
	 * @author taken from WP core (see includes/class-wp-list-table.php)
	 * @return string echo the html pagination bar
	 */
	public function pagination( $which, $current, $total_items, $per_page ) {

		$total_pages = ( $per_page > 0 ) ? ceil( $total_items / $per_page ) : 1;

		/* translators: %s: number of items */
		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items, 'nggallery' ), number_format_i18n( $total_items ) ) . '</span>';

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' ) . ( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' );

		$current_url = remove_query_arg( [ 'hotkeys_highlight_last', 'hotkeys_highlight_first' ], $current_url );

		$page_links = [];

		$disable_first = '';
		$disable_last  = '';
		if ( $current == 1 ) {
			$disable_first = ' disabled';
		}
		if ( $current == $total_pages ) {
			$disable_last = ' disabled';
		}

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page', 'nggallery' ),
			Router::esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page', 'nggallery' ),
			Router::esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which ) {
			$html_current_page = $current;
		} else {
			$html_current_page = sprintf(
				"<input class='current-page' title='%s' type='text' name='%s' value='%s' size='%d' />",
				esc_attr__( 'Current page', 'nggallery' ),
				esc_attr__( 'post_paged', 'nggallery' ),
				$current,
				strlen( $total_pages )
			);
		}

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		/* translators: 1: current page number, 2: total pages */
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging', 'nggallery' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page', 'nggallery' ),
			Router::esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page', 'nggallery' ),
			Router::esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$output .= "\n<span class='pagination-links'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}

		$pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pagination contains safe HTML for pagination display
		echo $pagination;
		return $pagination;
	}
}
