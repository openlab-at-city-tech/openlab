<?php


/**
 * Remove once get_pro_api_version() is > 4.0
 *
 * @deprecated
 */
function nextgen_esc_url( $url, $protocols = null, $context = 'display' ) {
	return \Imagely\NGG\Util\Router::esc_url( $url, $protocols, $context );
}

/**
 * @depecated
 */
function nggShowRelatedGallery( $taglist, $maxImages = 0 ) {
	return \Imagely\NGG\Display\DisplayManager::_render_related_string( $taglist, $maxImages, $type = null );
}

/**
 * @depecated
 */
function nggShowRelatedImages( $type = null, $maxImages = 0 ) {
	return \Imagely\NGG\Display\DisplayManager::_render_related_string( null, $maxImages, $type );
}

/**
 * @deprecated
 */
function the_related_images( $type = 'tags', $maxNumbers = 7 ) {
	return \Imagely\NGG\Display\DisplayManager::_render_related_string( null, $maxNumbers, $type );
}

/**
 * @depecated
 */
function nggShowImageBrowser( $galleryID, $template = '' ) {
	$renderer = \Imagely\NGG\DisplayedGallery\Renderer::get_instance();
	$retval   = $renderer->display_images(
		[
			'gallery_ids'  => [ $galleryID ],
			'display_type' => NGG_BASIC_IMAGEBROWSER,
			'template'     => $template,
		]
	);

	return apply_filters( 'ngg_show_imagebrowser_content', $retval, $galleryID );
}

/**
 * @deprecated
 */
function nggCreateImageBrowser( $picturelist, $template = '' ) {
	$renderer  = \Imagely\NGG\DisplayedGallery\Renderer::get_instance();
	$image_ids = [];
	foreach ( $picturelist as $image ) {
		$image_ids[] = $image->pid;
	}
	return $renderer->display_images(
		[
			'image_ids'    => $image_ids,
			'display_type' => NGG_BASIC_IMAGEBROWSER,
			'template'     => $template,
		]
	);
}

/**
 * @deprecated
 */
function nggShowSlideshow( $galleryID, $width, $height ) {
	$args = [
		'source'         => 'galleries',
		'container_ids'  => $galleryID,
		'gallery_width'  => $width,
		'gallery_height' => $height,
		'display_type'   => NGG_BASIC_SLIDESHOW,
	];

	echo \Imagely\NGG\DisplayedGallery\Renderer::get_instance()->display_images( $args );
}

/**
 * @deprecated
 */
function nggShowGallery( $galleryID, $template = '', $images_per_page = false ) {
	$args = [
		'source'        => 'galleries',
		'container_ids' => $galleryID,
	];

	if ( apply_filters( 'ngg_show_imagebrowser_first', false, $galleryID ) ) {
		$args['display_type'] = NGG_BASIC_IMAGEBROWSER;
	} else {
		$args['display_type'] = NGG_BASIC_THUMBNAILS;
	}

	if ( ! empty( $template ) ) {
		$args['template'] = $template;
	}
	if ( ! empty( $images_per_page ) ) {
		$args['images_per_page'] = $images_per_page;
	}

	echo \Imagely\NGG\DisplayedGallery\Renderer::get_instance()->display_images( $args );
}

/**
 * @deprecated
 */
function nggShowAlbum( $albumID, $template = 'extend', $gallery_template = '' ) {
	$renderer = \Imagely\NGG\DisplayedGallery\Renderer::get_instance();
	$retval   = $renderer->display_images(
		[
			'album_ids'                => [ $albumID ],
			'display_type'             => 'photocrati-nextgen_basic_extended_album',
			'template'                 => $template,
			'gallery_display_template' => $gallery_template,
		]
	);

	return apply_filters( 'ngg_show_album_content', $retval, $albumID );
}
