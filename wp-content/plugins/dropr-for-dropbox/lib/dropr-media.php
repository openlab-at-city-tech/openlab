<?php

/**
 * Dropbox Media Class
 *
 * @link       http://awsm.in/awsm-dropbox
 * @since      1.2
 *
 * @package    Dropr_main
 */
class Dropr_media {
	private static $instance;
	private $plugin_path;
	private $plugin_version;

	public static function getInstance( $plugin_path, $version ) {
		if ( self::$instance === null ) {
			self::$instance = new Dropr_media( $plugin_path, $version );
		}
		return self::$instance;
	}

	private function __construct( $plugin_path, $version ) {
		$this->plugin_path    = $plugin_path;
		$this->plugin_version = $version;

		add_filter( 'wp_video_shortcode_override', array( $this, 'dropr_video' ), PHP_INT_MAX, 2 );
		add_filter( 'wp_audio_shortcode_override', array( $this, 'dropr_audio' ), PHP_INT_MAX, 2 );
	}

	/**
	 * Dropr Video override
	 * @since  1.1
	 */
	public function dropr_video( $content, $attr ) {
		global $content_width;
		$post_id = get_post() ? get_the_ID() : 0;

		static $instance = 0;
		$instance++;

		$video = null;

		$default_types = wp_get_video_extensions();
		$defaults_atts = array(
			'src'      => '',
			'poster'   => '',
			'loop'     => '',
			'autoplay' => '',
			'preload'  => 'metadata',
			'width'    => 640,
			'height'   => 360,
			'class'    => 'wp-video-shortcode',
		);

		foreach ( $default_types as $type ) {
			$defaults_atts[ $type ] = '';
		}

		$atts = shortcode_atts( $defaults_atts, $attr, 'video' );

		if ( is_admin() ) {
			// shrink the video so it isn't huge in the admin
			if ( $atts['width'] > $defaults_atts['width'] ) {
				$atts['height'] = round( ( $atts['height'] * $defaults_atts['width'] ) / $atts['width'] );
				$atts['width']  = $defaults_atts['width'];
			}
		} else {
			// if the video is bigger than the theme
			if ( ! empty( $content_width ) && $atts['width'] > $content_width ) {
				$atts['height'] = round( ( $atts['height'] * $content_width ) / $atts['width'] );
				$atts['width']  = $content_width;
			}
		}

		$is_vimeo      = $is_youtube = false;
		$yt_pattern    = '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#';
		$vimeo_pattern = '#^https?://(.+\.)?vimeo\.com/.*#';

		$primary = false;
		if ( ! empty( $atts['src'] ) ) {
			$is_vimeo   = ( preg_match( $vimeo_pattern, $atts['src'] ) );
			$is_youtube = ( preg_match( $yt_pattern, $atts['src'] ) );
			if ( ! $is_youtube && ! $is_vimeo ) {
				$filename = parse_url( $atts['src'], PHP_URL_PATH );
				$type     = wp_check_filetype( $filename, wp_get_mime_types() );
				if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
					return sprintf( '<a class="wp-embedded-video" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
				}
			}

			if ( $is_vimeo ) {
				wp_enqueue_script( 'mediaelement-vimeo' );
			}

			$primary = true;
			array_unshift( $default_types, 'src' );
		} else {
			foreach ( $default_types as $ext ) {
				if ( ! empty( $atts[ $ext ] ) ) {
					$filename = parse_url( $atts[ $ext ], PHP_URL_PATH );
					$type     = wp_check_filetype( $filename, wp_get_mime_types() );
					if ( strtolower( $type['ext'] ) === $ext ) {
						$primary = true;
					}
				}
			}
		}

		if ( ! $primary ) {
			$videos = get_attached_media( 'video', $post_id );
			if ( empty( $videos ) ) {
				return;
			}

			$video       = reset( $videos );
			$atts['src'] = wp_get_attachment_url( $video->ID );
			if ( empty( $atts['src'] ) ) {
				return;
			}

			array_unshift( $default_types, 'src' );
		}

		/**
		 * Filters the media library used for the video shortcode.
		 *
		 * @since 3.6.0
		 *
		 * @param string $library Media library used for the video shortcode.
		 */
		$library = apply_filters( 'wp_video_shortcode_library', 'mediaelement' );
		if ( 'mediaelement' === $library && did_action( 'init' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement-vimeo' );
		}

		// Mediaelement has issues with some URL formats for Vimeo and YouTube, so
		// update the URL to prevent the ME.js player from breaking.
		if ( 'mediaelement' === $library ) {
			if ( $is_youtube ) {
				// Remove `feature` query arg and force SSL - see #40866.
				$atts['src'] = remove_query_arg( 'feature', $atts['src'] );
				$atts['src'] = set_url_scheme( $atts['src'], 'https' );
			} elseif ( $is_vimeo ) {
				// Remove all query arguments and force SSL - see #40866.
				$parsed_vimeo_url = wp_parse_url( $atts['src'] );
				$vimeo_src        = 'https://' . $parsed_vimeo_url['host'] . $parsed_vimeo_url['path'];

				// Add loop param for mejs bug - see #40977, not needed after #39686.
				$loop        = $atts['loop'] ? '1' : '0';
				$atts['src'] = add_query_arg( 'loop', $loop, $vimeo_src );
			}
		}

		/**
		 * Filters the class attribute for the video shortcode output container.
		 *
		 * @since 3.6.0
		 * @since 4.9.0 The `$atts` parameter was added.
		 *
		 * @param string $class CSS class or list of space-separated classes.
		 * @param array  $atts  Array of video shortcode attributes.
		 */
		$atts['class'] = apply_filters( 'wp_video_shortcode_class', $atts['class'], $atts );

		$html_atts = array(
			'class'    => $atts['class'],
			'id'       => sprintf( 'video-%d-%d', $post_id, $instance ),
			'width'    => absint( $atts['width'] ),
			'height'   => absint( $atts['height'] ),
			'poster'   => esc_url( $atts['poster'] ),
			'loop'     => wp_validate_boolean( $atts['loop'] ),
			'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
			'preload'  => $atts['preload'],
		);

		// These ones should just be omitted altogether if they are blank
		foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
			if ( empty( $html_atts[ $a ] ) ) {
				unset( $html_atts[ $a ] );
			}
		}

		$attr_strings = array();
		foreach ( $html_atts as $k => $v ) {
			$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
		}

		$html = '';
		if ( 'mediaelement' === $library && 1 === $instance ) {
			$html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";
		}
		$html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

		$fileurl = '';
		$source  = '<source type="%s" src="%s" />';
		foreach ( $default_types as $fallback ) {
			if ( ! empty( $atts[ $fallback ] ) ) {
				if ( empty( $fileurl ) ) {
					$fileurl = $atts[ $fallback ];
				}
				if ( 'src' === $fallback && $is_youtube ) {
					$type = array( 'type' => 'video/youtube' );
				} elseif ( 'src' === $fallback && $is_vimeo ) {
					$type = array( 'type' => 'video/vimeo' );
				} else {
					$filename = parse_url( $atts[ $fallback ], PHP_URL_PATH );
					$type     = wp_check_filetype( $filename, wp_get_mime_types() );
				}
				$url   = add_query_arg( '_', $instance, $atts[ $fallback ] );
				$html .= sprintf( $source, $type['type'], esc_url( $url ) );
			}
		}

		if ( ! empty( $content ) ) {
			if ( false !== strpos( $content, "\n" ) ) {
				$content = str_replace( array( "\r\n", "\n", "\t" ), '', $content );
			}
			$html .= trim( $content );
		}

		if ( 'mediaelement' === $library ) {
			$html .= wp_mediaelement_fallback( $fileurl );
		}
		$html .= '</video>';

		$width_rule = '';
		if ( ! empty( $atts['width'] ) ) {
			$width_rule = sprintf( 'width: %dpx;', $atts['width'] );
		}
		$output = sprintf( '<div style="%s" class="wp-video">%s</div>', $width_rule, $html );

		/**
		 * Filters the output of the video shortcode.
		 *
		 * @since 3.6.0
		 *
		 * @param string $output  Video shortcode HTML output.
		 * @param array  $atts    Array of video shortcode attributes.
		 * @param string $video   Video file.
		 * @param int    $post_id Post ID.
		 * @param string $library Media library used for the video shortcode.
		 */
		return apply_filters( 'wp_video_shortcode', $output, $atts, $video, $post_id, $library );
	}

	/**
	 * Dropr Audio override
	 * @since  1.1
	 */
	public function dropr_audio( $content, $attr ) {

		$post_id = get_post() ? get_the_ID() : 0;

		static $instance = 0;
		$instance++;

		$audio = null;

		$default_types = wp_get_audio_extensions();
		$defaults_atts = array(
			'src'      => '',
			'loop'     => '',
			'autoplay' => '',
			'preload'  => 'none',
			'class'    => 'wp-audio-shortcode',
			'style'    => 'width: 100%;',
		);
		foreach ( $default_types as $type ) {
			$defaults_atts[ $type ] = '';
		}

		$atts = shortcode_atts( $defaults_atts, $attr, 'audio' );

		$primary = false;
		if ( ! empty( $atts['src'] ) ) {
			$type = wp_check_filetype( $atts['src'], wp_get_mime_types() );
			if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
				return sprintf( '<a class="wp-embedded-audio" href="%s">%s</a>', esc_url( $atts['src'] ), esc_html( $atts['src'] ) );
			}
			$primary = true;
			array_unshift( $default_types, 'src' );
		} else {
			foreach ( $default_types as $ext ) {
				if ( ! empty( $atts[ $ext ] ) ) {
					$filename = parse_url( $atts[ $ext ], PHP_URL_PATH );
					$type     = wp_check_filetype( $filename, wp_get_mime_types() );
					if ( strtolower( $type['ext'] ) === $ext ) {
						$primary = true;
					}
				}
			}
		}

		if ( ! $primary ) {
			$audios = get_attached_media( 'audio', $post_id );
			if ( empty( $audios ) ) {
				return;
			}

			$audio       = reset( $audios );
			$atts['src'] = wp_get_attachment_url( $audio->ID );
			if ( empty( $atts['src'] ) ) {
				return;
			}

			array_unshift( $default_types, 'src' );
		}

		/**
		 * Filters the media library used for the audio shortcode.
		 *
		 * @since 3.6.0
		 *
		 * @param string $library Media library used for the audio shortcode.
		 */
		$library = apply_filters( 'wp_audio_shortcode_library', 'mediaelement' );
		if ( 'mediaelement' === $library && did_action( 'init' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		/**
		 * Filters the class attribute for the audio shortcode output container.
		 *
		 * @since 3.6.0
		 * @since 4.9.0 The `$atts` parameter was added.
		 *
		 * @param string $class CSS class or list of space-separated classes.
		 * @param array  $atts  Array of audio shortcode attributes.
		 */
		$atts['class'] = apply_filters( 'wp_audio_shortcode_class', $atts['class'], $atts );

		$html_atts = array(
			'class'    => $atts['class'],
			'id'       => sprintf( 'audio-%d-%d', $post_id, $instance ),
			'loop'     => wp_validate_boolean( $atts['loop'] ),
			'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
			'preload'  => $atts['preload'],
			'style'    => $atts['style'],
		);

		// These ones should just be omitted altogether if they are blank
		foreach ( array( 'loop', 'autoplay', 'preload' ) as $a ) {
			if ( empty( $html_atts[ $a ] ) ) {
				unset( $html_atts[ $a ] );
			}
		}

		$attr_strings = array();
		foreach ( $html_atts as $k => $v ) {
			$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
		}

		$html = '';
		if ( 'mediaelement' === $library && 1 === $instance ) {
			$html .= "<!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->\n";
		}
		$html .= sprintf( '<audio %s controls="controls">', join( ' ', $attr_strings ) );

		$fileurl = '';
		$source  = '<source type="%s" src="%s" />';
		foreach ( $default_types as $fallback ) {
			if ( ! empty( $atts[ $fallback ] ) ) {
				if ( empty( $fileurl ) ) {
					$fileurl = $atts[ $fallback ];
				}
				$filename = parse_url( $atts[ $fallback ], PHP_URL_PATH );
				$type     = wp_check_filetype( $filename, wp_get_mime_types() );
				$url      = add_query_arg( '_', $instance, $atts[ $fallback ] );
				$html    .= sprintf( $source, $type['type'], esc_url( $url ) );
			}
		}

		if ( 'mediaelement' === $library ) {
			$html .= wp_mediaelement_fallback( $fileurl );
		}
		$html .= '</audio>';

		/**
		 * Filters the audio shortcode output.
		 *
		 * @since 3.6.0
		 *
		 * @param string $html    Audio shortcode HTML output.
		 * @param array  $atts    Array of audio shortcode attributes.
		 * @param string $audio   Audio file.
		 * @param int    $post_id Post ID.
		 * @param string $library Media library used for the audio shortcode.
		 */
		return apply_filters( 'wp_audio_shortcode', $html, $atts, $audio, $post_id, $library );
	}
}
