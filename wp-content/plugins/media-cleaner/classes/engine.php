<?php

class Meow_WPMC_Engine {

	function __construct( $core, $admin ) {
		$this->core = $core;
		$this->admin = $admin;
	}

	/*
		STEP 1: Parse the content, and look for references
	*/

	/**
	 * Returns the posts to check the references
	 * @param int $offset Negative number means no limit
	 * @param int $size   Negative number means no limit
	 * @return NULL|array
	 */
	function get_posts_to_check( $offset = -1, $size = -1 ) {
		global $wpdb;
		$r = null;

		// Maybe we could avoid to check more post_types.
		// SELECT post_type, COUNT(*) FROM `wp_posts` GROUP BY post_type
		$q = <<<SQL
SELECT p.ID FROM $wpdb->posts p
WHERE p.post_status NOT IN ('inherit', 'trash', 'auto-draft')
AND p.post_type NOT IN ('attachment', 'shop_order', 'shop_order_refund', 'nav_menu_item', 'revision', 'auto-draft', 'wphb_minify_group', 'customize_changeset', 'oembed_cache', 'nf_sub', 'jp_img_sitemap')
AND p.post_type NOT LIKE 'dlssus_%'
AND p.post_type NOT LIKE 'ml-slide%'
AND p.post_type NOT LIKE '%acf-%'
AND p.post_type NOT LIKE '%edd_%'
SQL;
		if ( $offset >= 0 && $size >= 0 ) {
			$q .= " LIMIT %d, %d";
			$r = $wpdb->get_col( $wpdb->prepare( $q, $offset, $size ) );

		} else // No limit
			$r = $wpdb->get_col( $q );

		return $r;
	}

	// Parse the posts for references (based on $limit and $limitsize for paging the scan)
	function extractRefsFromContent( $limit, $limitsize, &$message = '' ) {
		if ( empty( $limit ) )
			$this->core->reset_issues();

		$method = $this->core->current_method;

		// Check content is a different option depending on the method
		$check_content = false;
		if ( $method === 'media' ) {
			$check_content = $this->core->get_option( 'content' );
		}
		else if ( $method === 'files' ) {
			$check_content = $this->core->get_option( 'filesystem_content' );
		}

		if ( $method == 'media' && !$check_content ) {
			$message = __( "Skipped, as Content is not selected.", 'media-cleaner' );
			return true;
		}

		if ( $method == 'files' && !$check_content ) {
			$message = __( "Skipped, as Content is not selected.", 'media-cleaner' );
			return true;
		}

		// Initialize the parsers
		do_action( 'wpmc_initialize_parsers' );

		$posts = $this->get_posts_to_check( $limit, $limitsize );

		// Only at the beginning, check the Widgets and the Scan Once in the Parsers
		if ( empty( $limit ) ) {
			$this->core->log( "🏁 Extracting refs from content..." );
			//if ( get_option( 'wpmc_widgets', false ) ) {
				global $wp_registered_widgets;
				$syswidgets = $wp_registered_widgets;
				$active_widgets = get_option( 'sidebars_widgets' );
				foreach ( $active_widgets as $sidebar_name => $widgets ) {
					if ( $sidebar_name != 'wp_inactive_widgets' && !empty( $widgets ) && is_array( $widgets ) ) {
						foreach ( $widgets as $key => $widget ) {
							do_action( 'wpmc_scan_widget', $syswidgets[$widget] );
						}
					}
				}
				do_action( 'wpmc_scan_widgets' );
			//}
			do_action( 'wpmc_scan_once' );
		}

		$this->core->timeout_check_start( count( $posts ) );

		foreach ( $posts as $post ) {
			$this->core->timeout_check();

			// Check content
			if ( $check_content ) {
				do_action( 'wpmc_scan_postmeta', $post );
				$html = get_post_field( 'post_content', $post );
				do_action( 'wpmc_scan_post', $html, $post );
			}

			// Extra scanning methods
			do_action( 'wpmc_scan_extra', $post );

			$this->core->timeout_check_additem();
		}

		// Write the references found (and cached) by the parsers
		$this->core->write_references();

		$finished = count( $posts ) < $limitsize;
		if ( $finished )
			$this->core->log();
		$elapsed = $this->core->timeout_get_elapsed();
		$message = sprintf(
			// translators: %1$d is number of posts, %2$s is time in milliseconds
			__( "Extracted references from %1\$d posts in %2\$s.", 'media-cleaner' ), count( $posts ), $elapsed
		);
		return $finished;
	}

	// Parse the posts for references (based on $limit and $limitsize for paging the scan)
	function extractRefsFromLibrary( $limit, $limitsize, &$message = '' ) {
		$method = $this->core->current_method;
		if ( $method == 'media' ) {
			$message = __( "Skipped, as it is not needed for the Media Library method.", 'media-cleaner' );
			return true;
		}
		$check_library = $this->core->get_option( 'media_library' );
		if ( !$check_library ) {
			$message = __( "Skipped, as Media Library is not selected.", 'media-cleaner' );
			return true;
		}

		$medias = $this->get_media_entries( $limit, $limitsize );

		// Only at the beginning
		if ( empty( $limit ) ) {
			$this->core->log( "🏁 Extracting refs from Media Library..." );
		}

		$this->core->timeout_check_start( count( $medias ) );
		foreach ( $medias as $media ) {
			$this->core->timeout_check();
			// Check the media
			$paths = $this->core->get_paths_from_attachment( $media );
			$this->core->add_reference_url( $paths, 'MEDIA LIBRARY' );
			$this->core->timeout_check_additem();
		}

		// Write the references found (and cached) by the parsers
		$this->core->write_references();

		$finished = count( $medias ) < $limitsize;
		if ( $finished )
			$this->core->log();
		$elapsed = $this->core->timeout_get_elapsed();
		$message = sprintf( __( "Extracted references from %d medias in %s.", 'media-cleaner' ), count( $medias ), $elapsed );
		return $finished;
	}

	/*
		STEP 2: List the media entries (or files)
	*/

	// Get files in /uploads (if path is null, the root of /uploads is returned)
	function get_files( $path = null ) {
		$files = apply_filters( 'wpmc_list_uploaded_files', null, $path );
		return $files ? $files : array();
	}

	/**
	 * Returns the media entries to check the references
	 * @param int $offset Negative number means no limit
	 * @param int $size   Negative number means no limit
	 * @return NULL|array
	 */
	function get_media_entries( $offset = -1, $size = -1, $unattachedOnly = false ) {
		global $wpdb;
		$r = null;

		$extraAnd = $unattachedOnly ? "AND p.post_parent = 0" : "";

		$q = <<<SQL
SELECT p.ID FROM $wpdb->posts p
WHERE p.post_status = 'inherit'
$extraAnd
AND p.post_type = 'attachment'
SQL;
		if ( $this->core->get_option( 'images_only' ) ) {
			// Get only media entries which are images
			$q .= " AND p.post_mime_type IN ( 'image/jpeg', 'image/gif', 'image/png', 'image/webp',
				'image/bmp', 'image/tiff', 'image/x-icon', 'image/svg' )";
		}

		if ( $offset >= 0 && $size >= 0 ) {
			$q .= " LIMIT %d, %d";
			$r = $wpdb->get_col( $wpdb->prepare( $q, $offset, $size ) );

		} else // No limit
			$r = $wpdb->get_col( $q );

		return $r;
	}

	/*
		STEP 3: Check the media entries (or files) against the references
	*/

	function check_media( $media ) {
		return $this->core->check_media( $media );
	}

	function check_file( $file ) {
		// Basically, wpmc_check_file returns either true if it's used, or
		// the codename of the issue.
		$issue = apply_filters( 'wpmc_check_file', false, $file );
		$used = $issue === true;
		if ( !$used ) {
			global $wpdb;
			$filepath = trailingslashit( $this->core->upload_path ) . stripslashes( $file );
			$clean_path = $this->core->clean_uploaded_filename( $file );
			$table_name = $wpdb->prefix . "mclean_scan";
			$filesize = file_exists( $filepath ) ? filesize ($filepath) : 0;
			$wpdb->insert( $table_name,
				array(
					'time' => current_time('mysql'),
					'type' => 0,
					'path' => $clean_path,
					'size' => $filesize,
					'issue' => $issue
				)
			);
		}
		return $used;
	}

}

?>