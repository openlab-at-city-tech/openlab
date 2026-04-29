<?php

add_filter( 'media_upload_tabs', 'ngg_wp_upload_tabs' );
add_action( 'media_upload_nextgen', 'media_upload_nextgen' );

// TODO: attempting to enforce the following breaks the TinyMCE 'Add Media' button's import-from-NextGEN feature, and
// TODO: no nonce is provided by WordPress at the time.
//
// phpcs:disable WordPress.Security.NonceVerification.Missing
// phpcs:disable WordPress.Security.NonceVerification.Recommended

function ngg_wp_upload_tabs( $tabs ) {
	$newtab = [ 'nextgen' => __( 'NextGEN Gallery', 'nggallery' ) ];
	return array_merge( $tabs, $newtab );
}

function media_upload_nextgen() {
	// Not in use.
	$errors = false;

	// Generate TinyMCE HTML output.
	if ( isset( $_POST['send'] ) ) {
		$keys        = array_keys( isset( $_POST['send'] ) ? map_deep( wp_unslash( $_POST['send'] ), 'sanitize_text_field' ) : [] );
		$send_id     = (int) array_shift( $keys );
		$image       = isset( $_POST['image'][ $send_id ] ) ? map_deep( wp_unslash( $_POST['image'][ $send_id ] ), 'sanitize_text_field' ) : [];
		$alttext     = stripslashes( htmlspecialchars( $image['alttext'], ENT_QUOTES ) );
		$description = stripslashes( htmlspecialchars( $image['description'], ENT_QUOTES ) );

		// here is no new line allowed.
		$clean_description = preg_replace( "/\n|\r\n|\r$/", ' ', $description );
		$img               = nggdb::find_image( $send_id );
		$thumbcode         = $img->get_thumbcode();
		$class             = "ngg-singlepic ngg-{$image['align']}";

		// Create a shell displayed-gallery so we can inspect its settings.
		$args               = new stdClass();
		$args->display_type = NGG_BASIC_SINGLEPIC;
		$displayed_gallery  = new \Imagely\NGG\DataTypes\DisplayedGallery( $args );

		$width  = $displayed_gallery->display_settings['width'];
		$height = $displayed_gallery->display_settings['height'];

		// Build output.
		if ( $image['size'] == 'thumbnail' ) {
			$html = "<img src='{$image['url']}' alt='{$alttext}' class='{$class}' />";
		} else {
			$html = '';
		}

		// Wrap the link to the fullsize image around.
		$html = "<a {$thumbcode} href='{$image['url']}' title='{$clean_description}'>{$html}</a>";

		if ( $image['size'] == 'full' ) {
			$html = "<img src='{$image['url']}' alt='{$alttext}' class='{$class}' />";
		}

		if ( $image['size'] == 'singlepic' ) {
			$html = "[singlepic id={$send_id} w={$width} h={$height} float={$image['align']}]";
		}

		media_upload_nextgen_save_image();

		// Return it to TinyMCE.
		return media_send_to_editor( $html );
	}

	// Save button.
	if ( isset( $_POST['save'] ) ) {
		media_upload_nextgen_save_image();
	}

	wp_iframe( 'media_upload_nextgen_form', $errors );
	die();
}

function media_upload_nextgen_save_image() {
	global $wpdb;

	check_admin_referer( 'ngg-media-form' );

	if ( ! empty( $_POST['image'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_unslash only removes slashes, values are sanitized via map_deep on line 84
		foreach ( wp_unslash( $_POST['image'] ) as $image_id => $image ) {
			$image_id = sanitize_text_field( wp_unslash( $image_id ) );
			$image    = map_deep( $image, 'sanitize_text_field' );
			// create a unique slug.
			$image_slug = nggdb::get_unique_slug( sanitize_title( $image['alttext'] ), 'image' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->nggpictures} SET `image_slug` = %s, `alttext` = %s, `description` = %s WHERE pid = %d",
					[
						$image_slug,
						$image['alttext'],
						$image['description'],
						$image_id,
					]
				)
			);
			wp_cache_delete( $image_id, 'ngg_image' );
		}
	}
}

function media_upload_nextgen_form( $errors ) {
	global $wpdb, $ngg;

	media_upload_header();

	$from           = isset( $_GET['from'] ) && 'block-editor' === sanitize_text_field( wp_unslash( $_GET['from'] ) ) ? 'block-editor' : 'classic-editor';
	$post_id        = isset( $_REQUEST['post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) ) : 0;
	$galleryID      = 0;
	$total          = 1;
	$picarray       = [];
	$chromeless     = isset( $_GET['chromeless'] ) ? sanitize_text_field( wp_unslash( $_GET['chromeless'] ) ) : null;
	$chromeless_url = $chromeless ? ( '&chromeless=' . $chromeless ) : null;

	$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=nextgen&post_id=$post_id" . $chromeless_url, 'admin' );

	// Get number of images in gallery.
	if ( isset( $_REQUEST['select_gal'] ) ) {
		$galleryID = (int) sanitize_text_field( wp_unslash( $_REQUEST['select_gal'] ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE `galleryid` = %d",
				[
					$galleryID,
				]
			)
		);
	}

	// Build navigation.
	$paged = isset( $_GET['paged'] ) ? intval( sanitize_text_field( wp_unslash( $_GET['paged'] ) ) ) : 0;
	if ( $paged < 1 ) {
		$paged = 1;
	}
	$start = ( $paged - 1 ) * 10;
	if ( $start < 1 ) {
		$start = 0;
	}

	// Get the images.
	if ( $galleryID != 0 ) {
		// Using %i in $wpdb->prepare() to signify column identifiers was only added in WP 6.2
		//
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$picarray = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT `pid` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d AND `exclude` != 1 ORDER BY {$ngg->options['galSort']}, `pid` {$ngg->options['galSortDir']} LIMIT {$start}, 10",
				[
					$galleryID,
				]
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	// WP-Core code for Post-thumbnail.
	$calling_post_id = 0;
	if ( isset( $_GET['post_id'] ) ) {
		$calling_post_id = intval( sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) );
	}
	?>

<script>
	function NGGSetAsThumbnail(id, wp_nonce, ngg_nonce) {
		if (top.set_ngg_post_thumbnail) {
			top.set_ngg_post_thumbnail(id, wp_nonce);
			return;
		}

		var $link = jQuery('a#ngg-post-thumbnail-' + id);

		$link.text(setPostThumbnailL10n.saving);
		jQuery.post(
			ajaxurl,
			{
				action: "ngg_set_post_thumbnail",
				post_id: post_id,
				thumbnail_id: id,
				cookie: encodeURIComponent(document.cookie),
				nonce: ngg_nonce
		},
		function(str) {
			var win = window.dialogArguments || opener || parent || top;
			$link.text( setPostThumbnailL10n.setThumbnail );
			if ( str == '0' ) {
				alert( setPostThumbnailL10n.error );
			} else if (str == '-1') {
				// image removed
			} else {
				jQuery('a.ngg-post-thumbnail').each(function() { jQuery(this).show(); });
				jQuery('a.ngg-post-thumbnail-standin').each(function() { jQuery(this).hide(); });
				$link.hide();

				var $dummy = $link.next();
				$dummy.attr('id', 'wp-post-thumbnail-' + str);
				$dummy.show();
				WPSetAsThumbnail(str, wp_nonce);
			}
		});
	}
</script>

<form id="filter" action="" method="get">
<input type="hidden" name="from" value="<?php echo esc_attr( $from ); ?>"/>
<input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
<input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
	<?php
	if ( $chromeless ) {
		?>
<input type="hidden" name="chromeless" value="<?php echo esc_attr( $chromeless ); ?>" />
		<?php
	}
	?>
<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />

<div class="tablenav">
	<?php
	$page_links = paginate_links(
		[
			'base'    => add_query_arg( 'paged', '%#%' ),
			'format'  => '',
			'total'   => ceil( $total / 10 ),
			'current' => $paged,
		]
	);

	if ( $page_links ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $page_links contains safe pagination HTML from WordPress core
		echo "<div class='tablenav-pages'>$page_links</div>";
	}
	?>

	<div class="alignleft actions">
		<select id="select_gal" name="select_gal" style="width:220px;">
			<option value="0" <?php selected( '0', $galleryID ); ?> ><?php esc_attr_e( 'No gallery', 'nggallery' ); ?></option>
			<?php
			// Show gallery selection.
			$gallerylist = \Imagely\NGG\DataMappers\Gallery::get_instance()->find_all();
			if ( is_array( $gallerylist ) ) {
				foreach ( $gallerylist as $gallery ) {
					$selected      = ( $gallery->gid == $galleryID ) ? ' selected="selected"' : '';
					$gallery_title = apply_filters( 'ngg_gallery_title_select_field', $gallery->title, $gallery, $gallery->gid == $galleryID );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $selected contains safe HTML attribute 'selected="selected"' or empty string
					echo '<option value="' . esc_attr( $gallery->gid ) . '"' . $selected . '>' . esc_html( $gallery_title ) . "</option>\n";
				}
			}
			?>
		</select>
		<input type="submit" id="show-gallery" value="<?php esc_attr_e( 'Select &#187;', 'nggallery' ); ?>" class="button-secondary" />
	</div>
	<br style="clear:both;" />
</div>
</form>
<br style="clear:both;"/>
<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $form_action_url ); ?>" class="media-upload-form" id="library-form">

	<?php wp_nonce_field( 'ngg-media-form' ); ?>

	<script type="text/javascript">
	<!--
	jQuery(function($){
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
			updateMediaForm();
		}
	});
	-->
	</script>

	<style type="text/css">
		.ngg-from-block-editor .ml-submit,
		.ngg-from-block-editor .describe .alttext,
		.ngg-from-block-editor .describe .caption,
		.ngg-from-block-editor .describe .align,
		.ngg-from-block-editor .describe .alttext,
		.ngg-from-block-editor .describe .image-size,
		.ngg-from-block-editor .describe .ngg-mlitp
		{
			display: none;
		}

		#media-items .media-item {
			min-height: 77px;
		}
		/* 
		* The .pinkynail class is added by WordPress core via the prepareMediaItem() JS function.
		* Constrain its size to maintain consistent row height.
		*/
		#media-items .media-item img.pinkynail {
			width: 70px;
			height: 50px;
			object-fit: cover;
		}
	</style>

	<div id="media-items" class="ngg-from-<?php echo esc_attr( $from ); ?>">
	<?php
	if ( is_array( $picarray ) ) {
		$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$calling_post_id" );
		$storage    = \Imagely\NGG\DataStorage\Manager::get_instance();
		foreach ( $picarray as $picid ) {
			// TODO:Reduce SQL Queries.
			$picture    = nggdb::find_image( $picid );
			$dimensions = $storage->get_image_dimensions( $picid, 'thumb' );
			extract( $dimensions );
			$thumb_url = $storage->get_image_url( $picid, 'thumb' );
			?>

			<div id='media-item-<?php echo esc_attr( $picid ); ?>' class='media-item preloaded'>
			<div class='filename'></div>
			<a class='toggle describe-toggle-on' href='#'><?php esc_html_e( 'Show', 'nggallery' ); ?></a>
			<a class='toggle describe-toggle-off' href='#'><?php esc_html_e( 'Hide', 'nggallery' ); ?></a>
			<div class='filename new'><?php echo ( empty( $picture->alttext ) ) ? esc_html( wp_html_excerpt( $picture->filename, 60 ) ) : esc_html( stripslashes( wp_html_excerpt( $picture->alttext, 60 ) ) ); ?></div>
			<table class='slidetoggle describe startclosed'><tbody>
				<tr class="thumb">
					<td rowspan='4'><img class='thumbnail' alt='<?php echo esc_attr( $picture->alttext ); ?>' src='<?php echo esc_url( $thumb_url ); ?>'/></td>
					<td><?php esc_html_e( 'Image ID:', 'nggallery' ); ?><?php echo esc_html( $picid ); ?></td>
				</tr>
				<tr><td><?php echo esc_html( $picture->filename ); ?></td></tr>
				<tr><td><?php echo esc_html( stripslashes( $picture->alttext ?? '' ) ); ?></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr class="alttext">
					<td class="label"><label for="image[<?php echo esc_attr( $picid ); ?>][alttext]"><?php esc_attr_e( 'Alt/Title text', 'nggallery' ); ?></label></td>
					<td class="field"><input id="image[<?php echo esc_attr( $picid ); ?>][alttext]" name="image[<?php echo esc_attr( $picid ); ?>][alttext]" value="<?php echo esc_attr( stripslashes( $picture->alttext ) ); ?>" type="text"/></td>
				</tr>
				<tr class="caption">
					<td class="label"><label for="image[<?php echo esc_attr( $picid ); ?>][description]"><?php esc_attr_e( 'Description', 'nggallery' ); ?></label></td>
						<td class="field"><textarea name="image[<?php echo esc_attr( $picid ); ?>][description]" id="image[<?php echo esc_attr( $picid ); ?>][description]"><?php echo esc_attr( stripslashes( $picture->description ) ); ?></textarea></td>
				</tr>
					<tr class="align">
						<td class="label"><label for="image[<?php echo esc_attr( $picid ); ?>][align]"><?php esc_attr_e( 'Alignment', 'nggallery' ); ?></label></td>
						<td class="field">
							<input name="image[<?php echo esc_attr( $picid ); ?>][align]" id="image-align-none-<?php echo esc_attr( $picid ); ?>" checked="checked" value="none" type="radio" />
							<label for="image-align-none-<?php echo esc_attr( $picid ); ?>" class="align image-align-none-label"><?php esc_attr_e( 'None', 'nggallery' ); ?></label>
							<input name="image[<?php echo esc_attr( $picid ); ?>][align]" id="image-align-left-<?php echo esc_attr( $picid ); ?>" value="left" type="radio" />
							<label for="image-align-left-<?php echo esc_attr( $picid ); ?>" class="align image-align-left-label"><?php esc_attr_e( 'Left', 'nggallery' ); ?></label>
							<input name="image[<?php echo esc_attr( $picid ); ?>][align]" id="image-align-center-<?php echo esc_attr( $picid ); ?>" value="center" type="radio" />
							<label for="image-align-center-<?php echo esc_attr( $picid ); ?>" class="align image-align-center-label"><?php esc_attr_e( 'Center', 'nggallery' ); ?></label>
							<input name="image[<?php echo esc_attr( $picid ); ?>][align]" id="image-align-right-<?php echo esc_attr( $picid ); ?>" value="right" type="radio" />
							<label for="image-align-right-<?php echo esc_attr( $picid ); ?>" class="align image-align-right-label"><?php esc_attr_e( 'Right', 'nggallery' ); ?></label>
						</td>
					</tr>
					<tr class="image-size">
						<th class="label"><label for="image[<?php echo esc_attr( $picid ); ?>][size]"><span class="alignleft"><?php esc_attr_e( 'Size', 'nggallery' ); ?></span></label>
						</th>
						<td class="field">
							<input name="image[<?php echo esc_attr( $picid ); ?>][size]" id="image-size-thumb-<?php echo esc_attr( $picid ); ?>" type="radio" checked="checked" value="thumbnail" />
							<label for="image-size-thumb-<?php echo esc_attr( $picid ); ?>"><?php esc_attr_e( 'Thumbnail', 'nggallery' ); ?></label>
							<input name="image[<?php echo esc_attr( $picid ); ?>][size]" id="image-size-full-<?php echo esc_attr( $picid ); ?>" type="radio" value="full" />
							<label for="image-size-full-<?php echo esc_attr( $picid ); ?>"><?php esc_attr_e( 'Full size', 'nggallery' ); ?></label>
							<input name="image[<?php echo esc_attr( $picid ); ?>][size]" id="image-size-singlepic-<?php echo esc_attr( $picid ); ?>" type="radio" value="singlepic" />
							<label for="image-size-singlepic-<?php echo esc_attr( $picid ); ?>"><?php esc_attr_e( 'Singlepic', 'nggallery' ); ?></label>
						</td>
					</tr>
				<tr class="submit">
						<td>
							<input type="hidden" name="image[<?php echo esc_attr( $picid ); ?>][thumb]" value="<?php echo esc_attr( $picture->thumbURL ); ?>" />
							<input type="hidden" name="image[<?php echo esc_attr( $picid ); ?>][url]" value="<?php echo esc_attr( $picture->imageURL ); ?>" />
						</td>
						<td class="savesend">
							<?php
							if ( $calling_post_id && current_theme_supports( 'post-thumbnails', get_post_type( $calling_post_id ) ) ) {
								$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$calling_post_id" );
							}
								$second_nonce = wp_create_nonce( 'ngg_set_post_thumbnails' );
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Complex HTML structure with onclick handler for WordPress media functionality
								echo "<a class='ngg-post-thumbnail' id='ngg-post-thumbnail-" . $picid . "' href='#' onclick='NGGSetAsThumbnail(\"$picid\", \"$ajax_nonce\", \"$second_nonce\"); return false;'>" . esc_html__( 'Use as featured image', 'nggallery' ) . '</a>';
								echo "<a class='ngg-post-thumbnail-standin' href='#' style='display:none;'></a>";
							?>
							<button type="submit" id="ngg-mlitp-<?php echo esc_attr( $picid ); ?>" class="button ngg-mlitp" value="1" name="send[<?php echo esc_attr( $picid ); ?>]"><?php esc_html_e( 'Insert into Post', 'nggallery' ); ?></button>
						</td>
				</tr>
			</tbody></table>
			</div>
			<?php
		}
	}
	?>
	</div>
	<p class="ml-submit">
		<input type="submit" class="button savebutton" name="save" value="<?php esc_attr_e( 'Save all changes', 'nggallery' ); ?>" />
	</p>
	<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
	<input type="hidden" name="select_gal" id="select_gal" value="<?php echo (int) $galleryID; ?>" />
</form>

<script type="text/javascript">
jQuery(function($) {
	if (window.location.toString().indexOf('block-editor') == -1) {
		// reset the media library modal tab
		var mlmodal = top.wp.media.editor.get();
		if (mlmodal) {
			mlmodal.on('close', function() {
				mlmodal.setState('insert');
			});
		}
	}
});
</script>

	<?php
}
