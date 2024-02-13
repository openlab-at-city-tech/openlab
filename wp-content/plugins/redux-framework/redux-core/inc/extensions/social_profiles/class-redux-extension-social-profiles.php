<?php
/**
 * Redux Social Profiles Extension Class
 *
 * @package Redux
 * @author  Kevin Provance <kevin.provance@gmail.com>
 * @class   Redux_Extension_Social_Profiles
 * @version 4.3.17
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Extension_Social_Profiles' ) ) {


	/**
	 * Main ReduxFramework social profiles extension class
	 *
	 * @since       1.0.0
	 */
	class Redux_Extension_Social_Profiles extends Redux_Extension_Abstract {

		/**
		 * Extension version.
		 *
		 * @var string
		 */
		public static $version = '4.3.17';

		/**
		 * Extension friendly name.
		 *
		 * @var string
		 */
		public $extension_name = 'Social Profiles';

		/**
		 * Field ID.
		 *
		 * @var mixed|null
		 */
		private $field_id = null;

		/**
		 * Field array.
		 *
		 * @var array|mixed
		 */
		public $field = array();

		/**
		 * Panel opt_name.
		 *
		 * @var string
		 */
		public $opt_name = '';

		/**
		 * Class Constructor. Defines the args for the extensions class
		 *
		 * @since       1.0.0
		 * @access      public
		 *
		 * @param       ReduxFramework $redux Parent settings.
		 *
		 * @return      void
		 */
		public function __construct( $redux ) {
			parent::__construct( $redux, __FILE__ );

			$this->add_field( 'social_profiles' );

			require_once __DIR__ . '/redux-social-profiles-helpers.php';

			include_once 'social_profiles/inc/class-redux-social-profiles-defaults.php';
			include_once 'social_profiles/inc/class-redux-social-profiles-functions.php';

			Redux_Social_Profiles_Functions::init( $redux );

			$this->field = Redux_Social_Profiles_Functions::get_field( $redux );

			if ( ! is_array( $this->field ) ) {
				return;
			}

			$this->field_id = $this->field['id'];
			$this->opt_name = $redux->args['opt_name'];

			$upload_dir = Redux_Social_Profiles_Functions::$upload_dir;

			if ( ! is_dir( $upload_dir ) ) {
				$redux->filesystem->execute( 'mkdir', $upload_dir );
			}

			if ( ! class_exists( 'Redux_Social_Profiles_Widget' ) ) {
				$enable = apply_filters( 'redux/extensions/social_profiles/' . $this->opt_name . '/widget/enable', true ); // phpcs:ignore WordPress.NamingConventions.ValidHookName

				if ( $enable ) {
					include_once 'social_profiles/inc/class-redux-social-profiles-widget.php';
					new Redux_Social_Profiles_Widget( $redux, $this->field_id );
				}
			}

			if ( ! class_exists( 'Redux_Social_Profiles_Shortcode' ) ) {
				$enable = apply_filters( 'redux/extensions/social_profiles/' . $this->opt_name . '/shortcode/enable', true ); // phpcs:ignore WordPress.NamingConventions.ValidHookName

				if ( $enable ) {
					include_once 'social_profiles/inc/class-redux-social-profiles-shortcode.php';
					new Redux_Social_Profiles_Shortcode( $redux, $this->field_id );
				}
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			add_filter( "redux/options/{$this->parent->args['opt_name']}/defaults", array( $this, 'set_defaults' ) );
			add_action( 'redux/validate/' . $this->parent->args['opt_name'] . '/before_validation', array( $this, 'save_me' ), 0, 3 );
			add_filter( 'redux/metaboxes/save/before_validate', array( $this, 'save_me' ), 0, 3 );

			// Reset hooks.
			add_action( 'redux/validate/' . $this->parent->args['opt_name'] . '/defaults', array( $this, 'reset_defaults' ), 0, 3 );
			add_action( 'redux/validate/' . $this->parent->args['opt_name'] . '/defaults_section', array( $this, 'reset_defaults_section' ), 0, 3 );
		}

		/**
		 * Reset section defaults.
		 *
		 * @param array $defaults Default values.
		 *
		 * @return array
		 */
		public function reset_defaults_section( array $defaults = array() ): array {
			if ( isset( $_COOKIE[ 'redux_current_tab_' . $this->parent->args['opt_name'] ] ) ) {
				$cur_tab = sanitize_title( wp_unslash( $_COOKIE[ 'redux_current_tab_' . $this->parent->args['opt_name'] ] ) );
				$tab_num = strval( $this->parent->field_sections['social_profiles'][ $this->field_id ] );

				if ( $cur_tab === $tab_num ) {
					if ( '' !== $this->field_id && isset( $this->parent->options_defaults[ $this->field_id ] ) ) {
						$data = Redux_Social_Profiles_Functions::get_default_data();

						Redux_Social_Profiles_Functions::write_data_file( $data );
					}
				}

				$defaults[ $this->field_id ] = Redux_Social_Profiles_Functions::read_data_file();
			}

			return $defaults;
		}

		/**
		 * Reset defaults.
		 *
		 * @param array $defaults Default values.
		 *
		 * @return array
		 */
		public function reset_defaults( array $defaults = array() ): array {
			if ( '' !== $this->field_id && isset( $this->parent->options_defaults[ $this->field_id ] ) ) {
				$data = Redux_Social_Profiles_Functions::get_default_data();

				Redux_Social_Profiles_Functions::write_data_file( $data );

				$defaults[ $this->field_id ] = $data;
			}

			return $defaults;
		}

		/**
		 * Set default values.
		 *
		 * @param array $defaults Default values.
		 *
		 * @return array
		 */
		public function set_defaults( array $defaults = array() ): array {
			if ( empty( $this->field_id ) ) {
				return $defaults;
			}

			$comp_file = Redux_Social_Profiles_Functions::get_data_path();

			if ( ! file_exists( $comp_file ) ) {
				$data = Redux_Social_Profiles_Functions::get_default_data();

				Redux_Social_Profiles_Functions::write_data_file( $data );

				$this->parent->options[ $this->field_id ] = $data;
			}

			return $defaults;
		}

		/**
		 * Save Data.
		 *
		 * @param array $saved_options  Saved options.
		 * @param array $changed_values Changed values.
		 *
		 * @return array
		 */
		public function save_me( array $saved_options = array(), array $changed_values = array() ): array {
			if ( empty( $this->field ) ) {
				$this->field    = Redux_Social_Profiles_Functions::get_field();
				$this->field_id = $this->field['id'];
			}

			if ( ! isset( $saved_options[ $this->field_id ] ) || empty( $saved_options[ $this->field_id ] ) || ( is_array( $saved_options[ $this->field_id ] ) && $saved_options === $changed_values ) || ! array_key_exists( $this->field_id, $saved_options ) ) {
				return $saved_options;
			}

			// We'll use the reset hook instead.
			if ( ! empty( $saved_options['defaults'] ) || ! empty( $saved_options['defaults-section'] ) ) {
				return $saved_options;
			}

			$first_value = reset( $saved_options[ $this->field_id ] ); // First Element's Value.

			if ( isset( $first_value['data'] ) ) {
				$raw_data = $saved_options[ $this->field_id ];

				$save_data = array();

				// Enum through saved data.
				foreach ( $raw_data as $val ) {
					if ( is_array( $val ) ) {

						if ( ! isset( $val['data'] ) ) {
							return array();
						}

						$data = json_decode( rawurldecode( $val['data'] ), true );

						$save_data[] = array(
							'id'         => $data['id'],
							'icon'       => $data['icon'],
							'enabled'    => $data['enabled'],
							'url'        => $data['url'],
							'color'      => $data['color'],
							'background' => $data['background'],
							'order'      => $data['order'],
							'name'       => $data['name'],
							'label'      => $data['label'],
						);
					}
				}

				$save_file = false;

				if ( ! isset( $old_options[ $this->field_id ] ) || ( isset( $old_options[ $this->field_id ] ) && ! empty( $old_options[ $this->field_id ] ) ) ) {
					$save_file = true;
				}

				if ( ! empty( $old_options[ $this->field_id ] ) && $old_options[ $this->field_id ] !== $saved_options[ $this->field_id ] ) {
					$save_file = true;
				}

				if ( $save_file ) {
					Redux_Social_Profiles_Functions::write_data_file( $save_data );
				}

				$saved_options[ $this->field_id ] = $save_data;
			}

			return $saved_options;
		}

		/**
		 * Enqueue scripts/styles.
		 */
		public function enqueue_styles() {
			// Field CSS.
			wp_enqueue_style(
				'redux-field-social-profiles-frontend',
				$this->extension_url . 'social_profiles/css/field_social_profiles_frontend.css',
				array(),
				self::$version
			);
		}
	}
}

class_alias( 'Redux_Extension_Social_Profiles', 'ReduxFramework_Extension_social_profiles' );
