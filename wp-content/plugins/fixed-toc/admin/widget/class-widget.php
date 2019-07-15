<?php
/**
 * Register Fixedtoc_widget.
 *
 * @since 3.0.0
 */
class Fixedtoc_Widget extends WP_Widget {
	/**
	 * widget sections slug.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $fixedtoc_widget_sections_slug = 'fixedtoc_widget_sections';	
	
	/**
	 * Widget value.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var array
	 */	
	private $fixedtoc_instance = array();
	
	/**
	 * Index of the widget.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var int
	 */
	private static $index = 0;
	
	/**
	 * Register widget with WordPress.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
		$classname = 'ftwp-widget';
		
		parent::__construct(
			'fixedtoc', // Base ID
			esc_html__( 'Fixed TOC', 'fixedtoc' ), // Name
			array(
				'classname' => $classname,
				'description' => esc_html__( 'Display a Fixed TOC to the current page content.', 'fixedtoc' ), 
			) // Args
		);
		
		// Register sections
		add_action( 'admin_init', array( $this, 'register_sections' ) );
		
		// Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @since 3.0.0.
	 * @access public
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		if ( 1 <= self::$index ) {
			return;
		}
		
		if ( ! fixedtoc_is_true( 'toc_page' ) || ! fixedtoc_is_true( 'in_widget' ) ) {
			return;
		}
		
		$GLOBALS['FIXEDTOC_WIDGET_VALS'] = $instance;
		
		$contents = apply_filters( 'fixedtoc_widget_content', '' );
		if ( empty( $contents ) ) {
			return;
		}
		
		do_action( 'fixedtoc_before_widget' );
		
		echo $args['before_widget'];
		echo '<div id="ftwp-widget-container">' . $contents . '</div>';
		echo $args['after_widget'];
		
		do_action( 'fixedtoc_after_widget' );
		
		self::$index++;
	}

	/**
	 * Back-end widget form.
	 *
	 * @since 3.0.0.
	 * @access public
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		printf( '<p>%s</p>', __( 'Make sure you have added only one the Fixed TOC widget at the same page.', 'fixedtoc' ) );
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'fields/class-fields-factory.php';
//		do_settings_sections($this->fixedtoc_widget_sections_slug);
		$this->do_settings_sections( $instance );
	}
	
	/**
	 * Prints out all settings sections.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	private function do_settings_sections( $instance ) {
		global $wp_settings_sections, $wp_settings_fields;
		$page = $this->fixedtoc_widget_sections_slug;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;
		
		echo '<div class="ftoc-widget-form">';

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['title'] )
				echo "<h3 class=\"ftoc-section-title\">{$section['title']}</h3>\n";

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
				continue;
			}		
			
			$this->do_settings_fields( $page, $section['id'], $instance );
		}
		
		echo '</div>';
	}
	
	/**
	 * Print out the settings fields for a particular settings section.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return string $page. Slug title of the admin page who's settings fields you want to show.
	 * @return string $section. Slug title of the settings section who's fields you want to show.
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	private function do_settings_fields( $page, $section, $instance ) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			$data_name = isset( $field['args']['data_name'] ) ? $field['args']['data_name'] : '';
			$name = $this->get_field_name( $data_name );
			$id = $this->get_field_id( $data_name );
			$field['args']['name'] = $this->get_field_name( $data_name );
			$field['args']['input_attrs']['id'] = $id;
			$field['args']['label_for'] = $id;
			$field['args']['value'] = isset( $instance[ $data_name ] ) ? $instance[ $data_name ] : fixedtoc_get_option( $data_name );
			
			echo '<div id="div-' . esc_attr( $id ) . '" class="ftoc-widget-field">';
			
			if ( empty( $field['args']['label'] ) ) {
				echo '';
			} elseif ( ! empty( $field['args']['label_for'] ) ) {
				echo '<label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label><br>';
			} else {
				echo '<span>' . $field['title'] . '</span><br>';
			}

			call_user_func( $field['callback'], $field['args'] );
			
			echo '</div>';
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 3.0.0.
	 * @access public
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		return Fixedtoc_Admin_Control::sanitize( $new_instance );
	}
	
	/**
	 * register sections.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_sections() {
		require_once 'class-widget-sections.php';
		new Fixedtoc_Widget_Sections( $this );
	}	
	
	/**
	 * Add section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $id
	 * @param string $title
	 * @param string $callback
	 * @return string
	 */
	public function add_section( $id, $title, $callback ) {
		add_settings_section( $id, $title, $callback, $this->fixedtoc_widget_sections_slug );
		return $id;
	}	
	
	/**
	 * Add section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $section_id
	 * @param string $field_name
	 * @return void
	 */
	public function add_field( $section_id, $field_name ) {
		$name = fixedtoc_get_field_data( $field_name, 'name' );
		$title = fixedtoc_get_field_data( $field_name, 'label' );
		
		$args = fixedtoc_get_field_data( $field_name );
		$args['input_attrs'] = isset( $args['input_attrs'] ) ? $args['input_attrs'] : array();
		$args['input_attrs'] = isset( $args['widget_input_attrs'] ) ? $args['widget_input_attrs'] : $args['input_attrs'];
		$args['des'] = isset( $args['widget_des'] ) ? $args['widget_des'] : '';
		$args['data_name'] = $name;
		
		add_settings_field( $name, $title, array( $this, 'render_field' ), $this->fixedtoc_widget_sections_slug, $section_id, $args );
	}

	/**
	 * Render field.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $args
	 * @return string
	 */
	public function render_field( $args ) {
		if ( ! $args ) {
			return '';
		}

		$html 			= isset( $args['prefix'] ) && $args['prefix'] ? $args['prefix'] . ' ' : '';
		$field_obj 	= new Fixedtoc_Fields_Factory( $args );
		$html 			.= $field_obj->get_html();
		$html 			.= isset( $args['suffix'] ) && $args['suffix'] ? ' ' . $args['suffix'] . '<br>' : '<br>';
		$html 			.= isset( $args['des'] ) && $args['des'] ? '<small>' . $args['des'] . '</small>' : '';

		echo $html;		
	}
	
	/**
	 * Enqueue scripts.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'widgets.php' != $hook ) {
			return;
		}
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		
		wp_enqueue_style( 'fixedtoc_widget_style', plugins_url( 'widget-style.css', __FILE__ ) );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'fixedtoc_widget_script', plugins_url( 'widget-script.js', __FILE__ ), array( 'jquery' ), '', true );
	}
	
}