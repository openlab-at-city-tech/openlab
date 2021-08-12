<?php
// Don't load directly
defined( 'WPINC' ) or die;

use Tribe\Customizer\Controls\Heading;
use Tribe\Customizer\Controls\Radio;
use Tribe\Customizer\Controls\Separator;

/**
 * The Events Calendar Customizer Section Abstract.
 * Extend this when you are trying to create a new The Events Calendar Section
 * on the Customize from WordPress.
 *
 * @package Common
 * @subpackage Customizer
 * @since 4.0
 */
abstract class Tribe__Customizer__Section {

	/**
	 * ID of the section.
	 *
	 * @since 4.0
	 *
	 * @access public
	 * @var string
	 */
	public $ID;

	/**
	 * Load this section by default.
	 *
	 * @since 4.4
	 *
	 * @access public
	 * @var string
	 */
	public $load = true;

	/**
	 * Default values for the settings on this class.
	 *
	 * @since 4.0
	 *
	 * @access private
	 * @var array
	 */
	public $defaults = [];

	/**
	 * Information to setup the Section.
	 *
	 * @since 4.0
	 *
	 * @access public
	 * @var array
	 */
	public $arguments = [
		'priority'	=> 10,
		'capability'  => 'edit_theme_options',
		'title'	   => null,
		'description' => null,
	];

	/**
	 * Allows sections to be loaded in order for overrides.
	 *
	 * @var integer
	 */
	public $queue_priority = 15;

	/**
	 * Private variable holding the class Instance.
	 *
	 * @since 4.0
	 *
	 * @access private
	 * @var Tribe__Events__Pro__Customizer__Section
	 */
	private static $instances;

	protected $content_headings = [];
	protected $content_settings = [];
	protected $content_controls = [];

	/**
	 * Setup and Load hooks for this Section.
	 *
	 * @since  4.0
	 *
	 * @return Tribe__Customizer__Section
	 */
	final public function __construct() {
		$slug = self::get_section_slug( get_class( $this ) );

		// If for weird reason we don't have the Section name
		if ( ! is_string( $this->ID ) ){
			$this->ID = $slug;
		}

		// Allow child classes to setup the section.
		$this->setup();

		// Hook the Register methods
		add_action( "tribe_customizer_register_{$this->ID}_settings", [ $this, 'register_settings' ], 10, 2 );
		add_filter( 'tribe_customizer_pre_sections', [ $this, 'register' ], 10, 2 );

		// Append this section CSS template
		add_filter( 'tribe_customizer_css_template', [ $this, 'get_css_template' ], $this->queue_priority );
		add_filter( "tribe_customizer_section_{$this->ID}_defaults", [ $this, 'get_defaults' ], 10 );

		// Create the Ghost Options
		add_filter( 'tribe_customizer_pre_get_option', [ $this, 'filter_settings' ], 10, 2 );

		// By Default Invoking a new Section will load, unless `load` is set to false
		if ( true === (bool) $this->load ) {
			tribe( 'customizer' )->load_section( $this );
		}
	}

	/**
	 * This method will be executed when the Class is Initialized.
	 * Overwrite this method to be able to setup the arguments of your section.
	 *
	 * @return void
	 */
	public function setup() {
		$this->setup_defaults();
		$this->setup_arguments();
		$this->setup_content_arguments();
	}

	/**
	 * Register this Section.
	 *
	 * @param  array  $sections   Array of Sections.
	 * @param  Tribe__Customizer $customizer Our internal Cutomizer Class Instance.
	 *
	 * @return array  Return the modified version of the Section array.
	 */
	public function register( $sections, Tribe__Customizer $customizer ) {
		$sections[ $this->ID ] = $this->arguments;

		return $sections;
	}

	/**
	 * Overwrite this method to create the Fields/Settings for this section.
	 *
	 * @param  WP_Customize_Section $section The WordPress section instance.
	 * @param  WP_Customize_Manager $manager The WordPress Customizer Manager.
	 *
	 * @return void
	 */
	public function register_settings( WP_Customize_Section $section, WP_Customize_Manager $manager ) {
		$customizer = tribe( 'customizer' );

		$headings = $this->get_content_headings();

		if ( ! empty( $headings ) ) {
			foreach( $headings as $name => $args ) {
				$setting_name = $customizer->get_setting_name( $name, $section );
				$this->add_heading(  $section, $manager, $setting_name, $args );
			}
		}

		$settings = $this->get_content_settings();

		if ( ! empty( $settings ) ) {
			foreach( $settings as $name => $args ) {
				$setting_name = $customizer->get_setting_name( $name, $section );
				$this->add_setting( $manager, $setting_name, $name, $args );
			}
		}

		$controls = $this->get_content_controls();

		if ( ! empty( $controls ) ) {
			foreach( $controls as $name => $args ) {
				$setting_name = $customizer->get_setting_name( $name, $section );
				$this->add_control(  $section, $manager, $setting_name, $args );
			}
		}
	}

	/**
	 * Function that encapsulates the logic for if a setting should be added to the Customizer style template.
	 * Note: this depends on a default value being set -
	 *       if the setting value is empty OR the default value it's not displayed.
	 *
	 * @since 4.13.3
	 *
	 * @param string $setting The setting slug, like 'grid_lines_color'.
	 * @param int $section_id The ID for the section - defaults to the current one if not set.
	 *
	 * @return boolean If the setting should be added to the style template.
	 */
	public function should_include_setting_css( $setting, $section_id = null ) {
		if ( empty( $setting ) ) {
			return false;
		}

		if ( empty( $section_id ) ) {
			$section_id = $this->ID;
		}

		$setting_value = tribe( 'customizer' )->get_option( [ $section_id, $setting ] );
		$section       = tribe( 'customizer' )->get_section( $section_id );

		return ! empty( $setting_value ) && $section->get_default(  $setting ) !== $setting_value;
	}

	/**
	 * Function to simplify getting an option value.
	 *
	 * @since 4.13.3
	 *
	 * @param string $setting The setting slug, like 'grid_lines_color'.
	 *
	 * @return string The setting value;
	 */
	public function get_option( $setting ) {
		if ( empty( $setting ) ) {
			return '';
		}

		return tribe( 'customizer' )->get_option( [ $this->ID, $setting ] );
	}

	public function to_rgb( $color ) {
		$color_object  = new \Tribe__Utils__Color( $color );
		$color_rgb_arr = $color_object::hexToRgb( $color );
		$color_rgb     = $color_rgb_arr['R'] . ',' . $color_rgb_arr['G'] . ',' . $color_rgb_arr['B'];

		return $color_rgb;
	}

	/**
	 * Overwrite this method to be able to implement the CSS template related to this section.
	 *
	 * @return string The CSS template.
	 */
	public function get_css_template( $template ) {
		return $template;
	}

	/**
	 * Overwrite this method to be able to create dynamic settings.
	 *
	 * @param  array  $settings The actual options on the database.
	 *
	 * @return array $settings The modified settings.
	 */
	public function create_ghost_settings( $settings = [] ) {
		return $settings;
	}

	/**
	 * Get the section slug based on the Class name.
	 *
	 * @param  string $class_name The name of this Class.
	 * @return string $slug The slug for this Class.
	 */
	final public static function get_section_slug( $class_name ) {
		$abstract_name = __CLASS__;
		$reflection = new ReflectionClass( $class_name );

		// Get the Slug without the Base name.
		$slug = str_replace( $abstract_name . '_', '', $reflection->getName() );

		if ( false !== strpos( $slug, '__Customizer__' ) ) {
			$slug = explode( '__Customizer__', $slug );
			$slug = end( $slug );
		}

		return strtolower( $slug );
	}

	/**
	 * Set up default values.
	 *
	 * @since 4.13.3
	 */
	public function setup_defaults() {}

	/**
	 * Get the (filtered) default settings.
	 *
	 * @return array The filtered defaults.
	 */
	public function get_defaults( $settings = [] ) {
		// Create Ghost Options
		$settings = $this->create_ghost_settings( wp_parse_args( $settings, $this->defaults ) );

		/**
		 * Allows filtering the default values for all sections.
		 *
		 * @since 4.13.3
		 *
		 * @param array                      $settings The default settings
		 * @param Tribe__Customizer__Section $section The section object.
		 */
		$settings = apply_filters( 'tribe_customizer_default_settings', $settings, $this );

		/**
		 * Allows filtering the default values for a specific section.
		 *
		 * @since 4.13.3
		 *
		 * @param array                      $settings The default settings
		 * @param Tribe__Customizer__Section $section The section object.
		 */
		return apply_filters( "tribe_customizer_{$this->ID}_default_settings", $settings, $this );
	}

	/**
	 * Get a single Default Value by key.
	 *
	 * @param string $key The key for the requested value.
	 *
	 * @return mixed The requested value.
	 */
	public function get_default( $key ) {
		$defaults = $this->get_defaults();

		if ( ! isset( $defaults[ $key ] ) ) {
			return null;
		}

		return $defaults[ $key ];
	}

	/**
	 * Hooks to the `tribe_customizer_pre_get_option`. This applies the `$this->create_ghost_settings()` method
	 * to the settings on the correct section.
	 *
	 * @param  array $settings  Values from the Database from Customizer actions.
	 * @param  array $search	Indexed search @see Tribe__Customizer::search_var().
	 *
	 * @return array
	 */
	public function filter_settings( $settings, $search ) {
		// Exit early.
		if ( null === $search ) {
			return $settings;
		}

		// Only Apply if getting the full options or Section.
		if ( is_array( $search ) && count( $search ) > 1 ) {
			return $settings;
		}

		if ( is_array( $search ) && count( $search ) === 1 ) {
			$settings = $this->create_ghost_settings( $settings );
		} else {
			$settings[ $this->ID ] = $this->create_ghost_settings( $settings[ $this->ID ] );
		}

		return $settings;
	}

	/**
	 * Set up section arguments.
	 *
	 * @since 4.13.3
	 *
	 * @return void
	 */
	public function setup_arguments() {}

	/**
	 * Sets up the Customizer section content.
	 *
	 * @since 4.13.3
	 */
	public function setup_content_arguments(){
		$this->setup_content_headings();
		$this->setup_content_settings();
		$this->setup_content_controls();
	}

	/* Headings */

	/**
	 * Sets up the Customizer section Header and Separator arguments.
	 *
	 * @since 4.13.3
	 */
	public function setup_content_headings() {}

	/**
	 * Get the (filtered) content headings and separator arguments.
	 * @see filter_content_headings()
	 *
	 * @since 4.13.3
	 *
	 * @return array<string,mixed> The filtered arguments.
	 */
	public function get_content_headings() {
		return $this->filter_content_headings( $this->content_headings );
	}

	/**
	 * Filter the content headings arguments
	 *
	 * @since 4.13.3
	 *
	 * @param array<string,mixed> $arguments The list of arguments for headings and separators.
	 *
	 * @return array<string,mixed> $arguments The filtered array of arguments.
	 */
	public function filter_content_headings( $arguments ) {
		/**
		 * Applies a filter to the validation map for instance arguments.
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( 'tribe_customizer_section_content_headings', $arguments, $this );

		$section_slug = static::get_section_slug( get_class( $this ) );

		/**
		 * Applies a filter to the validation map for instance arguments for a specific widget. Based on the widget slug of the widget
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( "tribe_customizer_section_{$section_slug}_content_headings", $arguments, $this );

		return $arguments;
	}

	/**
	 * Sugar syntax to add heading and separator sections to the customizer content.
	 * These are controls only in name: they do not actually control or save any setting.
	 *
	 * @since 4.13.3
	 *
	 * @param WP_Customize_Manager $manager   The instance of the Customizer Manager.
	 * @param string			   $name	  HTML name Attribute name of the setting.
	 * @param array<string,mixed>  $arguments The control arguments.
	 *
	 */
	protected function add_heading( $section, $manager, $name, $args ) {
		$this->add_control( $section, $manager, $name, $args );
	}

	/* Settings */

	/**
	 * Sets up the Customizer settings arguments.
	 *
	 * @since 4.13.3
	 */
	public function setup_content_settings() {}

	/**
	 * Get the (filtered) content setting arguments.
	 * @see filter_content_settings()
	 *
	 * @since 4.13.3
	 *
	 * @return array<string,mixed> The filtered arguments.
	 */
	public function get_content_settings() {
		return $this->filter_content_settings( $this->content_settings );
	}

	/**
	 * Filter the content settings arguments
	 *
	 * @since 4.13.3
	 *
	 * @param array<string,mixed> $arguments The list of arguments for settings.
	 *
	 * @return array<string,mixed> $arguments The filtered array of arguments.
	 */
	public function filter_content_settings( $arguments ) {
		/**
		 * Applies a filter to the validation map for instance arguments.
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( 'tribe_customizer_section_content_settings', $arguments, $this );

		$section_slug = static::get_section_slug( get_class( $this ) );

		/**
		 * Applies a filter to the validation map for instance arguments for a specific widget. Based on the widget slug of the widget
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( "tribe_customizer_section_{$section_slug}_content_settings", $arguments, $this );

		return $arguments;
	}

	/**
	 * Sugar syntax to add a setting to the customizer content.
	 *
	 * @since 4.13.3
	 *
	 * @param WP_Customize_Manager $manager	  The instance of the Customizer Manager.
	 * @param string			   $setting_name HTML name Attribute name of the setting.
	 * @param string			   $key		  The key for the default value.
	 * @param array<string,mixed>  $arguments	The control arguments.
	 */
	protected function add_setting( $manager, $setting_name, $key, $args ) {
		// Get the default values.
		$defaults = [
			'default' => $this->get_default( $key ),
			'type'	=> 'option',
		];

		// Add a setting.
		$manager->add_setting(
			$setting_name,
			array_merge( $defaults, $args )
		);
	}

	/* Controls */

	/**
	 * Sets up the Customizer controls arguments.
	 *
	 * @since 4.13.3
	 */
	public function setup_content_controls() {}

	/**
	 * Get a list (array) of accepted control types.
	 * In the format slug => control class name.
	 *
	 * @since 4.13.3
	 *
	 * @return array<string,string> The array of control types and their associated classes.
	 */
	public function get_accepted_control_types() {
		$accepted_control_types = [
			'checkbox'	     => WP_Customize_Control::class,
			'color'		     => WP_Customize_Color_Control::class,
			'default'		 => WP_Customize_Control::class,
			'dropdown-pages' => WP_Customize_Control::class,
			'heading'		 => Heading::class,
			'image'		     => WP_Customize_Image_Control::class,
			'radio'		     => Radio::class,
			'select'		 => WP_Customize_Control::class,
			'separator'	     => Separator::class,
			'textarea'	     => WP_Customize_Control::class,
		];

		/**
		 * Allows filtering the accepted control types.
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,string> $control_types The map of keys to WP Control classes.
		 */
		return apply_filters( 'tribe_customizer_accepted_control_types', $accepted_control_types, $this );
	}

	/**
	 * Determine if a control type is in our list of accepted ones.
	 *
	 * @since 4.13.3
	 *
	 * @param string $type The "slug" of the control type.
	 *
	 * @return boolean If a control type is in our list of accepted ones.
	 */
	public function is_control_type_accepted( $type ) {
		$types = $this->get_accepted_control_types();

		if ( empty( $type ) ) {
			return false;
		}

		if ( empty( $types[ $type ] ) ) {
			return false;
		}

		if ( ! class_exists( $types[ $type ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the class object associated with a control type.
	 *
	 * @since 4.13.3
	 *
	 * @param string $type The "slug" of the control type.
	 *
	 * @return object|false The control type class or false if type not found.
	 */
	public function get_control_type( $type ) {
		$types = $this->get_accepted_control_types();

		if ( empty( $type ) ) {
			return $types[ 'default' ];
		}

		if ( empty( $types[ $type ] ) ) {
			return false;
		}

		return $types[ $type ];
	}

	/**
	 * Get the (filtered) content control arguments.
	 * @see filter_content_controls()
	 *
	 * @since 4.13.3
	 *
	 * @return array<string,mixed> The filtered arguments.
	 */
	public function get_content_controls() {
		return $this->filter_content_controls( $this->content_controls );
	}

	/**
	 * Filter the content control arguments
	 *
	 * @since 4.13.3
	 *
	 * @param array<string,mixed> $arguments The list of arguments for controls.
	 *
	 * @return array<string,mixed> $arguments The filtered array of arguments.
	 */
	public function filter_content_controls( $arguments ) {
		/**
		 * Applies a filter to the validation map for instance arguments.
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( 'tribe_customizer_section_content_controls', $arguments, $this );

		$section_slug = static::get_section_slug( get_class( $this ) );

		/**
		 * Applies a filter to the validation map for instance arguments for a specific widget. Based on the widget slug of the widget
		 *
		 * @since 4.13.3
		 *
		 * @param array<string,callable> $arguments Current set of callbacks for arguments.
		 * @param static				 $instance  The widget instance we are dealing with.
		 */
		$arguments = apply_filters( "tribe_customizer_section_{$section_slug}_content_controls", $arguments, $this );

		return $arguments;
	}

	/**
	 * Sugar syntax to add a control to the customizer content.
	 *
	 * @since 4.13.3
	 *
	 * @param WP_Customize_Manager $manager	  The instance of the Customizer Manager.
	 * @param string			   $setting_name HTML name Attribute name of the setting.
	 * @param array<string,mixed>  $arguments	The control arguments.
	 */
	protected function add_control( $section, $manager, $setting_name, $args  ) {
		// Validate our control choice.
		if ( ! isset( $args['type'] ) ) {
			return;
		}

		$type = (string) $args['type'];

		if ( ! $this->is_control_type_accepted( $type ) ) {
			return;
		}

		$type = $this->get_control_type( $type );

		if ( $section instanceof WP_Customize_Section ) {
			$section = (string) $section->id;
		}

		if ( ! is_string( $section ) ) {
			return;
		}

		// Get the default values.
		$defaults = [
			'section' => $section,
		];

		$args = array_merge( $defaults, $args );

		$manager->add_control(
			new $type(
				$manager,
				$setting_name,
				$args
			)
		);
	}
}
