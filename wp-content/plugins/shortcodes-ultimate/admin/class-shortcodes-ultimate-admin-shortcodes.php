<?php

/**
 * The Available shortcodes menu component.
 *
 * @since        5.0.0
 *
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/admin
 */
final class Shortcodes_Ultimate_Admin_Shortcodes extends Shortcodes_Ultimate_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  5.0.0
	 * @param string  $plugin_file    The path of the main plugin file
	 * @param string  $plugin_version The current version of the plugin
	 */
	public function __construct( $plugin_file, $plugin_version, $plugin_prefix ) {
		parent::__construct( $plugin_file, $plugin_version, $plugin_prefix );
	}

	/**
	 * Add menu page.
	 *
	 * @since   5.0.0
	 */
	public function add_menu_pages() {

		/**
		 * Submenu: Available shortcodes
		 * admin.php?page=shortcodes-ultimate
		 */
		$this->add_submenu_page(
			rtrim( $this->plugin_prefix, '-_' ),
			__( 'Available shortcodes', 'shortcodes-ultimate' ),
			__( 'Available shortcodes', 'shortcodes-ultimate' ),
			$this->get_capability(),
			rtrim( $this->plugin_prefix, '-_' ),
			array( $this, 'the_menu_page' )
		);

	}

	/**
	 * Display menu page.
	 *
	 * @since    5.0.8
	 * @return   string   Menu page markup.
	 */
	public function the_menu_page() {
		$this->the_template( 'admin/partials/pages/shortcodes' );
	}

	/**
	 * Add help tabs and set help sidebar at Add-ons page.
	 *
	 * @since  5.0.0
	 * @param WP_Screen $screen WP_Screen instance.
	 */
	public function add_help_tabs( $screen ) {

		if ( ! $this->is_component_page() ) {
			return;
		}

		$screen->add_help_tab( array(
				'id'      => 'shortcodes-ultimate-shortcodes',
				'title'   => __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ),
				'content' => $this->get_template( 'admin/partials/help/shortcodes' ),
			) );

		$screen->set_help_sidebar( $this->get_template( 'admin/partials/help/sidebar' ) );

	}

	/**
	 * Enqueue JavaScript(s) and Stylesheet(s) for the component.
	 *
	 * @since   5.0.0
	 */
	public function enqueue_scripts() {

		if ( ! $this->is_component_page() ) {
			return;
		}

		wp_enqueue_style( 'shortcodes-ultimate-admin', $this->plugin_url . 'admin/css/admin.css', array( 'su-icons' ), $this->plugin_version );

	}

	/**
	 * Helper function to create shortcode code with default settings.
	 *
	 * Example output: "[su_button color="#ff0000" ... ] Click me [/su_button]".
	 *
	 * @param mixed   $args Array with settings
	 * @since  5.0.0
	 * @return string      Shortcode code
	 */
	protected function get_shortcode_code( $args ) {

		$defaults = array(
			'id'     => '',
			'number' => 1,
			'nested' => false,
		);

		// Accept shortcode ID as a string
		if ( is_string( $args ) ) {
			$args = array( 'id' => $args );
		}

		$args = wp_parse_args( $args, $defaults );

		// Check shortcode ID
		if ( empty( $args['id'] ) ) {
			return '';
		}

		// Get shortcode data
		$shortcode = $this->get_shortcode( $args['id'] );

		// Prepare shortcode prefix
		$prefix = get_option( 'su_option_prefix' );

		// Prepare attributes container
		$attributes = '';

		// Loop through attributes
		foreach ( $shortcode['atts'] as $attr_id => $attribute ) {

			// Skip hidden attributes
			if ( isset( $attribute['hidden'] ) && $attribute['hidden'] ) {
				continue;
			}

			// Add attribute
			$attributes .= sprintf( ' %s="%s"', esc_html( $attr_id ), esc_attr( $attribute['default'] ) );

		}

		// Create opening tag with attributes
		$output = "[{$prefix}{$args['id']}{$attributes}]";

		// Indent nested shortcodes
		if ( $args['nested'] ) {
			$output = "\t" . $output;
		}

		// Insert shortcode content
		if ( isset( $shortcode['content'] ) ) {

			if ( is_string( $shortcode['content'] ) ) {
				$output .= $shortcode['content'];
			}

			// Create complex content
			else if ( is_array( $shortcode['content'] ) && $args['id'] !== $shortcode['content']['id'] ) {

					$shortcode['content']['nested'] = true;
					$output .= $this->get_shortcode_code( $shortcode['content'] );

				}

		}

		// Add closing tag
		if ( isset( $shortcode['type'] ) && $shortcode['type'] === 'wrap' ) {
			$output .= "[/{$prefix}{$args['id']}]";
		}

		// Repeat shortcode
		if ( $args['number'] > 1 ) {
			$output = implode( "\n", array_fill( 0, $args['number'], $output ) );
		}

		// Add line breaks around nested shortcodes
		if ( $args['nested'] ) {
			$output = "\n{$output}\n";
		}

		return $output;

	}

	/**
	 * Retrieve shortcodes data.
	 *
	 * @since  5.0.0
	 * @return array  Shortcodes data.
	 */
	protected function get_shortcodes() {

		$shortcodes = su_get_all_shortcodes();

		foreach ( $shortcodes as $id => $shortcode ) {

			// Skip deprecated shortcodes
			if ( isset( $shortcode['deprecated'] ) ) {
				$shortcode['skip_in_available'] = true;
			}

			// Skip nested shortcodes
			if ( isset( $shortcode['required_parent'] ) ) {
				$shortcode['skip_in_available'] = true;
			}

			// Skip supplementary shortcodes
			if ( isset( $shortcode['required_sibling'] ) ) {
				$shortcode['skip_in_available'] = true;
			}

			$shortcodes[ $id ] = array( 'id' => $id ) + $shortcode;

		}

		return $shortcodes;

	}

	/**
	 * Retrieve shortcodes data for shortcodes-list page.
	 *
	 * @since  5.0.0
	 * @return array  Shortcodes data.
	 */
	protected function get_shortcodes_list() {

		$shortcodes = $this->get_shortcodes();
		$group = isset( $_GET['group'] ) ? sanitize_title( $_GET['group'] ) : false;

		// Filter unwanted shortcodes
		foreach ( $shortcodes as $shortcode ) {

			// Filter skipped shortcodes
			if ( isset( $shortcode['skip_in_available'] ) ) {

				unset( $shortcodes[ $shortcode['id'] ] );
				continue;

			}

			// Filter shortcodes with specified group
			if (
				$group &&
				isset( $shortcode['group'] ) &&
				! in_array( $group, explode( ' ', $shortcode['group'] ) )
			) {

				unset( $shortcodes[ $shortcode['id'] ] );
				continue;

			}

		}

		return $shortcodes;

	}

	/**
	 * Retrieve the groups data.
	 *
	 * @since  5.0.0
	 * @return array  Array with groups data.
	 */
	protected function get_groups() {

		$groups  = su_get_config( 'groups' );
		$current = ( isset( $_GET['group'] ) ) ? sanitize_title( $_GET['group'] ) : 'all';
		$groups['all'] = __( 'All shortcodes', 'shortcodes-ultimate' );

		foreach ( $groups as $id => $title ) {

			$groups[$id] = array(
				'id'     => $id,
				'url'    => add_query_arg( 'group', $id, $this->get_component_url() ),
				'title'  => $title,
				'active' => false,
			);

			if ( $id === 'all' ) {
				$groups[$id]['url'] = $this->get_component_url();
			}

			if ( $id === $current ) {
				$groups[$id]['active'] = true;
			}

		}

		return $groups;

	}

	/**
	 * Retrieve the shortcode data.
	 *
	 * @since  5.0.0
	 * @param string  $shortcode_id The shortcode ID.
	 * @return mixed  Array with shortcode data, or FALSE if shortcode was not found.
	 */
	protected function get_shortcode( $shortcode_id ) {

		$shortcodes = $this->get_shortcodes();

		if ( isset( $shortcodes[ $shortcode_id ] ) ) {

			$data = $shortcodes[ $shortcode_id ];
			$data = $data + array( 'id' => $shortcode_id );

		}

		return isset( $data ) ? $data : false;

	}

	/**
	 * Template tag to retrieve the shortcode data (depending on $_GET).
	 *
	 * @since  5.0.0
	 * @return mixed  Array with shortcode data, or FALSE if shortcode was not found.
	 */
	protected function get_single_shortcode() {

		$shortcode = $this->get_shortcode( sanitize_title( $_GET['shortcode'] ) );

		return isset( $shortcode['skip_in_available'] ) ? false : $shortcode;

	}

	/**
	 * Template tag to retrieve the shortcode options.
	 *
	 * @since  5.0.0
	 * @return mixed  Array with shortcode data, or FALSE if shortcode was not found.
	 */
	protected function get_single_shortcode_options() {

		$options = array();
		$shortcode = $this->get_single_shortcode();

		if ( ! $shortcode || ! isset( $shortcode['atts'] ) ) {
			return $options;
		}

		$options[] = $shortcode;

		if ( isset( $shortcode['required_child'] ) ) {
			$child = $this->get_shortcode( $shortcode['required_child'] );
			$options[] = $child;
		}

		if ( isset( $shortcode['possible_sibling'] ) ) {
			$child = $this->get_shortcode( $shortcode['possible_sibling'] );
			$options[] = $child;
		}

		return $options;

	}

	/**
	 * This conditional tag checks if a singular shortcode is being displayed.
	 *
	 * @since  5.0.0
	 * @return boolean True on success, false on failure.
	 */
	protected function is_single_shortcode_page() {
		return isset( $_GET['shortcode'] );
	}

	/**
	 * Returns shortcodes prefix.
	 *
	 * @since  5.0.0
	 * @return string Shortcodes prefix.
	 */
	protected function get_shortcodes_prefix() {
		return get_option( 'su_option_prefix' );
	}

	/**
	 * Retrieve the string with possible values of specified attribute.
	 *
	 * @since  5.0.0
	 * @param array   $args Attribute details.
	 * @return string       Possible values.
	 */
	protected function get_possible_values( $args ) {

		$args = wp_parse_args( $args, array(
				'type'   => 'text',
				'values' => array(),
				'min'    => 0,
				'max'    => 1,
				'step'   => 1,
			) );

		$image_sources = array(
			'media: 1,2,3'       => __( 'Media file IDs', 'shortcodes-ultimate' ),
			'posts: 1,2,3'       => __( 'Post IDs', 'shortcodes-ultimate' ),
			'posts: recent'      => __( 'Recent posts', 'shortcodes-ultimate' ),
			'category: 1,2,3'    => __( 'Category IDs', 'shortcodes-ultimate' ),
			'taxonomy: book/3,5' => __( 'Taxonomy term slug / term IDs', 'shortcodes-ultimate' ),
		);

		$possible = array(
			'select'          => $this->implodef( '<br>', $args['values'], '%1$s (%2$s)' ),
			'color'           => __( '#HEX color', 'shortcodes-ultimate' ),
			'text'            => __( 'Any text value', 'shortcodes-ultimate' ),
			'border'          => __( 'CSS border shorthand property', 'shortcodes-ultimate' ),
			'shadow'          => __( 'CSS box/text-shadow shorthand property', 'shortcodes-ultimate' ),
			'extra_css_class' => __( 'CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
			'post_type'       => __( 'Post type slug(s) separated by comma(s)', 'shortcodes-ultimate' ),
			'taxonomy'        => __( 'Taxonomy slug(s) separated by comma(s)', 'shortcodes-ultimate' ),
			'term'            => __( 'Term slug(s) separated by comma(s)', 'shortcodes-ultimate' ),
			'upload'          => __( 'The URL of uploaded file', 'shortcodes-ultimate' ),
			'bool'            => sprintf( __( '%s or %s', 'shortcodes-ultimate' ), 'yes', 'no' ),
			'number'          => sprintf( __( 'Number from %s to %s', 'shortcodes-ultimate' ), $args['min'], $args['max'] ),
			'slider'          => sprintf( __( 'Number from %s to %s', 'shortcodes-ultimate' ), $args['min'], $args['max'] ),
			'icon'            => sprintf( '%s. %s: <em>icon: star</em>, <em>http://example.com/icon.png</em>', __( 'FontAwesome icon name (with "icon:" prefix) or icon image URL', 'shortcodes-ultimate' ), __( 'Examples', 'shortcodes-ultimate' ) ),
			'image_source'    => $this->implodef( '<br>', $image_sources, '%1$s (%2$s)' ),
		);

		return isset( $possible[$args['type']] ) ? $possible[$args['type']] : '&mdash;';

	}

	/**
	 * Get default value of specified attribute.
	 *
	 * @since  5.0.0
	 * @param array   $args Attribute details.
	 * @return string       Default value.
	 */
	protected function get_default_value( $args ) {

		if ( isset( $args['default'] ) && $args['default'] !== '' ) {
			return $args['default'];
		}

		return sprintf( '<p class="description">%s</p>', __( 'none', 'shortcodes-ultimate' ) );

	}

	/**
	 * Helper function to display the shortcode image.
	 *
	 * @since  5.0.0
	 * @param array   $shortcode Shortcode data.
	 * @return int               The size of shortcode image.
	 * @return bool              Display the result or not.
	 * @return string            The URL of shortcode image.
	 */
	public function shortcode_image( $shortcode, $size, $echo = true ) {

		if ( isset( $shortcode['image'] ) ) {
			$image = $shortcode['image'];
		}
		elseif ( isset( $shortcode['icon'] ) ) {
			$image = $shortcode['icon'];
		}
		else {
			$image = $this->plugin_url . 'admin/images/shortcodes/_default.svg';
		}

		$font_size = $size - 20;

		// <img> tag
		if ( strpos( $image, '/' ) !== false ) {
			$template = '<img src="%1$s" alt="" width="%2$s" height="%2$s">';
		}

		// <i> FontAwesome tag
		else {
			$template = '
				<i class="sui sui-%1$s" style="
					display: block;
					width: %2$spx;
					height: %2$spx;
					line-height: %2$spx;
					font-size: %3$spx;
					text-align: center;
					color: #888;
				"></i>';
		}

		if ( $echo ) {
			printf( $template, $image, $size, $font_size );
		}
		else {
			return sprintf( $template, $image, $size, $font_size );
		}

	}

	/**
	 * Remove unwanted markers from shortcode description.
	 *
	 * @since  5.0.0
	 * @param string  $description Original shortcode description.
	 * @return string              Clean shortcode description.
	 */
	public function get_shortcode_description( $description ) {
		return str_replace( array( '<b%value>', '</b>', '%su_skins_link%' ), '', $description );
	}

	/**
	 * Filter to add action links at plugins screen.
	 *
	 * @since 5.0.8
	 * @param array $links Default links.
	 */
	public function add_action_links( $links ) {

		$plugin_links = array(
			sprintf(
				'<a href="%s">%s</a>',
				esc_attr( $this->get_component_url() ),
				esc_html( __( 'Shortcodes', 'shortcodes-ultimate' ) )
			),
		);

		return array_merge( $plugin_links, $links );

	}

	/**
	 * Helper to format output of implode function.
	 *
	 * Applies custom format to the every item of the array.
	 * Allows to output array keys as well as values.
	 *
	 * @since   5.0.0
	 *
	 * @param string  $glue    Glue for pieces.
	 * @param array   $pieces  Array with pieces.
	 * @param string  $pattern String pattern (%1$s will be replaced with item key, %2$s will be replaced with item value).
	 * @return  string         "Imploded" string.
	 */
	public function implodef( $glue, $pieces, $pattern ) {

		foreach ( $pieces as $key => $value ) {
			$pieces[$key] = sprintf( $pattern, $key, $value );
		}

		return implode( $glue, $pieces );

	}

}
