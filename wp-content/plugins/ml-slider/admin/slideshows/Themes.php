<?php

if (!defined('ABSPATH')) {
die('No direct access.');
}

/**
 * Class to handle themes
 */
class MetaSlider_Themes
{
    /**
     * Theme instance
     * 
     * @var object
     * @see get_instance()
     */
    protected static $instance = null;

    /**
     * Theme name
     * 
     * @var string
     */
    public $theme_id;

    /**
     * List of supported slide types
     * 
     * @var array
     */
    public $supported_slideshow_libraries = array('flex', 'responsive', 'nivo', 'coin');

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Used to access the instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Method to get all the free themes from the theme directory
     * 
     * @return array|WP_Error whether the file was included, or error class
     */
    public function get_all_free_themes()
    {
        if (!file_exists(METASLIDER_THEMES_PATH . 'manifest.php') || !file_exists(METASLIDER_THEMES_PATH . 'manifest-legacy.php')) {
            return new WP_Error('manifest_not_found', __('No themes found.', 'ml-slider'), array('status' => 404));
        }

        
        if (is_multisite() && $settings = get_site_option('metaslider_global_settings')) {
            $global_settings = $settings;
        }
        
        if ($settings = get_option('metaslider_global_settings')) {
            $global_settings = $settings;
        }
        
        $new_install = get_option('metaslider_new_user');
        
        if (
            (isset($global_settings['legacy']) && true == $global_settings['legacy']) ||
            (isset($new_install)  && 'new' == $new_install)
        ) {
            $themes = (include METASLIDER_THEMES_PATH . 'manifest.php');
        } else {
            $themes = (include METASLIDER_THEMES_PATH . 'manifest-legacy.php');
        }

        // If is not Pro, let's include some Premium themes with upgrade link
        if (! metaslider_pro_is_active()) {
            $premium_themes = (include METASLIDER_THEMES_PATH . 'manifest-premium.php');
            $themes = array_merge($themes, $premium_themes);
        }
        
        /**
         * Check if we have extra themes/ folders added from external sources,
         * including MetaSlider Pro 
         * 
         * e.g. 
         * array(
         *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
         *  '/path/to/wp-content/themes/my-theme/ms-themes/'
         * )
         */
        $extra_themes = apply_filters('metaslider_extra_themes', array());
        foreach ($extra_themes as $location) {
            // Make sure there is a manifest
            if (file_exists(trailingslashit($location) . 'manifest.php')) {
                $manifest = include(trailingslashit($location) . 'manifest.php');

                // Make sure each theme has an existing folder, title, description
                foreach ($manifest as $data) {
                    if (isset($data['folder'])
                        && file_exists($folder = trailingslashit($location) . $data['folder'])
                        && isset($data['title']) 
                        && isset($data['description'])
                        && isset($data['screenshot_dir'])
                    ) {
                        // Adjust type
                        $data['type'] = isset($data['type']) ? $data['type'] : 'external';
                        
                        // Set a temporary array key to pass the customize.php file location
                        if (file_exists($customize = trailingslashit($folder) . 'customize.php')) {
                            $data['theme_customize_temp_'] = $customize;
                        }

                        // Add a key to the theme array
                        $data = array( $data['folder'] => $data );

                        // Merge and set new theme to the top
                        $themes = array_merge($themes, $data);
                    }
                }
            }
        }

        // Add theme customization settings
        $themes = $this->add_base_customize_settings($themes);

        return $themes;
    }

    /**
     * Add customize array key to each theme that have its own customize.php file
     * 
     * @since 3.91.0
     * 
     * @param array $themes Themes array from manifest file
     * 
     * @return array 
     */
    public function add_base_customize_settings($themes)
    {
        foreach ( $themes as $item ) {
            $folder = $item['folder'];

            // Check if we use a different customize.php file (e.g. is an external theme) for this theme or default
            $customize_file = isset($item['theme_customize_temp_']) ? $item['theme_customize_temp_'] : METASLIDER_THEMES_PATH . $folder . '/customize.php';
            
            if (in_array($item['type'], array('free', 'premium', 'external')) 
                && file_exists($customize_file)
            ) {
                $customize_settings = (include $customize_file);
                $themes[$folder]['customize'] = $this->merge_theme_customizations($customize_settings);
            }

            // Remove temporary array keys
            if (isset($themes[$folder]['theme_customize_temp_'])) {
                unset($themes[$folder]['theme_customize_temp_']);
            }
        }

        return $themes;
    }

    /**
     * Get customize settings from a specific theme
     * 
     * @since 3.91.0
     * @since 3.93 - Added $alt_customize_file param
     * 
     * @param string $theme                     Theme slug in lowercase. Use the theme's folder name actually.
     * @param bool|string $alt_customize_file   Alternative customize.php location
     *                                          Use cases: if is a Pro theme or a custom theme.
     *                                          e.g. 'path/to/another/themes/my-theme/customize.php'
     * 
     * return array
     */
    public function add_base_customize_settings_single($theme, $alt_customize_file = false)
    {
        $customize_file = $alt_customize_file 
                        ? $alt_customize_file 
                        : METASLIDER_THEMES_PATH . $theme . '/customize.php';
        
        if (file_exists($customize_file)) {
            $customize_settings = (include $customize_file);
            $data = $this->merge_theme_customizations($customize_settings);
        } else {
            $data = array();
        }

        return $data;
    }

    /**
     * Get single theme data from manifest file
     * 
     * @since 3.93.0
     * 
     * @param string $theme Theme slug in lowercase. Use the theme's folder name actually.
     * 
     * return array
     */
    public function get_single_theme($theme)
    {
        $all_themes = $this->get_all_free_themes();

        return $all_themes[$theme];
    }

    /**
     * Method to get all custom themes from the database.
     * 
     * @return array Returns an array of custom themes or an empty array if none found
     */
    public function get_custom_themes()
    {
        $custom_themes = array();
        if ((bool) $themes = get_option('metaslider-themes')) {
            foreach ($themes as $id => $theme) {
                $custom_themes[$id] = array(
                    'folder' => $id,
                    'title' => $theme['title'],
                    'type' => 'custom'
                );
            }
        }
        return $custom_themes;
    }

    /**
     * Method to get details about a theme
     *
     * @param string $id - Id of the slideshow
     * @return void
     */
    public function details($id)
    {
    }

    /**
     * Method to get the object by theme name/id
     *
     * @param string $slideshow_id - Id of the slideshow
     * @param string $theme_id     - Id of the theme
     * 
     * @return bool|array - The theme object or false if no theme
     */
    public function get_theme_object($slideshow_id, $theme_id)
    {
        if (is_wp_error($free_themes = $this->get_all_free_themes())) {
            $free_themes = array();
        }
        $themes = array_merge($free_themes, $this->get_custom_themes());
        foreach ($themes as $one_theme) {
            if ($one_theme['folder'] === $theme_id) {
                $theme = $one_theme;
            }
        }

        // If the folder isn't set then something went wrong or no theme is set.
        if (!isset($theme['folder']) || '' == $theme['folder']) {
            return false;
        }

        // If the version isn't set, grab the latest
        if (!isset($theme['version']) || '' == $theme['version']) {
            $theme['version'] = $this->get_latest_version($theme['folder']);
        }

        return $theme;
    }


    /**
     * Method to get a random theme
     *
     * @param bool $all - Whether to include themes that arent fully supported
     * 
     * @return bool|array - The theme object or false if no theme
     */
    public function random($all = false)
    {
        if (is_wp_error($free_themes = $this->get_all_free_themes())) {
            return false;
        }
        if ($all) {
return array_rand($free_themes);
        }

        $themes = array();
        foreach ($free_themes as $id => $theme) {
            // Be sure the theme supports all slider libraries
            if (count($this->supported_slideshow_libraries) === count($theme['supports'])) {
                $themes[$id] = $theme;
            }
        }

        return array_rand($themes);
    }

    /**
     * Method to get the current set theme for a slideshow
     *
     * @param string $slideshow_id - Id of the slideshow
     * 
     * @return bool|array - The theme object or false if no theme
     */
    public function get_current_theme($slideshow_id)
    {

        $theme = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);

        // If the theme is none, it means no theme is set (happens if they remove a theme)
        // $theme may be WP_Error due to a bug in 3.9.1
        if ('none' === $theme || is_wp_error($theme)) {
return false;
        }

        $is_a_custom_theme = (isset($theme['folder']) && ('_theme' === substr($theme['folder'], 0, 6)));

        // Check here for a legacy theme OR a custom theme
        if (!isset($theme['folder']) || $is_a_custom_theme) {
            $settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
            
            // * This might be a nivo theme or a custom theme (pro)
            if (isset($settings['theme'])) {
                $settings['theme'] = in_array($settings['theme'], array('light', 'dark', 'bar')) ? 'nivo-' . $settings['theme'] : $settings['theme'];
                $theme = $this->get_theme_object($slideshow_id, $settings['theme']);

                // Update the theme to the new system
                update_post_meta($slideshow_id, 'metaslider_slideshow_theme', $theme);
            }
        }

        // If the folder isn't set then something went wrong or no theme is set.
        if (!isset($theme['folder']) || '' == $theme['folder']) {
            return false;
        }

        // If the version isn't set, grab the latest
        if (!isset($theme['version']) || '' == $theme['version']) {
            $theme['version'] = $this->get_latest_version($theme['folder']);
        }

        // At this point, if it's a custom theme we are okay
        if ($is_a_custom_theme) {
return $theme;
        }

        /**
         * Check if we have extra themes/ folders added from external sources,
         * including MetaSlider Pro 
         * 
         * e.g. 
         * array(
         *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
         *  '/path/to/wp-content/themes/my-theme/ms-themes/'
         * )
         */
        $extra_themes = apply_filters('metaslider_extra_themes', array());
        foreach ($extra_themes as $location) {
            if (file_exists(trailingslashit($location) . trailingslashit($theme['folder']) . trailingslashit($theme['version']) . 'theme.php')) {
                return $theme;
            }
        }

        // If the folder is in in our theme selection
        if (file_exists(METASLIDER_THEMES_PATH . trailingslashit($theme['folder']) . $theme['version'])) {
            return $theme;
        }

        // Remove the broken/not found theme
        $this->set($slideshow_id, array());

        // The theme wasnt found anywhere
        return new WP_Error('theme_not_found', __('We removed your selected theme as it could not be found. Was the folder deleted?', 'ml-slider'));
    }

    /**
     * Method to get the version of the latest theme
     *
     * @param string $folder - Folder name in /themes/
     * @return string the version number
     */
    public function get_latest_version($folder)
    {

        // If the changelog isn't there for some reason just assume it's v1.0.0
        if (!file_exists(METASLIDER_THEMES_PATH . trailingslashit($folder) . 'changelog.php')) {
            return 'v1.0.0';
        }
        $changelog = (include METASLIDER_THEMES_PATH . trailingslashit($folder) . 'changelog.php');
        return current(array_keys($changelog));
    }

    /**
     * Method to set the theme
     *
     * @param int|string $slideshow_id The id of the current slideshow
     * @param array      $theme        The selected theme object
     * @return bool true on successful update, false on failure.
     */
    public function set($slideshow_id, $theme)
    {

        // For legacy reasons we have to query the settings
        $settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);

        // If the theme isn't set, then they attempted to remove the theme
        if (!isset($theme['folder']) || is_wp_error($theme)) {
            $settings['theme'] = 'none';
            $settings['theme_customize'] = array();
            update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
            return update_post_meta($slideshow_id, 'metaslider_slideshow_theme', 'none');
        }

        // For custom themes, it's easier to use the legacy setting because the pro plugin
        // already hooks into it.
        if ('_theme' === substr($theme['folder'], 0, 6)) {
            $settings['theme'] = $theme['folder'];
            update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
        } else if (isset($settings['theme'])) {
            // If the theme isn't a custom theme, we should unset the legacy setting
            // unset($settings['theme']); // ! Pro requires this to be set
            $settings['theme'] = '';
            update_post_meta($slideshow_id, 'ml-slider_settings', $settings);

            // Save the customizations defaults
            $this->save_theme_customizations( $slideshow_id, $theme );
        }

        // We don't want to store customization manifest in metaslider_slideshow_theme postmeta
        if (isset($theme['customize'])) {
            unset($theme['customize']);
        }

        // This will return false if the data is the same, unfortunately
        return (bool) update_post_meta($slideshow_id, 'metaslider_slideshow_theme', $theme);
    }

    /**
     * Merge base theme customizations with specific theme customizations
     * 
     * @since 3.91.0
     * 
     * @param array $customizations Theme customizations
     * 
     * @return array
     */
    public function merge_theme_customizations( $customizations )
    {
        $base_customizations = ( include METASLIDER_THEMES_PATH . 'customize.php' );

        return array_merge( $customizations, $base_customizations );
    }

    /**
     * Save theme customizations
     * 
     * @since 3.91.0
     * 
     * @param int $slideshow_id Slideshow ID
     * @param array $theme Theme with all the props including folder and customize
     * 
     * @return void
     */
    public function save_theme_customizations( $slideshow_id, $theme )
    {
        $theme_settings = array();
        $customizations = $theme['customize'];

        /** 
         * Just save name => default/value of each array inside 'fields' from customize.php manifest
         *
         * In customize.php as:
         * array(
         *      array(
         *          'label' => 'Arrows',
         *          'fields' => array(
         *              array(
         *                  'label' => 'Normal',
         *                  'name' => 'arrows_color',
         *                  'type' => 'color',
         *                  'default' => '#336699',
         *                  'css' => '%s .lorem { color: %s }'
         *              ),
         *              // More arrays ...
         *          )
         *      )
         * )
         * 
         * Stored in db as:
         * array(
         *     'arrows_color' => '#336699',
         *     'another_setting' => '#feb123',
         * )
         */
        foreach ( $customizations as $row_item ) {
            foreach ($row_item['fields'] as $field_item) {
                $theme_settings[$field_item['name']] = $field_item['default'];
            }
        }

        $slideshow_settings = get_post_meta( $slideshow_id, 'ml-slider_settings', true );
        $slideshow_settings['theme_customize'] = $theme_settings;

        update_post_meta( $slideshow_id, 'ml-slider_settings', $slideshow_settings );
    }

    /**
     * Load in the selected theme assets.
     * 
     * @param int|string $slideshow_id The id of the current slideshow
     * @param string     $theme_id     The folder name of a theme
     * 
     * @return bool|WP_Error whether the file was included, or error class
     */
    public function load_theme($slideshow_id, $theme_id = null)
    {

        // Don't load a theme on the editor page.
        if (is_admin() && function_exists('get_current_screen') && $screen = get_current_screen()) {
            if ('metaslider-pro_page_metaslider-theme-editor' === $screen->id) {
            return false;
            }
        }

        $theme = (is_null($theme_id)) ? $this->get_current_theme($slideshow_id) : $this->get_theme_object($slideshow_id, $theme_id);

        // No theme for this slideshow? Set a default class
        if ( false === $theme ) {
            add_filter( 'metaslider_css_classes', array( $this, 'add_no_theme_class' ), 10, 3 );
            remove_filter( 'metaslider_css_classes', array( $this, 'add_theme_class' ), 10, 3 );
        }
        
        // 'none' is the default theme to load no theme. 
        // @TODO - Why we don't seem to get 'none' for slideshows with no theme?
        if ('none' == $theme_id) {
            return false;
        }
        if (is_wp_error($theme) || false === $theme) {
            return $theme;
        }

        // We have a theme, so lets add the class to the body
        $this->theme_id = $theme['folder'];

        // Add the theme class name to the slideshow
        add_filter('metaslider_css_classes', array($this, 'add_theme_class'), 10, 3);
        remove_filter( 'metaslider_css_classes', array( $this, 'add_no_theme_class' ), 10, 3 );

        // Check our themes for a match
        if (file_exists(METASLIDER_THEMES_PATH . $theme['folder'])) {
            $theme_dir = METASLIDER_THEMES_PATH . $theme['folder'];
        }

        /**
         * Check if we have extra themes/ folders added from external sources,
         * including MetaSlider Pro 
         * 
         * e.g. 
         * array(
         *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
         *  '/path/to/wp-content/themes/my-theme/ms-themes/'
         * )
         */
        $extra_themes = apply_filters('metaslider_extra_themes', array(), $slideshow_id);
        foreach ($extra_themes as $location) {
            if (file_exists(trailingslashit($location) . $theme['folder'])) {
                $theme_dir = trailingslashit($location) . $theme['folder'];
            }
        }

        // Load in the base theme class
        if (isset($theme_dir) && isset($theme['version'])) {
            require_once(METASLIDER_THEMES_PATH . 'ms-theme-base.php');
            return include_once trailingslashit($theme_dir) . trailingslashit($theme['version']) . 'theme.php';
        }
        
        // This should be a custom theme (pro)
        return $theme;
    }

    /**
     * Add the theme to the class so styles will apply. The theme can be
     * overridden, for example from the preview functionality
     * 
     * @param string $class        The slideshow classlist
     * @param string $slideshow_id The id of the slideshow
     * @param string $settings     The settings for the slideshow
     */
    public function add_theme_class($class, $slideshow_id, $settings)
    {
        $class .= ' ms-theme-' . $this->theme_id;
        return $class;
    }

    /**
     * Add the default class when no theme is selected
     * 
     * @since 3.31
     * 
     * @param string $class        The slideshow classlist
     * @param string $slideshow_id The id of the slideshow
     * @param string $settings     The settings for the slideshow
     */
    public function add_no_theme_class( $class, $slideshow_id, $settings )
    {
        $class .= ' ms-theme-default';
        return $class;
    }
}
