<?php
/**
 * Setmore Plus admin class
 */
if ( !defined( 'ABSPATH' ) ) die;

class Setmore_Plus_Admin {

	public $tags;

	function __construct() {

		$this->set_tags();

		$this->add_actions();

	}

	private function set_tags() {
		$this->tags = array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
				'class'  => array() ),
			'b' => array(),
		);
	}

	public function add_actions() {
		add_action( 'admin_init', array( $this, 'default_settings' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ), 20 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		add_action( 'load-settings_page_setmoreplus', array( $this, 'load_admin_scripts' ) );
		add_action( 'load-settings_page_setmoreplus', array( $this, 'load_lnt_style' ) );

		// Loading Colorbox to show a screenshot in a popup, not for the scheduler.
		add_action( 'load-settings_page_setmoreplus', array( $this, 'load_colorbox' ) );

		add_action( 'load-widgets.php', array( $this, 'load_widget_scripts' ) );

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		// LNT icon
		add_action( 'load-plugins.php', array( $this, 'load_lnt_style' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

		add_action( 'wp_ajax_setmoreplus_add_url', array( $this, 'add_url_function' ) );
		add_action( 'wp_ajax_nopriv_setmoreplus_add_url', array( $this, 'add_url_function' ) );
	}

	/**
	 * Update settings
	 */
	public function default_settings() {
		$options         = get_option( 'setmoreplus' );
        $default_options = $this->get_defaults();
		$plugin_version  = Setmore_Plus::get_plugin_version();

		if ( !$options ) {

			// New activation
			$options = $default_options;

		} else {

            // >= 3.7
            $current_version = get_option( 'setmoreplus_version' );
            if ( !$current_version ) {
                // < 3.7
                $current_version = $options['plugin_version'];
            }

            if ( $current_version == $plugin_version ) {
                return;
            }

            // Updating from 2.1
            $previous_setting = get_option( 'setmoreplus_url' );
            if ( $previous_setting ) {
                $default_options['url'] = $previous_setting;
                delete_option( 'setmoreplus_url' );
            }

            unset( $options['plugin_version'] );

            /**
			 * Update all URLs to https
			 * @since 3.7
			 */
			$options['url'] = Setmore_Plus::add_scheme( $options['url'] );

			if ( $options['staff_urls'] ) {

				foreach ( $options['staff_urls'] as $key => $data ) {
					if ( $data['url'] ) {
						$options['staff_urls'][ $key ]['url'] = Setmore_Plus::add_scheme( $data['url'] );
					}
				}

			}

			// Merge in new options
			$options = array_merge( $default_options, $options );

		}

		update_option( 'setmoreplus', $options );
		update_option( 'setmoreplus_version', $plugin_version );
	}

	public function settings_init() {

		register_setting( 'setmoreplus_group', 'setmoreplus', array( $this, 'sanitize_options' ) );

		add_settings_section(
			'setmoreplus_section',
			'',
			'',
			'setmoreplus_group'
		);

		add_settings_field(
			'setmoreplus-lang',
			'<label for="setmoreplus_lang">'. __( 'Preferred Language', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_lang' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-link-text',
			'<label for="setmoreplus_link_text">'. __( 'Link Text', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_link_text' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-url',
			'<label for="setmoreplus_url">'. __( 'Your Booking Page URL', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_url' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-staff-urls',
			'<label for="setmoreplus_staff_urls">'. __( 'Your Staff Booking Pages', 'setmore-plus' ) . '</label><br><em style="font-weight: 400">(optional)</em>',
			array( $this, 'render_setting_staff_urls' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-app-examples',
			'<label for="setmoreplus_app_examples">'. __( 'Scheduler Sizes', 'setmore-plus' ) . '</label>',
			array( $this, 'render_app_examples' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-popup-dimensions',
			'<label for="setmoreplus_popup_dimensions">'. __( 'Popup Dimensions', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_popup_dimensions' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-embed-dimensions',
			'<label for="setmoreplus_embed_dimensions">'. __( 'Embed Dimensions', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_embed_dimensions' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-defer',
			'<label for="setmoreplus_defer">' . __( 'Script Loading', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_defer' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

		add_settings_field(
			'setmoreplus-lnt',
			'<label for="setmoreplus_lnt" class="lnt">' . __( 'Leave No Trace', 'setmore-plus' ) . '</label>',
			array( $this, 'render_setting_lnt' ),
			'setmoreplus_group',
			'setmoreplus_section'
		);

	}

	public function add_admin_menu() {
		add_options_page( 'Setmore Plus', 'Setmore Plus', 'manage_options', 'setmoreplus', array( $this, 'options_page' ) );
	}

	public function load_admin_scripts() {
		$options = get_option( 'setmoreplus' );
		$version = get_option( 'setmoreplus_version' );

		wp_enqueue_style( 'setmoreplus-admin', SETMOREPLUS_URL . 'css/admin.css', array(), $version );

		wp_enqueue_script( 'setmoreplus-admin-script', SETMOREPLUS_URL . 'js/setmoreplus-admin.js', array( 'colorbox-script' ), $version, $options['defer'] );

		wp_localize_script( 'setmoreplus-admin-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function load_lnt_style() {
		wp_enqueue_style( 'setmoreplus-lnt', SETMOREPLUS_URL . 'css/lnt.css' );
	}

	public function load_colorbox() {
		wp_enqueue_style( 'colorbox-style', SETMOREPLUS_URL . 'inc/colorbox/colorbox.css' );
		wp_enqueue_script( 'colorbox-script', SETMOREPLUS_URL . 'inc/colorbox/jquery.colorbox-min.js', array( 'jquery' ), false, true );
	}

	public function load_widget_scripts() {
		wp_enqueue_style( 'setmoreplus-widget-admin', SETMOREPLUS_URL . 'css/widget-admin.css' );
	}

	public function get_defaults() {
		return array(
			'lang'                    => 'english',
			'link_text'               => __( 'Book Appointment', 'setmore-plus' ),
			'url'                     => '',
			'staff_urls'              => null,
			'width'                   => 585,
			'width_p'                 => 'px',
			'height'                  => 680,
			'height_p'                => 'px',
			'mobile_breakpoint'       => 585,
			'embed_desktop_width'     => 585,
			'embed_desktop_width_p'   => 'px',
			'embed_desktop_height'    => 680,
			'embed_mobile_breakpoint' => 585,
			'embed_mobile_height'     => 680,
			'defer'                   => 1,
			'lnt'                     => 1,
		);
	}

	/**
	 * Plugin action links
	 *
	 * @param $links
	 * @param $file
	 * @return mixed
	 */
	public function plugin_action_links( $links, $file ) {
		if ( $file == SETMOREPLUS ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=setmoreplus' ), __( 'Settings', 'setmore-plus' ) );
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Plugin meta row
	 *
	 * @param        $plugin_meta
	 * @param        $plugin_file
	 * @param array  $plugin_data
	 * @param string $status
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data = array(), $status = '' ) {
		if ( $plugin_file == SETMOREPLUS ) {
			$plugin_meta[] = '<span class="lnt">' . __( 'Leave No Trace', 'setmore-plus' ) . '</span>';
		}
		return $plugin_meta;
	}

	/**
	 * [Add New] Ajax receiver
	 */
	public function add_url_function() {
		$count = $_REQUEST['count'] + 1;
		$this->render_staff_url_row( $count );
		die();
	}

	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div class="wrap setmore">
			<h1><?php _e( 'Setmore Plus', 'setmore-plus' ); ?></h1>
			<?php
			$tab    = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
			$screen = get_current_screen();
			$url    = add_query_arg( 'page', 'setmoreplus', admin_url( $screen->parent_file ) );
			?>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo $url; ?>" class="nav-tab <?php $this->is_active_tab( $tab, '' ); ?>">
					<?php _e( 'Settings', 'setmore-plus' ); ?>
				</a>
				<a href="<?php echo $url; ?>&tab=instructions" class="nav-tab <?php $this->is_active_tab( $tab, 'instructions' ); ?>">
					<?php _e( 'Instructions', 'setmore-plus' ); ?>
				</a>
			</h2>
			<?php
			switch ( $tab ) {
				case 'instructions':
					include SETMOREPLUS_DIR . 'inc/instructions.php';
					break;
				default:
					include SETMOREPLUS_DIR . 'inc/settings.php';
			}
			?>
		</div>
		<?php
	}

	public function is_active_tab( $tab, $nav_tab ) {
		echo $tab == $nav_tab ? 'nav-tab-active' : '';
	}

	public function sanitize_options( $input ) {
		$new['lang'] = sanitize_text_field( $input['lang'] );

		if ( $input['link_text'] ) {
			$new['link_text'] = sanitize_text_field( $input['link_text'] );
		} else {
			$defaults = $this->get_defaults();
			$new['link_text'] = $defaults['link_text'];
		}

		$new['url'] = Setmore_Plus::add_scheme( sanitize_text_field( $input['url'] ) );

		if ( isset( $input['staff_urls'] ) ) {

			// Using independent counter because we cannot actually change the input value in the DOM.
			$i = 1;
			foreach ( $input['staff_urls'] as $id => $data ) {
				if ( $data['name'] && $data['url'] ) {
					$new['staff_urls'][ $i++ ] = array(
						'name' => sanitize_text_field( $data['name'] ),
						'url'  => Setmore_Plus::add_scheme( sanitize_text_field( $data['url'] ) ),
					);
				}
			}

		} else {

			$new['staff_urls'] = null;

		}

		$new['width']             = sanitize_text_field( $input['width'] );
		$new['width_p']           = sanitize_text_field( $input['width_p'] );
		$new['height']            = sanitize_text_field( $input['height'] );
		$new['height_p']          = sanitize_text_field( $input['height_p'] );
		$new['mobile_breakpoint'] = sanitize_text_field( $input['mobile_breakpoint'] );

		$new['embed_desktop_width']   = sanitize_text_field( $input['embed_desktop_width'] );
		$new['embed_desktop_width_p'] = sanitize_text_field( $input['embed_desktop_width_p'] );
		$new['embed_desktop_height']  = sanitize_text_field( $input['embed_desktop_height'] );

		$new['embed_mobile_breakpoint'] = sanitize_text_field( $input['embed_mobile_breakpoint'] );
		$new['embed_mobile_height']     = sanitize_text_field( $input['embed_mobile_height'] );

		$new['defer'] = isset( $input['defer'] ) ? $input['defer'] : 1;
		$new['lnt']   = isset( $input['lnt'] ) ? $input['lnt'] : 0;

		return $new;
	}

	public function render_setting_lang() {
		$options = get_option( 'setmoreplus' );
		$languages = Setmore_Plus::get_lang();
		?>
		<select id="setmoreplus_lang" name="setmoreplus[lang]">
			<option value="" <?php selected( '', $options['lang'] ); ?>>
				<?php _e( 'use site language', 'setmore-plus' ); ?>
			</option>
			<?php foreach ( $languages as $key => $lang ): ?>
				<option value="<?php echo $key; ?>" <?php selected( $key, $options['lang'] ); ?>>
					<?php echo $lang; ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p>
			<?php printf( __( 'Setmore supports %d languages. If using your site language and the language is not supported, the default is English.', 'setmore-plus' ), count( $languages ) ); ?>
			<?php printf(
				wp_kses(
					__( '<a href="%s" target="_blank">Help</a>' ),
					$this->tags
				),
				esc_url( 'http://support.setmore.com/article/288-customize-your-booking-page' )
			); ?>
		</p>
		<?php
	}

	public function render_setting_link_text() {
		$options = get_option( 'setmoreplus' );
		?>
		<input type="text" id="setmoreplus_link_text"
			   name="setmoreplus[link_text]" value="<?php echo $options['link_text']; ?>"
			   class="regular-text"/>
		<?php
	}

	public function render_setting_url() {
		$options = get_option( 'setmoreplus' );
		?>
		<input type="text" id="setmoreplus_url" name="setmoreplus[url]" value="<?php echo $options['url']; ?>"
			   placeholder="<?php _e( 'Setmore Booking Page URL', 'setmore-plus' ); ?>" style="width: 100%;"/>
		<p>
			<?php printf(
				wp_kses(
					__( 'To find your unique URL, <a href="%s" target="_blank">sign in to Setmore</a> and click on <b>Apps & Integrations</b>.', 'setmore-plus' ),
					$this->tags
				),
				esc_url( 'https://my.setmore.com/profile/#configure' )
			); ?>
			<?php printf(
				wp_kses(
					__( '<a href="%s" target="_blank">Help</a>' ),
					$this->tags
				),
				esc_url( 'http://support.setmore.com/article/208-booking-page-url' )
			); ?>
		</p>
		<?php
	}

	public function render_setting_staff_urls() {
		$options = get_option( 'setmoreplus' );
		?>
		<div class="table-wrapper">
			<div id="staff-urls" class="table">
				<div class="row staff-header">
					<div class="cell staff-ids"><?php _e( 'ID' ); ?></div>
					<div class="cell staff-name"><?php _e( 'Staff Name', 'setmore-plus' ); ?></div>
					<div class="cell"><?php _e( 'Staff Booking Page URL', 'setmore-plus' ); ?></div>
					<div class="cell"></div>
				</div>
				<?php
				if ( $options['staff_urls'] ) {
					foreach ( $options['staff_urls'] as $id => $data ) {
						$this->render_staff_url_row( $id, $data['name'], $data['url'] );
					}
				}
				?>
			</div>
		</div>
		<p><input type="button" class="button" id="add-url" value="<?php _e( 'Add New', 'setmore-plus' ); ?>"></p>

		<p>
			<?php printf(
				wp_kses(
					__( 'To find your individual Staff Booking Pages, <a href="%s" target="_blank">sign in to Setmore</a> and navigate to <b>Settings > Staff</b>.', 'setmore-plus' ),
					$this->tags
				),
				esc_url( 'http://my.setmore.com' )
			); ?>
		</p>
		<?php
	}

	public function render_staff_url_row( $id, $name = '', $url = '' ) {
		?>
		<div class="row staff-row">
			<input type="hidden" class="original-order" value="<?php echo $id; ?>">
			<div class="cell">
				<div class="staff-id">
					<?php echo $id; ?>
				</div>
			</div>
			<div class="cell staff-name">
				<input type="text" name="setmoreplus[staff_urls][<?php echo $id; ?>][name]"
					   value="<?php echo $name; ?>" placeholder="<?php _e( 'staff name', 'setmore-plus' ); ?>">
			</div>
			<div class="cell staff-url">
				<input type="text" name="setmoreplus[staff_urls][<?php echo $id; ?>][url]"
					   value="<?php echo $url; ?>"
					   placeholder="<?php _e( 'Staff Booking Page', 'setmore-plus' ); ?>">
			</div>
			<div class="cell staff-delete"></div>
		</div>
		<?php
	}

	public function render_app_examples() {
		?>
		<p><?php _e( 'The scheduler has three similar versions depending on the width of the popup or embed frame.', 'setmore-plus' ); ?> <a id="openGallery" href="#"><?php _e( 'See examples', 'setmore-plus' ); ?></a></p>
		<table class="sizes">
			<tr>
				<th><?php _e( 'Size', 'setmore-plus' ); ?></th>
				<th><?php _e( 'Intended Use', 'setmore-plus' ); ?></th>
				<th><?php _e( 'Width', 'setmore-plus' ); ?></th>
			</tr>
			<tr>
				<td><?php _e( 'Small', 'setmore-plus' ); ?></td>
				<td><?php _e( 'mobile devices', 'setmore-plus' ); ?></td>
				<td><?php _e( 'less than 582 pixels, at least 320 pixels recommended', 'setmore-plus' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Medium', 'setmore-plus' ); ?></td>
				<td><?php _e( 'desktops', 'setmore-plus' ); ?></td>
				<td><?php _e( 'at least 582 pixels', 'setmore-plus' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'Large', 'setmore-plus' ); ?></td>
				<td><?php _e( 'desktop full-width pages', 'setmore-plus' ); ?></td>
				<td><?php _e( 'at least 873 pixels', 'setmore-plus' ); ?></td>
			</tr>
		</table>
		<div style="display: none;">
			<a href="<?php echo SETMOREPLUS_IMAGES; ?>SetmorePlus-small.png" class="screenshot"
			   title="<?php _e( 'Small', 'setmore-plus' ); ?>">Small</a>
			<a href="<?php echo SETMOREPLUS_IMAGES; ?>SetmorePlus-medium.png" class="screenshot"
			   title="<?php _e( 'Medium', 'setmore-plus' ); ?>">Medium</a>
			<a href="<?php echo SETMOREPLUS_IMAGES; ?>SetmorePlus-large.png" class="screenshot"
			   title="<?php _e( 'Large', 'setmore-plus' ); ?>">Large</a>
		</div>
		<?php
	}

	public function render_setting_popup_dimensions() {
		$options  = get_option( 'setmoreplus' );
		$defaults = $this->get_defaults();
		?>
		<p><?php _e( 'When using <code>[setmoreplus link]</code> or <code>[setmoreplus button]</code>:', 'setmore-plus' ); ?></p>
		<div id="setmoreplus_popup_dimensions">
			<table class="dimensions">
				<tr>
					<td></td>
					<td><?php _e( 'Width', 'setmore-plus' ); ?></td>
					<td><?php _e( 'Height', 'setmore-plus' ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Desktop', 'setmore-plus' ); ?></td>
					<td>
						<label>
							<input id="setmoreplus_width" type="text" name="setmoreplus[width]"
								   value="<?php echo $options['width']; ?>"
								   data-default="<?php echo $defaults['width']; ?>"
								   data-current="<?php echo $options['width']; ?>"
								   class="four-digits next-to-select">
						</label>
						<label>
							<select name="setmoreplus[width_p]"
									data-default="<?php echo $defaults['width_p']; ?>"
									data-target="setmoreplus_width"
									class="pxpct">
								<option value="px" <?php selected( $options['width_p'], 'px' ); ?>><?php _e( 'pixels', 'setmore-plus' ); ?></option>
								<option value="%" <?php selected( $options['width_p'], '%' ); ?>><?php _e( 'percent', 'setmore-plus' ); ?></option>
							</select>
						</label>
					</td>
					<td>
						<label>
							<input id="setmoreplus_height" type="text" name="setmoreplus[height]"
								   value="<?php echo $options['height']; ?>"
								   data-default="<?php echo $defaults['height']; ?>"
								   data-current="<?php echo $options['height']; ?>"
								   class="four-digits next-to-select">
						</label>
						<label>
							<select name="setmoreplus[height_p]"
									data-default="<?php echo $defaults['height_p']; ?>"
									data-target="setmoreplus_height"
									class="pxpct">
								<option value="px" <?php selected( $options['height_p'], 'px' ); ?>><?php _e( 'pixels', 'setmore-plus' ); ?></option>
								<option value="%" <?php selected( $options['height_p'], '%' ); ?>><?php _e( 'percent', 'setmore-plus' ); ?></option>
							</select>
						</label>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Mobile', 'setmore-plus' ); ?></td>
					<td colspan="2">
						<label>
							<?php _e( '100% <b>screen</b> width and height below', 'setmore-plus' ); ?>
							<input type="text" name="setmoreplus[mobile_breakpoint]"
								   value="<?php echo $options['mobile_breakpoint']; ?>"
								   data-default="<?php echo $defaults['mobile_breakpoint']; ?>"
								   class="four-digits"> <?php _e( 'pixels', 'setmore-plus' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<p><input type="button" class="button secondary restore-defaults" value="<?php _e( 'Restore Defaults', 'setmore-plus' ); ?>"></p>
		</div>
		<?php
	}

	public function render_setting_embed_dimensions() {
		$options  = get_option( 'setmoreplus' );
		$defaults = $this->get_defaults();
		?>
		<p><?php _e( 'When using <code>[setmoreplus]</code> to embed the scheduler directly in a page:', 'setmore-plus' ); ?></p>
		<div id="setmoreplus_embed_dimensions">
			<table class="dimensions">
				<tr>
					<td></td>
					<td><?php _e( 'Width', 'setmore-plus' ); ?></td>
					<td><?php _e( 'Height', 'setmore-plus' ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Desktop', 'setmore-plus' ); ?></td>
					<td>
						<label>
							<input id="setmoreplus_embed_desktop_width" type="text" name="setmoreplus[embed_desktop_width]"
								   value="<?php echo $options['embed_desktop_width']; ?>"
								   data-default="<?php echo $defaults['embed_desktop_width']; ?>"
								   data-current="<?php echo $options['embed_desktop_width']; ?>"
								   class="four-digits next-to-select">
						</label>
						<label>
							<select name="setmoreplus[embed_desktop_width_p]"
									data-default="<?php echo $defaults['embed_desktop_width_p']; ?>"
									data-target="setmoreplus_embed_desktop_width"
									class="pxpct">
								<option value="px" <?php selected( $options['embed_desktop_width_p'], 'px' ); ?>><?php _e( 'pixels', 'setmore-plus' ); ?></option>
								<option value="%" <?php selected( $options['embed_desktop_width_p'], '%' ); ?>><?php _e( 'percent', 'setmore-plus' ); ?></option>
							</select>
						</label>
					</td>
					<td>
						<label>
							<input id="setmoreplus_embed_desktop_height" type="text" name="setmoreplus[embed_desktop_height]"
								   value="<?php echo $options['embed_desktop_height']; ?>"
								   data-default="<?php echo $defaults['embed_desktop_height']; ?>"
								   data-current="<?php echo $options['embed_desktop_height']; ?>"
								   class="four-digits"> <?php _e( 'pixels', 'setmore-plus' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Mobile', 'setmore-plus' ); ?></td>
					<td>
						<label>
							<?php _e( '100% <b>container</b> width below', 'setmore-plus' ); ?>
							<input type="text" name="setmoreplus[embed_mobile_breakpoint]"
								   value="<?php echo $options['embed_mobile_breakpoint']; ?>"
								   data-default="<?php echo $defaults['embed_mobile_breakpoint']; ?>"
								   class="four-digits"> <?php _e( 'pixels', 'setmore-plus' ); ?>
						</label>
					</td>
					<td>
						<label>
							<input id="setmoreplus_embed_mobile_height" type="text" name="setmoreplus[embed_mobile_height]"
								   value="<?php echo $options['embed_mobile_height']; ?>"
								   data-default="<?php echo $defaults['embed_mobile_height']; ?>"
								   class="four-digits"> <?php _e( 'pixels', 'setmore-plus' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<p><input type="button" class="button secondary restore-defaults" value="<?php _e( 'Restore Defaults', 'setmore-plus' ); ?>"></p>
		</div>
		<?php
	}

	public function render_setting_defer() {
		$options = get_option( 'setmoreplus' );
		?>
		<div id="defer">
			<select id="setmoreplus_defer" name="setmoreplus[defer]">
				<option value="1" <?php selected( $options['defer'], 1 ); ?>>
					<?php _e( 'Normal (default)', 'setmore-plus' ); ?>
				</option>
				<option value="0" <?php selected( $options['defer'], 0 ); ?>>
					<?php _e( 'Priority', 'setmore-plus' ); ?>
				</option>
			</select>

			<p>
				<?php _e( '<strong>Normal</strong> works well in the majority of cases.', 'setmore-plus' ); ?>
				<?php _e( 'Try <strong>Priority</strong> if your Setmore link fails to produce a popup.', 'setmore-plus' ); ?>
			</p>
		</div>
		<?php
	}

	public function render_setting_lnt() {
		$options = get_option( 'setmoreplus' );
		?>
		<div id="leave-no-trace">
			<select id="setmoreplus_lnt" name="setmoreplus[lnt]">
				<option value="1" <?php selected( $options['lnt'], 1 ); ?>>
					<?php _e( 'Yes - Deleting this plugin will also delete these settings.', 'setmore-plus' ); ?>
				</option>
				<option value="0" <?php selected( $options['lnt'], 0 ); ?>>
					<?php _e( 'No - These settings will remain after deleting this plugin.', 'setmore-plus' ); ?>
				</option>
			</select>

			<p class="description">
				<?php _e( 'Deactivating this plugin will not delete anything.', 'setmore-plus' ); ?>
			</p>
		</div>
		<?php
	}

}
