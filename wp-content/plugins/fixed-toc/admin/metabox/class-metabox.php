<?php
/**
 * Register Meta Box page.
 *
 * @since 3.0.0
 */
class Fixedtoc_Metabox {
	/**
	 * Meta name.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $meta_name = '_fixed_toc';
	
	/**
	 * Meta box slug.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $meta_slug = 'fixedtoc_metabox';
	
	/**
	 * Contructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */	
	public function __construct() {
    add_action( 'load-post.php',     array( $this, 'hooks' ) );
    add_action( 'load-post-new.php', array( $this, 'hooks' ) );
	}
	
	/**
	 * Hook into the appropriate actions.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', 									array( $this, 'add_meta_box' ) );
		add_action( 'save_post',      									array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts',  					array( $this, 'enqueue_scripts' ) );
	}

 /**
	 * Adds the meta box container.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	public function add_meta_box( $post_type ) {
		// Limit meta box to certain post types.
		$post_types = (array) fixedtoc_get_option( 'general_post_types' );
		
		if ( ! in_array( $post_type, $post_types ) ) {
			return;
		}
		
		// Add meta box
		add_meta_box(
				'fixedtoc-metabox',
				__( 'Fixed TOC', 'fixedtoc' ),
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'normal',
				'high'
		);
		
		// Register sections
		$this->register_sections();
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @return int|void
	 */
	public function save( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['fixedtoc_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['fixedtoc_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'fixedtoc_action' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		// Sanitize the user input.
		$vals = isset( $_POST[ $this->meta_name ] ) ? $_POST[ $this->meta_name ] : array();
		$vals = Fixedtoc_Admin_Control::sanitize( $vals );

		// Update the meta field.
		update_post_meta( $post_id, $this->meta_name, $vals );
	}
	
	/**
	 * Register settings.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @void
	 */
	private function register_sections() {
		// Add sections
		require_once 'class-metabox-sections.php';
		new Fixedtoc_Metabox_Sections( $this );
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
		add_settings_section( $id, $title, $callback, $this->meta_slug );
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
		$id = sanitize_key( 'fixedtoc_meta_' . $name );
		$title = fixedtoc_get_field_data( $field_name, 'label' );
		
		$args = fixedtoc_get_field_data( $field_name );
		$args['name'] = $this->meta_name . '[' . $name . ']';
		$args['value'] = fixedtoc_get_meta( $name );
		$args['input_attrs'] = isset( $args['input_attrs'] ) ? $args['input_attrs'] : array();
		$args['input_attrs'] = isset( $args['meta_input_attrs'] ) ? $args['meta_input_attrs'] : $args['input_attrs'];
		$args['input_attrs']['id'] = $id;
		$args['input_attrs']['disabled'] = is_null( fixedtoc_get_meta( $name, false, true ) ) ? true : false;
		$args['label_for'] = $id;
		$args['des'] = isset( $args['meta_des'] ) ? $args['meta_des'] : '';
		
		add_settings_field( $id, $title, array( $this, 'render_field' ), $this->meta_slug, $section_id, $args );
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
		$html 			.= isset( $args['suffix'] ) && $args['suffix'] ? ' ' . $args['suffix'] : '';
		$html 			.= isset( $args['meta_des'] ) && $args['meta_des'] ? '<p class="description">' . $args['meta_des'] . '</p>' : '';

		echo $html;		
	}

	/**
	 * Render Meta Box content.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'fixedtoc_action', 'fixedtoc_nonce' );

		// Display the form, using the current value.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'fields/class-fields-factory.php';
		$this->render_onoff_field();
?>
		<ol id="fixedtoc-document">
			<li>
				<p class="description"><?php _e( 'Click the pen icon from gray to blue, then you can set the special option for this page.', 'fixedtoc' );?></p>
			</li>
			<li>
				<p class="description"><?php _e( 'Click the pen icon from blue to gray, it will cancel the special option and restore the default global option.', 'fixedtoc' );?></p>
			</li>
		</ol>
<?php
		echo '<div id="fixedtoc-metabox-inner">';
		$this->do_settings_sections();
		echo '</div>';
	}

	/**
	 * Render on/off field.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void.
	 */
	private function render_onoff_field() {
		$name = fixedtoc_get_field_data( 'general_enable', 'name' );
		$attr_name = $this->meta_name . '[' . $name . ']';
		$value = (bool) fixedtoc_get_meta( $name );
		$checked = checked( $value, true, false );
		
		echo '<h2 id="ftoc-onoff-title">' . __( 'Disable/Enable', 'fixedtoc' ) . '</h2>';
		echo '<input type="hidden" name="' . esc_attr( $attr_name ) . '" value="0">';
		echo '<div id="ftoc-onoff" class="ftoc-switch">';
		echo '<input type="checkbox" id="ftoc-onoff-toggle" name="' . esc_attr( $attr_name ) . '" value="1"' . $checked . '>';
		echo '<label for="ftoc-onoff-toggle"></label>';
		echo '</div>';
	}
	
	/**
	 * Prints out all settings sections.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function do_settings_sections() {
		global $wp_settings_sections, $wp_settings_fields;
		$page = $this->meta_slug;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['title'] )
				echo "<h2 class=\"ftoc-section-title\">{$section['title']}</h2>\n";

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
				continue;
			}		
			
			echo '<table class="form-table">';
			$this->do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}
	
	/**
	 * Print out the settings fields for a particular settings section.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return string $page. Slug title of the admin page who's settings fields you want to show.
	 * @return string $section. Slug title of the settings section who's fields you want to show.
	 * @return void
	 */
	private function do_settings_fields($page, $section) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			$class = isset( $field['args']['input_attrs']['disabled'] ) && $field['args']['input_attrs']['disabled'] ? ' class="ftoc-disabled"' : '';
			echo '<tr id="tr_' . esc_attr( $field['args']['label_for'] ) . '"' . $class . '>';
			
			$button = '<button type="button" class="ftoc-field-control"><span class="dashicons dashicons-edit"></span></button>';

			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<th scope="row">' . $button . '<label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
			} else {
				echo '<th scope="row">' . $button . $field['title'] . '</th>';
			}

			echo '<td>';
			call_user_func($field['callback'], $field['args']);
			echo '</td>';
			echo '</tr>';
		}
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
		$post_types = fixedtoc_get_option( 'general_post_types' );
		if ( ! $post_types ) {
			return;
		}
		
		$post_types = (array) $post_types;
		if ( in_array( get_post_type(), $post_types ) ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			
			wp_enqueue_style( 'fixedtoc_metabox_style', plugins_url( 'metabox-style.css', __FILE__ ) );
			wp_enqueue_script( 'fixedtoc_metabox_script', plugins_url( 'metabox-script.js', __FILE__ ), array( 'jquery-ui-accordion' ), '', true );
		}
	}
	
}