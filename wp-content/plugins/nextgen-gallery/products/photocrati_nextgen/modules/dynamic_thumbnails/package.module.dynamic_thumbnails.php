<?php
/**
 * Class C_Dynamic_Thumbnails_Controller
 * @implements I_Dynamic_Thumbnails_Controller
 */
class C_Dynamic_Thumbnails_Controller extends C_MVC_Controller
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Dynamic_Thumbnails_Controller');
    }
    /**
     * Returns an instance of this class
     *
     * @param string|bool $context
     * @return C_Dynamic_Thumbnails_Controller
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function index_action($return = FALSE)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '-1');
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        $uri = $_SERVER['REQUEST_URI'];
        $params = $dynthumbs->get_params_from_uri($uri);
        $request_params = $params;
        if ($params != null) {
            $storage = C_Gallery_Storage::get_instance();
            // Note, URLs should always include quality setting when returned by Gallery Storage component
            // this sanity check is mostly for manually testing URLs
            if (!isset($params['quality'])) {
                // Note: there's a problem when doing this as using the same set of parameters to *retrieve* the image path/URL will lead to a different filename than the one tha was used to *generate* it (which went through here)
                // The statement above about URLs always containing quality setting is not true anymore, this is because we need to retrieve default quality from the imgQuality and thumbquality settings, depending on "full" or "thumbnail" request in the ngglegacy storage
                //$params['quality'] = 100;
            }
            $image_id = $params['image'];
            $size = $dynthumbs->get_size_name($params);
            $abspath = $storage->get_image_abspath($image_id, $size, true);
            $valid = true;
            // Render invalid image if hash check fails
            if ($abspath == null) {
                $uri_plain = $dynthumbs->get_uri_from_params($request_params);
                $hash = wp_hash($uri_plain);
                if (strpos($uri, $hash) === false) {
                    $valid = false;
                    $filename = M_Static_Assets::get_static_abspath('photocrati-dynamic_thumbnails#invalid_image.png');
                    $this->set_content_type('image/png');
                    readfile($filename);
                    $this->render();
                }
            }
            if ($valid) {
                $storage->render_image($image_id, $size);
            }
        }
    }
}