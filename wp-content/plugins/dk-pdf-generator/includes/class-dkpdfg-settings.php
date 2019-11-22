<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class DKPDFG_Settings {

	private static $_instance = null;
	public $parent = null;
	public $_token;
	public $base = '';
	public $settings = array();

	public function __construct ( $parent ) {

		$this->parent = $parent;

		$this->base = 'dkpdfg_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add DK PDF Generator page to DK PDF admin menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ), 20 );

	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {

		$this->settings = $this->settings_fields();

		// create an option for selected posts
		add_option( 'dkpdfg_selected_posts', array() );

		// create an option for selected post categories
		add_option( 'dkpdfg_selected_categories', array() );

		// create options for date ranges (select post categories)
		add_option( 'dkpdfg_date_from', date( 'Y-m-d', strtotime("-1 month")) );
		add_option( 'dkpdfg_date_to', date( 'Y-m-d', current_time( 'timestamp', 1 )) );
	}

	/**
	 * Add settings page to tools page
	 * @return void
	 */
	public function add_menu_item () {

		if ( is_plugin_active( 'dk-pdf/dk-pdf.php' ) ) {

			$page = add_submenu_page( 'dkpdf' . '_settings', 'DK PDF Generator', 'DK PDF Generator', 'manage_options', 'dkpdf-gtool', array( $this, 'dkpdf_gtool_screen' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );

		}

	}

	public function dkpdf_gtool_screen() {

		// Build page HTML
		$html = '<div class="wrap" id="' . 'dkpdfg' . '_settings">' . "\n";
			$html .= '<h2>' . __( 'DK PDF Generator' , 'dkpdfg' ) . '</h2>' . "\n";

			$html .= '<div class="dkpdfg-container">';

				if ( is_plugin_active( 'dk-pdf/dk-pdf.php' ) ) {

					$html .= '<div class="dkpdfg-container-left">';

						// select posts one by one
						$html .= '<h3 style="margin-bottom:0px;">'. __('Select posts, pages and custom post types', 'dkpdfg').'</h3>';
						$html .= '<div style="width:100%;float:left;text-align:right;padding-bottom:5px;"><a id="dkpdfg-clearoptions" href="#">Clear all</a></div>';
						$html .= '<div style="width:100%;float:left;" class="control-group">';
						  $html .= '<select style="width:100%;"id="dkpdfg-search-posts" multiple class="dkpdf-posts-controller" placeholder="'. __('Search by title', 'dkpdfg').'">';
						  $html .= '</select>';
						$html .= '</div>';

						// create pdf button
						$html .= '<form style="width:100%;float:left;margin-top:15px;margin-bottom:25px;" id="dkpdfg-create-pdf" method="post">';
							$html .= '<input id="dkpdfg-create-button" type="submit" class="button-primary" disabled value="'. esc_attr( __( 'Create PDF' , 'dkpdfg' ) ) . '"/>';
							$html .= wp_nonce_field( 'dkpdfg_create_pdf_action', 'dkpdfg_create_pdf_nonce_field' );
							$html .= '<input type="hidden" name="dkpdfg_action_create" value="dkpdfg_action_create">';
						$html .= '</form>';

						$html .= '<hr>';

						// select categories
						$html .= '<h3 style="margin-bottom:0px;">'. __('Select posts categories and taxonomy terms', 'dkpdfg').'</h3>';
						$html .= '<div style="width:100%;float:left;text-align:right;padding-bottom:5px;"><a id="dkpdfg-categories-clearoptions" href="#">Clear all</a></div>';
						$html .= '<div style="width:100%;float:left;" class="control-group">';
						  $html .= '<select style="width:100%;"id="dkpdfg-search-categories" multiple class="dkpdf-categories-controller" placeholder="'. __('Search by category name or taxonomy term', 'dkpdfg').'">';
						  $html .= '</select>';
						$html .= '</div>';

						// select by creation date
						$date_from = get_option( 'dkpdfg_date_from', date( 'Y-m-d', strtotime("-1 month")) );
						$date_to = get_option( 'dkpdfg_date_to', date( 'Y-m-d', current_time( 'timestamp', 1 )) );
						$html .= '<h4 style="margin-bottom:10px;width:100%;float:left;margin-top:10px;">'. __('Select date range (from - to)', 'dkpdfg').'</h4>';
						$html .= '<input class="dkpdfg-dates" type="text" id="dkpdfg-date-from" name="dkpdfg-date-from" value="'. $date_from .'"/>';
						$html .= '<input class="dkpdfg-dates" type="text" id="dkpdfg-date-to" name="dkpdfg-date-top" value="'. $date_to .'"/>';

						// create categories pdf button
						$html .= '<form style="width:100%;float:left;margin-top:15px;margin-bottom:25px;" id="dkpdfg-create-categories-pdf" method="post">';
							$html .= '<input id="dkpdfg-create-categories-button" type="submit" class="button-primary" disabled value="'. esc_attr( __( 'Create PDF' , 'dkpdfg' ) ) . '"/>';
							$html .= wp_nonce_field( 'dkpdfg_create_categories_pdf_action', 'dkpdfg_create_categories_pdf_nonce_field' );
							$html .= '<input type="hidden" name="dkpdfg_action_create_categories" value="dkpdfg_action_create_categories">';
						$html .= '</form>';

						$html .= '<hr>';

					$html .= '</div>';

					$html .= '<div class="dkpdfg-container-right">';

						$tab = '';
						if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
							$tab .= $_GET['tab'];
						}

						// Show page tabs
						if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

							$html .= '<h2 class="nav-tab-wrapper">' . "\n";

							$c = 0;
							foreach ( $this->settings as $section => $data ) {

								// Set tab class
								$class = 'nav-tab';
								if ( ! isset( $_GET['tab'] ) ) {
									if ( 0 == $c ) {
										$class .= ' nav-tab-active';
									}
								} else {
									if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
										$class .= ' nav-tab-active';
									}
								}

								// Set tab link
								$tab_link = add_query_arg( array( 'tab' => $section ) );
								if ( isset( $_GET['settings-updated'] ) ) {
									$tab_link = remove_query_arg( 'settings-updated', $tab_link );
								}

								// Output tab
								$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

								++$c;
							}

							$html .= '</h2>' . "\n";
						}

						// settings form
						$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

							// Get settings fields
							ob_start();
							settings_fields( 'dkpdfg' . '_settings' );
							do_settings_sections( 'dkpdfg' . '_settings' );
							$html .= ob_get_clean();

							$html .= '<p class="submit">' . "\n";
								$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
								$html .= '<input name="dkpdfg-save" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'dkpdfg' ) ) . '" />' . "\n";
							$html .= '</p>' . "\n";

						$html .= '</form>' . "\n";

					$html .= '</div>';

				} else {

					$html .= 'Please active DK PDF plugin.';

				}

			$html .= '</div>';

		$html .= '</div>' . "\n";

		echo $html;

	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the dkpdfg-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( 'dkpdfg' . '-settings-js', plugins_url( 'dk-pdf-generator/assets/js/settings-admin.js' ), array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( 'dkpdfg' . '-settings-js' );

	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . 'dkpdfg' . '_settings">' . __( 'Settings', 'dkpdfg' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['dkpdfg_cover'] = array(
			'title'					=> __( 'Cover', 'dkpdfg' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'show_cover',
					'label'			=> __( 'Show Cover', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'cover_title',
					'label'			=> __( 'Title' , 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'cover_description',
					'label'			=> __( 'Description' , 'dkpdfg' ),
					'description'	=> __( 'HTML tags are allowed.', 'dkpdf' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'cover_text_align',
					'label'			=> __( 'Text align', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ),
					'default'		=> 'left'
				),
				array(
					'id' 			=> 'cover_text_margin_top',
					'label'			=> __( 'Text margin top', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'number',
					'default'		=> '100',
					'placeholder'	=> '100'
				),
				array(
					'id' 			=> 'cover_text_color',
					'label'			=> __( 'Text color', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'color',
					'default'		=> '#000'
				),
				array(
					'id' 			=> 'cover_bg_color',
					'label'			=> __( 'Background color', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'color',
					'default'		=> '#FFF'
				),

			)
		);

		$settings['dkpdfg_toc'] = array(
			'title'					=> __( 'Table of contents', 'dkpdfg' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'show_toc',
					'label'			=> __( 'Show Table of contents page', 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'toc_title',
					'label'			=> __( 'Title' , 'dkpdfg' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> __( 'Table of contents' , 'dkpdfg' ),
					'placeholder'	=> ''
				),
			)
		);

		$settings['dkpdfg_button'] = array(
			'title'					=> __( 'PDF Button Shortcode', 'dkpdfg' ),
			'description'			=> 'Use [dkpdfg-button] inside your content or in your templates via echo do_shortcode("[dkpdfg-button]");',
			'fields'				=> array(
				array(
					'id' 			=> 'pdfgbutton_text',
					'label'			=> __( 'Button text' , 'dkpdf' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> 'PDF Button',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'pdfgbutton_align',
					'label'			=> __( 'Align', 'dkpdf' ),
					'description'	=> '',
					'type'			=> 'radio',
					'options'		=> array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ),
					'default'		=> 'right'
				),
			)
		);

		$settings = apply_filters( 'dkpdfg' . '_settings_fields', $settings );

		return $settings;

	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'dkpdfg' . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( 'dkpdfg' . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), 'dkpdfg' . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Main DKPDFG_Settings Instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
