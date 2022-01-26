<?php
/**
 * Class and methods to integrate EWWW IO and NextGEN Gallery.
 *
 * @link https://ewww.io
 * @package EWWW_Image_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'EWWW_Nextgen' ) ) {
	/**
	 * Allows EWWW to integrate with the NextGEN Gallery plugin.
	 *
	 * Adds automatic optimization on upload, a bulk optimizer, and compression details when
	 * managing galleries.
	 */
	class EWWW_Nextgen {
		/**
		 * Stores results for the bulk process.
		 *
		 * @access private
		 * @var array $bulk_sizes
		 */
		public $bulk_sizes = array();

		/**
		 * Initializes the nextgen integration functions.
		 */
		public function __construct() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			add_filter( 'ngg_manage_images_number_of_columns', array( $this, 'ewww_manage_images_number_of_columns' ) );
			add_filter( 'ngg_manage_images_columns', array( $this, 'manage_images_columns' ) );
			add_filter( 'ngg_manage_images_row_actions', array( $this, 'ewww_manage_images_row_actions' ) );
			if ( ewww_image_optimizer_test_background_opt() ) {
				add_action( 'ngg_added_new_image', array( $this, 'queue_new_image' ) );
				ewwwio_debug_message( 'background mode enabled for nextgen' );
			} else {
				add_action( 'ngg_added_new_image', array( $this, 'ewww_added_new_image' ) );
				ewwwio_debug_message( 'background mode NOT enabled for nextgen' );
			}
			add_action( 'wp_ajax_ewww_ngg_manual', array( $this, 'ewww_ngg_manual' ) );
			add_action( 'wp_ajax_ewww_ngg_cloud_restore', array( $this, 'ewww_ngg_cloud_restore' ) );
			add_action( 'admin_action_ewww_ngg_manual', array( $this, 'ewww_ngg_manual' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ewww_ngg_manual_actions_script' ) );
			add_action( 'admin_menu', array( $this, 'ewww_ngg_bulk_menu' ) );
			add_action( 'admin_menu', array( $this, 'ewww_ngg_update_menu' ), PHP_INT_MAX - 1 );
			add_action( 'admin_head', array( $this, 'ewww_ngg_bulk_actions_script' ) );
			add_action( 'admin_init', array( $this, 'ewww_ngg_bulk_action_handler' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ewww_ngg_bulk_script' ), 20 );
			add_action( 'wp_ajax_bulk_ngg_preview', array( $this, 'ewww_ngg_bulk_preview' ) );
			add_action( 'wp_ajax_bulk_ngg_init', array( $this, 'ewww_ngg_bulk_init' ) );
			add_action( 'wp_ajax_bulk_ngg_filename', array( $this, 'ewww_ngg_bulk_filename' ) );
			add_action( 'wp_ajax_bulk_ngg_loop', array( $this, 'ewww_ngg_bulk_loop' ) );
			add_action( 'wp_ajax_bulk_ngg_cleanup', array( $this, 'ewww_ngg_bulk_cleanup' ) );
			add_action( 'ngg_generated_image', array( $this, 'ewww_ngg_generated_image' ), 10, 2 );
			add_filter( 'ngg_get_image_size_params', array( $this, 'ewww_ngg_quality_param' ), 10, 2 );
		}

		/**
		 * Adds the Bulk Optimize page to the NextGEN menu.
		 */
		function ewww_ngg_bulk_menu() {
			if ( ! defined( 'NGGFOLDER' ) ) {
				return;
			}
			add_submenu_page( NGGFOLDER, esc_html__( 'Bulk Optimize', 'ewww-image-optimizer' ), esc_html__( 'Bulk Optimize', 'ewww-image-optimizer' ), apply_filters( 'ewww_image_optimizer_bulk_permissions', '' ), 'ewww-ngg-bulk', array( &$this, 'ewww_ngg_bulk_preview' ) );
			remove_submenu_page( 'nextgen-gallery', 'ngg_imagify' );
		}

		/**
		 * Removes unnecessary menu items from the NextGEN menu.
		 */
		function ewww_ngg_update_menu() {
			if ( ! defined( 'NGGFOLDER' ) ) {
				return;
			}
			remove_submenu_page( NGGFOLDER, 'ngg_imagify' );
		}

		/**
		 * Keep the NextGEN quality level sane and inline with user settings.
		 *
		 * @param array  $params The image sizing parameters.
		 * @param string $size The name of the size being processed.
		 * @return array The image sizing parameters, sanitized.
		 */
		function ewww_ngg_quality_param( $params, $size ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			$settings = C_NextGen_Settings::get_instance();
			if ( is_array( $params ) ) {
				ewwwio_debug_message( 'params is an array' );
				if ( ! empty( $params['quality'] ) && 100 === (int) $params['quality'] ) {
					$wp_quality = (int) apply_filters( 'jpeg_quality', 82, 'image_resize' );
					// If the size is full and the WP default has not been altered, go for higher quality. Otherwise, obey the current WP setting.
					$params['quality'] = 'full' === $size && 82 === $wp_quality ? 90 : $wp_quality;
					ewwwio_debug_message( "setting quality for ngg to {$params['quality']} for $size" );
				}
			}
			if ( empty( $params ) || empty( $params['quality'] ) ) {
				$wp_quality = (int) apply_filters( 'jpeg_quality', 82, 'image_resize' );
				if ( 'full' === $size ) {
					$ngg_quality = (int) $settings->imgQuality;
				} else {
					$ngg_quality = (int) $settings->thumbquality;
				}
				if ( empty( $ngg_quality ) || 100 === $ngg_quality ) {
					$params['quality'] = 'full' === $size && 82 === $wp_quality ? 90 : $wp_quality;
					ewwwio_debug_message( "setting quality for ngg to {$params['quality']} for $size" );
				}
			}
			return $params;
		}

		/**
		 * Looks for more sizes to optimize in the image metadata.
		 *
		 * @param array $sizes The image sizes NextGEN gave us.
		 * @param array $meta The image metadata from NextGEN.
		 * @return array The full list of known image sizes for this image.
		 */
		function maybe_get_more_sizes( $sizes, $meta ) {
			if ( 2 === count( $sizes ) && ewww_image_optimizer_iterable( $meta ) ) {
				foreach ( $meta as $meta_key => $meta_val ) {
					if ( 'backup' !== $meta_key && is_array( $meta_val ) && isset( $meta_val['width'] ) && ! in_array( $meta_key, $sizes, true ) ) {
						$sizes[] = $meta_key;
					}
				}
			}
			return $sizes;
		}

		/**
		 * Adds a newly uploaded image to the optimization queue.
		 *
		 * @param object|array $image The new image.
		 * @param object       $storage A nextgen storage object for finding metadata.
		 */
		function queue_new_image( $image, $storage = null ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			if ( empty( $storage ) ) {
				// Creating the 'registry' object for working with nextgen.
				$registry = C_Component_Registry::get_instance();
				// Creating a database storage object from the 'registry' object.
				$storage = $registry->get_utility( 'I_Gallery_Storage' );
			}
			// Find the image id.
			if ( is_array( $image ) ) {
				$image_id = $image['id'];
				$image    = $storage->object->_image_mapper->find( $image_id, true );
			} else {
				$image_id = $storage->object->_get_image_id( $image );
			}
			global $ewwwio_ngg2_background;
			if ( ! class_exists( 'WP_Background_Process' ) ) {
				require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'background.php' );
			}
			if ( ! is_object( $ewwwio_ngg2_background ) ) {
				$ewwwio_ngg2_background = new EWWWIO_Ngg2_Background_Process();
			}
			ewwwio_debug_message( "backgrounding optimization for $image_id" );
			$ewwwio_ngg2_background->push_to_queue( array( 'id' => $image_id ) );
			$ewwwio_ngg2_background->dispatch();
			ewww_image_optimizer_debug_log();
		}

		/**
		 * Optimizes an image (and derivatives).
		 *
		 * @global object $ewww_image Contains more information about the image currently being processed.
		 *
		 * @param object|array $image The new image.
		 * @param object       $storage A nextgen storage object for finding metadata.
		 * @return object The image object with any modifications necessary.
		 */
		function ewww_added_new_image( $image, $storage = null ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			if ( empty( $storage ) ) {
				// Creating the 'registry' object for working with nextgen.
				$registry = C_Component_Registry::get_instance();
				// Creating a database storage object from the 'registry' object.
				$storage = $registry->get_utility( 'I_Gallery_Storage' );
			}
			global $ewww_image;
			$this->bulk_sizes = array();
			// Find the image id.
			if ( is_array( $image ) ) {
				$image_id = $image['id'];
				$image    = $storage->object->_image_mapper->find( $image_id, true );
			} else {
				$image_id = $storage->object->_get_image_id( $image );
			}
			ewwwio_debug_message( "image id: $image_id" );
			// Get an array of sizes available for the $image.
			$sizes = $storage->get_image_sizes( $image );
			$sizes = $this->maybe_get_more_sizes( $sizes, $image->meta_data );
			// Run the optimizer on the image for each $size.
			if ( ewww_image_optimizer_iterable( $sizes ) ) {
				foreach ( $sizes as $size ) {
					if ( 'full' === $size ) {
						$full_size = true;
					} else {
						$full_size = false;
					}
					if ( 'backup' === $size ) {
						continue;
					}
					// Get the absolute path.
					$file_path = $storage->get_image_abspath( $image, $size );
					ewwwio_debug_message( "optimizing (nextgen) $size: $file_path" );
					$ewww_image         = new EWWW_Image( $image_id, 'nextgen', $file_path );
					$ewww_image->resize = $size;
					// Optimize the image and grab the results.
					$res = ewww_image_optimizer( $file_path, 2, false, false, $full_size );
					ewwwio_debug_message( "results {$res[1]}" );
					$this->bulk_sizes[ $size ] = $res[1];
				}
			}
			return $image;
		}

		/**
		 * Optimizes a generated image.
		 *
		 * @global object $ewww_image Contains more information about the image currently being processed.
		 *
		 * @param object $image A nextgen image object.
		 * @param object $size The name of the size generated.
		 */
		function ewww_ngg_generated_image( $image, $size ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			global $ewww_image;
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage            = $registry->get_utility( 'I_Gallery_Storage' );
			$filename           = $storage->get_image_abspath( $image, $size );
			$ewww_image         = new EWWW_Image( $image->pid, 'nextgen', $filename );
			$ewww_image->resize = $size;
			if ( file_exists( $filename ) ) {
				ewww_image_optimizer( $filename, 2 );
				ewwwio_debug_message( "nextgen dynamic thumb saved: $filename" );
				$image_size = ewww_image_optimizer_filesize( $filename );
				ewwwio_debug_message( "optimized size: $image_size" );
			}
			ewww_image_optimizer_debug_log();
		}

		/**
		 * Manually process an image from the NextGEN Gallery.
		 */
		function ewww_ngg_manual() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( false === isset( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Sanitize the attachment $id.
			$id = (int) $_REQUEST['ewww_attachment_ID'];
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			global $ewww_force;
			$ewww_force = ! empty( $_REQUEST['ewww_force'] ) ? true : false;
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );
			// Get an image object.
			$image   = $storage->object->_image_mapper->find( $id );
			$image   = $this->ewww_added_new_image( $image, $storage );
			$success = $this->ewww_manage_image_custom_column( '', $image );
			if ( 'exceeded' === get_transient( 'ewww_image_optimizer_cloud_status' ) || ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_exceeded' ) > time() ) {
				ewwwio_ob_clean();
				wp_die(
					wp_json_encode(
						array(
							'error' => '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License exceeded', 'ewww-image-optimizer' ) . '</a>',
						)
					)
				);
			}
			if ( 'exceeded quota' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				ewwwio_ob_clean();
				wp_die(
					wp_json_encode(
						array(
							'error' => '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>',
						)
					)
				);
			}
			if ( ! wp_doing_ajax() ) {
				// Get the referring page, and send the user back there.
				wp_safe_redirect( wp_get_referer() );
				die;
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}

		/**
		 * Restore an image from the NextGEN Gallery.
		 */
		function ewww_ngg_cloud_restore() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( false === isset( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Sanitize the attachment $id.
			$id = intval( $_REQUEST['ewww_attachment_ID'] );
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
						wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );
			// Get an image object.
			$image = $storage->object->_image_mapper->find( $id );
			ewww_image_optimizer_cloud_restore_from_meta_data( $image->pid, 'nextgen' );
			$success = $this->ewww_manage_image_custom_column( '', $image );
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}

		/**
		 * Prepare javascript for one-click actions on manage gallery page.
		 *
		 * @param string $hook The hook value for the current page.
		 */
		function ewww_ngg_manual_actions_script( $hook ) {
			$screen = get_current_screen();
			if ( is_null( $screen ) ) {
				return;
			}
			if ( 'nextgen-gallery_page_nggallery-manage-gallery' !== $screen->id && 'nggallery-manage-images' !== $screen->id ) {
				return;
			}
			if ( ! current_user_can( apply_filters( 'ewww_image_optimizer_manual_permissions', '' ) ) ) {
				return;
			}
			add_thickbox();
			wp_enqueue_script( 'ewwwnextgenscript', plugins_url( '/includes/nextgen.js', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE ), array( 'jquery' ), EWWW_IMAGE_OPTIMIZER_VERSION );
			wp_enqueue_style( 'jquery-ui-tooltip-custom', plugins_url( '/includes/jquery-ui-1.10.1.custom.css', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE ), array(), EWWW_IMAGE_OPTIMIZER_VERSION );
			// Submit a couple variables needed for javascript functions.
			$loading_image = plugins_url( '/images/spinner.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
			wp_localize_script(
				'ewwwnextgenscript',
				'ewww_vars',
				array(
					'optimizing' => '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' /></p>",
					'restoring'  => '<p>' . esc_html__( 'Restoring', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' /></p>",
				)
			);
		}

		/**
		 * Adds our column to the list for users to toggle via Screen Options.
		 *
		 * @param array $columns A list of existing column names.
		 * @return array The revised list of column names.
		 */
		function manage_images_columns( $columns ) {
			if ( is_array( $columns ) ) {
				$columns['ewww_image_optimizer'] = esc_html__( 'Image Optimizer', 'ewww-image-optimizer' );
			}
			return $columns;
		}

		/**
		 * Filter for ngg_manage_images_number_of_columns hook, changed in NGG 2.0.50ish.
		 *
		 * @param int $count The number of columns for the table display.
		 * @return int The new number of columns.
		 */
		function ewww_manage_images_number_of_columns( $count ) {
			$count++;
			add_filter( "ngg_manage_images_column_{$count}_header", array( $this, 'ewww_manage_images_columns' ) );
			add_filter( "ngg_manage_images_column_{$count}_content", array( $this, 'ewww_manage_image_custom_column' ), 10, 2 );
			return $count;
		}

		/**
		 * Outputs column header via ngg_manage_images_column_x_header hook.
		 *
		 * @param array|null $columns List of headers for the table.
		 * @return array|string The new list of headers, or the single header for EWWW IO.
		 */
		function ewww_manage_images_columns( $columns = null ) {
			if ( is_array( $columns ) ) {
				$columns['ewww_image_optimizer'] = esc_html__( 'Image Optimizer', 'ewww-image-optimizer' );
				return $columns;
			}
			return esc_html__( 'Image Optimizer', 'ewww-image-optimizer' );
		}

		/**
		 * Outputs the image optimizer column data via ngg_manage_images_column_x_content hook.
		 *
		 * @global object $wpdb
		 *
		 * @param string $column_name The name of the current column.
		 * @param int    $id The image id for the current row.
		 * @return string The column output, potentially echoed instead.
		 */
		function ewww_manage_image_custom_column( $column_name, $id ) {
			// Once we've found our custom column (newer versions will be blank).
			if ( 'ewww_image_optimizer' === $column_name || ! $column_name ) {
				ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
				// Creating the 'registry' object for working with nextgen.
				$registry = C_Component_Registry::get_instance();
				// Creating a database storage object from the 'registry' object.
				$storage = $registry->get_utility( 'I_Gallery_Storage' );
				ob_start();
				if ( is_object( $id ) ) {
					$image = $id;
				} else {
					// Get an image object.
					$image = $storage->object->_image_mapper->find( $id );
				}
				echo '<div id="ewww-nextgen-status-' . (int) $image->pid . '">';
				if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_debug' ) && ewww_image_optimizer_function_exists( 'print_r' ) ) {
					$print_meta   = print_r( $image->meta_data, true );
					$print_meta   = preg_replace( array( '/ /', '/\n+/' ), array( '&nbsp;', '<br />' ), esc_html( $print_meta ) );
					$debug_button = __( 'Show Metadata', 'ewww-image-optimizer' );
					echo "<button type='button' class='ewww-show-debug-meta button button-secondary' data-id='" . (int) $image->pid . "' style='background-color:#a9c524;'>" . esc_html( $debug_button ) . '</button>' .
						"<div id='ewww-debug-meta-" . (int) $image->pid . "' style='font-size: 10px;padding: 10px;margin:3px -10px 10px;line-height: 1.1em;display: none;'>" . wp_kses_post( $print_meta ) . '</div>';
				}
				// Get the absolute path.
				$file_path = $storage->get_image_abspath( $image, 'full' );
				// Get the mimetype of the image.
				$type = ewww_image_optimizer_quick_mimetype( $file_path );
				// Check to see if we have a tool to handle the mimetype detected.
				if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_JPEGTRAN' ) ) {
					ewww_image_optimizer_tool_init();
					ewww_image_optimizer_notice_utils( 'quiet' );
				}
				$skip = ewww_image_optimizer_skip_tools();
				switch ( $type ) {
					case 'image/jpeg':
						// If jpegtran is missing, tell the user.
						if ( ! EWWW_IMAGE_OPTIMIZER_JPEGTRAN && ! $skip['jpegtran'] ) {
							/* translators: %s: name of a tool like jpegtran */
							$msg = '<div>' . sprintf( esc_html__( '%s is missing', 'ewww-image-optimizer' ), '<em>jpegtran</em>' ) . '</div>';
						}
						break;
					case 'image/png':
						// If the PNG tools are missing, tell the user.
						if ( ( ! $skip['optipng'] && ! EWWW_IMAGE_OPTIMIZER_OPTIPNG ) || ( ! $skip['pngout'] && ! EWWW_IMAGE_OPTIMIZER_PNGOUT ) ) {
							/* translators: %s: name of a tool like jpegtran */
							$msg = '<div>' . sprintf( esc_html__( '%s is missing', 'ewww-image-optimizer' ), '<em>optipng/pngout</em>' ) . '</div>';
						}
						break;
					case 'image/gif':
						// If gifsicle is missing, tell the user.
						if ( ! EWWW_IMAGE_OPTIMIZER_GIFSICLE && ! $skip['gifsicle'] ) {
							/* translators: %s: name of a tool like jpegtran */
							$msg = '<div>' . sprintf( esc_html__( '%s is missing', 'ewww-image-optimizer' ), '<em>gifsicle</em>' ) . '</div>';
						}
						break;
					default:
						$msg = '<div>' . esc_html__( 'Unsupported file type', 'ewww-image-optimizer' ) . '</div>';
				}
				// File isn't in a format we can work with, we don't work with strangers.
				if ( ! empty( $msg ) ) {
					if ( is_object( $id ) ) {
						return $msg;
					} else {
						ob_end_clean();
						echo wp_kses_post( $msg );
						return;
					}
				}
				// If we have metadata, populate db from meta.
				if ( ! empty( $image->meta_data['ewww_image_optimizer'] ) ) {
					$sizes = $storage->get_image_sizes( $image );
					$sizes = $this->maybe_get_more_sizes( $sizes, $image->meta_data );
					if ( ewww_image_optimizer_iterable( $sizes ) ) {
						foreach ( $sizes as $size ) {
							if ( 'full' === $size ) {
								$full_size = true;
							} elseif ( 'backup' === $size ) {
								continue;
							} else {
								$file_path = $storage->get_image_abspath( $image, $size );
								$full_size = false;
							}
							ewww_image_optimizer_update_file_from_meta( $file_path, 'nextgen', $image->pid, $size );
						}
					}
				}
				$backup_available = false;
				global $wpdb;
				$optimized_images = $wpdb->get_results( $wpdb->prepare( "SELECT image_size,orig_size,resize,converted,level,backup,updated FROM $wpdb->ewwwio_images WHERE attachment_id = %d AND gallery = 'nextgen' AND image_size <> 0 ORDER BY orig_size DESC", $image->pid ), ARRAY_A );
				if ( ! empty( $optimized_images ) ) {
					list( $detail_output, $converted, $backup_available ) = ewww_image_optimizer_custom_column_results( $image->pid, $optimized_images );
					echo wp_kses_post( $detail_output );
					// Display the optimization link with the appropriate text.
					$this->ewww_render_optimize_action_link( $image->pid, null, true, $backup_available );
				} elseif ( ewww_image_optimizer_image_is_pending( $image->pid, 'nextg-async' ) ) {
					echo '<div>' . esc_html__( 'In Progress', 'ewww-image-optimizer' ) . '</div>';
					// Otherwise, give the image size, and a link to optimize right now.
				} else {
					// Display the optimization link with the appropriate text.
					echo '<br>';
					$this->ewww_render_optimize_action_link( $image->pid, null, false, $backup_available );
				}
				echo '</div>';
				if ( is_object( $id ) ) {
					return ob_get_clean();
				} else {
					ob_end_flush();
				}
			} // End if().
		}

		/**
		 * Output the action link for the custom column.
		 *
		 * @global object $wpdb
		 *
		 * @param int|string $id The ID number of the nextgen image, or the string 'optimize'.
		 * @param object     $image A nextgen image object.
		 * @param bool       $optimized Optional. True if the image has already been optimized. Default false.
		 * @param bool       $restorable Optional. True if the image can be restored via the API. Default false.
		 */
		function ewww_render_optimize_action_link( $id, $image = null, $optimized = false, $restorable = false ) {
			if ( ! current_user_can( apply_filters( 'ewww_image_optimizer_manual_permissions', '' ) ) ) {
				return;
			}
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			if ( 'optimize' === $id && is_object( $image ) && ! empty( $image->pid ) ) {
				$id = $image->pid;
				global $wpdb;
				$optimized_images = $wpdb->get_results( $wpdb->prepare( "SELECT image_size,orig_size,resize,converted,level,backup,updated FROM $wpdb->ewwwio_images WHERE attachment_id = %d AND gallery = 'nextgen' AND image_size <> 0 ORDER BY orig_size DESC", $id ), ARRAY_A );
				if ( ! empty( $optimized_images ) ) {
					$optimized = true;
				}
			}
			$ewww_manual_nonce = wp_create_nonce( 'ewww-manual-' . $id );
			if ( $optimized ) {
				printf(
					'<a class="ewww-manual-optimize" data-id="%1$d" data-nonce="%2$s" href="' . esc_url( admin_url( 'admin.php?action=ewww_ngg_manual' ) ) . '&amp;ewww_manual_nonce=%2$s&amp;ewww_force=1&amp;ewww_attachment_ID=%1$d">%3$s</a>',
					(int) $id,
					esc_attr( $ewww_manual_nonce ),
					esc_html__( 'Re-optimize', 'ewww-image-optimizer' )
				);
				if ( $restorable ) {
					printf(
						'<br><a class="ewww-manual-cloud-restore" data-id="%1$d" data-nonce="%2$s" href="' . esc_url( admin_url( 'admin.php?action=ewww_ngg_cloud_restore' ) ) . '&amp;ewww_manual_nonce=%2$s&amp;ewww_attachment_ID=%1$d">%3$s</a>',
						(int) $id,
						esc_attr( $ewww_manual_nonce ),
						esc_html__( 'Restore original', 'ewww-image-optimizer' )
					);
				}
			} else {
				printf(
					'<a class="ewww-manual-optimize" data-id="%1$d" data-nonce="%2$s" href="' . esc_url( admin_url( 'admin.php?action=ewww_ngg_manual' ) ) . '&amp;ewww_manual_nonce=%2$s&amp;ewww_attachment_ID=%1$d">%3$s</a>',
					(int) $id,
					esc_attr( $ewww_manual_nonce ),
					esc_html__( 'Optimize now!', 'ewww-image-optimizer' )
				);
			}
		}

		/**
		 * Capture the output for the action link(s).
		 *
		 * @param int|string $id The ID number of the nextgen image, or the string 'optimize'.
		 * @param object     $image A nextgen image object.
		 * @return string The output from the action_link function.
		 */
		function ewww_render_optimize_action_link_capture( $id, $image = null ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			ob_start();
			$this->ewww_render_optimize_action_link( $id, $image, false, false );
			return ob_get_clean();
		}

		/**
		 * Append our action link to the list.
		 *
		 * @param array $actions A list of actions with to display under the image.
		 * @return array The updated list of actions.
		 */
		function ewww_manage_images_row_actions( $actions ) {
			$actions['optimize'] = array( &$this, 'ewww_render_optimize_action_link_capture' );
			return $actions;
		}

		/**
		 * Output the html for the bulk optimize page.
		 *
		 * @global string $eio_debug In-memory debug log.
		 */
		function ewww_ngg_bulk_preview() {
			if ( ! empty( $_REQUEST['doaction'] ) ) {
				if (
					empty( $_REQUEST['_wpnonce'] ) ||
					(
						! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'ngg_bulkgallery' ) &&
						! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'ngg_updategallery' )
					)
				) {
						ewwwio_debug_message( 'nonce verify failed' );
						return;
				}
				// If there is no requested bulk action, do nothing.
				if ( empty( $_REQUEST['bulkaction'] ) ) {
					return;
				}
				// If there is no media to optimize, do nothing.
				if ( ! is_array( $_REQUEST['doaction'] ) ) {
					return;
				}
			}
			list( $fullsize_count, $resize_count ) = ewww_image_optimizer_count_optimized( 'ngg' );
			// Make sure there are some attachments to process.
			if ( $fullsize_count < 1 ) {
				echo '<p>' . esc_html__( 'You do not appear to have uploaded any images yet.', 'ewww-image-optimizer' ) . '</p>';
				return;
			}
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Bulk Optimize', 'ewww-image-optimizer' ); ?></h1>
				<?php
				if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) ) {
					ewww_image_optimizer_cloud_verify( ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) );
					echo '<span id="ewww-bulk-credits-available">' . esc_html__( 'Image credits available:', 'ewww-image-optimizer' ) . ' ' . wp_kses_post( ewww_image_optimizer_cloud_quota() ) . '</span>';
				}
				echo '<div id="ewww-bulk-warning" class="ewww-bulk-info notice notice-warning"><p>' . esc_html__( 'Bulk Optimization will alter your original images and cannot be undone. Please be sure you have a backup of your images before proceeding.', 'ewww-image-optimizer' ) . '</p></div>';
				// Retrieve the value of the 'bulk resume' option and set the button text for the form to use.
				$resume = get_option( 'ewww_image_optimizer_bulk_ngg_resume' );
				if ( empty( $resume ) ) {
					$button_text = __( 'Start optimizing', 'ewww-image-optimizer' );
				} else {
					$button_text = __( 'Resume previous optimization', 'ewww-image-optimizer' );
				}
				$delay = ewww_image_optimizer_get_option( 'ewww_image_optimizer_delay' ) ? ewww_image_optimizer_get_option( 'ewww_image_optimizer_delay' ) : 0;
				/* translators: 1-4: number(s) of images */
				$selected_images_text = sprintf( __( '%1$d images have been selected, with %2$d resized versions.', 'ewww-image-optimizer' ), $fullsize_count, $resize_count );
				?>
					<div id="ewww-bulk-loading"></div>
					<div id="ewww-bulk-progressbar"></div>
					<div id="ewww-bulk-counter"></div>
			<form id="ewww-bulk-stop" style="display:none;" method="post" action="">
				<br /><input type="submit" class="button-secondary action" value="<?php esc_attr_e( 'Stop Optimizing', 'ewww-image-optimizer' ); ?>" />
			</form>
			<div id="ewww-bulk-widgets" class="metabox-holder" style="display:none">
				<div class="meta-box-sortables">
				<div id="ewww-bulk-last" class="postbox">
					<button type="button" class="ewww-handlediv button-link" aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Click to toggle', 'ewww-image-optimizer' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="ewww-hndle"><span><?php esc_html_e( 'Last Image Optimized', 'ewww-image-optimizer' ); ?></span></h2>
					<div class="inside"></div>
				</div>
				</div>
				<div class="meta-box-sortables">
				<div id="ewww-bulk-status" class="postbox">
					<button type="button" class="ewww-handlediv button-link" aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Click to toggle', 'ewww-image-optimizer' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="ewww-hndle"><span><?php esc_html_e( 'Optimization Log', 'ewww-image-optimizer' ); ?></span></h2>
					<div class="inside"></div>
				</div>
				</div>
			</div>
			<form id="ewww-bulk-controls" class="ewww-bulk-form">
				<p><label for="ewww-force" style="font-weight: bold"><?php esc_html_e( 'Force re-optimize', 'ewww-image-optimizer' ); ?></label><?php ewwwio_help_link( 'https://docs.ewww.io/article/65-force-re-optimization', '5bb640a7042863158cc711cd' ); ?>
					&emsp;<input type="checkbox" id="ewww-force" name="ewww-force">
					&nbsp;<?php esc_html_e( 'Previously optimized images will be skipped by default, check this box before scanning to override.', 'ewww-image-optimizer' ); ?>
					&nbsp;<a href="tools.php?page=ewww-image-optimizer-tools"><?php esc_html_e( 'View optimization history.', 'ewww-image-optimizer' ); ?></a>
				</p>
				<p>
					<label for="ewww-delay" style="font-weight: bold"><?php esc_html_e( 'Pause between images', 'ewww-image-optimizer' ); ?></label>&emsp;<input type="text" id="ewww-delay" name="ewww-delay" value="<?php echo (int) $delay; ?>"> <?php esc_html_e( 'in seconds, 0 = disabled', 'ewww-image-optimizer' ); ?>
				</p>
				<div id="ewww-delay-slider"></div>
			</form>
				<div id="ewww-bulk-forms">
			<p class="ewww-bulk-info"><?php echo esc_html( $selected_images_text ); ?></p>
			<form id="ewww-bulk-start" class="ewww-bulk-form" method="post" action="">
				<input type="submit" class="button-primary action" value="<?php echo esc_attr( $button_text ); ?>" />
			</form>
			<?php
			// If there is a previous bulk operation to resume, give the user the option to reset the resume flag.
			if ( ! empty( $resume ) ) {
				?>
				<p class="ewww-bulk-info" style="margin-top:3em;"><?php esc_html_e( 'Would like to clear the queue and start over?', 'ewww-image-optimizer' ); ?></p>
				<form id="ewww-bulk-reset" class="ewww-bulk-form" method="post" action="">
					<?php wp_nonce_field( 'ewww-image-optimizer-bulk-reset', 'ewww_wpnonce' ); ?>
					<input type="hidden" name="ewww_reset" value="1">
					<input type="submit" class="button-secondary action" value="<?php esc_attr_e( 'Clear Queue', 'ewww-image-optimizer' ); ?>" />
				</form>
				<?php
			}
			echo '</div>';
			if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_debug' ) ) {
				global $eio_debug;
				echo '<div style="clear:both;"></div>';
				echo '<p><strong>' . esc_html__( 'Debugging Information', 'ewww-image-optimizer' ) . ':</strong></p><div style="border:1px solid #e5e5e5;background:#fff;overflow:auto;height:300px;width:800px;">' . wp_kses_post( $eio_debug ) . '</div>';
			}
			echo '</div>';
			return;
		}

		/**
		 * Removes the nextgen jquery styling when it gets in the way.
		 */
		function ewww_ngg_style_remove() {
			wp_deregister_style( 'jquery-ui-nextgen' );
			wp_deregister_style( 'ngg_progressbar' );
			wp_deregister_style( 'nextgen_admin_css' );
			wp_deregister_style( 'pc-autoupdate-admin' );
			wp_deregister_script( 'nextgen_admin_js_atp' );
			wp_deregister_script( 'nextgen_admin_js' );
			wp_deregister_script( 'ngg_progressbar' );
			wp_deregister_script( 'frame_event_publisher' );
			wp_deregister_script( 'pc-autoupdate-admin' );
		}

		/**
		 * Prepares the javascript for a bulk operation.
		 *
		 * @global object $wpdb
		 *
		 * @param string $hook Identifier for the page being loaded.
		 */
		function ewww_ngg_bulk_script( $hook ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			ewwwio_debug_message( $hook );
			if ( strpos( $hook, 'ewww-ngg-bulk' ) === false ) {
				return;
			}
			$images = null;
			// See if the user wants to reset the previous bulk status.
			if (
				! empty( $_REQUEST['ewww_reset'] ) &&
				! empty( $_REQUEST['ewww_wpnonce'] ) &&
				wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk-reset' )
			) {
				update_option( 'ewww_image_optimizer_bulk_ngg_resume', '' );
			}
			if ( ! empty( $_REQUEST['bulkaction'] ) && 'bulk_optimize' !== $_REQUEST['bulkaction'] ) {
				return;
			}
			if ( ! empty( $_REQUEST['doaction'] ) && ! is_array( $_REQUEST['doaction'] ) ) {
				return;
			}
			// See if there is a previous operation to resume.
			$resume = get_option( 'ewww_image_optimizer_bulk_ngg_resume' );
			// If we've been given a bulk action to perform.
			if ( ! empty( $_REQUEST['doaction'] ) && ! empty( $_REQUEST['bulkaction'] ) && ! empty( $_REQUEST['bulk_type'] ) ) {
				// If we are optimizing a specific group of images.
				if ( 'images' === $_REQUEST['bulk_type'] && 'bulk_optimize' === $_REQUEST['bulkaction'] ) {
					ewwwio_debug_message( 'detected bulk action from manage images' );
					check_admin_referer( 'ngg_updategallery' );
					// Reset the resume status, not allowed here.
					update_option( 'ewww_image_optimizer_bulk_ngg_resume', '' );
					// Retrieve the image IDs from POST.
					$images = array_map( 'intval', $_REQUEST['doaction'] );
					ewwwio_debug_message( 'requested images: ' . implode( ',', $images ) );
				}
				// If we are optimizing a specific group of galleries.
				if ( 'galleries' === $_REQUEST['bulk_type'] && 'bulk_optimize' === $_REQUEST['bulkaction'] ) {
					ewwwio_debug_message( 'detected bulk action from manage galleries' );
					check_admin_referer( 'ngg_bulkgallery' );
					global $nggdb;
					// Reset the resume status, not allowed here.
					update_option( 'ewww_image_optimizer_bulk_ngg_resume', '' );
					$ids  = array();
					$gids = array_map( 'intval', $_REQUEST['doaction'] );
					ewwwio_debug_message( 'requested galleries: ' . implode( ',', $gids ) );
					// For each gallery we are given.
					foreach ( $gids as $gid ) {
						// Get a list of IDs.
						$gallery_list = $nggdb->get_gallery( $gid );
						// For each ID.
						foreach ( $gallery_list as $image ) {
							// Add it to the array.
							$images[] = $image->pid;
						}
					}
					ewwwio_debug_message( 'requested images: ' . implode( ',', $images ) );
				}
			} elseif ( ! empty( $resume ) ) {
				// Otherwise, if we have an operation to resume...
				// get the list of attachment IDs from the db.
				$images = get_option( 'ewww_image_optimizer_bulk_ngg_attachments' );
				// Otherwise, if we are on the standard bulk page, get all the images in the db.
			} elseif ( strpos( $hook, '_page_ewww-ngg-bulk' ) ) {
				global $wpdb;
				$images = $wpdb->get_col( "SELECT pid FROM $wpdb->nggpictures ORDER BY sortorder ASC" );
			} // End if().
			// Store the image IDs to process in the db.
			update_option( 'ewww_image_optimizer_bulk_ngg_attachments', $images, false );
			// Add the EWWW IO script.
			wp_enqueue_script( 'ewwwbulkscript', plugins_url( '/includes/eio-bulk.js', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE ), array( 'jquery', 'jquery-ui-progressbar', 'jquery-ui-slider', 'postbox', 'dashboard' ), EWWW_IMAGE_OPTIMIZER_VERSION );
			// Replacing the built-in nextgen styling rules for progressbar, partially because the bulk optimize page doesn't work without them.
			wp_deregister_style( 'ngg-jqueryui' );
			wp_deregister_style( 'ngg-jquery-ui' );
			wp_deregister_style( 'ngg_progressbar' );
			wp_deregister_style( 'nextgen_admin_css' );
			wp_deregister_style( 'pc-autoupdate-admin' );
			wp_deregister_script( 'nextgen_admin_js_atp' );
			wp_deregister_script( 'nextgen_admin_js' );
			wp_deregister_script( 'ngg_progressbar' );
			wp_deregister_script( 'frame_event_publisher' );
			wp_deregister_script( 'pc-autoupdate-admin' );
			add_action( 'admin_head', array( &$this, 'ewww_ngg_style_remove' ) );
			wp_register_style( 'jquery-ui-nextgen', plugins_url( '/includes/jquery-ui-1.10.1.custom.css', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE ) );
			// Enqueue the progressbar styling.
			wp_enqueue_style( 'jquery-ui-nextgen' );
			wp_add_inline_style( 'jquery-ui-nextgen', '.ui-widget-header { background-color: ' . ewww_image_optimizer_admin_background() . '; }' );
			// Include all the vars we need for javascript.
			wp_localize_script(
				'ewwwbulkscript',
				'ewww_vars',
				array(
					'_wpnonce'              => wp_create_nonce( 'ewww-image-optimizer-bulk' ),
					'gallery'               => 'nextgen',
					'attachments'           => count( $images ),
					'scan_fail'             => esc_html__( 'Operation timed out, you may need to increase the max_execution_time for PHP', 'ewww-image-optimizer' ),
					'operation_stopped'     => esc_html__( 'Optimization stopped, reload page to resume.', 'ewww-image-optimizer' ),
					'operation_interrupted' => esc_html__( 'Operation Interrupted', 'ewww-image-optimizer' ),
					'temporary_failure'     => esc_html__( 'Temporary failure, seconds left to retry:', 'ewww-image-optimizer' ),
					'remove_failed'         => esc_html__( 'Could not remove image from table.', 'ewww-image-optimizer' ),
					'optimized'             => esc_html__( 'Optimized', 'ewww-image-optimizer' ),
				)
			);
		}

		/**
		 * Start the bulk operation.
		 */
		function ewww_ngg_bulk_init() {
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			$output      = array();
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				$output['error'] = esc_html__( 'Access denied.', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			// Toggle the resume flag to indicate an operation is in progress.
			update_option( 'ewww_image_optimizer_bulk_ngg_resume', 'true' );
			$attachments = get_option( 'ewww_image_optimizer_bulk_ngg_attachments' );
			if ( ! is_array( $attachments ) && ! empty( $attachments ) ) {
				$attachments = unserialize( $attachments );
			}
			if ( ! is_array( $attachments ) ) {
				$output['error'] = esc_html__( 'Error retrieving list of images' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			$id            = array_shift( $attachments );
			$file          = $this->ewww_ngg_bulk_filename( $id );
			$loading_image = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
			if ( empty( $file ) ) {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
			} else {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$file</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}

		/**
		 * Retrieve the filename of the image being optimized.
		 *
		 * @param int $id The ID number of the image.
		 * @return string|bool The name of the current file or false.
		 */
		function ewww_ngg_bulk_filename( $id ) {
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );
			// Get an image object.
			$image = $storage->object->_image_mapper->find( $id );
			// Get the filename for the image, and output our current status.
			$file_path = esc_html( $storage->get_image_abspath( $image, 'full' ) );
			if ( ! empty( $file_path ) ) {
				return $file_path;
			} else {
				return false;
			}
		}

		/**
		 * Process each image in the bulk queue.
		 *
		 * @global bool $ewww_defer Set to false to avoid deferring image optimization.
		 */
		function ewww_ngg_bulk_loop() {
			global $ewww_defer;
			$ewww_defer  = false;
			$output      = array();
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				$output['error'] = esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			session_write_close();
			// Find out if our nonce is on it's last leg/tick.
			$tick = wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' );
			if ( 2 === $tick ) {
				$output['new_nonce'] = wp_create_nonce( 'ewww-image-optimizer-bulk' );
			} else {
				$output['new_nonce'] = '';
			}
			// Find out what time we started, in microseconds.
			$started = microtime( true );
			// Get the list of attachments remaining from the db.
			$attachments = get_option( 'ewww_image_optimizer_bulk_ngg_attachments' );
			$id          = array_shift( $attachments );
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );
			// Get an image object.
			$image       = $storage->object->_image_mapper->find( $id );
			$image       = $this->ewww_added_new_image( $image, $storage );
			$ewww_status = get_transient( 'ewww_image_optimizer_cloud_status' );
			if ( ! empty( $ewww_status ) && preg_match( '/exceeded/', $ewww_status ) ) {
				$output['error'] = esc_html__( 'License Exceeded', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			// Output the results of the optimization.
			$output['results'] = sprintf( '<p>' . esc_html__( 'Optimized image:', 'ewww-image-optimizer' ) . ' <strong>%s</strong><br>', esc_html( basename( $storage->object->get_image_abspath( $image, 'full' ) ) ) );
			if ( ewww_image_optimizer_iterable( $this->bulk_sizes ) ) {
				foreach ( $this->bulk_sizes as $size => $results_msg ) {
					if ( 'backup' === $size ) {
						continue;
					} elseif ( 'full' === $size ) {
						/* Translators: %s: The compression results/savings */
						$output['results'] .= sprintf( esc_html__( 'Full size - %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $results_msg ) );
					} elseif ( 'thumbnail' === $size ) {
						// Output the results of the thumb optimization.
						/* Translators: %s: The compression results/savings */
						$output['results'] .= sprintf( esc_html__( 'Thumbnail - %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $results_msg ) );
					} else {
						// Output savings for any other sizes, if they ever exist...
						$output['results'] .= ucfirst( $size ) . ' - ' . esc_html( $results_msg ) . '<br>';
					}
				}
				$this->bulk_sizes = array();
			}
			// Output how much time we spent.
			$elapsed = microtime( true ) - $started;
			/* Translators: %s: The localized number of seconds */
			$output['results']  .= sprintf( esc_html( _n( 'Elapsed: %s second', 'Elapsed: %s seconds', $elapsed, 'ewww-image-optimizer' ) ) . '</p>', number_format_i18n( $elapsed, 2 ) );
			$output['completed'] = 1;
			// Store the list back in the db.
			update_option( 'ewww_image_optimizer_bulk_ngg_attachments', $attachments, false );
			if ( ! empty( $attachments ) ) {
				$next_attachment = array_shift( $attachments );
				$next_file       = $this->ewww_ngg_bulk_filename( $next_attachment );
				$loading_image   = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
				if ( $next_file ) {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$next_file</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
				} else {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
				}
			} else {
				$output['done'] = 1;
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}

		/**
		 * Finish the bulk operation.
		 */
		function ewww_ngg_bulk_cleanup() {
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				ewwwio_ob_clean();
				wp_die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
			}
			// Reset all the bulk options in the db.
			update_option( 'ewww_image_optimizer_bulk_ngg_resume', '' );
			update_option( 'ewww_image_optimizer_bulk_ngg_attachments', '', false );
			ewwwio_ob_clean();
			wp_die( '<p><b>' . esc_html__( 'Finished Optimization!', 'ewww-image-optimizer' ) . '</b></p>' );
		}

		/**
		 * Insert a bulk optimize option in the actions list for the gallery and image management pages (via javascript, since we have no hooks).
		 */
		function ewww_ngg_bulk_actions_script() {
			global $current_screen;
			if ( ( strpos( $current_screen->id, 'nggallery-manage-images' ) === false && strpos( $current_screen->id, 'nggallery-manage-gallery' ) === false ) || ! current_user_can( apply_filters( 'ewww_image_optimizer_bulk_permissions', '' ) ) ) {
				return;
			}
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('select[name^="bulkaction"] option:last-child').after('<option value="bulk_optimize"><?php esc_html_e( 'Bulk Optimize', 'ewww-image-optimizer' ); ?></option>');
				});
			</script>
			<?php
		}

		/**
		 * Handles the bulk actions POST.
		 */
		function ewww_ngg_bulk_action_handler() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			if (
				empty( $_REQUEST['_wpnonce'] ) ||
				(
					! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'ngg_bulkgallery' ) &&
					! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'ngg_updategallery' )
				)
			) {
				ewwwio_debug_message( 'nonce verify failed' );
				return;
			}
			// If the requested page is blank, or not a bulk_optimize, do nothing.
			if ( empty( $_REQUEST['nggpage'] ) || empty( $_REQUEST['bulkaction'] ) || 'bulk_optimize' !== $_REQUEST['bulkaction'] ) {
				return;
			}
			// If there is no media to optimize, do nothing.
			if ( empty( $_REQUEST['doaction'] ) || ! is_array( $_REQUEST['doaction'] ) ) {
				return;
			}
			// If the requested page does not match, do nothing.
			if ( 'manage-galleries' !== $_REQUEST['nggpage'] && 'manage-images' !== $_REQUEST['nggpage'] ) {
				return;
			}

			$type = 'images';
			if ( 'manage-galleries' === $_REQUEST['nggpage'] ) {
				$type = 'galleries';
			}
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'       => 'ewww-ngg-bulk',
						'_wpnonce'   => sanitize_key( $_REQUEST['_wpnonce'] ),
						'bulk_type'  => $type,
						'bulkaction' => 'bulk_optimize',
						'doaction'   => array_map( 'intval', wp_unslash( $_REQUEST['doaction'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					),
					admin_url( 'admin.php' )
				)
			);
			ewwwio_memory( __METHOD__ );
			exit();
		}
	}
	// Initialize the plugin and the class.
	global $ewwwngg;
	$ewwwngg = new EWWW_Nextgen();
} // End if().

if ( ! empty( $_REQUEST['page'] ) && 'ngg_other_options' !== $_REQUEST['page'] && ! class_exists( 'EWWWIO_Gallery_Storage' ) && class_exists( 'Mixin' ) && class_exists( 'C_Gallery_Storage' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	/**
	 * Extension for NextGEN image generation.
	 */
	class EWWWIO_Gallery_Storage extends Mixin {
		/**
		 * Generates an image size (via the parent class) and then optimizes it.
		 *
		 * @param int|object $image A nextgen image object or the image ID number.
		 * @param string     $size The name of the size.
		 * @param array      $params Image generation parameters: width, height, and crop_frame.
		 * @param bool       $skip_defaults I have no idea, ask the NextGEN devs...
		 */
		function generate_image_size( $image, $size, $params = null, $skip_defaults = false ) {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_CLOUD' ) ) {
				ewww_image_optimizer_cloud_init();
			}
			$success = $this->call_parent( 'generate_image_size', $image, $size, $params, $skip_defaults );
			if ( $success ) {
				$filename = $success->fileName;
				ewww_image_optimizer( $filename );
				ewwwio_debug_message( "nextgen dynamic thumb saved via extension: $filename" );
				$image_size = ewww_image_optimizer_filesize( $filename );
				ewwwio_debug_message( "optimized size: $image_size" );
			}
			ewww_image_optimizer_debug_log();
			ewwwio_memory( __METHOD__ );
			return $success;
		}
	}
	$storage = C_Gallery_Storage::get_instance();
	$storage->get_wrapped_instance()->add_mixin( 'EWWWIO_Gallery_Storage' );
}
