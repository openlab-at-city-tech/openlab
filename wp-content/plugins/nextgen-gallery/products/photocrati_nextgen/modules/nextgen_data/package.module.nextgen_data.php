<?php
/**
 * Modifies a custom post datamapper to use the WordPress built-in 'attachment'
 * custom post type, as used by the Media Library
 *
 * @todo Not used yet
 */
class A_Attachment_DataMapper extends Mixin
{
    function initialize()
    {
        $this->object->_object_name = 'attachment';
    }
    /**
     * Saves the entity using the wp_insert_attachment function instead of the wp_insert_post
     * @param object $entity
     * @return int Attachment ID
     */
    function _save_entity($entity)
    {
        $post = $this->object->_convert_entity_to_post($entity);
        $filename = property_exists($entity, 'filename') ? $entity->filename : FALSE;
        $primary_key = $this->object->get_primary_key_column();
        if ($post_id = $attachment_id = wp_insert_attachment($post, $filename)) {
            $new_entity = $this->object->find($post_id);
            foreach ($new_entity as $key => $value) {
                $entity->{$key} = $value;
            }
            // Merge meta data with WordPress Attachment Meta Data
            if (property_exists($entity, 'meta_data')) {
                $meta_data = wp_get_attachment_metadata($attachment_id);
                if (isset($meta_data['image_meta'])) {
                    $entity->meta_data = array_merge_recursive($meta_data['image_meta'], $entity->meta_data);
                    wp_update_attachment_metadata($attachment_id, $entity->meta_data);
                }
            }
            // Save properties are post meta as well
            $this->object->_flush_and_update_postmeta($attachment_id, $entity instanceof stdClass ? $entity : $entity->get_entity(), array('_wp_attached_file', '_wp_attachment_metadata', '_mapper'));
            $entity->id_field = $primary_key;
        }
        return $attachment_id;
    }
    function select($fields = '*')
    {
        $ret = $this->call_parent('select', $fields);
        $this->object->_query_args['datamapper_attachment'] = true;
        return $ret;
    }
}
/**
 * Class A_NextGen_Data_Factory
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_NextGen_Data_Factory extends Mixin
{
    function gallery($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Gallery($properties, $mapper, $context);
    }
    function gallery_image($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Image($properties, $mapper, $context);
    }
    function image($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Image($properties, $mapper, $context);
    }
    function album($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Album($properties, $mapper, $context);
    }
    function gallery_storage($context = FALSE)
    {
        return new C_Gallery_Storage($context);
    }
    function extra_fields($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Datamapper_Model($mapper, $properties, $context);
    }
    function gallerystorage($context = FALSE)
    {
        return $this->object->gallery_storage($context);
    }
}
/**
 * Class C_Album
 * @mixin Mixin_NextGen_Album_Instance_Methods
 * @implements I_Album
 */
class C_Album extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Album_Mapper';
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_NextGen_Album_Instance_Methods');
        $this->implement('I_Album');
    }
    /**
     * Instantiates an Album object
     * @param array $properties
     * @param C_Album_Mapper|bool $mapper (optional)
     * @param string|bool $context (optional)
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        parent::initialize($mapper, $properties);
    }
}
/**
 * Provides instance methods for the album
 */
class Mixin_NextGen_Album_Instance_Methods extends Mixin
{
    function validation()
    {
        $this->validates_presence_of('name');
        $this->validates_numericality_of('previewpic');
        return $this->object->is_valid();
    }
    /**
     * Gets all galleries associated with the album
     * @param array|bool $models (optional)
     * @return array
     */
    function get_galleries($models = FALSE)
    {
        $retval = array();
        $mapper = C_Gallery_Mapper::get_instance();
        $gallery_key = $mapper->get_primary_key_column();
        $retval = $mapper->find_all(array("{$gallery_key} IN %s", $this->object->sortorder), $models);
        return $retval;
    }
}
/**
 * Class C_Album_Mapper
 * @mixin Mixin_NextGen_Table_Extras
 * @mixin Mixin_Album_Mapper
 * @implements I_Album_Mapper
 */
class C_Album_Mapper extends C_CustomTable_DataMapper_Driver
{
    static $_instance = NULL;
    function initialize($object_name = FALSE)
    {
        parent::initialize('ngg_album');
    }
    function define($context = FALSE, $not_used = FALSE)
    {
        // Define the context
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, 'album');
        $this->_primary_key_column = 'id';
        // Define the mapper
        parent::define('ngg_album', $context);
        $this->add_mixin('Mixin_NextGen_Table_Extras');
        $this->add_mixin('Mixin_Album_Mapper');
        $this->implement('I_Album_Mapper');
        $this->set_model_factory_method('album');
        // Define the columns
        $this->define_column('id', 'BIGINT', 0);
        $this->define_column('name', 'VARCHAR(255)');
        $this->define_column('slug', 'VARCHAR(255');
        $this->define_column('previewpic', 'BIGINT', 0);
        $this->define_column('albumdesc', 'TEXT');
        $this->define_column('sortorder', 'TEXT');
        $this->define_column('pageid', 'BIGINT', 0);
        $this->define_column('extras_post_id', 'BIGINT', 0);
        // Mark the columns which should be unserialized
        $this->add_serialized_column('sortorder');
    }
    /**
     * Returns an instance of the album datamapper
     * @param bool|string $context
     * @return C_Album_Mapper
     */
    static function get_instance($context = FALSE)
    {
        if (is_null(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass($context);
        }
        return self::$_instance;
    }
    /**
     * @param string $slug
     * @return null|stdClass|C_Album
     */
    public function get_by_slug($slug)
    {
        $results = $this->object->select()->where(['slug = %s', sanitize_title($slug)])->limit(1)->run_query();
        return array_pop($results);
    }
}
/**
 * Provides album-specific methods for the datamapper
 */
class Mixin_Album_Mapper extends Mixin
{
    /**
     * Gets the post title when the Custom Post driver is used
     * @param C_DataMapper_Model|C_Album|stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->name;
    }
    function _save_entity($entity)
    {
        $retval = $this->call_parent('_save_entity', $entity);
        if ($retval) {
            do_action('ngg_album_updated', $entity);
            C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');
        }
        return $retval;
    }
    /**
     * Sets the defaults for an album
     * @param C_DataMapper_Model|C_Album|stdClass $entity
     */
    function set_defaults($entity)
    {
        $this->object->_set_default_value($entity, 'name', '');
        $this->object->_set_default_value($entity, 'albumdesc', '');
        $this->object->_set_default_value($entity, 'sortorder', array());
        $this->object->_set_default_value($entity, 'previewpic', 0);
        $this->object->_set_default_value($entity, 'exclude', 0);
        if (isset($entity->name) && !isset($entity->slug)) {
            $entity->slug = nggdb::get_unique_slug(sanitize_title($entity->name), 'album');
        }
        if (!is_admin()) {
            if (!empty($entity->name)) {
                $entity->name = M_I18N::translate($entity->name, 'album_' . $entity->{$entity->id_field} . '_name');
            }
            if (!empty($entity->albumdesc)) {
                $entity->albumdesc = M_I18N::translate($entity->albumdesc, 'album_' . $entity->{$entity->id_field} . '_description');
            }
            // these fields are set when the album is a child to another album
            if (!empty($entity->title)) {
                $entity->title = M_I18N::translate($entity->title, 'album_' . $entity->{$entity->id_field} . '_name');
            }
            if (!empty($entity->galdesc)) {
                $entity->galdesc = M_I18N::translate($entity->galdesc, 'album_' . $entity->{$entity->id_field} . '_description');
            }
        }
    }
}
class Mixin_Dynamic_Thumbnails_Manager extends Mixin
{
    function get_route_name()
    {
        return C_NextGen_Settings::get_instance()->get('dynamic_thumbnail_slug');
    }
    function _get_params_sanitized($params)
    {
        if (isset($params['rotation'])) {
            $rotation = intval($params['rotation']);
            if ($rotation && in_array(abs($rotation), array(90, 180, 270))) {
                $rotation = $rotation % 360;
                if ($rotation < 0) {
                    $rotation = 360 - $rotation;
                }
                $params['rotation'] = $rotation;
            } else {
                unset($params['rotation']);
            }
        }
        if (isset($params['flip'])) {
            $flip = strtolower($params['flip']);
            if (in_array($flip, array('h', 'v', 'hv'))) {
                $params['flip'] = $flip;
            } else {
                unset($params['flip']);
            }
        }
        return $params;
    }
    function get_uri_from_params($params)
    {
        $params = $this->object->_get_params_sanitized($params);
        $image = isset($params['image']) ? $params['image'] : null;
        $image_id = is_scalar($image) || is_null($image) ? (int) $image : $image->pid;
        $image_width = isset($params['width']) ? $params['width'] : null;
        $image_height = isset($params['height']) ? $params['height'] : null;
        $image_quality = isset($params['quality']) ? $params['quality'] : null;
        $image_type = isset($params['type']) ? $params['type'] : null;
        $image_crop = isset($params['crop']) ? $params['crop'] : null;
        $image_watermark = isset($params['watermark']) ? $params['watermark'] : null;
        $image_rotation = isset($params['rotation']) ? $params['rotation'] : null;
        $image_flip = isset($params['flip']) ? $params['flip'] : null;
        $image_reflection = isset($params['reflection']) ? $params['reflection'] : null;
        $uri = null;
        $uri .= '/';
        $uri .= $this->object->get_route_name() . '/';
        $uri .= strval($image_id) . '/';
        $uri .= strval($image_width) . 'x' . strval($image_height);
        if ($image_quality != null) {
            $uri .= 'x' . strval($image_quality);
        }
        $uri .= '/';
        if ($image_type != null) {
            $uri .= $image_type . '/';
        }
        if ($image_crop) {
            $uri .= 'crop/';
        }
        if ($image_watermark) {
            $uri .= 'watermark/';
        }
        if ($image_rotation) {
            $uri .= 'rotation-' . $image_rotation . '/';
        }
        if ($image_flip) {
            $uri .= 'flip-' . $image_flip . '/';
        }
        if ($image_reflection) {
            $uri .= 'reflection/';
        }
        return $uri;
    }
    function get_image_uri($image, $params)
    {
        $params['image'] = $image;
        $uri = $this->object->get_uri_from_params($params);
        if (substr($uri, -1) != '/') {
            $uri .= '/';
        }
        $uri .= wp_hash($uri) . '/';
        return $uri;
    }
    function get_image_url($image, $params)
    {
        return C_Router::get_instance()->get_url($this->object->get_image_uri($image, $params), FALSE, 'root');
    }
    function get_params_from_uri($uri)
    {
        $regex = '/\\/?' . $this->object->get_route_name() . '\\/(\\d+)(?:\\/(.*))?/';
        $match = null;
        // XXX move this URL clean up to I_Router?
        $uri = preg_replace('/\\/index.php\\//', '/', $uri, 1);
        $uri = trim($uri, '/');
        if (@preg_match($regex, $uri, $match) > 0) {
            $image_id = $match[1];
            $uri_args = isset($match[2]) ? explode('/', $match[2]) : array();
            $params = array('image' => $image_id);
            foreach ($uri_args as $uri_arg) {
                $uri_arg_set = explode('-', $uri_arg);
                $uri_arg_name = array_shift($uri_arg_set);
                $uri_arg_value = $uri_arg_set ? array_shift($uri_arg_set) : null;
                $size_match = null;
                if ($uri_arg == 'watermark') {
                    $params['watermark'] = true;
                } else {
                    if ($uri_arg_name == 'rotation') {
                        $params['rotation'] = $uri_arg_value;
                    } else {
                        if ($uri_arg_name == 'flip') {
                            $params['flip'] = $uri_arg_value;
                        } else {
                            if ($uri_arg == 'reflection') {
                                $params['reflection'] = true;
                            } else {
                                if ($uri_arg == 'crop') {
                                    $params['crop'] = true;
                                } else {
                                    if (in_array(strtolower($uri_arg), apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES))) {
                                        $params['type'] = $uri_arg;
                                    } else {
                                        if (preg_match('/(\\d+)x(\\d+)(?:x(\\d+))?/i', $uri_arg, $size_match) > 0) {
                                            $params['width'] = $size_match[1];
                                            $params['height'] = $size_match[2];
                                            if (isset($size_match[3])) {
                                                $params['quality'] = $size_match[3];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $this->object->_get_params_sanitized($params);
        }
        return null;
    }
    function _get_name_prefix_list()
    {
        return array('id' => 'nggid0', 'size' => 'ngg0dyn-', 'flags' => '00f0', 'flag' => array('w0' => 'watermark', 'c0' => 'crop', 'r1' => 'rotation', 'f1' => 'flip', 'r0' => 'reflection', 't0' => 'type'), 'flag_len' => 2, 'max_value_length' => 15);
    }
    function get_name_from_params($params, $only_size_name = false, $id_in_name = true)
    {
        $prefix_list = $this->object->_get_name_prefix_list();
        $id_prefix = $prefix_list['id'];
        $size_prefix = $prefix_list['size'];
        $flags_prefix = $prefix_list['flags'];
        $flags = $prefix_list['flag'];
        $max_value_length = $prefix_list['max_value_length'];
        $params = $this->object->_get_params_sanitized($params);
        $image = isset($params['image']) ? $params['image'] : null;
        $image_width = isset($params['width']) ? $params['width'] : null;
        $image_height = isset($params['height']) ? $params['height'] : null;
        $image_quality = isset($params['quality']) ? $params['quality'] : null;
        $extension = null;
        $name = null;
        // if $only_size_name is false then we include the file name and image id for the image
        if (!$only_size_name) {
            if (is_int($image)) {
                $imap = C_Image_Mapper::get_instance();
                $image = $imap->find($image);
            }
            if ($image != null) {
                // this is used to remove the extension and then add it back at the end of the name
                $extension = M_I18n::mb_pathinfo($image->filename, PATHINFO_EXTENSION);
                if ($extension != null) {
                    $extension = '.' . $extension;
                }
                $name .= M_I18n::mb_basename($image->filename);
                $name .= '-';
                if ($id_in_name) {
                    $image_id = strval($image->pid);
                    $id_len = min($max_value_length, strlen($image_id));
                    $id_len_hex = dechex($id_len);
                    // sanity check, should never occurr if $max_value_length is not messed up, ensure only 1 character is used to encode length or else skip parameter
                    if (strlen($id_len_hex) == 1) {
                        $name .= $id_prefix . $id_len . substr($image_id, 0, $id_len);
                        $name .= '-';
                    }
                }
            }
        }
        $name .= $size_prefix;
        $name .= strval($image_width) . 'x' . strval($image_height);
        if ($image_quality != null) {
            $name .= 'x' . $image_quality;
        }
        $name .= '-';
        $name .= $flags_prefix;
        foreach ($flags as $flag_prefix => $flag_name) {
            $flag_value = 0;
            if (isset($params[$flag_name])) {
                $flag_value = $params[$flag_name];
                if (!is_string($flag_value)) {
                    // only strings or ints allowed, sprintf is required because intval(0) returns '' and not '0'
                    $flag_value = intval($flag_value);
                    $flag_value = sprintf('%d', $flag_value);
                }
            }
            $flag_value = strval($flag_value);
            $flag_len = min($max_value_length, strlen($flag_value));
            $flag_len_hex = dechex($flag_len);
            // sanity check, should never occurr if $max_value_length is not messed up, ensure only 1 character is used to encode length or else skip parameter
            if (strlen($flag_len_hex) == 1) {
                $name .= $flag_prefix . $flag_len . substr($flag_value, 0, $flag_len);
            }
        }
        $name .= $extension;
        return $name;
    }
    function get_size_name($params)
    {
        $name = $this->object->get_name_from_params($params, true);
        return $name;
    }
    function get_image_name($image, $params)
    {
        $params['image'] = $image;
        $name = $this->object->get_name_from_params($params);
        return $name;
    }
    function get_params_from_name($name, $is_only_size_name = false)
    {
        $prefix_list = $this->object->_get_name_prefix_list();
        $id_prefix = $prefix_list['id'];
        $size_prefix = $prefix_list['size'];
        $flags_prefix = $prefix_list['flags'];
        $max_value_length = $prefix_list['max_value_length'];
        $size_name = '';
        $id_name = '';
        $params = [];
        if (!$is_only_size_name) {
            $name = M_I18n::mb_basename($name);
        }
        $size_index = strrpos($name, $size_prefix);
        if ($size_index > 0 || $size_index === 0) {
            // check if name contains dynamic size/params info by looking for prefix
            $size_name = substr($name, $size_index);
        }
        if (!$is_only_size_name) {
            // name should contain the image id, search for prefix
            $id_index = strrpos($name, $id_prefix);
            if ($id_index > 0 || $id_index === 0) {
                if ($size_index > 0 && $size_index > $id_index) {
                    $id_name = substr($name, $id_index, $size_index - $id_index);
                } else {
                    $id_name = substr($name, $id_index);
                }
            }
        }
        // Double check we got a correct dynamic size/params string
        if (substr($size_name, 0, strlen($size_prefix)) == $size_prefix) {
            $flags = $prefix_list['flag'];
            // get the length of the flag id (the key in the $flags array) in the string (how many characters to consume)
            $flag_id_len = $prefix_list['flag_len'];
            $params_str = substr($size_name, strlen($size_prefix));
            $params_parts = explode('-', $params_str);
            // $param_part is a single param, separated by '-'
            foreach ($params_parts as $param_part) {
                // Parse WxHxQ - Q=quality
                $param_size = explode('x', $param_part);
                $param_size_count = count($param_size);
                if (substr($param_part, 0, strlen($flags_prefix)) == $flags_prefix) {
                    /* Set flags, using $flags keys as prefixes */
                    // move string pointer up (after the main flags prefix)
                    $param_flags = substr($param_part, strlen($flags_prefix));
                    $param_flags_len = strlen($param_flags);
                    $flags_todo = $flags;
                    while (true) {
                        // ensure we don't run into an infinite loop ;)
                        if (count($flags_todo) == 0 || strlen($param_flags) == 0) {
                            break;
                        }
                        // get the flag prefix (a key in the $flags array) using flag id length
                        $flag_prefix = substr($param_flags, 0, $flag_id_len);
                        // move string pointer up (after the single flag prefix)
                        $param_flags = substr($param_flags, $flag_id_len);
                        // get the length of the flag value in the string (how many characters to consume)
                        // flag value length is stored in a single hexadecimal character next to the flag prefix
                        $flag_value_len = min(hexdec(substr($param_flags, 0, 1)), min($max_value_length, strlen($param_flags) - 1));
                        // get the flag value
                        $flag_value = substr($param_flags, 1, $flag_value_len);
                        // move string pointer up (after the entire flag)
                        $param_flags = substr($param_flags, $flag_value_len + 1);
                        // make sure the flag is supported
                        if (isset($flags[$flag_prefix])) {
                            $flag_name = $flags[$flag_prefix];
                            if (is_numeric($flag_value)) {
                                // convert numerical flags to integers
                                $flag_value = intval($flag_value);
                            }
                            $params[$flag_name] = $flag_value;
                            if (isset($flags_todo[$flag_prefix])) {
                                unset($flags_todo[$flag_prefix]);
                            }
                        } else {
                            // XXX unknown flag?
                        }
                    }
                } else {
                    if ($param_size_count == 2 || $param_size_count == 3) {
                        // Set W H Q
                        $params['width'] = intval($param_size[0]);
                        $params['height'] = intval($param_size[1]);
                        if (isset($param_size[2]) && intval($param_size[2]) > 0) {
                            $params['quality'] = intval($param_size[2]);
                        }
                    }
                }
            }
        }
        // Double check we got a correct id string
        if (substr($id_name, 0, strlen($id_prefix)) == $id_prefix) {
            // move string pointer up (after the prefix)
            $id_name = substr($id_name, strlen($id_prefix));
            // get the length of the image id in the string (how many characters to consume)
            $id_len = min(hexdec(substr($id_name, 0, 1)), min($max_value_length, strlen($id_name) - 1));
            // get the id based on old position and id length
            $image_id = intval(substr($id_name, 1, $id_len));
            if ($image_id > 0) {
                $params['image'] = $image_id;
            }
        }
        return $this->object->_get_params_sanitized($params);
    }
    function is_size_dynamic($name, $is_only_size_name = false)
    {
        $params = $this->object->get_params_from_name($name, $is_only_size_name);
        if (isset($params['width']) && isset($params['height'])) {
            return true;
        }
        return false;
    }
}
/**
 * Class C_Dynamic_Thumbnails_Manager
 * @mixin Mixin_Dynamic_Thumbnails_Manager
 */
class C_Dynamic_Thumbnails_Manager extends C_Component
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Dynamic_Thumbnails_Manager');
        $this->add_mixin('Mixin_Dynamic_Thumbnails_Manager');
    }
    /**
     * @param bool|string $context
     * @return C_Dynamic_Thumbnails_Manager
     */
    static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Dynamic_Thumbnails_Manager($context);
        }
        return self::$_instances[$context];
    }
}
// 0.9.10 is compatible with PHP 8.0 but requires 7.2.0 as its minimum.
if (version_compare(phpversion(), '7.2.0', '>=')) {
    require_once 'pel-0.9.10/autoload.php';
} else {
    require_once 'pel-0.9.9/autoload.php';
}
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpegInvalidMarkerException;
class C_Exif_Writer
{
    /**
     * @param $filename
     * @return array|null
     */
    public static function read_metadata($filename)
    {
        if (!self::is_jpeg_file($filename)) {
            return NULL;
        }
        try {
            $data = new PelDataWindow(@file_get_contents($filename));
            $exif = new PelExif();
            if (PelJpeg::isValid($data)) {
                $jpeg = $file = new PelJpeg();
                $jpeg->load($data);
                $exif = $jpeg->getExif();
                if ($exif === NULL) {
                    $exif = new PelExif();
                    $jpeg->setExif($exif);
                    $tiff = new PelTiff();
                    $exif->setTiff($tiff);
                } else {
                    $tiff = $exif->getTiff();
                }
            } elseif (PelTiff::isValid($data)) {
                $tiff = $file = new PellTiff();
                $tiff->load($data);
            } else {
                return NULL;
            }
            $ifd0 = $tiff->getIfd();
            if ($ifd0 === NULL) {
                $ifd0 = new PelIfd(PelIfd::IFD0);
                $tiff->setIfd($ifd0);
            }
            $tiff->setIfd($ifd0);
            $exif->setTiff($tiff);
            $retval = array('exif' => $exif, 'iptc' => NULL);
            @getimagesize($filename, $iptc);
            if (!empty($iptc['APP13'])) {
                $retval['iptc'] = $iptc['APP13'];
            }
        } catch (PelIfdException $exception) {
            return NULL;
        } catch (PelInvalidArgumentException $exception) {
            return NULL;
        } catch (PelInvalidDataException $exception) {
            return NULL;
        } catch (PelJpegInvalidMarkerException $exception) {
            return NULL;
        } catch (Exception $exception) {
            return NULL;
        }
        return $retval;
    }
    /**
     * @param $origin_file
     * @param $destination_file
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    public static function copy_metadata($origin_file, $destination_file)
    {
        if (!self::is_jpeg_file($origin_file)) {
            return FALSE;
        }
        // Read existing data from the source file
        $metadata = self::read_metadata($origin_file);
        if (!empty($metadata) && is_array($metadata)) {
            return self::write_metadata($destination_file, $metadata);
        } else {
            return FALSE;
        }
    }
    /**
     * @param $filename
     * @param $metadata
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    public static function write_metadata($filename, $metadata)
    {
        if (!self::is_jpeg_file($filename) || !is_array($metadata)) {
            return FALSE;
        }
        try {
            // Prevent the orientation tag from ever being anything other than normal horizontal
            /** @var PelExif $exif */
            $exif = $metadata['exif'];
            $tiff = $exif->getTiff();
            $ifd0 = $tiff->getIfd();
            $orientation = new PelEntryShort(PelTag::ORIENTATION, 1);
            $ifd0->addEntry($orientation);
            $tiff->setIfd($ifd0);
            $exif->setTiff($tiff);
            $metadata['exif'] = $exif;
            // Copy EXIF data to the new image and write it
            $new_image = new PelJpeg($filename);
            $new_image->setExif($metadata['exif']);
            $new_image->saveFile($filename);
            // Copy IPTC / APP13 to the new image and write it
            if ($metadata['iptc']) {
                return self::write_IPTC($filename, $metadata['iptc']);
            }
        } catch (PelInvalidArgumentException $exception) {
            return FALSE;
        } catch (PelInvalidDataException $exception) {
            error_log("Could not write data to {$filename}");
            error_log(print_r($exception, TRUE));
            return FALSE;
        }
    }
    // In case bcmath isn't enabled we use these simple wrappers.
    static function bcadd($one, $two, $scale = NULL)
    {
        if (!function_exists('bcadd')) {
            return floatval($one) + floatval($two);
        } else {
            return bcadd($one, $two, $scale);
        }
    }
    static function bcmul($one, $two, $scale = NULL)
    {
        if (!function_exists('bcmul')) {
            return floatval($one) * floatval($two);
        } else {
            return bcmul($one, $two, $scale);
        }
    }
    static function bcpow($one, $two, $scale = NULL)
    {
        if (!function_exists('bcpow')) {
            return floatval($one) ** floatval($two);
        } else {
            return bcpow($one, $two, $scale);
        }
    }
    /**
     * Use bcmath as a replacement to hexdec() to handle numbers than PHP_INT_MAX. Also validates the $hex parameter using ctypes.
     *
     * @param string $hex
     * @return float|int|string|null
     */
    public static function bchexdec($hex)
    {
        // Ensure $hex is actually a valid hex number and won't generate deprecated conversion warnings on PHP 7.4+
        if (!ctype_xdigit($hex)) {
            return NULL;
        }
        $decimal = 0;
        $length = strlen($hex);
        for ($i = 1; $i <= $length; $i++) {
            $decimal = self::bcadd($decimal, self::bcmul(strval(hexdec($hex[$i - 1])), self::bcpow('16', strval($length - $i))));
        }
        return $decimal;
    }
    /**
     * @param string $filename
     * @param array $data
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    public static function write_IPTC($filename, $data)
    {
        if (!self::is_jpeg_file($filename)) {
            return FALSE;
        }
        $length = strlen($data) + 2;
        // Avoid invalid APP13 regions
        if ($length > 0xffff) {
            return FALSE;
        }
        // Wrap existing data in segment container we can embed new content in
        $data = chr(0xff) . chr(0xed) . chr($length >> 8 & 0xff) . chr($length & 0xff) . $data;
        $new_file_contents = @file_get_contents($filename);
        if (!$new_file_contents || strlen($new_file_contents) <= 0) {
            return FALSE;
        }
        $new_file_contents = substr($new_file_contents, 2);
        // Create new image container wrapper
        $new_iptc = chr(0xff) . chr(0xd8);
        // Track whether content was modified
        $new_fields_added = !$data;
        // This can cause errors if incorrectly pointed at a non-JPEG file
        try {
            // Loop through each JPEG segment in search of region 13
            while ((self::bchexdec(substr($new_file_contents, 0, 2)) & 0xfff0) === 0xffe0) {
                $segment_length = hexdec(substr($new_file_contents, 2, 2)) & 0xffff;
                $segment_number = hexdec(substr($new_file_contents, 1, 1)) & 0xf;
                // Not a segment we're interested in
                if ($segment_length <= 2) {
                    return FALSE;
                }
                $current_segment = substr($new_file_contents, 0, $segment_length + 2);
                if (13 <= $segment_number && !$new_fields_added) {
                    $new_iptc .= $data;
                    $new_fields_added = TRUE;
                    if (13 === $segment_number) {
                        $current_segment = '';
                    }
                }
                $new_iptc .= $current_segment;
                $new_file_contents = substr($new_file_contents, $segment_length + 2);
            }
        } catch (Exception $exception) {
            return FALSE;
        }
        if (!$new_fields_added) {
            $new_iptc .= $data;
        }
        if ($file = @fopen($filename, 'wb')) {
            return @fwrite($file, $new_iptc . $new_file_contents);
        } else {
            return FALSE;
        }
    }
    /**
     * Determines if the file extension is .jpg or .jpeg
     *
     * @param $filename
     * @return bool
     */
    public static function is_jpeg_file($filename)
    {
        $extension = M_I18n::mb_pathinfo($filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), array('jpeg', 'jpg', 'jpeg_backup', 'jpg_backup')) ? TRUE : FALSE;
    }
}
/**
 * Creates a model representing a NextGEN Gallery object
 * @mixin Mixin_NextGen_Gallery_Validation
 * @implements I_Gallery
 */
class C_Gallery extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Gallery_Mapper';
    /**
     * Defines the interfaces and methods (through extensions and hooks)
     * that this class provides
     */
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_NextGen_Gallery_Validation');
        $this->implement('I_Gallery');
    }
    /**
     * Instantiates a new model
     * @param array|stdClass $properties (optional)
     * @param C_Gallery_Mapper|false $mapper (optional)
     * @param string|bool $context (optional)
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        parent::initialize($mapper, $properties);
    }
    function get_images()
    {
        $mapper = C_Image_Mapper::get_instance();
        return $mapper->select()->where(array('galleryid = %d', $this->gid))->order_by('sortorder')->run_query();
    }
}
class Mixin_NextGen_Gallery_Validation
{
    /**
     * Validates whether the gallery can be saved
     */
    function validation()
    {
        // If a title is present, we can auto-populate some other properties
        if ($this->object->title) {
            // Strip html
            $this->object->title = M_NextGen_Data::strip_html($this->object->title, TRUE);
            $sanitized_title = str_replace(' ', '-', $this->object->title);
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $sanitized_title = remove_accents($sanitized_title);
            }
            // If no name is present, use the title to generate one
            if (!$this->object->name) {
                $this->object->name = apply_filters('ngg_gallery_name', sanitize_file_name($sanitized_title));
            }
            // Assign a slug; possibly updating the current slug if it was conceived by a method other than sanitize_title()
            // NextGen 3.2.19 and older used a method adopted from esc_url() which would convert ampersands to "&amp;"
            // and allow slashes in gallery slugs which breaks their ability to be linked to as children of albums
            $sanitized_slug = sanitize_title($sanitized_title);
            if (empty($this->object->slug) || $this->object->slug !== $sanitized_slug) {
                $this->object->slug = $sanitized_slug;
                $this->object->slug = nggdb::get_unique_slug($this->object->slug, 'gallery');
            }
        }
        // Set what will be the path to the gallery
        $storage = C_Gallery_Storage::get_instance();
        if (!$this->object->path) {
            $this->object->path = $storage->get_gallery_relpath($this->object);
        }
        // Ensure that the gallery path is restricted to $fs->get_document_root('galleries')
        $fs = C_Fs::get_instance();
        $root = $fs->get_document_root('galleries');
        $storage->flush_gallery_path_cache($this->object);
        $gallery_abspath = $storage->get_gallery_abspath($this->object);
        if (strpos($gallery_abspath, $root) === FALSE) {
            $this->object->add_error(sprintf(__("Gallery path must be located in %s", 'nggallery'), $root), 'gallerypath');
            $this->object->path = $storage->get_upload_relpath($this->object);
        }
        $this->object->path = trailingslashit($this->object->path);
        // Check for '..' in the path
        $sections = explode(DIRECTORY_SEPARATOR, trim($this->object->path, '/\\'));
        if (in_array('..', $sections, TRUE)) {
            $this->object->add_error(__("Gallery paths may not use '..' to access parent directories)", 'nggallery'));
        }
        // Establish some rules on where galleries can go
        $abspath = $storage->get_gallery_abspath($this->object);
        // Galleries should at least be a sub-folder, not directly in WP_CONTENT
        $not_directly_in = array('content' => wp_normalize_path(WP_CONTENT_DIR), 'wordpress root' => $fs->get_document_root());
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $not_directly_in['document root'] = $_SERVER['DOCUMENT_ROOT'];
        }
        foreach ($not_directly_in as $label => $dir) {
            if ($abspath == $dir) {
                $this->object->add_error(sprintf(__("Gallery path must be a sub-directory under the %s directory", 'nggallery'), $label), 'gallerypath');
            }
        }
        $ABSPATH = wp_normalize_path(ABSPATH);
        // Disallow galleries from being under these directories at all
        $not_ever_in = array('plugins' => wp_normalize_path(WP_PLUGIN_DIR), 'must use plugins' => wp_normalize_path(WPMU_PLUGIN_DIR), 'wp-admin' => $fs->join_paths($ABSPATH, 'wp-admin'), 'wp-includes' => $fs->join_paths($ABSPATH, 'wp-includes'), 'themes' => get_theme_root());
        foreach ($not_ever_in as $label => $dir) {
            if (strpos($abspath, $dir) === 0) {
                $this->object->add_error(sprintf(__("Gallery path cannot be under %s directory", 'nggallery'), $label), 'gallerypath');
            }
        }
        // Regardless of where they are just don't let the path end in any of these
        $never_named = array('wp-admin', 'wp-includes', 'wp-content');
        foreach ($never_named as $name) {
            if ($name === end($sections)) {
                $this->object->add_error(sprintf(__("Gallery path cannot end with a directory named %s", 'nggallery'), $name), 'gallerypath');
            }
        }
        unset($storage);
        $this->object->validates_presence_of('title');
        $this->object->validates_presence_of('name');
        $this->object->validates_uniqueness_of('slug');
        $this->object->validates_numericality_of('author');
        return $this->object->is_valid();
    }
}
/**
 * Provides a datamapper for galleries
 * @mixin Mixin_NextGen_Table_Extras
 * @mixin Mixin_Gallery_Mapper
 * @implements I_Gallery_Mapper
 */
class C_Gallery_Mapper extends C_CustomTable_DataMapper_Driver
{
    public static $_instance = NULL;
    /**
     * Define the object
     * @param string|bool $context (optional)
     * @param mixed $not_used Not used, exists only to prevent PHP warnings
     */
    function define($context = FALSE, $not_used = FALSE)
    {
        // Add 'gallery' context
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, 'gallery');
        $this->_primary_key_column = 'gid';
        // Continue defining the object
        parent::define('ngg_gallery', $context);
        $this->set_model_factory_method('gallery');
        $this->add_mixin('Mixin_NextGen_Table_Extras');
        $this->add_mixin('Mixin_Gallery_Mapper');
        $this->implement('I_Gallery_Mapper');
        // Define the columns
        $this->define_column('gid', 'BIGINT', 0);
        $this->define_column('name', 'VARCHAR(255)');
        $this->define_column('slug', 'VARCHAR(255)');
        $this->define_column('path', 'TEXT');
        $this->define_column('title', 'TEXT');
        $this->define_column('pageid', 'INT', 0);
        $this->define_column('previewpic', 'INT', 0);
        $this->define_column('author', 'INT', 0);
        $this->define_column('extras_post_id', 'BIGINT', 0);
    }
    function initialize($object_name = FALSE)
    {
        parent::initialize('ngg_gallery');
    }
    /**
     * Returns a singleton of the gallery mapper
     * @param bool|string $context
     * @return C_Gallery_Mapper
     */
    public static function get_instance($context = False)
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass($context);
        }
        return self::$_instance;
    }
    /**
     * @param string $slug
     * @return C_Gallery|stdClass|null
     */
    public function get_by_slug($slug)
    {
        $sanitized_slug = sanitize_title($slug);
        // Try finding the gallery by slug first; if nothing is found assume that the user passed a gallery id
        $retval = $this->object->select()->where(array('slug = %s', $sanitized_slug))->limit(1)->run_query();
        // NextGen used to turn "This & That" into "this-&amp;-that" when assigning gallery slugs
        if (empty($retval) && strpos($slug, '&') !== FALSE) {
            return $this->get_by_slug(str_replace('&', '&amp;', $slug));
        }
        return reset($retval);
    }
    function set_preview_image($gallery, $image, $only_if_empty = FALSE)
    {
        $retval = FALSE;
        // We need the gallery object
        if (is_numeric($gallery)) {
            $gallery = $this->object->find($gallery);
        }
        // We need the image id
        if (!is_numeric($image)) {
            if (method_exists($image, 'id')) {
                $image = $image->id();
            } else {
                $image = $image->{$image->id_field};
            }
        }
        if ($gallery && $image) {
            if ($only_if_empty && !$gallery->previewpic or !$only_if_empty) {
                $gallery->previewpic = $image;
                $retval = $this->object->save($gallery);
            }
        }
        return $retval;
    }
}
class Mixin_Gallery_Mapper extends Mixin
{
    /**
     * Uses the title property as the post title when the Custom Post driver is used
     * @param object $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->title;
    }
    function _save_entity($entity)
    {
        $storage = C_Gallery_Storage::get_instance();
        // A bug in NGG 2.1.24 allowed galleries to be created with spaces in the directory name, unreplaced by dashes
        // This causes a few problems everywhere, so we here allow users a way to fix those galleries by just re-saving
        if (FALSE !== strpos($entity->path, ' ')) {
            $abspath = $storage->get_gallery_abspath($entity->{$entity->id_field});
            $pre_path = $entity->path;
            $entity->path = str_replace(' ', '-', $entity->path);
            $new_abspath = str_replace($pre_path, $entity->path, $abspath);
            // Begin adding -1, -2, etc until we have a safe target: rename() will overwrite existing directories
            if (@file_exists($new_abspath)) {
                $max_count = 100;
                $count = 0;
                $corrected_abspath = $new_abspath;
                while (@file_exists($corrected_abspath) && $count <= $max_count) {
                    $count++;
                    $corrected_abspath = $new_abspath . '-' . $count;
                }
                $new_abspath = $corrected_abspath;
                $entity->path = $entity->path . '-' . $count;
            }
            @rename($abspath, $new_abspath);
        }
        $slug = $entity->slug;
        $entity->slug = str_replace(' ', '-', $entity->slug);
        $entity->slug = sanitize_title($entity->slug);
        if ($slug != $entity->slug) {
            $entity->slug = nggdb::get_unique_slug($entity->slug, 'gallery');
        }
        $retval = $this->call_parent('_save_entity', $entity);
        if ($retval) {
            $path = $storage->get_gallery_abspath($entity);
            if (!file_exists($path)) {
                wp_mkdir_p($path);
                do_action('ngg_created_new_gallery', $entity->{$entity->id_field});
            }
            C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');
        }
        return $retval;
    }
    function destroy($gallery, $with_dependencies = FALSE)
    {
        $retval = FALSE;
        if ($gallery) {
            if (is_numeric($gallery)) {
                $gallery_id = $gallery;
                $gallery = $this->object->find($gallery_id);
            } else {
                $gallery_id = $gallery->{$gallery->id_field};
            }
            // TODO: Look into making this operation more efficient
            if ($with_dependencies) {
                $image_mapper = C_Image_Mapper::get_instance();
                // Delete the image files from the filesystem
                $settings = C_NextGen_Settings::get_instance();
                if ($settings->deleteImg) {
                    $storage = C_Gallery_Storage::get_instance();
                    $storage->delete_gallery($gallery);
                }
                // Delete the image records from the DB
                foreach ($image_mapper->find_all_for_gallery($gallery_id) as $image) {
                    $image_mapper->destroy($image);
                }
                $image_key = $image_mapper->get_primary_key_column();
                $image_table = $image_mapper->get_table_name();
                // Delete tag associations no longer needed. The following SQL statement
                // deletes all tag associates for images that no longer exist
                global $wpdb;
                $wpdb->query("\n\t\t\t\t\tDELETE wptr.* FROM {$wpdb->term_relationships} wptr\n\t\t\t\t\tINNER JOIN {$wpdb->term_taxonomy} wptt\n\t\t\t\t\tON wptt.term_taxonomy_id = wptr.term_taxonomy_id\n\t\t\t\t\tWHERE wptt.term_taxonomy_id = wptr.term_taxonomy_id\n\t\t\t\t\tAND wptt.taxonomy = 'ngg_tag'\n\t\t\t\t\tAND wptr.object_id NOT IN (SELECT {$image_key} FROM {$image_table})");
            }
            $retval = $this->call_parent('destroy', $gallery);
            if ($retval) {
                do_action('ngg_delete_gallery', $gallery);
                C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');
            }
        }
        return $retval;
    }
    /**
     * Sets default values for the gallery
     * @param object $entity
     */
    function set_defaults($entity)
    {
        // If author is missing, then set to the current user id
        // TODO: Using wordpress function. Should use abstraction
        $this->object->_set_default_value($entity, 'author', get_current_user_id());
        if (!is_admin()) {
            if (!empty($entity->title)) {
                $entity->title = M_I18N::translate($entity->title, 'gallery_' . $entity->{$entity->id_field} . '_name');
            }
            if (!empty($entity->galdesc)) {
                $entity->galdesc = M_I18N::translate($entity->galdesc, 'gallery_' . $entity->{$entity->id_field} . '_description');
            }
        }
    }
}
class E_UploadException extends E_NggErrorException
{
    function __construct($message = '', $code = NULL, $previous = NULL)
    {
        if (!$message) {
            $message = "There was a problem uploading the file.";
        }
        if (PHP_VERSION_ID >= 50300) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }
}
class E_InsufficientWriteAccessException extends E_NggErrorException
{
    function __construct($message = FALSE, $filename = NULL, $code = NULL, $previous = NULL)
    {
        if (!$message) {
            $message = "Could not write to file. Please check filesystem permissions.";
        }
        if ($filename) {
            $message .= " Filename: {$filename}";
        }
        if (PHP_VERSION_ID >= 50300) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }
}
class E_NoSpaceAvailableException extends E_NggErrorException
{
    function __construct($message = '', $code = NULL, $previous = NULL)
    {
        if (!$message) {
            $message = "You have exceeded your storage capacity. Please remove some files and try again.";
        }
        if (PHP_VERSION_ID >= 50300) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }
}
class E_No_Image_Library_Exception extends E_NggErrorException
{
    function __construct($message = '', $code = NULL, $previous = NULL)
    {
        if (!$message) {
            $message = "The site does not support the GD Image library. Please ask your hosting provider to enable it.";
        }
        if (PHP_VERSION_ID >= 50300) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }
}
/**
 * Class C_Gallery_Storage
 *
 * @implements I_Gallery_Storage
 * @mixin Mixin_GalleryStorage_Base_Dynamic
 * @mixin Mixin_GalleryStorage_Base_Getters
 * @mixin Mixin_GalleryStorage_Base_Management
 * @mixin Mixin_GalleryStorage_Base_Upload
 */
class C_Gallery_Storage extends C_Component
{
    public static $_instances = array();
    static $gallery_abspath_cache = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_GalleryStorage_Base_Dynamic');
        $this->add_mixin('Mixin_GalleryStorage_Base_Getters');
        $this->add_mixin('Mixin_GalleryStorage_Base_Management');
        $this->add_mixin('Mixin_GalleryStorage_Base_Upload');
        $this->implement('I_Gallery_Storage');
        $this->implement('I_GalleryStorage_Driver');
        // backwards compatibility
    }
    /**
     * Provides some aliases to defined methods; thanks to this a call to C_Gallery_Storage->get_thumb_url() is
     * translated to C_Gallery_Storage->get_image_url('thumb').
     * TODO: Remove this 'magic' method so that our code is always understandable without needing deep context
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    function __call($method, $args)
    {
        if (preg_match("/^get_(\\w+)_(abspath|url|dimensions|html|size_params)\$/", $method, $match)) {
            if (isset($match[1]) && isset($match[2]) && !$this->has_method($method)) {
                $method = 'get_image_' . $match[2];
                $args[] = $match[1];
                return parent::__call($method, $args);
            }
        }
        return parent::__call($method, $args);
    }
    /**
     * For compatibility reasons, we include this method. This used to be used to get the underlying storage driver.
     * Necessary for Imagify integration
     */
    function &get_wrapped_instance()
    {
        return $this;
    }
    function initialize()
    {
        parent::initialize();
        $this->_gallery_mapper = C_Gallery_Mapper::get_instance();
        $this->_image_mapper = C_Image_Mapper::get_instance();
    }
    /**
     * @param bool|string $context
     * @return C_Gallery_Storage
     */
    static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Gallery_Storage($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Gets the id of a gallery, regardless of whether an integer
     * or object was passed as an argument
     * @param mixed $gallery_obj_or_id
     * @return null|int
     */
    function _get_gallery_id($gallery_obj_or_id)
    {
        $retval = NULL;
        $gallery_key = $this->object->_gallery_mapper->get_primary_key_column();
        if (is_object($gallery_obj_or_id)) {
            if (isset($gallery_obj_or_id->{$gallery_key})) {
                $retval = $gallery_obj_or_id->{$gallery_key};
            }
        } elseif (is_numeric($gallery_obj_or_id)) {
            $retval = $gallery_obj_or_id;
        }
        return $retval;
    }
    /**
     * Outputs/renders an image
     * @param int|stdClass|C_Image $image
     * @return bool
     */
    function render_image($image, $size = FALSE)
    {
        $format_list = $this->object->get_image_format_list();
        $abspath = $this->object->get_image_abspath($image, $size, true);
        if ($abspath == null) {
            $thumbnail = $this->object->generate_image_size($image, $size);
            if ($thumbnail != null) {
                $abspath = $thumbnail->fileName;
                $thumbnail->destruct();
            }
        }
        if ($abspath != null) {
            $data = @getimagesize($abspath);
            $format = 'jpg';
            if ($data != null && is_array($data) && isset($format_list[$data[2]])) {
                $format = $format_list[$data[2]];
            }
            // Clear output
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            $format = strtolower($format);
            // output image and headers
            header('Content-type: image/' . $format);
            readfile($abspath);
            return true;
        }
        return false;
    }
    /**
     * Sets a NGG image as a post thumbnail for the given post
     *
     * @param int $postId
     * @param int|C_Image|stdClass $image
     * @param bool $only_create_attachment
     * @return int
     */
    function set_post_thumbnail($postId, $image, $only_create_attachment = FALSE)
    {
        $retval = FALSE;
        // Get the post ID
        if (is_object($postId)) {
            $post = $postId;
            $postId = isset($post->ID) ? $post->ID : $post->post_id;
        }
        // Get the image
        if (is_int($image)) {
            $imageId = $image;
            $mapper = C_Image_Mapper::get_instance();
            $image = $mapper->find($imageId);
        }
        if ($image && $postId) {
            $attachment_id = $this->object->is_in_media_library($image->pid);
            if ($attachment_id === FALSE) {
                $attachment_id = $this->object->copy_to_media_library($image);
            }
            if ($attachment_id) {
                if (!$only_create_attachment) {
                    set_post_thumbnail($postId, $attachment_id);
                }
                $retval = $attachment_id;
            }
        }
        return $retval;
    }
    function convert_slashes($path)
    {
        $search = array('/', "\\");
        $replace = array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        return str_replace($search, $replace, $path);
    }
    /**
     * Empties the gallery cache directory of content
     * @param object $gallery
     */
    function flush_cache($gallery)
    {
        $cache = C_Cache::get_instance();
        $cache->flush_directory($this->object->get_cache_abspath($gallery));
    }
    /**
     * Sanitizes a directory path, replacing whitespace with dashes.
     *
     * Taken from WP' sanitize_file_name() and modified to not act on file extensions.
     *
     * Removes special characters that are illegal in filenames on certain
     * operating systems and special characters requiring special escaping
     * to manipulate at the command line. Replaces spaces and consecutive
     * dashes with a single dash. Trims period, dash and underscore from beginning
     * and end of filename. It is not guaranteed that this function will return a
     * filename that is allowed to be uploaded.
     * @param string $dirname The directory name to be sanitized
     * @return string The sanitized directory name
     */
    public function sanitize_directory_name($dirname)
    {
        $dirname_raw = $dirname;
        $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "\$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", "%", "+", chr(0));
        $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $dirname_raw);
        $dirname = preg_replace("#\\x{00a0}#siu", ' ', $dirname);
        $dirname = str_replace($special_chars, '', $dirname);
        $dirname = str_replace(array('%20', '+'), '-', $dirname);
        $dirname = preg_replace('/[\\r\\n\\t -]+/', '-', $dirname);
        $dirname = trim($dirname, '.-_');
        return $dirname;
    }
    /**
     * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
     * @param object|int $image Image ID or an image object
     * @param string $size
     * @param array $params
     * @param bool $skip_defaults
     * @return bool|array
     */
    function calculate_image_size_dimensions($image, $size, $params = null, $skip_defaults = false)
    {
        $retval = FALSE;
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $params = $this->object->get_image_size_params($image, $size, $params, $skip_defaults);
            // Get the image filename
            $image_path = $this->object->get_original_abspath($image, 'original');
            $clone_path = $this->object->get_image_abspath($image, $size);
            $retval = $this->object->calculate_image_clone_dimensions($image_path, $clone_path, $params);
        }
        return $retval;
    }
    /**
     * Generates a "clone" for an existing image, the clone can be altered using the $params array
     * @param string $image_path
     * @param string $clone_path
     * @param array $params
     * @return null|object
     */
    function generate_image_clone($image_path, $clone_path, $params)
    {
        $crop = isset($params['crop']) ? $params['crop'] : NULL;
        $watermark = isset($params['watermark']) ? $params['watermark'] : NULL;
        $reflection = isset($params['reflection']) ? $params['reflection'] : NULL;
        $rotation = isset($params['rotation']) ? $params['rotation'] : NULL;
        $flip = isset($params['flip']) ? $params['flip'] : '';
        $destpath = NULL;
        $thumbnail = NULL;
        $result = $this->object->calculate_image_clone_result($image_path, $clone_path, $params);
        // XXX this should maybe be removed and extra settings go into $params?
        $settings = apply_filters('ngg_settings_during_image_generation', C_NextGen_Settings::get_instance()->to_array());
        // Ensure we have a valid image
        if ($image_path && @file_exists($image_path) && $result != null && !isset($result['error'])) {
            $image_dir = dirname($image_path);
            $clone_path = $result['clone_path'];
            $clone_dir = $result['clone_directory'];
            $clone_format = $result['clone_format'];
            $format_list = $this->object->get_image_format_list();
            // Ensure target directory exists, but only create 1 subdirectory
            if (!@file_exists($clone_dir)) {
                if (strtolower(realpath($image_dir)) != strtolower(realpath($clone_dir))) {
                    if (strtolower(realpath($image_dir)) == strtolower(realpath(dirname($clone_dir)))) {
                        wp_mkdir_p($clone_dir);
                    }
                }
            }
            $method = $result['method'];
            $width = $result['width'];
            $height = $result['height'];
            $quality = $result['quality'];
            if ($quality == null) {
                $quality = 100;
            }
            if ($method == 'wordpress') {
                $original = wp_get_image_editor($image_path);
                $destpath = $clone_path;
                if (!is_wp_error($original)) {
                    $original->resize($width, $height, $crop);
                    $original->set_quality($quality);
                    $original->save($clone_path);
                }
            } else {
                if ($method == 'nextgen') {
                    $destpath = $clone_path;
                    $thumbnail = new C_NggLegacy_Thumbnail($image_path, true);
                    if (!$thumbnail->error) {
                        if ($crop) {
                            $crop_area = $result['crop_area'];
                            $crop_x = $crop_area['x'];
                            $crop_y = $crop_area['y'];
                            $crop_width = $crop_area['width'];
                            $crop_height = $crop_area['height'];
                            $thumbnail->crop($crop_x, $crop_y, $crop_width, $crop_height);
                        }
                        $thumbnail->resize($width, $height);
                    } else {
                        $thumbnail = NULL;
                    }
                }
            }
            // We successfully generated the thumbnail
            if (is_string($destpath) && (@file_exists($destpath) || $thumbnail != null)) {
                if ($clone_format != null) {
                    if (isset($format_list[$clone_format])) {
                        $clone_format_extension = $format_list[$clone_format];
                        $clone_format_extension_str = null;
                        if ($clone_format_extension != null) {
                            $clone_format_extension_str = '.' . $clone_format_extension;
                        }
                        $destpath_info = M_I18n::mb_pathinfo($destpath);
                        $destpath_extension = $destpath_info['extension'];
                        if (strtolower($destpath_extension) != strtolower($clone_format_extension)) {
                            $destpath_dir = $destpath_info['dirname'];
                            $destpath_basename = $destpath_info['filename'];
                            $destpath_new = $destpath_dir . DIRECTORY_SEPARATOR . $destpath_basename . $clone_format_extension_str;
                            if (@file_exists($destpath) && rename($destpath, $destpath_new) || $thumbnail != null) {
                                $destpath = $destpath_new;
                            }
                        }
                    }
                }
                if (is_null($thumbnail)) {
                    $thumbnail = new C_NggLegacy_Thumbnail($destpath, true);
                    if ($thumbnail->error) {
                        $thumbnail = null;
                        return null;
                    }
                } else {
                    $thumbnail->fileName = $destpath;
                }
                // This is quite odd, when watermark equals int(0) it seems all statements below ($watermark == 'image') and ($watermark == 'text') both evaluate as true
                // so we set it at null if it evaluates to any null-like value
                if ($watermark == null) {
                    $watermark = null;
                }
                if ($watermark == 1 || $watermark === true) {
                    $watermark_setting_keys = array('wmFont', 'wmType', 'wmPos', 'wmXpos', 'wmYpos', 'wmPath', 'wmText', 'wmOpaque', 'wmFont', 'wmSize', 'wmColor');
                    foreach ($watermark_setting_keys as $watermark_key) {
                        if (!isset($params[$watermark_key])) {
                            $params[$watermark_key] = $settings[$watermark_key];
                        }
                    }
                    if (in_array(strval($params['wmType']), array('image', 'text'))) {
                        $watermark = $params['wmType'];
                    } else {
                        $watermark = 'text';
                    }
                }
                $watermark = strval($watermark);
                if ($watermark == 'image') {
                    $thumbnail->watermarkImgPath = $params['wmPath'];
                    $thumbnail->watermarkImage($params['wmPos'], $params['wmXpos'], $params['wmYpos']);
                } else {
                    if ($watermark == 'text') {
                        $thumbnail->watermarkText = $params['wmText'];
                        $thumbnail->watermarkCreateText($params['wmColor'], $params['wmFont'], $params['wmSize'], $params['wmOpaque']);
                        $thumbnail->watermarkImage($params['wmPos'], $params['wmXpos'], $params['wmYpos']);
                    }
                }
                if ($rotation && in_array(abs($rotation), array(90, 180, 270))) {
                    $thumbnail->rotateImageAngle($rotation);
                }
                $flip = strtolower($flip);
                if ($flip && in_array($flip, array('h', 'v', 'hv'))) {
                    $flip_h = in_array($flip, array('h', 'hv'));
                    $flip_v = in_array($flip, array('v', 'hv'));
                    $thumbnail->flipImage($flip_h, $flip_v);
                }
                if ($reflection) {
                    $thumbnail->createReflection(40, 40, 50, FALSE, '#a4a4a4');
                }
                // Force format
                if ($clone_format != null && isset($format_list[$clone_format])) {
                    $thumbnail->format = strtoupper($format_list[$clone_format]);
                }
                $thumbnail = apply_filters('ngg_before_save_thumbnail', $thumbnail);
                // Always retrieve metadata from the backup when possible
                $backup_path = $image_path . '_backup';
                $exif_abspath = @file_exists($backup_path) ? $backup_path : $image_path;
                $exif_iptc = @C_Exif_Writer::read_metadata($exif_abspath);
                $thumbnail->save($destpath, $quality);
                @C_Exif_Writer::write_metadata($destpath, $exif_iptc);
            }
        }
        return $thumbnail;
    }
    /**
     * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
     * @param string $image_path
     * @param string $clone_path
     * @param array $params
     * @return null|array
     */
    function calculate_image_clone_dimensions($image_path, $clone_path, $params)
    {
        $retval = null;
        $result = $this->object->calculate_image_clone_result($image_path, $clone_path, $params);
        if ($result != null) {
            $retval = array('width' => $result['width'], 'height' => $result['height'], 'real_width' => $result['real_width'], 'real_height' => $result['real_height']);
        }
        return $retval;
    }
    /**
     * Returns an array of properties of a resulting clone image if and when generated
     * @param string $image_path
     * @param string $clone_path
     * @param array $params
     * @return null|array
     */
    function calculate_image_clone_result($image_path, $clone_path, $params)
    {
        $width = isset($params['width']) ? $params['width'] : NULL;
        $height = isset($params['height']) ? $params['height'] : NULL;
        $quality = isset($params['quality']) ? $params['quality'] : NULL;
        $type = isset($params['type']) ? $params['type'] : NULL;
        $crop = isset($params['crop']) ? $params['crop'] : NULL;
        $watermark = isset($params['watermark']) ? $params['watermark'] : NULL;
        $rotation = isset($params['rotation']) ? $params['rotation'] : NULL;
        $reflection = isset($params['reflection']) ? $params['reflection'] : NULL;
        $crop_frame = isset($params['crop_frame']) ? $params['crop_frame'] : NULL;
        $result = NULL;
        // Ensure we have a valid image
        if ($image_path && @file_exists($image_path)) {
            // Ensure target directory exists, but only create 1 subdirectory
            $image_dir = dirname($image_path);
            $clone_dir = dirname($clone_path);
            $image_extension = M_I18n::mb_pathinfo($image_path, PATHINFO_EXTENSION);
            $image_extension_str = null;
            $clone_extension = M_I18n::mb_pathinfo($clone_path, PATHINFO_EXTENSION);
            $clone_extension_str = null;
            if ($image_extension != null) {
                $image_extension_str = '.' . $image_extension;
            }
            if ($clone_extension != null) {
                $clone_extension_str = '.' . $clone_extension;
            }
            $image_basename = M_I18n::mb_basename($image_path);
            $clone_basename = M_I18n::mb_basename($clone_path);
            // We use a default suffix as passing in null as the suffix will make WordPress use a default
            $clone_suffix = null;
            $format_list = $this->object->get_image_format_list();
            $clone_format = null;
            // format is determined below and based on $type otherwise left to null
            // suffix is only used to reconstruct paths for image_resize function
            if (strpos($clone_basename, $image_basename) === 0) {
                $clone_suffix = substr($clone_basename, strlen($image_basename));
            }
            if ($clone_suffix != null && $clone_suffix[0] == '-') {
                // WordPress adds '-' on its own
                $clone_suffix = substr($clone_suffix, 1);
            }
            // Get original image dimensions
            $dimensions = getimagesize($image_path);
            if ($width == null && $height == null) {
                if ($dimensions != null) {
                    if ($width == null) {
                        $width = $dimensions[0];
                    }
                    if ($height == null) {
                        $height = $dimensions[1];
                    }
                } else {
                    // XXX Don't think there's any other option here but to fail miserably...use some hard-coded defaults maybe?
                    return null;
                }
            }
            if ($dimensions != null) {
                $dimensions_ratio = $dimensions[0] / $dimensions[1];
                if ($width == null) {
                    $width = (int) round($height * $dimensions_ratio);
                    if ($width == $dimensions[0] - 1) {
                        $width = $dimensions[0];
                    }
                } else {
                    if ($height == null) {
                        $height = (int) round($width / $dimensions_ratio);
                        if ($height == $dimensions[1] - 1) {
                            $height = $dimensions[1];
                        }
                    }
                }
                if ($width > $dimensions[0]) {
                    $width = $dimensions[0];
                }
                if ($height > $dimensions[1]) {
                    $height = $dimensions[1];
                }
                $image_format = $dimensions[2];
                if ($type != null) {
                    if (is_string($type)) {
                        $type = strtolower($type);
                        // Indexes in the $format_list array correspond to IMAGETYPE_XXX values appropriately
                        if (($index = array_search($type, $format_list)) !== false) {
                            $type = $index;
                            if ($type != $image_format) {
                                // Note: this only changes the FORMAT of the image but not the extension
                                $clone_format = $type;
                            }
                        }
                    }
                }
            }
            if ($width == null || $height == null) {
                // Something went wrong...
                return null;
            }
            // We now need to estimate the 'quality' or level of compression applied to the original JPEG: *IF* the
            // original image has a quality lower than the $quality parameter we will end up generating a new image
            // that is MUCH larger than the original. 'Quality' as an EXIF or IPTC property is quite unreliable
            // and not all software honors or treats it the same way. This calculation is simple: just compare the size
            // that our image could become to what it currently is. '3' is important here as JPEG uses 3 bytes per pixel.
            //
            // First we attempt to use ImageMagick if we can; it has a more robust method of calculation.
            if (!empty($dimensions['mime']) && $dimensions['mime'] == 'image/jpeg') {
                $possible_quality = NULL;
                $try_image_magick = TRUE;
                if (defined('NGG_DISABLE_IMAGICK') && NGG_DISABLE_IMAGICK || function_exists('is_wpe') && ($dimensions[0] >= 8000 || $dimensions[1] >= 8000)) {
                    $try_image_magick = FALSE;
                }
                if ($try_image_magick && extension_loaded('imagick') && class_exists('Imagick')) {
                    $img = new Imagick($image_path);
                    if (method_exists($img, 'getImageCompressionQuality')) {
                        $possible_quality = $img->getImageCompressionQuality();
                    }
                }
                // ImageMagick wasn't available so we guess it from the dimensions and filesize
                if ($possible_quality === NULL) {
                    $filesize = filesize($image_path);
                    $possible_quality = 101 - $width * $height * 3 / $filesize;
                }
                if ($possible_quality !== NULL && $possible_quality < $quality) {
                    $quality = $possible_quality;
                }
            }
            $result['clone_path'] = $clone_path;
            $result['clone_directory'] = $clone_dir;
            $result['clone_suffix'] = $clone_suffix;
            $result['clone_format'] = $clone_format;
            $result['base_width'] = $dimensions[0];
            $result['base_height'] = $dimensions[1];
            // image_resize() has limitations:
            // - no easy crop frame support
            // - fails if the dimensions are unchanged
            // - doesn't support filename prefix, only suffix so names like thumbs_original_name.jpg for $clone_path are not supported
            //   also suffix cannot be null as that will make WordPress use a default suffix...we could use an object that returns empty string from __toString() but for now just fallback to ngg generator
            if (FALSE) {
                // disabling the WordPress method for Iteration #6
                //			if (($crop_frame == null || !$crop) && ($dimensions[0] != $width && $dimensions[1] != $height) && $clone_suffix != null)
                $result['method'] = 'wordpress';
                $new_dims = image_resize_dimensions($dimensions[0], $dimensions[1], $width, $height, $crop);
                if ($new_dims) {
                    list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $new_dims;
                    $width = $dst_w;
                    $height = $dst_h;
                } else {
                    $result['error'] = new WP_Error('error_getting_dimensions', __('Could not calculate resized image dimensions'));
                }
            } else {
                $result['method'] = 'nextgen';
                $original_width = $dimensions[0];
                $original_height = $dimensions[1];
                $aspect_ratio = $width / $height;
                $orig_ratio_x = $original_width / $width;
                $orig_ratio_y = $original_height / $height;
                if ($crop) {
                    $algo = 'shrink';
                    // either 'adapt' or 'shrink'
                    if ($crop_frame != null) {
                        $crop_x = (int) round($crop_frame['x']);
                        $crop_y = (int) round($crop_frame['y']);
                        $crop_width = (int) round($crop_frame['width']);
                        $crop_height = (int) round($crop_frame['height']);
                        $crop_final_width = (int) round($crop_frame['final_width']);
                        $crop_final_height = (int) round($crop_frame['final_height']);
                        $crop_width_orig = $crop_width;
                        $crop_height_orig = $crop_height;
                        $crop_factor_x = $crop_width / $crop_final_width;
                        $crop_factor_y = $crop_height / $crop_final_height;
                        $crop_ratio_x = $crop_width / $width;
                        $crop_ratio_y = $crop_height / $height;
                        if ($algo == 'adapt') {
                            // XXX not sure about this...don't use for now
                            #							$crop_width = (int) round($width * $crop_factor_x);
                            #							$crop_height = (int) round($height * $crop_factor_y);
                        } else {
                            if ($algo == 'shrink') {
                                if ($crop_ratio_x < $crop_ratio_y) {
                                    $crop_width = max($crop_width, $width);
                                    $crop_height = (int) round($crop_width / $aspect_ratio);
                                } else {
                                    $crop_height = max($crop_height, $height);
                                    $crop_width = (int) round($crop_height * $aspect_ratio);
                                }
                                if ($crop_width == $crop_width_orig - 1) {
                                    $crop_width = $crop_width_orig;
                                }
                                if ($crop_height == $crop_height_orig - 1) {
                                    $crop_height = $crop_height_orig;
                                }
                            }
                        }
                        $crop_diff_x = (int) round(($crop_width_orig - $crop_width) / 2);
                        $crop_diff_y = (int) round(($crop_height_orig - $crop_height) / 2);
                        $crop_x += $crop_diff_x;
                        $crop_y += $crop_diff_y;
                        $crop_max_x = $crop_x + $crop_width;
                        $crop_max_y = $crop_y + $crop_height;
                        // Check if we're overflowing borders
                        //
                        if ($crop_x < 0) {
                            $crop_x = 0;
                        } else {
                            if ($crop_max_x > $original_width) {
                                $crop_x -= $crop_max_x - $original_width;
                            }
                        }
                        if ($crop_y < 0) {
                            $crop_y = 0;
                        } else {
                            if ($crop_max_y > $original_height) {
                                $crop_y -= $crop_max_y - $original_height;
                            }
                        }
                    } else {
                        if ($orig_ratio_x < $orig_ratio_y) {
                            $crop_width = $original_width;
                            $crop_height = (int) round($height * $orig_ratio_x);
                        } else {
                            $crop_height = $original_height;
                            $crop_width = (int) round($width * $orig_ratio_y);
                        }
                        if ($crop_width == $width - 1) {
                            $crop_width = $width;
                        }
                        if ($crop_height == $height - 1) {
                            $crop_height = $height;
                        }
                        $crop_x = (int) round(($original_width - $crop_width) / 2);
                        $crop_y = (int) round(($original_height - $crop_height) / 2);
                    }
                    $result['crop_area'] = array('x' => $crop_x, 'y' => $crop_y, 'width' => $crop_width, 'height' => $crop_height);
                } else {
                    // Just constraint dimensions to ensure there's no stretching or deformations
                    list($width, $height) = wp_constrain_dimensions($original_width, $original_height, $width, $height);
                }
            }
            $result['width'] = $width;
            $result['height'] = $height;
            $result['quality'] = $quality;
            $real_width = $width;
            $real_height = $height;
            if ($rotation && in_array(abs($rotation), array(90, 270))) {
                $real_width = $height;
                $real_height = $width;
            }
            if ($reflection) {
                // default for nextgen was 40%, this is used in generate_image_clone as well
                $reflection_amount = 40;
                // Note, round() would probably be best here but using the same code that C_NggLegacy_Thumbnail uses for compatibility
                $reflection_height = intval($real_height * ($reflection_amount / 100));
                $real_height = $real_height + $reflection_height;
            }
            $result['real_width'] = $real_width;
            $result['real_height'] = $real_height;
        }
        return $result;
    }
    function generate_resized_image($image, $save = TRUE)
    {
        $image_abspath = $this->object->get_image_abspath($image, 'full');
        $generated = $this->object->generate_image_clone($image_abspath, $image_abspath, $this->object->get_image_size_params($image, 'full'));
        if ($generated && $save) {
            $this->object->update_image_dimension_metadata($image, $image_abspath);
        }
        if ($generated) {
            $generated->destruct();
        }
    }
    public function update_image_dimension_metadata($image, $image_abspath)
    {
        // Ensure that fullsize dimensions are added to metadata array
        $dimensions = getimagesize($image_abspath);
        $full_meta = array('width' => $dimensions[0], 'height' => $dimensions[1], 'md5' => $this->object->get_image_checksum($image, 'full'));
        if (!isset($image->meta_data) or is_string($image->meta_data) && strlen($image->meta_data) == 0 or is_bool($image->meta_data)) {
            $image->meta_data = array();
        }
        $image->meta_data = array_merge($image->meta_data, $full_meta);
        $image->meta_data['full'] = $full_meta;
        // Don't forget to append the 'full' entry in meta_data in the db
        $this->object->_image_mapper->save($image);
    }
    /**
     * Most major browsers do not honor the Orientation meta found in EXIF. To prevent display issues we inspect
     * the EXIF data and rotate the image so that the EXIF field is not necessary to display the image correctly.
     * Note: generate_image_clone() will handle the removal of the Orientation tag inside the image EXIF.
     * Note: This only handles single-dimension rotation; at the time this method was written there are no known
     * camera manufacturers that both rotate and flip images.
     * @param $image
     * @param bool $save
     */
    public function correct_exif_rotation($image, $save = TRUE)
    {
        $image_abspath = $this->object->get_image_abspath($image, 'full');
        // This method is necessary
        if (!function_exists('exif_read_data')) {
            return;
        }
        // We only need to continue if the Orientation tag is set
        $exif = @exif_read_data($image_abspath, 'exif');
        if (empty($exif['Orientation']) || $exif['Orientation'] == 1) {
            return;
        }
        $degree = 0;
        if ($exif['Orientation'] == 3) {
            $degree = 180;
        }
        if ($exif['Orientation'] == 6) {
            $degree = 90;
        }
        if ($exif['Orientation'] == 8) {
            $degree = 270;
        }
        $parameters = array('rotation' => $degree);
        $generated = $this->object->generate_image_clone($image_abspath, $image_abspath, $this->object->get_image_size_params($image, 'full', $parameters), $parameters);
        if ($generated && $save) {
            $this->object->update_image_dimension_metadata($image, $image_abspath);
        }
        if ($generated) {
            $generated->destruct();
        }
    }
    /**
     * Flushes the cache we use for path/url calculation for galleries
     */
    function flush_gallery_path_cache($gallery)
    {
        $gallery = is_numeric($gallery) ? $gallery : $gallery->gid;
        unset(self::$gallery_abspath_cache[$gallery]);
    }
    /**
     * Gets the id of an image, regardless of whether an integer
     * or object was passed as an argument
     * @param object|int $image_obj_or_id
     * @return null|int
     */
    function _get_image_id($image_obj_or_id)
    {
        $retval = NULL;
        $image_key = $this->object->_image_mapper->get_primary_key_column();
        if (is_object($image_obj_or_id)) {
            if (isset($image_obj_or_id->{$image_key})) {
                $retval = $image_obj_or_id->{$image_key};
            }
        } elseif (is_numeric($image_obj_or_id)) {
            $retval = $image_obj_or_id;
        }
        return $retval;
    }
    /**
     * Returns the absolute path to the cache directory of a gallery.
     *
     * Without the gallery parameter the legacy (pre 2.0) shared directory is returned.
     *
     * @param int|object|false|C_Gallery $gallery (optional)
     * @return string Absolute path to cache directory
     */
    function get_cache_abspath($gallery = FALSE)
    {
        return path_join($this->object->get_gallery_abspath($gallery), 'cache');
    }
    /**
     * Gets the absolute path where the full-sized image is stored
     * @param int|object $image
     * @return null|string
     */
    function get_full_abspath($image)
    {
        return $this->object->get_image_abspath($image, 'full');
    }
    /**
     * Alias to get_image_dimensions()
     * @param int|object $image
     * @return array
     */
    function get_full_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'full');
    }
    /**
     * Alias to get_image_html()
     * @param int|object $image
     * @return string
     */
    function get_full_html($image)
    {
        return $this->object->get_image_html($image, 'full');
    }
    /**
     * Alias for get_original_url()
     *
     * @param int|stdClass|C_Image $image
     * @return string
     */
    function get_full_url($image)
    {
        return $this->object->get_image_url($image, 'full');
    }
    function get_gallery_root()
    {
        return wp_normalize_path(C_Fs::get_instance()->get_document_root('galleries'));
    }
    function _get_computed_gallery_abspath($gallery)
    {
        $retval = NULL;
        $gallery_root = $this->get_gallery_root();
        // Get the gallery entity from the database
        if ($gallery) {
            if (is_numeric($gallery)) {
                $gallery = $this->object->_gallery_mapper->find($gallery);
            }
        }
        // It just doesn't exist
        if (!$gallery) {
            return $retval;
        }
        // We we have a gallery, determine it's path
        if ($gallery) {
            if (isset($gallery->path)) {
                $retval = $gallery->path;
            } elseif (isset($gallery->slug)) {
                $basepath = wp_normalize_path(C_NextGen_Settings::get_instance()->gallerypath);
                $retval = path_join($basepath, $this->object->sanitize_directory_name(sanitize_title($gallery->slug)));
            }
            // Normalize the gallery path. If the gallery path starts with /wp-content, and
            // NGG_GALLERY_ROOT_TYPE is set to 'content', then we need to strip out the /wp-content
            // from the start of the gallery path
            if (NGG_GALLERY_ROOT_TYPE === 'content') {
                $retval = preg_replace("#^/?wp-content#", "", $retval);
            }
            // Ensure that the path is absolute
            if (strpos($retval, $gallery_root) !== 0) {
                // path_join() behaves funny - if the second argument starts with a slash,
                // it won't join the two paths together
                $retval = preg_replace("#^/#", "", $retval);
                $retval = path_join($gallery_root, $retval);
            }
            $retval = wp_normalize_path($retval);
        }
        return $retval;
    }
    /**
     * Get the abspath to the gallery folder for the given gallery
     * The gallery may or may not already be persisted
     * @param int|object|C_Gallery $gallery
     * @return string
     */
    function get_gallery_abspath($gallery)
    {
        $gallery_id = is_numeric($gallery) ? $gallery : (is_object($gallery) && isset($gallery->gid) ? $gallery->gid : NULL);
        if (!$gallery_id || !isset(self::$gallery_abspath_cache[$gallery_id])) {
            self::$gallery_abspath_cache[$gallery_id] = $this->object->_get_computed_gallery_abspath($gallery);
        }
        return self::$gallery_abspath_cache[$gallery_id];
    }
    function get_gallery_relpath($gallery)
    {
        // Special hack for home.pl: their document root is just '/'
        $root = $this->object->get_gallery_root();
        if ($root === '/') {
            return $this->get_gallery_abspath($gallery);
        }
        return str_replace($this->object->get_gallery_root(), '', $this->get_gallery_abspath($gallery));
    }
    /**
     * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
     * @param int|object $image
     * @param string $size (optional) Default = full
     * @return string
     */
    function _get_computed_image_abspath($image, $size = 'full', $check_existance = FALSE)
    {
        $retval = NULL;
        // If we have the id, get the actual image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have the image entity - user could have passed in an incorrect id
        if (is_object($image)) {
            if ($gallery_path = $this->object->get_gallery_abspath($image->galleryid)) {
                $folder = $prefix = $size;
                switch ($size) {
                    # Images are stored in the associated gallery folder
                    case 'full':
                        $retval = path_join($gallery_path, $image->filename);
                        break;
                    case 'backup':
                        $retval = path_join($gallery_path, $image->filename . '_backup');
                        if (!@file_exists($retval)) {
                            $retval = path_join($gallery_path, $image->filename);
                        }
                        break;
                    case 'thumbnail':
                        $size = 'thumbnail';
                        $folder = 'thumbs';
                        $prefix = 'thumbs';
                    // deliberately no break here
                    default:
                        // NGG 2.0 stores relative filenames in the meta data of
                        // an image. It does this because it uses filenames
                        // that follow conventional WordPress naming scheme.
                        $image_path = NULL;
                        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                        if (isset($image->meta_data) && isset($image->meta_data[$size]) && isset($image->meta_data[$size]['filename'])) {
                            if ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                                $image_path = path_join($this->object->get_cache_abspath($image->galleryid), $image->meta_data[$size]['filename']);
                            } else {
                                $image_path = path_join($gallery_path, $folder);
                                $image_path = path_join($image_path, $image->meta_data[$size]['filename']);
                            }
                        } else {
                            if ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                                $params = $dynthumbs->get_params_from_name($size, true);
                                $image_path = path_join($this->object->get_cache_abspath($image->galleryid), $dynthumbs->get_image_name($image, $params));
                                // Filename is not found in meta, nor dynamic
                            } else {
                                $settings = C_NextGen_Settings::get_instance();
                                // This next bit is annoying but necessary for legacy reasons. NextGEN until 3.19 stored thumbnails
                                // with a filename of "thumbs_(whatever.jpg)" which Google indexes as "thumbswhatever.jpg" which is
                                // not good for SEO. From 3.19 on the default setting is "thumbs-" but we must account for legacy
                                // sites.
                                $image_path = path_join($gallery_path, $folder);
                                $new_thumb_path = path_join($image_path, "{$prefix}-{$image->filename}");
                                $old_thumb_path = path_join($image_path, "{$prefix}_{$image->filename}");
                                if ($settings->get('dynamic_image_filename_separator_use_dash', FALSE)) {
                                    // Check for thumbs- first
                                    if (file_exists($new_thumb_path)) {
                                        $image_path = $new_thumb_path;
                                    } elseif (file_exists($old_thumb_path)) {
                                        // Check for thumbs_ as a fallback
                                        $image_path = $old_thumb_path;
                                    } else {
                                        // The thumbnail file does not exist, default to thumbs-
                                        $image_path = $new_thumb_path;
                                    }
                                } else {
                                    // Reversed: the option is disabled so check for thumbs_
                                    if (file_exists($old_thumb_path)) {
                                        $image_path = $old_thumb_path;
                                    } elseif (file_exists($new_thumb_path)) {
                                        // In case the user has switched back and forth, check for thumbs-
                                        $image_path = $new_thumb_path;
                                    } else {
                                        // Default to thumbs_ per the site setting
                                        $image_path = $old_thumb_path;
                                    }
                                }
                            }
                        }
                        $retval = $image_path;
                        break;
                }
            }
        }
        if ($retval && $check_existance && !@file_exists($retval)) {
            $retval = NULL;
        }
        return $retval;
    }
    function get_image_checksum($image, $size = 'full')
    {
        $retval = NULL;
        if ($image_abspath = $this->get_image_abspath($image, $size, TRUE)) {
            $retval = md5_file($image_abspath);
        }
        return $retval;
    }
    /**
     * Gets the dimensions for a particular-sized image
     *
     * @param int|object $image
     * @param string $size
     * @return null|array
     */
    function get_image_dimensions($image, $size = 'full')
    {
        $retval = NULL;
        // If an image id was provided, get the entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $size = $this->normalize_image_size_name($size);
            if (!$size) {
                $size = 'full';
            }
            // Image dimensions are stored in the $image->meta_data
            // property for all implementations
            if (isset($image->meta_data) && isset($image->meta_data[$size])) {
                $retval = $image->meta_data[$size];
            } else {
                $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                $abspath = $this->object->get_image_abspath($image, $size, TRUE);
                if ($abspath) {
                    $dims = @getimagesize($abspath);
                    if ($dims) {
                        $retval['width'] = $dims[0];
                        $retval['height'] = $dims[1];
                    }
                } elseif ($size == 'backup') {
                    $retval = $this->object->get_image_dimensions($image, 'full');
                }
                if (!$retval && $dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                    $new_dims = $this->object->calculate_image_size_dimensions($image, $size);
                    $retval = array('width' => $new_dims['real_width'], 'height' => $new_dims['real_height']);
                }
            }
        }
        return $retval;
    }
    function get_image_format_list()
    {
        $format_list = [IMAGETYPE_GIF => 'gif', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
        return $format_list;
    }
    /**
     * Gets the HTML for an image
     * @param int|object $image
     * @param string $size
     * @param array $attributes (optional)
     * @return string
     */
    function get_image_html($image, $size = 'full', $attributes = array())
    {
        $retval = "";
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            // Set alt text if not already specified
            if (!isset($attributes['alttext'])) {
                $attributes['alt'] = esc_attr($image->alttext);
            }
            // Set the title if not already set
            if (!isset($attributes['title'])) {
                $attributes['title'] = esc_attr($image->alttext);
            }
            // Set the dimensions if not set already
            if (!isset($attributes['width']) or !isset($attributes['height'])) {
                $dimensions = $this->object->get_image_dimensions($image, $size);
                if (!isset($attributes['width'])) {
                    $attributes['width'] = $dimensions['width'];
                }
                if (!isset($attributes['height'])) {
                    $attributes['height'] = $dimensions['height'];
                }
            }
            // Set the url if not already specified
            if (!isset($attributes['src'])) {
                $attributes['src'] = $this->object->get_image_url($image, $size);
            }
            // Format attributes
            $attribs = array();
            foreach ($attributes as $attrib => $value) {
                $attribs[] = "{$attrib}=\"{$value}\"";
            }
            $attribs = implode(" ", $attribs);
            // Return HTML string
            $retval = "<img {$attribs} />";
        }
        return $retval;
    }
    function _get_computed_image_url($image, $size = 'full')
    {
        $retval = NULL;
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        // Get the image abspath
        $image_abspath = $this->object->get_image_abspath($image, $size);
        if ($dynthumbs->is_size_dynamic($size) && !file_exists($image_abspath)) {
            if (defined('NGG_DISABLE_DYNAMIC_IMG_URLS') && constant('NGG_DISABLE_DYNAMIC_IMG_URLS')) {
                $params = array('watermark' => false, 'reflection' => false, 'crop' => true);
                $result = $this->generate_image_size($image, $size, $params);
                if ($result) {
                    $image_abspath = $this->object->get_image_abspath($image, $size);
                }
            } else {
                return NULL;
            }
        }
        // Assuming we have an abspath, we can translate that to a url
        if ($image_abspath) {
            // Replace the gallery root with the proper url segment
            $gallery_root = preg_quote($this->get_gallery_root(), '#');
            $image_uri = preg_replace("#^{$gallery_root}#", "", $image_abspath);
            // Url encode each uri segment
            $segments = explode("/", $image_uri);
            $segments = array_map('rawurlencode', $segments);
            $image_uri = preg_replace("#^/#", "", implode("/", $segments));
            // Join gallery root and image uri
            $gallery_root = trailingslashit(NGG_GALLERY_ROOT_TYPE == 'site' ? site_url() : WP_CONTENT_URL);
            $gallery_root = is_ssl() ? str_replace('http:', 'https:', $gallery_root) : $gallery_root;
            $retval = $gallery_root . $image_uri;
        }
        return $retval;
    }
    function normalize_image_size_name($size = 'full')
    {
        switch ($size) {
            case 'full':
            case 'original':
            case 'image':
            case 'orig':
            case 'resized':
                $size = 'full';
                break;
            case 'thumbnails':
            case 'thumbnail':
            case 'thumb':
            case 'thumbs':
                $size = 'thumbnail';
                break;
        }
        return $size;
    }
    /**
     * Returns the named sizes available for images
     * @return array
     */
    function get_image_sizes($image = FALSE)
    {
        $retval = array('full', 'thumbnail');
        if (is_numeric($image)) {
            $image = C_Image_Mapper::get_instance()->find($image);
        }
        if ($image) {
            if ($image->meta_data) {
                $meta_data = is_object($image->meta_data) ? get_object_vars($image->meta_data) : $image->meta_data;
                foreach ($meta_data as $key => $value) {
                    if (is_array($value) && isset($value['width']) && !in_array($key, $retval)) {
                        $retval[] = $key;
                    }
                }
            }
        }
        return $retval;
    }
    function get_image_size_params($image, $size, $params = array(), $skip_defaults = false)
    {
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        if ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
            $named_params = $dynthumbs->get_params_from_name($size, true);
            if (!$params) {
                $params = array();
            }
            $params = array_merge($params, $named_params);
        }
        $params = apply_filters('ngg_get_image_size_params', $params, $size, $image);
        // Ensure we have a valid image
        if ($image) {
            $settings = C_NextGen_Settings::get_instance();
            if (!$skip_defaults) {
                // Get default settings
                if ($size == 'full') {
                    if (!isset($params['quality'])) {
                        $params['quality'] = $settings->imgQuality;
                    }
                } else {
                    if (!isset($params['crop'])) {
                        $params['crop'] = $settings->thumbfix;
                    }
                    if (!isset($params['quality'])) {
                        $params['quality'] = $settings->thumbquality;
                    }
                }
            }
            // width and height when omitted make generate_image_clone create a clone with original size, so try find defaults regardless of $skip_defaults
            if (!isset($params['width']) || !isset($params['height'])) {
                // First test if this is a "known" image size, i.e. if we store these sizes somewhere when users re-generate these sizes from the UI...this is required to be compatible with legacy
                // try the 2 default built-in sizes, first thumbnail...
                if ($size == 'thumbnail') {
                    if (!isset($params['width'])) {
                        $params['width'] = $settings->thumbwidth;
                    }
                    if (!isset($params['height'])) {
                        $params['height'] = $settings->thumbheight;
                    }
                } else {
                    if ($size == 'full') {
                        if (!isset($params['width'])) {
                            if ($settings->imgAutoResize) {
                                $params['width'] = $settings->imgWidth;
                            }
                        }
                        if (!isset($params['height'])) {
                            if ($settings->imgAutoResize) {
                                $params['height'] = $settings->imgHeight;
                            }
                        }
                    } else {
                        if (isset($image->meta_data) && isset($image->meta_data[$size])) {
                            $dimensions = $image->meta_data[$size];
                            if (!isset($params['width'])) {
                                $params['width'] = $dimensions['width'];
                            }
                            if (!isset($params['height'])) {
                                $params['height'] = $dimensions['height'];
                            }
                        }
                    }
                }
            }
            if (!isset($params['crop_frame'])) {
                $crop_frame_size_name = 'thumbnail';
                if (isset($image->meta_data[$size]['crop_frame'])) {
                    $crop_frame_size_name = $size;
                }
                if (isset($image->meta_data[$crop_frame_size_name]['crop_frame'])) {
                    $params['crop_frame'] = $image->meta_data[$crop_frame_size_name]['crop_frame'];
                    if (!isset($params['crop_frame']['final_width'])) {
                        $params['crop_frame']['final_width'] = $image->meta_data[$crop_frame_size_name]['width'];
                    }
                    if (!isset($params['crop_frame']['final_height'])) {
                        $params['crop_frame']['final_height'] = $image->meta_data[$crop_frame_size_name]['height'];
                    }
                }
            } else {
                if (!isset($params['crop_frame']['final_width'])) {
                    $params['crop_frame']['final_width'] = $params['width'];
                }
                if (!isset($params['crop_frame']['final_height'])) {
                    $params['crop_frame']['final_height'] = $params['height'];
                }
            }
        }
        return $params;
    }
    /**
     * Alias to get_image_dimensions()
     * @param int|object $image
     * @return array
     */
    function get_original_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'full');
    }
    /**
     * Alias to get_image_html()
     * @param int|object $image
     * @return string
     */
    function get_original_html($image)
    {
        return $this->object->get_image_html($image, 'full');
    }
    /**
     * Gets the url to the original-sized image
     * @param int|stdClass|C_Image $image
     * @param bool $check_existance (optional)
     * @return string
     */
    function get_original_url($image, $check_existance = FALSE)
    {
        return $this->object->get_image_url($image, 'full', $check_existance);
    }
    /**
     * @param object|bool $gallery (optional)
     * @return string
     */
    function get_upload_abspath($gallery = FALSE)
    {
        // Base upload path
        $retval = C_NextGen_Settings::get_instance()->gallerypath;
        $fs = C_Fs::get_instance();
        // If a gallery has been specified, then we'll
        // append the slug
        if ($gallery) {
            $retval = $this->get_gallery_abspath($gallery);
        }
        // We need to make this an absolute path
        if (strpos($retval, $fs->get_document_root('gallery')) !== 0) {
            $retval = rtrim($fs->join_paths($fs->get_document_root('gallery'), $retval), "/\\");
        }
        // Convert slashes
        return wp_normalize_path($retval);
    }
    /**
     * Gets the upload path, optionally for a particular gallery
     * @param int|C_Gallery|object|false $gallery (optional)
     * @return string
     */
    function get_upload_relpath($gallery = FALSE)
    {
        $fs = C_Fs::get_instance();
        $retval = str_replace($fs->get_document_root('gallery'), '', $this->object->get_upload_abspath($gallery));
        return '/' . wp_normalize_path(ltrim($retval, "/"));
    }
    /**
     * Set correct file permissions (taken from wp core). Should be called
     * after writing any file
     *
     * @class nggAdmin
     * @param string $filename
     * @return bool $result
     */
    function _chmod($filename = '')
    {
        $stat = @stat(dirname($filename));
        $perms = $stat['mode'] & 0666;
        // Remove execute bits for files
        if (@chmod($filename, $perms)) {
            return TRUE;
        }
        return FALSE;
    }
    function _delete_gallery_directory($abspath)
    {
        // Remove all image files and purge all empty directories left over
        $iterator = new DirectoryIterator($abspath);
        // Only delete image files! Other files may be stored incorrectly but it's not our place to delete them
        $removable_extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
        foreach ($removable_extensions as $extension) {
            $removable_extensions[] = $extension . '_backup';
        }
        foreach ($iterator as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            } elseif ($file->isFile() || $file->isLink()) {
                $extension = strtolower(pathinfo($file->getPathname(), PATHINFO_EXTENSION));
                if (in_array($extension, $removable_extensions, TRUE)) {
                    @unlink($file->getPathname());
                }
            } elseif ($file->isDir()) {
                $this->object->_delete_gallery_directory($file->getPathname());
            }
        }
        // DO NOT remove directories that still have files in them. Note: '.' and '..' are included with getSize()
        $empty = TRUE;
        foreach ($iterator as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            }
            $empty = FALSE;
        }
        if ($empty) {
            @rmdir($iterator->getPath());
        }
    }
    /**
     * @param C_Image[]|int[] $images
     * @param C_Gallery|int $dst_gallery
     * @return int[]
     */
    function copy_images($images, $dst_gallery)
    {
        $retval = array();
        // Ensure that the image ids we have are valid
        $image_mapper = C_Image_Mapper::get_instance();
        foreach ($images as $image) {
            if (is_numeric($image)) {
                $image = $image_mapper->find($image);
            }
            $image_abspath = $this->object->get_image_abspath($image, 'backup') ?: $this->object->get_image_abspath($image);
            if ($image_abspath) {
                // Import the image; this will copy the main file
                $new_image_id = $this->object->import_image_file($dst_gallery, $image_abspath, $image->filename);
                if ($new_image_id) {
                    // Copy the properties of the old image
                    $new_image = $image_mapper->find($new_image_id);
                    foreach (get_object_vars($image) as $key => $value) {
                        if (in_array($key, array('pid', 'galleryid', 'meta_data', 'filename', 'sortorder', 'extras_post_id'))) {
                            continue;
                        }
                        $new_image->{$key} = $value;
                    }
                    $image_mapper->save($new_image);
                    // Copy tags
                    $tags = wp_get_object_terms($image->pid, 'ngg_tag', 'fields=ids');
                    $tags = array_map('intval', $tags);
                    wp_set_object_terms($new_image_id, $tags, 'ngg_tag', true);
                    // Copy all of the generated versions (resized versions, watermarks, etc)
                    foreach ($this->object->get_image_sizes($image) as $named_size) {
                        if (in_array($named_size, array('full', 'thumbnail'))) {
                            continue;
                        }
                        $old_abspath = $this->object->get_image_abspath($image, $named_size);
                        $new_abspath = $this->object->get_image_abspath($new_image, $named_size);
                        if (is_array(@stat($old_abspath))) {
                            $new_dir = dirname($new_abspath);
                            // Ensure the target directory exists
                            if (@stat($new_dir) === FALSE) {
                                wp_mkdir_p($new_dir);
                            }
                            @copy($old_abspath, $new_abspath);
                        }
                    }
                    // Mark as done
                    $retval[] = $new_image_id;
                }
            }
        }
        return $retval;
    }
    /**
     * Moves images from to another gallery
     * @param array $images
     * @param int|object $gallery
     * @return int[]
     */
    function move_images($images, $gallery)
    {
        $retval = $this->object->copy_images($images, $gallery);
        if ($images) {
            foreach ($images as $image_id) {
                $this->object->delete_image($image_id);
            }
        }
        return $retval;
    }
    /**
     * @param string $abspath
     * @return bool
     */
    function delete_directory($abspath)
    {
        $retval = FALSE;
        if (@file_exists($abspath)) {
            $files = scandir($abspath);
            array_shift($files);
            array_shift($files);
            foreach ($files as $file) {
                $file_abspath = implode(DIRECTORY_SEPARATOR, array(rtrim($abspath, "/\\"), $file));
                if (is_dir($file_abspath)) {
                    $this->object->delete_directory($file_abspath);
                } else {
                    unlink($file_abspath);
                }
            }
            rmdir($abspath);
            $retval = @file_exists($abspath);
        }
        return $retval;
    }
    function delete_gallery($gallery)
    {
        $fs = C_Fs::get_instance();
        $safe_dirs = array(DIRECTORY_SEPARATOR, $fs->get_document_root('plugins'), $fs->get_document_root('plugins_mu'), $fs->get_document_root('templates'), $fs->get_document_root('stylesheets'), $fs->get_document_root('content'), $fs->get_document_root('galleries'), $fs->get_document_root());
        $abspath = $this->object->get_gallery_abspath($gallery);
        if ($abspath && file_exists($abspath) && !in_array(stripslashes($abspath), $safe_dirs)) {
            $this->object->_delete_gallery_directory($abspath);
        }
    }
    /**
     * @param int|C_Image $image
     * @param string|FALSE $size
     * @return bool
     */
    function delete_image($image, $size = FALSE)
    {
        $retval = FALSE;
        // Ensure that we have the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            $image_id = $image->{$image->id_field};
            do_action('ngg_delete_image', $image_id, $size);
            // Delete only a particular image size
            if ($size) {
                $abspath = $this->object->get_image_abspath($image, $size);
                if ($abspath && @file_exists($abspath)) {
                    @unlink($abspath);
                }
                if (isset($image->meta_data) && isset($image->meta_data[$size])) {
                    unset($image->meta_data[$size]);
                    $this->object->_image_mapper->save($image);
                }
            } else {
                foreach ($this->object->get_image_sizes($image) as $named_size) {
                    $image_abspath = $this->object->get_image_abspath($image, $named_size);
                    @unlink($image_abspath);
                }
                // Delete the entity
                $this->object->_image_mapper->destroy($image);
            }
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * Recover image from backup copy and reprocess it
     *
     * @param int|stdClass|C_Image $image
     * @return bool|string result code
     */
    function recover_image($image)
    {
        $retval = FALSE;
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            $full_abspath = $this->object->get_image_abspath($image);
            $backup_abspath = $this->object->get_image_abspath($image, 'backup');
            if ($backup_abspath != $full_abspath && @file_exists($backup_abspath)) {
                if (is_writable($full_abspath) && is_writable(dirname($full_abspath))) {
                    // Copy the backup
                    if (@copy($backup_abspath, $full_abspath)) {
                        // Backup images are not altered at all; we must re-correct the EXIF/Orientation tag
                        $this->object->correct_exif_rotation($image, TRUE);
                        // Re-create non-fullsize image sizes
                        foreach ($this->object->get_image_sizes($image) as $named_size) {
                            if (in_array($named_size, array('full', 'backup'))) {
                                continue;
                            }
                            // Reset thumbnail cropping set by 'Edit thumb' dialog
                            if ($named_size === 'thumbnail') {
                                unset($image->meta_data[$named_size]['crop_frame']);
                            }
                            $thumbnail = $this->object->generate_image_clone($full_abspath, $this->object->get_image_abspath($image, $named_size), $this->object->get_image_size_params($image, $named_size));
                            if ($thumbnail) {
                                $thumbnail->destruct();
                            }
                        }
                        do_action('ngg_recovered_image', $image);
                        // Reimport all metadata
                        $retval = $this->object->_image_mapper->reimport_metadata($image);
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Copies a NGG image to the media library and returns the attachment_id
     *
     * @param C_Image|int|stdClass $image
     * @return FALSE|int attachment_id
     */
    function copy_to_media_library($image)
    {
        $retval = FALSE;
        // Get the image
        if (is_int($image)) {
            $imageId = $image;
            $mapper = C_Image_Mapper::get_instance();
            $image = $mapper->find($imageId);
        }
        if ($image) {
            $subdir = apply_filters('ngg_import_to_media_library_subdir', 'nggallery_import');
            $wordpress_upload_dir = wp_upload_dir();
            $path = $wordpress_upload_dir['path'] . DIRECTORY_SEPARATOR . $subdir;
            if (!file_exists($path)) {
                wp_mkdir_p($path);
            }
            $image_abspath = C_Gallery_Storage::get_instance()->get_image_abspath($image, "full");
            $new_file_path = $path . DIRECTORY_SEPARATOR . $image->filename;
            $image_data = getimagesize($image_abspath);
            $new_file_mime = $image_data['mime'];
            $i = 1;
            while (file_exists($new_file_path)) {
                $i++;
                $new_file_path = $path . DIRECTORY_SEPARATOR . $i . '-' . $image->filename;
            }
            if (@copy($image_abspath, $new_file_path)) {
                $upload_id = wp_insert_attachment(['guid' => $new_file_path, 'post_mime_type' => $new_file_mime, 'post_title' => preg_replace('/\\.[^.]+$/', '', $image->alttext), 'post_content' => '', 'post_status' => 'inherit'], $new_file_path);
                update_post_meta($upload_id, '_ngg_image_id', intval($image->pid));
                // wp_generate_attachment_metadata() comes from this file
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $image_meta = wp_generate_attachment_metadata($upload_id, $new_file_path);
                // Generate and save the attachment metas into the database
                wp_update_attachment_metadata($upload_id, $image_meta);
                $retval = $upload_id;
            }
        }
        return $retval;
    }
    /**
     * Delete the given NGG image from the media library
     *
     * @var int|stdClass $imageId
     */
    function delete_from_media_library($imageId)
    {
        // Get the image
        if (!is_int($imageId)) {
            $image = $imageId;
            $imageId = $image->pid;
        }
        if ($postId = $this->object->is_in_media_library($imageId)) {
            wp_delete_post($postId);
        }
    }
    /**
     * Determines if the given NGG image id has been uploaded to the media library
     *
     * @param integer $imageId
     * @return FALSE|int attachment_id
     */
    function is_in_media_library($imageId)
    {
        $retval = FALSE;
        // Get the image
        if (is_object($imageId)) {
            $image = $imageId;
            $imageId = $image->pid;
        }
        // Try to find an attachment for the given image_id
        if ($imageId) {
            $query = new WP_Query(array('post_type' => 'attachment', 'meta_key' => '_ngg_image_id', 'meta_value_num' => $imageId));
            foreach ($query->get_posts() as $post) {
                $retval = $post->ID;
            }
        }
        return $retval;
    }
    /**
     * @param string $filename
     * @return bool
     */
    public function is_allowed_image_extension($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        $allowed_extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
        foreach ($allowed_extensions as $extension) {
            $allowed_extensions[] = $extension . '_backup';
        }
        return in_array($extension, $allowed_extensions);
    }
    function is_current_user_over_quota()
    {
        $retval = FALSE;
        $settings = C_NextGen_Settings::get_instance();
        if (is_multisite() && $settings->get('wpmuQuotaCheck')) {
            require_once ABSPATH . 'wp-admin/includes/ms.php';
            $retval = upload_is_user_over_quota(FALSE);
        }
        return $retval;
    }
    /**
     * @param string? $filename
     * @return bool
     */
    function is_image_file($filename = NULL)
    {
        $retval = FALSE;
        if (!$filename && isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $filename = $_FILES['file']['tmp_name'];
        }
        $allowed_mime = apply_filters('ngg_allowed_mime_types', NGG_DEFAULT_ALLOWED_MIME_TYPES);
        // If we can, we'll verify the mime type
        if (function_exists('exif_imagetype')) {
            if (($image_type = @exif_imagetype($filename)) !== FALSE) {
                $retval = in_array(image_type_to_mime_type($image_type), $allowed_mime);
            }
        } else {
            $file_info = @getimagesize($filename);
            if (isset($file_info[2])) {
                $retval = in_array(image_type_to_mime_type($file_info[2]), $allowed_mime);
            }
        }
        return $retval;
    }
    function is_zip()
    {
        $retval = FALSE;
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file_info = $_FILES['file'];
            if (isset($file_info['type'])) {
                $type = $file_info['type'];
                $type_parts = explode('/', $type);
                if (strtolower($type_parts[0]) == 'application') {
                    $spec = $type_parts[1];
                    $spec_parts = explode('-', $spec);
                    $spec_parts = array_map('strtolower', $spec_parts);
                    if (in_array($spec, array('zip', 'octet-stream')) || in_array('zip', $spec_parts)) {
                        $retval = true;
                    }
                }
            }
        }
        return $retval;
    }
    function maybe_base64_decode($data)
    {
        $decoded = base64_decode($data);
        if ($decoded === FALSE) {
            return $data;
        } else {
            if (base64_encode($decoded) == $data) {
                return base64_decode($data);
            }
        }
        return $data;
    }
    function get_unique_abspath($file_abspath)
    {
        $filename = basename($file_abspath);
        $dir_abspath = dirname($file_abspath);
        $num = 1;
        $pattern = path_join($dir_abspath, "*_{$filename}");
        if ($found = glob($pattern)) {
            natsort($found);
            $last = array_pop($found);
            $last = basename($last);
            if (preg_match("/^(\\d+)_/", $last, $match)) {
                $num = intval($match[1]) + 1;
            }
        }
        return path_join($dir_abspath, "{$num}_{$filename}");
    }
    function sanitize_filename_for_db($filename = NULL)
    {
        $filename = $filename ? $filename : uniqid('nextgen-gallery');
        $filename = preg_replace("#^/#", "", $filename);
        $filename = sanitize_file_name($filename);
        if (preg_match("/\\-(png|jpg|gif|jpeg|jpg_backup)\$/i", $filename, $match)) {
            $filename = str_replace($match[0], '.' . $match[1], $filename);
        }
        return $filename;
    }
    /**
     * Determines whether a WebP image is animated which GD does not support.
     *
     * @see https://developers.google.com/speed/webp/docs/riff_container
     * @param string $filename
     * @return bool
     */
    public function is_animated_webp($filename)
    {
        $retval = FALSE;
        $handle = fopen($filename, 'rb');
        fseek($handle, 12);
        if (fread($handle, 4) === 'VP8X') {
            fseek($handle, 20);
            $flag = fread($handle, 1);
            $retval = (bool) (ord($flag) >> 1 & 1);
        }
        fclose($handle);
        return $retval;
    }
    function import_image_file($dst_gallery, $image_abspath, $filename = NULL, $image = FALSE, $override = FALSE, $move = FALSE)
    {
        $image_abspath = wp_normalize_path($image_abspath);
        if ($this->object->is_current_user_over_quota()) {
            $message = sprintf(__('Sorry, you have used your space allocation. Please delete some files to upload more files.', 'nggallery'));
            throw new E_NoSpaceAvailableException($message);
        }
        // Do we have a gallery to import to?
        if ($dst_gallery) {
            // Get the gallery abspath. This is where we will put the image files
            $gallery_abspath = $this->object->get_gallery_abspath($dst_gallery);
            // If we can't write to the directory, then there's no point in continuing
            if (!@file_exists($gallery_abspath)) {
                @wp_mkdir_p($gallery_abspath);
            }
            if (!is_writable($gallery_abspath)) {
                throw new E_InsufficientWriteAccessException(FALSE, $gallery_abspath, FALSE);
            }
            // Sanitize the filename for storing in the DB
            $filename = $this->sanitize_filename_for_db($filename);
            // Ensure that the filename is valid
            $extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
            $extensions[] = '_backup';
            $ext_list = implode('|', $extensions);
            if (!preg_match("/({$ext_list})\$/i", $filename)) {
                throw new E_UploadException(__('Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery'));
            }
            // GD does not support animated WebP and will generate a fatal error when we try to create thumbnails or resize
            if ($this->is_animated_webp($image_abspath)) {
                throw new E_UploadException(__('Animated WebP images are not supported.', 'nggallery'));
            }
            // Compute the destination folder
            $new_image_abspath = path_join($gallery_abspath, $filename);
            // Are the src and dst the same? If so, we don't have to copy or move files
            if ($image_abspath != $new_image_abspath) {
                // If we're not to override, ensure that the filename is unique
                if (!$override && @file_exists($new_image_abspath)) {
                    $new_image_abspath = $this->object->get_unique_abspath($new_image_abspath);
                    $filename = $this->sanitize_filename_for_db(basename($new_image_abspath));
                }
                // Try storing the file
                $copied = copy($image_abspath, $new_image_abspath);
                if ($copied && $move) {
                    unlink($image_abspath);
                }
                // Ensure that we're not vulerable to CVE-2017-2416 exploit
                if (($dimensions = getimagesize($new_image_abspath)) !== FALSE) {
                    if (isset($dimensions[0]) && intval($dimensions[0]) > 30000 || isset($dimensions[1]) && intval($dimensions[1]) > 30000) {
                        unlink($new_image_abspath);
                        throw new E_UploadException(__('Image file too large. Maximum image dimensions supported are 30k x 30k.'));
                    }
                }
            }
            // Save the image in the DB
            $image_mapper = C_Image_Mapper::get_instance();
            $image_mapper->_use_cache = FALSE;
            if ($image) {
                if (is_numeric($image)) {
                    $image = $image_mapper->find($image);
                }
            }
            if (!$image) {
                $image = $image_mapper->create();
            }
            $image->alttext = preg_replace("#\\.\\w{2,4}\$#", "", $filename);
            $image->galleryid = is_numeric($dst_gallery) ? $dst_gallery : $dst_gallery->gid;
            $image->filename = $filename;
            $image->image_slug = nggdb::get_unique_slug(sanitize_title_with_dashes($image->alttext), 'image');
            $image_id = $image_mapper->save($image);
            if (!$image_id) {
                $exception = '';
                foreach ($image->get_errors() as $field => $errors) {
                    foreach ($errors as $error) {
                        if (!empty($exception)) {
                            $exception .= "<br/>";
                        }
                        $exception .= __(sprintf("Error while uploading %s: %s", $filename, $error), 'nextgen-gallery');
                    }
                }
                throw new E_UploadException($exception);
            }
            // Important: do not remove this line. The image mapper's save() routine imports metadata
            // meaning we must re-acquire a new $image object after saving it above; if we do not our
            // existing $image object will lose any metadata retrieved during said save() method.
            $image = $image_mapper->find($image_id);
            $image_mapper->_use_cache = TRUE;
            $settings = C_NextGen_Settings::get_instance();
            // Backup the image
            if ($settings->get('imgBackup', FALSE)) {
                $this->object->backup_image($image, TRUE);
            }
            // Most browsers do not honor EXIF's Orientation header: rotate the image to prevent display issues
            $this->object->correct_exif_rotation($image, TRUE);
            // Create resized version of image
            if ($settings->get('imgAutoResize', FALSE)) {
                $this->object->generate_resized_image($image, TRUE);
            }
            // Generate a thumbnail for the image
            $this->object->generate_thumbnail($image);
            // Set gallery preview image if missing
            C_Gallery_Mapper::get_instance()->set_preview_image($dst_gallery, $image_id, TRUE);
            // Automatically watermark the main image if requested
            if ($settings->get('watermark_automatically_at_upload', 0)) {
                $image_abspath = $this->object->get_image_abspath($image, 'full');
                $this->object->generate_image_clone($image_abspath, $image_abspath, array('watermark' => TRUE));
            }
            // Notify other plugins that an image has been added
            do_action('ngg_added_new_image', $image);
            // delete dirsize after adding new images
            delete_transient('dirsize_cache');
            // Seems redundant to above hook. Maintaining for legacy purposes
            do_action('ngg_after_new_images_added', is_numeric($dst_gallery) ? $dst_gallery : $dst_gallery->gid, array($image_id));
            return $image_id;
        } else {
            throw new E_EntityNotFoundException();
        }
        return NULL;
    }
    /**
     * Uploads base64 file to a gallery
     *
     * @param int|stdClass|C_Gallery $gallery
     * @param string $data base64-encoded string of data representing the image
     * @param string|false (optional) $filename specifies the name of the file
     * @param int|false $image_id (optional)
     * @param bool $override (optional)
     * @return bool|int
     */
    function upload_base64_image($gallery, $data, $filename = FALSE, $image_id = FALSE, $override = FALSE, $move = FALSE)
    {
        try {
            $temp_abspath = tempnam(sys_get_temp_dir(), '');
            // Try writing the image
            $fp = fopen($temp_abspath, 'wb');
            fwrite($fp, $this->maybe_base64_decode($data));
            fclose($fp);
        } catch (E_UploadException $ex) {
            throw $ex;
        }
        return $this->object->import_image_file($gallery, $temp_abspath, $filename, $image_id, $override, $move);
    }
    /**
     * Uploads an image for a particular gallery
     * @param int|object|C_Gallery $gallery
     * @param string|bool $filename (optional) Specifies the name of the file
     * @param string|bool $data (optional) If specified, expects base64 encoded string of data
     * @return C_Image
     */
    function upload_image($gallery, $filename = FALSE, $data = FALSE)
    {
        $retval = NULL;
        // Ensure that we have the data present that we require
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            //		$_FILES = Array(
            //		 [file]	=>	Array (
            //            [name] => Canada_landscape4.jpg
            //            [type] => image/jpeg
            //            [tmp_name] => /private/var/tmp/php6KO7Dc
            //            [error] => 0
            //            [size] => 64975
            //         )
            //
            $file = $_FILES['file'];
            if ($this->object->is_zip()) {
                $retval = $this->object->upload_zip($gallery);
            } else {
                if ($this->is_image_file()) {
                    $retval = $this->object->import_image_file($gallery, $file['tmp_name'], $filename ? $filename : (isset($file['name']) ? $file['name'] : FALSE), FALSE, FALSE, TRUE);
                } else {
                    // Remove the non-valid (and potentially insecure) file from the PHP upload directory
                    if (isset($_FILES['file']['tmp_name'])) {
                        $filename = $_FILES['file']['tmp_name'];
                        @unlink($filename);
                    }
                    throw new E_UploadException(__('Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery'));
                }
            }
        } elseif ($data) {
            $retval = $this->object->upload_base64_image($gallery, $data, $filename);
        } else {
            throw new E_UploadException();
        }
        return $retval;
    }
    /**
     * @param int $gallery_id
     * @return array|bool
     */
    function upload_zip($gallery_id)
    {
        if (!$this->object->is_zip()) {
            return FALSE;
        }
        $retval = FALSE;
        $memory_limit = intval(ini_get('memory_limit'));
        if (!extension_loaded('suhosin') && $memory_limit < 256) {
            @ini_set('memory_limit', '256M');
        }
        $fs = C_Fs::get_instance();
        // Uses the WordPress ZIP abstraction API
        include_once $fs->join_paths(ABSPATH, 'wp-admin', 'includes', 'file.php');
        WP_Filesystem(FALSE, get_temp_dir(), TRUE);
        // Ensure that we truly have the gallery id
        $gallery_id = $this->object->_get_gallery_id($gallery_id);
        $zipfile = $_FILES['file']['tmp_name'];
        $dest_path = implode(DIRECTORY_SEPARATOR, array(rtrim(get_temp_dir(), "/\\"), 'unpacked-' . M_I18n::mb_basename($zipfile)));
        // Attempt to extract the zip file into the normal system directory
        $extracted = $this->object->extract_zip($zipfile, $dest_path);
        // Now verify it worked. get_temp_dir() will check each of the following directories to ensure they are
        // a directory and against wp_is_writable(). Should ALL of those options fail we will fallback to wp_upload_dir().
        //
        // WP_TEMP_DIR
        // sys_get_temp_dir()
        // ini/upload_tmp_dir
        // WP_CONTENT_DIR
        // /tmp
        $size = 0;
        $files = glob($dest_path . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            if (is_array(stat($file))) {
                $size += filesize($file);
            }
        }
        // Extraction failed; attempt again with wp_upload_dir()
        if ($size == 0) {
            // Remove the empty directory we may have possibly created but could not write to
            $this->object->delete_directory($dest_path);
            $destination = wp_upload_dir();
            $destination_path = $destination['basedir'];
            $dest_path = implode(DIRECTORY_SEPARATOR, array(rtrim($destination_path, "/\\"), rand(), 'unpacked-' . M_I18n::mb_basename($zipfile)));
            $extracted = $this->object->extract_zip($zipfile, $dest_path);
        }
        if ($extracted) {
            $retval = $this->object->import_gallery_from_fs($dest_path, $gallery_id);
        }
        $this->object->delete_directory($dest_path);
        if (!extension_loaded('suhosin')) {
            @ini_set('memory_limit', $memory_limit . 'M');
        }
        return $retval;
    }
    /**
     * @param string $zipfile
     * @param string $dest_path
     * @return bool FALSE on failure
     */
    public function extract_zip($zipfile, $dest_path)
    {
        wp_mkdir_p($dest_path);
        if (class_exists('ZipArchive', FALSE) && apply_filters('unzip_file_use_ziparchive', TRUE)) {
            $zipObj = new ZipArchive();
            if ($zipObj->open($zipfile) === FALSE) {
                return FALSE;
            }
            for ($i = 0; $i < $zipObj->numFiles; $i++) {
                $filename = $zipObj->getNameIndex($i);
                if (!$this->object->is_allowed_image_extension($filename)) {
                    continue;
                }
                $zipObj->extractTo($dest_path, array($zipObj->getNameIndex($i)));
            }
        } else {
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
            $zipObj = new PclZip($zipfile);
            $zipContent = $zipObj->listContent();
            $indexesToExtract = array();
            foreach ($zipContent as $zipItem) {
                if ($zipItem['folder']) {
                    continue;
                }
                if (!$this->object->is_allowed_image_extension($zipItem['stored_filename'])) {
                    continue;
                }
                $indexesToExtract[] = $zipItem['index'];
            }
            if (!$zipObj->extractByIndex(implode(',', $indexesToExtract), $dest_path)) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
/**
 * Model for NextGen Gallery Images
 * @mixin Mixin_NextGen_Gallery_Image_Validation
 * @implements I_Image
 */
class C_Image extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Image_Mapper';
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_NextGen_Gallery_Image_Validation');
        $this->implement('I_Image');
    }
    /**
     * Instantiates a new model
     * @param array|stdClass $properties (optional)
     * @param C_Image_Mapper|false $mapper (optional)
     * @param string|false $context (optional)
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        parent::initialize($mapper, $properties);
    }
    /**
     * Returns the model representing the gallery associated with this image
     * @param object|false $model (optional)
     * @return C_Gallery|object
     */
    function get_gallery($model = FALSE)
    {
        return C_Gallery_Mapper::get_instance()->find($this->galleryid, $model);
    }
}
class Mixin_NextGen_Gallery_Image_Validation extends Mixin
{
    function validation()
    {
        // Additional checks...
        if (isset($this->object->description)) {
            $this->object->description = M_NextGen_Data::strip_html($this->object->description, TRUE);
        }
        if (isset($this->object->alttext)) {
            $this->object->alttext = M_NextGen_Data::strip_html($this->object->alttext, TRUE);
        }
        $this->validates_presence_of('galleryid', 'filename', 'alttext', 'exclude', 'sortorder', 'imagedate');
        $this->validates_numericality_of('galleryid');
        $this->validates_numericality_of($this->id());
        $this->validates_numericality_of('sortorder');
        $this->validates_length_of('filename', 185, '<=', __('Image filenames may not be longer than 185 characters in length', 'nextgen-gallery'));
        return $this->object->is_valid();
    }
}
/**
 * Class C_Image_Mapper
 *
 * @mixin Mixin_NextGen_Table_Extras
 * @mixin Mixin_Gallery_Image_Mapper
 * @implements I_Image_Mapper
 */
class C_Image_Mapper extends C_CustomTable_DataMapper_Driver
{
    public static $_instance = NULL;
    /**
     * Defines the gallery image mapper
     * @param string|false $context (optional)
     * @param mixed $not_used
     */
    function define($context = FALSE, $not_used = FALSE)
    {
        // Add 'attachment' context
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, 'attachment');
        // Define the mapper
        $this->_primary_key_column = 'pid';
        parent::define('ngg_pictures', $context);
        $this->add_mixin('Mixin_NextGen_Table_Extras');
        $this->add_mixin('Mixin_Gallery_Image_Mapper');
        $this->implement('I_Image_Mapper');
        $this->set_model_factory_method('image');
        // Define the columns
        $this->define_column('pid', 'BIGINT', 0);
        $this->define_column('image_slug', 'VARCHAR(255)');
        $this->define_column('post_id', 'BIGINT', 0);
        $this->define_column('galleryid', 'BIGINT', 0);
        $this->define_column('filename', 'VARCHAR(255)');
        $this->define_column('description', 'TEXT');
        $this->define_column('alttext', 'TEXT');
        $this->define_column('imagedate', 'DATETIME');
        $this->define_column('exclude', 'INT', 0);
        $this->define_column('sortorder', 'BIGINT', 0);
        $this->define_column('meta_data', 'TEXT');
        $this->define_column('extras_post_id', 'BIGINT', 0);
        $this->define_column('updated_at', 'BIGINT');
        // Mark the columns which should be unserialized
        $this->add_serialized_column('meta_data');
    }
    function initialize($object_name = FALSE)
    {
        parent::initialize('ngg_pictures');
    }
    /**
     * @param bool|string $context
     * @return C_Image_Mapper
     */
    static function get_instance($context = False)
    {
        if (is_null(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass($context);
        }
        return self::$_instance;
    }
    /**
     * Finds all images for a gallery
     * @param $gallery
     * @param bool $model
     *
     * @return array
     */
    function find_all_for_gallery($gallery, $model = FALSE)
    {
        $retval = array();
        $gallery_id = 0;
        if (is_object($gallery)) {
            if (isset($gallery->id_field)) {
                $gallery_id = $gallery->{$gallery->id_field};
            } else {
                $key = $this->object->get_primary_key_column();
                if (isset($gallery->{$key})) {
                    $gallery_id = $gallery->{$key};
                }
            }
        } elseif (is_numeric($gallery)) {
            $gallery_id = $gallery;
        }
        if ($gallery_id) {
            $retval = $this->object->select()->where(array("galleryid = %s", $gallery_id))->run_query(FALSE, $model);
        }
        return $retval;
    }
    function reimport_metadata($image_or_id)
    {
        // Get the image
        $image = NULL;
        if (is_int($image_or_id)) {
            $image = $this->object->find($image_or_id);
        } else {
            $image = $image_or_id;
        }
        // Reset all image details that would have normally been imported
        if (is_array($image->meta_data)) {
            unset($image->meta_data['saved']);
        }
        nggAdmin::import_MetaData($image);
        return $this->object->save($image);
    }
    /**
     * Retrieves the id from an image
     * @param $image
     * @return bool
     */
    function get_id($image)
    {
        $retval = FALSE;
        // Have we been passed an entity and is the id_field set?
        if ($image instanceof stdClass) {
            if (isset($image->id_field)) {
                $retval = $image->{$image->id_field};
            }
        } else {
            $retval = $image->id();
        }
        // If we still don't have an id, then we'll lookup the primary key
        // and try fetching it manually
        if (!$retval) {
            $key = $this->object->get_primary_key_column();
            $retval = $image->{$key};
        }
        return $retval;
    }
}
/**
 * Sets the alttext property as the post title
 */
class Mixin_Gallery_Image_Mapper extends Mixin
{
    function destroy($image)
    {
        $retval = $this->call_parent('destroy', $image);
        // Delete tag associations with the image
        if (!is_numeric($image)) {
            $image = $image->{$image->id_field};
        }
        wp_delete_object_term_relationships($image, 'ngg_tag');
        C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');
        return $retval;
    }
    function _save_entity($entity)
    {
        $entity->updated_at = time();
        // If successfully saved then import metadata
        $retval = $this->call_parent('_save_entity', $entity);
        if ($retval) {
            include_once NGGALLERY_ABSPATH . '/admin/functions.php';
            $image_id = $this->get_id($entity);
            if (!isset($entity->meta_data['saved'])) {
                nggAdmin::import_MetaData($image_id);
            }
            C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');
        }
        return $retval;
    }
    function get_post_title($entity)
    {
        return $entity->alttext;
    }
    function set_defaults($entity)
    {
        // If not set already, we'll add an exclude property. This is used
        // by NextGEN Gallery itself, as well as the Attach to Post module
        $this->object->_set_default_value($entity, 'exclude', 0);
        // Ensure that the object has description and alttext attributes
        $this->object->_set_default_value($entity, 'description', '');
        $this->object->_set_default_value($entity, 'alttext', '');
        // If not set already, set a default sortorder
        $this->object->_set_default_value($entity, 'sortorder', 0);
        // The imagedate must be set
        if (!isset($entity->imagedate) or is_null($entity->imagedate) or $entity->imagedate == '0000-00-00 00:00:00') {
            $entity->imagedate = date("Y-m-d H:i:s");
        }
        // If a filename is set, and no alttext is set, then set the alttext
        // to the basename of the filename (legacy behavior)
        if (isset($entity->filename)) {
            $path_parts = M_I18n::mb_pathinfo($entity->filename);
            $alttext = !isset($path_parts['filename']) ? substr($path_parts['basename'], 0, strpos($path_parts['basename'], '.')) : $path_parts['filename'];
            $this->object->_set_default_value($entity, 'alttext', $alttext);
        }
        // Set unique slug
        if (!empty($entity->alttext) && empty($entity->image_slug)) {
            $entity->image_slug = nggdb::get_unique_slug(sanitize_title_with_dashes($entity->alttext), 'image');
        }
        // Ensure that the exclude parameter is an integer or boolean-evaluated
        // value
        if (is_string($entity->exclude)) {
            $entity->exclude = intval($entity->exclude);
        }
        // Trim alttext and description
        $entity->description = trim($entity->description);
        $entity->alttext = trim($entity->alttext);
        if (!is_admin()) {
            if (!empty($entity->description)) {
                $entity->description = M_I18N::translate($entity->description, 'pic_' . $entity->{$entity->id_field} . '_description');
            }
            if (!empty($entity->alttext)) {
                $entity->alttext = M_I18N::translate($entity->alttext, 'pic_' . $entity->{$entity->id_field} . '_alttext');
            }
        }
    }
}
/**
 * This class provides a lazy-loading wrapper to the NextGen-Legacy "nggImage" class for use in legacy style templates
 */
class C_Image_Wrapper
{
    public $_cache;
    // cache of retrieved values
    public $_settings;
    // I_Settings_Manager cache
    public $_storage;
    // I_Gallery_Storage cache
    public $_galleries;
    // cache of I_Gallery_Mapper (plural)
    public $_orig_image;
    // original provided image
    public $_orig_image_id;
    // original image ID
    public $_cache_overrides;
    // allow for forcing variable values
    public $_legacy = FALSE;
    public $_displayed_gallery;
    // cached object
    /**
     * Constructor. Converts the image class into an array and fills from defaults any missing values
     *
     * @param object $image Individual result from displayed_gallery->get_entities()
     * @param object $displayed_gallery Displayed gallery -- MAY BE NULL
     * @param bool $legacy Whether the image source is from NextGen Legacy or NextGen
     * @return void
     */
    public function __construct($image, $displayed_gallery = NULL, $legacy = FALSE)
    {
        // for clarity
        if ($displayed_gallery && isset($displayed_gallery->display_settings['number_of_columns'])) {
            $columns = $displayed_gallery->display_settings['number_of_columns'];
        } else {
            $columns = 0;
        }
        // Public variables
        $defaults = array(
            'errmsg' => '',
            // Error message to display, if any
            'error' => FALSE,
            // Error state
            'imageURL' => '',
            // URL Path to the image
            'thumbURL' => '',
            // URL Path to the thumbnail
            'imagePath' => '',
            // Server Path to the image
            'thumbPath' => '',
            // Server Path to the thumbnail
            'href' => '',
            // A href link code
            // Mostly constant
            'thumbPrefix' => 'thumbs_',
            // FolderPrefix to the thumbnail
            'thumbFolder' => '/thumbs/',
            // Foldername to the thumbnail
            // Image Data
            'galleryid' => 0,
            // Gallery ID
            'pid' => 0,
            // Image ID
            'filename' => '',
            // Image filename
            'description' => '',
            // Image description
            'alttext' => '',
            // Image alttext
            'imagedate' => '',
            // Image date/time
            'exclude' => '',
            // Image exclude
            'thumbcode' => '',
            // Image effect code
            // Gallery Data
            'name' => '',
            // Gallery name
            'path' => '',
            // Gallery path
            'title' => '',
            // Gallery title
            'pageid' => 0,
            // Gallery page ID
            'previewpic' => 0,
            // Gallery preview pic
            'style' => $columns > 0 ? 'style="width:' . floor(100 / $columns) . '%;"' : '',
            'hidden' => FALSE,
            'permalink' => '',
            'tags' => '',
        );
        // convert the image to an array and apply the defaults
        $this->_orig_image = $image;
        $image = (array) $image;
        foreach ($defaults as $key => $val) {
            if (!isset($image[$key])) {
                $image[$key] = $val;
            }
        }
        // cache the results
        ksort($image);
        $id_field = !empty($image['id_field']) ? $image['id_field'] : 'pid';
        $this->_cache = (array) apply_filters('ngg_image_object', (object) $image, $image[$id_field]);
        $this->_orig_image_id = $image[$id_field];
        $this->_legacy = $legacy;
        $this->_displayed_gallery = $displayed_gallery;
    }
    public function __set($name, $value)
    {
        $this->_cache[$name] = $value;
    }
    public function __isset($name)
    {
        return isset($this->_cache[$name]);
    }
    public function __unset($name)
    {
        unset($this->_cache[$name]);
    }
    /**
     * Lazy-loader for image variables.
     *
     * @param string $name Parameter name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->_cache_overrides[$name])) {
            return $this->_cache_overrides[$name];
        }
        // at the bottom we default to returning $this->_cache[$name].
        switch ($name) {
            case 'alttext':
                $this->_cache['alttext'] = empty($this->_cache['alttext']) ? ' ' : html_entity_decode(stripslashes($this->_cache['alttext']));
                return $this->_cache['alttext'];
            case 'author':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['author'] = $gallery->name;
                return $this->_cache['author'];
            case 'caption':
                $caption = html_entity_decode(stripslashes($this->__get('description')));
                if (empty($caption)) {
                    $caption = '&nbsp;';
                }
                $this->_cache['caption'] = $caption;
                return $this->_cache['caption'];
            case 'description':
                $this->_cache['description'] = empty($this->_cache['description']) ? ' ' : html_entity_decode(stripslashes($this->_cache['description']));
                return $this->_cache['description'];
            case 'galdesc':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['galdesc'] = $gallery->name;
                return $this->_cache['galdesc'];
            case 'gid':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['gid'] = $gallery->{$gallery->id_field};
                return $this->_cache['gid'];
            case 'href':
                return $this->__get('imageHTML');
            case 'id':
                return $this->_orig_image_id;
            case 'imageHTML':
                $tmp = '<a href="' . $this->__get('imageURL') . '" title="' . htmlspecialchars(stripslashes($this->__get('description'))) . '" ' . $this->get_thumbcode($this->__get('name')) . '>' . '<img alt="' . $this->__get('alttext') . '" src="' . $this->__get('imageURL') . '"/>' . '</a>';
                $this->_cache['href'] = $tmp;
                $this->_cache['imageHTML'] = $tmp;
                return $this->_cache['imageHTML'];
            case 'imagePath':
                $storage = $this->get_storage();
                $this->_cache['imagePath'] = $storage->get_image_abspath($this->_orig_image, 'full');
                return $this->_cache['imagePath'];
            case 'imageURL':
                $storage = $this->get_storage();
                $this->_cache['imageURL'] = $storage->get_image_url($this->_orig_image, 'full');
                return $this->_cache['imageURL'];
            case 'linktitle':
                $this->_cache['linktitle'] = htmlspecialchars(stripslashes($this->__get('description')));
                return $this->_cache['linktitle'];
            case 'name':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['name'] = $gallery->name;
                return $this->_cache['name'];
            case 'pageid':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['pageid'] = $gallery->name;
                return $this->_cache['pageid'];
            case 'path':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['path'] = $gallery->name;
                return $this->_cache['path'];
            case 'permalink':
                $this->_cache['permalink'] = $this->__get('imageURL');
                return $this->_cache['permalink'];
            case 'pid':
                return $this->_orig_image_id;
            case 'id_field':
                $this->_cache['id_field'] = !empty($this->_orig_image->id_field) ? $this->_orig_image->id_field : 'pid';
                return $this->_cache['id_field'];
            case 'pidlink':
                $application = C_Router::get_instance()->get_routed_app();
                $controller = C_Display_Type_Controller::get_instance();
                $this->_cache['pidlink'] = $controller->set_param_for($application->get_routed_url(TRUE), 'pid', $this->__get('image_slug'));
                return $this->_cache['pidlink'];
            case 'previewpic':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['previewpic'] = $gallery->name;
                return $this->_cache['previewpic'];
            case 'size':
                $w = 0;
                $h = 0;
                if ($this->_displayed_gallery && isset($this->_displayed_gallery->display_settings)) {
                    $ds = $this->_displayed_gallery->display_settings;
                    if (isset($ds['override_thumbnail_settings']) && $ds['override_thumbnail_settings']) {
                        $w = $ds['thumbnail_width'];
                        $h = $ds['thumbnail_height'];
                    }
                }
                if (!$w || !$h) {
                    if (is_string($this->_orig_image->meta_data)) {
                        $this->_orig_image = C_NextGen_Serializable::unserialize($this->_orig_image->meta_data);
                    }
                    if (!isset($this->_orig_image->meta_data['thumbnail'])) {
                        $storage = $this->get_storage();
                        $storage->generate_thumbnail($this->_orig_image);
                    }
                    $w = $this->_orig_image->meta_data['thumbnail']['width'];
                    $h = $this->_orig_image->meta_data['thumbnail']['height'];
                }
                return "width='{$w}' height='{$h}'";
            case 'slug':
                $gallery = $this->get_legacy_gallery($this->__get('galleryid'));
                $this->_cache['slug'] = $gallery->name;
                return $this->_cache['slug'];
            case 'tags':
                $this->_cache['tags'] = wp_get_object_terms($this->__get('id'), 'ngg_tag', 'fields=all');
                return $this->_cache['tags'];
            case 'thumbHTML':
                $tmp = '<a href="' . $this->__get('imageURL') . '" title="' . htmlspecialchars(stripslashes($this->__get('description'))) . '" ' . $this->get_thumbcode($this->__get('name')) . '>' . '<img alt="' . $this->__get('alttext') . '" src="' . $this->thumbURL . '"/>' . '</a>';
                $this->_cache['href'] = $tmp;
                $this->_cache['thumbHTML'] = $tmp;
                return $this->_cache['thumbHTML'];
            case 'thumbPath':
                $storage = $this->get_storage();
                $this->_cache['thumbPath'] = $storage->get_image_abspath($this->_orig_image, 'thumbnail');
                return $this->_cache['thumbPath'];
            case 'thumbnailURL':
                $storage = $this->get_storage();
                $thumbnail_size_name = 'thumbnail';
                if ($this->_displayed_gallery && isset($this->_displayed_gallery->display_settings)) {
                    $ds = $this->_displayed_gallery->display_settings;
                    if (isset($ds['override_thumbnail_settings']) && $ds['override_thumbnail_settings']) {
                        $dynthumbs = C_Component_Registry::get_instance()->get_utility('I_Dynamic_Thumbnails_Manager');
                        $dyn_params = array('width' => $ds['thumbnail_width'], 'height' => $ds['thumbnail_height']);
                        if ($ds['thumbnail_quality']) {
                            $dyn_params['quality'] = $ds['thumbnail_quality'];
                        }
                        if ($ds['thumbnail_crop']) {
                            $dyn_params['crop'] = TRUE;
                        }
                        if ($ds['thumbnail_watermark']) {
                            $dyn_params['watermark'] = TRUE;
                        }
                        $thumbnail_size_name = $dynthumbs->get_size_name($dyn_params);
                    }
                }
                $this->_cache['thumbnailURL'] = $storage->get_image_url($this->_orig_image, $thumbnail_size_name);
                return $this->_cache['thumbnailURL'];
            case 'thumbcode':
                if ($this->_displayed_gallery && isset($this->_displayed_gallery->display_settings) && isset($this->_displayed_gallery->display_settings['use_imagebrowser_effect']) && $this->_displayed_gallery->display_settings['use_imagebrowser_effect'] && !empty($this->_orig_image->thumbcode)) {
                    $this->_cache['thumbcode'] = $this->_orig_image->thumbcode;
                } else {
                    $this->_cache['thumbcode'] = $this->get_thumbcode($this->__get('name'));
                }
                return $this->_cache['thumbcode'];
            case 'thumbURL':
                return $this->__get('thumbnailURL');
            case 'title':
                $this->_cache['title'] = stripslashes($this->__get('name'));
                return $this->_cache['title'];
            case 'url':
                $storage = $this->get_storage();
                $this->_cache['url'] = $storage->get_image_url($this->_orig_image, 'full');
                return $this->_cache['url'];
            default:
                return $this->_cache[$name];
        }
    }
    // called on initial nggLegacy image at construction. not sure what to do with it now.
    function construct_ngg_Image($gallery)
    {
        do_action_ref_array('ngg_get_image', array(&$this));
        unset($this->tags);
    }
    /**
     * Retrieves and caches an I_Settings_Manager instance
     *
     * @return mixed
     */
    function get_settings()
    {
        if (is_null($this->_settings)) {
            $this->_settings = C_NextGen_Settings::get_instance();
        }
        return $this->_settings;
    }
    /**
     * Retrieves and caches an I_Gallery_Storage instance
     *
     * @return mixed
     */
    function get_storage()
    {
        if (is_null($this->_storage)) {
            $this->_storage = C_Gallery_Storage::get_instance();
        }
        return $this->_storage;
    }
    /**
     * Retrieves I_Gallery_Mapper instance.
     *
     * @param int $gallery_id Gallery ID
     * @return mixed
     */
    function get_gallery($gallery_id)
    {
        if (isset($this->container) && method_exists($this->container, 'get_gallery')) {
            return $this->container->get_gallery($gallery_id);
        }
        return C_Gallery_Mapper::get_instance()->find($gallery_id);
    }
    /**
     * Retrieves I_Gallery_Mapper instance.
     *
     * @param int $gallery_id Gallery ID
     * @return mixed
     */
    function get_legacy_gallery($gallery_id)
    {
        return C_Gallery_Mapper::get_instance()->find($gallery_id);
    }
    /**
     * Get the thumbnail code (to add effects on thumbnail click)
     *
     * Applies the filter 'ngg_get_thumbcode'
     * @param string $gallery_name (optional) Default = ''
     * @return string
     */
    function get_thumbcode($gallery_name = '')
    {
        if (empty($this->_displayed_gallery)) {
            $effect_code = C_NextGen_Settings::get_instance()->thumbCode;
            $effect_code = str_replace('%GALLERY_ID%', $gallery_name, $effect_code);
            $effect_code = str_replace('%GALLERY_NAME%', $gallery_name, $effect_code);
            $retval = $effect_code;
        } else {
            $controller = C_Display_Type_Controller::get_instance();
            $retval = $controller->get_effect_code($this->_displayed_gallery);
            // This setting requires that we disable the effect code
            $ds = $this->_displayed_gallery->display_settings;
            if (isset($ds['use_imagebrowser_effect']) && $ds['use_imagebrowser_effect']) {
                $retval = '';
            }
        }
        $retval = apply_filters('ngg_get_thumbcode', $retval, $this);
        // ensure some additional data- fields are added; provides Pro-Lightbox compatibility
        $retval .= ' data-image-id="' . $this->__get('id') . '"';
        $retval .= ' data-src="' . $this->__get('imageURL') . '"';
        $retval .= ' data-thumbnail="' . $this->__get('thumbnailURL') . '"';
        $retval .= ' data-title="' . esc_attr($this->__get('alttext')) . '"';
        $retval .= ' data-description="' . esc_attr($this->__get('description')) . '"';
        $this->_cache['thumbcode'] = $retval;
        return $retval;
    }
    /**
     * For compatibility support
     *
     * @return mixed
     */
    function get_href_link()
    {
        return $this->__get('imageHTML');
    }
    /**
     * For compatibility support
     *
     * @return mixed
     */
    function get_href_thumb_link()
    {
        return $this->__get('thumbHTML');
    }
    /**
     * Function exists for legacy support but has been gutted to not do anything
     *
     * @param string|int $width (optional) Default = ''
     * @param string|int $height (optional) Default = ''
     * @param string $mode could be watermark | web20 | crop
     * @return bool|string The url for the image or false if failed
     */
    function cached_singlepic_file($width = '', $height = '', $mode = '')
    {
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        $storage = $this->get_storage();
        // determine what to do with 'mode'
        $display_reflection = FALSE;
        $display_watermark = FALSE;
        if (!is_array($mode)) {
            $mode = explode(',', $mode);
        }
        if (in_array('web20', $mode)) {
            $display_reflection = TRUE;
        }
        if (in_array('watermark', $mode)) {
            $display_watermark = TRUE;
        }
        // and go for it
        $params = array('width' => $width, 'height' => $height, 'watermark' => $display_watermark, 'reflection' => $display_reflection);
        return $storage->get_image_url((object) $this->_cache, $dynthumbs->get_size_name($params));
    }
    /**
     * Get the tags associated to this image
     */
    function get_tags()
    {
        return $this->__get('tags');
    }
    /**
     * Get the permalink to the image
     *
     * TODO: Get a permalink to a page presenting the image
     */
    function get_permalink()
    {
        return $this->__get('permalink');
    }
    /**
     * Returns the _cache array; used by nggImage
     * @return array
     */
    function _get_image()
    {
        return $this->_cache;
    }
}
class C_Image_Wrapper_Collection implements ArrayAccess
{
    public $container = array();
    public $galleries = array();
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    public function offsetSet($offset, $value)
    {
        if (is_object($value)) {
            $value->container = $this;
        }
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
    /**
     * Retrieves and caches an I_Gallery_Mapper instance for this gallery id
     *
     * @param int $gallery_id Gallery ID
     * @return mixed
     */
    public function get_gallery($gallery_id)
    {
        if (!isset($this->galleries[$gallery_id]) || is_null($this->galleries[$gallery_id])) {
            $this->galleries[$gallery_id] = C_Gallery_Mapper::get_instance();
        }
        return $this->galleries[$gallery_id];
    }
}
class C_NextGen_Data_Installer extends C_NggLegacy_Installer
{
    function get_registry()
    {
        return C_Component_Registry::get_instance();
    }
    function install()
    {
        $this->remove_table_extra_options();
    }
    function remove_table_extra_options()
    {
        global $wpdb;
        $likes = array("option_name LIKE '%ngg_gallery%'", "option_name LIKE '%ngg_pictures%'", "option_name LIKE '%ngg_album%'");
        $sql = "DELETE FROM {$wpdb->options} WHERE " . implode(" OR ", $likes);
        $wpdb->query($sql);
    }
    function uninstall($hard = FALSE)
    {
        if ($hard) {
            /* Yes: this is commented twice.
            		// TODO for now never delete galleries/albums/content
            #			$mappers = array(
            #				$this->get_registry()->get_utility('I_Album_Mapper'),
            #				$this->get_registry()->get_utility('I_Gallery_Mapper'),
            #				$this->get_registry()->get_utility('I_Image_Mapper'),
            #			);
            
            #			foreach ($mappers as $mapper) {
            #				$mapper->delete()->run_query();
            #			}
            
            #			// Remove ngg tags
            #			global $wpdb;
            #			$wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id IN (SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy='ngg_tag')");
            #			$wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy='ngg_tag'");
                        */
        }
    }
}
class C_NextGen_Metadata extends C_Component
{
    // Image data
    public $image = '';
    // The image object
    public $file_path = '';
    // Path to the image file
    public $size = FALSE;
    // The image size
    public $exif_data = FALSE;
    // EXIF data array
    public $iptc_data = FALSE;
    // IPTC data array
    public $xmp_data = FALSE;
    // XMP data array
    // Filtered Data
    public $exif_array = FALSE;
    // EXIF data array
    public $iptc_array = FALSE;
    // IPTC data array
    public $xmp_array = FALSE;
    // XMP data array
    public $sanitize = FALSE;
    // sanitize meta data on request
    /**
     * Class constructor
     * 
     * @param int $image Image ID
     * @param bool $onlyEXIF TRUE = will parse only EXIF data
     * @return bool FALSE if the file does not exist or metadat could not be read
     */
    public function __construct($image, $onlyEXIF = FALSE)
    {
        if (is_numeric($image)) {
            $image = C_Image_Mapper::get_instance()->find($image);
        }
        $this->image = apply_filters('ngg_find_image_meta', $image);
        $this->file_path = C_Gallery_Storage::get_instance()->get_image_abspath($this->image);
        if (!@file_exists($this->file_path)) {
            return FALSE;
        }
        $this->size = @getimagesize($this->file_path, $metadata);
        if ($this->size && is_array($metadata)) {
            // get exif - data
            if (is_callable('exif_read_data')) {
                $this->exif_data = @exif_read_data($this->file_path, NULL, TRUE);
            }
            // stop here if we didn't need other meta data
            if ($onlyEXIF) {
                return TRUE;
            }
            // get the iptc data - should be in APP13
            if (is_callable('iptcparse') && isset($metadata['APP13'])) {
                $this->iptc_data = @iptcparse($metadata['APP13']);
            }
            // get the xmp data in a XML format
            if (is_callable('xml_parser_create')) {
                $this->xmp_data = $this->extract_XMP($this->file_path);
            }
            return TRUE;
        }
        return FALSE;
    }
    /**
     * return the saved meta data from the database
     *
     * @since 1.4.0
     * @param string $object (optional)
     * @return array|mixed return either the complete array or the single object
     */
    function get_saved_meta($object = false)
    {
        $meta = $this->image->meta_data;
        // Check if we already import the meta data to the database
        if (!is_array($meta) || !isset($meta['saved']) || $meta['saved'] !== TRUE) {
            return false;
        }
        // return one element if requested
        if ($object) {
            return $meta[$object];
        }
        //removed saved parameter we don't need that to show
        unset($meta['saved']);
        // and remove empty tags or arrays
        foreach ($meta as $key => $value) {
            if (empty($value) or is_array($value)) {
                unset($meta[$key]);
            }
        }
        // on request sanitize the output
        if ($this->sanitize == true) {
            array_walk($meta, 'esc_html');
        }
        return $meta;
    }
    /**
     * nggMeta::get_EXIF()
     * See also http://trac.wordpress.org/changeset/6313
     *
     * @return bool|array
     */
    function get_EXIF($object = false)
    {
        if (!$this->exif_data) {
            return false;
        }
        if (!is_array($this->exif_array)) {
            $meta = array();
            if (isset($this->exif_data['EXIF'])) {
                $exif = $this->exif_data['EXIF'];
                if (!empty($exif['FNumber'])) {
                    $meta['aperture'] = 'F ' . round($this->exif_frac2dec($exif['FNumber']), 2);
                }
                if (!empty($exif['Model'])) {
                    $meta['camera'] = trim($exif['Model']);
                }
                if (!empty($exif['DateTimeDigitized'])) {
                    $meta['created_timestamp'] = $this->exif_date2ts($exif['DateTimeDigitized']);
                } else {
                    if (!empty($exif['DateTimeOriginal'])) {
                        $meta['created_timestamp'] = $this->exif_date2ts($exif['DateTimeOriginal']);
                    } else {
                        if (!empty($exif['FileDateTime'])) {
                            $meta['created_timestamp'] = $this->exif_date2ts($exif['FileDateTime']);
                        }
                    }
                }
                if (!empty($exif['FocalLength'])) {
                    $meta['focal_length'] = $this->exif_frac2dec($exif['FocalLength']) . __(' mm', 'nggallery');
                }
                if (!empty($exif['ISOSpeedRatings'])) {
                    $meta['iso'] = $exif['ISOSpeedRatings'];
                }
                if (!empty($exif['ExposureTime'])) {
                    $meta['shutter_speed'] = $this->exif_frac2dec($exif['ExposureTime']);
                    $meta['shutter_speed'] = ($meta['shutter_speed'] > 0.0 and $meta['shutter_speed'] < 1.0) ? '1/' . round(1 / $meta['shutter_speed'], -1) : $meta['shutter_speed'];
                    $meta['shutter_speed'] .= __(' sec', 'nggallery');
                }
                // Bit 0 indicates the flash firing status. On some images taken on older iOS versions, this may be
                // incorrectly stored as an array.
                if (is_array($exif['Flash'])) {
                    $meta['flash'] = __('Fired', 'nggallery');
                } elseif (!empty($exif['Flash'])) {
                    $meta['flash'] = $exif['Flash'] & 1 ? __('Fired', 'nggallery') : __('Not fired', ' nggallery');
                }
            }
            // additional information
            if (isset($this->exif_data['IFD0'])) {
                $exif = $this->exif_data['IFD0'];
                if (!empty($exif['Model'])) {
                    $meta['camera'] = $exif['Model'];
                }
                if (!empty($exif['Make'])) {
                    $meta['make'] = $exif['Make'];
                }
                if (!empty($exif['ImageDescription'])) {
                    $meta['title'] = $this->utf8_encode($exif['ImageDescription']);
                }
                if (!empty($exif['Orientation'])) {
                    $meta['Orientation'] = $exif['Orientation'];
                }
            }
            // this is done by Windows
            if (isset($this->exif_data['WINXP'])) {
                $exif = $this->exif_data['WINXP'];
                if (!empty($exif['Title']) && empty($meta['title'])) {
                    $meta['title'] = $this->utf8_encode($exif['Title']);
                }
                if (!empty($exif['Author'])) {
                    $meta['author'] = $this->utf8_encode($exif['Author']);
                }
                if (!empty($exif['Keywords'])) {
                    $meta['keywords'] = $this->utf8_encode($exif['Keywords']);
                }
                if (!empty($exif['Subject'])) {
                    $meta['subject'] = $this->utf8_encode($exif['Subject']);
                }
                if (!empty($exif['Comments'])) {
                    $meta['caption'] = $this->utf8_encode($exif['Comments']);
                }
            }
            $this->exif_array = $meta;
        }
        // return one element if requested
        if ($object == true) {
            $value = isset($this->exif_array[$object]) ? $this->exif_array[$object] : false;
            return $value;
        }
        // on request sanitize the output
        if ($this->sanitize == true) {
            array_walk($this->exif_array, 'esc_html');
        }
        return $this->exif_array;
    }
    // convert a fraction string to a decimal
    function exif_frac2dec($str)
    {
        @(list($n, $d) = explode('/', $str));
        if (!empty($d)) {
            return $n / $d;
        }
        return $str;
    }
    // convert the exif date format to a unix timestamp
    function exif_date2ts($str)
    {
        $retval = is_numeric($str) ? $str : @strtotime($str);
        if (!$retval && $str) {
            @(list($date, $time) = explode(' ', trim($str)));
            @(list($y, $m, $d) = explode(':', $date));
            $retval = strtotime("{$y}-{$m}-{$d} {$time}");
        }
        return $retval;
    }
    /**
     * nggMeta::readIPTC() - IPTC Data Information for EXIF Display
     *
     * @param object $object (optional)
     * @return null|bool|array
     */
    function get_IPTC($object = false)
    {
        if (!$this->iptc_data) {
            return false;
        }
        if (!is_array($this->iptc_array)) {
            // --------- Set up Array Functions --------- //
            $iptcTags = array("2#005" => 'title', "2#007" => 'status', "2#012" => 'subject', "2#015" => 'category', "2#025" => 'keywords', "2#055" => 'created_date', "2#060" => 'created_time', "2#080" => 'author', "2#085" => 'position', "2#090" => 'city', "2#092" => 'location', "2#095" => 'state', "2#100" => 'country_code', "2#101" => 'country', "2#105" => 'headline', "2#110" => 'credit', "2#115" => 'source', "2#116" => 'copyright', "2#118" => 'contact', "2#120" => 'caption');
            $meta = array();
            foreach ($iptcTags as $key => $value) {
                if (isset($this->iptc_data[$key])) {
                    $meta[$value] = trim($this->utf8_encode(implode(", ", $this->iptc_data[$key])));
                }
            }
            $this->iptc_array = $meta;
        }
        // return one element if requested
        if ($object) {
            return isset($this->iptc_array[$object]) ? $this->iptc_array[$object] : NULL;
        }
        // on request sanitize the output
        if ($this->sanitize == true) {
            array_walk($this->iptc_array, 'esc_html');
        }
        return $this->iptc_array;
    }
    /**
     * nggMeta::extract_XMP()
     * get XMP DATA
     * code by Pekka Saarinen http://photography-on-the.net
     *
     * @param mixed $filename
     * @return bool|string
     */
    function extract_XMP($filename)
    {
        //TODO:Require a lot of memory, could be better
        ob_start();
        @readfile($filename);
        $source = ob_get_contents();
        ob_end_clean();
        $start = strpos($source, "<x:xmpmeta");
        $end = strpos($source, "</x:xmpmeta>");
        if (!$start === false && !$end === false) {
            $lenght = $end - $start;
            $xmp_data = substr($source, $start, $lenght + 12);
            unset($source);
            return $xmp_data;
        }
        unset($source);
        return false;
    }
    /**
     * nggMeta::get_XMP()
     *
     * @package Taken from http://php.net/manual/en/function.xml-parse-into-struct.php
     * @author Alf Marius Foss Olsen & Alex Rabe
     * @return bool|array
     *
     */
    function get_XMP($object = false)
    {
        if (!$this->xmp_data) {
            return false;
        }
        if (!is_array($this->xmp_array)) {
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            // Dont mess with my cAsE sEtTings
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            // Dont bother with empty info
            xml_parse_into_struct($parser, $this->xmp_data, $values);
            xml_parser_free($parser);
            $xmlarray = array();
            // The XML array
            $this->xmp_array = array();
            // The returned array
            $stack = array();
            // tmp array used for stacking
            $list_array = array();
            // tmp array for list elements
            $list_element = false;
            // rdf:li indicator
            foreach ($values as $val) {
                if ($val['type'] == "open") {
                    array_push($stack, $val['tag']);
                } elseif ($val['type'] == "close") {
                    // reset the compared stack
                    if ($list_element == false) {
                        array_pop($stack);
                    }
                    // reset the rdf:li indicator & array
                    $list_element = false;
                    $list_array = array();
                } elseif ($val['type'] == "complete") {
                    if ($val['tag'] == "rdf:li") {
                        // first go one element back
                        if ($list_element == false) {
                            array_pop($stack);
                        }
                        $list_element = true;
                        // do not parse empty tags
                        if (empty($val['value'])) {
                            continue;
                        }
                        // save it in our temp array
                        $list_array[] = $val['value'];
                        // in the case it's a list element we seralize it
                        $value = implode(",", $list_array);
                        $this->setArrayValue($xmlarray, $stack, $value);
                    } else {
                        array_push($stack, $val['tag']);
                        // do not parse empty tags
                        if (!empty($val['value'])) {
                            $this->setArrayValue($xmlarray, $stack, $val['value']);
                        }
                        array_pop($stack);
                    }
                }
            }
            // foreach
            // don't parse a empty array
            if (empty($xmlarray) || empty($xmlarray['x:xmpmeta'])) {
                return false;
            }
            // cut off the useless tags
            $xmlarray = $xmlarray['x:xmpmeta']['rdf:RDF']['rdf:Description'];
            // --------- Some values from the XMP format--------- //
            $xmpTags = array('xap:CreateDate' => 'created_timestamp', 'xap:ModifyDate' => 'last_modfied', 'xap:CreatorTool' => 'tool', 'dc:format' => 'format', 'dc:title' => 'title', 'dc:creator' => 'author', 'dc:subject' => 'keywords', 'dc:description' => 'caption', 'photoshop:AuthorsPosition' => 'position', 'photoshop:City' => 'city', 'photoshop:Country' => 'country');
            foreach ($xmpTags as $key => $value) {
                // if the kex exist
                if (isset($xmlarray[$key])) {
                    switch ($key) {
                        case 'xap:CreateDate':
                        case 'xap:ModifyDate':
                            $this->xmp_array[$value] = strtotime($xmlarray[$key]);
                            break;
                        default:
                            $this->xmp_array[$value] = $xmlarray[$key];
                    }
                }
            }
        }
        // return one element if requested
        if ($object != false) {
            return isset($this->xmp_array[$object]) ? $this->xmp_array[$object] : false;
        }
        // on request sanitize the output
        if ($this->sanitize == true) {
            array_walk($this->xmp_array, 'esc_html');
        }
        return $this->xmp_array;
    }
    function setArrayValue(&$array, $stack, $value)
    {
        if ($stack) {
            $key = array_shift($stack);
            $this->setArrayValue($array[$key], $stack, $value);
            return $array;
        } else {
            $array = $value;
        }
        return $array;
    }
    /**
     * nggMeta::get_META() - return a meta value form the available list
     *
     * @param string $object
     * @return mixed $value
     */
    function get_META($object = FALSE)
    {
        if ($value = $this->get_saved_meta($object)) {
            return $value;
        }
        if ($object == 'created_timestamp' && ($d = $this->get_IPTC('created_date')) && ($t = $this->get_IPTC('created_time'))) {
            return $this->exif_date2ts($d . ' ' . $t);
        }
        $order = apply_filters('ngg_metadata_parse_order', ['XMP', 'IPTC', 'EXIF']);
        foreach ($order as $method) {
            $method = 'get_' . $method;
            if (method_exists($this, $method) && ($value = $this->{$method}($object))) {
                return $value;
            }
        }
        return FALSE;
    }
    /**
     * nggMeta::i8n_name() -  localize the tag name
     *
     * @param mixed $key
     * @return string Translated $key
     */
    function i18n_name($key)
    {
        $tagnames = array('aperture' => __('Aperture', 'nggallery'), 'credit' => __('Credit', 'nggallery'), 'camera' => __('Camera', 'nggallery'), 'caption' => __('Caption', 'nggallery'), 'created_timestamp' => __('Date/Time', 'nggallery'), 'copyright' => __('Copyright', 'nggallery'), 'focal_length' => __('Focal length', 'nggallery'), 'iso' => __('ISO', 'nggallery'), 'shutter_speed' => __('Shutter speed', 'nggallery'), 'title' => __('Title', 'nggallery'), 'author' => __('Author', 'nggallery'), 'tags' => __('Tags', 'nggallery'), 'subject' => __('Subject', 'nggallery'), 'make' => __('Make', 'nggallery'), 'status' => __('Edit Status', 'nggallery'), 'category' => __('Category', 'nggallery'), 'keywords' => __('Keywords', 'nggallery'), 'created_date' => __('Date Created', 'nggallery'), 'created_time' => __('Time Created', 'nggallery'), 'position' => __('Author Position', 'nggallery'), 'city' => __('City', 'nggallery'), 'location' => __('Location', 'nggallery'), 'state' => __('Province/State', 'nggallery'), 'country_code' => __('Country code', 'nggallery'), 'country' => __('Country', 'nggallery'), 'headline' => __('Headline', 'nggallery'), 'credit' => __('Credit', 'nggallery'), 'source' => __('Source', 'nggallery'), 'copyright' => __('Copyright Notice', 'nggallery'), 'contact' => __('Contact', 'nggallery'), 'last_modfied' => __('Last modified', 'nggallery'), 'tool' => __('Program tool', 'nggallery'), 'format' => __('Format', 'nggallery'), 'width' => __('Image Width', 'nggallery'), 'height' => __('Image Height', 'nggallery'), 'flash' => __('Flash', 'nggallery'));
        if (isset($tagnames[$key])) {
            $key = $tagnames[$key];
        }
        return $key;
    }
    /**
     * Return the Timestamp from the image , if possible it's read from exif data
     * @return string
     */
    function get_date_time()
    {
        // Try getting the created_timestamp field
        $date = $this->exif_date2ts($this->get_META('created_timestamp'));
        if (!$date) {
            $image_path = C_Gallery_Storage::get_instance()->get_backup_abspath($this->image);
            $date = @filectime($image_path);
        }
        // Failback
        if (!$date) {
            $date = time();
        }
        // Return the MySQL format
        $date_time = date('Y-m-d H:i:s', $date);
        return $date_time;
    }
    /**
     * This function return the most common metadata, via a filter we can add more
     * Reason : GD manipulation removes that options
     *
     * @since V1.4.0
     * @return array|false
     */
    function get_common_meta()
    {
        global $wpdb;
        $meta = array('aperture' => 0, 'credit' => '', 'camera' => '', 'caption' => '', 'created_timestamp' => 0, 'copyright' => '', 'focal_length' => 0, 'iso' => 0, 'shutter_speed' => 0, 'flash' => 0, 'title' => '', 'keywords' => '');
        $meta = apply_filters('ngg_read_image_metadata', $meta);
        // meta should be still an array
        if (!is_array($meta)) {
            return false;
        }
        foreach ($meta as $key => $value) {
            $meta[$key] = $this->get_META($key);
        }
        //let's add now the size of the image
        $meta['width'] = $this->size[0];
        $meta['height'] = $this->size[1];
        return $meta;
    }
    /**
     * If needed sanitize each value before output
     *
     * @return void
     */
    function sanitize()
    {
        $this->sanitize = true;
    }
    /**
     * Wrapper to utf8_encode() that avoids double encoding
     *
     * Regex adapted from http://www.w3.org/International/questions/qa-forms-utf-8.en.php
     * to determine if the given string is already UTF-8. mb_detect_encoding() is not
     * always available and is limited in accuracy
     *
     * @param string $str
     * @return string
     */
    function utf8_encode($str)
    {
        $is_utf8 = preg_match('%^(?:
              [\\x09\\x0A\\x0D\\x20-\\x7E]            # ASCII
            | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte
            |  \\xE0[\\xA0-\\xBF][\\x80-\\xBF]        # excluding overlongs
            | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte
            |  \\xED[\\x80-\\x9F][\\x80-\\xBF]        # excluding surrogates
            |  \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}     # planes 1-3
            | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15
            |  \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}     # plane 16
            )*$%xs', $str);
        if (!$is_utf8) {
            utf8_encode($str);
        }
        return $str;
    }
}
/**
 * gd.thumbnail.inc.php
 * 
 * @author 		Ian Selby (ian@gen-x-design.com)
 * @copyright 	Copyright 2006-2011
 * @version 	1.3.0 (based on 1.1.3)
 * @modded      by Alex Rabe
 * 
 */
/**
 * PHP class for dynamically resizing, cropping, and rotating images for thumbnail purposes and either displaying them on-the-fly or saving them.
 *
 */
class C_NggLegacy_Thumbnail
{
    /**
     * Error message to display, if any
     *
     * @var string
     */
    var $errmsg;
    /**
     * Whether or not there is an error
     *
     * @var boolean
     */
    var $error;
    /**
     * Format of the image file
     *
     * @var string
     */
    var $format;
    /**
     * File name and path of the image file
     *
     * @var string
     */
    var $fileName;
    /**
     * Current dimensions of working image
     *
     * @var array
     */
    var $currentDimensions;
    /**
     * New dimensions of working image
     *
     * @var array
     */
    var $newDimensions;
    /**
     * Image resource for newly manipulated image
     *
     * @var resource
     * @access private
     */
    var $newImage;
    /**
     * Image resource for image before previous manipulation
     *
     * @var resource
     * @access private
     */
    var $oldImage;
    /**
     * Image resource for image being currently manipulated
     *
     * @var resource
     * @access private
     */
    var $workingImage;
    /**
     * Percentage to resize image by
     *
     * @var int
     * @access private
     */
    var $percent;
    /**
     * Maximum width of image during resize
     *
     * @var int
     * @access private
     */
    var $maxWidth;
    /**
     * Maximum height of image during resize
     *
     * @var int
     * @access private
     */
    var $maxHeight;
    /**
     * Image for Watermark
     *
     * @var string
     * 
     */
    var $watermarkImgPath;
    /**
     * Text for Watermark
     *
     * @var string
     * 
     */
    var $watermarkText;
    /**
     * Image Resource ID for Watermark
     *
     * @var string
     * 
     */
    function __construct($fileName, $no_ErrorImage = false)
    {
        //make sure the GD library is installed
        if (!function_exists("gd_info")) {
            echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
            echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
            throw new E_No_Image_Library_Exception();
        }
        //initialize variables
        $this->errmsg = '';
        $this->error = false;
        $this->currentDimensions = array();
        $this->newDimensions = array();
        $this->fileName = $fileName;
        $this->percent = 100;
        $this->maxWidth = 0;
        $this->maxHeight = 0;
        $this->watermarkImgPath = '';
        $this->watermarkText = '';
        //check to see if file exists
        if (!@file_exists($this->fileName)) {
            $this->errmsg = 'File not found';
            $this->error = true;
        } elseif (!is_readable($this->fileName)) {
            $this->errmsg = 'File is not readable';
            $this->error = true;
        }
        $image_size = null;
        //if there are no errors, determine the file format
        if ($this->error == false) {
            //	        set_time_limit(30);
            @ini_set('memory_limit', -1);
            $image_size = @getimagesize($this->fileName);
            if (isset($image_size) && is_array($image_size)) {
                $extensions = [IMAGETYPE_GIF => 'GIF', IMAGETYPE_JPEG => 'JPG', IMAGETYPE_PNG => 'PNG', IMAGETYPE_WEBP => 'WEBP'];
                $extension = array_key_exists($image_size[2], $extensions) ? $extensions[$image_size[2]] : '';
                if ($extension) {
                    $this->format = $extension;
                } else {
                    $this->errmsg = 'Unknown file format';
                    $this->error = true;
                }
            } else {
                $this->errmsg = 'File is not an image';
                $this->error = true;
            }
        }
        // increase memory-limit if possible, GD needs this for large images
        if (!extension_loaded('suhosin')) {
            @ini_set('memory_limit', '512M');
        }
        if ($this->error == false) {
            // Check memory consumption if file exists
            $this->checkMemoryForImage($this->fileName);
        }
        //initialize resources if no errors
        if ($this->error == false) {
            $img_err = null;
            switch ($this->format) {
                case 'GIF':
                    if (function_exists('ImageCreateFromGif')) {
                        $this->oldImage = @ImageCreateFromGif($this->fileName);
                    } else {
                        $img_err = __('Support for GIF format is missing.', 'nggallery');
                    }
                    break;
                case 'JPG':
                    if (function_exists('ImageCreateFromJpeg')) {
                        $this->oldImage = @ImageCreateFromJpeg($this->fileName);
                    } else {
                        $img_err = __('Support for JPEG format is missing.', 'nggallery');
                    }
                    break;
                case 'PNG':
                    if (function_exists('ImageCreateFromPng')) {
                        $this->oldImage = @ImageCreateFromPng($this->fileName);
                    } else {
                        $img_err = __('Support for PNG format is missing.', 'nggallery');
                    }
                    break;
                case 'WEBP':
                    if (function_exists('imagecreatefromwebp')) {
                        $this->oldImage = @imagecreatefromwebp($this->fileName);
                    } else {
                        $img_err = __('Support for WEBP format is missing.', 'nggallery');
                    }
                    break;
            }
            if (!$this->oldImage) {
                if ($img_err == null) {
                    $img_err = __('Check memory limit', 'nggallery');
                }
                $this->errmsg = sprintf(__('Create Image failed. %1$s', 'nggallery'), $img_err);
                $this->error = true;
            } else {
                $this->currentDimensions = array('width' => $image_size[0], 'height' => $image_size[1]);
                $this->newImage = $this->oldImage;
            }
        }
        if ($this->error == true) {
            if (!$no_ErrorImage) {
                $this->showErrorImage();
            }
            return;
        }
    }
    /**
     * Calculate the memory limit
     * @param string $filename
     *
     */
    function checkMemoryForImage($filename)
    {
        $imageInfo = getimagesize($filename);
        switch ($this->format) {
            case 'GIF':
                // measured factor 1 is better
                $CHANNEL = 1;
                break;
            case 'JPG':
                $CHANNEL = $imageInfo['channels'];
                break;
            case 'PNG':
                // didn't get the channel for png
                $CHANNEL = 3;
                break;
            case 'WEBP':
                $CHANNEL = $imageInfo['bits'];
                break;
        }
        $bits = !empty($imageInfo['bits']) ? $imageInfo['bits'] : 32;
        // imgInfo[bits] is not always available
        return $this->checkMemoryForData($imageInfo[0], $imageInfo[1], $CHANNEL, $bits);
    }
    function checkMemoryForData($width, $height, $channels = null, $bits = null)
    {
        $imageInfo = getimagesize($this->fileName);
        if ($channels == null) {
            switch ($this->format) {
                case 'GIF':
                    // measured factor 1 is better
                    $channels = 1;
                    break;
                case 'JPG':
                    $channels = $imageInfo['channels'];
                    break;
                case 'PNG':
                    // didn't get the channel for png
                    $channels = 3;
                    break;
                case 'WEBP':
                    $channels = $imageInfo['bits'];
                    break;
            }
        }
        if ($bits == null) {
            $bits = !empty($imageInfo['bits']) ? $imageInfo['bits'] : 32;
        }
        // imgInfo[bits] is not always available
        if (function_exists('memory_get_usage') && ini_get('memory_limit')) {
            $MB = 1048576;
            // number of bytes in 1M
            $K64 = 65536;
            // number of bytes in 64K
            $TWEAKFACTOR = 1.68;
            // Or whatever works for you
            $memoryNeeded = round((doubleval($width * $height * $bits * $channels) / 8 + $K64) * $TWEAKFACTOR);
            $memoryNeeded = memory_get_usage() + $memoryNeeded;
            // get memory limit
            $memory_limit = ini_get('memory_limit');
            // PHP docs : Note that to have no memory limit, set this directive to -1.
            if ($memory_limit == -1) {
                return true;
            }
            // Just check megabyte limits, not higher
            if (strtolower(substr($memory_limit, -1)) == 'm') {
                if ($memory_limit != '') {
                    $memory_limit = intval(substr($memory_limit, 0, -1)) * 1024 * 1024;
                }
                if ($memoryNeeded > $memory_limit) {
                    $memoryNeeded = round($memoryNeeded / 1024 / 1024, 2);
                    $this->errmsg = 'Exceed Memory limit. Require : ' . $memoryNeeded . " MByte";
                    $this->error = true;
                    return false;
                }
            }
        }
        return true;
    }
    function __destruct()
    {
        $this->destruct();
    }
    /**
     * Must be called to free up allocated memory after all manipulations are done
     */
    function destruct()
    {
        if (is_resource($this->newImage) || $this->newImage instanceof GdImage) {
            @imagedestroy($this->newImage);
        }
        if (is_resource($this->oldImage) || $this->oldImage instanceof GdImage) {
            @imagedestroy($this->oldImage);
        }
        if (is_resource($this->workingImage) || $this->workingImage instanceof GdImage) {
            @imagedestroy($this->workingImage);
        }
    }
    /**
     * Returns the current width of the image
     *
     * @return int
     */
    function getCurrentWidth()
    {
        return $this->currentDimensions['width'];
    }
    /**
     * Returns the current height of the image
     *
     * @return int
     */
    function getCurrentHeight()
    {
        return $this->currentDimensions['height'];
    }
    /**
     * Calculates new image width
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcWidth($width, $height)
    {
        $newWp = 100 * $this->maxWidth / $width;
        $newHeight = $height * $newWp / 100;
        if (intval($newHeight) == $this->maxHeight - 1) {
            $newHeight = $this->maxHeight;
        }
        return array('newWidth' => intval($this->maxWidth), 'newHeight' => intval($newHeight));
    }
    /**
     * Calculates new image height
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcHeight($width, $height)
    {
        $newHp = 100 * $this->maxHeight / $height;
        $newWidth = $width * $newHp / 100;
        if (intval($newWidth) == $this->maxWidth - 1) {
            $newWidth = $this->maxWidth;
        }
        return array('newWidth' => intval($newWidth), 'newHeight' => intval($this->maxHeight));
    }
    /**
     * Calculates new image size based on percentage
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcPercent($width, $height, $percent = -1)
    {
        if ($percent == -1) {
            $percent = $this->percent;
        }
        $newWidth = $width * $percent / 100;
        $newHeight = $height * $percent / 100;
        return array('newWidth' => intval($newWidth), 'newHeight' => intval($newHeight));
    }
    /**
     * Calculates new image size based on width and height, while constraining to maxWidth and maxHeight
     *
     * @param int $width
     * @param int $height
     */
    function calcImageSize($width, $height)
    {
        // $width and $height are the CURRENT image resolutions
        $ratio_w = $this->maxWidth / $width;
        $ratio_h = $this->maxHeight / $height;
        if ($ratio_w >= $ratio_h) {
            $width = $this->maxWidth;
            $height = (int) round($height * $ratio_h, 0);
        } else {
            $height = $this->maxHeight;
            $width = (int) round($width * $ratio_w, 0);
        }
        $this->newDimensions = array('newWidth' => $width, 'newHeight' => $height);
    }
    /**
     * Calculates new image size based percentage
     *
     * @param int $width
     * @param int $height
     */
    function calcImageSizePercent($width, $height)
    {
        if ($this->percent > 0) {
            $this->newDimensions = $this->calcPercent($width, $height);
        }
    }
    /**
     * Displays error image
     *
     */
    function showErrorImage()
    {
        header('Content-type: image/png');
        $errImg = ImageCreate(220, 25);
        $bgColor = imagecolorallocate($errImg, 0, 0, 0);
        $fgColor1 = imagecolorallocate($errImg, 255, 255, 255);
        $fgColor2 = imagecolorallocate($errImg, 255, 0, 0);
        imagestring($errImg, 3, 6, 6, 'Error:', $fgColor2);
        imagestring($errImg, 3, 55, 6, $this->errmsg, $fgColor1);
        imagepng($errImg);
        imagedestroy($errImg);
    }
    /**
     * Resizes image to fixed Width x Height
     * 
     * @param int $Width
     * @param int $Height
     * @param int $deprecated Unused
     */
    function resizeFix($Width = 0, $Height = 0, $deprecated = 3)
    {
        if (!$this->checkMemoryForData($Width, $Height)) {
            return;
        }
        $this->newWidth = $Width;
        $this->newHeight = $Height;
        if (function_exists("ImageCreateTrueColor")) {
            $this->workingImage = ImageCreateTrueColor($this->newWidth, $this->newHeight);
        } else {
            $this->workingImage = ImageCreate($this->newWidth, $this->newHeight);
        }
        //		ImageCopyResampled(
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newWidth;
        $this->currentDimensions['height'] = $this->newHeight;
    }
    /**
     * Resizes image to maxWidth x maxHeight
     *
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $deprecated Unused
     */
    function resize($maxWidth = 0, $maxHeight = 0, $deprecated = 3)
    {
        if (!$this->checkMemoryForData($maxWidth, $maxHeight)) {
            return;
        }
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists("ImageCreateTrueColor")) {
            $this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        } else {
            $this->workingImage = ImageCreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        }
        //		ImageCopyResampled(
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newDimensions['newWidth'], $this->newDimensions['newHeight'], $this->currentDimensions['width'], $this->currentDimensions['height']);
        ImageDestroy($this->oldImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newDimensions['newWidth'];
        $this->currentDimensions['height'] = $this->newDimensions['newHeight'];
    }
    /**
     * Resizes the image by $percent percent
     *
     * @param int $percent
     */
    function resizePercent($percent = 0)
    {
        $dims = $this->calcPercent($this->currentDimensions['width'], $this->currentDimensions['height'], $percent);
        if (!$this->checkMemoryForData($dims['newWidth'], $dims['newHeight'])) {
            return;
        }
        $this->percent = $percent;
        $this->calcImageSizePercent($this->currentDimensions['width'], $this->currentDimensions['height']);
        if (function_exists("ImageCreateTrueColor")) {
            $this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        } else {
            $this->workingImage = ImageCreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        }
        $this->ImageCopyResampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newDimensions['newWidth'], $this->newDimensions['newHeight'], $this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newDimensions['newWidth'];
        $this->currentDimensions['height'] = $this->newDimensions['newHeight'];
    }
    /**
     * Crops the image from calculated center in a square of $cropSize pixels
     *
     * @param int $cropSize
     */
    function cropFromCenter($cropSize)
    {
        if ($cropSize > $this->currentDimensions['width']) {
            $cropSize = $this->currentDimensions['width'];
        }
        if ($cropSize > $this->currentDimensions['height']) {
            $cropSize = $this->currentDimensions['height'];
        }
        $cropX = intval(($this->currentDimensions['width'] - $cropSize) / 2);
        $cropY = intval(($this->currentDimensions['height'] - $cropSize) / 2);
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists("ImageCreateTrueColor")) {
            $this->workingImage = ImageCreateTrueColor($cropSize, $cropSize);
        } else {
            $this->workingImage = ImageCreate($cropSize, $cropSize);
        }
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $cropX, $cropY, $cropSize, $cropSize, $cropSize, $cropSize);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $cropSize;
        $this->currentDimensions['height'] = $cropSize;
    }
    /**
     * Advanced cropping function that crops an image using $startX and $startY as the upper-left hand corner.
     *
     * @param int $startX
     * @param int $startY
     * @param int $width
     * @param int $height
     */
    function crop($startX, $startY, $width, $height)
    {
        if (!$this->checkMemoryForData($width, $height)) {
            return;
        }
        //make sure the cropped area is not greater than the size of the image
        if ($width > $this->currentDimensions['width']) {
            $width = $this->currentDimensions['width'];
        }
        if ($height > $this->currentDimensions['height']) {
            $height = $this->currentDimensions['height'];
        }
        //make sure not starting outside the image
        if ($startX + $width > $this->currentDimensions['width']) {
            $startX = $this->currentDimensions['width'] - $width;
        }
        if ($startY + $height > $this->currentDimensions['height']) {
            $startY = $this->currentDimensions['height'] - $height;
        }
        if ($startX < 0) {
            $startX = 0;
        }
        if ($startY < 0) {
            $startY = 0;
        }
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists("ImageCreateTrueColor")) {
            $this->workingImage = ImageCreateTrueColor($width, $height);
        } else {
            $this->workingImage = ImageCreate($width, $height);
        }
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $startX, $startY, $width, $height, $width, $height);
        ImageDestroy($this->oldImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $width;
        $this->currentDimensions['height'] = $height;
    }
    /**
     * Outputs the image to the screen, or saves to $name if supplied.  Quality of JPEG images can be controlled with the $quality variable
     *
     * @param int $quality
     * @param string $name
     */
    function show($quality = 100, $name = '')
    {
        switch ($this->format) {
            case 'GIF':
                if ($name != '') {
                    @ImageGif($this->newImage, $name) or $this->error = true;
                } else {
                    header('Content-type: image/gif');
                    ImageGif($this->newImage);
                }
                break;
            case 'JPG':
                if ($name != '') {
                    @ImageJpeg($this->newImage, $name, $quality) or $this->error = true;
                } else {
                    header('Content-type: image/jpeg');
                    ImageJpeg($this->newImage, NULL, $quality);
                }
                break;
            case 'PNG':
                if ($name != '') {
                    @ImagePng($this->newImage, $name) or $this->error = true;
                } else {
                    header('Content-type: image/png');
                    ImagePng($this->newImage);
                }
                break;
            case 'WEBP':
                if ($name != '') {
                    $this->error = !@imagewebp($this->newImage, $name);
                } else {
                    header('Content-type: image/webp');
                    imagewebp($this->newImage);
                }
                break;
        }
    }
    /**
     * Saves image as $name (can include file path), with quality of # percent if file is a jpeg
     *
     * @param string $name
     * @param int $quality
     * @return bool errorstate
     */
    function save($name, $quality = 100)
    {
        $this->show($quality, $name);
        if ($this->error == true) {
            $this->errmsg = 'Create Image failed. Check safe mode settings';
            return false;
        }
        if (function_exists('do_action')) {
            do_action('ngg_ajax_image_save', $name);
        }
        return true;
    }
    /**
     * Creates Apple-style reflection under image, optionally adding a border to main image
     *
     * @param int $percent
     * @param int $reflection
     * @param int $white
     * @param bool $border
     * @param string $borderColor
     */
    function createReflection($percent, $reflection, $white, $border = true, $borderColor = '#a4a4a4')
    {
        $width = $this->currentDimensions['width'];
        $height = $this->currentDimensions['height'];
        $reflectionHeight = intval($height * ($reflection / 100));
        $newHeight = $height + $reflectionHeight;
        $reflectedPart = $height * ($percent / 100);
        $this->workingImage = ImageCreateTrueColor($width, $newHeight);
        ImageAlphaBlending($this->workingImage, true);
        $colorToPaint = ImageColorAllocateAlpha($this->workingImage, 255, 255, 255, 0);
        ImageFilledRectangle($this->workingImage, 0, 0, $width, $newHeight, $colorToPaint);
        imagecopyresampled($this->workingImage, $this->newImage, 0, 0, 0, $reflectedPart, $width, $reflectionHeight, $width, $height - $reflectedPart);
        $this->imageFlipVertical();
        imagecopy($this->workingImage, $this->newImage, 0, 0, 0, 0, $width, $height);
        imagealphablending($this->workingImage, true);
        for ($i = 0; $i < $reflectionHeight; $i++) {
            $colorToPaint = imagecolorallocatealpha($this->workingImage, 255, 255, 255, ($i / $reflectionHeight * -1 + 1) * $white);
            imagefilledrectangle($this->workingImage, 0, $height + $i, $width, $height + $i, $colorToPaint);
        }
        if ($border == true) {
            $rgb = $this->hex2rgb($borderColor, false);
            $colorToPaint = imagecolorallocate($this->workingImage, $rgb[0], $rgb[1], $rgb[2]);
            imageline($this->workingImage, 0, 0, $width, 0, $colorToPaint);
            //top line
            imageline($this->workingImage, 0, $height, $width, $height, $colorToPaint);
            //bottom line
            imageline($this->workingImage, 0, 0, 0, $height, $colorToPaint);
            //left line
            imageline($this->workingImage, $width - 1, 0, $width - 1, $height, $colorToPaint);
            //right line
        }
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $width;
        $this->currentDimensions['height'] = $newHeight;
    }
    /**
     * Flip an image.
     *
     * @param bool $horz flip the image in horizontal mode
     * @param bool $vert flip the image in vertical mode
     * @return true
     */
    function flipImage($horz = false, $vert = false)
    {
        $sx = $vert ? $this->currentDimensions['width'] - 1 : 0;
        $sy = $horz ? $this->currentDimensions['height'] - 1 : 0;
        $sw = $vert ? -$this->currentDimensions['width'] : $this->currentDimensions['width'];
        $sh = $horz ? -$this->currentDimensions['height'] : $this->currentDimensions['height'];
        $this->workingImage = imagecreatetruecolor($this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $sx, $sy, $this->currentDimensions['width'], $this->currentDimensions['height'], $sw, $sh);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        return true;
    }
    /**
     * Rotate an image clockwise or counter clockwise
     *
     * @param string $dir Either CW or CCW
     * @return bool
     */
    function rotateImage($dir = 'CW')
    {
        $angle = $dir == 'CW' ? 90 : -90;
        return $this->rotateImageAngle($angle);
    }
    /**
     * Rotate an image clockwise or counter clockwise
     *
     * @param int $angle Degrees to rotate the target image
     * @return bool
     */
    function rotateImageAngle($angle = 90)
    {
        if (function_exists('imagerotate')) {
            $this->currentDimensions['width'] = imagesx($this->workingImage);
            $this->currentDimensions['height'] = imagesy($this->workingImage);
            $this->oldImage = $this->workingImage;
            // imagerotate() rotates CCW ;
            // See for help: https://evertpot.com/115/
            $this->newImage = imagerotate($this->oldImage, 360 - $angle, 0);
            return true;
        }
        $this->workingImage = imagecreatetruecolor($this->currentDimensions['height'], $this->currentDimensions['width']);
        imagealphablending($this->workingImage, false);
        imagesavealpha($this->workingImage, true);
        switch ($angle) {
            case 90:
                for ($x = 0; $x < $this->currentDimensions['width']; $x++) {
                    for ($y = 0; $y < $this->currentDimensions['height']; $y++) {
                        if (!imagecopy($this->workingImage, $this->oldImage, $this->currentDimensions['height'] - $y - 1, $x, $x, $y, 1, 1)) {
                            return false;
                        }
                    }
                }
                break;
            case -90:
                for ($x = 0; $x < $this->currentDimensions['width']; $x++) {
                    for ($y = 0; $y < $this->currentDimensions['height']; $y++) {
                        if (!imagecopy($this->workingImage, $this->oldImage, $y, $this->currentDimensions['width'] - $x - 1, $x, $y, 1, 1)) {
                            return false;
                        }
                    }
                }
                break;
            default:
                return false;
        }
        $this->currentDimensions['width'] = imagesx($this->workingImage);
        $this->currentDimensions['height'] = imagesy($this->workingImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        return true;
    }
    /**
     * Inverts working image, used by reflection function
     * 
     * @access	private
     */
    function imageFlipVertical()
    {
        $x_i = imagesx($this->workingImage);
        $y_i = imagesy($this->workingImage);
        for ($x = 0; $x < $x_i; $x++) {
            for ($y = 0; $y < $y_i; $y++) {
                imagecopy($this->workingImage, $this->workingImage, $x, $y_i - $y - 1, $x, $y, 1, 1);
            }
        }
    }
    /**
     * Converts hexidecimal color value to rgb values and returns as array/string
     *
     * @param string $hex
     * @param bool $asString
     * @return array|string
     */
    function hex2rgb($hex, $asString = false)
    {
        // strip off any leading #
        if (0 === strpos($hex, '#')) {
            $hex = substr($hex, 1);
        } else {
            if (0 === strpos($hex, '&H')) {
                $hex = substr($hex, 2);
            }
        }
        // break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2) - 1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);
        // convert each tuple to decimal
        $rgb[0] = isset($rgb[0]) ? hexdec($rgb[0]) : 0;
        $rgb[1] = isset($rgb[1]) ? hexdec($rgb[1]) : 0;
        $rgb[2] = isset($rgb[2]) ? hexdec($rgb[2]) : 0;
        return $asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb;
    }
    /**
     * Based on the Watermark function by Marek Malcherek  
     * http://www.malcherek.de
     *
     * @param string $color
     * @param string $wmFont
     * @param int $wmSize
     * @param int $wmOpaque
     */
    function watermarkCreateText($color, $wmFont, $wmSize = 10, $wmOpaque = 90)
    {
        if (!$color) {
            $color = '000000';
        }
        // set font path
        $wmFontPath = NGGALLERY_ABSPATH . "fonts/" . $wmFont;
        if (!is_readable($wmFontPath)) {
            return;
        }
        // This function requires both the GD library and the FreeType library.
        if (!function_exists('ImageTTFBBox')) {
            return;
        }
        $words = preg_split('/ /', $this->watermarkText);
        $lines = array();
        $line = '';
        $watermark_image_width = 0;
        // attempt adding a new word until the width is too large; then start a new line and start again
        foreach ($words as $word) {
            // sanitize the text being input; imagettftext() can be sensitive
            $TextSize = $this->ImageTTFBBoxDimensions($wmSize, 0, $this->correct_gd_unc_path($wmFontPath), $line . preg_replace('~^(&([a-zA-Z0-9]);)~', htmlentities('${1}'), mb_convert_encoding($word, "HTML-ENTITIES", "UTF-8")));
            if ($watermark_image_width == 0) {
                $watermark_image_width = $TextSize['width'];
            }
            if ($TextSize['width'] > $this->newDimensions['newWidth']) {
                $lines[] = trim($line);
                $line = '';
            } else {
                if ($TextSize['width'] > $watermark_image_width) {
                    $watermark_image_width = $TextSize['width'];
                }
            }
            $line .= $word . ' ';
        }
        $lines[] = trim($line);
        // use this string to determine our largest possible line height
        $line_dimensions = $this->ImageTTFBBoxDimensions($wmSize, 0, $this->correct_gd_unc_path($wmFontPath), 'MXQJALYmxqjabdfghjklpqry019`@$^&*(,!132');
        $line_height = (float) $line_dimensions['height'] * 1.05;
        // Create an image to apply our text to
        $this->workingImage = ImageCreateTrueColor($watermark_image_width, (int) (count($lines) * $line_height));
        ImageSaveAlpha($this->workingImage, true);
        ImageAlphaBlending($this->workingImage, false);
        $bgText = imagecolorallocatealpha($this->workingImage, 255, 255, 255, 127);
        imagefill($this->workingImage, 0, 0, $bgText);
        $wmTransp = 127 - (int) $wmOpaque * 1.27;
        $rgb = $this->hex2rgb($color, false);
        $TextColor = imagecolorallocatealpha($this->workingImage, (int) $rgb[0], (int) $rgb[1], (int) $rgb[2], (int) $wmTransp);
        // Put text on the image, line-by-line
        $y_pos = $wmSize;
        foreach ($lines as $line) {
            imagettftext($this->workingImage, $wmSize, 0, 0, $y_pos, $TextColor, $this->correct_gd_unc_path($wmFontPath), $line);
            $y_pos += $line_height;
        }
        $this->watermarkImgPath = $this->workingImage;
        return;
    }
    /**
     * Returns a path that can be used with imagettftext() and ImageTTFBBox()
     *
     * imagettftext() and ImageTTFBBox() cannot load resources from Windows UNC paths
     * and require they be mangled to be like //server\filename instead of \\server\filename
     * @param string $path Absolute file path
     * @return string $path Mangled absolute file path
     */
    public function correct_gd_unc_path($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && substr($path, 0, 2) === '\\\\') {
            $path = ltrim($path, '\\\\');
            $path = '//' . $path;
        }
        return $path;
    }
    /**
     * Calculates the width & height dimensions of ImageTTFBBox().
     *
     * Note: ImageTTFBBox() is unreliable with large font sizes
     * @param $wmSize
     * @param $fontAngle
     * @param $wmFontPath
     * @param $text
     * @return array
     */
    function ImageTTFBBoxDimensions($wmSize, $fontAngle, $wmFontPath, $text)
    {
        $box = @ImageTTFBBox($wmSize, $fontAngle, $this->correct_gd_unc_path($wmFontPath), $text);
        $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
        $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
        $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
        $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
        return array("width" => $max_x - $min_x, "height" => $max_y - $min_y);
    }
    function applyFilter($filterType)
    {
        $args = func_get_args();
        array_unshift($args, $this->newImage);
        return call_user_func_array('imagefilter', $args);
    }
    /**
     * Modfied Watermark function by Steve Peart 
     * http://parasitehosting.com/
     *
     * @param string $relPOS
     * @param int $xPOS
     * @param int $yPOS
     */
    function watermarkImage($relPOS = 'botRight', $xPOS = 0, $yPOS = 0)
    {
        // if it's a resource ID take it as watermark text image
        if (is_resource($this->watermarkImgPath) || $this->watermarkImgPath instanceof GdImage) {
            $this->workingImage = $this->watermarkImgPath;
        } else {
            // (possibly) search for the file from the document root
            if (!is_file($this->watermarkImgPath)) {
                $fs = C_Fs::get_instance();
                if (is_file($fs->join_paths($fs->get_document_root('content'), $this->watermarkImgPath))) {
                    $this->watermarkImgPath = $fs->get_document_root('content') . $this->watermarkImgPath;
                }
            }
            // Would you really want to use anything other than a png?
            $this->workingImage = @imagecreatefrompng($this->watermarkImgPath);
            // if it's not a valid file die...
            if (empty($this->workingImage) or !$this->workingImage) {
                return;
            }
        }
        imagealphablending($this->workingImage, false);
        imagesavealpha($this->workingImage, true);
        $sourcefile_width = imageSX($this->oldImage);
        $sourcefile_height = imageSY($this->oldImage);
        $watermarkfile_width = imageSX($this->workingImage);
        $watermarkfile_height = imageSY($this->workingImage);
        switch (substr($relPOS, 0, 3)) {
            case 'top':
                $dest_y = 0 + $yPOS;
                break;
            case 'mid':
                $dest_y = $sourcefile_height / 2 - $watermarkfile_height / 2;
                break;
            case 'bot':
                $dest_y = $sourcefile_height - $watermarkfile_height - $yPOS;
                break;
            default:
                $dest_y = 0;
                break;
        }
        switch (substr($relPOS, 3)) {
            case 'Left':
                $dest_x = 0 + $xPOS;
                break;
            case 'Center':
                $dest_x = $sourcefile_width / 2 - $watermarkfile_width / 2;
                break;
            case 'Right':
                $dest_x = $sourcefile_width - $watermarkfile_width - $xPOS;
                break;
            default:
                $dest_x = 0;
                break;
        }
        // debug
        // $this->errmsg = 'X '.$dest_x.' Y '.$dest_y;
        // $this->showErrorImage();
        // if a gif, we have to upsample it to a truecolor image
        if ($this->format == 'GIF') {
            $tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);
            imagecopy($tempimage, $this->oldImage, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);
            $this->newImage = $tempimage;
        }
        $this->imagecopymerge_alpha($this->newImage, $this->workingImage, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, 100);
    }
    /**
     * Wrapper to imagecopymerge() that allows PNG transparency
     */
    function imagecopymerge_alpha($destination_image, $source_image, $destination_x, $destination_y, $source_x, $source_y, $source_w, $source_h, $pct)
    {
        $cut = imagecreatetruecolor($source_w, $source_h);
        imagecopy($cut, $destination_image, 0, 0, (int) $destination_x, (int) $destination_y, (int) $source_w, (int) $source_h);
        imagecopy($cut, $source_image, 0, 0, $source_x, $source_y, $source_w, $source_h);
        imagecopymerge($destination_image, $cut, (int) $destination_x, (int) $destination_y, 0, 0, (int) $source_w, (int) $source_h, (int) $pct);
    }
    /**
     * Modfied imagecopyresampled function to save transparent images
     * See : http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
     * @since 1.9.0
     * 
     * @param resource $dst_image
     * @param resource $src_image
     * @param int $dst_x
     * @param int $dst_y
     * @param int $src_x
     * @param int $src_y
     * @param int $dst_w
     * @param int $dst_h
     * @param int $src_w
     * @param int $src_h
     * @return bool
     */
    function imagecopyresampled(&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        // Check if this image is PNG or GIF, then set if Transparent
        if ($this->format == 'GIF' || $this->format == 'PNG') {
            imagealphablending($dst_image, false);
            imagesavealpha($dst_image, true);
            $transparent = imagecolorallocatealpha($dst_image, 255, 255, 255, 127);
            imagefilledrectangle($dst_image, 0, 0, $dst_w, $dst_h, $transparent);
        }
        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        return true;
    }
}
/**
 * Provides methods to C_Gallery_Storage related to dynamic images, thumbnails, clones, etc
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Dynamic extends Mixin
{
    /**
     * Generates a specific size for an image
     * @param int|object|C_Image $image
     * @param string $size
     * @param array|null $params (optional)
     * @param bool $skip_defaults (optional)
     * @return bool|object
     */
    function generate_image_size($image, $size, $params = null, $skip_defaults = false)
    {
        $retval = FALSE;
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $params = $this->object->get_image_size_params($image, $size, $params, $skip_defaults);
            $settings = C_NextGen_Settings::get_instance();
            // Get the image filename
            $filename = $this->object->get_image_abspath($image, 'original');
            $thumbnail = null;
            if ($size == 'full' && $settings->imgBackup == 1) {
                // XXX change this? 'full' should be the resized path and 'original' the _backup path
                $backup_path = $this->object->get_backup_abspath($image);
                if (!@file_exists($backup_path)) {
                    @copy($filename, $backup_path);
                }
            }
            // Generate the thumbnail using WordPress
            $existing_image_abpath = $this->object->get_image_abspath($image, $size);
            $existing_image_dir = dirname($existing_image_abpath);
            wp_mkdir_p($existing_image_dir);
            $clone_path = $existing_image_abpath;
            $thumbnail = $this->object->generate_image_clone($filename, $clone_path, $params);
            // We successfully generated the thumbnail
            if ($thumbnail != null) {
                $clone_path = $thumbnail->fileName;
                if (function_exists('getimagesize')) {
                    $dimensions = getimagesize($clone_path);
                } else {
                    $dimensions = array($params['width'], $params['height']);
                }
                if (!isset($image->meta_data)) {
                    $image->meta_data = array();
                }
                $size_meta = array('width' => $dimensions[0], 'height' => $dimensions[1], 'filename' => M_I18n::mb_basename($clone_path), 'generated' => microtime());
                if (isset($params['crop_frame'])) {
                    $size_meta['crop_frame'] = $params['crop_frame'];
                }
                $image->meta_data[$size] = $size_meta;
                if ($size == 'full') {
                    $image->meta_data['width'] = $size_meta['width'];
                    $image->meta_data['height'] = $size_meta['height'];
                }
                $retval = $this->object->_image_mapper->save($image);
                do_action('ngg_generated_image', $image, $size, $params);
                if ($retval == 0) {
                    $retval = false;
                }
                if ($retval) {
                    $retval = $thumbnail;
                }
            } else {
                // Something went wrong. Thumbnail generation failed!
            }
        }
        return $retval;
    }
    /**
     * Generates a thumbnail for an image
     * @param int|stdClass|C_Image $image
     * @return bool
     */
    function generate_thumbnail($image, $params = null, $skip_defaults = false)
    {
        $sized_image = $this->object->generate_image_size($image, 'thumbnail', $params, $skip_defaults);
        $retval = false;
        if ($sized_image != null) {
            $retval = true;
            $sized_image->destruct();
        }
        return $retval;
    }
}
/**
 * Provides getter methods to C_Gallery_Storage for determining absolute paths, URL, etc
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Getters extends Mixin
{
    static $image_abspath_cache = array();
    static $image_url_cache = array();
    /**
     * Gets the absolute path of the backup of an original image
     * @param string $image
     * @return null|string
     */
    function get_backup_abspath($image)
    {
        $retval = null;
        if ($image_path = $this->object->get_image_abspath($image)) {
            $retval = $image_path . '_backup';
        }
        return $retval;
    }
    function get_backup_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'backup');
    }
    function get_backup_url($image)
    {
        return $this->object->get_image_url($image, 'backup');
    }
    /**
     * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
     * @param int|object $image
     * @param string $size (optional) Default = full
     * @param bool $check_existance (optional) Default = false
     * @return string
     */
    function get_image_abspath($image, $size = 'full', $check_existance = FALSE)
    {
        $image_id = is_numeric($image) ? $image : $image->pid;
        $size = $this->object->normalize_image_size_name($size);
        $key = strval($image_id) . $size;
        if ($check_existance || !isset(self::$image_abspath_cache[$key])) {
            $retval = $this->object->_get_computed_image_abspath($image, $size, $check_existance);
            self::$image_abspath_cache[$key] = $retval;
        }
        $retval = self::$image_abspath_cache[$key];
        return $retval;
    }
    /**
     * Gets the url of a particular-sized image
     * @param int|object $image
     * @param string $size
     * @return string
     */
    function get_image_url($image, $size = 'full')
    {
        $retval = NULL;
        $image_id = is_numeric($image) ? $image : $image->pid;
        $key = strval($image_id) . $size;
        $success = TRUE;
        if (!isset(self::$image_url_cache[$key])) {
            $url = $this->object->_get_computed_image_url($image, $size);
            if ($url) {
                self::$image_url_cache[$key] = $url;
                $success = TRUE;
            } else {
                $success = FALSE;
            }
        }
        if ($success) {
            $retval = self::$image_url_cache[$key];
        } else {
            $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
            if ($dynthumbs->is_size_dynamic($size)) {
                $params = $dynthumbs->get_params_from_name($size);
                $retval = $dynthumbs->get_image_url($image, $params);
            }
        }
        return apply_filters('ngg_get_image_url', $retval, $image, $size);
    }
    /**
     * An alias for get_full_abspath()
     * @param int|object $image
     * @param bool $check_existance
     * @return null|string
     */
    function get_original_abspath($image, $check_existance = FALSE)
    {
        return $this->object->get_image_abspath($image, 'full', $check_existance);
    }
    /**
     * Flushes the cache we use for path/url calculation for images
     */
    function flush_image_path_cache($image, $size)
    {
        $image = is_numeric($image) ? $image : $image->pid;
        $key = strval($image) . $size;
        unset(self::$image_abspath_cache[$key]);
        unset(self::$image_url_cache[$key]);
    }
}
/**
 * Provides the basic methods of gallery management to C_Gallery_Storage
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Management extends Mixin
{
    /**
     * Backs up an image file
     *
     * @param int|object $image
     * @param bool $save
     * @return bool
     */
    function backup_image($image, $save = TRUE)
    {
        $retval = FALSE;
        $image_path = $this->object->get_image_abspath($image);
        if ($image_path && @file_exists($image_path)) {
            $retval = copy($image_path, $this->object->get_backup_abspath($image));
            // Store the dimensions of the image
            if (function_exists('getimagesize')) {
                $mapper = C_Image_Mapper::get_instance();
                if (!is_object($image)) {
                    $image = $mapper->find($image);
                }
                if ($image) {
                    if (empty($image->meta_data) || !is_array($image->meta_data)) {
                        $image->meta_data = array();
                    }
                    $dimensions = getimagesize($image_path);
                    $image->meta_data['backup'] = array('filename' => basename($image_path), 'width' => $dimensions[0], 'height' => $dimensions[1], 'generated' => microtime());
                    if ($save) {
                        $mapper->save($image);
                    }
                }
            }
        }
        return $retval;
    }
}
/**
 * Provides upload-related methods used by C_Gallery_Storage
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Upload extends Mixin
{
    /**
     * @param string $abspath
     * @param int $gallery_id
     * @param bool $create_new_gallerypath
     * @param null|string $gallery_title
     * @param array[string] $filenames
     * @return array|bool FALSE on failure
     */
    function import_gallery_from_fs($abspath, $gallery_id = NULL, $create_new_gallerypath = TRUE, $gallery_title = NULL, $filenames = array())
    {
        if (@(!file_exists($abspath))) {
            return FALSE;
        }
        $fs = C_Fs::get_instance();
        $retval = array('image_ids' => array());
        // Ensure that this folder has images
        $files = array();
        $directories = array();
        foreach (scandir($abspath) as $file) {
            if ($file == '.' || $file == '..' || strtoupper($file) == '__MACOSX') {
                continue;
            }
            $file_abspath = $fs->join_paths($abspath, $file);
            // Omit 'hidden' directories prefixed with a period
            if (is_dir($file_abspath) && strpos($file, '.') !== 0) {
                $directories[] = $file_abspath;
            } elseif ($this->is_image_file($file_abspath)) {
                if ($filenames && array_search($file_abspath, $filenames) !== FALSE) {
                    $files[] = $file_abspath;
                } else {
                    if (!$filenames) {
                        $files[] = $file_abspath;
                    }
                }
            }
        }
        if (empty($files) && empty($directories)) {
            return FALSE;
        }
        // Get needed utilities
        $gallery_mapper = C_Gallery_Mapper::get_instance();
        // Recurse through the directory and pull in all of the valid images we find
        if (!empty($directories)) {
            foreach ($directories as $dir) {
                $subImport = $this->object->import_gallery_from_fs($dir, $gallery_id, $create_new_gallerypath, $gallery_title, $filenames);
                if ($subImport) {
                    $retval['image_ids'] = array_merge($retval['image_ids'], $subImport['image_ids']);
                }
            }
        }
        // If no gallery has been specified, then use the directory name as the gallery name
        if (!$gallery_id) {
            // Create the gallery
            $gallery = $gallery_mapper->create(array('title' => $gallery_title ? $gallery_title : M_I18n::mb_basename($abspath)));
            if (!$create_new_gallerypath) {
                $gallery_root = $fs->get_document_root('gallery');
                $gallery->path = str_ireplace($gallery_root, '', $abspath);
            }
            // Save the gallery
            if ($gallery->save()) {
                $gallery_id = $gallery->id();
            }
        }
        // Ensure that we have a gallery id
        if (!$gallery_id) {
            return FALSE;
        } else {
            $retval['gallery_id'] = $gallery_id;
        }
        // Remove full sized image if backup is included
        $files_to_import = [];
        foreach ($files as $file_abspath) {
            if (preg_match("#_backup\$#", $file_abspath)) {
                $files_to_import[] = $file_abspath;
                continue;
            } elseif (in_array([$file_abspath . "_backup", 'thumbs_' . $file_abspath, 'thumbs-' . $file_abspath], $files)) {
                continue;
            }
            $files_to_import[] = $file_abspath;
        }
        foreach ($files_to_import as $file_abspath) {
            $basename = preg_replace('#_backup$#', '', pathinfo($file_abspath, PATHINFO_BASENAME));
            if ($this->is_image_file($file_abspath)) {
                if ($image_id = $this->import_image_file($gallery_id, $file_abspath, $basename, FALSE, FALSE, FALSE)) {
                    $retval['image_ids'][] = $image_id;
                }
            }
        }
        // Add the gallery name to the result
        if (!isset($gallery)) {
            $gallery = $gallery_mapper->find($gallery_id);
        }
        $retval['gallery_name'] = $gallery->title;
        return $retval;
    }
}
/**
 * Class Mixin_NextGen_Table_Extras
 * @mixin C_CustomPost_DataMapper_Driver
 */
class Mixin_NextGen_Table_Extras extends Mixin
{
    const CUSTOM_POST_NAME = __CLASS__;
    function initialize()
    {
        // Each record in a NextGEN Gallery table has an associated custom post in the wp_posts table
        $this->object->_custom_post_mapper = new C_CustomPost_DataMapper_Driver($this->object->get_object_name());
        $this->object->_custom_post_mapper->set_model_factory_method('extra_fields');
    }
    /**
     * Defines a column for the mapper
     * @param $name
     * @param $data_type
     * @param null $default_value
     * @param bool $extra
     */
    function define_column($name, $data_type, $default_value = NULL, $extra = FALSE)
    {
        $this->call_parent('define_column', $name, $data_type, $default_value);
        if ($extra) {
            $this->object->_columns[$name]['extra'] = TRUE;
        } else {
            $this->object->_columns[$name]['extra'] = FALSE;
        }
    }
    /**
     * Gets a list of all the extra columns defined for this table
     * @return array
     */
    function get_extra_columns()
    {
        $retval = array();
        foreach ($this->object->_columns as $key => $properties) {
            if ($properties['extra']) {
                $retval[] = $key;
            }
        }
        return $retval;
    }
    /**
     * Adds a column to the database
     * @param $column_name
     * @param $datatype
     * @param null $default_value
     * @return bool
     */
    function _add_column($column_name, $datatype, $default_value = NULL)
    {
        $skip = FALSE;
        if (isset($this->object->_columns[$column_name]) and $this->object->_columns[$column_name]['extra']) {
            $skip = TRUE;
        }
        if (!$skip) {
            $this->call_parent('_add_column', $column_name, $datatype, $default_value);
        }
        return !$skip;
    }
    function create_custom_post_entity($entity)
    {
        $custom_post_entity = new stdClass();
        // If the custom post entity already exists then it needs
        // an ID
        if (isset($entity->extras_post_id)) {
            $custom_post_entity->ID = $entity->extras_post_id;
        }
        // If a property isn't a column for the table, then
        // it belongs to the custom post record
        foreach (get_object_vars($entity) as $key => $value) {
            if (!$this->object->has_column($key)) {
                unset($entity->{$key});
                if ($this->object->has_defined_column($key) && $key != $this->object->get_primary_key_column()) {
                    $custom_post_entity->{$key} = $value;
                }
            }
        }
        // Used to help find these type of records
        $custom_post_entity->post_name = self::CUSTOM_POST_NAME;
        return $custom_post_entity;
    }
    /**
     * Creates a new record in the custom table, as well as a custom post record
     * @param $entity
     */
    function _create($entity)
    {
        $retval = FALSE;
        $custom_post_entity = $this->create_custom_post_entity($entity);
        // Try persisting the custom post type record first
        if ($custom_post_id = $this->object->_custom_post_mapper->save($custom_post_entity)) {
            // Add the custom post id property
            $entity->extras_post_id = $custom_post_id;
            // Try saving the custom table record. If that fails, then destroy the previously created custom post type record
            if (!($retval = $this->call_parent('_create', $entity))) {
                $this->object->_custom_post_mapper->destroy($custom_post_id);
            }
        }
        return $retval;
    }
    // Updates a custom table record and it's associated custom post type record in the database
    function _update($entity)
    {
        $retval = FALSE;
        $custom_post_entity = $this->create_custom_post_entity($entity);
        $custom_post_id = $this->object->_custom_post_mapper->save($custom_post_entity);
        $entity->extras_post_id = $custom_post_id;
        $retval = $this->call_parent('_update', $entity);
        foreach ($this->get_extra_columns() as $key) {
            if (isset($custom_post_entity->{$key})) {
                $entity->{$key} = $custom_post_entity->{$key};
            }
        }
        return $retval;
    }
    function destroy($entity)
    {
        if (isset($entity->extras_post_id)) {
            wp_delete_post($entity->extras_post_id, TRUE);
        }
        return $this->call_parent('destroy', $entity);
    }
    function _regex_replace($in)
    {
        global $wpdb;
        $from = 'FROM `' . $this->object->get_table_name() . '`';
        $out = str_replace('FROM', ", GROUP_CONCAT(CONCAT_WS('@@', meta_key, meta_value)) AS 'extras' FROM", $in);
        $out = str_replace($from, "{$from} LEFT OUTER JOIN `{$wpdb->postmeta}` ON `{$wpdb->postmeta}`.`post_id` = `extras_post_id` ", $out);
        return $out;
    }
    /**
     * Gets the generated query
     */
    function get_generated_query()
    {
        // Add extras column
        if ($this->object->is_select_statement() && stripos($this->object->_select_clause, 'count(') === FALSE) {
            $table_name = $this->object->get_table_name();
            $primary_key = "{$table_name}.{$this->object->get_primary_key_column()}";
            if (stripos($this->object->_select_clause, 'DISTINCT') === FALSE) {
                $this->object->_select_clause = str_replace('SELECT', 'SELECT DISTINCT', $this->object->_select_clause);
            }
            $this->object->group_by($primary_key);
            $sql = $this->call_parent('get_generated_query');
            // Sections may be omitted by wrapping them in mysql/C style comments
            if (stripos($sql, '/*NGG_NO_EXTRAS_TABLE*/') !== FALSE) {
                $parts = explode('/*NGG_NO_EXTRAS_TABLE*/', $sql);
                foreach ($parts as $ndx => $row) {
                    if ($ndx % 2 != 0) {
                        continue;
                    }
                    $parts[$ndx] = $this->_regex_replace($row);
                }
                $sql = implode('', $parts);
            } else {
                $sql = $this->_regex_replace($sql);
            }
        } else {
            $sql = $this->call_parent('get_generated_query');
        }
        return $sql;
    }
    function _convert_to_entity($entity)
    {
        // Add extra columns to entity
        if (isset($entity->extras)) {
            $extras = $entity->extras;
            unset($entity->extras);
            foreach (explode(',', $extras) as $extra) {
                if ($extra) {
                    list($key, $value) = explode('@@', $extra);
                    if ($this->object->has_defined_column($key) && !isset($entity->key)) {
                        $entity->{$key} = $value;
                    }
                }
            }
        }
        // Cast custom_post_id as integer
        if (isset($entity->extras_post_id)) {
            $entity->extras_post_id = intval($entity->extras_post_id);
        } else {
            $entity->extras_post_id = 0;
        }
        $retval = $this->call_parent('_convert_to_entity', $entity);
        return $entity;
    }
}