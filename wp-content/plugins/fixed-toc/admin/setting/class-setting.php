<?php
/**
 * Settings page.
 * Register setting page for the plugin under the settings admin page.
 *
 * @since 3.0.0
 */
class Fixedtoc_Setting {
	/**
	 * Page's hook_suffix.
	 *
	 * @since 3.0.0
	 * @access private
	 * 
	 * @var false|string
	 */
	private $hook_suffix = false;
	
	/**
	 * Options group name.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $option_group = 'fixedtoc_settings_group';
	
	/**
	 * Options name.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $option_name = 'fixed_toc';
	
	/**
	 * Menu slug.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $menu_slug = 'fixedtoc';
	
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu', 						array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', 						array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add a submenu page under the settings page.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @void
	 */
	public function add_settings_page() {
		$this->hook_suffix = add_options_page(
			__( 'Fixed TOC', 'fixedtoc' ), 
			__( 'Fixed TOC', 'fixedtoc' ), 
			'manage_options',
			$this->menu_slug, 
			array( $this, 'render_settings_content' ) 
		);
		
		// Print inline styles.
		add_action( 'admin_head-' . $this->hook_suffix, array( $this, 'print_inline_styles' ) );
	}

	/**
	 * Render settings page content.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @void
	 */
	public function render_settings_content() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'fields/class-fields-factory.php';
	?>
		<div class="wrap">
			<h2>
				<span id="ftoc-title-icon" class="dashicons dashicons-editor-ol"></span>
				<?php _e( 'Fixed TOC Settings', 'fixedtoc' ); ?>
			</h2>
			<form method="post" action="options.php">
				<?php 
					settings_fields( $this->option_group );
					do_settings_sections( $this->menu_slug );
					submit_button();
				?>
			</form>
		</div>
	<?php
	}

	/**
	 * Register settings.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @void
	 */
	public function register() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_setting' ) );
		
		// Add sections
		require_once 'class-setting-sections.php';
		new Fixedtoc_Setting_Sections( $this );
	}
	
	/**
	 * Sanitize Setting.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $new_vals
	 * @return array
	 */
	public function sanitize_setting( $new_vals ) {
		if ( ! $new_vals ) {
			return $new_vals;
		}
		
		$new_vals = Fixedtoc_Admin_Control::sanitize( $new_vals );
		
		// Merge cutomize option value.
		$old_vals = get_option( 'fixed_toc' );
		if ( false !== $old_vals ) {
			$new_vals = array_merge( $old_vals, $new_vals );
		}
		
		return $new_vals;
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
		add_settings_section( $id, $title, $callback, $this->menu_slug );
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
		$id = sanitize_key( 'fixedtoc_option_' . $name );
		$title = fixedtoc_get_field_data( $field_name, 'label' );
		
		$args = fixedtoc_get_field_data( $field_name );
		$args['input_attrs']['id'] = $id;
		$args['label_for'] = $id;
		$args['name'] = $this->option_name . '[' . $name . ']';
		$args['value'] = fixedtoc_get_option( $name );
		
		add_settings_field( $id, $title, array( $this, 'render_field' ), $this->menu_slug, $section_id, $args );
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
		$html 			.= isset( $args['des'] ) && $args['des'] ? '<p class="description">' . $args['des'] . '</p>' : '';

		echo $html;		
	}
	
	/**
	 * Enqueue scripts
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $hook
	 * @return void
	 */	
	public function enqueue_scripts( $hook ) {
		if ( 'settings_page_fixedtoc' != $hook ) {
			return;
		}
		
		wp_enqueue_script( 'fixedtoc_setting_script', plugins_url( 'setting.js', __FILE__ ), array( 'jquery' ) );
	}

	/**
	 * Print inline styles
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $hook
	 * @return void
	 */	
	public function print_inline_styles() {
	?>
		<style type="text/css">
			#ftoc-title-icon {
				width: auto;
				height: auto;
				line-height: inherit;
				font-size: 1.3em;
			}
		</style>
	<?php	
	}
	
}