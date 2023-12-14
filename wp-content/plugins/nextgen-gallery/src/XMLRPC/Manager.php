<?php
/**
 * XMLRPC Manager
 *
 * @since 3.5.0
 *
 * @package Nextgen Gallery
 */

namespace Imagely\NGG\XMLRPC;

/**
 * Manager Class
 *
 * @since 3.5.0
 */
class Manager {

	/**
	 * Controller object
	 *
	 * @since 3.5.0
	 *
	 * @var object
	 */
	protected static $nextgen_api_xmlrpc = null;

	/**
	 * Add Methods
	 *
	 * @since 3.5.0
	 *
	 * @param array $methods Holds Methods.
	 *
	 * @return array
	 */
	public static function add_methods( $methods ) {
		self::$nextgen_api_xmlrpc = new Controller();

		$methods['ngg.installed']        = [ self::$nextgen_api_xmlrpc, 'get_version' ];
		$methods['ngg.setPostThumbnail'] = [ self::$nextgen_api_xmlrpc, 'set_post_thumbnail' ];

		// Image methods.
		$methods['ngg.getImage']    = [ self::$nextgen_api_xmlrpc, 'get_image' ];
		$methods['ngg.getImages']   = [ self::$nextgen_api_xmlrpc, 'get_images' ];
		$methods['ngg.uploadImage'] = [ self::$nextgen_api_xmlrpc, 'upload_image' ];
		$methods['ngg.editImage']   = [ self::$nextgen_api_xmlrpc, 'edit_image' ];
		$methods['ngg.deleteImage'] = [ self::$nextgen_api_xmlrpc, 'delete_image' ];

		// Gallery methods.
		$methods['ngg.getGallery']    = [ self::$nextgen_api_xmlrpc, 'get_gallery' ];
		$methods['ngg.getGalleries']  = [ self::$nextgen_api_xmlrpc, 'get_galleries' ];
		$methods['ngg.newGallery']    = [ self::$nextgen_api_xmlrpc, 'create_gallery' ];
		$methods['ngg.editGallery']   = [ self::$nextgen_api_xmlrpc, 'edit_gallery' ];
		$methods['ngg.deleteGallery'] = [ self::$nextgen_api_xmlrpc, 'delete_gallery' ];

		// Album methods.
		$methods['ngg.getAlbum']    = [ self::$nextgen_api_xmlrpc, 'get_album' ];
		$methods['ngg.getAlbums']   = [ self::$nextgen_api_xmlrpc, 'get_albums' ];
		$methods['ngg.newAlbum']    = [ self::$nextgen_api_xmlrpc, 'create_album' ];
		$methods['ngg.editAlbum']   = [ self::$nextgen_api_xmlrpc, 'edit_album' ];
		$methods['ngg.deleteAlbum'] = [ self::$nextgen_api_xmlrpc, 'delete_album' ];

		return $methods;
	}
}
