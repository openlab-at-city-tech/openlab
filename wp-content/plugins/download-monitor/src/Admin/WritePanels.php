<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Admin class.
 *
 * Class that handles the edit.php page for the download post type.
 */
class DLM_Admin_Writepanels {

	/**
	 * The Download CPT
	 *
	 * @var object
	 */
	private $download_post = null;

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'add_meta_boxes_dlm_download', array( $this, 'add_meta_boxes' ), 15 );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 100, 2 );
		add_action( 'dlm_save_meta_boxes', array( $this, 'save_meta_boxes' ), 100, 2 );
		add_action( 'wp_ajax_dlm_upload_file', array( $this, 'upload_file' ) );
		add_filter( 'redirect_post_location', array( $this, 'redirect_no_title' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'notice_no_title' ), 8 );
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param WP_Post $post The post object.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes( $post ) {
		// We remove the Publish metabox and add to our queue.
		remove_meta_box( 'submitdiv', 'dlm_download', 'side' );
		global $pagenow;
		// If we are not on the new post page, we need to retrieve the download post, else it will trigger Exception.
		if ( 'post-new.php' !== $pagenow ) {
			if ( ! isset( $this->download_post ) || $post->ID !== $this->download_post->get_id() ) {
				if ( ! isset( $GLOBALS['dlm_download'] ) ) {
					try {
						$this->download_post = download_monitor()->service( 'download_repository' )->retrieve_single( $post->ID );
					} catch ( Exception $e ) {
						$this->download_post = new DLM_Download();
					}
				} else {
					$this->download_post = $GLOBALS['dlm_download'];
				}
			}
		}

		$meta_boxes = apply_filters(
			'dlm_download_metaboxes',
			array(
				array(
					'id'       => 'submitdiv',
					'title'    => esc_html__( 'Publish' ),
					'callback' => 'post_submit_meta_box',
					'screen'   => 'dlm_download',
					'context'  => 'side',
					'priority' => 1,
				),
				array(
					'id'       => 'download-monitor-information',
					'title'    => esc_html__( 'Download Information', 'download-monitor' ),
					'callback' => array( $this, 'download_information' ),
					'screen'   => 'dlm_download',
					'context'  => 'side',
					'priority' => 5,
				),
				array(
					'id'       => 'download-monitor-options',
					'title'    => esc_html__( 'Download Options', 'download-monitor' ),
					'callback' => array( $this, 'download_options' ),
					'screen'   => 'dlm_download',
					'context'  => 'side',
					'priority' => 10,
				),
				array(
					'id'       => 'download-monitor-file',
					'title'    => esc_html__( 'Downloadable Files/Versions', 'download-monitor' ),
					'callback' => array( $this, 'download_files' ),
					'screen'   => 'dlm_download',
					'context'  => 'normal',
					'priority' => 20,
				),
			)
		);

		uasort( $meta_boxes, array( 'DLM_Admin_Helper', 'sort_data_by_priority' ) );

		foreach ( $meta_boxes as $metabox ) {
			// Priority is left out as we prioritise based on our sorting function.
			add_meta_box( $metabox['id'], $metabox['title'], $metabox['callback'], $metabox['screen'], $metabox['context'], 'high' );
		}

		remove_meta_box( 'postexcerpt', 'dlm_download', 'normal' );
	}

	/**
	 * Hide the Custom Fields metabox by default. Users can enable it from Screen Options.
	 *
	 * @param array     $hidden The default hidden metaboxes.
	 * @param WP_Screen $screen The current screen.
	 *
	 * @access public
	 * @return array
	 */
	public function default_hidden_meta_boxes( $hidden, $screen ) {
		if ( 'dlm_download' === $screen->id && ! in_array( 'postcustom', $hidden, true ) ) {
			$hidden[] = 'postcustom';
		}

		return $hidden;
	}

	/**
	 * download_information function.
	 *
	 * @access public
	 *
	 * @param  WP_Post $post
	 *
	 * @return void
	 */
	public function download_information( $post ) {
		echo '<div class="dlm_information_panel">';

		try {
			/** @var DLM_Download $download */
			if ( ! isset( $this->download_post ) || $post->ID !== $this->download_post->get_id() ) {
				if ( ! isset( $GLOBALS['dlm_download'] ) ) {
					$this->download_post = download_monitor()->service( 'download_repository' )->retrieve_single( $post->ID );
				} else {
					$this->download_post = $GLOBALS['dlm_download'];
				}
			}
		} catch ( Exception $e ) {
			$this->download_post = new DLM_Download();
			$this->download_post->set_id( $post->ID );
			$this->download_post->set_post( $post );
		}

		echo '<div id="dlm-download-information-app"></div>';

		wp_add_inline_script(
			'dlm_download_information',
			'window.dlmDownloadInformation = ' . wp_json_encode(
				array(
					'downloadId' => $this->download_post->get_id(),
					'url'        => $this->download_post->get_the_download_link(),
					'shortcode'  => '[download id="' . $this->download_post->get_id() . '"]',
					'strings'    => array(
						'url'       => __( 'URL', 'download-monitor' ),
						'shortcode' => __( 'Shortcode', 'download-monitor' ),
						'copy'      => __( 'Copy', 'download-monitor' ),
						'copied'    => __( 'Copied', 'download-monitor' ),
					),
				)
			) . ';',
			'before'
		);

		echo '</div>';
	}

	/**
	 * download_options function.
	 *
	 * @access public
	 *
	 * @param  WP_Post $post
	 *
	 * @return void
	 */
	public function download_options( $post ) {
		try {
			/** @var DLM_Download $download */
			if ( ! isset( $this->download_post ) || $post->ID !== $this->download_post->get_id() ) {
				if ( ! isset( $GLOBALS['dlm_download'] ) ) {
					$this->download_post = download_monitor()->service( 'download_repository' )->retrieve_single( $post->ID );
				} else {
					$this->download_post = $GLOBALS['dlm_download'];
				}
			}
		} catch ( Exception $e ) {
			$this->download_post = new DLM_Download();
		}

		echo '<div class="dlm_options_panel">';

		do_action( 'dlm_options_start', $this->download_post->get_id(), $this->download_post );

		echo '<p class="form-field form-field-checkbox">
			<input type="checkbox" name="_featured" id="_featured" ' . checked( true, $this->download_post->is_featured(), false ) . ' />
			<label for="_featured">' . esc_html__( 'Featured download', 'download-monitor' ) . '</label>
			<span class="dlm-description">' . esc_html__( 'Mark this download as featured. Used by shortcodes and widgets.', 'download-monitor' ) . '</span>
		</p>';

		echo '<p class="form-field form-field-checkbox">
			<input type="checkbox" name="_members_only" id="_members_only" ' . checked( true, $this->download_post->is_members_only(), false ) . ' />
			<label for="_members_only">' . esc_html__( 'Members only', 'download-monitor' ) . '</label>
			<span class="dlm-description">' . esc_html__( 'Only logged in users will be able to access the file via a download link if this is enabled.', 'download-monitor' ) . '</span>
		</p>';

		echo '<p class="form-field form-field-checkbox">
			<input type="checkbox" name="_redirect_only" id="_redirect_only" ' . checked( true, $this->download_post->is_redirect_only(), false ) . ' />
			<label for="_redirect_only">' . esc_html__( 'Redirect to file', 'download-monitor' ) . '</label>
			<span class="dlm-description">' . wp_kses_post( __( 'Don\'t force download. If the <code>dlm_uploads</code> folder is protected you may need to move your file.', 'download-monitor' ) ) . '</span><span class="dlm-open-in-new-tab" style="' . ( $this->download_post->is_redirect_only() ? 'display:inline-block;' : 'display:none;' ) . '"><input type="checkbox" name="_new_tab" id="_new_tab" ' . checked( true, $this->download_post->is_new_tab(), false ) . ' /><label for="_new_tab">' . esc_html__( 'Open in new tab', 'download-monitor' ) . '</label></span>
		</p>';

		do_action( 'dlm_options_end', $this->download_post->get_id(), $this->download_post );

		echo '</div>';
	}

	/**
	 * Downloadable Files metabox content.
	 *
	 * @access public
	 * @return void
	 */
	public function download_files() {
		global $post;

		/** @var DLM_Download $download */
		try {
			if ( ! isset( $this->download_post ) || $post->ID !== $this->download_post->get_id() ) {
				if ( ! isset( $GLOBALS['dlm_download'] ) ) {
					$this->download_post = download_monitor()->service( 'download_repository' )->retrieve_single( $post->ID );
				} else {
					$this->download_post = $GLOBALS['dlm_download'];
				}
			}
		} catch ( Exception $e ) {
			$download = new DLM_Download();
		}

		wp_nonce_field( 'save_meta_data', 'dlm_nonce' );
		?>
		<div class="download_monitor_files dlm-metaboxes-wrapper">
			<?php
			echo '<input type="hidden" name="dlm_post_id" id="dlm-post-id" value="' . esc_attr( $post->ID ) . '"/>';
			echo '<input type="hidden" name="dlm_post_id" id="dlm-plugin-url" value="' . esc_attr( download_monitor()->get_plugin_url() ) . '"/>';
			echo '<input type="hidden" name="dlm_post_id" id="dlm-ajax-nonce-add-file" value="' . esc_attr( wp_create_nonce( 'add-file' ) ) . '"/>';
			echo '<input type="hidden" name="dlm_post_id" id="dlm-ajax-nonce-remove-file" value="' . esc_attr( wp_create_nonce( 'remove-file' ) ) . '"/>';

			do_action( 'dlm_download_monitor_files_writepanel_start', $this->download_post );
			?>
			<?php
			$versions = array();
			if ( null !== $this->download_post ) {
				$versions = $this->download_post->get_versions();
			}
			$upload_handler_class = ( ! empty( $versions ) ) ? 'hidden' : '';
			?>
			<div id="dlm-new-upload" class="
			<?php
			echo esc_attr( $upload_handler_class );
			?>
			">
				<div class="dlm-uploading-file hidden">
					<label>
					<?php
						esc_html_e( 'Uploading file:', 'download-monitor' )
					?>
						<span></span></label>
					<label class="dlm-file-uploaded hidden">
					<?php
						esc_html_e( 'File uploaded.', 'download-monitor' )
					?>
					</label>
					<div class="dlm-uploading-progress-bar"></div>
				</div>
				<div id="plupload-upload-ui" class="hide-if-no-js drag-drop">
					<div id="drag-drop-area" style="position: relative;">
						<div class="drag-drop-inside">
							<p class="drag-drop-info" style="letter-spacing: 1px;font-size: 10pt">
							<?php
								esc_html_e( 'Drag & Drop here to create a new version', 'download-monitor' );
							?>
							</p>
							<p>
							</p>
							<p>— or —</p>
							<p>
								<?php
								$buttons = array(
									'upload_file'     => array(
										'text' => __( 'Upload file', 'download-monitor' ),
									),
									'media_library'   => array(
										'text' => __( 'Media Library', 'download-monitor' ),
										'data' => array(
											'choose' => __( 'Choose a file', 'download-monitor' ),
											'update' => __( 'Insert file URL', 'download-monitor' ),
										),
									),
									'external_source' => array(
										'text' => __( 'Custom URL', 'download-monitor' ),
									),
								);

								if ( ! get_option( 'dlm_turn_off_file_browser', true ) ) {
									$buttons['browse_for_file'] = array( 'text' => __( 'Browse for file', 'download-monitor' ) );
								}

								// Unset the browse for file button if the constant is defined and set to false.
								if ( defined( 'DLM_FILE_BROWSER' ) && ! DLM_FILE_BROWSER ) {
									unset( $buttons['browse_for_file'] );
								}

								$buttons = apply_filters( 'dlm_downloadable_file_version_buttons', $buttons );

								foreach ( $buttons as $key => $button ) {
									echo '<a href="#" ' . ( 'upload_file' === $key ? 'id="plupload-browse-button"' : '' ) . ' class="button dlm_' . esc_attr( $key ) . '" ';
									if ( ! empty( $button['data'] ) ) {
										foreach ( $button['data'] as $data_key => $data_value ) {
											echo 'data-' . esc_attr( $data_key ) . '="' . esc_attr( $data_value ) . '" ';
										}
									}
									echo '>' . esc_html( $button['text'] ) . '</a> ';
								}
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="dlm-metaboxes dlm-versions-tab" 
			<?php
			echo( ! empty( $versions ) ? '' : 'style="display:none;"' );
			?>
			>
				<p>
					<strong>
					<?php
						printf( wp_kses_post( 'Your version(s) <span class="dlm-versions-number">( %s )</span>', 'download-monitor' ), ( ! empty( $versions ) ? count( $versions ) : 1 ) );
					?>
					</strong>
				</p>
				<p class="toolbar">
					<a href="#" class="button plus add_file">
					<?php
						echo esc_html__( 'Add file', 'download-monitor' );
					?>
					</a>
				</p>
			</div>
			<div class="dlm-metaboxes downloadable_files">
				<?php
				$i = - 1;

				// $versions declared above.
				if ( $versions ) {
					$paths        = array();
					$file_browser = defined( 'DLM_FILE_BROWSER' ) ? ! (bool) DLM_FILE_BROWSER : get_option( 'dlm_turn_off_file_browser', true );

					/** @var DLM_Download_Version $version */
					foreach ( $versions as $version ) {
						++$i;
						$paths = array_merge( $paths, $version->get_mirrors() );
						download_monitor()->service( 'view_manager' )->display(
							'meta-box/version',
							array(
								'version_increment'   => $i,
								'file_id'             => $version->get_id(),
								'file_version'        => $version->get_version(),
								'file_post_date'      => $version->get_date(),
								'file_download_count' => $version->get_download_count(),
								'meta_download_count' => $version->get_meta_download_count(),
								'file_urls'           => $version->get_mirrors(),
								'version'             => $version,
								'date_format'         => get_option( 'date_format' ),
								'file_browser'        => $file_browser,
							)
						);
					}
				}
				?>
			</div>
			<?php

			// Check if there are any non-allowed paths.
			if ( ! empty( $paths ) ) {
				$new_path      = false;
				$new_paths     = array();
				$allowed_paths = DLM_Downloads_Path_Helper::get_all_paths();
				foreach ( $paths as $file_path ) {
					// Get file path, remote file check and restriction.
					list( $file_path, $remote_file, $restriction ) = download_monitor()->service( 'file_manager' )->get_secure_path( $file_path );
					// If remote file don't check for allowed path.
					if ( $remote_file ) {
						continue;
					}
					if ( ! $file_path ) {
						continue;
					}
					$f_path = str_replace( DLM_Utils::basename( $file_path ), '', $file_path );
					// Check if the path is in the allowed paths and has not been added to the new paths array.
					if ( $restriction && ! in_array( $f_path, $new_paths ) ) {
						$new_paths[] = $f_path;
					}
				}
				// If there are new paths, display a notice.
				if ( ! empty( $new_paths ) ) {
					$html = '';
					// Default notice to be shown.
					$notice = sprintf(
						esc_html__( 'You\'re trying to serve files from your server that are not in the enabled allowed paths. If you want to learn more about this, %1$sclick here%2$s', 'download-monitor' ),
						'<a href="https://www.download-monitor.com/kb/add-path/" target="_blank">',
						'</a>'
					);
					if ( is_multisite() && ! current_user_can( 'manage_network' ) ) {
						// Notice to be shown if the installation is a multisite.
						$notice = sprintf(
							esc_html__(
								'You\'re trying to serve files from your server that are not in the enabled allowed paths. Please contact the network administrator to add or enable the path(s) for your website. 
                 If you want to learn more about this, %1$sclick here%2$s',
								'download-monitor'
							),
							'<a href="https://www.download-monitor.com/kb/add-path/" target="_blank">',
							'</a>'
						);
					}
					// Cycle through new paths and display them.
					foreach ( $new_paths as $new_path ) {
						$exists = false;
						// Check if the path is already in the allowed paths but is disabled.
						foreach ( $allowed_paths as $a_path ) {
							if ( str_replace( DIRECTORY_SEPARATOR, '/', $a_path['path_val'] ) === str_replace( DIRECTORY_SEPARATOR, '/', $new_path ) ) {
								$exists = true;
							}
						}
						// If it is a multisite installation, only the network admin can add paths.
						if (  is_multisite() && ! current_user_can( 'manage_network' ) ) {
							$html .= '<p class="dlm-restricted-path__recommended_allowed_path" >'
									. esc_html__( 'Recommended path:', 'download-monitor' ) .
									' <code>' . esc_html( $new_path ) . '</code>&nbsp;&nbsp;&nbsp;</p>';
						} else {
							$html .= '<p class="dlm-restricted-path__recommended_allowed_path" >';
							// If the path is not in the allowed paths display a button to add it.
							if ( ! $exists ) {
								$html .= esc_html__( 'Recommended path:', 'download-monitor' ) .
										' <code>' . esc_html( $new_path ) . '</code>&nbsp;&nbsp;&nbsp;<button class="button button-primary" id="dlm-add-recommended-path" data-path="' . esc_attr( $new_path ) . '" data-security="' . wp_create_nonce( 'dlm-ajax-nonce' ) . '">' .
										esc_html__( 'Add path', 'download-monitor' ) . '</button></p>';
								$html .= '<p class="dlm-restricted-path__description">';
								$html .= esc_html__( 'This will add the download path to the "Approved Download Path" list. ', 'download-monitor' );
							} else { // If the path is in the allowed paths but is disabled display a button to enable it.
								$html .= esc_html__( 'Path:', 'download-monitor' ) .
										' <code>' . esc_html( $new_path ) . '</code>&nbsp;&nbsp;&nbsp;<button class="button button-primary" id="dlm-enable-path" data-path="' . esc_attr( $new_path ) . '" data-security="' . wp_create_nonce( 'dlm-ajax-nonce' ) . '">' .
										esc_html__( 'Enable path', 'download-monitor' ) . '</button></p>';
								$html .= '<p class="dlm-restricted-path__description">';
								$html .= esc_html__( 'This will enable the download path from the "Approved Download Path" list. ', 'download-monitor' );
							}
							$html .= '</p>';
						}
					}

					echo '<div class="dlm-restricted-path notice notice-warning">';
					echo '<h4 class="dlm-restricted-path__restricted_file_path">' . wp_kses_post( $notice ) . '</h4>';
					echo wp_kses_post( $html );
					echo '</div>';
				}
			}
			?>
			<?php
			do_action( 'dlm_download_monitor_files_writepanel_end', $this->download_post );
			?>
			<div id="dlm-file-browser-root"></div>

		</div>
		<?php
	}

	/**
	 * Add error query arg to redirect when title is missing, and force draft so the download isn't published.
	 * $_POST is still available in this filter — same request as the save.
	 */
	public function redirect_no_title( $location, $post_id ) {
		static $processed = false;
		if ( $processed ) {
			return $location;
		}
		if ( 'dlm_download' !== get_post_type( $post_id ) ) {
			return $location;
		}
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( ! isset( $_POST['post_title'] ) || '' !== trim( sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) ) ) {
			return $location;
		}
		$processed = true;
		// If dlm_nonce is still present, save_post didn't run — save meta data (files, versions, options) now.
		// If save_post already ran, it unsets dlm_nonce, so we skip to avoid double-saving.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_POST['dlm_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['dlm_nonce'] ), 'save_meta_data' ) ) {
			unset( $_POST['dlm_nonce'] );
			$post = get_post( $post_id );
			if ( $post ) {
				$this->save_meta_boxes( $post_id, $post );
			}
		}
		// Force draft and explicitly clear the title so WordPress doesn't show "Auto Draft".
		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft', 'post_title' => '' ) );
		return add_query_arg( 'dlm_error', 'no_title', remove_query_arg( 'message', $location ) );
	}

	/**
	 * Show error notice — priority 8, before DLM removes admin_notices at priority 9.
	 */
	public function notice_no_title() {
		$screen = get_current_screen();
		if ( ! $screen || 'dlm_download' !== $screen->post_type ) {
			return;
		}
		if ( isset( $_GET['dlm_error'] ) && 'no_title' === $_GET['dlm_error'] ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Please add a title before publishing the download.', 'download-monitor' ) . '</p></div>';
		}
	}

	/**
	 * save_post function.
	 *
	 * @access public
	 *
	 * @param  int     $post_id
	 * @param  WP_Post $post
	 *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		// validate nonce.
		// phpcs:ignore
		if ( empty( $_POST['dlm_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['dlm_nonce'] ), 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type != 'dlm_download' ) {
			return;
		}

		// unset nonce because it's only valid of 1 post
		unset( $_POST['dlm_nonce'] );

		do_action( 'dlm_save_meta_boxes', $post_id, $post );
	}

	/**
	 * save function.
	 *
	 * @access public
	 *
	 * @param  int     $post_id
	 * @param  WP_Post $post
	 *
	 * @return void
	 */
	public function save_meta_boxes( $post_id, $post ) {
		if ( 'dlm_download' !== $post->post_type ) {
			return;
		}
		/**
		 * Fetch old download object
		 * There are certain props we don't need to manually persist here because WP does this automatically for us.
		 * These props are:
		 * - Download Title
		 * - Download Status
		 * - Download Author
		 * - Download Description & Excerpt
		 * - Download Categories
		 * - Download Tags
		 */
		/** @var DLM_Download $download */
		try {
			if ( isset( $GLOBALS['dlm_download'] ) ) {
				$this->download_post = $GLOBALS['dlm_download'];
			} elseif ( ! isset( $this->download_post ) || $post->ID !== $this->download_post->get_id() ) {
				$this->download_post = download_monitor()->service( 'download_repository' )->retrieve_single( $post_id );
			}
		} catch ( Exception $e ) {
			// download not found, no point in continuing
			return;
		}
		// set the 'Download Options'
		$this->download_post->set_featured( ( isset( $_POST['_featured'] ) ) );
		$this->download_post->set_members_only( ( isset( $_POST['_members_only'] ) ) );
		$this->download_post->set_redirect_only( ( isset( $_POST['_redirect_only'] ) ) );
		$this->download_post->set_new_tab( ( isset( $_POST['_new_tab'] ) ) );
		$total_meta_download_count = 0;

		// Process files
		if ( isset( $_POST['downloadable_file_id'] ) ) {
			// gather post data we will sanitize in for becase each variable is an array.
			// phpcs:disable
			$downloadable_file_id             = $_POST['downloadable_file_id'];
			$downloadable_file_menu_order     = $_POST['downloadable_file_menu_order'];
			$downloadable_file_version        = $_POST['downloadable_file_version'];
			$downloadable_file_urls           = wp_unslash( $_POST['downloadable_file_urls'] );
			$downloadable_file_download_count = array();
			$downloadable_file_date           = '';
			$downloadable_file_date_hour      = array();
			$downloadable_file_date_minute    = array();
			if ( apply_filters( 'dlm_show_version_extra_fields', false ) ) {
				$downloadable_file_download_count = isset( $_POST['downloadable_file_download_count'] ) ? $_POST['downloadable_file_download_count'] : array();
				$downloadable_file_date           = isset( $_POST['downloadable_file_date'] ) ? $_POST['downloadable_file_date'] : '';
				$downloadable_file_date_hour      = isset( $_POST['downloadable_file_date_hour'] ) ? $_POST['downloadable_file_date_hour'] : array();
				$downloadable_file_date_minute    = isset( $_POST['downloadable_file_date_minute'] ) ? $_POST['downloadable_file_date_minute'] : array();
			}

			// loop
			for ( $i = 0; $i <= max( array_keys( $downloadable_file_id ) ); $i ++ ) {
				// file id must be set in post data
				if ( ! isset( $downloadable_file_id[ $i ] ) ) {
					continue;
				}

				// sanatize post data
				$file_id             = absint( $downloadable_file_id[ $i ] );
				$file_menu_order     = absint( $downloadable_file_menu_order[ $i ] );
				$file_version        = sanitize_text_field( $downloadable_file_version[ $i ] );
				$file_download_count = isset( $downloadable_file_download_count[ $i ] ) ? sanitize_text_field( $downloadable_file_download_count[ $i ] ) : '';
				$file_date_str       = ! empty( $downloadable_file_date[ $i ] ) ? sanitize_text_field( $downloadable_file_date[ $i ] ) : '';
				$file_date_hour      = ! empty( $downloadable_file_date_hour[ $i ] ) ? absint( $downloadable_file_date_hour[ $i ] ) : 0;
				$file_date_minute    = ! empty( $downloadable_file_date_minute[ $i ] ) ? absint( $downloadable_file_date_minute[ $i ] ) : 0;
				$files               = array_filter( array_map( 'trim', explode( "\n", $downloadable_file_urls[ $i ] ) ) );
				$secured_files       = array();
				$file_manager        = new DLM_File_Manager();

				// only continue if there's a file_id
				if ( ! $file_id ) {
					continue;
				}

				try {
					// create version object
					/** @var DLM_Download_Version $version */
					$version = download_monitor()->service( 'version_repository' )->retrieve_single( $file_id );

					// set post data in version object
					$version->set_author( get_current_user_id() );
					$version->set_menu_order( $file_menu_order );
					$version->set_version( $file_version );
					// If date came from the form (extra fields enabled), use it; otherwise preserve existing.
					if ( ! empty( $file_date_str ) ) {
						$version->set_date( new DateTime( $file_date_str . ' ' . $file_date_hour . ':' . $file_date_minute . ':00' ) );
					} elseif ( ! $version->get_date() ) {
						$version->set_date( new DateTime() );
					}
					$version->set_mirrors( $files );

					// Only update download count if submitted (extra fields enabled and value entered).
					if ( '' !== $file_download_count ) {
						$version->set_meta_download_count( $file_download_count );
					}

					// persist version
					download_monitor()->service( 'version_repository' )->persist( $version );
					// add version download count to total download count
					$total_meta_download_count += absint( $version->get_meta_download_count() );
				} catch ( Exception $e ) {
				}

				// do dlm_save_downloadable_file action
				do_action( 'dlm_save_downloadable_file', $file_id, $i );
			}
		}

		// sync download_count
		$this->download_post->set_meta_download_count( $total_meta_download_count );
		// persist download
		download_monitor()->service( 'download_repository' )->persist( $this->download_post );

		// do dlm_save_metabox action
		do_action( 'dlm_save_metabox', $post_id, $post, $this->download_post );
	}

	/**
	 * Directly upload file
	 *
	 * @return void
	 *
	 * @since 4.5.4
	 */
	public function upload_file() {
		if ( ! current_user_can( 'upload_file' ) ) {
			wp_send_json_error( array( 'errorMessage' => esc_html__( 'You are not allowed to upload files.', 'download-monitor' ) ) );
		}

		$uploadedfile         = $_FILES['file'];
		$image_url            = $uploadedfile['tmp_name'];
		$wp_allowed           = wp_check_filetype( $image_url );
		$dlm_denied_filetypes = array( 'php', 'htaccess' );
		// If not allowed file type, return error.
		if ( ! $wp_allowed['ext'] || in_array( $wp_allowed['ext'], $dlm_denied_filetypes ) ) {
			wp_send_json_error( array( 'errorMessage' => esc_html__( 'File type not allowed.', 'download-monitor' ) ) );
		}

		$upload_dir = wp_upload_dir();

		$image_data = file_get_contents( $image_url );

		$filename = $uploadedfile['name'];

		$file = $upload_dir['basedir'] . '/dlm_uploads/' . date( 'Y/m/' ) . $filename;

		if ( ! file_put_contents( $file, $image_data ) ) {
			wp_send_json_error( array( 'errorMessage' => esc_html__( 'Failed to write the file at: ', 'download-monitor' ) . $file ) );
		}

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );

		if ( ! is_wp_error( $attach_id ) ) {
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		} else {
			wp_send_json_error( array( 'errorMessage' => $attach_id->get_error_message() ) );
		}

		wp_update_attachment_metadata( $attach_id, $attach_data );

		wp_send_json_success( array( 'file_url' => wp_get_attachment_url( $attach_id ) ) );
	}
}
