<?php

namespace Imagely\NGG\XMLRPC;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Util\Security;

class Controller {

	/**
	 * Gets the version of NextGEN Gallery installed
	 *
	 * @return array
	 */
	public function get_version() {
		return [ 'version' => NGG_PLUGIN_VERSION ];
	}

	/**
	 * Login a user
	 *
	 * @param string $username
	 * @param string $password
	 * @param int    $blog_id
	 * @return bool|\WP_Error|\WP_User
	 */
	public function _login( $username, $password, $blog_id = 1 ) {
		$retval = false;

		if ( ! is_a( ( $user_obj = wp_authenticate( $username, $password ) ), 'WP_Error' ) ) {
			wp_set_current_user( $user_obj->ID );
			$retval = $user_obj;
			if ( is_multisite() ) {
				switch_to_blog( $blog_id );
			}
		}

		return $retval;
	}

	public function _can_manage_gallery( $gallery_id_or_obj, $check_upload_capability = false ) {
		$retval = false;

		// Get the gallery object, if we don't have it already.
		if ( is_int( $gallery_id_or_obj ) ) {
			$gallery_mapper = GalleryMapper::get_instance();
			$gallery        = $gallery_mapper->find( $gallery_id_or_obj );
		} else {
			$gallery = $gallery_id_or_obj;
		}

		if ( $gallery ) {
			if ( \get_current_user_id() === $gallery->author ) {
				$retval = true;
			} elseif ( Security::is_allowed( 'nextgen_edit_gallery_unowned' ) ) {
				$retval = true;
			}

			// Optionally, check if the user can upload to this gallery.
			if ( $retval && $check_upload_capability ) {
				$retval = Security::is_allowed( 'nextgen_upload_image' );
			}
		}

		return $retval;
	}

	public function _add_gallery_properties( $gallery ) {
		if ( is_object( $gallery ) ) {
			$image_mapper = ImageMapper::get_instance();
			$storage      = StorageManager::get_instance();

			// Vladimir's Lightroom plugins requires the 'id' to be a string
			// Ask if he can accept integers as well. Currently, integers break
			// his plugin.
			$gallery->gid = (string) $gallery->gid;

			// Set other gallery properties.
			$tmp              = $image_mapper->select( 'DISTINCT COUNT(*) as counter' )->where( [ 'galleryid = %d', $gallery->gid ] )->run_query( false, false, true );
			$image_counter    = array_pop( $tmp );
			$gallery->counter = $image_counter->counter;
			$gallery->abspath = $storage->get_gallery_abspath( $gallery );
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Returns a single image object
	 *
	 * @param array $args (blog_id, username, password, pid)
	 * @param bool  $return_model (optional)
	 * @return object|\IXR_Error
	 */
	public function get_image( $args, $return_model = false ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );
		$image_id = intval( $args[3] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Try to find the image.
			$image_mapper = ImageMapper::get_instance();
			if ( ( $image = $image_mapper->find( $image_id, true ) ) ) {
				// Try to find the gallery that the image belongs to.
				$gallery_mapper = GalleryMapper::get_instance();
				if ( ( $gallery = $gallery_mapper->find( $image->galleryid ) ) ) {
					// Does the user have sufficient capabilities?
					if ( $this->_can_manage_gallery( $gallery ) ) {
						$storage         = StorageManager::get_instance();
						$image->imageURL = $storage->get_image_url( $image, 'full', true );
						$image->thumbURL = $storage->get_image_url( $image, 'thumb' );

						$image->imagePath = $storage->get_image_abspath( $image );
						$image->thumbPath = $storage->get_thumb_abspath( $image );
						$retval           = $image;
					} else {
						$retval = new \IXR_Error( 403, "You don't have permission to manage gallery #{$image->galleryid}" );
					}
				} else {
					// No gallery found.
					$retval = new \IXR_Error( 404, "Gallery not found (with id #{$image->galleryid})" );
				}
			} else {
				// No image found.
				$retval = new \IXR_Error( 404, "Image not found (with id #{$image_id})" );
			}
		}

		return $retval;
	}

	/**
	 * Returns a collection of images
	 *
	 * @param array $args (blog_id, username, password, gallery_id
	 * @return array|\IXR_Error
	 */
	public function get_images( $args ) {
		$retval     = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id    = intval( $args[0] );
		$username   = strval( $args[1] );
		$password   = strval( $args[2] );
		$gallery_id = intval( $args[3] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Try to find the gallery.
			$mapper = GalleryMapper::get_instance();
			if ( ( $gallery = $mapper->find( $gallery_id, true ) ) ) {
				// Does the user have sufficient capabilities?
				if ( $this->_can_manage_gallery( $gallery ) ) {
					$retval = $gallery->get_images();
				} else {
					$retval = new \IXR_Error( 403, "You don't have permission to manage gallery #{$gallery_id}" );
				}
			}

			// No gallery found.
			else {
				$retval = new \IXR_Error( 404, "Gallery not found (with id #{$gallery_id}" );
			}
		}

		return $retval;
	}

	/**
	 * Uploads an image to a particular gallery
	 *
	 * @param $args (blog_id, username, password, data)
	 *
	 * Data is an assoc array:
	 *            o string name
	 *            o string type (optional)
	 *            o base64 bits
	 *            o bool overwrite (optional)
	 *            o int gallery
	 *            o int image_id  (optional)
	 * @return object|\IXR_Error
	 */
	public function upload_image( $args ) {
		$retval     = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id    = intval( $args[0] );
		$username   = strval( $args[1] );
		$password   = strval( $args[2] );
		$data       = $args[3];
		$gallery_id = isset( $data['gallery_id'] ) ? $data['gallery_id'] : $data['gallery'];
		if ( ! isset( $data['override'] ) ) {
			$data['override'] = false;
		}
		if ( ! isset( $data['overwrite'] ) ) {
			$data['overwrite'] = false;
		}
		if ( ! isset( $data['image_id'] ) ) {
			$data['image_id'] = false;
		}
		$data['override'] = $data['overwrite'];

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Try to find the gallery.
			$mapper = GalleryMapper::get_instance();
			if ( ( $gallery = $mapper->find( $gallery_id, true ) ) ) {
				// Does the user have sufficient capabilities?
				if ( $this->_can_manage_gallery( $gallery, true ) ) {
					// Upload the image.
					$storage = StorageManager::get_instance();
					try {
						$image = $storage->upload_base64_image(
							$gallery,
							$data['bits'],
							$data['name'],
							$data['image_id'],
							$data['override']
						);
						if ( $image ) {
							$image            = is_int( $image ) ? ImageMapper::get_instance()->find( $image, true ) : $image;
							$storage          = StorageManager::get_instance();
							$image->imageURL  = $storage->get_image_url( $image );
							$image->thumbURL  = $storage->get_image_url( $image, 'thumb' );
							$image->imagePath = $storage->get_image_abspath( $image );
							$image->thumbPath = $storage->get_thumb_abspath( $image );
							$retval           = $image->get_entity();
						} else {
							$retval = new \IXR_Error( 500, 'Could not upload image' );
						}
					} catch ( \Exception $exception ) {
						$retval = new \IXR_Error( 500, 'Could not upload image: ' . $exception->getMessage() );
					}
				} else {
					$retval = new \IXR_Error( 403, "You don't have permission to upload to gallery #{$gallery_id}" );
				}
			} else {
				// No gallery found.
				$retval = new \IXR_Error( 404, "Gallery not found (with id #{$gallery_id}" );
			}
		}

		return $retval;
	}

	/**
	 * Edits an image object
	 *
	 * @param $args (blog_id, username, password, image_id, alttext, description, exclude, other_properties
	 * @return \IXR_Error|object
	 */
	public function edit_image( $args ) {
		$alttext     = strval( $args[4] );
		$description = strval( $args[5] );
		$exclude     = intval( $args[6] );
		$properties  = isset( $args[7] ) ? (array) $args[7] : [];

		$retval = $this->get_image( $args, true );
		if ( ! ( $retval instanceof \IXR_Error ) ) {
			$retval->alttext     = $alttext;
			$retval->description = $description;
			$retval->exclude     = $exclude;

			// Other properties can be specified using an associative array.
			foreach ( $properties as $key => $value ) {
				$retval->$key = $value;
			}

			// Unset any dynamic properties not part of the schema.
			foreach ( [ 'imageURL', 'thumbURL', 'imagePath', 'thumbPath' ] as $key ) {
				unset( $retval->$key );
			}

			$retval = $retval->save();
		}

		return $retval;
	}

	/**
	 * Deletes an existing image from a gallery
	 *
	 * @param array $args (blog_id, username, password, image_id)
	 * @return bool
	 */
	public function delete_image( $args ) {
		$image = $this->get_image( $args, true );

		if ( ! ( $image instanceof \IXR_Error ) ) {
			return ImageMapper::get_instance()->destroy( $image );
		}

		return false;
	}

	/**
	 * Creates a new gallery
	 *
	 * @param array $args (blog_id, username, password, title)
	 * @return int|\IXR_Error
	 */
	public function create_gallery( $args ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );
		$title    = strval( $args[3] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			if ( Security::is_allowed( 'nextgen_edit_gallery' ) ) {
				$mapper = GalleryMapper::get_instance();
				if ( ( $gallery = $mapper->create( [ 'title' => $title ] ) ) && $gallery->save() ) {
					$retval = $gallery->id();
				} else {
					$retval = new \IXR_Error( 500, 'Unable to create gallery' );
				}
			} else {
				$retval = new \IXR_Error( 403, 'Sorry, but you must be able to manage galleries. Check your roles/capabilities.' );
			}
		}

		return $retval;
	}

	/**
	 * Edits an existing gallery
	 *
	 * @param array $args (blog_id, username, password, gallery_id, name, title, description, preview_pic_id)
	 * @return int|bool|\IXR_Error
	 */
	public function edit_gallery( $args ) {
		$retval     = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id    = intval( $args[0] );
		$username   = strval( $args[1] );
		$password   = strval( $args[2] );
		$gallery_id = intval( $args[3] );
		$name       = strval( $args[4] );
		$title      = strval( $args[5] );
		$galdesc    = strval( $args[6] );
		$image_id   = intval( $args[7] );
		$properties = isset( $args[8] ) ? (array) $args[8] : [];

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			$mapper = GalleryMapper::get_instance();
			if ( ( $gallery = $mapper->find( $gallery_id, true ) ) ) {
				if ( $this->_can_manage_gallery( $gallery ) ) {
					$gallery->name       = $name;
					$gallery->title      = $title;
					$gallery->galdesc    = $galdesc;
					$gallery->previewpic = $image_id;
					foreach ( $properties as $key => $value ) {
						$gallery->$key = $value;
					}

					// Unset dynamic properties not part of the schema.
					unset( $gallery->counter );
					unset( $gallery->abspath );

					$retval = $gallery->save();
				} else {
					$retval = new \IXR_Error( 403, "You don't have permission to modify this gallery" );
				}
			} else {
				$retval = new \IXR_Error( 404, "Gallery #{$gallery_id} doesn't exist" );
			}
		}

		return $retval;
	}

	/**
	 * Returns all galleries
	 *
	 * @param array $args (blog_id, username, password)
	 * @return array|\IXR_Error
	 */
	public function get_galleries( $args ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Do we have permission?
			if ( Security::is_allowed( 'nextgen_edit_gallery' ) ) {
				$mapper = GalleryMapper::get_instance();
				$retval = [];
				foreach ( $mapper->find_all() as $gallery ) {
					$this->_add_gallery_properties( $gallery );
					$retval[ $gallery->{$gallery->id_field} ] = (array) $gallery;
				}
			} else {
				$retval = new \IXR_Error( 401, __( 'Sorry, you must be able to manage galleries' ) );
			}
		}

		return $retval;
	}

	/**
	 * Gets a single gallery instance
	 *
	 * @param array $args (blog_id, username, password, gallery_id)
	 * @param bool  $return_model
	 * @return object|bool|\IXR_Error
	 */
	public function get_gallery( $args, $return_model = false ) {
		$retval     = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id    = intval( $args[0] );
		$username   = strval( $args[1] );
		$password   = strval( $args[2] );
		$gallery_id = intval( $args[3] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			$mapper = GalleryMapper::get_instance();
			if ( ( $gallery = $mapper->find( $gallery_id, true ) ) ) {
				if ( $this->_can_manage_gallery( $gallery ) ) {
					$this->_add_gallery_properties( $gallery );
					$retval = $gallery;
				} else {
					$retval = new \IXR_Error( 403, "Sorry, but you don't have permission to manage gallery #{$gallery->gid}" );
				}
			} else {
				$retval = false;
			}
		}

		return $retval;
	}

	/**
	 * Deletes a gallery
	 *
	 * @param array $args (blog_id, username, password, gallery_id)
	 * @return bool
	 */
	public function delete_gallery( $args ) {
		$gallery = $this->get_gallery( $args, true );

		if ( ! ( $gallery instanceof \IXR_Error ) && is_object( $gallery ) ) {
			return GalleryMapper::get_instance()->destroy( $gallery );
		}

		return false;
	}

	/**
	 * Creates a new album
	 *
	 * @param array $args (blog_id, username, password, title, previewpic, description, galleries
	 * @return int|\IXR_Error
	 */
	public function create_album( $args ) {
		$retval     = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id    = intval( $args[0] );
		$username   = strval( $args[1] );
		$password   = strval( $args[2] );
		$title      = strval( $args[3] );
		$previewpic = isset( $args[4] ) ? intval( $args[4] ) : 0;
		$desc       = isset( $args[5] ) ? strval( $args[5] ) : '';
		$sortorder  = isset( $args[6] ) ? $args[6] : '';
		$page_id    = isset( $args[7] ) ? intval( $args[7] ) : 0;

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Is request allowed?
			if ( Security::is_allowed( 'nextgen_edit_album' ) ) {
				$mapper = AlbumMapper::get_instance();
				$album  = $mapper->create(
					[
						'name'       => $title,
						'previewpic' => $previewpic,
						'albumdesc'  => $desc,
						'sortorder'  => $sortorder,
						'pageid'     => $page_id,
					]
				);

				if ( $album->save() ) {
					$retval = $album->id();
				} else {
					$retval = new \IXR_Error( 500, 'Unable to create album' );
				}
			}
		}

		return $retval;
	}

	/**
	 * Returns all albums
	 *
	 * @param $args (blog_id, username, password)
	 * @return \IXR_Error
	 */
	public function get_albums( $args ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Are we allowed?
			if ( Security::is_allowed( 'nextgen_edit_album' ) ) {
				// Fetch all albums.
				$mapper = AlbumMapper::get_instance();
				$retval = [];
				foreach ( $mapper->find_all() as $album ) {
					// Vladimir's Lightroom plugins requires the 'id' to be a string
					// Ask if he can accept integers as well. Currently, integers break
					// his plugin.
					$album->id        = (string) $album->id;
					$album->galleries = $album->sortorder;

					$retval[ $album->{$album->id_field} ] = (array) $album;
				}
			} else {
				$retval = new \IXR_Error( 403, 'Sorry, you must be able to manage albums' );
			}
		}

		return $retval;
	}

	/**
	 * Gets a single album
	 *
	 * @param array $args (blog_id, username, password, album_id)
	 * @param bool  $return_model (optional)
	 * @return object|bool|\IXR_Error
	 */
	public function get_album( $args, $return_model = false ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );
		$album_id = intval( $args[3] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			// Are we allowed?
			if ( Security::is_allowed( 'nextgen_edit_album' ) ) {
				$mapper = AlbumMapper::get_instance();
				if ( ( $album = $mapper->find( $album_id, true ) ) ) {
					// Vladimir's Lightroom plugins requires the 'id' to be a string
					// Ask if he can accept integers as well. Currently, integers break
					// his plugin.
					$album->id        = (string) $album->id;
					$album->galleries = $album->sortorder;

					$retval = $album;
				} else {
					$retval = false;
				}
			} else {
				$retval = new \IXR_Error( 403, 'Sorry, you must be able to manage albums' );
			}
		}

		return $retval;
	}

	/**
	 * Deletes an existing album
	 *
	 * @param array $args (blog_id, username, password, album_id)
	 * @return bool
	 */
	public function delete_album( $args ) {
		$album = $this->get_album( $args, true );

		if ( ! ( $album instanceof \IXR_Error ) ) {
			return AlbumMapper::get_instance()->destroy( $album );
		}

		return false;
	}

	/**
	 * Edit an existing album
	 *
	 * @param array $args (blog_id, username, password, album_id, name, preview pic id, description, galleries).
	 * @return object|\IXR_Error
	 */
	public function edit_album( $args ) {
		$retval = $this->get_album( $args, true );

		if ( ! ( $retval instanceof \IXR_Error ) ) {
			$retval->name       = strval( $args[4] );
			$retval->previewpic = intval( $args[5] );
			$retval->albumdesc  = strval( $args[6] );
			$retval->sortorder  = $args[7];

			$properties = isset( $args[8] ) ? $args[8] : [];
			foreach ( $properties as $key => $value ) {
				$retval->$key = $value;
			}
			unset( $retval->galleries );

			$retval = $retval->save();
		}

		return $retval;
	}

	/**
	 * Sets the post thumbnail for a post to a NextGEN Gallery image
	 *
	 * @param $args (blog_id, username, password, post_id, image_id)
	 * @return \IXR_Error|int attachment id
	 */
	public function set_post_thumbnail( $args ) {
		$retval   = new \IXR_Error( 403, 'Invalid username or password' );
		$blog_id  = intval( $args[0] );
		$username = strval( $args[1] );
		$password = strval( $args[2] );
		$post_ID  = intval( $args[3] );
		$image_id = intval( $args[4] );

		// Authenticate the user.
		if ( $this->_login( $username, $password, $blog_id ) ) {
			if ( current_user_can( 'edit_post', $post_ID ) ) {
				$retval = StorageManager::get_instance()->set_post_thumbnail( $post_ID, $image_id );
			} else {
				$retval = new \IXR_Error( 403, 'Sorry but you need permission to do this' );
			}
		}

		return $retval;
	}
}
