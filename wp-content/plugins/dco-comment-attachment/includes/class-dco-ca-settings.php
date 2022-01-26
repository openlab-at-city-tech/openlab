<?php
/**
 * Plugin settings: DCO_CA_Settings class
 *
 * @package DCO_Comment_Attachment
 * @author Denis Yanchevskiy
 * @copyright 2019
 * @license GPLv2+
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Class with plugin settings.
 *
 * @since 1.0.0
 *
 * @see DCO_CA_Base
 */
class DCO_CA_Settings extends DCO_CA_Base {

	/**
	 * The plugin options ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id The plugin options ID.
	 */
	const ID = 'dco_ca';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ) );
	}

	/**
	 * Initializes hooks.
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		parent::init_hooks();

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'create_menu' ) );
	}

	/**
	 * Registers plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting( self::ID, self::ID );

		$sections = $this->get_sections();
		foreach ( $sections as $key => $title ) {
			add_settings_section(
				$key,
				$title,
				array( $this, 'section_render' ),
				self::ID
			);
		}

		$fields = $this->get_fields();
		foreach ( $fields as $key => $field ) {
			$args = array(
				'label_for' => $key,
				'name'      => $key,
				'desc'      => $field['desc'],
				'type'      => $field['type'],
			);

			if ( isset( $field['choices'] ) ) {
				$args['choices'] = $field['choices'];
			}

			if ( isset( $field['label_for'] ) && ! $field['label_for'] ) {
				unset( $args['label_for'] );
			}

			add_settings_field(
				$key,
				$field['label'],
				array( $this, 'field_render' ),
				self::ID,
				$field['section'],
				$args
			);
		}
	}

	/**
	 * Adds an options page to the settings section in the admin menu.
	 *
	 * @since 1.0.0
	 */
	public function create_menu() {
		add_options_page( __( 'DCO Comment Attachment Settings', 'dco-comment-attachment' ), esc_html__( 'DCO Comment Attachment', 'dco-comment-attachment' ), 'manage_options', 'dco-comment-attachment', array( $this, 'render' ) );
	}

	/**
	 * Outputs the plugin settings page markup.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'DCO Comment Attachment Settings', 'dco-comment-attachment' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::ID );
				do_settings_sections( self::ID );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Gets plugin settings sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Settings sections.
	 */
	public function get_sections() {
		$sections = array(
			'general'         => esc_html__( 'General', 'dco-comment-attachment' ),
			'images'          => esc_html__( 'Images', 'dco-comment-attachment' ),
			'multiple_upload' => esc_html__( 'Multiple upload', 'dco-comment-attachment' ),
			'permissions'     => esc_html__( 'Permissions', 'dco-comment-attachment' ),
			'in_admin'        => esc_html__( 'Admin Panel', 'dco-comment-attachment' ),
		);

		return $sections;
	}

	/**
	 * Gets plugin settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Settings fields.
	 */
	public function get_fields() {
		$fields = array(
			'max_upload_size'          => array(
				'label'   => esc_html__( 'Maximum upload file size', 'dco-comment-attachment' ),
				/* translators: %s: the maximum allowed upload file size */
				'desc'    => sprintf( __( 'Set the value in megabytes. Currently your server allows you to upload files up to %s.', 'dco-comment-attachment' ), $this->get_max_upload_size( true, true ) ),
				'section' => 'general',
				'type'    => 'number',
				'default' => $this->get_max_upload_size( false, true ),
			),
			'required_attachment'      => array(
				'label'   => esc_html__( 'Is attachment required?', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, the user will not be able to post a comment without attaching an attachment.', 'dco-comment-attachment' ),
				'section' => 'general',
				'type'    => 'checkbox',
				'default' => 0,
			),
			'embed_attachment'         => array(
				'label'   => esc_html__( 'Embed attachment?', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, the attachment is displayed as an image, video, audio, or file link. Otherwise, all attachments will be displayed as links to files.', 'dco-comment-attachment' ),
				'section' => 'general',
				'type'    => 'checkbox',
				'default' => 1,
			),
			'autoembed_links'          => array(
				'label'   => esc_html__( 'Autoembed links in comment text?', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, links (like YouTube, Facebook, Twitter, etc.) in the comment text will be automatically turned into embedded content.', 'dco-comment-attachment' ),
				'section' => 'general',
				'type'    => 'checkbox',
				'default' => 1,
			),
			'enable_multiple_upload'   => array(
				'label'   => esc_html__( 'Enable multiple upload?', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, users will be able to upload multiple attachments at once.', 'dco-comment-attachment' ),
				'section' => 'multiple_upload',
				'type'    => 'checkbox',
				'default' => 0,
			),
			'combine_images'           => array(
				'label'   => esc_html__( 'Combine images to gallery?', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, attached images will be combined to a gallery. Otherwise, the images will be displayed as a list.', 'dco-comment-attachment' ),
				'section' => 'multiple_upload',
				'type'    => 'checkbox',
				'default' => 1,
			),
			'gallery_size'             => array(
				'label'   => esc_html__( 'Gallery image size', 'dco-comment-attachment' ),
				'desc'    => __( 'The size of the thumbnail for the gallery of attached images.', 'dco-comment-attachment' ),
				'section' => 'multiple_upload',
				'type'    => 'dropdown',
				'default' => 'thumbnail',
			),
			'thumbnail_size'           => array(
				'label'   => esc_html__( 'Attachment image size', 'dco-comment-attachment' ),
				'desc'    => __( 'The size of the thumbnail for attached images.', 'dco-comment-attachment' ),
				'section' => 'images',
				'type'    => 'dropdown',
				'default' => 'medium',
			),
			'link_thumbnail'           => array(
				'label'     => esc_html__( 'Link thumbnail?', 'dco-comment-attachment' ),
				'desc'      => '',
				'section'   => 'images',
				'type'      => 'radio',
				'default'   => 0,
				'choices'   => array(
					'0' => __( 'Not link', 'dco-comment-attachment' ),
					/* translators: %s: the link to the plugin FAQ section on WordPress.org */
					'1' => sprintf( __( 'Link to a full-size image with lightbox plugins support (see <a href="%s">FAQ</a> for details)', 'dco-comment-attachment' ), 'https://wordpress.org/plugins/dco-comment-attachment/#what%20lightbox%20plugins%20are%20supported%3F' ),
					'2' => __( 'Link to a full-size image in a new tab', 'dco-comment-attachment' ),
					'3' => __( 'Link to the attachment page', 'dco-comment-attachment' ),
				),
				'label_for' => false,
			),
			'allowed_file_types'       => array(
				'label'   => esc_html__( 'Allowed File Types', 'dco-comment-attachment' ),
				'desc'    => '* — ' . __( 'available for embedding.', 'dco-comment-attachment' ) . '<br>** — ' . __( 'allowed only for Administrators and Editors.', 'dco-comment-attachment' ),
				'section' => 'permissions',
				'type'    => 'checkbox',
				'default' => $this->get_allowed_file_types( 'array' ),
			),
			'who_can_upload'           => array(
				'label'     => esc_html__( 'Who can upload attachment?', 'dco-comment-attachment' ),
				'desc'      => '',
				'section'   => 'permissions',
				'type'      => 'radio',
				'default'   => 1,
				'choices'   => array(
					'1' => __( 'All users', 'dco-comment-attachment' ),
					'2' => __( 'Only logged users', 'dco-comment-attachment' ),
				),
				'label_for' => false,
			),
			'manually_moderation'      => array(
				'label'   => esc_html__( 'Manually moderate comments with attachments', 'dco-comment-attachment' ),
				'desc'    => __( 'If checked, all comments with attachments must be manually approved before they appear on the site.', 'dco-comment-attachment' ),
				'section' => 'permissions',
				'type'    => 'checkbox',
				'default' => 0,
			),
			'delete_with_comment'      => array(
				'label'   => esc_html__( 'Delete attachment when comment is deleted?', 'dco-comment-attachment' ),
				'desc'    => __( 'If unchecked, the attachment will be available in Media Library after the comment has been deleted.', 'dco-comment-attachment' ),
				'section' => 'in_admin',
				'type'    => 'checkbox',
				'default' => 1,
			),
			'delete_attachment_action' => array(
				'label'     => esc_html__( 'Delete Attachment action on Edit Comments page', 'dco-comment-attachment' ),
				'desc'      => '',
				'section'   => 'in_admin',
				'type'      => 'radio',
				'default'   => 1,
				'choices'   => array(
					'1' => __( 'Delete attachment from Media Library', 'dco-comment-attachment' ),
					'0' => __( 'Unattach attachment from comment', 'dco-comment-attachment' ),
				),
				'label_for' => false,
			),
		);

		return $fields;
	}

	/**
	 * Outputs the settings section content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Section arguments.
	 */
	public function section_render( $args ) {
	}

	/**
	 * Outputs the setting fields markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field arguments.
	 */
	public function field_render( $args ) {
		$id           = self::ID;
		$name         = $args['name'];
		$setting_val  = $this->get_option( $name );
		$control_name = "{$id}[$name]";
		$control_id   = $name;

		switch ( $args['type'] ) {
			case 'number':
				if ( 'max_upload_size' === $args['name'] ) {
					$this->field_max_upload_size_render( $setting_val, $control_name, $control_id, $args );
				}
				break;
			case 'checkbox':
				if ( 'allowed_file_types' === $args['name'] ) {
					$this->field_allowed_file_types_render( $setting_val, $control_name, $control_id, $args );
				} else {
					$this->field_checkbox_render( $setting_val, $control_name, $control_id, $args );
				}
				break;
			case 'radio':
				$this->field_radio_render( $setting_val, $control_name, $control_id, $args );
				break;
			case 'dropdown':
				if ( 'thumbnail_size' === $args['name'] || 'gallery_size' === $args['name'] ) {
					$this->field_thumbnail_size_render( $setting_val, $control_name, $control_id, $args );
				}
				break;
		}
		if ( $args['desc'] ) {
			$allowed_tags = array( 'br' => array() );
			echo '<p class="description">' . wp_kses( $args['desc'], $allowed_tags ) . '</p>';
		}
	}

	/**
	 * Outputs the setting max_upload_size field markup.
	 *
	 * @since 1.0.0
	 *
	 * @param int|float $setting_val The setting value from DB.
	 * @param string    $control_name The name attribute for the setting field.
	 * @param string    $control_id The id attribute for the setting field.
	 * @param array     $args Field arguments.
	 */
	public function field_max_upload_size_render( $setting_val, $control_name, $control_id, $args ) {
		$max = $this->get_max_upload_size( false, true );
		echo '<input type="number" name="' . esc_attr( $control_name ) . '" class="regular-text" id="' . esc_attr( $control_id ) . '" value="' . esc_attr( $setting_val ) . '" min="1" max="' . esc_attr( $max ) . '">';
	}

	/**
	 * Outputs the setting checkbox field markup.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $setting_val The setting value from DB.
	 * @param string $control_name The name attribute for the setting field.
	 * @param string $control_id The id attribute for the setting field.
	 * @param array  $args Field arguments.
	 */
	public function field_checkbox_render( $setting_val, $control_name, $control_id, $args ) {
		echo '<input type="hidden" name="' . esc_attr( $control_name ) . '" value="0">';
		echo '<input type="checkbox" name="' . esc_attr( $control_name ) . '" id="' . esc_attr( $control_id ) . '" value="1"' . checked( 1, $setting_val, false ) . '>';
	}

	/**
	 * Outputs the setting radio field markup.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $setting_val The setting value from DB.
	 * @param string $control_name The name attribute for the setting field.
	 * @param string $control_id The id attribute for the setting field.
	 * @param array  $args Field arguments.
	 */
	public function field_radio_render( $setting_val, $control_name, $control_id, $args ) {
		echo '<fieldset>';
		$radios = array();
		foreach ( $args['choices'] as $v => $choice ) {
			$allowed_tags = array( 'a' => array( 'href' => true ) );
			$radios[]     = '<label><input type="radio" name="' . esc_attr( $control_name ) . '" value="' . esc_attr( $v ) . '"' . checked( $v, $setting_val, false ) . '> ' . wp_kses( $choice, $allowed_tags ) . '</label>';
		}
		echo implode( '<br>', $radios ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</fieldset>';
	}

	/**
	 * Outputs the setting thumbnail_size field markup.
	 *
	 * @since 1.0.0
	 *
	 * @param string $setting_val The setting value from DB.
	 * @param string $control_name The name attribute for the setting field.
	 * @param string $control_id The id attribute for the setting field.
	 * @param array  $args Field arguments.
	 */
	public function field_thumbnail_size_render( $setting_val, $control_name, $control_id, $args ) {
		$choices = $this->get_thumbnail_sizes();
		echo '<select name="' . esc_attr( $control_name ) . '" id="' . esc_attr( $control_id ) . '">';
		foreach ( $choices as $val => $choice ) {
			$width  = $choice['width'];
			$height = $choice['height'];
			$size   = __( 'Size', 'dco-comment-attachment' ) . ": {$width}x{$height}";

			$crop = __( 'No', 'dco-comment-attachment' );
			if ( $choice['crop'] ) {
				$crop = __( 'Yes', 'dco-comment-attachment' );
			}
			$crop = __( 'Crop', 'dco-comment-attachment' ) . ": $crop";

			$title = ucfirst( $val );
			$text  = "$title, $size, $crop";

			echo '<option value="' . esc_attr( $val ) . '"' . selected( $val, $setting_val, false ) . '>' . esc_html( $text ) . '</option>';
		}
		$val  = 'full';
		$text = __( 'Full (original image)', 'dco-comment-attachment' );
		echo '<option value="' . esc_attr( $val ) . '"' . selected( $val, $setting_val, false ) . '>' . esc_html( $text ) . '</option>';
		echo '</select>';
	}

	/**
	 * Outputs the setting allowed_file_types field markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $setting_val The setting value from DB.
	 * @param string $control_name The name attribute for the setting field.
	 * @param string $control_id The id attribute for the setting field.
	 * @param array  $args Field arguments.
	 */
	public function field_allowed_file_types_render( $setting_val, $control_name, $control_id, $args ) {
		$special_exts = array( 'htm', 'html', 'js' );
		$embed_exts   = array_merge( wp_get_video_extensions(), wp_get_audio_extensions(), $this->get_image_exts() );

		/*
		* Translators: If the type names in your language are wider or narrower than in English - you can change the width of the column here.
		*/
		$column_width = _x( '100', 'Allowed File Types Setting: column width in px', 'dco-comment-attachment' );

		echo '<div id="dco-file-types">';
		$types = $this->get_allowed_file_types();
		$more  = 6;
		foreach ( $types as $type ) {
			echo '<div class="dco-file-type" style="width: ' . (int) $column_width . 'px;">';
			echo '<label class="dco-file-type-name" title="' . esc_attr__( 'Click to check/uncheck all extensions of this type.', 'dco-comment-attachment' ) . '"><input type="checkbox" class="dco-file-type-name-checkbox"> ' . $this->mb_ucfirst( esc_html( $type['name'] ) ) . '</label>';
			echo '<div class="dco-file-type-items">';
			$i = 1;
			foreach ( $type['exts'] as $ext ) {
				if ( $i === $more ) {
					echo '</div><div class="dco-file-type-items-more">';
				}
				$mark = '';
				if ( in_array( $ext, $embed_exts, true ) ) {
					$mark = ' *';
				}
				if ( in_array( $ext, $special_exts, true ) ) {
					$mark = ' **';
				}
				echo '<label class="dco-file-type-item"><input type="checkbox" class="dco-file-type-item-checkbox" name="' . esc_attr( $control_name ) . '[]" value="' . esc_attr( $ext ) . '"' . checked( in_array( $ext, $setting_val, true ), true, false ) . '> ' . esc_html( $ext . $mark ) . '</label>';
				$i++;
			}
			echo '</div>';
			if ( $i > $more ) {
				echo '<a href="#" class="dco-show-all">' . esc_html__( 'Show all', 'dco-comment-attachment' ) . '</a>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Gets thumbnail sizes registered on the site.
	 *
	 * @since 1.0.0
	 *
	 * @return array Thumbnail sizes.
	 */
	public function get_thumbnail_sizes() {
		$standard_sizes = array(
			'thumbnail',
			'medium',
			'medium_large',
			'large',
		);

		foreach ( $standard_sizes as $size ) {
			$sizes[ $size ] = array(
				'width'  => get_option( "{$size}_size_w" ),
				'height' => get_option( "{$size}_size_h" ),
				'crop'   => get_option( "{$size}_crop" ),
			);
		}

		return array_merge( $sizes, wp_get_additional_image_sizes() );
	}

	/**
	 * Helper function for make a string's first character uppercase.
	 *
	 * @since 1.0.0
	 *
	 * @param string $str Source string.
	 * @return string String with first character uppercase.
	 */
	public function mb_ucfirst( $str ) {
		$fc = mb_strtoupper( mb_substr( $str, 0, 1, 'UTF-8' ), 'UTF-8' );
		return $fc . mb_substr( $str, 1, null, 'UTF-8' );
	}

}
