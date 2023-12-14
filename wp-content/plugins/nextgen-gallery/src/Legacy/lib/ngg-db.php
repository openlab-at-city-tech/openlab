<?php
/**
 * NextGEN Gallery Database Class
 *
 * @author Alex Rabe, Vincent Prat
 *
 * @since 1.0.0
 */
class nggdb {

	/**
	 * Holds the list of all galleries
	 *
	 * @since 1.1.0
	 * @access public
	 * @var object|array
	 */
	public $galleries = false;

	/**
	 * Holds the list of all images
	 *
	 * @since 1.3.0
	 * @access public
	 * @var object|array
	 */
	public $images = false;

	/**
	 * Holds the list of all albums
	 *
	 * @since 1.3.0
	 * @access public
	 * @var object|array
	 */
	public $albums = false;

	/**
	 * The array for the pagination
	 *
	 * @since 1.1.0
	 * @access public
	 * @var array
	 */
	public $paged = false;

	/**
	 * Init the Database Abstraction layer for NextGEN Gallery
	 */
	function __construct() {
		global $wpdb;

		$this->galleries = array();
		$this->images    = array();
		$this->albums    = array();
		$this->paged     = array();

		register_shutdown_function( array( &$this, '__destruct' ) );
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @return bool Always true
	 */
	function __destruct() {
		return true;
	}

	/**
	 * This function return all information about the gallery and the images inside
	 *
	 * @deprecated
	 * @param int|string $id or $name
	 * @param string     $order_by
	 * @param string     $order_dir (ASC |DESC)
	 * @param bool       $exclude
	 * @param int        $limit number of paged galleries, 0 shows all galleries
	 * @param int        $start the start index for paged galleries
	 * @param bool       $json remove the key for associative array in json request
	 * @return array An array containing the LegacyImage objects representing the images in the gallery.
	 */
	static function get_gallery( $id, $order_by = 'sortorder', $order_dir = 'ASC', $exclude = true, $limit = 0, $start = 0, $json = false ) {
		$retval = array();

		$image_mapper = \Imagely\NGG\DataMappers\Image::get_instance();
		if (is_numeric( $id )) {
			$image_mapper->select()->where( array( "galleryid = %d", $id ) );
		} else {
			$image_mapper->select()->where( array( "slug = %s", $id ) );
		}
		$image_mapper->order_by( $order_by, $order_dir );

		if ($exclude) {
			$image_mapper->where( array( 'exclude != %d', 1 ) );
		}

		if ($limit && $start) {
			$image_mapper->limit( $limit, $start );
		} elseif ($limit) {
			$image_mapper->limit( $limit );
		}

		foreach ($image_mapper->run_query() as $dbimage) {
			$image    = new \Imagely\NGG\DataTypes\LegacyImage( $dbimage );
			$retval[] = $image;
		}

		return $retval;
	}

	/**
	 * This function return all information about the gallery and the images inside
	 *
	 * @param int|string $id Or $name
	 * @param string     $order_by
	 * @param string     $order_dir (ASC|DESC)
	 * @param bool       $exclude
	 * @return array An array containing the nggImage objects representing the images in the gallery.
	 */
	static function get_ids_from_gallery( $id, $order_by = 'sortorder', $order_dir = 'ASC', $exclude = true ) {

		global $wpdb;

		// Check for the exclude setting
		$exclude_clause = ( $exclude ) ? ' AND tt.exclude<>1 ' : '';

		// Say no to any other value
		$order_dir = ( $order_dir == 'DESC' ) ? 'DESC' : 'ASC';
		$order_by  = ( empty( $order_by ) ) ? 'sortorder' : $order_by;

		// Query database
		if ( is_numeric( $id ) ) {
			$result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = %d $exclude_clause ORDER BY tt.{$order_by} $order_dir", $id ) );
		} else {
			$result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.slug = %s $exclude_clause ORDER BY tt.{$order_by} $order_dir", $id ) );
		}

		return $result;
	}

	/**
	 * Delete a gallery AND all the pictures associated to this gallery!
	 *
	 * @id The gallery ID
	 */
	function delete_gallery( $id ) {
		$mapper  = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$gallery = $mapper->find( $id );
		$mapper->destroy( $gallery );
		wp_cache_delete( $id, 'ngg_gallery' );

		return true;
	}

	/**
	 * Get an album given its ID
	 *
	 * @id The album ID or name
	 * @return object|bool A nggGallery object (false if not found)
	 */
	function find_album( $id ) {
		global $wpdb;

		// Query database
		if ( is_numeric( $id ) && $id != 0 ) {
			if ( $album = wp_cache_get( $id, 'ngg_album' ) ) {
				return $album;
			}

			$album = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->nggalbum WHERE id = %d", $id ) );
		} elseif ( $id == 'all' || ( is_numeric( $id ) && $id == 0 ) ) {
			// init the object and fill it
			$album             = new stdClass();
			$album->id         = 'all';
			$album->name       = __( 'Album overview', 'nggallery' );
			$album->albumdesc  = __( 'Album overview', 'nggallery' );
			$album->previewpic = 0;
			$album->sortorder  = \Imagely\NGG\Util\Serializable::serialize( $wpdb->get_col( "SELECT gid FROM $wpdb->nggallery" ) );
		} else {
			$album = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->nggalbum WHERE slug = %s", $id ) );
		}

		// Unserialize the galleries inside the album
		if ( $album ) {

			if ( !empty( $album->sortorder ) ) {
				$album->gallery_ids = \Imagely\NGG\Util\Serializable::unserialize( $album->sortorder );
			}

			// it was a bad idea to use a object, stripslashes_deep() could not used here, learn from it
			$album->albumdesc = stripslashes( $album->albumdesc );
			$album->name      = stripslashes( $album->name );

			wp_cache_add( $album->id, $album, 'ngg_album' );
			return $album;
		}

		return false;
	}

	/**
	 * Get an image given its ID
	 *
	 * @param int|string $id The image ID or Slug
	 * @return bool|object A nggImage object representing the image (false if not found)
	 */
	static function find_image( $id ) {
		global $wpdb;

		if ( is_numeric( $id ) ) {

			if ( $image = wp_cache_get( $id, 'ngg_image' ) ) {
				return $image;
			}

			$result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.pid = %d ", $id ) );
		} else {
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.image_slug = %s ", $id ) );
		}

		// Build the object from the query result
		if ($result) {
			$image = new nggImage( $result );
			return $image;
		}

		return false;
	}

	/**
	 * Get images given a list of IDs
	 *
	 * @param int[] $pids array of picture_ids
	 * @return nggImage[]
	 */
	static function find_images_in_list( $pids, $exclude = false, $order = 'ASC' ): array {
		global $wpdb;

		$result = [];

		// Ensure all $pid are cast to integers
		$pids = array_map(
			function ( $pid ) {
				return intval( $pid );
			},
			$pids
		);

		$sql = $wpdb->prepare(
			"SELECT t.*, tt.*
                       FROM {$wpdb->nggpictures} AS t
                       INNER JOIN {$wpdb->nggallery} AS tt ON t.galleryid = tt.gid
                       WHERE t.pid IN (" . implode( ', ', array_fill( 0, count( $pids ), '%s' ) ) . ")"
				. sprintf( $exclude ? ' AND t.exclude <> 1 ' : '', [] )
				. ' ORDER BY ' . sprintf( $order === 'RAND' ? 'RAND()' : 't.pid ASC', [] ),
			...$pids
		);

		$images = $wpdb->get_results( $sql, OBJECT_K );

		// Build the image objects from the query result
		if ($images) {
			foreach ($images as $key => $image) {
				$result[$key] = new nggImage( $image );
			}
		}

		return $result;
	}

	/**
	 * Add an image to the database
	 *
	 * @since V1.4.0
	 * @param int|FALSE    $id ID of the gallery
	 * @param string|FALSE $filename (optional)
	 * @param string       $description (optional)
	 * @param string       $alttext (optional)
	 * @param array|false  $meta_data (optional)
	 * @param int          $post_id (required for sync with WP media lib) (optional)
	 * @param string       $imagedate (optional)
	 * @param int          $exclude (0 or 1) (optional)
	 * @param int          $sortorder (optional)
	 * @return int Result of the ID of the inserted image
	 */
	public static function add_image( $id = false, $filename = false, $description = '', $alttext = '', $meta_data = false, $post_id = 0, $imagedate = '0000-00-00 00:00:00', $exclude = 0, $sortorder = 0 ) {
		global $wpdb;

		if ( is_array( $meta_data ) ) {
			$meta_data = \Imagely\NGG\Util\Serializable::serialize( $meta_data );
		}

		// slug must be unique, we use the alttext for that
		$slug = self::get_unique_slug( sanitize_title( $alttext ), 'image' );

		// Add the image
		if (false === $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->nggpictures} (
                            `image_slug`,
                            `galleryid`,
                            `filename`,
                            `description`,
                            `alttext`,
                            `meta_data`,
                            `post_id`,
                            `imagedate`,
                            `exclude`,
                            `sortorder`
                        ) VALUES (%s, %d, %s, %s, %s, %s, %d, %s, %d, %d)",
				$slug,
				$id,
				$filename,
				$description,
				$alttext,
				$meta_data,
				$post_id,
				$imagedate,
				$exclude,
				$sortorder
			)
		)) {
			return false;
		}

		$imageID = (int) $wpdb->insert_id;

		\Imagely\NGG\DataMappers\Gallery::get_instance()->set_preview_image( $id, $imageID, true );

		// Remove from cache the galley, needs to be rebuild now
		wp_cache_delete( $id, 'ngg_gallery' );

		// and give me the new id
		return $imageID;
	}

	/**
	 * Add an gallery to the database
	 *
	 * @since V1.7.0
	 * @param string $title or name of the gallery (optional)
	 * @param string $path (optional)
	 * @param string $description (optional)
	 * @param int    $pageid (optional)
	 * @param int    $previewpic (optional)
	 * @param int    $author (optional)
	 * @return int result of the ID of the inserted gallery
	 */
	static function add_gallery( $title = '', $path = '', $description = '', $pageid = 0, $previewpic = 0, $author = 0 ) {
		global $wpdb;

		// slug must be unique, we use the title for that
		$slug = self::get_unique_slug( sanitize_title( $title ), 'gallery' );

		// Note : The field 'name' is deprecated, it's currently kept only for compat reason with older shortcodes, we copy the slug into this field
		if ( false === $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $wpdb->nggallery (name, slug, path, title, galdesc, pageid, previewpic, author)
													 VALUES (%s, %s, %s, %s, %s, %d, %d, %d)",
				$slug,
				$slug,
				$path,
				$title,
				$description,
				$pageid,
				$previewpic,
				$author
			)
		) ) {
			return false;
		}

		$galleryID = (int) $wpdb->insert_id;

		do_action( 'ngg_created_new_gallery', $galleryID );
		\Imagely\NGG\Util\Transient::flush( 'displayed_gallery_rendering' );

		// and give me the new id
		return $galleryID;
	}

	/**
	 * Get the last images registered in the database with a maximum number of $limit results
	 *
	 * @param integer $page start offset as page number (0,1,2,3,4...)
	 * @param integer $limit the number of result
	 * @param bool    $exclude do not show excluded images
	 * @param int     $galleryId Only look for images with this gallery id, or in all galleries if id is 0
	 * @param string  $orderby is one of "id" (default, order by pid), "date" (order by exif date), sort (order by user sort order)
	 * @deprecated
	 * @return bool|array
	 */
	static function find_last_images( $page = 0, $limit = 30, $exclude = true, $galleryId = 0, $orderby = "pid" ) {
		// Determine ordering
		$order_field     = $orderby;
		$order_direction = 'DESC';
		switch ($orderby) {
			case 'date':
			case 'imagedate':
			case 'time':
			case 'datetime':
				$order_field     = 'imagedate';
				$order_direction = 'DESC';
				break;
			case 'sort':
			case 'sortorder':
				$order_field     = 'sortorder';
				$order_direction = 'ASC';
				break;

		}

		// Start query
		$mapper = \Imagely\NGG\DataMappers\Image::get_instance();
		$mapper->select()->order_by( $order_field, $order_direction );

		// Calculate limit and offset
		if (!$limit) {
			$limit = 30;
		}
		$offset = $page*$limit;
		if ($offset && $limit) {
			$mapper->limit( $limit, $offset );
		}

		// Add exclusion clause
		if ($exclude) {
			$mapper->where( array( "exclude = %d", 0 ) );
		}

		// Add gallery clause
		if ($galleryId) {
			$mapper->where( array( "galleryid = %d", $galleryId ) );
		}

		return $mapper->run_query();
	}

	/**
	 * @return nggImage[] An array containing the nggImage objects representing the images in the album.
	 */
	function find_images_in_album(
		$album,
		string $order_by = 'galleryid',
		string $order_dir = 'ASC',
		bool $exclude = true
	): array {
		global $wpdb;

		if (!is_object( $album )) {
			$album = self::find_album( $album );
		}

		$album->gallery_ids = array_map(
			function ( $id ) {
				return intval( trim( $id, " '" ) );
			},
			$album->gallery_ids
		);

		// Say no to any other value
		$order_dir = ( $order_dir == 'DESC' ) ? 'DESC' : 'ASC';
		$order_by  = ( empty( $order_by ) ) ? 'galleryid' : $order_by;

		$sql = $wpdb->prepare(
			"SELECT t.*, tt.*   
                       FROM {$wpdb->nggallery} AS t
                       INNER JOIN {$wpdb->nggpictures} AS tt ON t.gid = tt.galleryid
                       WHERE tt.galleryid IN (" . implode( ', ', array_fill( 0, count( $album->gallery_ids ), '%s' ) ) . ")"
				. sprintf( $exclude ? ' AND tt.exclude <> 1 ' : '', [] ) . "
                       ORDER BY tt.{$order_by} {$order_dir}",
			...$album->gallery_ids
		);

		$result = $wpdb->get_results( $sql );

		// Return the object from the query result
		if ($result) {
			$images = [];
			foreach ($result as $image) {
				$images[] = new nggImage( $image );
			}
			return $images;
		}

		return [];
	}

	/**
	 * @param int $limit Number of results, 0 shows all results
	 * @return \Imagely\NGG\DataTypes\Image[]
	 */
	function search_for_images( string $request, int $limit = 0 ): array {
		global $wpdb;

		// If a search pattern is specified, load the posts that match
		if (!empty( $request )) {
			// added slashes screw with quote grouping when done early, so done later
			$request = stripslashes( $request );

			// split the words it a array if seperated by a space or comma
			preg_match_all( '/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $request, $matches );
			$search_terms = array_map( array( $this, 'trim_quotes_and_whitespace' ), $matches[0] );

			$searchand = '';
			$search    = '';

			foreach ((array) $search_terms as $term) {
				$term      = addslashes_gpc( $term );
				$search   .= $wpdb->prepare(
					"{$searchand}((tt.description LIKE %s) OR (tt.alttext LIKE %s) OR (tt.filename LIKE %s))",
					[
						'%' . $term . '%',
						'%' . $term . '%',
						'%' . $term . '%',
					]
				);
				$searchand = ' AND ';
			}

			$term = esc_sql( $request );
			if (count( $search_terms ) > 1 && $search_terms[0] != $request ) {
				$search .= $wpdb->prepare(
					" OR (tt.description LIKE %s) OR (tt.alttext LIKE %s) OR (tt.filename LIKE %s)",
					[
						'%' . $term . '%',
						'%' . $term . '%',
						'%' . $term . '%',
					]
				);
			}

			if (!empty( $search )) {
				$search = " AND ({$search}) ";
			}

			if ($limit > 0) {
				$limit_by = $wpdb->prepare( 'LIMIT %d', [ (int) $limit ] );
			} else {
				$limit_by = '';
			}
		} else {
			return [];
		}

		$result = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `tt`.`pid` FROM `{$wpdb->nggallery}` AS `t`
                        INNER JOIN `{$wpdb->nggpictures}` AS `tt` ON `t`.`gid` = `tt`.`galleryid`
                        WHERE 1 = 1 
                        {$search}
                        ORDER BY `tt`.`pid` ASC
                        {$limit_by}",
				[]
			)
		);

		// TODO: Currently we don't support a proper pagination
		$this->paged['total_objects']        = $this->paged['objects_per_page'] = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
		$this->paged['max_objects_per_page'] = 1;

		// Return the object from the query result
		if ($result) {
			$images = array();
			$mapper = \Imagely\NGG\DataMappers\Image::get_instance();
			foreach ($result as $image_id) {
				$images[] = $mapper->find( $image_id );
			}
			return $images;
		}

		return [];
	}

	function trim_quotes_and_whitespace( $str ): string {
		return trim( $str, "\"'\n\r" );
	}

	/**
	 * Update or add meta data for an image
	 *
	 * @since 1.4.0
	 * @param int   $id The image ID
	 * @param array $new_values An array with existing or new values
	 * @return bool result of query
	 */
	static function update_image_meta( $id, $new_values ) {
		global $wpdb;

		// Query database for existing values
		// Use cache object
		$old_values      = $wpdb->get_var( $wpdb->prepare( "SELECT meta_data FROM $wpdb->nggpictures WHERE pid = %d ", $id ) );
		$old_values      = \Imagely\NGG\Util\Serializable::unserialize( $old_values );
		$meta            = array_merge( (array) $old_values, (array) $new_values );
		$serialized_meta = \Imagely\NGG\Util\Serializable::serialize( $meta );
		$result          = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->nggpictures SET meta_data = %s WHERE pid = %d", $serialized_meta, $id ) );

		do_action( 'ngg_updated_image_meta', $id, $meta );

		wp_cache_delete( $id, 'ngg_image' );

		return $result;
	}

	/**
	 * Computes a unique slug for the gallery,album or image, when given the desired slug.
	 *
	 * @since 1.7.0
	 * @param string $slug the desired slug (post_name)
	 * @param string $type ('image', 'album' or 'gallery')
	 * @param int    $id ID of the object, so that it's not checked against itself (optional)
	 * @return string unique slug for the object, based on $slug (with a -1, -2, etc. suffix)
	 */
	static function get_unique_slug( $slug, $type ) {
		global $wpdb;

		$slug   = stripslashes( $slug );
		$retval = $slug;

		// We have to create a somewhat complex query to find the next available slug. The query could easily
		// be simplified if we could use MySQL REGEX, but there are still hosts using MySQL 5.0, and REGEX is
		// only supported in MySQL 5.1 and higher
		$field = '';
		$table = '';
		switch ($type) {
			case 'image':
				$field = 'image_slug';
				$table = $wpdb->nggpictures;
				break;
			case 'album':
				$field = 'slug';
				$table = $wpdb->nggalbum;
				break;
			case 'gallery':
				$field = 'slug';
				$table = $wpdb->nggallery;
				break;
		}

		$query = $wpdb->prepare(
			"SELECT {$field}, SUBSTR({$field}, %d) AS 'i' FROM {$table}
                    WHERE ({$field} LIKE %s AND CONVERT(SUBSTR({$field}, %d), SIGNED) BETWEEN 1 AND %d) OR {$field} = %s
                    ORDER BY CAST(i AS SIGNED INTEGER) DESC LIMIT 1",
			[
				strlen( "{$slug}-" ) + 1,
				$wpdb->esc_like( "{$slug}-" ) . '%',
				strlen( "{$slug}-" ) + 1,
				PHP_INT_MAX,
				$slug,
			]
		);

		// If the above query returns a result, it means that the slug is already taken
		if (( $last_slug = $wpdb->get_var( $query ) )) {

			// If the last known slug has an integer attached, then it means that we need to increment that integer
			$quoted_slug = preg_quote( $slug, '/' );
			if (preg_match( "/{$quoted_slug}-(\\d+)/", $last_slug, $matches )) {
				$i      = intval( $matches[1] ) + 1;
				$retval = "{$slug}-{$i}";
			} else {
				$retval = "{$slug}-1";
			}
		}

		return $retval;
	}
}

if (!isset( $GLOBALS['nggdb'] )) {
	$GLOBALS['nggdb'] = new nggdb();
}
