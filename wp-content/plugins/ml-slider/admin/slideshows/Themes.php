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

                        // Set a temporary array key to pass the settings.php file location
                        if (file_exists($edit_settings = trailingslashit($folder) . 'settings.php')) {
                            $data['theme_edit_settings_temp_'] = $edit_settings;
                        }

                        // Add a key to the theme array
                        $data = array( $data['folder'] => $data );

                        // Merge and set new theme to the top
                        $themes = array_merge($themes, $data);
                    }
                }
            }
        }

        // Add theme customization and edit settings
        $themes = $this->add_base_customize_settings($themes);
        $themes = $this->add_base_edit_settings($themes);

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
     * Add edit settings array key to each theme that have its own settings.php file
     * 
     * @since 3.98
     * 
     * @param array $themes Themes array from manifest file
     * 
     * @return array 
     */
    public function add_base_edit_settings($themes)
    {
        $default_settings = MetaSlider_Slideshow_Settings::defaults();

        // Convert booleans linked to <select> fields into strings
        foreach( array( 'links', 'navigation', 'smartCrop', 'random' ) as $setting_ ) {
            if ( isset( $default_settings[$setting_] ) && is_bool( $default_settings[$setting_] ) ) {
                $default_settings[$setting_] = $default_settings[$setting_] === true ? 'true' : 'false';
            }
        }

        // Remove non required settings
        foreach( array( 'title', 'type', 'theme' ) as $setting_ ) {
            if ( isset( $default_settings[$setting_] ) ) {
                unset( $default_settings[$setting_] );
            }
        }

        foreach ( $themes as $item ) {
            $folder = $item['folder'];

            // Check if we use a different settings.php file (e.g. is an external theme) for this theme or default
            $edit_settings_file = isset($item['theme_edit_settings_temp_']) ? $item['theme_edit_settings_temp_'] : METASLIDER_THEMES_PATH . $folder . '/settings.php';
            
            if ( in_array( $item['type'], array( 'free', 'premium', 'external' ) ) ) {
                if ( file_exists($edit_settings_file) ) {
                    $edit_settings = ( include $edit_settings_file );
                    $merged_settings = array_merge( $default_settings, $edit_settings );
                    $themes[$folder]['edit_settings'] = $merged_settings;
                } else {
                    // No settings.php file? Just assign defaults
                    $themes[$folder]['edit_settings'] = $default_settings;
                }
            }

            // Remove temporary array keys
            if (isset($themes[$folder]['theme_edit_settings_temp_'])) {
                unset($themes[$folder]['theme_edit_settings_temp_']);
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
                    'type' => 'custom',
                    'version' => (isset($theme['version']) ? $theme['version'] : 'v1')
                );
                
                // Data exclusive to v2 themes
                if (isset($theme['base'])) {
                    $custom_themes[$id]['base'] = $theme['base'];
                }
                if (isset($theme['base_title'])) {
                    $custom_themes[$id]['base_title'] = $theme['base_title'];
                }
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

            $res = update_post_meta( $slideshow_id, 'metaslider_slideshow_theme', 'none' );

            // @since 3.103
            do_action( 'metaslider_set_theme', $slideshow_id, $settings );

            return $res;
        }

        // For custom themes, it's easier to use the legacy setting because the pro plugin
        // already hooks into it.
        if ('_theme' === substr($theme['folder'], 0, 6)) {
            /* @since 3.94 - Make sure we keep theme_customize empty for custom themes based on core themes (v2 theme editor), 
             * let's empty 'theme_customize' to make sure we don't keep previous customize settings saved 
             * in case the new assigned theme doesn't have 'customize' to override it */
            if(isset($theme['type']) && ! in_array($theme['type'], array('free', 'premium'))) {
                if ('_theme_v2' === substr($theme['folder'], 0, 9)) {
                    // Is a theme created with v2 Theme editor
                    $settings['theme_customize'] = array();
                } elseif (isset($settings['theme_customize'])) {
                    // Is a theme created with v1 Theme editor (legacy)
                    unset($settings['theme_customize']);
                }
            }

            $settings['theme'] = $theme['folder'];
            update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
        } else if (isset($settings['theme'])) {

            /* @since 3.94 - If theme doesn't have 'customize' array key, let's empty 'theme_customize'
             * Important: even if is empty, 'theme_customize' is required in 'ml-slider_settings' postmeta db 
             * for themes created with v2 theme editor */
            if (! isset($theme['customize']) && isset($settings['theme_customize'])) {
                $settings['theme_customize'] = array();
            }

            // If the theme isn't a custom theme, we should unset the legacy setting
            // unset($settings['theme']); // ! Pro requires this to be set
            $settings['theme'] = '';
            update_post_meta($slideshow_id, 'ml-slider_settings', $settings);

            // Save the customizations defaults
            $this->save_theme_customizations( $slideshow_id, $theme );
        }

        // We don't want to store customization manifest and edit_settings 
        // in metaslider_slideshow_theme postmeta
        if (isset($theme['customize'])) {
            unset($theme['customize']);
        }
        if (isset($theme['edit_settings'])) {
            unset($theme['edit_settings']);
        }

        $res = (bool) update_post_meta( $slideshow_id, 'metaslider_slideshow_theme', $theme );
        
        // @since 3.103
        do_action( 'metaslider_set_theme', $slideshow_id, $settings );

        // This will return false if the data is the same, unfortunately
        return $res;
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
        $base_customizations    = ( include METASLIDER_THEMES_PATH . 'customize.php' );
        $all_customizations     = array_merge( $customizations, $base_customizations );

        // Merge Pro customize.php
        if ( defined( 'METASLIDERPRO_THEMES_PATH' ) 
            && metaslider_pro_is_active()
            && file_exists( $pro = METASLIDERPRO_THEMES_PATH . '/customize.php' )
        ) {
            $all_customizations = $this->merge_settings_by_name( $all_customizations, include $pro );
        }

        return $all_customizations;
    }

    /**
     * Merge $a customize.php 'settings' into $b customize.php 'settings' by matching 'section' 
     * 
     * @since 3.96
     * 
     * @param array $a Customize file A
     * @param array $b Customize file B
     * 
     * @return array $a modified version
     */
    public function merge_settings_by_name( &$a, $b ) {
        foreach ($b as $b_section) {
            foreach ( $a as &$a_section ) {
                // Check if sections match by 'name'
                if (isset( $a_section['name'], $b_section['name'] ) 
                    && $a_section['name'] === $b_section['name']
                ) {
                    // Merge 'settings' arrays
                    if ( isset( $a_section['settings'], $b_section['settings'] ) ) {
                        $a_section['settings'] = array_merge( $a_section['settings'], $b_section['settings'] );
                    }
                }
            }
            unset( $a_section ); // Break reference to prevent side effects
        }

        return $a;
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
        $customizations = $theme['customize'];

        // To fix incoming warning: foreach() argument must be of type array|object, null given
        if (! is_array($customizations)) {
            return false;
        }

        $theme_settings = $this->get_customize_defaults($customizations, array('color')); // Only get 'color' settings

        $slideshow_settings = get_post_meta( $slideshow_id, 'ml-slider_settings', true );
        $slideshow_settings['theme_customize'] = $theme_settings;

        update_post_meta( $slideshow_id, 'ml-slider_settings', $slideshow_settings );
    }

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
     * 
     * @since 3.94
     * 
     * @param array $customizations All the data from 'customize' from theme's manifest file
     * @param bool|array $filter    Include only these setting types only. e.g. array('color') = only include color settings
     * 
     * @return array
     */
    public function get_customize_defaults($customizations, $filter = false)
    {
        $res = array();

        if (! is_array($customizations)) {
            return $res;
        }

        // Loop each section that has 'status' key
        foreach ($customizations as $section) {

            if (!$filter || in_array($section['type'], $filter)) {
                // Extract 'name' and 'default' for 'section' type
                $res[$section['name']] = $section['default'];
            }

            // Loop each section 'settings' key
            foreach ($section['settings'] as $row_item) {
                
                if ($row_item['type'] === 'message') {
                    // If type is 'message', we don't need to do anything
                } elseif ($row_item['type'] === 'section') {
                    // If type is 'section', let's look for the list of fields.
                    // Usually for multiple color fields grouped together

                    if (!$filter || in_array($row_item['type'], $filter)) {
                        $res[$row_item['name']] = $row_item['default'];
                    }
                } elseif ($row_item['type'] === 'fields') {
                    // If type is 'fields', let's look for the list of fields. 
                    // Usually for multiple color fields grouped together

                    foreach ($row_item['fields'] as $field_item) {
                        if (!$filter || in_array($field_item['type'], $filter)) {
                            $res[$field_item['name']] = $field_item['default'];
                        }
                    } 
                } else { 
                    if (!$filter || in_array($row_item['type'], $filter)) {
                        // Get individual fields
                        $res[$row_item['name']] = $row_item['default'];
                    }
                }
            }
        }

        return $res;
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
        $is_theme_editor_screen = is_admin() 
            && function_exists('get_current_screen') 
            && ($screen = get_current_screen()) 
            && 'metaslider-pro_page_metaslider-theme-editor' === $screen->id;

        // Don't load a theme on the editor page.
        if ($is_theme_editor_screen && (! isset($_GET['version']) || $_GET['version'] == 'v1')) {
            return false;
        }

        // @since 3.94 - Adjust $theme_id load for v2 editor
        if ($is_theme_editor_screen && (isset($_GET['version']) && $_GET['version'] == 'v2')) {
            if (! empty($_GET['theme_slug'] ?? null)) {
                // e.g. /wp-admin/admin.php?page=metaslider-theme-editor&theme_slug=_theme_1733247735&version=v2
                $custom_theme_slug  = sanitize_key($_GET['theme_slug']);
                $custom_themes      = get_option('metaslider-themes');

                if ($custom_themes 
                    && isset($custom_themes[$custom_theme_slug]) 
                    && isset($custom_themes[$custom_theme_slug]['base'])
                ) {
                    $theme_id = $custom_themes[$custom_theme_slug]['base'];
                }
            } elseif (! empty($_GET['base'] ?? null)) {
                // e.g. /wp-admin/admin.php?page=metaslider-theme-editor&add=true&version=v2&base=outline
                $theme_id = sanitize_key($_GET['base']);
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
        $extra_themes = apply_filters('metaslider_extra_themes', array());
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
     * Filter the manifest data by field 'dependencies' and return final CSS
     * 
     * @since 3.96
     * 
     * @param array $data The manifest data with all the customzie settings
     * 
     * @return string
     */
    public function filter_customize_css( $data )
    {
        foreach ( $data as $key => $item ) {
            if ( isset( $item['dependencies'] ) && $item['dependencies'] !== false ) {

                foreach ( $data[$key]['dependencies'] as $dep ) {
                    
                    // If dependecy 'when' value is different to $data[$key]['value] (aka $item['value']) value, 
                    // let's exclude from $data
                    if ( $dep['when'] !== $item['value'] ) {
                        if ( isset( $data[$dep['show']] ) ) {
                            unset($data[$dep['show']]);
                        }
                    }
                }
            }
        }

        // Let's extract only the CSS and merge as a single string
        $css = "";
        foreach ( $data as $item ) {
            if ( ! empty( $item['css'] ) ) {
                $css .= $item['css'];
            }
        }

        return $css;
    }

    /**
     * When match get the value of a css_field 
     * from another field based on css_field value
     * 
     * @since 3.96
     * 
     * @return bool|string|integer|float false if no match found (not a css_field)
     */
    public function value_linked_to_field( $manifest, $stored, $name )
    {
        foreach ( $manifest as $section ) {
            // Loop each section 'settings' key
            foreach ( $section['settings'] as $row_item ) {

                // Skip fields, its childs and message types
                if ( $row_item['type'] !== 'fields' && $row_item['type'] !== 'message' ) {

                    if ( isset( $row_item['css_field'] )
                        && $row_item['css_field'] === $name 
                        && isset( $stored[$row_item['name']] )
                    ) {
                        // Exit early and return the matching data
                        return $stored[$row_item['name']];
                    }
                }
            }
        }
    
        return false;
    }

    /**
     * Loop each theme customize setting from customize.php and build final working CSS
     * 
     * 
     * @since 3.94
     * 
     * @param $array $manifest  The manifest customize data. e.g. $manifest['customize']
     * @param $array $stored    The stored data. e.g. 'theme_customize' data at ml-slider_settings postmeta
     * @param int $slideshow_id Sldieshow ID
     * 
     * @return string
     */
    public function build_customize_css($manifest, $stored, $slideshow_id)
    {
        /**
         * We'll store some data linked to the setting name
         * 
         * @since 3.96
         * 
         * e.g.
         * array(
         *      array(
         *          'arrows_color' => array(
         *              'value' => '#fff'
         *              'css' => '[ms_id] .lorem { color: [ms_value] }'
         *              'dependencies' => array()
         *          )
         *      ),
         *      // More elements ...
         * )
         **/ 
        $pre_output = array();

        foreach ($manifest as $section) {

            // Loop each section 'settings' key
            foreach ($section['settings'] as $row_item) {

                if ($row_item['type'] === 'message') {
                    // If type is 'message', we don't need to do anything
                } elseif ($row_item['type'] === 'fields') {
                    // If type is 'fields', let's look for the list of fields. 
                    // Usually for multiple color fields grouped together

                    foreach ($row_item['fields'] as $field_item) {
                        
                        // Check if setting from manifest exists in db
                        if (isset($stored[$field_item['name']]) && isset($field_item['css'])) {
                            if ($field_item['css'] == 'css_rules' && isset($field_item['css_rules'])) {
                                // CSS code is actually on css_rules key (aka css = 'css_rules') BUT based on value parameter
                                // @TODO Maybe allow array of CSS too as in regular css key?

                                // Store final CSS an dependencies if any linked to its name
                                $pre_output[$field_item['name']] = array(
                                    'value' => $field_item['css_rules'][$stored[$field_item['name']]],
                                    'css' => $this->adjust_css_placeholders(
                                        $field_item['css_rules'][$stored[$field_item['name']]], 
                                        "#metaslider-id-{$slideshow_id}", 
                                        $stored[$field_item['name']]
                                    ) . "\n",
                                    'dependencies' => $field_item['dependencies'] ?? false
                                );

                            } elseif (is_array($field_item['css'])) {
                                $css_merge = "";

                                // CSS is an array of strings
                                foreach ($field_item['css'] as $css_item) {
                                    
                                    $css_merge .= $this->adjust_css_placeholders(
                                        $css_item,
                                        "#metaslider-id-{$slideshow_id}", 
                                        $stored[$field_item['name']]
                                    ) . "\n";
                                }

                                // Store final CSS an dependencies if any linked to its name
                                $pre_output[$field_item['name']] = array(
                                    'value' => $stored[$field_item['name']],
                                    'css' => $css_merge,
                                    'dependencies' => $field_item['dependencies'] ?? false
                                );

                            } else {
                                // CSS is a single string
                                // Store final CSS an dependencies if any linked to its name
                                $pre_output[$field_item['name']] = array(
                                    'value' => $stored[$field_item['name']],
                                    'css' => $this->adjust_css_placeholders(
                                        $field_item['css'],
                                        "#metaslider-id-{$slideshow_id}", 
                                        $stored[$field_item['name']]
                                    ) . "\n",
                                    'dependencies' => $field_item['dependencies'] ?? false
                                );
                            }
                        }

                    } 
                } else { 
                    
                    // Check if setting from manifest exists in db
                    if (isset($stored[$row_item['name']]) && isset($row_item['css'])) {
                        if ($row_item['css'] == 'css_rules' && isset($row_item['css_rules'])) {
                            // CSS code is actually on css_rules key (aka css = 'css_rules') BUT based on value parameter
                            // @TODO Maybe allow array of CSS too as in regular css key?

                            // Is this field linked to another field through their css_field key? 
                            // We're using only value_linked_to_field() on settings 
                            // in row (not inside fields) with css_rules only. Perhaps support more in future?
                            $linked_field = $this->value_linked_to_field( $manifest, $stored, $row_item['name'] );

                            if ( $linked_field === false ) {
                                // Store final CSS an dependencies if any linked to its name
                                $pre_output[$row_item['name']] = array(
                                    'value' => $stored[$row_item['name']],
                                    'css' => $this->adjust_css_placeholders(
                                        $row_item['css_rules'][$stored[$row_item['name']]], 
                                        "#metaslider-id-{$slideshow_id}", 
                                        $stored[$row_item['name']]
                                    ) . "\n",
                                    'dependencies' => $row_item['dependencies'] ?? false
                                );
                            } else {
                                // Is a css_field linked; take css from linked css_field
                                $pre_output[$row_item['name']] = array(
                                    'value' => $stored[$row_item['name']],
                                    'css' => $this->adjust_css_placeholders(
                                        $row_item['css_rules'][$stored[$row_item['name']]], 
                                        "#metaslider-id-{$slideshow_id}", 
                                        $linked_field
                                    ) . "\n",
                                    'dependencies' => $row_item['dependencies'] ?? false
                                );
                            }
                            
                        } elseif ( $row_item['css'] == 'css_field' ) {
                            // Is a css_field. We're skipping it...
                        } elseif (is_array($row_item['css'])) {
                            $css_merge = "";

                            // CSS is an array of strings
                            foreach ($row_item['css'] as $css_item) {

                                $css_merge .= $this->adjust_css_placeholders(
                                    $css_item, 
                                    "#metaslider-id-{$slideshow_id}", 
                                    $stored[$row_item['name']]
                                ) . "\n";
                            }

                            // Store final CSS an dependencies if any linked to its name
                            $pre_output[$row_item['name']] = array(
                                'value' => $stored[$row_item['name']],
                                'css' => $css_merge,
                                'dependencies' => $row_item['dependencies'] ?? false
                            );
                        } else {
                            // CSS is a single string
                            // Store final CSS an dependencies if any linked to its name
                            $pre_output[$row_item['name']] = array(
                                'value' => $stored[$row_item['name']],
                                'css' => $this->adjust_css_placeholders(
                                    $row_item['css'], 
                                    "#metaslider-id-{$slideshow_id}", 
                                    $stored[$row_item['name']]
                                ) . "\n",
                                'dependencies' => $row_item['dependencies'] ?? false
                            );
                        }
                    }

                }
            }
        }

        return $this->filter_customize_css( $pre_output );
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

    /**
     * Adjust CSS from customize.php to replace [ms_id] and [value] placeholders
     * 
     * @since 3.94
     * 
     * @param string $css               e.g. '[ms_id] .flexslider .caption-wrap a { color: [ms_value] }'
     * @param string $id                e.g. "#metaslider-id-{$slideshow_id}"
     * @param string|int|float $value   e.g. 'rgba(255,255,255,0.8)' or 18
     * 
     * @return string
     */
    public function adjust_css_placeholders($css, $id, $value)
    {
        // @since 3.99
        $css = apply_filters( 'metaslider_load_font', $css, $value );

        $search = array(
            '[ms_id]',
            '[ms_value]',
            '[ms_field_value]'
        );

        $replace = array(
            $id,
            strip_tags( $value ),
            strip_tags( $value )
        );

        return str_replace($search, $replace, $css);
    }

    /**
     * Sanitize and validate color formats (hex, rgb, rgba)
     * 
     * @since 3.95
     * 
     * @param string $color 
     * 
     * @return string|bool Return color or false if invalid
     */
    public function sanitize_color( $color ) {
        $hexRegex = '/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/';
        $rgbRegex = '/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/';
        $rgbaRegex = '/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0|1|0?\.\d+)\s*\)$/';

        return preg_match( $hexRegex, $color ) || preg_match( $rgbRegex, $color ) || preg_match( $rgbaRegex, $color ) ? $color : false;
    }

    /**
     * Validate and sanitize range
     * 
     * @since 3.96
     * 
     * @param string $number 
     * 
     * @return string|bool Return number as string or false if invalid
     */
    public function sanitize_range( $number, $min, $max ) {
        // Clean up the input to remove unwanted characters
        $number = sanitize_text_field( $number );

        // Match valid numbers: -123, 45, 1.8, -0.5, or 0
        if ( preg_match( '/^-?\d+(\.\d)?$/', $number ) ) {
            // If valid, cast to float or integer based on the input
            $number = strpos( $number, '.') !== false ? (float) $number : (int) $number;
            //$min = strpos( (String) $min, '.') !== false ? (float) $min : (int) $min;
            //$max = strpos( (String) $max, '.') !== false ? (float) $max : (int) $max;

            // Check $number is between $min and $max
            return $number >= $min && $number <= $max ? (String) $number : false;
        }

        // Invalid $number
        return false;
    }

    /**
     * Validate and sanitize select
     * @TODO - Check if $value is one of the available options in $item['options']?
     * 
     * @since 3.96
     * 
     * @param string $number 
     * 
     * @return string
     */
    public function sanitize_select( $value ) {
        return sanitize_text_field( $value );
    }

    /**
     * Get theme manifest (customize.php)
     * 
     * @since 3.96
     * 
     * @param string $theme Theme name in lowercase (slug). e.g. 'bitono'
     * @param string $type  'free' or 'premium'
     * 
     * @return array
     */
    public function get_theme_manifest( $theme, $type )
    {
        $manifest = array();

        // Is a premium, external or custom theme (v2), override $manifest path
        if ($type !== 'free') {
            // Check if is a custom v2 based on a free theme
            $manifest = $this->add_base_customize_settings_single($theme);

            /**
             * Check if is a premium or custom theme (v2) based on a premium theme
             * by looping extra themes/ folders added from external sources,
             * including MetaSlider Pro.
             * We may also support external themes (custom coded themes added by users).
             * 
             * e.g. 
             * array(
             *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
             *  '/path/to/wp-content/themes/my-theme/ms-themes/'
             * )
             */
            if (! count($manifest)) {
                $extra_themes = apply_filters('metaslider_extra_themes', array());

                foreach ($extra_themes as $location) {
                    // Check if customize.php file that belongs to $theme as theme name (lowercase) exists
                    if (file_exists($customize_file = trailingslashit($location) . trailingslashit($theme) . 'customize.php')) {
                        // Get the data from customize.php files
                        $manifest = $this->add_base_customize_settings_single(
                            $theme, $customize_file
                        );
                        break;
                    }
                }
            }
        } else {
            // Is a free theme - Get data from themes/$theme/customize.php
            $manifest = $this->add_base_customize_settings_single($theme);
        }

        return $manifest;
    }

    /**
     * Theme is free, premium... ?
     * 
     * @since 3.96
     * 
     * @param string $theme Theme name in lowercase (slug). e.g. 'bitono'
     * 
     * return string|bool
     */
    public function get_theme_type( $theme )
    {
        $data = $this->get_single_theme( $theme );

        if ( isset( $data['type'] ) ) {
            return $data['type'];
        }
        
        return false;
    }

    /**
     * Validate and sanitize manifest vs stored or submitted theme customize
     * 
     * @since 3.96
     * 
     * @param $array $manifest  The manifest customize data. e.g. $manifest['customize']
     * @param $array $stored    The stored data. e.g. 'theme_customize' data at ml-slider_settings postmeta
     * 
     * return array
     */
    public function validate_theme_stored( $manifest, $stored )
    {
        // Valid and sanitized $stored
        $stored_valid = array();

        if ( ! is_array( $manifest ) ) {
            return $stored_valid;
        }

        foreach ( $manifest as $section ) {
            // Loop each section 'settings' key
            foreach ( $section['settings'] as $row_item ) {
                if ( $row_item['type'] === 'message' ) {
                    // If type is 'message', we don't need to do anything
                } elseif ( $row_item['type'] === 'fields' ) {
                    // If type is 'fields', let's look for the list of fields. 
                    // Usually for multiple color fields grouped together

                    foreach ( $row_item['fields'] as $field_item ) {
                        // Check if setting from manifest exists in $stored and sanitized value is not false
                        if ( isset( $stored[$field_item['name']] ) 
                            && ( $value_ = $this->sanitize_theme_setting( $field_item, $stored[$field_item['name']] ) ) !== false
                        ) {
                            $stored_valid[$field_item['name']] = $value_;
                        }
                    } 
                } else { 
                    // Check if setting from manifest exists in $stored and sanitized value is not false
                    if ( isset( $stored[$row_item['name']] ) 
                        && ( $value_ = $this->sanitize_theme_setting( $row_item, $stored[$row_item['name']] ) ) !== false
                    ) {
                        $stored_valid[$row_item['name']] = $value_;
                    }
                }
            }
        }

        return $stored_valid;
    }

    /**
     * Sanitize individual custom theme setting
     * 
     * @since 3.96
     * 
     * @param array $item   e.g. array(
     *                          'label' => esc_html__('Default', 'ml-slider'),
     *                          'name' => 'arrows_color',
     *                          'type' => 'color',
     *                          'default' => '#333333',
     *                          'css' => '[ms_id] .flexslider .flex-direction-nav li a { background: [ms_value] }'
     *                      )
     * @param string|float $value The db stored or submitted value 
     * 
     * @return bool|string If false, stored value is invalid
     */
    public function sanitize_theme_setting( $item, $value )
    {
        switch ( $item['type'] ) {
            case 'color':
                return $this->sanitize_color( $value );
            break;
            case 'select':
                return $this->sanitize_select( $value );
            break;
            case 'range':
                return $this->sanitize_range( $value, $item['min'], $item['max'] );
            break;
            case 'font':
                return sanitize_text_field( $value );
            break;
        }

        return false;
    }
}
