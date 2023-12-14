<?php

function nggallery_manage_gallery_main() {
	global $ngg;

	$action_status = [
		'message' => '',
		'status'  => 'ok',
	];

	// Build the pagination for more than 25 galleries.
	$_GET['paged'] = isset( $_GET['paged'] ) && ( $_GET['paged'] > 0 ) ? absint( $_GET['paged'] ) : 1;

	$items_per_page = apply_filters( 'ngg_manage_galleries_items_per_page', 25 );

	$start = ( $_GET['paged'] - 1 ) * $items_per_page;

	if ( ! empty( $_GET['order'] ) && in_array( strtoupper( $_GET['order'] ), [ 'DESC', 'ASC' ] ) ) {
		$order = $_GET['order'];
	} else {
		$order = apply_filters( 'ngg_manage_galleries_items_order', 'ASC' );
	}

	if ( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], [ 'gid', 'title', 'author' ] ) ) {
		$orderby = $_GET['orderby'];
	} else {
		$orderby = apply_filters( 'ngg_manage_galleries_items_orderby', 'gid' );
	}

	$gallery_mapper            = \Imagely\NGG\DataMappers\Gallery::get_instance();
	$total_number_of_galleries = $gallery_mapper->count();

	$query = $gallery_mapper->select();

	if ( ! empty( $_GET['gs'] ) ) {
		$gs = sanitize_text_field( wp_unslash( $_GET['gs'] ) );
		$query->where( [ 'title LIKE %s', '%' . trim( $gs ) . '%' ] );
	} else {
		$gs = null;
	}

	$gallerylist = $query->order_by( $orderby, $order )
						->limit( $items_per_page, $start )
						->run_query();

	$wp_list_table = new _NGG_Galleries_List_Table( 'nggallery-manage-gallery' );

	?>

	<script>
		var $ = jQuery;
		// Listen for frame events
		$(function() {
			if ($(this).data('ready')) {
				return;
			}

			if (window.Frame_Event_Publisher) {
				// If a new gallery is added, refresh the page
				Frame_Event_Publisher.listen_for('attach_to_post:new_gallery attach_to_post:manage_images attach_to_post:images_added',function(){
					window.location.href = window.location.href.toString();
				});
			}

			$(this).data('ready', true);
		});

		function checkAll(form) {
			for (var i = 0, n = form.elements.length; i < n; i++) {
				if (form.elements[i].type === "checkbox") {
					if (form.elements[i].name === "doaction[]") {
						if (form.elements[i].checked == true) {
							form.elements[i].checked = false;
						} else {
							form.elements[i].checked = true;
						}
					}
				}
			}
		}

		function getNumChecked(form) {
			var num = 0;
			for (var i = 0, n = form.elements.length; i < n; i++) {
				if (form.elements[i].type === "checkbox") {
					if(form.elements[i].name === "doaction[]") {
						if (form.elements[i].checked == true) {
							num++;
						}
					}
				}
			}

			return num;
		}

		// this function check for a the number of selected images, sumbmit false when no one selected
		function checkSelected() {
			if (typeof document.activeElement == "undefined" && document.addEventListener) {
				document.addEventListener("focus", function (e) {
					document.activeElement = e.target;
				}, true);
			}

			if (document.activeElement.name === 'post_paged') {
				return true;
			}

			var numchecked = getNumChecked(document.getElementById('editgalleries'));

			if (numchecked < 1) {
				alert('<?php echo esc_js( __( 'No images selected', 'nggallery' ) ); ?>');
				return false;
			}

			var actionId = $('#bulkaction').val();

			switch (actionId) {
				case "resize_images":
					showDialog('resize_images', '<?php echo esc_js( __( 'Resize images', 'nggallery' ) ); ?>');
					return false;
					break;
				case "new_thumbnail":
					showDialog('new_thumbnail', '<?php echo esc_js( __( 'Create new thumbnails', 'nggallery' ) ); ?>');
					return false;
					break;
			}

			return confirm(
				'<?php printf( esc_js( __( "You are about to start the bulk edit for %s galleries \n \n 'Cancel' to stop, 'OK' to proceed.", 'nggallery' ) ), "' + numchecked + '" ); ?>'
			);
		}

		function showDialog(windowId, title) {
			var form = document.getElementById('editgalleries');
			var elementlist = "";
			for (var i = 0, n = form.elements.length; i < n; i++) {
				if (form.elements[i].type == "checkbox") {
					if (form.elements[i].name == "doaction[]") {
						if (form.elements[i].checked == true) {
							if (elementlist == "") {
								elementlist = form.elements[i].value;
							} else {
								elementlist += "," + form.elements[i].value;
							}
						}
					}
				}
			}
			$("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
			$("#" + windowId + "_imagelist").val(elementlist);

			// now show the dialog
			$("#" + windowId).dialog({
				width: 640,
				resizable: false,
				modal: true,
				title: title,
				position: {
					my: 'center',
					at: 'center',
					of: window.parent
				}
			});
			$("#" + windowId + ' .dialog-cancel').on('click', function() {
				$("#" + windowId).dialog("close");
			});

			$('.ui-dialog-titlebar-close').text('X');
		}

		function showAddGallery() {
			$("#addGallery").dialog({
				width: 640,
				resizable: false,
				modal: true,
				title: '<?php echo esc_js( __( 'Add new gallery', 'nggallery' ) ); ?>',
				position: {
					my: 'center',
					at: 'center',
					of: window.parent
				}
			});
			$("#addGallery .dialog-cancel").on('click', function() {
				$("#addGallery").dialog("close");
			});
		}
	</script>

	<?php if ( isset( $action_status ) && $action_status['message'] != '' ) { ?>
	<div id="message"
		class="<?php echo ( $action_status['status'] == 'ok' ? 'updated' : $action_status['status'] ); ?> fade">
		<p>
			<strong><?php echo $action_status['message']; ?></strong>
		</p>
	</div>
<?php } ?>

	<div class="wrap ngg_manage_galleries">
		<div class="ngg_page_content_header">
			<h3>
				<?php echo _n( 'Manage Galleries', 'Manage Galleries', 2, 'nggallery' ); ?>
			</h3>
		</div>

		<div class='ngg_page_content_main'>

			<div class="search-box-wrapper">

				<form class="search-form" action="" method="get">
					<div class="search-box">
						<input type="hidden"
								id="page-name"
								name="page"
								value="nggallery-manage-gallery"/>
						<input type="text"
								id="media-search-input"
								name="s"
								placeholder="<?php esc_attr_e( 'Search Images', 'nggallery' ); ?>"
								value="<?php the_search_query(); ?>"/>
						<input type="submit"
								value="<?php esc_attr_e( 'Search Images', 'nggallery' ); ?>"
								class="button-primary"/>
					</div>
				</form>

				<form class="search-form" action="" method="get">
					<div class="search-box">
						<input type="hidden"
								id="page-name"
								name="page"
								value="nggallery-manage-gallery"/>
						<input type="text"
								id="gallery-search-input"
								name="gs"
								placeholder="<?php esc_attr_e( 'Search Galleries', 'nggallery' ); ?>"
								value="<?php print ! empty( $_GET['gs'] ) ? esc_attr( trim( $_GET['gs'] ) ) : ''; ?>"/>
						<input type="submit"
								value="<?php esc_attr_e( 'Search Galleries', 'nggallery' ); ?>"
								class="button-primary"/>
					</div>
				</form>

			</div>

			<form id="editgalleries"
					class="nggform"
					method="POST"
					action="<?php echo \Imagely\NGG\Util\Router::esc_url( $ngg->manage_page->base_page . '&orderby=' . $orderby . '&order=' . $order . '&paged=' . $_GET['paged'] ); ?>"
					accept-charset="utf-8">

				<?php wp_nonce_field( 'ngg_bulkgallery' ); ?>

				<input type="hidden"
						name="nggpage"
						value="manage-galleries"/>



				<div class="tablenav top">

					<div class="alignleft actions">

						<?php if ( function_exists( 'json_encode' ) ) { ?>

							<select name="bulkaction" id="bulkaction">
								<option value="no_action">     <?php esc_html_e( 'Bulk actions', 'nggallery' ); ?></option>
								<option value="delete_gallery"><?php esc_html_e( 'Delete', 'nggallery' ); ?></option>
								<option value="set_watermark"> <?php esc_html_e( 'Set watermark', 'nggallery' ); ?></option>
								<option value="new_thumbnail"> <?php esc_html_e( 'Create new thumbnails', 'nggallery' ); ?></option>
								<option value="resize_images"> <?php esc_html_e( 'Resize images', 'nggallery' ); ?></option>
								<option value="import_meta">   <?php esc_html_e( 'Import metadata', 'nggallery' ); ?></option>
								<option value="recover_images"><?php esc_html_e( 'Recover from backup', 'nggallery' ); ?></option>
							</select>

							<input name="showThickbox"
									class="button-primary"
									type="submit"
									value="<?php esc_attr_e( 'Apply', 'nggallery' ); ?>"
									onclick="if (!checkSelected()) return false;"/>
						<?php } ?>

						<?php if ( current_user_can( 'NextGEN Upload images' ) && nggGallery::current_user_can( 'NextGEN Add new gallery' ) ) { ?>
							<input name="doaction"
									class="button-primary action"
									type="submit"
									onclick="showAddGallery(); return false;"
									value="<?php esc_attr_e( 'Add new gallery', 'nggallery' ); ?>"/>
						<?php } ?>

					</div>

					<?php $ngg->manage_page->pagination( 'top', $_GET['paged'], $total_number_of_galleries, $items_per_page ); ?>

				</div>

				<?php
				// Allows for additional content to be injected between the bulk actions and the actual tabular data.
				do_action( 'ngg_manage_galleries_above_table' );
				?>

				<table class="wp-list-table widefat" cellspacing="0">

					<thead>
						<tr>
							<?php $wp_list_table->print_column_headers( true ); ?>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<?php $wp_list_table->print_column_headers( false ); ?>
						</tr>
					</tfoot>

					<tbody id="the-list">
						<?php
						if ( $gallerylist ) {
							// get the columns.
							$gallery_columns = $wp_list_table->get_columns();
							$hidden_columns  = get_hidden_columns( 'nggallery-manage-gallery' );

							foreach ( $gallerylist as $gallery ) {
								$alternate   = ( ! isset( $alternate ) || $alternate == 'class="alternate"' ) ? '' : 'class="alternate"';
								$gid         = $gallery->gid;
								$name        = ( empty( $gallery->title ) ) ? $gallery->name : $gallery->title;
								$author_user = get_userdata( (int) $gallery->author );
								?>
								<tr id="gallery-<?php echo $gid; ?>" <?php echo $alternate; ?>>
									<?php
									foreach ( $gallery_columns as $gallery_column_key => $column_display_name ) {
										$class = "class='{$gallery_column_key} column-{$gallery_column_key}'";
										$style = '';
										if ( in_array( $gallery_column_key, $hidden_columns ) ) {
											$style = ' style="display:none;"';
										}

										$attributes = "{$class}{$style}";

										switch ( $gallery_column_key ) {
											case 'cb':
												?>
												<th scope="row" class="column-cb check-column">
													<?php if ( nggAdmin::can_manage_this_gallery( $gallery->author ) ) { ?>
														<input name="doaction[]" type="checkbox" value="<?php echo $gid; ?>"/>
													<?php } ?>
												</th>
												<?php
												break;
											case 'id':
												?>
												<td <?php echo $attributes; ?>>
													<?php echo $gid; ?>
												</td>
												<?php
												break;
											case 'title':
												?>
												<td class="title column-title">
													<?php if ( nggAdmin::can_manage_this_gallery( $gallery->author ) ) { ?>
														<a href="<?php echo wp_nonce_url( $ngg->manage_page->base_page . '&amp;mode=edit&amp;gid=' . $gid, 'ngg_editgallery' ); ?>"
															class='edit'
															title="<?php esc_attr_e( 'Edit', 'nggallery' ); ?>">
															<?php echo esc_html( \Imagely\NGG\Display\I18N::translate( $name ) ); ?>
														</a>
													<?php } else { ?>
														<?php echo esc_html( \Imagely\NGG\Display\I18N::translate( $gallery->title ) ); ?>
													<?php } ?>
													<div class="row-actions"></div>
												</td>
												<?php
												break;
											case 'description':
												?>
												<td <?php echo $attributes; ?>>
													<?php echo esc_html( \Imagely\NGG\Display\I18N::translate( $gallery->galdesc ) ); ?>
													&nbsp;
												</td>
												<?php
												break;
											case 'author':
												$author_string = $author_user === false ? __( 'Deleted user', 'nggallery' ) : $author_user->display_name;
												?>
												<td <?php echo $attributes; ?>>
													<?php echo esc_html( $author_string ); ?>
												</td>
												<?php
												break;
											case 'page_id':
												?>
												<td <?php echo $attributes; ?>>
													<?php echo $gallery->pageid; ?>
												</td>
												<?php
												break;
											case 'quantity':
												global $wpdb;
												$gallery->counter = $wpdb->get_var(
													$wpdb->prepare(
														"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE galleryid = %d",
														$gallery->{$gallery->id_field}
													)
												);
												?>
												<td <?php echo $attributes; ?>>
													<?php echo $gallery->counter; ?>
												</td>
												<?php
												break;
											default:
												?>
												<td <?php echo $attributes; ?>>
													<?php do_action( 'ngg_manage_gallery_custom_column', $gallery_column_key, $gid ); ?>
												</td>
												<?php
												break;
										}
									} // end foreach
									?>
								</tr>
								<?php
							} // end foreach
						} else {
							echo '<tr><td colspan="7" align="center"><strong>' . esc_html__( 'No entries found', 'nggallery' ) . '</strong></td></tr>';
						}
						?>
					</tbody>
				</table>

				<div class="tablenav bottom">
					<?php $ngg->manage_page->pagination( 'bottom', $_GET['paged'], $total_number_of_galleries, $items_per_page ); ?>
				</div>
			</form>

			<?php do_action( 'ngg_manage_galleries_marketing_block' ); ?>

		</div> <!-- /.ngg_page_content_main -->
	</div> <!-- /.wrap -->

	<!-- #addGallery -->
	<div id="addGallery"
		style="display: none;">
		<form id="form-tags"
				method="POST"
				accept-charset="utf-8">

			<?php wp_nonce_field( 'ngg_bulkgallery' ); ?>

			<input type="hidden"
					name="nggpage"
					value="manage-galleries"/>

			<table width="100%"
					border="0"
					cellspacing="3"
					cellpadding="3">
				<tr>
					<td>
						<strong><?php esc_html_e( 'New Gallery', 'nggallery' ); ?>:</strong>
						<input type="text"
								size="35"
								name="galleryname"
								value=""/>
						<br/>
						<?php if ( ! is_multisite() ) { ?>
							<?php esc_html_e( 'Create a new , empty gallery below the folder', 'nggallery' ); ?>
							<strong><?php echo $ngg->options['gallerypath']; ?></strong>
							<br/>
						<?php } ?>
						<i>(<?php esc_html_e( 'Allowed characters for file and folder names are', 'nggallery' ); ?>: a-z, A-Z, 0-9, -, _)</i>
					</td>
				</tr>

				<?php do_action( 'ngg_add_new_gallery_form' ); ?>

				<tr align="right">
					<td class="submit">
						<input class="button-primary"
								type="submit"
								name="addgallery"
								value="<?php esc_attr_e( 'OK', 'nggallery' ); ?>"/>
						&nbsp;
						<input class="button-primary dialog-cancel"
								type="reset"
								value="&nbsp;<?php esc_attr_e( 'Cancel', 'nggallery' ); ?>&nbsp;"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<!-- /#addGallery -->

	<!-- #resize_images -->
	<div id="resize_images" style="display: none;">
		<form id="form-resize-images" method="POST" accept-charset="utf-8">

			<?php wp_nonce_field( 'ngg_bulkgallery' ); ?>

			<input type="hidden"
					id="resize_images_imagelist"
					name="TB_imagelist"
					value=""/>

			<input type="hidden"
					id="resize_images_bulkaction"
					name="TB_bulkaction"
					value=""/>

			<input type="hidden"
					name="nggpage"
					value="manage-galleries"/>

			<table width="100%"
					border="0"
					cellspacing="3"
					cellpadding="3">

				<tr valign="top">
					<td>
						<strong><?php esc_html_e( 'Resize Images to', 'nggallery' ); ?>:</strong>
					</td>
					<td>
						<input type="text"
								size="5"
								name="imgWidth"
								value="<?php echo esc_attr( $ngg->options['imgWidth'] ); ?>"/>
						x
						<input type="text"
								size="5"
								name="imgHeight"
								value="<?php echo esc_attr( $ngg->options['imgHeight'] ); ?>"/>
						<br/>
						<small><?php esc_html_e( 'Width x height (in pixel). NextGEN Gallery will keep ratio size', 'nggallery' ); ?></small>
					</td>
				</tr>
				<tr align="right">
					<td colspan="2" class="submit">
						<input class="button-primary"
								type="submit"
								name="TB_ResizeImages"
								value="<?php esc_attr_e( 'OK', 'nggallery' ); ?>"/>
						&nbsp;
						<input class="button-primary dialog-cancel"
								type="reset"
								value="&nbsp;<?php esc_attr_e( 'Cancel', 'nggallery' ); ?>&nbsp;"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<!-- /#resize_images -->

	<!-- #new_thumbnail -->
	<div id="new_thumbnail"
		style="display: none;">

		<form id="form-new-thumbnail"
				method="POST"
				accept-charset="utf-8">

			<?php wp_nonce_field( 'ngg_bulkgallery' ); ?>

			<input type="hidden"
					id="new_thumbnail_imagelist"
					name="TB_imagelist"
					value=""/>

			<input type="hidden"
					id="new_thumbnail_bulkaction"
					name="TB_bulkaction"
					value=""/>

			<input type="hidden"
					name="nggpage"
					value="manage-galleries"/>

			<table width="100%"
					border="0"
					cellspacing="3"
					cellpadding="3">

				<tr valign="top">
					<th align="left">
						<?php esc_html_e( 'Width x height (in pixel)', 'nggallery' ); ?>
					</th>
					<td>
						<?php include __DIR__ . '/thumbnails-template.php'; ?>
					</td>
				</tr>

				<tr valign="top">
					<th align="left">
						<?php esc_html_e( 'Set fix dimension', 'nggallery' ); ?>
					</th>
					<td>
						<input type="checkbox"
								name="thumbfix"
								value="1"
							<?php checked( '1', $ngg->options['thumbfix'] ); ?>/>
						<br/>
						<small><?php esc_html_e( 'Ignore the aspect ratio, no portrait thumbnails', 'nggallery' ); ?></small>
					</td>
				</tr>

				<tr align="right">
					<td colspan="2" class="submit">
						<input class="button-primary"
								type="submit"
								name="TB_NewThumbnail"
								value="<?php esc_attr_e( 'OK', 'nggallery' ); ?>"/>
						&nbsp;
						<input class="button-primary dialog-cancel"
								type="reset"
								value="&nbsp;<?php esc_attr_e( 'Cancel', 'nggallery' ); ?>&nbsp;"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<!-- /#new_thumbnail -->

	<?php
}

/**
 * Constructor class to create the table layout
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 1.8.0
 * @access private
 */
class _NGG_Galleries_List_Table extends WP_List_Table {

	public $_screen;
	public $_columns;

	function __construct( $screen ) {
		if ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}

		$this->_screen  = $screen;
		$this->_columns = [];

		add_filter( 'manage_' . $screen->id . '_columns', [ &$this, 'get_columns' ], 0 );
	}

	function get_column_info() {
		$columns   = get_column_headers( $this->_screen );
		$hidden    = get_hidden_columns( $this->_screen );
		$_sortable = $this->get_sortable_columns();

		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) ) {
				continue;
			}

			$data = (array) $data;
			if ( ! isset( $data[1] ) ) {
				$data[1] = false;
			}

			$sortable[ $id ] = $data;
		}

		return [ $columns, $hidden, $sortable, null ];
	}

	// define the columns to display, the syntax is 'internal name' => 'display name'.
	function get_columns() {
		$columns = [];

		$columns['cb']          = '<input name="checkall" type="checkbox" onclick="checkAll(document.getElementById(\'editgalleries\'));"/>';
		$columns['id']          = __( 'ID', 'nggallery' );
		$columns['title']       = _n( 'Gallery', 'Galleries', 1, 'nggallery' );
		$columns['description'] = __( 'Description', 'nggallery' );
		$columns['author']      = __( 'Author', 'nggallery' );
		$columns['page_id']     = __( 'Page ID', 'nggallery' );
		$columns['quantity']    = _n( 'Image', 'Images', 2, 'nggallery' );

		$columns = apply_filters( 'ngg_manage_gallery_columns', $columns );

		return $columns;
	}

	function get_sortable_columns() {
		return [
			'id'     => [ 'gid', true ],
			'title'  => 'title',
			'author' => 'author',
		];
	}
}
