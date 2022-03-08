<?php
// phpcs:ignoreFile -- legacy code in need of refactoring.
/*
Plugin Name: Taxonomy Dropdown Widget
Plugin URI: https://ethitter.com/plugins/taxonomy-dropdown-widget/
Description: Creates a dropdown list of non-hierarchical taxonomies as an alternative to the term (tag) cloud. Widget provides numerous options to tailor the output to fit your site. Dropdown function can also be called directly for use outside of the widget. Formerly known as <strong><em>Tag Dropdown Widget</em></strong>.
Author: Erick Hitter
Version: 2.3.1
Author URI: https://ethitter.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 ** TAXONOMY DROPDOWN WIDGET PLUGIN
 **/
class taxonomy_dropdown_widget_plugin {
	/**
	 * Singleton!
	 */
	private static $__instance = null;

	/**
	 * Class variables
	 */
	protected $option_defaults = array(
		'taxonomy'        => 'post_tag',
		'select_name'     => 'Select Tag',
		'max_name_length' => 0,
		'cutoff'          => '&hellip;',
		'limit'           => 0,
		'order'           => 'ASC',
		'orderby'         => 'name',
		'threshold'       => 0,
		'incexc'          => 'exclude',
		'incexc_ids'      => array(),
		'hide_empty'      => true,
		'post_counts'     => false,
	);

	/**
	 * Implement the singleton
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! is_a( self::$__instance, __CLASS__ ) ) {
			self::$__instance = new self;

			self::$__instance->setup();
		}

		return self::$__instance;
	}

	/**
	 * Silence is golden!
	 */
	private function __construct() {}

	/**
	 * Register actions and activation/deactivation hooks
	 * @uses add_action
	 * @uses register_activation_hook
	 * @uses register_deactivation_hook
	 * @return null
	 */
	protected function setup() {
		add_action( 'widgets_init', array( $this, 'action_widgets_init' ) );

		register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation_hook' ) );

		add_action( 'split_shared_term', array( $this, 'action_split_shared_term' ), 10, 4 );
	}

	/**
	 * Allow access to certain variables that were previously public
	 *
	 * @return mixed
	 */
	public function __get( $var ) {
		if ( 'option_defaults' == $var ) {
			return $this->option_defaults;
		}

		return null;
	}

	/**
	 * Run plugin cleanup on activation
	 * @uses this::cleanup
	 * @hook activation
	 * @return null
	 */
	public function activation_hook() {
		$this->cleanup();
	}

	/**
	 * Unregister widget when plugin is deactivated and run cleanup
	 * @uses this::cleanup
	 * @hook deactivation
	 * @return null
	 */
	public function deactivation_hook() {
		$this->cleanup();
	}

	/**
	 * Remove options related to plugin versions older than 2.0.
	 * @uses apply_filters
	 * @uses delete_option
	 * @return null
	 */
	private function cleanup() {
		if ( ! apply_filters( 'taxonomy_dropdown_widget_run_cleanup', true ) ) {
			return;
		}

		// Remove unused options
		$legacy_options = array(
			'widget_TagDropdown',
			'widget_TagDropdown_exclude',
			'function_TagDropdown',
			'TDW_direct',
		);

		foreach ( $legacy_options as $legacy_option ) {
			delete_option( $legacy_option );
		}

		// Let others play too
		do_action( 'taxonomy_dropdown_widget_cleanup' );
	}

	/**
	 * Register widget
	 * @uses register_widget
	 * @action widgets_init
	 * @return null
	 */
	public function action_widgets_init() {
		register_widget( 'taxonomy_dropdown_widget' );
	}

	/**
	 * Update widget options when terms are split
	 *
	 * Starting in WP 4.2, terms that were previously shared will now be split into their own terms when the terms are updated.
	 * To ensure the widget continues to include/exclude the updated terms, we search widget options on terms split and update stored IDs.
	 *
	 * @param int    $old_id   ID of shared term before the split
	 * @param int    $new_id   ID of new term created after the split
	 * @param int    $tt_id    Term taxonomy ID of split term
	 * @param string $taxonomy Taxonomy of the term being split from its shared entry
	 * @action split_shared_term
	 * @return null
	 */
	public function action_split_shared_term( $old_id, $new_id, $tt_id, $taxonomy ) {
		// WP provides no utility function for getting widget options, so we go straight to the source
		$all_widget_options = $_all_widget_options = get_option( 'widget_taxonomy_dropdown_widget', false );

		// Loop through each widget's options and update stored term IDs if they're being split here
		if ( is_array( $all_widget_options ) && ! empty( $all_widget_options ) ) {
			foreach ( $all_widget_options as $key => $options ) {
				// Check if widget needs updating
				if ( ! is_array( $options ) ) {
					continue;
				}

				if ( $options['taxonomy'] !== $taxonomy ) {
					continue;
				}

				if ( empty( $options['incexc_ids'] ) ) {
					continue;
				}

				// Account for legacy data storage option
				if ( is_string( $options['incexc_ids'] ) ) {
					$options['incexc_ids'] = explode( ',', $options['incexc_ids'] );
					$options['incexc_ids'] = array_map( 'absint', $options['incexc_ids'] );
					$options['incexc_ids'] = array_filter( $options['incexc_ids'] );
				}

				// Find stored term to update and do so
				$key_to_update = array_search( $old_id, $options['incexc_ids'] );

				if ( false === $key_to_update ) {
					continue;
				} else {
					$all_widget_options[ $key ]['incexc_ids'][ $key_to_update ] = $new_id;
				}
			}
		}

		// If the term split was one in a widget option, update the options
		// Reduces `update_option()` calls if nothing's changed
		if ( $all_widget_options !== $_all_widget_options ) {
			update_option( 'widget_taxonomy_dropdown_widget', $all_widget_options );
		}
	}

	/**
	 * Render widget
	 *
	 * @param array $options
	 * @param string|int $id
	 * @uses wp_parse_args
	 * @uses this::sanitize_options
	 * @uses sanitize_title
	 * @uses apply_filters
	 * @uses get_terms
	 * @uses esc_attr
	 * @uses esc_html
	 * @uses is_tag
	 * @uses is_tax
	 * @uses esc_url
	 * @uses get_term_link
	 * @uses selected
	 * @return string or false
	 */
	public function render_dropdown( $options, $id = false ) {
		$options = wp_parse_args( $options, $this->option_defaults );
		$options = $this->sanitize_options( $options );

		$id = is_numeric( $id ) ? intval( $id ) : sanitize_title( $id );

		// Set up options array for get_terms
		$terms_options = array(
			'order'        => $options['order'],
			'orderby'      => $options['orderby'],
			'hide_empty'   => $options['hide_empty'],
			'hierarchical' => false,
		);

		if ( $options['limit'] ) {
			$terms_options[ 'number' ] = $options['limit'];
		}

		if ( ! empty( $options['incexc_ids'] ) ) {
			$terms_options[ $options['incexc'] ] = $options['incexc_ids'];
		}

		$terms_options = apply_filters( 'taxonomy_dropdown_widget_options', $terms_options, $id );
		$terms_options = apply_filters( 'TagDropdown_get_tags', $terms_options );

		// Get terms
		$terms = get_terms( $options['taxonomy'], $terms_options );

		if ( is_array( $terms ) && ! empty( $terms ) ) {
			// Determine CSS ID
			if ( is_int( $id ) ) {
				$css_id = ' id="taxonomy_dropdown_widget_dropdown_' . $id . '"';
			} elseif ( ! empty( $id ) ) {
				$css_id = ' id="' . esc_attr( $id ) . '"';
			} else {
				$css_id = '';
			}

			// Start dropdown
			$output = '<select name="taxonomy_dropdown_widget_dropdown_' . esc_attr( $id ) . '" class="taxonomy_dropdown_widget_dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"' . $css_id . '>' . "\r\n";

			$default = apply_filters( 'taxonomy_dropdown_widget_dropdown_default_item', '<option value="">' . esc_html( $options['select_name'] ) . '</option>', $id, $options, $terms_options, $terms );
			if ( ! empty( $default ) ) {
				$output .= "\t" . $default . "\r\n";
			}

			// Populate dropdown
			$i = 1;
			foreach ( $terms as $term ) {
				if ( $options['threshold'] > 0 && $term->count < $options['threshold'] ) {
					continue;
				}

				// Set selected attribute if on an archive page for the current term
				$current = is_tag() ? is_tag( $term->slug ) : is_tax( $term->taxonomy, $term->slug );

				// Open option tag
				$item = '<option value="' . esc_url( get_term_link( (int) $term->term_id, $term->taxonomy ) ) . '"' . ( selected( $current, true, false ) ) . '>';

				// Tag name
				$name = esc_attr( $term->name );
				if ( $options['max_name_length'] > 0 && strlen( $name ) > $options['max_name_length'] ) {
					$name = substr( $name, 0, $options['max_name_length'] ) . $options['cutoff'];
				}
				$item .= $name;

				// Count
				if ( $options['post_counts'] ) {
					$item .= ' (' . intval( $term->count ) . ')';
				}

				// Close option tag
				$item .= '</option>';

				$item = apply_filters( 'taxonomy_dropdown_widget_dropdown_single_item', $item, $term, $id, $options, $terms_options, $terms );
				if ( ! empty( $item ) ) {
					$output .= "\t" . $item . "\r\n";
				}

				$i++;
			}

			// End dropdown
			$output .= '</select>' . "\r\n";
			$output = apply_filters( 'taxonomy_dropdown_widget_dropdown', $output, $id, $options, $terms_options, $terms );

			// Depending on size of list, `$terms` could be quite large.
			unset( $terms );

			return $output;
		} else {
			return false;
		}
	}

	/**
	 * Sanitize plugin options
	 * @param array|string $options
	 * @uses taxonomy_exists
	 * @uses sanitize_text_field
	 * @uses absint
	 * @uses wp_parse_args
	 * @return array
	 */
	public function sanitize_options( $options ) {
		// WP supports strings for arguments in place of arrays.
		// Match the expectation, though arrays are preferred.
		if ( is_string( $options ) ) {
			wp_parse_str( $options, $options );
		}

		$options_sanitized = array(
			'hide_empty'  => true,
			'post_counts' => false,
		);

		$keys = array_merge( array_keys( $this->option_defaults ), array( 'title' ) );

		if ( is_array( $options ) ) {
			foreach ( $keys as $key ) {
				if ( ! array_key_exists( $key, $options ) ) {
					continue;
				}

				$value = $options[ $key ];

				switch( $key ) {
					case 'taxonomy' :
						if ( taxonomy_exists( $value ) ) {
							$options_sanitized[ $key ] = $value;
						}
					break;

					case 'title' :
					case 'select_name' :
					case 'cutoff' :
						$value = sanitize_text_field( $value );

						if ( ! empty( $value ) || $key == 'title' ) {
							$options_sanitized[ $key ] = $value;
						}
					break;

					case 'max_name_length' :
					case 'limit' :
					case 'threshold' :
						$options_sanitized[ $key ] = absint( $value );
					break;

					case 'order' :
						if ( $value == 'ASC' || $value == 'DESC' ) {
							$options_sanitized[ $key ] = $value;
						}
					break;

					case 'orderby' :
						if ( $value == 'name' || $value == 'count' ) {
							$options_sanitized[ $key ] = $value;
						}
					break;

					case 'incexc' :
						if ( $value == 'include' || $value == 'exclude' ) {
							$options_sanitized[ $key ] = $value;
						}
					break;

					case 'incexc_ids' :
						$options_sanitized[ $key ] = array();

						if ( is_string( $value ) ) {
							$value = explode( ',', $value );
						}

						if ( is_array( $value ) ) {
							foreach ( $value as $term_id ) {
								$term_id = intval( $term_id );

								if ( $term_id > 0 ) {
									$options_sanitized[ $key ][] = $term_id;
								}

								unset( $term_id );
							}

							sort( $options_sanitized[ $key ], SORT_NUMERIC );
						}
					break;

					case 'hide_empty' :
					case 'post_counts' :
						$options_sanitized[ $key ] = (bool)$value;
					break;

					default :
					break;
				}
			}
		}

		// Ensure array contains all keys by parsing against defaults after options are sanitized
		$options_sanitized = wp_parse_args( $options_sanitized, $this->option_defaults );

		return $options_sanitized;
	}
}

// Prior to introduction of singleton, plugin was instantiated in a global. Continuing to do so improves backwards compatibility.
$GLOBALS['taxonomy_dropdown_widget_plugin'] = taxonomy_dropdown_widget_plugin::get_instance();

/**
 ** TAXONOMY DROPDOWN WIDGET
 **/
class taxonomy_dropdown_widget extends WP_Widget {
	/**
	 * Class variables
	 */
	private $defaults = array(
		'title' => 'Tags',
	);

	private $plugin = null;

	/**
	 * Register widget and populate class variables
	 * @uses parent::__construct()
	 * @uses taxonomy_dropdown_widget_plugin::get_instance
	 * @return null
	 */
	public function __construct() {
		parent::__construct( false, 'Taxonomy Dropdown Widget', array( 'description' => 'Displays selected non-hierarchical taxonomy terms in a dropdown list.' ) );

		// Shortcut to the main plugin instance from within the widget class
		$this->plugin = taxonomy_dropdown_widget_plugin::get_instance();

		// Load plugin class and populate defaults
		if ( is_object( $this->plugin ) && is_array( $this->plugin->option_defaults ) ) {
			$this->defaults = array_merge( $this->plugin->option_defaults, $this->defaults );
		}
	}

	/**
	 * Render widget
	 * @param array $args
	 * @param array $instance
	 * @uses wp_parse_args
	 * @uses apply_filters
	 * @return string or null
	 */
	public function widget( $args, $instance ) {
		// Options
		$instance = wp_parse_args( $instance, $this->defaults );

		// Widget
		if ( $widget = $this->plugin->render_dropdown( $instance, $this->number ) ) {
			// Wrapper and title
			$output = $args['before_widget'] . "\r\n";

			if ( ! empty( $instance['title'] ) ) {
				$output .= $args['before_title'] . apply_filters( 'taxonomy_dropdown_widget_title', '<label for="taxonomy_dropdown_widget_dropdown_' . $this->number . '">' . $instance['title'] . '</label>', $this->number ) . $args['after_title'] . "\r\n";
			}

			// Widget
			$output .= $widget . "\r\n";

			// Wrapper
			$output .= $args['after_widget'] . "\r\n";

			echo $output;
		}
	}

	/**
	 * Options sanitization
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $this->plugin->sanitize_options( $new_instance );
	}

	/**
	 * Widget options
	 * @param array $instance
	 * @uses wp_parse_args
	 * @uses get_taxonomies
	 * @uses _e
	 * @uses this::get_field_id
	 * @uses this::get_field_name
	 * @uses esc_attr
	 * @uses selected
	 * @uses checked
	 * @return string
	 */
	public function form( $instance ) {
		// Get options
		$options = wp_parse_args( $instance, $this->defaults );

		// Get taxonomies and remove certain Core taxonomies that shouldn't be accessed directly.
		$taxonomies = get_taxonomies( array(
			'public'       => true,
			'hierarchical' => false,
		), 'objects' );

		if ( array_key_exists( 'nav_menu', $taxonomies ) ) {
			unset( $taxonomies[ 'nav_menu' ] );
		}

		if ( array_key_exists( 'post_format', $taxonomies ) ) {
			unset( $taxonomies[ 'post_format' ] );
		}

	?>
		<h3><?php _e( 'Basic Settings', 'taxonomy_dropdown_widget' ); ?></h3>

		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'taxonomy_dropdown_widget' ); ?>:</label><br />
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
				<?php foreach ( $taxonomies as $tax ) : ?>
					<option value="<?php echo esc_attr( $tax->name ); ?>"<?php selected( $tax->name, $options['taxonomy'], true ); ?>><?php echo $tax->labels->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat code" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo esc_attr( $options['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'select_name' ); ?>"><?php _e( 'Default dropdown item:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'select_name' ); ?>" class="widefat code" id="<?php echo $this->get_field_id( 'select_name' ); ?>" value="<?php echo esc_attr( $options['select_name'] ); ?>" />
		</p>

		<h3><?php _e( 'Order', 'taxonomy_dropdown_widget' ); ?></h3>

		<p>
			<label><?php _e( 'Order terms by:', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="name" id="<?php echo $this->get_field_name( 'order_name' ); ?>"<?php checked( $options['orderby'], 'name', true ); ?> />
			<label for="<?php echo $this->get_field_name( 'order_name' ); ?>"><?php _e( 'Name', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'orderby' ); ?>" value="count" id="<?php echo $this->get_field_name( 'order_count' ); ?>"<?php checked( $options['orderby'], 'count', true ); ?> />
			<label for="<?php echo $this->get_field_name( 'order_count' ); ?>"><?php _e( 'Post count', 'taxonomy_dropdown_widget' ); ?></label>
		</p>

		<p>
			<label><?php _e( 'Order terms:', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'order' ); ?>" value="ASC" id="<?php echo $this->get_field_name( 'order_asc' ); ?>"<?php checked( $options['order'], 'ASC', true ); ?> />
			<label for="<?php echo $this->get_field_name( 'order_asc' ); ?>"><?php _e( 'Ascending', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'order' ); ?>" value="DESC" id="<?php echo $this->get_field_name( 'order_desc' ); ?>"<?php checked( $options['order'], 'DESC', true ); ?> />
			<label for="<?php echo $this->get_field_name( 'order_desc' ); ?>"><?php _e( 'Descending', 'taxonomy_dropdown_widget' ); ?></label>
		</p>

		<h3><?php _e( 'Term Display', 'taxonomy_dropdown_widget' ); ?></h3>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit number of terms shown to:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo $this->get_field_id( 'limit' ); ?>" value="<?php echo intval( $options['limit'] ); ?>" size="3" /><br />
			<span class="description"><small><?php _e( 'Enter <strong>0</strong> for no limit.', 'taxonomy_dropdown_widget' ); ?></small></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'max_name_length' ); ?>"><?php _e( 'Trim long term names to <em>x</em> characters:</label>', 'taxonomy_dropdown_widget' ); ?><br />
			<input type="text" name="<?php echo $this->get_field_name( 'max_name_length' ); ?>" id="<?php echo $this->get_field_id( 'max_name_length' ); ?>" value="<?php echo intval( $options['max_name_length'] ); ?>" size="3" /><br />
			<span class="description"><small><?php _e( 'Enter <strong>0</strong> to show full tag names.', 'taxonomy_dropdown_widget' ); ?></small></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cutoff' ); ?>"><?php _e( 'Indicator that term names are trimmed:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'cutoff' ); ?>" id="<?php echo $this->get_field_id( 'cutoff' ); ?>" value="<?php echo esc_attr( $options['cutoff'] ); ?>" size="3" /><br />
			<span class="description"><small><?php _e( 'Leave blank to use an elipsis (&hellip;).', 'taxonomy_dropdown_widget' ); ?></small></span>
		</p>

		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>"  value="0"<?php checked( false, $options['hide_empty'], true ); ?> />
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e( 'Include terms that aren\'t assigned to any objects (empty terms).', 'taxonomy_dropdown_widget' ); ?></label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'post_counts' ); ?>" id="<?php echo $this->get_field_id( 'post_counts' ); ?>"  value="1"<?php checked( true, $options['post_counts'], true ); ?> />
			<label for="<?php echo $this->get_field_id( 'post_counts' ); ?>"><?php _e( 'Display object (post) counts after term names.', 'taxonomy_dropdown_widget' ); ?></label>
		</p>

		<h3><?php _e( 'Include/Exclude Terms', 'taxonomy_dropdown_widget' ); ?></h3>

		<p>
			<label><?php _e( 'Include/exclude terms:', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'incexc' ); ?>" value="include" id="<?php echo $this->get_field_id( 'include' ); ?>"<?php checked( $options['incexc'], 'include', true ); ?> />
			<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e( 'Include only the term IDs listed below', 'taxonomy_dropdown_widget' ); ?></label><br />

			<input type="radio" name="<?php echo $this->get_field_name( 'incexc' ); ?>" value="exclude" id="<?php echo $this->get_field_id( 'exclude' ); ?>"<?php checked( $options['incexc'], 'exclude', true ); ?> />
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude the term IDs listed below', 'taxonomy_dropdown_widget' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'incexc_ids' ); ?>"><?php _e( 'Term IDs to include/exclude based on above setting:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'incexc_ids' ); ?>" class="widefat code" id="<?php echo $this->get_field_id( 'incexc_ids' ); ?>" value="<?php echo esc_attr( implode( ', ', $options['incexc_ids'] ) ); ?>" /><br />
			<span class="description"><small><?php _e( 'Enter comma-separated list of term IDs.', 'taxonomy_dropdown_widget' ); ?></small></span>
		</p>

		<h3><?php _e( 'Advanced', 'taxonomy_dropdown_widget' ); ?></h3>

		<p>
			<label for="<?php echo $this->get_field_id( 'threshold' ); ?>"><?php _e( 'Show terms assigned to at least this many posts:', 'taxonomy_dropdown_widget' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'threshold' ); ?>" id="<?php echo $this->get_field_id( 'threshold' ); ?>" value="<?php echo intval( $options['threshold'] ); ?>" size="3" /><br />
			<span class="description"><small><?php _e( 'Set to <strong>0</strong> to display all terms matching the above criteria.', 'taxonomy_dropdown_widget' ); ?></small></span>
		</p>

	<?php
	}
}

/**
 ** HELPER FUNCTIONS
 **/

/**
 * Render taxonomy dropdown
 * @param array $options
 * @param string|int $id
 * @uses taxonomy_dropdown_widget_plugin::get_instance
 * @return string or false
 */
function taxonomy_dropdown_widget( $options = array(), $id = '' ) {
	// Sanitize options
	$options = taxonomy_dropdown_widget_plugin::get_instance()->sanitize_options( $options );

	return taxonomy_dropdown_widget_plugin::get_instance()->render_dropdown( $options, $id );
}

/**
 ** LEGACY FUNCTIONS FOR BACKWARDS COMPATIBILITY
 **/

if ( ! function_exists( 'generateTagDropdown' ) ) :
	/**
	 * Build tag dropdown based on provided arguments
	 * @since 1.7
	 * @uses _deprecated_function
	 * @uses taxonomy_dropdown_widget_plugin::get_instance
	 * @return string or false
	 */
	function generateTagDropdown( $args ) {
		_deprecated_function( 'generateTagDropdown', '2.0 of Taxonomy (Tag) Dropdown Widget', 'taxonomy_dropdown_widget' );

		// Sanitize options
		$options = taxonomy_dropdown_widget_plugin::get_instance()->sanitize_options( $args );

		return '<!-- NOTICE: The function used to generate this dropdown list is deprecated as of version 2.0 of Taxonomy Dropdown Widget. You should update your template to use `taxonomy_dropdown_widget()` instead. -->' . "\r\n" . taxonomy_dropdown_widget_plugin::get_instance()->render_dropdown( $options, 'legacy_gtd' );
	}
endif;

if ( ! function_exists( 'TDW_direct' ) ) :
	/**
	 * Build tag dropdown based on provided arguments
	 * @since 1.6
	 * @uses _deprecated_function
	 * @uses taxonomy_dropdown_widget_plugin::get_instance
	 * @return string or false
	 */
	function TDW_direct( $limit = false, $count = false, $exclude = false ) {
		_deprecated_function( 'TDW_direct', '1.7 of Taxonomy (Tag) Dropdown Widget', 'taxonomy_dropdown_widget' );

		// Build options array from function parameters
		$options = array(
			'max_name_length' => $limit,
			'post_count'      => $count,
		);

		if ( $exclude ) {
			$options[ 'incexc' ]     = 'exclude';
			$options[ 'incexc_ids' ] = $exclude;
		}

		// Sanitize options
		$options = taxonomy_dropdown_widget_plugin::get_instance()->sanitize_options( $options );

		echo '<!-- NOTICE: The function used to generate this dropdown list is deprecated as of version 1.7 of Taxonomy Dropdown Widget. You should update your template to use `taxonomy_dropdown_widget()` instead. -->' . "\r\n" . taxonomy_dropdown_widget_plugin::get_instance()->render_dropdown( $options, 'legacy_tdw' );
	}
endif;

if ( ! function_exists( 'makeTagDropdown' ) ) :
	/**
	 * Build tag dropdown based on provided arguments
	 * @since 1.3
	 * @uses _deprecated_function
	 * @uses taxonomy_dropdown_widget_plugin::get_instance
	 * @return string or false
	 */
	function makeTagDropdown( $limit = false ) {
		_deprecated_function( 'makeTagDropdown', '1.6 of Taxonomy (Tag) Dropdown Widget', 'taxonomy_dropdown_widget' );

		// Sanitize options
		$options = array(
			'max_name_length' => intval( $limit ),
		);

		echo '<!-- NOTICE: The function used to generate this dropdown list is deprecated as of version 1.6 of Taxonomy Dropdown Widget. You should update your template to use `taxonomy_dropdown_widget()` instead. -->' . "\r\n" . taxonomy_dropdown_widget_plugin::get_instance()->render_dropdown( $options, 'legacy_mtd' );
	}
endif;
