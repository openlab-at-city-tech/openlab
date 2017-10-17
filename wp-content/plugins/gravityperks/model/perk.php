<?php

class GP_Perk {

    public $tooltips;

    /**
    * When HTML elements are generated for the settings page, they are saved here and auto saved.
    *
    * @see generate_enable_checkbox()
    *
    */
    public $setting_ids;

    protected $basename;

    /**
    * A safe "slug" for use in option names, html IDs, etc...
    *
    * @var mixed
    */
    protected $slug;

    function __construct( $perk_file = null ) {

        if( ! class_exists( 'GWPerks') ) {
	        return;
        }

        if( ! $perk_file && empty( $this->basename ) ) {
        	_doing_it_wrong( __CLASS__ . ':' . __METHOD__, 'Oops! You\'re instantiating this perk to early.', '1.2.21' );
        	return;
        }

        $this->basename = $perk_file;
        $this->slug = strtolower( basename( $perk_file, '.php' ) );

    }

    function init() {

        $this->maybe_setup();

    }

    /**
    * Get a Perk object by file.
    *
    * @param string $perk_file
    * @return GWPerk Object or WP_Error Object
    */
    public static function get_perk( $perk_file ) {

        $perk_class = str_replace( '-', '_', basename( $perk_file, '.php' ) );

        if( ! class_exists( $perk_class ) ) {

            $perk_path = WP_PLUGIN_DIR . '/' . $perk_file;
            if( ! file_exists( $perk_path ) ) {
                return new WP_Error( 'perk_file_error', __('The file for this perk does not exist.', 'gravityperks' ) );
            }

            include_once( $perk_path );
            if( ! class_exists( $perk_class ) ) {

	            $perk_bits     = explode( '/', $perk_file );
	            $alt_perk_file = sprintf( '%s/%s/class-%s', WP_PLUGIN_DIR, $perk_bits[0], $perk_bits[1] );

                if( file_exists( $alt_perk_file ) ) {
                    include_once( $alt_perk_file );
                }

                if( ! class_exists( $perk_class ) ) {
                    $perk_data = self::get_perk_data( $perk_file );
                    if( ! empty( $perk_data ) ) {
                        $filename = strtolower( str_replace( ' ', '-', $perk_data['Name'] ) );
                        $alt_perk_file = sprintf( '%s/%s/class-%s.php', WP_PLUGIN_DIR, $perk_bits[0], $filename );
                        if( file_exists( $alt_perk_file ) ) {
                            include_once( $alt_perk_file );
                        }
                    }
                }

                if( ! class_exists( $perk_class ) ) {
                    return new WP_Error( 'perk_class_error', __( 'There is no class for this perk.', 'gravityperks' ) );
                }

            }


        }

        if( is_callable( array( $perk_class, 'get_instance' ) ) ) {
            $perk = call_user_func( array( $perk_class, 'get_instance' ), $perk_file );
        } else {
            $perk = new $perk_class( $perk_file );
        }

        return $perk;
    }

    public function get_property($property) {
        return isset($this->$property) ? $this->$property : false;
    }

    public function get_id() {
        return $this->get_property('id');
    }

    /**
    * Check if the minimum version of Gravity Perks is installed for this perk.
    *
    */
    public function is_gravity_perks_supported() {

        if( isset( $this->min_gravity_perks_version ) ) {
            $is_supported = version_compare( GWPerks::get_version(), $this->min_gravity_perks_version, '>=' );
        } else {
            $is_supported = true;
        }

        return $is_supported;
    }

    /**
    * Check if the minimum version of Gravity Forms is installed for this perk.
    *
    */
    public function is_gravity_forms_supported() {
        return isset($this->min_gravity_forms_version) ? GWPerks::is_gravity_forms_supported($this->min_gravity_forms_version) : true;
    }

    /**
    * Check if the minimum version of WordPress is installed for this perk.
    *
    */
    public function is_wp_supported() {
        return isset($this->min_wp_version) ? GWPerks::is_wp_supported($this->min_wp_version) : true;
    }

    /**
     * Check for minimum version of plugin.
     *
     * @param  array  $args Requirement options including 'class', 'property', 'method', and 'version'
     * @return boolean       Return true if minimum version of required plugin is installed. If no minimum version is specified, return true if required plugin class exists.
     */
    public function has_min_version($args) {

        extract( wp_parse_args( $args, array(
            'class' => false,
            'property' => false,
            'method' => false,
            'version' => false
        ) ) );

        if( !$class || !class_exists($class) )
            return false;

        if( $property ) {
            $class_properties = get_class_vars($class);
            if( isset( $class_properties[$property] ) && version_compare( $class_properties[$property], $version, '<') )
                return false;
        }

        if( $method && method_exists( $class, $method ) ) {
            $plugin_version = call_user_func( array( $class, $method ) );
            if( version_compare( $plugin_version, $version, '<' ) )
                return false;
        }

        return true;
    }

    /**
     * Check if the current perk has all it's requirements met.
     *
     * If requirements are not met, return an array of failed requirements;
     *
     * @return mixed true if requirements are met, array of failed requirements otherwise
     */
    public function is_supported() {
        $failed_requirements = $this->get_failed_requirements();
        return empty( $failed_requirements );
    }

    public function get_failed_requirements() {

        $failed_requirements = array();

        if( !$this->is_gravity_perks_supported() )
            $failed_requirements[] = array( 'code' => 'gravity_perks_required' );

        if( !$this->is_gravity_forms_supported() )
            $failed_requirements[] = array( 'code' => 'gravity_forms_required' );

        if( !$this->is_wp_supported() )
            $failed_requirements[] = array( 'code' => 'wp_required' );

        $requirements = array();
        if( method_exists( $this, 'requirements' ) )
            $requirements = $this->requirements();

        foreach( $requirements as $requirement ) {

            if( $this->has_min_version($requirement) )
                continue;

            $failed_requirements[] = array( 'code' => 'other_required', 'message' => gwar($requirement, 'message') );

        }

        return $failed_requirements;
    }

    public function maybe_setup() {

        $is_doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX === true;

        if( ( $is_doing_ajax || ! is_admin() ) && is_multisite() ) {
            return;
        }

        $saved_version_key   = $this->key( 'version' );
        $has_version_changed = isset( $this->version ) && $this->version != get_option( $saved_version_key );

        if( $has_version_changed ) {

            $this->setup();

            update_option( $saved_version_key, $this->version );

        }

    }

    protected function setup() { }

    public function activate() { }

    public function uninstall() { }



    // HELPER FUNCTIONS //

    function get_link_for($type, $plugin_file = false) {

        $plugin_file = $plugin_file ? $plugin_file : $this->basename;
        $base_url = admin_url('admin.php?page=gwp_perks');

        switch($type) {

        case 'activate':
            return wp_nonce_url( admin_url("plugins.php?action=activate&plugin=$plugin_file"), "activate-plugin_{$plugin_file}");

        case 'deactivate':
            return wp_nonce_url( admin_url("plugins.php?action=deactivate&plugin=$plugin_file"), "deactivate-plugin_{$plugin_file}");

        case 'uninstall':
            return wp_nonce_url( admin_url("plugins.php?action=uninstall&plugin=$plugin_file" ), "uninstall-plugin_{$plugin_file}" );

        case 'delete':
            $page = "plugins.php?action=delete-selected&checked[0]=$plugin_file&gwp=1";
            $url = is_multisite() ? network_admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url( $page );
            return wp_nonce_url( $url, "bulk-plugins" );

        case 'install':
            $page = "update.php?action=install-plugin&plugin=$plugin_file&gwp=1&from=gwp";
            // @TODO: might not need to pass blog ID anymore since we no longer are sending to network page for install
            $url = is_multisite() ? admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url($page);
            return wp_nonce_url( $url, "install-plugin_$plugin_file");

        case 'upgrade':
            $page = "update.php?action=upgrade-plugin&plugin={$plugin_file}&gwp=1&from=gwp";
            $url = is_multisite() ? network_admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url($page);
            return wp_nonce_url( $url, "upgrade-plugin_{$plugin_file}");

        case 'documentation':

            $documentation = $this->get_documentation();
            $url = esc_url( add_query_arg( array( 'view' => 'documentation', 'slug' => $this->basename, 'TB_iframe' => true, 'width' => 600, 'height' => 500), $base_url ) );

            // support returning a array with a URL for documentation
            if( is_array( $documentation ) ) {
                switch( $documentation['type'] ) {
                case 'url':
                    $url = $documentation['value'];
                    break;
                }
            }

            return $url;

        case 'settings':
            return esc_url( add_query_arg( array( 'view' => 'perk_settings', 'slug' => $this->basename, 'TB_iframe' => true, 'width' => 600, 'height' => 500 ), $base_url ) );
            break;

        // @TODO REVIEW BELOW //

        case 'purchase':
            return 'http://gravitywiz.com/gravity-perks/';
            break;
        case 'upgrade_details':
            return esc_url( add_query_arg(array('page' => 'gwp_perks', 'view' => 'perk_info', 'plugin' => $this->slug, 'TB_iframe' => 'true', 'width' => 600, 'height' => 500), admin_url('admin.php')) );
            break;
        }

        return '';
    }

    /**
    * Load perk data from plugin headers
    *
    */
    function load_perk_data() {
    	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        $this->data = self::get_perk_data( $this->basename );
    }

    function has_update() {

        if ( current_user_can( 'update_plugins' ) ) {
            $current = get_site_transient( 'update_plugins' );
            if( isset( $current->response[ $this->basename ] ) )
                return $current->response[ $this->basename ];
        }

        return false;
    }

    function failed_requirements_tooltip( $requirements ) {

        $messages = $this->get_requirement_messages( $requirements );

        $tooltip = '<ul><li>' . implode( '</li><li>', $messages ) . '</li></ul>';
        $icon = '<a class="gp-requirements tooltip gf_tooltip" tooltip="' . $tooltip . '" title="' . $tooltip . '" href="javascript:void(0);">Requirements</a>';

        echo $icon;

    }

    function get_requirement_messages( $requirements ) {

        $messages = array();
        foreach( $requirements as $requirement ) {
            if( gwar( $requirement, 'message' ) ) {
                $messages[] = gwar( $requirement, 'message' );
            } else {
                $messages[] = str_replace( "\"", "'", GWPerks::get_message( gwar( $requirement, 'code' ), $this->basename) );
            }
        }

        return $messages;
    }

    function drop_tables( $tables ) {
        GravityPerks::drop_tables( $tables );
    }

    function drop_options() {
        global $wpdb;

        $key     = $this->key( '' );
        $sql     = "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$key}%'";
        $results = $wpdb->get_results( $sql );
        $options = wp_list_pluck( $results, 'option_name' );

        foreach( $options as $option ) {
            delete_option( $option );
        }

    }

    function add_css_class( $class, $classes = '' ) {
        $classes = explode( ' ', $classes );
        array_push( $classes, $class );
        return implode( ' ', array_unique( $classes ) );
    }

    public static function doing_ajax( $action = false ) {

        if(!defined('DOING_AJAX') || !DOING_AJAX)
            return false;

        return $action ? $action == $_REQUEST['action'] : true;
    }





    // STATIC HELPER FUNCTIONS //

    public static function is_perk( $perk_file, $clear_plugin_cache = false ) {

        $plugin_path = WP_PLUGIN_DIR . '/' . $perk_file;
        if( !file_exists($plugin_path) )
            return false;

        $plugin = self::get_perk_data( $perk_file, $clear_plugin_cache );
        if( empty($plugin) || gwar($plugin, 'Perk') != 'True' )
            return false;

        return true;
    }

    public static function is_installed($perk_file) {
        return self::get_perk_data( $perk_file ) !== false;
    }

    public static function get_perk_data( $perk_file, $clear_plugin_cache = false ) {

        // get all plugin data (cached via get_plugins function)
        $plugins = GWPerks::get_plugins( $clear_plugin_cache );

        foreach( $plugins as $plugin_file => $plugin ) {
            if( $perk_file == $plugin_file )
                return $plugin;
        }

        return false;
    }



    // PERK DISPLAY VIEWS //

    function get_documentation( ) {

        if( !is_callable( array( $this, 'documentation' ) ) )
            $documentation = __( 'Oops! It doesn\'t look like this perk has any documentation.', 'gravityperks' );

        return $this->documentation();
    }

    /**
    * Include Markdown and run the perk documentation through it before outputting it to the screen.
    *
    */
    function display_documentation() {
    	_deprecated_function( __method__, '1.2.18.8' );
        echo GWPerks::markdown( $this->get_documentation() );
    }



    // PERK SETTINGS API //

    public static function save_perk_settings($slug, $new_settings) {

        $stored_settings = get_option("{$slug}_settings");
        if(!$stored_settings)
            $stored_settings = array();

        foreach($new_settings as $key => $setting) {
            $stored_settings[$key] = $setting;
        }

        return update_option("{$slug}_settings", $stored_settings);
    }

    public static function get_perk_settings($slug) {
        return get_option("{$slug}_settings");
    }





    // REVIEW ALL CODE BELOW THIS LINE //





    public function update() {
        $perk_options = $this->get_save_options();
        GWPerks::update_perk_option($perk_options);
    }

    public function set_property($property, $value) {
        $this->$property = $value;
        $this->update();
    }

//    public function activate() {
//        $this->set_property('is_active', true);
//    }

    public function deactivate() {
        $this->set_property('is_active', false);
    }

    public function delete() {

        // force refresh of installed perks cache on next page load
        GWPerks::flush_installed_perks();

        $perk_dir = str_replace(basename($this->filename), '', $this->filename);

        if(!$perk_dir)
            return new WP_Error('perk_delete', __('There was an issue deleting this perk. The perk directory was not available.', 'gravityperks'));

        $perk_dir_path = GWP_PERKS_DIR . $perk_dir;

        $success = self::remove_directory($perk_dir_path);

        if(!is_wp_error($installer->result))
            GWPerks::flush_installed_perks();

        return $success;
    }

    /**
    * Get default perks options, loop through and save current Perk values for each property. New default options
    * will automatically be saved.
    *
    */
    public function get_save_options() {

        $default_options = self::get_default_perk_options($this->slug);
        $save_options = array();

        foreach($default_options as $key => $value) {
            $save_options[$key] = isset($this->$key) ? $this->$key : '';
        }

        return $save_options;
    }



    /**
    * Looks for the form_settings_ui() method and the form_settings_js() method and add hooks
    * for the existing methods. Also sets $has_form_settings property to true, indicating that
    * the custom form settings tab should be displayed.
    *
    */
    public function enqueue_form_settings() {

        GWPerks::$has_form_settings = true;

        if(method_exists($this, 'form_settings_ui'))
            add_action('gws_form_settings', array(&$this, 'form_settings_ui'));

        if(method_exists($this, 'form_settings_js'))
            add_action('gform_editor_js', array(&$this, 'form_settings_js'));

    }

    /**
    * Looks for the field_settings_ui() method and the field_settings_js() method and enqueue
    * the existing methods. Also sets $has_field_settings property to true, indicating that
    * the custom field settings tab should be displayed.
    *
    */
    public function enqueue_field_settings( $priority = 10 ) {

        GWPerks::enqueue_field_settings();

        if( method_exists( $this, 'field_settings_ui' ) )
            add_action( 'gws_field_settings', array( $this, 'field_settings_ui' ) );

        if( method_exists( $this, 'field_settings_js' ) )
            add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );

    }

    public function add_tooltip( $key, $content ) {
        $this->tooltips[$key] = $content;
        add_filter( 'gform_tooltips', array( $this, 'load_tooltips' ) );
    }

    public function load_tooltips( $tooltips ) {
        return array_merge( $tooltips, $this->tooltips );
    }

    /**
    * Enqueue a script via WPs wp_enqueue_script() function. Optionally specify an array of pages for
    * which the script should be loaded.
    *
    * @param mixed $args
    */
    public static function enqueue_script($args) {

        $defaults = array(
            'pages' => array(),
            'handle' => '',
            'src'  => false,
            'deps' => array(),
            'ver' => false,
            'in_footer' => false
        );

        extract( shortcode_atts( $defaults, $args ) );

        if( ! $handle )
            return;

        if( !empty($pages) ) {
            $pages = is_array($pages) ? $pages : array($pages);
            if( in_array(gwget('page'), $pages) )
                wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        } else if(empty($pages)) {
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        }

    }

    public function get_base_url() {
        $folder = basename( dirname( $this->basename ) );
        // plugins_url() will auto-handle http/https, WP_UPLOAD_URL will not
        return plugins_url( $folder );
    }

    public function get_base_path() {
        $folder = basename(dirname($this->basename));
        return WP_PLUGIN_DIR . "/$folder";
    }

    public function key( $key ) {
	    $prefix = isset( $this->prefix ) ? $this->prefix : $this->slug . '_';
        return $prefix . $key;
    }

    public function field_prop($field, $prop, $prefix = false) {
        if($prefix === false)
            $prefix = $this->slug . '_';

        return gwar($field, $prefix . $prop);
    }

    function include_field( $class_name = false, $file = false ) {

		$field_file_paths = array(
			$file,
			$this->get_base_path() . '/fields.php',
			$this->get_base_path() . '/includes/fields.php'
		);

		foreach( $field_file_paths as $file_path ) {

			if( $file_path && file_exists( $file_path ) ) {

	            require_once( GWPerks::get_base_path() . '/model/field.php' );
	            require_once( $file_path );

	            $field_class = $class_name ? $class_name : $this->slug . 'Field';
                if( ! class_exists( $field_class ) )
                    return false;

                $args = array( 'perk' => $this );

                if( is_callable( array( $field_class, 'get_instance' ) ) ) {
                    $field_obj = call_user_func( array( $field_class, 'get_instance' ), $args );
                } else {
                    $field_obj = new $field_class( $args );
                }

                return $field_obj;
			}

		}

		return false;
    }



    // UI HELPERS //

    public static function generate_checkbox(&$perk, $args) {

        extract(wp_parse_args($args, array(
            'id' => '',
            'class' => '',
            'label' => '',
            'value' => 1,
            'description' => false,
            'data' => GWPerk::get_perk_settings($perk->get_id()),
            'onclick' => '',
            'toggle_section' => false
        )));

        $id = $perk->get_id() . "_$id";

        if($label)
            $label = "<label for=\"$id\">$label</label>";

        if($description)
            $description = "<p class=\"description\">$description</p>";

        if(is_array($data)) {
            $is_checked = isset($data[$id]) && $data[$id] ? 'checked="checked"' : '';
        } else {
            $is_checked = $data ? 'checked="checked"' : '';
        }

        if($toggle_section && !$onclick) {
            $onclick = 'onclick="gperk.toggleSection(this, \'' . $toggle_section . '\');"';
            $class .= ' gwp-expandable';
            $class .= $is_checked ? ' open' : '';
        }

        return "<div class=\"gwp-field gwp-checkbox $class\"><input type=\"checkbox\" id=\"$id\" name=\"$id\" value=\"$value\" $is_checked $onclick /> <div class=\"label\">$label $description</div></div>";
    }

    public static function generate_textarea($perk, $args) {

        extract(wp_parse_args($args, array(
            'id' => '',
            'class' => '',
            'label' => '',
            'value' => 1,
            'description' => false,
            'data' => GWPerk::get_perk_settings($perk->get_id())
        )));

        $id = $perk->get_id() . "_$id";

        if($label)
            $label = "<label for=\"$id\">$label</label>";

        if($description)
            $description = "<p class=\"description\">$description</p>";

        $value = gwar($data, $id);

        return "
            <div class=\"gwp-field gwp-textarea\">
                <div class=\"label\">
                    $label
                    $description
                </div>
                <textarea id=\"$id\" name=\"$id\">$value</textarea>
            </div>";
    }

    public static function generate_select($perk, $args) {

        extract(wp_parse_args($args, array(
            'id' => '',
            'class' => '',
            'label' => '',
            'values' => array(),
            'description' => false,
            'data' => GWPerk::get_perk_settings($perk->get_id())
        )));

        $id = $perk->get_id() . "_$id";

        if($label)
            $label = "<label for=\"$id\">$label</label>";

        if($description)
            $description = "<p class=\"description\">$description</p>";

        $value = gwar($data, $id);
        $options = self::generate_options($perk, $values, $value);

        return "
            <div class=\"gwp-field gwp-select\">
                <div class=\"label\">
                    $label
                    $description
                </div>
                <select id=\"$id\" name=\"$id\">
                    $options
                </select>
            </div>";
    }

    public static function generate_options($perk, $values, $selected_value) {

        $options  = array();
        $is_assoc = self::is_associative_array( $values );

        foreach( $values as $value => $text ) {
        	// allow non-associative arrays to be passed, use $value as as $text and $value
            if( ! $is_assoc ) {
                $value = $text;
            }
            $is_selected = $selected_value == $value ? 'selected="selected"' : '';
            $options[] = "<option value=\"$value\" $is_selected>$text</option>";
        }

        return implode("\n", $options);
    }

    public static function generate_input($perk, $args) {

        extract(wp_parse_args($args, array(
            'id' => '',
            'class' => '',
            'label' => '',
            'description' => false,
            'data' => GWPerk::get_perk_settings($perk->get_id()),
            'type' => 'text'
        )));

        $id = $perk->get_id() . "_$id";
        $value = gwar($data, $id);

        if($label)
            $label = "<label for=\"$id\">$label</label>";

        if($description)
            $description = "<p class=\"description\">$description</p>";

        return "
            <div class=\"gwp-field gwp-input gwp-input-$type $class\">
                <div class=\"label\">
                    $label
                    $description
                </div>
                <input type=\"$type\" id=\"$id\" name=\"$id\" value=\"$value\" />
            </div>";
    }

    public static function is_associative_array( $array ) {
	    return array_keys( $array ) !== range( 0, count( $array ) - 1 );
    }




    // SORT... //

    public static function get_default_perk_options($slug) {
        return array('slug' => $slug, 'is_active' => false, 'filename' => '');
    }

    public static function remove_directory($dir) {

        if(is_dir($dir)) {

            $objects = scandir($dir);

            foreach ($objects as $object) {
                if($object != "." && $object != "..") {
                    if(filetype($dir . '/' . $object) == "dir") {
                        self::remove_directory($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);

            return true;
        }

        return false;
    }

    public static function create_lead_object($form){

        $lead = array();
        $lead['id'] = -1;
        $lead['form_id'] = $form['id'];

        foreach($form["fields"] as $field){

            //Ignore fields that are marked as display only
            if(gwget('displayOnly', $field) && $field['type'] != 'password'){
                continue;
            }

            //only save fields that are not hidden (except on entry screen)
            if( !RGFormsModel::is_field_hidden($form, $field, array()) ){

                if(isset($field['inputs']) && is_array($field['inputs'])){
                    foreach($field['inputs'] as $input)
                        $lead[(string)$input['id']] = self::get_input_value($form, $field, $lead, $input['id']);
                }
                else {
                    $lead[$field['id']] = self::get_input_value($form, $field, $lead, $field['id']);
                }

            }

        }

        return $lead;
    }

    public static function get_input_value($form, $field, &$lead, $input_id){

        $input_name = 'input_' . str_replace('.', '_', $input_id);
        $value = gwpost( $input_name );

        if( empty( $value ) && $field->adminOnly && ! IS_ADMIN ) {
            $value = GFFormsModel::get_default_value($field, $input_id);
        }

        //processing values so that they are in the correct format for each input type
        $value = RGFormsModel::prepare_value($form, $field, $value, $input_name, gwar($lead, "id"));

        if(!empty($value) || $value === "0") {

            $value = apply_filters("gform_save_field_value", $value, $lead, $field, $form, $input_id );

        }

        return $value;
    }

    public static function is_form_valid($form) {

        foreach($form['fields'] as $field) {
            if($field['failed_validation'] == true) {
                return false;
            }
        }

        return true;
    }

    public static function has_merge_tag($merge_tag, $text) {
        preg_match('{' . $merge_tag . '([:]+)?([\w\s!?,\'"]+)?}', $text, $matches);
        return !empty($matches);
    }

    /**
    * Get perk setting, will automatically append the perk slug.
    *
    * @param mixed $key
    * @param mixed $settings
    */
    function get_setting($key, $settings = array()) {
        if(empty($settings))
            $settings = GWPerk::get_perk_settings($this->get_id());

        return gwar($settings, $this->get_id() . "_$key");
    }

    public static function echo_if($text, $option, $value = true) {
        if($option == $value)
            echo $text;
    }

    protected function register_script( $handle, $src, $deps, $ver, $in_footer ) {
        wp_register_script( $handle, $src, $deps, $ver, $in_footer );
        self::register_noconflict_script( $handle );
    }

    public static function register_noconflict_script( $script_name ) {
        add_filter( 'gform_noconflict_scripts', create_function( '$scripts', '$scripts[] = "' . $script_name . '"; return $scripts;' ) );
    }

    public static function register_noconflict_styles( $style_name ) {
        add_filter( 'gform_noconflict_styles', create_function( '$styles', '$styles[] = "' . $style_name . '"; return $styles;' ) );
    }

    public static function register_preview_style( $style_name ) {
        add_filter( 'gform_preview_styles', create_function( '$styles', '$styles[] = "' . $style_name . '"; return $styles;' ) );
    }

}

class GWPerk extends GP_Perk { }