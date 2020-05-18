<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * nggAdmin - Class for admin operation
 *
 * @package NextGEN Gallery
 * @author Alex Rabe
 *
 * @access public
 */
class nggAdmin{

    /**
     * create a new gallery & folder
     *
     * @class nggAdmin
     * @param string $title Name of the gallery
     * @param string $defaultpath
     * @param bool $output if the function should show an error messsage or not
     * @return bool|int
     */
    static function create_gallery($title, $defaultpath, $output = true) {

        global $user_ID;
        $fs       = C_Fs::get_instance();
        $storage  = C_Gallery_Storage::get_instance();

        // get the current user ID
        wp_get_current_user();

        //cleanup pathname
        $name = $storage->sanitize_directory_name( sanitize_title($title)  );
        $name = apply_filters('ngg_gallery_name', $name);
        $txt = '';

        $galleryObj = new stdClass;
        $galleryObj->path = '';
        $nggRoot = $storage->get_gallery_abspath($galleryObj);

        // No gallery name ?
        if ( empty($name) ) {
            if ($output) nggGallery::show_error( __('No valid gallery name!', 'nggallery') );
            return false;
        }

        $galleryObj = new stdClass;
        $galleryObj->path = $fs->join_paths($defaultpath, $name);
        $gallery_path = $storage->get_gallery_abspath($galleryObj);

        // check for main folder
        if ( !is_dir($nggRoot) ) {
            if ( !wp_mkdir_p( $nggRoot ) ) {
                $txt  = __('Directory', 'nggallery').' <strong>' . esc_html( $nggRoot ) . '</strong> '.__('didn\'t exist. Please create first the main gallery folder ', 'nggallery').'!<br />';
                $txt .= __('Check this link, if you didn\'t know how to set the permission :', 'nggallery').' <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> ';
                if ($output) nggGallery::show_error($txt);
                return false;
            }
        }

        // 1. Check for existing folder
        if ( is_dir($gallery_path) && !(SAFE_MODE) ) {
            $suffix = 1;
            do {
                $alt_name = substr ($name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "_$suffix";
                $galleryObj->path = $fs->join_paths($defaultpath, $alt_name);
                $gallery_path = $storage->get_gallery_abspath($galleryObj);
                $dir_check = is_dir($gallery_path);
                $suffix++;
            } while ( $dir_check );
            $name = $alt_name;
        }

        $thumb_path = $fs->join_paths($gallery_path, 'thumbs');

        // 2. Create new gallery folder
        if ( !wp_mkdir_p ($gallery_path) )
          $txt  = __('Unable to create directory ', 'nggallery') . esc_html($gallery_path) . '!<br />';

        // 3. Check folder permission
        if ( !is_writeable($gallery_path) )
            $txt .= __('Directory', 'nggallery').' <strong>' . esc_html($gallery_path) . '</strong> '.__('is not writeable !', 'nggallery').'<br />';

        // 4. Now create thumbnail folder inside
        if ( !is_dir($thumb_path) ) {
            if ( !wp_mkdir_p ($thumb_path) )
                $txt .= __('Unable to create directory ', 'nggallery').' <strong>' . esc_html($thumb_path) . '/thumbs !</strong>';
        }

        if (SAFE_MODE) {
            $help  = __('The server setting Safe-Mode is on !', 'nggallery');
            $help .= '<br />'.__('If you have problems, please create directory', 'nggallery').' <strong>' . esc_html($gallery_path) . '</strong> ';
            $help .= __('and the thumbnails directory', 'nggallery').' <strong>' . esc_html($thumb_path) . '</strong> '.__('with permission 777 manually !', 'nggallery');
            if ($output) nggGallery::show_message($help);
        }

        // show a error message
        if ( !empty($txt) ) {
            if (SAFE_MODE) {
                // for safe_mode , better delete folder, both folder must be created manually
                @rmdir($thumb_path);
                @rmdir($gallery_path);
            }
            if ($output) nggGallery::show_error($txt);
            return false;
        }

        // now add the gallery to the database
        $galleryID = nggdb::add_gallery($title, $defaultpath . $name, '', 0, 0, $user_ID );
        // here you can inject a custom function
        do_action('ngg_created_new_gallery', $galleryID);

        // return only the id if defined
        if ($output == false)
            return $galleryID;

        if ($galleryID != false && !isset($_REQUEST['attach_to_post']))
        {
            $url = admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $galleryID;
            $message = sprintf(__('Gallery successfully created. <a href="%s" target="_blank">Manage gallery</a>', 'nggallery'), $url);
            if ($output)
                nggGallery::show_message($message, 'gallery_created_msg');
        }
        return true;
    }

    /**
     * Scan folder for new images
     *
     * @class nggAdmin
     * @param string $dirname
     * @return array $files list of image filenames
     */
    static function scandir( $dirname = '.' ) {
        $ext = apply_filters('ngg_allowed_file_types', array('jpeg', 'jpg', 'png', 'gif') );

        $files = array();
        if( $handle = opendir( $dirname ) ) {
            while( false !== ( $file = readdir( $handle ) ) ) {
                $info = M_I18n::mb_pathinfo( $file );
                // just look for images with the correct extension
                if ( isset($info['extension']) ) {
                    if ( in_array( strtolower($info['extension']), $ext) ) {
                        if (!seems_utf8($file)) {
                            $file = utf8_encode( $file );
                        }

                        $files[] = $file;
                    }
                }
            }

            closedir( $handle );
        }
        sort( $files );
        return ( $files );
    }

    /**
     * nggAdmin::createThumbnail() - function to create or recreate a thumbnail
     *
     * @class nggAdmin
     * @param object|int $image Contain all information about the image or the id
     * @return string result code
     * @since v1.0.0
     */
    static function create_thumbnail($image) {

        if (is_object($image)) {
            if (isset($image->id)) {
                $image = $image->id;
            }
            elseif (isset($image->pid)) {
                $image = $image->pid;
            }
        }
        $storage  = C_Gallery_Storage::get_instance();

        // XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility
        $params = array('watermark' => false, 'reflection' => false);
        $result = $storage->generate_thumbnail($image, $params);

        if (!$result)
        {
            // XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status
            return __('Error while creating thumbnail.', 'nggallery');
        }

        // success
        return '1';
    }

    /**
     * nggAdmin::resize_image() - create a new image, based on the height /width
     *
     * @class nggAdmin
     * @param object|int $image Contain all information about the image or the id
     * @param integer $width optional
     * @param integer $height optional
     * @return string result code
     */
    static function resize_image($image, $width = 0, $height = 0)
    {
        if (is_object($image)) {
            if (isset($image->id)) {
                $image = $image->id;
            }
            elseif (isset($image->pid)) {
                $image = $image->pid;
            }
        }

        $storage  = C_Gallery_Storage::get_instance();
        // XXX maybe get rid of this...it's needed to get width/height defaults, placing these directly in generate_image_size could have unwanted consequences
        $settings = C_NextGen_Settings::get_instance();

        // XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility
        $params = array('watermark' => false, 'reflection' => false);

        if ($width > 0) {
            $params['width'] = $width;
        }
        else {
            $params['width'] = $settings->imgWidth;
        }

        if ($height > 0) {
            $params['height'] = $height;
        }
        else {
            $params['height'] = $settings->imgHeight;
        }

        $result = $storage->generate_image_size($image, 'full', $params);

        if (!$result)
        {
            // XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status
            return __('Error while resizing image.', 'nggallery');
        }

        // success
        return '1';
    }

    /**
     * Rotated/Flip an image based on the orientation flag or a definded angle
     *
     * @param int|object $image
     * @param string|bool $dir (optional) CW (clockwise)or CCW (counter clockwise), if set to false, the exif flag will be used
     * @param string|bool $flip (optional) Either false | V (flip vertical) | H (flip horizontal)
     * @return string result code
     */
    static function rotate_image($image, $dir = false, $flip = false)
    {
        if (is_object($image)) {
            if (isset($image->id))        $image = $image->id;
            elseif (isset($image->pid))   $image = $image->pid;
        }
        $storage  = C_Gallery_Storage::get_instance();

        // XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility
        $params = array('watermark' => false, 'reflection' => false);
        $rotation = null;

        if ($dir === 'CW') {
            $rotation = 90;
        }
        else if ($dir === 'CCW') {
            $rotation = -90;
        }
        // if you didn't define a rotation, we look for the orientation flag in EXIF
        else if ($dir === false) {
            $meta = new nggMeta( $image);
            $exif = $meta->get_EXIF();

            if (isset($exif['Orientation'])) {

                switch ($exif['Orientation']) {
                    case 5 : // vertical flip + 90 rotate right
                        $flip = 'V';
                    case 6 : // 90 rotate right
                        $rotation = 90;
                        break;
                    case 7 : // horizontal flip + 90 rotate right
                        $flip = 'H';
                    case 8 : // 90 rotate left
                        $rotation = -90;
                        break;
                    case 4 : // vertical flip
                        $flip = 'V';
                        break;
                    case 3 : // 180 rotate left
                        $rotation = -180;
                        break;
                    case 2 : // horizontal flip
                        $flip = 'H';
                        break;
                    case 1 : // no action in the case it doesn't need a rotation
                    default:
                        return '0';
                        break;
                }
            } else
                return '0';
        }

        if ($rotation != null) {
            $params['rotation'] = $rotation;
        }

        if ($flip != null) {
            $params['flip'] = $flip;
        }

        $result = $storage->generate_image_size($image, 'full', $params);

        if (!$result)
        {
            // XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status
            return __('Error while rotating image.', 'nggallery');
        }

        // success
        return '1';
    }

    /**
     * nggAdmin::set_watermark() - set the watermark for the image
     *
     * @class nggAdmin
     * @param object|int $image Contain all information about the image or the id
     * @return string result code
     */
    static function set_watermark($image) {

        if (is_object($image)) {
            if (isset($image->id)) {
                $image = $image->id;
            }
            elseif (isset($image->pid)) {
                $image = $image->pid;
            }
        }

        $storage  = C_Gallery_Storage::get_instance();

        // XXX NextGEN Legacy was only handling watermarks at this stage, so we're forcefully disabling all else
        $params = array('watermark' => true, 'reflection' => false, 'crop' => false);
        $result = $storage->generate_image_size($image, 'full', $params);

        if (!$result)
        {
            // XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status
            return __('Error while applying watermark to image.', 'nggallery');
        }

        // success
        return '1';
    }

    /**
     * Recover image from backup copy and reprocess it
     *
     * @class nggAdmin
     * @since 1.5.0
     * @param object|int $image Contain all information about the image or the id
     * @return string result code
     */
    static function recover_image($image)
    {
        return C_Gallery_Storage::get_instance()->recover_image($image);
    }

    /**
     * Add images to database
     *
     * @class nggAdmin
     * @param int $galleryID
     * @param array $imageslist
     * @return array $image_ids IDs which have been successfully added
     */
    public static function add_Images($galleryID, $imageslist)
    {
        global $ngg;

        $image_ids = array();

        if (is_array($imageslist))
        {
            foreach($imageslist as $picture) {

                // filter function to rename/change/modify image before
                $picture = apply_filters('ngg_pre_add_new_image', $picture, $galleryID);

                // strip off the extension of the filename
                $path_parts = M_I18n::mb_pathinfo($picture);
                $alttext = (!isset($path_parts['filename'])) ? substr($path_parts['basename'], 0,strpos($path_parts['basename'], '.')) : $path_parts['filename'];

                // save it to the database
                $pic_id = nggdb::add_image($galleryID, $picture, '', $alttext);

                if (C_NextGen_Settings::get_instance()->imgBackup && !empty($pic_id))
                {
                    $storage = C_Gallery_Storage::get_instance();
                    $storage->backup_image($pic_id);
                }

                if (!empty($pic_id))
                    $image_ids[] = $pic_id;

                // add the metadata
                nggAdmin::import_MetaData($pic_id);

                // auto rotate
                nggAdmin::rotate_image( $pic_id );

                // Autoresize image if required
                if ($ngg->options['imgAutoResize'])
                {
                    $imagetmp  = nggdb::find_image( $pic_id );
                    $sizetmp   = @getimagesize ( $imagetmp->imagePath );
                    $widthtmp  = $ngg->options['imgWidth'];
                    $heighttmp = $ngg->options['imgHeight'];
                    if (($sizetmp[0] > $widthtmp && $widthtmp) || ($sizetmp[1] > $heighttmp && $heighttmp))
                        nggAdmin::resize_image( $pic_id );
                }

                // action hook for post process after the image is added to the database
                $image = array(
                    'id'        => $pic_id,
                    'filename'  => $picture,
                    'galleryID' => $galleryID
                );
                do_action('ngg_added_new_image', $image);
            }
        }

        // delete dirsize after adding new images
        delete_transient( 'dirsize_cache' );

        do_action('ngg_after_new_images_added', $galleryID, $image_ids );

        return $image_ids;
    }

    /**
     * Import some meta data into the database (if avialable)
     *
     * @class nggAdmin
     * @param array|int $imagesIds
     * @return string result code
     */
    static function import_MetaData($imagesIds) {

        global $wpdb;

        require_once(NGGALLERY_ABSPATH . '/lib/image.php');

        if (!is_array($imagesIds))
            $imagesIds = array($imagesIds);

        foreach($imagesIds as $imageID) {

	        // Get the image
	        $image = NULL;
	        if (is_int($imageID)) {
		        $image = C_Image_Mapper::get_instance()->find($imageID);
	        }
	        else $image = $imageID;

            if ($image) {

                $meta = nggAdmin::get_MetaData( $image);

                // get the title
                $alttext = empty( $meta['title'] ) ? $image->alttext : $meta['title'];

                // get the caption / description field
                $description = empty( $meta['caption'] ) ? $image->description : $meta['caption'];

                // get the file date/time from exif
                $timestamp = $meta['timestamp'];

                // first update database
                $result = $wpdb->query(
                    $wpdb->prepare("UPDATE $wpdb->nggpictures SET
                        alttext = %s,
                        description = %s,
                        imagedate = %s
                    WHERE pid = %d", $alttext, $description, $timestamp, $image->pid) );

                if ($result === false)
                    return ' <strong>' . esc_html( $image->filename ) . ' ' . __('(Error : Couldn\'t not update data base)', 'nggallery') . '</strong>';

                //this flag will inform us that the import is already one time performed
                $meta['common']['saved']  = true;
                $result = nggdb::update_image_meta($image->pid, $meta['common']);

                if ($result === false)
                    return ' <strong>' . esc_html( $image->filename ) . ' ' . __('(Error : Couldn\'t not update meta data)', 'nggallery') . '</strong>';

                // add the tags if we found some
                if ($meta['keywords']) {
                    $taglist = explode(',', $meta['keywords']);
                    wp_set_object_terms($image->pid, $taglist, 'ngg_tag');
                }

            } else
                return ' <strong>' . esc_html( $image->filename ) . ' ' . __('(Error : Couldn\'t not find image)', 'nggallery') . '</strong>';// error check
        }

        return '1';
    }

    /**
     * nggAdmin::get_MetaData()
     *
     * @class nggAdmin
     * @require NextGEN Meta class
     * @param int|object $image_or_id
     * @return array metadata
     */
    static function get_MetaData($image_or_id) {

        require_once(NGGALLERY_ABSPATH . '/lib/meta.php');

        $meta = array();

        $pdata = new nggMeta($image_or_id);

        $meta['title'] = trim ( $pdata->get_META('title') );
        $meta['caption'] = trim ( $pdata->get_META('caption') );
        $meta['keywords'] = trim ( $pdata->get_META('keywords') );
        $meta['timestamp'] = $pdata->get_date_time();
        // this contain other useful meta information
        $meta['common'] = $pdata->get_common_meta();
        // hook for addon plugin to add more meta fields
        $meta = apply_filters('ngg_get_image_metadata', $meta, $pdata);

        return $meta;

    }

    /**
     * Maybe import some meta data to the database. The functions checks the flag 'saved'
     * and if based on compat reason (pre V1.4.0) we save then some meta datas to the database
     *
     * @since V1.4.0
     * @param int|object $image_or_id
     * @return bool
     */
    function maybe_import_meta( $image_or_id )
    {
        require_once(NGGALLERY_ABSPATH . '/lib/meta.php');
		$id = is_int($image_or_id) ? $image_or_id : $image_or_id->{$image_or_id->id_field};
        $meta_obj = new nggMeta( $image_or_id );

        if ( $meta_obj->image->meta_data['saved'] != true ) {
            $common = $meta_obj->get_common_meta();
            //this flag will inform us that the import is already one time performed
            $common['saved']  = true;
            $result = nggdb::update_image_meta($id, $common);
        } else
            return false;

        return $result;
    }

    /**
     * nggAdmin::import_gallery()
     * TODO: Check permission of existing thumb folder & images
     *
     * @param string $galleryfolder contains relative path to the gallery itself
     * @param int $gallery_id
     * @return void
     */
    public static function import_gallery($galleryfolder, $gallery_id = NULL)
    {
        global $wpdb, $user_ID;

        // get the current user ID
        wp_get_current_user();

        $created_msg = '';

        // remove trailing slash at the end, if somebody use it
        $galleryfolder = untrailingslashit($galleryfolder);

        $fs = C_Fs::get_instance();
        if (is_null($gallery_id))
        {
            $gallerypath = $fs->join_paths($fs->get_document_root('content'), $galleryfolder);
        }
        else {
            $storage = C_Gallery_Storage::get_instance();
            $gallerypath = $storage->get_gallery_abspath($gallery_id);
        }

        if (!is_dir($gallerypath))
        {
            nggGallery::show_error(sprintf(__("Directory <strong>%s</strong> doesn&#96;t exist!", 'nggallery'), esc_html($gallerypath)));
            return;
        }

        // read list of images
        $new_imageslist = nggAdmin::scandir($gallerypath);

        if (empty($new_imageslist))
        {
            nggGallery::show_message(sprintf(__("Directory <strong>%s</strong> contains no pictures", 'nggallery'), esc_html($gallerypath)));
            return;
        }

        // take folder name as gallery name
        $galleryname = basename($galleryfolder);
        $galleryname = apply_filters('ngg_gallery_name', $galleryname);

        // check for existing gallery folder
        if (is_null($gallery_id))
            $gallery_id = $wpdb->get_var("SELECT gid FROM $wpdb->nggallery WHERE path = '$galleryfolder' ");

        if (!$gallery_id)
        {
            // now add the gallery to the database
            $gallery_id = nggdb::add_gallery( $galleryname, $galleryfolder, '', 0, 0, $user_ID );

            if (!$gallery_id)
            {
                nggGallery::show_error(__('Database error. Could not add gallery!','nggallery'));
                return;
            }
            else {
                do_action('ngg_created_new_gallery', $gallery_id);
            }

            $created_msg = sprintf(
                _n("Gallery <strong>%s</strong> successfully created!", 'Galleries <strong>%s</strong> successfully created!', 1, 'nggallery'),
                esc_html($galleryname)
            );
        }

        // Look for existing image list
        $old_imageslist = $wpdb->get_col("SELECT `filename` FROM {$wpdb->nggpictures} WHERE `galleryid` = '{$gallery_id}'");

        // if no images are there, create empty array
        if ($old_imageslist == NULL)
            $old_imageslist = array();

        // check difference
        $new_images = array_diff($new_imageslist, $old_imageslist);

        // all images must be valid files
        foreach($new_images as $key => $picture) {
            // filter function to rename/change/modify image before
            $picture = apply_filters('ngg_pre_add_new_image', $picture, $gallery_id);
            $new_images[$key] = $picture;

            if (!@getimagesize($gallerypath . '/' . $picture))
            {
                unset($new_images[$key]);
                @unlink($gallerypath . '/' . $picture);
            }
        }

        // add images to database
        $image_ids = nggAdmin::add_Images($gallery_id, $new_images);
        do_action('ngg_after_new_images_added', $gallery_id, $image_ids);

        //add the preview image if needed
        nggAdmin::set_gallery_preview ( $gallery_id );

        // now create thumbnails
        nggAdmin::do_ajax_operation( 'create_thumbnail' , $image_ids, __('Create new thumbnails','nggallery') );

        //TODO:Message will not shown, because AJAX routine require more time, message should be passed to AJAX
        $message  = $created_msg . sprintf(_n('%s picture successfully added', '%s pictures successfully added', count($image_ids), 'nggallery'), count($image_ids));
        $message .= ' [<a href="' . admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery_id . '" >';
        $message .=  __('Edit gallery','nggallery');
        $message .= '</a>]';

        nggGallery::show_message($message);

        return;
    }

    /**
     * Capability check. Check is the ID fit's to the user_ID
     *
     * @class nggAdmin
     * @param int $check_ID is the user_id
     * @return bool $result
     */
    static function can_manage_this_gallery($check_ID) {

        global $user_ID, $wp_roles;

        if ( !current_user_can('NextGEN Manage others gallery') ) {
            // get the current user ID
            wp_get_current_user();

            if ( $user_ID != $check_ID)
                return false;
        }

        return true;

    }

    /**
     * Initate the Ajax operation
     *
     * @class nggAdmin
     * @param string $operation name of the function which should be executed
     * @param array $image_array
     * @param string $title name of the operation
     * @return string the javascript output
     */
    static function do_ajax_operation( $operation, $image_array, $title = '' ) {

        if ( !is_array($image_array) || empty($image_array) )
            return '';

        $js_array  = implode('","', $image_array);

        // send out some JavaScript, which initate the ajax operation
        ob_start();
        ?>
        <script type="text/javascript">

            Images = new Array("<?php echo $js_array; ?>");

            nggAjaxOptions = {
                operation: "<?php echo $operation; ?>",
                ids: Images,
                header: "<?php echo $title; ?>",
                maxStep: Images.length
            };

            jQuery(document).ready( function(){
                nggProgressBar.init( nggAjaxOptions );
                nggAjax.init( nggAjaxOptions );
            } );
        </script>
    <?php
        $script = ob_get_clean();
        echo $script;
        return $script;
    }

    /**
     * nggAdmin::set_gallery_preview() - define a preview pic after the first upload, can be changed in the gallery settings
     *
     * @class nggAdmin
     * @param int $galleryID
     * @return void
     */
    static function set_gallery_preview( $galleryID )
    {
	    $gallery_mapper = C_Gallery_Mapper::get_instance();
	    if (($gallery = $gallery_mapper->find($galleryID))) {
		    if (!$gallery->previewpic) {
			    $image_mapper = C_Image_Mapper::get_instance();
			    if (($image = $image_mapper->select()->where(array('galleryid = %d', $galleryID))->where(array('exclude != 1'))->
			        order_by($image_mapper->get_primary_key_column())->limit(1)->run_query())) {
				    $gallery->previewpic = $image->{$image->id_field};
				    $gallery_mapper->save($gallery);
			    }
		    }
	    }
    }

    /**
     * Return a JSON coded array of Image ids for a requested gallery
     *
     * @class nggAdmin
     * @param int $galleryID
     * @return string|int (JSON)
     */
    static function get_image_ids( $galleryID ) {

        if ( !function_exists('json_encode') )
            return(-2);

        $gallery = nggdb::get_ids_from_gallery($galleryID, 'pid', 'ASC', false);

        header('Content-Type: text/plain; charset=' . get_option('blog_charset'), true);
        $output = json_encode($gallery);

        return $output;
    }

    /**
     * Deprecated function, restored to fix compatibility with "NextGen Public Uploader"
     *
     * @deprecated
     * @class nggAdmin
     * @param string $filename
     * @return bool $result
     */
    function chmod($filename = '')
    {
        $stat = @stat(dirname($filename));
        $perms = $stat['mode'] & 0000666;
        if (@chmod($filename, $perms))
            return true;
        return false;
    }

} // END class nggAdmin

// XXX temporary...used as a quick fix to refresh I_Settings_Manager when the nextgen option is updated manually in order to run Hooks etc.
function ngg_refreshSavedSettings()
{
    if (class_exists('C_NextGen_Settings')) {
        $settings = C_NextGen_Settings::get_instance();

        if ($settings != null)
        {
            $width          = $settings->thumbwidth;
            $height         = $settings->thumbheight;
            $new_dimension  = "{$width}x{$height}";
            $dimensions     = (array) $settings->thumbnail_dimensions;

            if (!in_array($new_dimension, $dimensions)) {
                $dimensions[]   = $new_dimension;
                $settings->thumbnail_dimensions = $dimensions;
                $settings->save();

                return true;
            }
        }
    }

    return false;
}
