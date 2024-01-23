<?php
/**
 * Main PHP Class for XML Image Sitemaps
 *
 * @author       Alex Rabe
 * @version      1.0
 * @copyright    Copyright 2011
 */
class nggSitemaps {

	public $images = [];

	function __construct() {
		add_filter( 'wpseo_sitemap_urlimages', array( &$this, 'add_wpseo_xml_sitemap_images' ), 10, 2 );
	}

	/**
	 * Filter support for WordPress SEO by Yoast 0.4.0 or higher ( http://wordpress.org/extend/plugins/wordpress-seo/ )
	 *
	 * @since Version 1.8.0
	 * @param array $images
	 * @param int   $post_id
	 * @return array $image list of all founded images
	 */
	function add_wpseo_xml_sitemap_images( $images, $post_id ) {

		$this->images = $images;

		// first get the content of the post/page
		$p = get_post( $post_id );

		// Backward check for older images
		$p->post_content = NextGEN_shortcodes::convert_shortcode( $p->post_content );

		// Don't process the images in the normal way
		remove_all_shortcodes();

		// We cannot parse at this point a album, just galleries & single images
		\Imagely\NGG\Display\Shortcodes::add( 'singlepic', array( &$this, 'add_images' ) );
		\Imagely\NGG\Display\Shortcodes::add( 'thumb', array( &$this, 'add_images' ) );
		\Imagely\NGG\Display\Shortcodes::add( 'nggallery', array( &$this, 'add_gallery' ) );
		\Imagely\NGG\Display\Shortcodes::add( 'imagebrowser', array( &$this, 'add_gallery' ) );
		\Imagely\NGG\Display\Shortcodes::add( 'slideshow', array( &$this, 'add_gallery' ) );

		// Search now for shortcodes
		do_shortcode( $p->post_content );

		return $this->images;
	}

	/**
	 * Parse the gallery/imagebrowser/slideshow shortcode and return all images into an array
	 *
	 * @TODO: replace or remove this function, it's return value isn't even linked to the queries it performs
	 * @param string $atts
	 * @return string
	 */
	function add_gallery( $atts ) {

		global $wpdb;

		$tmp = shortcode_atts( array( 'id' => 0 ), $atts, 'ngg' );
		extract( $tmp );

		$gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
		if (!is_numeric( $id )) {
			$tmp = $gallery_mapper->select()->where( array( 'name = %s', $id ) )->limit( 1 )->run_query();
			if (( $gallery = array_shift( $tmp ) )) {
				$id = $gallery->{$gallery->id_field};
			} else {
				$id = null;
			}
		}

		if ($id) {
			$gallery_storage = \Imagely\NGG\DataStorage\Manager::get_instance();
			$image_mapper    = \Imagely\NGG\DataMappers\Image::get_instance();
			foreach ($image_mapper->find_all_for_gallery( $id ) as $image) {
				$this->images[] = array(
					'src'   =>  $gallery_storage->get_image_url( $image ),
					'title' =>  $image->title,
					'alt'   =>  $image->alttext,
				);
			}
		}

		return '';
	}

	/**
	 * Parse the single image shortcode and return all images into an array
	 *
	 * @param array $atts
	 * @return string
	 */
	function add_images( $atts ) {

		$tmp = shortcode_atts( array( 'id' => 0 ), $atts, 'ngg' );
		extract( $tmp );

		// make an array out of the ids (for thumbs shortcode))
		$pids = explode( ',', $id );

		// Some error checks
		if ( count( $pids ) == 0 ) {
			return '';
		}

		$images = nggdb::find_images_in_list( $pids );

		foreach ($images as $image) {
			$newimage        = array();
			$newimage['src'] = $newimage['sc'] = $image->imageURL;
			if ( !empty( $image->title ) ) {
				$newimage['title'] = $image->title;
			}
			if ( !empty( $image->alttext ) ) {
				$newimage['alt'] = $image->alttext;
			}
			$this->images[] = $newimage;
		}

		return '';
	}
}
$nggSitemaps = new nggSitemaps();
