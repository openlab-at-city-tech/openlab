<?php
/**
 * NextGEN Gallery Database Class
 *
 * @author Alex Rabe, Vincent Prat
 *
 * @since 1.0.0
 */
class nggdb
{
    /**
     * Holds the list of all galleries
     *
     * @since 1.1.0
     * @access public
     * @var object|array
     */
    var $galleries = false;

    /**
     * Holds the list of all images
     *
     * @since 1.3.0
     * @access public
     * @var object|array
     */
    var $images = false;

    /**
     * Holds the list of all albums
     *
     * @since 1.3.0
     * @access public
     * @var object|array
     */
    var $albums = false;

    /**
     * The array for the pagination
     *
     * @since 1.1.0
     * @access public
     * @var array
     */
    var $paged = false;

    /**
     * Init the Database Abstraction layer for NextGEN Gallery
     *
     */
    function __construct() {
        global $wpdb;

        $this->galleries = array();
        $this->images    = array();
        $this->albums    = array();
        $this->paged     = array();

        register_shutdown_function(array(&$this, '__destruct'));
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
     * Get a gallery given its ID
     *
     *
     * @param int|string $id or $slug
     * @return object nggGallery object (null if not found)
     * @deprecated Use the C_Gallery_Mapper class instead
     */
    static function find_gallery( $id )
    {
	    // TODO: This method is only used by nggAdmin::set_gallery_preview(), which won't be
	    // exist after ngglegacy uses the gallery storage component to import galleries from the FS.
        $mapper = C_Gallery_Mapper::get_instance();
	    return $mapper->find($id);
    }

	/**
	 * Finds all galleries
	 * @deprecated
	 * @return array
	 */
	static function find_all_galleries()
	{
		$mapper = C_Gallery_Mapper::get_instance();
		return $mapper->find_all();
	}

    /**
     * Finds all albums
     *
     * @deprecated
     * @param string $order_by
     * @param string $order_dir
     * @param int $limit number of albums, 0 shows all albums
     * @param int $start the start index for paged albums
     * @return array $album
     */
    function find_all_album($order_by = 'id', $order_dir = 'ASC', $limit = '0', $start = '0')
    {
        $mapper = C_Album_Mapper::get_instance();
        $mapper->select();
        $mapper->where_and(array());
        $mapper->order_by($order_by, $order_dir);
        if ($limit > 0)
            $mapper->limit($limit, $start);
        return $mapper->run_query();
    }

    /**
     * Search for a filename
     *
     * @deprecated
     * @param string $filename
     * @param int|bool $galleryID (optional)
     * @return Array Result of the request
     */
    function search_for_file($filename, $galleryID = false)
    {
        $retval = array();
        $mapper = C_Image_Mapper::get_instance();
        $mapper->select();
        $mapper->where_and(array('filename = %s', $filename));
        if ($galleryID)
            $mapper->where_and(array('galleryid = %d', $galleryID));
        foreach ($mapper->run_query() as $dbimage) {
            $image = new C_Image_Wrapper($dbimage);
            $retval[] = $image;
        }
        return $retval;
    }

    /**
     * Get random images from one or more gallery
     *
     * @deprecated
     * @param int $number Number of images
     * @param int $gallery_id (optional) Gallery ID
     * @return array Array holding C_Image_Wrapper objects
     */
    function get_random_images($number = 1, $gallery_id = 0)
    {
        $mapper = C_Image_Mapper::get_instance();
        $mapper->select();
        $mapper->where_and(array('exclude != 1'));
        if ($gallery_id !== 0)
            $mapper->where_and(array('galleryid = %d', $gallery_id));
        $mapper->order_by('rand()');
        $mapper->limit($number, 0);
        foreach ($mapper->run_query() as $dbimage) {
            $image = new C_Image_Wrapper($dbimage);
            $retval[] = $image;
        }
        return $retval;
    }

    /**
     * This function return all information about the gallery and the images inside
     *
     * @deprecated
     * @param int|string $id or $name
     * @param string $order_by
     * @param string $order_dir (ASC |DESC)
     * @param bool $exclude
     * @param int $limit number of paged galleries, 0 shows all galleries
     * @param int $start the start index for paged galleries
     * @param bool $json remove the key for associative array in json request
     * @return array An array containing the C_Image_Wrapper objects representing the images in the gallery.
     */
    static function get_gallery($id, $order_by = 'sortorder', $order_dir = 'ASC', $exclude = true, $limit = 0, $start = 0, $json = false)
    {
	    $retval = array();

		$image_mapper = C_Image_Mapper::get_instance();
        if (is_numeric($id))
            $image_mapper->select()->where(array("galleryid = %d", $id));
        else
            $image_mapper->select()->where(array("slug = %s", $id));
	    $image_mapper->order_by($order_by, $order_dir);

	    if ($exclude) $image_mapper->where(array('exclude != %d', 1));

	    if ($limit && $start) $image_mapper->limit($limit, $start);
	    elseif ($limit) $image_mapper->limit($limit);

        foreach ($image_mapper->run_query() as $dbimage) {
            $image = new C_Image_Wrapper($dbimage);
	        $retval[] = $image;
        }

	    return $retval;
    }

    /**
     * This function return all information about the gallery and the images inside
     *
     * @param int|string $id Or $name
     * @param string $order_by
     * @param string $order_dir (ASC|DESC)
     * @param bool $exclude
     * @return array An array containing the nggImage objects representing the images in the gallery.
     */
    static function get_ids_from_gallery($id, $order_by = 'sortorder', $order_dir = 'ASC', $exclude = true)
    {

        global $wpdb;

        // Check for the exclude setting
        $exclude_clause = ($exclude) ? ' AND tt.exclude<>1 ' : '';

        // Say no to any other value
        $order_dir = ( $order_dir == 'DESC') ? 'DESC' : 'ASC';
        $order_by  = ( empty($order_by) ) ? 'sortorder' : $order_by;

        // Query database
        if( is_numeric($id) )
            $result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = %d $exclude_clause ORDER BY tt.{$order_by} $order_dir", $id ) );
        else
            $result = $wpdb->get_col( $wpdb->prepare( "SELECT tt.pid FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.slug = %s $exclude_clause ORDER BY tt.{$order_by} $order_dir", $id ) );

        return $result;
    }

    /**
     * Delete a gallery AND all the pictures associated to this gallery!
     *
     * @id The gallery ID
     */
    function delete_gallery( $id ) {
        $mapper = C_Gallery_Mapper::get_instance();
        $gallery = $mapper->find($id);
        $mapper->destroy($gallery);
        wp_cache_delete($id, 'ngg_gallery');

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
        if ( is_numeric($id) && $id != 0 ) {
            if ( $album = wp_cache_get($id, 'ngg_album') )
                return $album;

            $album = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->nggalbum WHERE id = %d", $id) );
        } elseif ( $id == 'all' || (is_numeric($id) && $id == 0) ) {
            // init the object and fill it
            $album = new stdClass();
            $album->id = 'all';
            $album->name = __('Album overview','nggallery');
            $album->albumdesc  = __('Album overview','nggallery');
            $album->previewpic = 0;
            $album->sortorder  = C_NextGen_Serializable::serialize( $wpdb->get_col("SELECT gid FROM $wpdb->nggallery") );
        } else {
            $album = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->nggalbum WHERE slug = %s", $id) );
        }

        // Unserialize the galleries inside the album
        if ( $album ) {

            if ( !empty( $album->sortorder ) )
                $album->gallery_ids = C_NextGen_Serializable::unserialize( $album->sortorder );

            // it was a bad idea to use a object, stripslashes_deep() could not used here, learn from it
            $album->albumdesc  = stripslashes($album->albumdesc);
            $album->name       = stripslashes($album->name);

            wp_cache_add($album->id, $album, 'ngg_album');
            return $album;
        }

        return false;
    }

    /**
     * Delete an album
     *
     * @id The album ID
     */
    function delete_album( $id ) {
        global $wpdb;

        $result = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->nggalbum WHERE id = %d", $id) );
        wp_cache_delete($id, 'ngg_album');

        return $result;
    }

    /**
     * Insert an image in the database
     *
     * @return int ID of the inserted image
     */
    function insert_image($gid, $filename, $alttext, $desc, $exclude) {
        global $wpdb;

        $result = $wpdb->query(
              "INSERT INTO $wpdb->nggpictures (galleryid, filename, description, alttext, exclude) VALUES "
            . "('$gid', '$filename', '$desc', '$alttext', '$exclude');");
        $pid = (int) $wpdb->insert_id;
        wp_cache_delete($gid, 'ngg_gallery');

        return $pid;
    }

    /**
     * nggdb::update_image() - Update an image in the database
     *
     * @param int $pid ID of the image
     * @param string|int $galleryid (optional)
     * @param string $filename (optional)
     * @param string $description (optional)
     * @param string $alttext (optional)
     * @param int $exclude (0 or 1) (optional)
     * @param int $sortorder (optional)
     * @return null|bool Result of update query
     */
    function update_image($pid, $galleryid = false, $filename = false, $description = false, $alttext = false, $exclude = false, $sortorder = false) {

        global $wpdb;

        $sql = array();
        $pid = (int) $pid;

        // slug must be unique, we use the alttext for that
        $slug = nggdb::get_unique_slug( sanitize_title( $alttext ), 'image' );

        $update = array(
            'image_slug'  => $slug,
            'galleryid'   => $galleryid,
            'filename'    => $filename,
            'description' => $description,
            'alttext'     => $alttext,
            'exclude'     => $exclude,
            'sortorder'   => $sortorder);

        // create the sql parameter "name = value"
        foreach ($update as $key => $value)
            if ($value !== false)
                $sql[] = $key . " = '" . $value . "'";

        // create the final string
        $sql = implode(', ', $sql);

        if ( !empty($sql) && $pid != 0)
            $result = $wpdb->query( "UPDATE $wpdb->nggpictures SET $sql WHERE pid = $pid" );

        wp_cache_delete($pid, 'ngg_image');

        return $result;
    }

     /**
      * nggdb::update_gallery() - Update an gallery in the database
      *
      * @since V1.7.0
      * @param int $id ID of the gallery
      * @param string|bool $name (optional) Name or title of the gallery
      * @param string|bool $path (optional)
      * @param string|bool $title (optional)
      * @param string|bool $description (optional)
      * @param int|bool $pageid (optional)
      * @param int|bool $previewpic (optional)
      * @param int|bool $author (optional)
      * @return null|bool Result of update query
     */
    function update_gallery($id, $name = false, $path = false, $title = false, $description = false, $pageid = false, $previewpic = false, $author = false) {

        global $wpdb;

        $sql = array();
        $id = (int) $id;

        // slug must be unique, we use the title for that
        $slug = nggdb::get_unique_slug( sanitize_title( $title ), 'gallery' );

        $update = array(
            'name'       => $name,
            'slug'       => $slug,
            'path'       => $path,
            'title'      => $title,
            'galdesc'    => $description,
            'pageid'     => $pageid,
            'previewpic' => $previewpic,
            'author'     => $author);

        // create the sql parameter "name = value"
        foreach ($update as $key => $value)
            if ($value !== false)
                $sql[] = $key . " = '" . $value . "'";

        // create the final string
        $sql = implode(', ', $sql);

        if ( !empty($sql) && $id != 0)
            $result = $wpdb->query( "UPDATE $wpdb->nggallery SET $sql WHERE gid = $id" );

        wp_cache_delete($id, 'ngg_gallery');

        return $result;
    }

     /**
     * nggdb::update_album() - Update an album in the database
     *
     * @since V1.7.0
     * @param int $id ID of the album
     * @param string $title (optional)
     * @param int $previewpic (optional)
     * @param string $description (optional)
     * @param array $sortorder (optional)
     * @param int $pageid (optional)
     * @return null|bool result of update query
     */
    function update_album($id, $name = false, $previewpic = false, $description = false, $sortorder = false, $pageid = false ) {

        global $wpdb;

        $sql = array();
        $id = (int) $id;

        // slug must be unique, we use the title for that
        $slug = nggdb::get_unique_slug( sanitize_title( $name ), 'album' );

        $update = array(
            'name'       => $name,
            'slug'       => $slug,
            'previewpic' => $previewpic,
            'albumdesc'  => $description,
            'sortorder'  => $sortorder,
            'pageid'     => $pageid);

        // create the sql parameter "name = value"
        foreach ($update as $key => $value)
            if ($value !== false)
                $sql[] = $key . " = '" . $value . "'";

        // create the final string
        $sql = implode(', ', $sql);

        if ( !empty($sql) && $id != 0)
            $result = $wpdb->query( "UPDATE $wpdb->nggalbum SET $sql WHERE id = $id" );

        wp_cache_delete($id, 'ngg_album');

        return $result;
    }

    /**
     * Get an image given its ID
     *
     * @param int|string $id The image ID or Slug
     * @return bool|object A nggImage object representing the image (false if not found)
     */
    static function find_image($id)
    {
        global $wpdb;

        if( is_numeric($id) ) {

            if ( $image = wp_cache_get($id, 'ngg_image') )
                return $image;

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.pid = %d ", $id ) );
        } else
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.image_slug = %s ", $id ) );

        // Build the object from the query result
        if ($result) {
            $image = new nggImage($result);
            return $image;
        }

        return false;
    }

    /**
     * Get images given a list of IDs
     *
     * @param $pids array of picture_ids
     * @return array An array of nggImage objects representing the images
     */
    static function find_images_in_list( $pids, $exclude = false, $order = 'ASC' ) {
        global $wpdb;

        $result = array();

        // Check for the exclude setting
        $exclude_clause = ($exclude) ? ' AND t.exclude <> 1 ' : '';

        // Check for the exclude setting
        $order_clause = ($order == 'RAND') ? 'ORDER BY rand() ' : ' ORDER BY t.pid ASC' ;

        if ( is_array($pids) ) {
            $id_list = "'" . implode("', '", $pids) . "'";

            // Save Query database
            $images = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->nggpictures AS t INNER JOIN $wpdb->nggallery AS tt ON t.galleryid = tt.gid WHERE t.pid IN ($id_list) $exclude_clause $order_clause", OBJECT_K);

            // Build the image objects from the query result
            if ($images) {
                foreach ($images as $key => $image)
                    $result[$key] = new nggImage( $image );
            }
        }
        return $result;
    }

    /**
     * Add an image to the database
     *
     * @since V1.4.0
     * @param int|FALSE $id ID of the gallery
     * @param string|FALSE $filename (optional)
     * @param string $description (optional)
     * @param string $alttext (optional)
     * @param array|false $meta_data (optional)
     * @param int $post_id (required for sync with WP media lib) (optional)
     * @param string $imagedate (optional)
     * @param int $exclude (0 or 1) (optional)
     * @param int $sortorder (optional)
     * @return int Result of the ID of the inserted image
     */
    public static function add_image($id = false, $filename = false, $description = '', $alttext = '', $meta_data = false, $post_id = 0, $imagedate = '0000-00-00 00:00:00', $exclude = 0, $sortorder = 0)
    {
        global $wpdb;

		if ( is_array($meta_data) )
			$meta_data = C_NextGen_Serializable::serialize($meta_data);

        // slug must be unique, we use the alttext for that
        $slug = nggdb::get_unique_slug(sanitize_title( $alttext ), 'image');

		// Add the image
		if (FALSE === $wpdb->query(
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
                $slug, $id, $filename, $description, $alttext, $meta_data, $post_id, $imagedate, $exclude, $sortorder)))
		{
			return FALSE;
		}

		$imageID = (int)$wpdb->insert_id;

        C_Gallery_Mapper::get_instance()->set_preview_image($id, $imageID, TRUE);

		// Remove from cache the galley, needs to be rebuild now
	    wp_cache_delete($id, 'ngg_gallery');

		//and give me the new id
		return $imageID;
    }

    /**
    * Add an album to the database
    *
	* @since V1.7.0
    * @param string|bool $name (optional)
    * @param int $previewpic (optional)
    * @param string $description (optional)
    * @param string $sortorder Must be in the form of a serialized array (optional)
    * @param int $pageid (optional)
    * @return int Result of the ID of the inserted album
    */
    function add_album( $name = false, $previewpic = 0, $description = '', $sortorder = '', $pageid = 0  ) {
        global $wpdb;

        // name must be unique, we use the title for that
        $slug = nggdb::get_unique_slug( sanitize_title( $name ), 'album' );

		// Add the album
		if ( false === $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->nggalbum (name, slug, previewpic, albumdesc, sortorder, pageid)
													 VALUES (%s, %s, %d, %s, %s, %d)", $name, $slug, $previewpic, $description, $sortorder, $pageid ) ) ) {
			return false;
		}

		$albumID = (int) $wpdb->insert_id;

		//and give me the new id
		return $albumID;
    }

    /**
    * Add an gallery to the database
    *
	* @since V1.7.0
    * @param string $title or name of the gallery (optional)
    * @param string $path (optional)
    * @param string $description (optional)
    * @param int $pageid (optional)
    * @param int $previewpic (optional)
    * @param int $author (optional)
    * @return int result of the ID of the inserted gallery
    */
    static function add_gallery( $title = '', $path = '', $description = '', $pageid = 0, $previewpic = 0, $author = 0  ) {
        global $wpdb;

        // slug must be unique, we use the title for that
        $slug = nggdb::get_unique_slug( sanitize_title( $title ), 'gallery' );

        // Note : The field 'name' is deprecated, it's currently kept only for compat reason with older shortcodes, we copy the slug into this field
		if ( false === $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->nggallery (name, slug, path, title, galdesc, pageid, previewpic, author)
													 VALUES (%s, %s, %s, %s, %s, %d, %d, %d)", $slug, $slug, $path, $title, $description, $pageid, $previewpic, $author ) ) ) {
			return false;
		}

		$galleryID = (int) $wpdb->insert_id;

        do_action('ngg_created_new_gallery', $galleryID);
        C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');

		//and give me the new id
		return $galleryID;
    }

    /**
    * Delete an image entry from the database
    * @param integer $id is the Image ID
    * @deprecated
    */
    static function delete_image( $id ) {

        return C_Image_Mapper::get_instance()->destroy($id);
    }

    /**
     * Get the last images registered in the database with a maximum number of $limit results
     *
     * @param integer $page start offset as page number (0,1,2,3,4...)
     * @param integer $limit the number of result
     * @param bool $exclude do not show excluded images
     * @param int $galleryId Only look for images with this gallery id, or in all galleries if id is 0
     * @param string $orderby is one of "id" (default, order by pid), "date" (order by exif date), sort (order by user sort order)
     * @deprecated
     * @return bool|array
     */
    static function find_last_images($page = 0, $limit = 30, $exclude = true, $galleryId = 0, $orderby = "pid") {
	    // Determine ordering
	    $order_field        = $orderby;
	    $order_direction    = 'DESC';
	    switch ($orderby) {
		    case 'date':
		    case 'imagedate':
		    case 'time':
		    case 'datetime':
			    $order_field = 'imagedate';
			    $order_direction = 'DESC';
			    break;
		    case 'sort':
		    case 'sortorder':
			    $order_field = 'sortorder';
			    $order_direction = 'ASC';
			    break;

	    }

		// Start query
	    $mapper = C_Image_Mapper::get_instance();
	    $mapper->select()->order_by($order_field, $order_direction);

	    // Calculate limit and offset
	    if (!$limit) $limit = 30;
	    $offset = $page*$limit;
	    if ($offset && $limit) $mapper->limit($limit, $offset);

	    // Add exclusion clause
	    if ($exclude) $mapper->where(array("exclude = %d", 0));

	    // Add gallery clause
	    if ($galleryId) $mapper->where(array("galleryid = %d", $galleryId));

		return $mapper->run_query();
    }

    /**
     * Get all the images from a given album
     *
     * @param object|int $album The album object or the id
     * @param string $order_by
     * @param string $order_dir
     * @param bool $exclude
     * @return null|array An array containing the nggImage objects representing the images in the album.
     */
    function find_images_in_album($album, $order_by = 'galleryid', $order_dir = 'ASC', $exclude = true) {
        // TODO: This method is only used by the JSON API. Once it's removed, this method can be removed

	    global $wpdb;

        if ( !is_object($album) )
            $album = nggdb::find_album( $album );

        // Get gallery list
        $gallery_list = implode(',', $album->gallery_ids);
        // Check for the exclude setting
        $exclude_clause = ($exclude) ? ' AND tt.exclude<>1 ' : '';

        // Say no to any other value
        $order_dir = ( $order_dir == 'DESC') ? 'DESC' : 'ASC';
        $order_by  = ( empty($order_by) ) ? 'galleryid' : $order_by;

        $result = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.galleryid IN ($gallery_list) $exclude_clause ORDER BY tt.$order_by $order_dir");
        // Return the object from the query result
        if ($result) {
            foreach ($result as $image) {
                $images[] = new nggImage( $image );
            }
            return $images;
        }

        return null;
    }

    /**
     * search for images and return the result
     *
     * @since 1.3.0
     * @param string $request
     * @param int $limit number of results, 0 shows all results
     * @return array|bool|null Result of the request
     */
    function search_for_images( $request, $limit = 0 ) {
        global $wpdb;

        // If a search pattern is specified, load the posts that match
        if ( !empty($request) ) {
            // added slashes screw with quote grouping when done early, so done later
            $request = stripslashes($request);

            // split the words it a array if seperated by a space or comma
            preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $request, $matches);
            $search_terms = array_map(array($this, 'trim_quotes_and_whitespace'), $matches[0]);

            $n = '%';
            $searchand = '';
            $search = '';

            foreach( (array) $search_terms as $term) {
                $term = addslashes_gpc($term);
                $search .= "{$searchand}((tt.description LIKE '{$n}{$term}{$n}') OR (tt.alttext LIKE '{$n}{$term}{$n}') OR (tt.filename LIKE '{$n}{$term}{$n}'))";
                $searchand = ' AND ';
            }

            $term = esc_sql($request);
            if (count($search_terms) > 1 && $search_terms[0] != $request )
                $search .= " OR (tt.description LIKE '{$n}{$term}{$n}') OR (tt.alttext LIKE '{$n}{$term}{$n}') OR (tt.filename LIKE '{$n}{$term}{$n}')";

            if ( !empty($search) )
                $search = " AND ({$search}) ";

            $limit_by  = ( $limit > 0 ) ? 'LIMIT ' . intval($limit) : '';
        } else {
            return false;
        }

        // build the final query
        $query = "SELECT `tt`.`pid` FROM `{$wpdb->nggallery}` AS `t` INNER JOIN `{$wpdb->nggpictures}` AS `tt` ON `t`.`gid` = `tt`.`galleryid` WHERE 1=1 {$search} ORDER BY `tt`.`pid` ASC {$limit_by}";

        $result = $wpdb->get_col($query);

        // TODO: Currently we didn't support a proper pagination
        $this->paged['total_objects'] = $this->paged['objects_per_page'] = intval ( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
        $this->paged['max_objects_per_page'] = 1;

        // Return the object from the query result
        if ($result) {
            $images = array();
            $mapper = C_Image_Mapper::get_instance();
            foreach ($result as $image_id) {
                $images[] = $mapper->find($image_id);
            }
            return $images;
        }

        return null;
    }

    function trim_quotes_and_whitespace($str)
    {
    	return trim($str, "\"'\n\r");
    }

    /**
     * search for galleries and return the result
     *
     * @since 1.7.0
     * @param string $request
     * @param int $limit number of results, 0 shows all results
     * @return bool|array Result of the request
     */
    function search_for_galleries( $request, $limit = 0 ) {
        global $wpdb;

        // If a search pattern is specified, load the posts that match
        if ( !empty($request) ) {
            // added slashes screw with quote grouping when done early, so done later
            $request = stripslashes($request);

            // split the words it a array if seperated by a space or comma
            preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $request, $matches);
            $search_terms = array_map(array($this, 'trim_quotes_and_whitespace'), $matches[0]);

            $n = '%';
            $searchand = '';
            $search = '';

            foreach( (array) $search_terms as $term) {
                $term = addslashes_gpc($term);
                $search .= "{$searchand}((title LIKE '{$n}{$term}{$n}') OR (name LIKE '{$n}{$term}{$n}') )";
                $searchand = ' AND ';
            }

            $term = esc_sql($request);
            if (count($search_terms) > 1 && $search_terms[0] != $request )
                $search .= " OR (title LIKE '{$n}{$term}{$n}') OR (name LIKE '{$n}{$term}{$n}')";

            if ( !empty($search) )
                $search = " AND ({$search}) ";

            $limit  = ( $limit > 0 ) ? 'LIMIT ' . intval($limit) : '';
        } else {
            return false;
        }

        // build the final query
        $query = "SELECT * FROM $wpdb->nggallery WHERE 1=1 $search ORDER BY title ASC $limit";
        $result = $wpdb->get_results($query);

        return $result;
    }

    /**
     * search for albums and return the result
     *
     * @since 1.7.0
     * @param string $request
     * @param int $limit number of results, 0 shows all results
     * @return bool|array Result of the request
     */
    function search_for_albums( $request, $limit = 0 ) {
        global $wpdb;

        // If a search pattern is specified, load the posts that match
        if ( !empty($request) ) {
            // added slashes screw with quote grouping when done early, so done later
            $request = stripslashes($request);

            // split the words it a array if seperated by a space or comma
            preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $request, $matches);
            $search_terms = array_map(array($this, 'trim_quotes_and_whitespace'), $matches[0]);

            $n = '%';
            $searchand = '';
            $search = '';

            foreach( (array) $search_terms as $term) {
                $term = addslashes_gpc($term);
                $search .= "{$searchand}(name LIKE '{$n}{$term}{$n}')";
                $searchand = ' AND ';
            }

            $term = esc_sql($request);
            if (count($search_terms) > 1 && $search_terms[0] != $request )
                $search .= " OR (name LIKE '{$n}{$term}{$n}')";

            if ( !empty($search) )
                $search = " AND ({$search}) ";

            $limit  = ( $limit > 0 ) ? 'LIMIT ' . intval($limit) : '';
        } else {
            return false;
        }

        // build the final query
        $query = "SELECT * FROM $wpdb->nggalbum WHERE 1=1 $search ORDER BY name ASC $limit";
        $result = $wpdb->get_results($query);

        return $result;
    }


    /**
     * Update or add meta data for an image
     *
     * @since 1.4.0
     * @param int $id The image ID
     * @param array $new_values An array with existing or new values
     * @return bool result of query
     */
    static function update_image_meta($id, $new_values)
    {
        global $wpdb;

        // Query database for existing values
        // Use cache object
        $old_values = $wpdb->get_var( $wpdb->prepare( "SELECT meta_data FROM $wpdb->nggpictures WHERE pid = %d ", $id ) );
        $old_values = C_NextGen_Serializable::unserialize( $old_values);
        $meta = array_merge( (array)$old_values, (array)$new_values );
        $serialized_meta = C_NextGen_Serializable::serialize($meta);
        $result = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->nggpictures SET meta_data = %s WHERE pid = %d", $serialized_meta, $id) );

        do_action('ngg_updated_image_meta', $id, $meta);

        wp_cache_delete($id, 'ngg_image');

        return $result;
    }

    /**
     * Computes a unique slug for the gallery,album or image, when given the desired slug.
     *
     * @since 1.7.0
     * @param string $slug the desired slug (post_name)
     * @param string $type ('image', 'album' or 'gallery')
     * @param int $id ID of the object, so that it's not checked against itself (optional)
     * @return string unique slug for the object, based on $slug (with a -1, -2, etc. suffix)
     */
    static function get_unique_slug( $slug, $type, $id = 0 )
    {
        global $wpdb;

        $slug    = stripslashes($slug);
        $retval  = $slug;

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

        // Generate SQL query
        $query = array();
        $query[] = "SELECT {$field}, SUBSTR({$field}, %d) AS 'i' FROM {$table}";
        $query[] = "WHERE ({$field} LIKE %s AND CONVERT(SUBSTR({$field}, %d), SIGNED) BETWEEN 1 AND %d) OR {$field} = %s";
        $query[] = "ORDER BY CAST(i AS SIGNED INTEGER) DESC LIMIT 1";
        $query = $wpdb->prepare(implode(" ", $query), strlen("{$slug}-")+1, $wpdb->esc_like("{$slug}-") . '%', strlen("{$slug}-")+1, PHP_INT_MAX, $slug);

        // If the above query returns a result, it means that the slug is already taken
        if (($last_slug = $wpdb->get_var($query))) {

            // If the last known slug has an integer attached, then it means that we need to increment that integer
            $quoted_slug = preg_quote($slug, '/');
            if (preg_match("/{$quoted_slug}-(\\d+)/", $last_slug, $matches)) {
                $i = intval($matches[1]) + 1;
                $retval = "{$slug}-{$i}";
            }
            else $retval = "{$slug}-1";
        }

        return $retval;
    }

}

if ( ! isset($GLOBALS['nggdb']) ) {
    /**
     * Initate the NextGEN Gallery Database Object, for later cache reasons
     * @global object $nggdb Creates a new nggdb object
     * @since 1.1.0
     */
    unset($GLOBALS['nggdb']);
    $GLOBALS['nggdb'] = new nggdb() ;
}