<?php
/**
 * Legacy admin sort management.
 *
 * @author Alex Rabe
 */

/**
 * Sorts gallery images by order.
 *
 * @param int $galleryID
 */
function nggallery_sortorder( $galleryID = 0 ) {
	global $wpdb, $nggdb;

	$action_status = [
		'message' => '',
		'status'  => 'ok',
	];

	if ( $galleryID == 0 ) {
		return;
	}

	$galleryID = (int) $galleryID;

	if ( isset( $_POST['updateSortorder'] ) ) {
		check_admin_referer( 'ngg_updatesortorder' );

		// get variable new sortorder.
		$sortArray = [];

		if ( ! empty( $_POST['sortorder'] ) ) {
			$sortArray = explode( ',', sanitize_text_field( wp_unslash( $_POST['sortorder'] ) ) );
		}

		if ( ! empty( $sortArray ) ) {
			$neworder = [];

			foreach ( $sortArray as $pid ) {
				$neworder[] = (int) $pid;
			}

			$sortindex = 1;

			foreach ( $neworder as $pic_id ) {
				// Scoped to $galleryID. The ids come straight from $_POST['sortorder'], so
				// without the galleryid predicate a sort submitted against a gallery the caller
				// owns rewrote the sortorder of any pid on the site - the gallery the handler was
				// handed never constrained the write. See issue #1093 (audit #7.7).
				//
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE `{$wpdb->nggpictures}` SET `sortorder` = %s WHERE `pid` = %d AND `galleryid` = %d",
						[
							$sortindex,
							$pic_id,
							$galleryID,
						]
					)
				);
				++$sortindex;
			}

			// The raw $wpdb writes above skip the mapper, so its cached result set is stale;
			// bump the version or the read below re-renders the pre-save order.
			\Imagely\NGG\DataMappers\Image::get_instance()->flush_query_cache();

			\Imagely\NGG\Util\Transient::flush( 'displayed_gallery_rendering' );

			do_action( 'ngg_gallery_sort', $galleryID );

			nggGallery::show_message( __( 'Sort order changed', 'nggallery' ) );
		}
	}

	// look for presort args.
	$presort   = isset( $_GET['presort'] ) ? sanitize_text_field( wp_unslash( $_GET['presort'] ) ) : false;
	$dir       = ( isset( $_GET['dir'] ) && sanitize_text_field( wp_unslash( $_GET['dir'] ) ) == 'DESC' ) ? 'DESC' : 'ASC';
	$sortitems = [ 'pid', 'filename', 'alttext', 'imagedate' ];

	// ensure that nobody added some evil sorting :-).
	// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
	if ( in_array( $presort, $sortitems ) ) {
		$picturelist = $nggdb->get_gallery( $galleryID, $presort, $dir, false );
	} else {
		$picturelist = $nggdb->get_gallery( $galleryID, 'sortorder', $dir, false );
	}

	// This is the url without any presort variable.
	$clean_url = 'admin.php?page=nggallery-manage-gallery&amp;mode=sort&amp;gid=' . $galleryID;

	// If we go back then the mode should be edit.
	$back_url = 'admin.php?page=nggallery-manage-gallery&amp;mode=edit&amp;gid=' . $galleryID;

	// In the case somebody presort, then we take this url.
	if ( isset( $_GET['dir'] ) || isset( $_GET['presort'] ) ) {
		$base_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // Fixed: sanitize_text_field preserved quotes/control chars in URL; esc_url_raw validates URL at ingestion before use in add_query_arg/output.
	} else {
		$base_url = $clean_url;
	}
	?>
	<script type="text/javascript">
		(function($) {
			$(function() {
				if ($(this).data('ready'))
					return;

				// Enable sorting
				$(".jqui-sortable").sortable({
					items: 'div.imageBox'
				});

				$('#sortGallery').on('submit',function (e) {
					var serial = "";
					var $images = $('div.imageBox');
					for (var i = 0; i < $images.length; i++) {
						var image_id = $images[i].id.split('-').pop();
						if (serial.length > 0) {
							serial = serial + ',';
						}
						serial = serial + image_id;
					}
					$('input[name=sortorder]').val(serial);
				});

				// Listen for events in other frames
				if (window.Frame_Event_Publisher) {
					Frame_Event_Publisher.listen_for('attach_to_post:manage_galleries attach_to_post:manage_images', function () {
						window.location.href = window.location.href.toString();
					});
				}

				$(this).data('ready', true);
			});
		})(jQuery);
	</script>

	<?php if ( $action_status['message'] != '' ) { ?>
		<div id="message"
			class="<?php echo esc_attr( ( $action_status['status'] == 'ok' ? 'updated' : $action_status['status'] ) ); ?> fade">
			<p><strong><?php echo esc_html( $action_status['message'] ); ?></strong></p>
		</div>
	<?php } ?>

	<div class="wrap ngg_gallery_sort">
		<div class="ngg_page_content_header">
			<h3><?php esc_html_e( 'Sort Gallery', 'nggallery' ); ?></h3>
		</div>

		<div class='ngg_page_content_main'>

			<form id="sortGallery"
					method="POST"
					action="<?php echo esc_url( $clean_url ); ?>" accept-charset="utf-8">

				<div class="tablenav">
					<div class="alignleft actions">
						<?php wp_nonce_field( 'ngg_updatesortorder' ); ?>
						<input class="button-primary action"
								type="submit"
								name="updateSortorder"
								value="<?php echo esc_attr( __( 'Update Sort Order', 'nggallery' ) ); ?>"/>
					</div>
					<div class="alignright actions">
						<a href="
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
						print \Imagely\NGG\Util\Router::esc_url( $back_url );
						?>
						"
							class="button-primary">
							<?php esc_html_e( 'Back to gallery', 'nggallery' ); ?>
						</a>
					</div>
				</div>

				<input name="sortorder" type="hidden"/>

				<ul class="subsubsub">
					<li><?php esc_html_e( 'Presort', 'nggallery' ); ?>:</li>
					<li><a href="<?php print esc_url( remove_query_arg( 'presort', $base_url ) ); ?>"<?php // Fixed: esc_attr wrong context for href; esc_url blocks javascript:/data: schemes in URL attribute. ?>
											<?php
											if ( $presort == '' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Unsorted', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'presort', 'pid', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url (scheme validation), not esc_attr. ?>
											<?php
											if ( $presort == 'pid' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Image ID', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'presort', 'filename', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url, not esc_attr. ?>
											<?php
											if ( $presort == 'filename' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Filename', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'presort', 'alttext', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url, not esc_attr. ?>
											<?php
											if ( $presort == 'alttext' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Alt/Title text', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'presort', 'imagedate', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url, not esc_attr. ?>
											<?php
											if ( $presort == 'imagedate' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Date/Time', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'dir', 'ASC', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url, not esc_attr. ?>
											<?php
											if ( $dir == 'ASC' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Ascending', 'nggallery' ); ?></a> |</li>
					<li><a href="<?php print esc_url( add_query_arg( 'dir', 'DESC', $base_url ) ); ?>"<?php // Fixed: href context needs esc_url, not esc_attr. ?>
											<?php
											if ( $dir == 'DESC' ) {
												print 'class="current"';}
											?>
					><?php esc_html_e( 'Descending', 'nggallery' ); ?></a></li>
				</ul>
			</form>

			<div id="debug" style="clear:both"></div>
			<div id='ngg-sort-gallery-container' class='jqui-sortable'>
				<?php
				if ( $picturelist ) {
					foreach ( $picturelist as $picture ) {
						// Assemble the url('...') value in a PHP string so no template
						// whitespace can land inside it; a newline there is a CSS parse
						// error and blanks the thumbnail.
						// Admin-only cache-buster: thumbURL is canonical now, so an edited
						// image would keep its old pixels here without this.
						$thumb_url = $picture->thumbURL;
						if ( ! empty( $picture->updated_at ) ) {
							$thumb_url = add_query_arg( 't', $picture->updated_at, $thumb_url );
						}
						$thumb_style = "background-image:url('" . \Imagely\NGG\Util\Router::esc_url( $thumb_url ) . "')";
						?>
						<div class="imageBox"
							id="pid-<?php print esc_attr( $picture->pid ); ?>">
							<div class="imageBox_theImage"
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() applied to the URL above ?>
								style="<?php print $thumb_style; ?>">
							</div>
							<div class="imageBox_label">
								<span><?php print esc_html( stripslashes( $picture->alttext ?? '' ) ); ?></span>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>

		</div> <!-- /ngg_page_content_main -->

		<?php do_action( 'ngg_sort_images_marketing_block' ); ?>

	</div>

	<?php
}