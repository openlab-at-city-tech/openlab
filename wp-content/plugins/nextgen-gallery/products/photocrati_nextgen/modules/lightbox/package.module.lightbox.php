<?php
class C_Lightbox_Installer_Mapper
{
    function find_by_name()
    {
        return NULL;
    }
}
class C_Lightbox_Installer
{
    public function __construct()
    {
        $this->mapper = new C_Lightbox_Installer_Mapper();
    }
    function install_lightbox($name, $title, $code, $stylesheet_paths = array(), $script_paths = array(), $values = array(), $i18n = array())
    {
        if (!is_array($stylesheet_paths) && is_string($stylesheet_paths) && FALSE !== strpos($stylesheet_paths, "\n")) {
            $stylesheet_paths = explode("\n", $stylesheet_paths);
        }
        if (!is_array($script_paths) && is_string($script_paths) && FALSE !== strpos($script_paths, "\n")) {
            $script_paths = explode("\n", $script_paths);
        }
        $lightbox = new C_NGG_Lightbox($name, array('title' => $title, 'code' => $code, 'styles' => $stylesheet_paths, 'scripts' => $script_paths, 'values' => $values, 'i18n' => $i18n));
        C_Lightbox_Library_Manager::get_instance()->register($name, $lightbox);
    }
}
class C_Lightbox_Library_Manager
{
    private $_lightboxes = array();
    private $_registered_defaults = FALSE;
    /**
     * @var C_Lightbox_Library_Manager
     */
    static $_instance = NULL;
    /**
     * @return C_Lightbox_Library_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function register_defaults()
    {
        $settings = C_NextGen_Settings::get_instance();
        $fs = C_Fs::get_instance();
        $router = C_Router::get_instance();
        // Add none as an option
        $none = new C_NGG_Lightbox('none');
        $none->title = __('None', 'nggallery');
        $this->register('none', $none);
        // Add Simplelightbox
        $simplelightbox = new C_NGG_Lightbox('simplelightbox');
        $simplelightbox->title = __('Simplelightbox', 'nggallery');
        $simplelightbox->code = 'class="ngg-simplelightbox" rel="%GALLERY_NAME%"';
        $simplelightbox->styles = array('photocrati-lightbox#simplelightbox/simple-lightbox.css');
        $simplelightbox->scripts = array('photocrati-lightbox#simplelightbox/simple-lightbox.js', 'photocrati-lightbox#simplelightbox/nextgen_simple_lightbox_init.js');
        $this->register('simplelightbox', $simplelightbox);
        // Add Fancybox
        $fancybox = new C_NGG_Lightbox('fancybox');
        $fancybox->title = __('Fancybox', 'nggallery');
        $fancybox->code = 'class="ngg-fancybox" rel="%GALLERY_NAME%"';
        $fancybox->styles = array('photocrati-lightbox#fancybox/jquery.fancybox-1.3.4.css');
        $fancybox->scripts = array('https://cdnjs.cloudflare.com/ajax/libs/jquery-browser/0.1.0/jquery.browser.min.js', 'photocrati-lightbox#fancybox/jquery.easing-1.3.pack.js', 'photocrati-lightbox#fancybox/jquery.fancybox-1.3.4.pack.js', 'photocrati-lightbox#fancybox/nextgen_fancybox_init.js');
        $this->register('fancybox', $fancybox);
        // Add Shutter
        $shutter = new C_NGG_Lightbox('shutter');
        $shutter->title = __('Shutter', 'nggallery');
        $shutter->code = 'class="shutterset_%GALLERY_NAME%"';
        $shutter->styles = array('photocrati-lightbox#shutter/shutter.css');
        $shutter->scripts = array('photocrati-lightbox#shutter/shutter.js', 'photocrati-lightbox#shutter/nextgen_shutter.js');
        $shutter->values = array('nextgen_shutter_i18n' => array('msgLoading' => __('L O A D I N G', 'nggallery'), 'msgClose' => __('Click to Close', 'nggallery')));
        $this->register('shutter', $shutter);
        // Add shutter reloaded
        $shutter2 = new C_NGG_Lightbox('shutter2');
        $shutter2->title = __('Shutter Reloaded', 'nggallery');
        $shutter2->code = 'class="shutterset_%GALLERY_NAME%"';
        $shutter2->styles = array('photocrati-lightbox#shutter_reloaded/shutter.css');
        $shutter2->scripts = array('photocrati-lightbox#shutter_reloaded/shutter.js', 'photocrati-lightbox#shutter_reloaded/nextgen_shutter_reloaded.js');
        $shutter2->values = array('nextgen_shutter2_i18n' => array(__('Previous', 'nggallery'), __('Next', 'nggallery'), __('Close', 'nggallery'), __('Full Size', 'nggallery'), __('Fit to Screen', 'nggallery'), __('Image', 'nggallery'), __('of', 'nggallery'), __('Loading...', 'nggallery')));
        $this->register('shutter2', $shutter2);
        // Add Thickbox
        $thickbox = new C_NGG_Lightbox('thickbox');
        $thickbox->title = __('Thickbox', 'nggallery');
        $thickbox->code = "class='thickbox' rel='%GALLERY_NAME%'";
        $thickbox->styles = array('wordpress#thickbox');
        $thickbox->scripts = array('photocrati-lightbox#thickbox/nextgen_thickbox_init.js', 'wordpress#thickbox');
        $thickbox->values = array('nextgen_thickbox_i18n' => array('next' => __('Next &gt;', 'nggallery'), 'prev' => __('&lt; Prev', 'nggallery'), 'image' => __('Image', 'nggallery'), 'of' => __('of', 'nggallery'), 'close' => __('Close', 'nggallery'), 'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.', 'nggallery')));
        $this->register('thickbox', $thickbox);
        // Allow third parties to integrate
        do_action('ngg_registered_default_lightboxes');
        // Add custom option
        $custom = new C_NGG_Lightbox('custom');
        $custom->title = __('Custom', 'nggallery');
        $custom->code = $settings->thumbEffectCode;
        $custom->styles = $settings->thumbEffectStyles;
        $custom->scripts = $settings->thumbEffectScripts;
        $this->register('custom_lightbox', $custom);
        $this->_registered_defaults = TRUE;
    }
    function register($name, $properties)
    {
        // We'll use an object to represent the lightbox
        $object = $properties;
        if (!is_object($properties)) {
            $object = new stdClass();
            foreach ($properties as $k => $v) {
                $object->{$k} = $v;
            }
        }
        // Set default properties
        $object->name = $name;
        if (!isset($object->title)) {
            $object->title = $name;
        }
        if (!isset($object->code)) {
            $object->code = '';
        }
        if (!isset($object->scripts)) {
            $object->scripts = array();
        }
        if (!isset($object->styles)) {
            $object->styles = array();
        }
        if (!isset($object->values)) {
            $object->values = array();
        }
        $this->_lightboxes[$name] = $object;
    }
    function deregister($name)
    {
        unset($this->_lightboxes[$name]);
    }
    function get($name)
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $retval = NULL;
        if (isset($this->_lightboxes[$name])) {
            $retval = $this->_lightboxes[$name];
        }
        return $retval;
    }
    /**
     * Returns which lightbox effect has been chosen
     *
     * Highslide and jQuery.Lightbox were removed in 2.0.73 due to licensing. If a user has selected
     * either of those options we silently make their selection fallback to Fancybox
     * @return null|string
     */
    function get_selected()
    {
        $settings = C_NextGen_Settings::get_instance();
        if (in_array($settings->thumbEffect, array('highslide', 'lightbox'))) {
            $settings->thumbEffect = 'fancybox';
        }
        return $this->get($settings->thumbEffect);
    }
    function get_selected_context()
    {
        return C_NextGen_Settings::get_instance()->thumbEffectContext;
    }
    function get_all()
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        return array_values($this->_lightboxes);
    }
    function is_registered($name)
    {
        return !is_null($this->get($name));
    }
    function maybe_enqueue()
    {
        $settings = C_NextGen_Settings::get_instance();
        $thumbEffectContext = isset($settings->thumbEffectContext) ? $settings->thumbEffectContext : '';
        if ($thumbEffectContext != 'nextgen_images') {
            $this->enqueue();
        }
    }
    function enqueue($lightbox = NULL)
    {
        $router = C_Router::get_instance();
        $settings = C_NextGen_Settings::get_instance();
        $thumbEffectContext = isset($settings->thumbEffectContext) ? $settings->thumbEffectContext : '';
        // If no lightbox has been provided, get the selected lightbox
        if (!$lightbox) {
            $lightbox = $this->get_selected();
        } else {
            $lightbox = $this->get($lightbox);
        }
        if (!wp_script_is('ngg_lightbox_context')) {
            wp_enqueue_script('ngg_lightbox_context', $router->get_static_url('photocrati-lightbox#lightbox_context.js'), array('ngg_common', 'photocrati_ajax'), NGG_SCRIPT_VERSION, TRUE);
        }
        // Make the path to the static resources available for libraries.
        //
        // Yes the {placeholder} is a stupid hack but it's necessary for Shutter Reloaded and is much faster
        // than making get_static_url() function without requesting a filename parameter
        $this->_add_script_data(
            'ngg_common',
            // TODO: Should this be ngg_lightbox_context instead?
            'nextgen_lightbox_settings',
            array('static_path' => M_Static_Assets::get_static_url('{placeholder}', 'photocrati-lightbox'), 'context' => $thumbEffectContext),
            TRUE,
            TRUE
        );
        // Enqueue lightbox resources, only if we have a configured lightbox
        if ($lightbox) {
            // Add lightbox script data
            if (isset($lightbox->values)) {
                foreach ($lightbox->values as $name => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    $this->_add_script_data('ngg_lightbox_context', $name, $value, TRUE);
                }
            }
            // Enqueue stylesheets
            for ($i = 0; $i < count($lightbox->styles); $i++) {
                $src = $lightbox->styles[$i];
                if (strpos($src, 'wordpress#') === 0) {
                    $parts = explode('wordpress#', $src);
                    wp_enqueue_style(array_pop($parts));
                } else {
                    if (!empty($src)) {
                        wp_enqueue_style($lightbox->name . "-{$i}", $this->_handle_url($src), array(), NGG_SCRIPT_VERSION);
                    }
                }
            }
            // Enqueue scripts
            for ($i = 0; $i < count($lightbox->scripts); $i++) {
                $src = $lightbox->scripts[$i];
                $handle = $lightbox->name . "-{$i}";
                if (strpos($src, 'wordpress#') === 0) {
                    $parts = explode('wordpress#', $src);
                    wp_enqueue_script(array_pop($parts));
                } else {
                    if (!empty($src)) {
                        wp_enqueue_script($handle, $this->_handle_url($src), array('ngg_lightbox_context'), NGG_SCRIPT_VERSION, TRUE);
                    }
                }
            }
        }
    }
    /**
     * Parses certain paths through get_static_url
     *
     * @param string $url
     * @param string $type Unused
     * @return string Resulting URL
     */
    static function _handle_url($url, $type = 'script')
    {
        $router = C_Router::get_instance();
        if (0 !== strpos($url, '/') && 0 !== strpos($url, 'wordpress#') && 0 !== strpos($url, 'http://') && 0 !== strpos($url, 'https://')) {
            $url = $router->get_static_url($url);
        } elseif (strpos($url, '/') === 0) {
            $url = home_url($url);
        }
        return $url;
    }
    /**
     * Adds data to the DOM which is then accessible by a script -- borrowed from display type controller class
     * @param string $handle
     * @param string $object_name
     * @param mixed $object_value
     * @param bool $define
     * @return bool
     */
    function _add_script_data($handle, $object_name, $object_value, $define = TRUE, $override = FALSE)
    {
        $retval = FALSE;
        // wp_localize_script allows you to add data to the DOM, associated
        // with a particular script. You can even call wp_localize_script
        // multiple times to add multiple objects to the DOM. However, there
        // are a few problems with wp_localize_script:
        //
        // - If you call it with the same object_name more than once, you're
        //   overwritting the first call.
        // - You cannot namespace your objects due to the "var" keyword always
        // - being used.
        //
        // To circumvent the above issues, we're going to use the WP_Scripts
        // object to workaround the above issues
        global $wp_scripts;
        // Has the script been registered or enqueued yet?
        if (isset($wp_scripts->registered[$handle])) {
            // Get the associated data with this script
            $script =& $wp_scripts->registered[$handle];
            $data = isset($script->extra['data']) ? $script->extra['data'] : '';
            // Construct the addition
            $addition = $define ? "\nvar {$object_name} = " . json_encode($object_value) . ';' : "\n{$object_name} = " . json_encode($object_value) . ';';
            // Add the addition
            if ($override) {
                $data .= $addition;
                $retval = TRUE;
            } else {
                if (strpos($data, $object_name) === FALSE) {
                    $data .= $addition;
                    $retval = TRUE;
                }
            }
            $script->extra['data'] = $data;
            unset($script);
        }
        return $retval;
    }
    function deregister_all()
    {
        $this->_lightboxes = array();
        $this->_registered_defaults = FALSE;
    }
}
/**
 * Represents a lightbox available in NextGEN Gallery
 * Class C_NGG_Lightbox
 * @mixin Mixin_NGG_Lightbox_Instance_Methods
 * @implements I_Lightbox
 */
class C_NGG_Lightbox extends C_Component
{
    function define($context = FALSE, $properties = array())
    {
        parent::define($context);
        $this->add_mixin('Mixin_NGG_Lightbox_Instance_Methods');
        $this->implement('I_Lightbox');
    }
    function initialize($name = '', $properties = array())
    {
        parent::initialize();
        $properties['name'] = $name;
        foreach ($properties as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
class Mixin_NGG_Lightbox_Instance_Methods extends Mixin
{
    /**
     * Returns true/false whether or not the lightbox supports displaying entities from the displayed gallery object
     * @param $displayed_gallery. By default, lightboxes don't support albums
     * @return bool
     */
    function is_supported($displayed_gallery)
    {
        $retval = TRUE;
        if (in_array($displayed_gallery->source, array('album', 'albums')) && !isset($displayed_gallery->display_settings['open_gallery_in_lightbox'])) {
            $retval = FALSE;
        }
        return $retval;
    }
}