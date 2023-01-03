<?php
/**
 * Basic functions: DCO_CA_Base class
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
 * Class with basic functions.
 *
 * @since 1.0.0
 */
class DCO_CA_Base {

	/**
	 * An array of plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @var array $options Plugin options.
	 */
	private $options = array();

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
		$this->set_options();

		// Compatibility with Loco Translate.
		load_plugin_textdomain( 'dco-comment-attachment' );
	}

	/**
	 * Sets plugin options to the `$options` property from the database.
	 *
	 * @since 1.0.0
	 */
	public function set_options() {
		$default = $this->get_default_options();
		$options = get_option( DCO_CA_Settings::ID );

		$this->options = wp_parse_args( $options, $default );
	}

	/**
	 * Gets an assigned attachment ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $comment_id Optional. The comment ID.
	 * @return int|array|string The assigned attachment ID(s) on success,
	 *                          empty string on failure.
	 */
	public function get_attachment_id( $comment_id = 0 ) {
		$meta_key = $this->get_attachment_meta_key();

		if ( ! $comment_id ) {
			$comment_id = get_comment_ID();
		}

		return get_comment_meta( $comment_id, $meta_key, true );
	}

	/**
	 * Checks if a comment has an attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $comment_id Optional. The comment ID.
	 * @return bool Whether the comment has an attachment.
	 */
	public function has_attachment( $comment_id = 0 ) {
		if ( ! $comment_id ) {
			$comment_id = get_comment_ID();
		}

		$attachment_id = $this->get_attachment_id( $comment_id );
		if ( ! $attachment_id ) {
			return false;
		}

		// Check that at least one attachment exists.
		foreach ( (array) $attachment_id as $attach_id ) {
			if ( wp_get_attachment_url( $attach_id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Assigns an attachment for the comment.
	 *
	 * @since 1.0.0
	 *
	 * @param int       $comment_id The comment ID.
	 * @param int|array $attachment_id The attachment ID(s).
	 * @return int|bool Meta ID on success, false on failure.
	 */
	public function assign_attachment( $comment_id, $attachment_id ) {
		$meta_key = $this->get_attachment_meta_key();

		// Compatibility with 1.x version.
		if ( is_array( $attachment_id ) && 1 === count( $attachment_id ) ) {
			$attachment_id = current( $attachment_id );
		}

		return update_comment_meta( $comment_id, $meta_key, $attachment_id );
	}

	/**
	 * Generates HTML markup for the attachment based on it type.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attachment_id The attachment ID.
	 * @return string HTML markup for the attachment.
	 */
	public function get_attachment_preview( $attachment_id ) {
		$url = wp_get_attachment_url( $attachment_id );

		if ( ! $url ) {
			return false;
		}

		$embed_type = $this->get_embed_type( $attachment_id );

		switch ( $embed_type ) {
			case 'image':
				$thumbnail_size = $this->get_option( 'thumbnail_size' );
				if ( is_admin() ) {
					/**
					 * Filters the attachment image size for the admin panel.
					 *
					 * @since 2.0.0
					 *
					 * @param string $size The thumbnail size of the attachment image.
					 */
					$thumbnail_size = apply_filters( 'dco_ca_admin_thumbnail_size', 'medium' );
				}

				$img = wp_get_attachment_image( $attachment_id, $thumbnail_size );

				/**
				 * 0 — No link
				 * 1 — Link to a full-size image with lightbox plugins support
				 * 2 — Link to a full-size image in a new tab
				 * 3 — Link to the attachment page
				 */
				$link_thumbnail = (int) $this->get_option( 'link_thumbnail' );
				if ( ! is_admin() && $link_thumbnail ) {
					$tab = '';
					if ( 2 === $link_thumbnail ) {
						$tab = ' target="_blank"';
					}

					if ( in_array( $link_thumbnail, array( 1, 2 ), true ) ) {
						$link = wp_get_attachment_image_url( $attachment_id, 'full' );
					} else {
						$link = get_attachment_link( $attachment_id );
					}

					$img = '<a href="' . esc_url( $link ) . '" class="dco-attachment-link dco-image-attachment-link"' . $tab . '>' . $img . '</a>';
					if ( 1 === $link_thumbnail ) {
						$img = $this->activate_lightbox( $img );
					}
				}

				$attachment_content = '<p class="dco-attachment dco-image-attachment">' . $img . '</p>';

				/**
				* Filters the HTML markup for the image attachment.
				*
				* @since 2.1.1
				*
				* @param string $attachment_content HTML markup for the attachment.
				* @param int $attachment_id The attachment ID.
				* @param string $thumbnail_size The thumbnail size of the attachment image.
				*/
				$attachment_content = apply_filters( 'dco_ca_get_attachment_preview_image', $attachment_content, $attachment_id, $thumbnail_size );

				break;
			case 'video':
				$attachment_content = '<div class="dco-attachment dco-video-attachment">' . do_shortcode( '[video src="' . esc_url( $url ) . '"]' ) . '</div>';
				break;
			case 'audio':
				$attachment_content = '<div class="dco-attachment dco-audio-attachment">' . do_shortcode( '[audio src="' . esc_url( $url ) . '"]' ) . '</div>';
				break;
			case 'misc':
				$download = '';

				/**
				* Filters whether to force download misc attachments.
				*
				* @since 2.3.0
				*
				* @param bool $force_download Whether to force download misc attachments.
				*/
				if ( apply_filters( 'dco_ca_force_download_misc_attachments', false ) ) {
					$download = ' download';
				}

				$title              = get_the_title( $attachment_id );
				$attachment_content = '<p class="dco-attachment dco-misc-attachment"><a href="' . esc_url( $url ) . '"' . $download . '>' . esc_html( $title ) . '</a></p>';
		}

		/**
		* Filters the HTML markup for the attachment.
		*
		* @since 2.1.1
		*
		* @param string $attachment_content HTML markup for the attachment.
		* @param int $attachment_id The attachment ID.
		* @param string $embed_type The embed type (image, video, audio, misc)
		*                           of the attachment.
		*/
		$attachment_content = apply_filters( 'dco_ca_get_attachment_preview', $attachment_content, $attachment_id, $embed_type );

		return $attachment_content;
	}

	/**
	 * Gets the embed type of the attachment by its extension.
	 *
	 * @since 1.3.0
	 *
	 * @param int $attachment_id The attachment ID.
	 * @return string The embed type (image, video, audio, misc).
	 */
	public function get_embed_type( $attachment_id ) {
		if ( ! $this->get_option( 'embed_attachment' ) ) {
			return 'misc';
		}

		$ext = wp_check_filetype( get_attached_file( $attachment_id ) )['ext'];

		if ( in_array( $ext, $this->get_image_exts(), true ) ) {
			return 'image';
		}

		if ( in_array( $ext, wp_get_video_extensions(), true ) ) {
			return 'video';
		}

		if ( in_array( $ext, wp_get_audio_extensions(), true ) ) {
			return 'audio';
		}

		return 'misc';
	}

	/**
	 * Adds compatibility with lightbox plugins.
	 *
	 * Supports:
	 *  - Simple Lightbox 2.8.1
	 *  - Easy FancyBox 1.8.18
	 *  - Responsive Lightbox & Gallery 2.3.1
	 *  - FooBox Image Lightbox 2.7.8
	 *  - FancyBox for WordPress 3.3.0
	 *
	 * @since 2.0.0
	 *
	 * @param string $img The image markup.
	 * @return string The image markup with lightbox support.
	 */
	public function activate_lightbox( $img ) {
		$comment_id = get_comment_ID();

		// Simple Lightbox.
		if ( function_exists( 'slb_activate' ) ) {
			$img = slb_activate( $img, $comment_id );
			// Responsive Lightbox & Gallery.
		} elseif ( function_exists( 'Responsive_Lightbox' ) ) {
			$selector = Responsive_Lightbox()->options['settings']['selector'];
			$rel      = $selector . '-gallery-' . $comment_id;
			$img      = str_replace( '<a', '<a data-rel="' . $rel . '"', $img );
			// Other lightbox plugins.
		} else {
			$rel = 'dco-ca-gallery-' . $comment_id;
			$img = str_replace( '<a', '<a rel="' . $rel . '"', $img );
		}

		// FooBox Image Lightbox.
		if ( class_exists( 'FooBox' ) ) {
			$img = str_replace( '<a', '<a class="foobox"', $img );
		}

		return $img;
	}

	/**
	 * Gets max upload file size.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $with_format Optional. Whether to int value or value with units.
	 *                                    Default false for int.
	 * @param bool $for_setting Optional. Whether to value from plugin settings
	 *                                    or system value.
	 *                                    Default false for plugin settings value.
	 * @return int|string Default integer, value with units if $with_format is true.
	 */
	public function get_max_upload_size( $with_format = false, $for_setting = false ) {
		$max_upload_size = $this->get_option( 'max_upload_size' ) * MB_IN_BYTES;

		if ( $for_setting && $with_format ) {
			return size_format( wp_max_upload_size() );
		}

		if ( $with_format ) {
			return size_format( $max_upload_size );
		}

		if ( $for_setting ) {
			return wp_max_upload_size() / MB_IN_BYTES;
		}

		return $max_upload_size;
	}

	/**
	 * Gets allowed upload file types.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format html for HTML markup, array for flat array.
	 *                       Default array with types.
	 * @return array|string File types allowed for upload.
	 */
	public function get_allowed_file_types( $format = 'default' ) {
		$mimes = array_keys( get_allowed_mime_types() );

		if ( 'array' === $format ) {
			$new_mimes = array();
			foreach ( $mimes as $mime ) {
				$exts = explode( '|', $mime );
				foreach ( $exts as $ext ) {
					$new_mimes[] = $ext;
				}
			}

			return $new_mimes;
		}

		$types = array(
			'image'       => array(
				'name' => __( 'image', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'audio'       => array(
				'name' => __( 'audio', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'video'       => array(
				'name' => __( 'video', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'document'    => array(
				'name' => __( 'document', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'spreadsheet' => array(
				'name' => __( 'spreadsheet', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'interactive' => array(
				'name' => __( 'interactive', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'text'        => array(
				'name' => __( 'text', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'archive'     => array(
				'name' => __( 'archive', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'code'        => array(
				'name' => __( 'code', 'dco-comment-attachment' ),
				'exts' => array(),
			),
			'other'       => array(
				'name' => __( 'other', 'dco-comment-attachment' ),
				'exts' => array(),
			),
		);

		foreach ( $mimes as $mime ) {
			$exts = explode( '|', $mime );
			foreach ( $exts as $ext ) {
				$type = wp_ext2type( $ext );
				if ( $type && isset( $types[ $type ]['exts'] ) ) {
					$types[ $type ]['exts'][] = $ext;
				} else {
					$types['other']['exts'][] = $ext;
				}
			}
		}

		if ( 'html' === $format ) {
			$types_arr = array();
			foreach ( $types as $type ) {
				if ( $type['exts'] ) {
					$title       = implode( ', ', $type['exts'] );
					$types_arr[] = '<abbr title="' . esc_attr( $title ) . '">' . esc_html( $type['name'] ) . '</abbr>';
				}
			}
			$types = implode( ', ', $types_arr );
		}

		return $types;
	}

	/**
	 * Gets all plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of plugin options.
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Gets the plugin option by the name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The option name.
	 * @return mixed|false Returns the value of the option if it is found,
	 *                     false if the option does not exist.
	 */
	public function get_option( $name ) {
		if ( isset( $this->options[ $name ] ) ) {
			/**
			 * Filters the value of the plugin option.
			 *
			 * The dynamic portion of the hook name, `$name`, refers to the option name.
			 *
			 * @since 2.0.0
			 *
			 * @param mixed $value Value of the option.
			 */
			return apply_filters( "dco_ca_get_option_{$name}", $this->options[ $name ] );
		}

		return false;
	}

	/**
	 * Gets default plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of plugin default options.
	 */
	public function get_default_options() {
		$options = array();

		$fields = dco_ca( '_settings' )->get_fields();
		foreach ( $fields as $name => $field ) {
			$options[ $name ] = $field['default'];
		}

		return $options;
	}

	/**
	 * Gets the meta key of the attachment ID for comment meta.
	 *
	 * @since 1.0.0
	 *
	 * return string The attachment ID meta key.
	 */
	public function get_attachment_meta_key() {
		return 'attachment_id';
	}

	/**
	 * Gets the list of image formats available for embedding.
	 *
	 * WordPress doesn't have a list of supported image formats.
	 * See https://core.trac.wordpress.org/ticket/41801
	 *
	 * @since 1.3.0
	 *
	 * return array The list of image formats.
	 */
	public function get_image_exts() {
		return array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp' );
	}

}
