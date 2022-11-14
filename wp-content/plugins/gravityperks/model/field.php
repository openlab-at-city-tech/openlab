<?php

class GWField {

	/**
	* Parse the default arguments and initialize the required Gravity Forms hooks.
	*
	* @param mixed $args
	* @return GWField
	*/
	function __construct( $args ) {

		/**
		 * @var $perk
		 * @var $type
		 * @var $name
		 * @var $button
		 * @var $field_settings
		 * @var $field_class
		 * @var $default_field_values
		 */
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( wp_parse_args( $args, array(

			// array of properties used to define the Field button which display in the form editor
			'editor_button'        => array(),

			// if you specify the CSS field setting classes which should display for this field, the GWField class will
			// handle outputting that bit of js
			'editor_settings'      => false,

			// if not specified, the field class will default to "gform_" + field type
			'field_class'          => false,

			// default field values
			'default_field_values' => array(),

		) ) );

		$this->perk = $perk;

		$this->type           = $type;
		$this->name           = $name;
		$this->button         = $this->button( $button );
		$this->field_settings = $this->field_settings( $field_settings );
		$this->field_class    = $field_class ? 'gform_' . $this->type . ' ' . $field_class : 'gform_' . $this->type;

		if ( ! gwar( $default_field_values, 'label' ) ) {
			$default_field_values['label'] = $this->name;
		}

		$this->default_field_values = $default_field_values;

		add_filter( 'gform_add_field_buttons', array( $this, 'add_button' ) );
		add_filter( 'gform_field_type_title', array( $this, 'type_label' ) );

		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );
		add_action( 'gform_editor_js', array( $this, 'field_default_values_js' ) );
		add_action( 'gform_editor_js', array( $this, 'editor_js' ) );
		add_filter( 'gform_field_css_class', array( $this, 'field_class' ), 10, 3 );

		add_filter( 'gform_field_input', array( $this, 'filter_input_html' ), 10, 5 );
		add_filter( 'gform_field_content', array( $this, 'filter_field_html' ), 10, 5 );

		add_filter( 'gform_entry_field_value', array( $this, 'get_this_field_value_entry_detail' ), 10, 4 );
		add_filter( 'gform_entries_field_value', array( $this, 'get_this_field_value_entry_list' ), 10, 4 );

		add_action( 'gform_enqueue_scripts', array( $this, 'filter_enqueue_field_scripts' ), 10, 2 );

		add_action( 'gform_field_standard_settings', array( $this, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_appearance_settings', array( $this, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_advanced_settings', array( $this, 'dynamic_setting_actions' ), 10, 2 );

	}

	public function get_this_field_value_entry_detail( $value, $field, $entry, $form ) {

		if ( $this->is_this_field_type( $field ) ) {
			$value = $this->get_value_entry_detail( $value, $field, $entry, $form );
		}

		return $value;
	}

	public function get_value_entry_detail( $value, $field, $entry, $form ) {
		return $value;
	}

	public function get_this_field_value_entry_list( $value, $form_id, $field_id, $entry ) {

		$form  = GFAPI::get_form( $form_id );
		$field = GFFormsmodel::get_field( $form, $field_id );

		if ( $this->is_this_field_type( $field ) ) {
			$value = $this->get_value_entry_list( $value, $form_id, $field_id, $entry );
		}

		return $value;
	}

	public function get_value_entry_list( $value, $form_id, $field_id, $entry ) {
		return $value;
	}




	function filter_enqueue_field_scripts( $form, $ajax ) {

		if ( ! is_array( $form['fields'] ) ) {
			return;
		}

		$has_this_field_type = false;

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_this_field_type( $field ) ) {
				$has_this_field_type = true;
				if ( method_exists( $this, 'enqueue_field_scripts' ) ) {
					$this->enqueue_field_scripts( $form, $ajax, $field );
				}
			}
		}

		if ( ! $has_this_field_type ) {
			return;
		}

		// init script should include all fields of the given type for a form
		if ( method_exists( $this, 'add_init_script' ) ) {
			$this->add_init_script( $form, $ajax );
		}

	}

	function filter_input_html( $input, $field, $value, $lead_id, $form_id ) {

		if ( ! $this->is_this_field_type( $field ) ) {
			return $input;
		}

		// form editor
		if ( $this->is_form_editor() ) {
			$input = $this->input_html_form_editor( $field, $value, $lead_id, $form_id );
		} elseif ( $this->is_entry_detail_edit() ) {
			// entry edit
			$input = $this->input_html_entry_detail_edit( $field, $value, $lead_id, $form_id );
		} elseif ( $this->is_entry_detail() ) {
			// entry detail
			$input = $this->input_html_entry_detail( $field, $value, $lead_id, $form_id );
		} else {
			// frontend
			$input = $this->input_html_frontend( $field, $value, $lead_id, $form_id );
		}

		return $input;
	}

	function input_html( $field, $value, $lead_id, $form_id ) {
		die( 'Method GWField::input_html() must be over-ridden in a sub-class.' );
	}

	function input_html_form_editor( $field, $value, $lead_id, $form_id ) {
		return $this->input_html( $field, $value, $lead_id, $form_id );
	}

	function input_html_entry_detail( $field, $value, $lead_id, $form_id ) {
		return $this->input_html( $field, $value, $lead_id, $form_id );
	}

	function input_html_entry_detail_edit( $field, $value, $lead_id, $form_id ) {
		return $this->input_html( $field, $value, $lead_id, $form_id );
	}

	function input_html_frontend( $field, $value, $lead_id, $form_id ) {
		return $this->input_html( $field, $value, $lead_id, $form_id );
	}

	function filter_field_html( $content, $field, $value, $entry_id, $form_id ) {

		if ( ! $this->is_this_field_type( $field ) ) {
			return $content;
		}

		return $this->field_html( $content, $field, $value, $entry_id, $form_id );
	}

	function field_html( $content, $field, $value, $entry_id, $form_id ) {
		return $content;
	}

	function filter_field_value() {

	}

	function field_value() {

	}



	function editor_js() { }



	function add_button( $field_groups ) {

		foreach ( $field_groups as &$field_group ) {

			if ( $field_group['name'] == $this->button['group'] ) {
				array_push( $field_group['fields'], $this->button );
				break;
			}
		}

		return $field_groups;
	}

	function type_label( $type ) {

		if ( $type == $this->type ) {
			return $this->name;
		}

		return $type;
	}

	function field_settings_js() {

		$field_settings = implode( ', ', array_map( function( $class ) {
			return ".{$class}";
		}, $this->field_settings ) );

		if ( $this->field_settings ) { ?>

			<script type="text/javascript">
				fieldSettings['<?php echo $this->type; ?>'] = '<?php echo $field_settings; ?>';
			</script>

			<?php
		}

	}

	function field_default_values_js() {
		?>

		<script type="text/javascript">

			function SetDefaultValues_<?php echo $this->type; ?>(field) {
				var defaultFieldValues = <?php echo json_encode( $this->default_field_values ); ?>;
				for( var key in defaultFieldValues ) {
					if( defaultFieldValues.hasOwnProperty( key ) )
						field[key] = defaultFieldValues[key];
				}
				return field;
			}

		</script>

		<?php
	}

	function field_class( $classes, $field, $form ) {

		if ( $this->is_this_field_type( $field ) ) {
			$classes .= " {$this->field_class}";
		}

		return $classes;
	}



	/**
	* Sets default paramters for button.
	*
	*   group: Default is 'standard_fields'; other accepted values are 'advanced_fields', 'post_fields', 'pricing_fields'
	*   class: Default is 'button'
	*   value: Default is the name property of the field
	*   onclick: Default is "StartAddField('{$this->type})"
	*
	*/
	function button( $button ) {

		$defaults = array(
			'group'   => 'standard_fields',
			'class'   => 'button',
			'value'   => $this->name,
			'onclick' => "StartAddField('{$this->type}')",
		);

		return wp_parse_args( $button, $defaults );
	}

	function field_settings( $field_settings ) {

		$settings = array();

		foreach ( $field_settings as $field_setting ) {
			switch ( $field_setting ) {
				case 'BASIC':
					$settings = array_merge($settings, array(
						'label_setting',
						'conditional_logic_field_setting',
						'css_class_setting',
						'prepopulate_field_setting',
						'default_value_setting',
						'description_setting',
						'visibility_setting',
						'error_message_setting',
						'admin_label_setting',
						'size_setting',
						'rules_setting',
						'duplicate_setting',
					));
					break;
				default:
					array_push( $settings, $field_setting );
			}
		}

		return $settings;
	}



	// # VIEWS

	function is_form_editor() {
		return ( IS_ADMIN && gwget( 'page' ) == 'gf_edit_forms' && ! gwget( 'view' ) ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && gwpost( 'action' ) == 'rg_add_field' );
	}

	function is_entry_detail() {
		return IS_ADMIN && gwget( 'page' ) == 'gf_entries' && gwget( 'view' ) == 'entry';
	}

	function is_entry_detail_edit() {
		return IS_ADMIN && gwget( 'page' ) == 'gf_entries' && gwget( 'view' ) == 'entry' && rgpost( 'screen_mode' );
	}



	/**
	* Check if the passed field is the same type as this GWField object.
	*
	* @param array $field
	*/
	function is_this_field_type( $field ) {
		return gwar( $field, 'type' ) == $this->type;
	}

	function has_this_field_type( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( $this->is_this_field_type( $field ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	* Adds position-based actions like "gform_advanced_settings_100" so you can hook directly to a position rather than checking
	* the $position inside your function before outputtting.
	*
	* @param int $position
	* @param int $form_id
	*/
	function dynamic_setting_actions( $position, $form_id ) {
		$action = current_filter() . '_' . $position;
		if ( did_action( $action ) < 1 ) {
			do_action( current_filter() . '_' . $position, $form_id );
			echo $position . '<br />';
		}
	}

	/**
	* Register a custom script so that it is "allowed" to be output on the Form Preview page when "No Conflict" mode
	* is activated.
	*
	* @param string $script_name
	*/



	/**
	* Check if WordPress is currently processing an AJAX requested. Optionally pass an AJAX action to check if that
	* specific action is being processed.
	*
	* @param string $action The AJAX action to check for.
	*/
	public static function doing_ajax( $action = false ) {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return false;
		}

		return $action ? $action == $_REQUEST['action'] : true;
	}

	protected final function method_is_overridden( $method_name, $base_class = 'GFField' ) {
		$reflector = new ReflectionMethod( $this, $method_name );
		$name      = $reflector->getDeclaringClass()->getName();
		return $name !== $base_class;
	}

}
