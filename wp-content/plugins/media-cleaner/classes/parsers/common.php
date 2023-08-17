<?php

class MeowApps_WPMC_Parser_Common {

	private $metakeys = array( '%gallery%', '%ids%' );

	public function __construct() {

		// Check theme and favicon
		add_action( 'wpmc_scan_once', array( $this, 'scan_once' ), 10, 0 );

		// Check widgets for IDs and URLs
		add_action( 'wpmc_scan_widget', array( $this, 'scan_widget' ), 10, 1 );

		// Detect values in the general (known, based on %like%) Meta Keys
		add_action( 'wpmc_scan_postmeta', array( $this, 'scan_postmeta' ), 10, 1 );

		// Check URLs, IDs, WP Gallery
		add_action( 'wpmc_scan_post', array( $this, 'scan_post' ), 10, 2 );
	}

	public function scan_once() {
		global $wpmc;
		$theme_ids = array();
		$theme_urls = array();
		$wpmc->get_images_from_themes( $theme_ids, $theme_urls );
		$wpmc->add_reference_id( $theme_ids, 'THEME' );
		$wpmc->add_reference_url( $theme_urls, 'THEME' );
		$favicon = $wpmc->get_favicon();
		if ( !empty( $favicon ) ) {
			$wpmc->add_reference_url( $favicon, 'SITE ICON' );
		}
	}

	function get_images_from_widget( $widget, &$ids, &$urls ) {
		global $wpmc;
		// TODO: We should test with widgets										
		if ( !isset( $widget['callback'] ) || !isset( $widget['callback'][0] ) ) {
			return;
		}
		$widget_class = $widget['callback'][0]->option_name;
		$instance_id = $widget['params'][0]['number'];
		$widget_data = get_option( $widget_class );
		if ( !empty( $widget_data[$instance_id]['text'] ) ) {
			$html = $widget_data[$instance_id]['text']; // mm change
			$urls = array_merge( $urls, $wpmc->get_urls_from_html( $html ) );
		}
		if ( !empty( $widget_data[$instance_id]['attachment_id'] ) ) {
			$id = $widget_data[$instance_id]['attachment_id'];
			array_push( $ids, $id );
		}
		if ( !empty( $widget_data[$instance_id]['url'] ) ) {
			$url = $widget_data[$instance_id]['url'];
			if ( $wpmc->is_url( $url ) ) {
				$url = $wpmc->clean_url( $url );
				if ( !empty($url) )
					array_push( $urls, $url );
			}
		}
		if ( !empty( $widget_data[$instance_id]['ids'] ) ) {
			$newIds = $widget_data[$instance_id]['ids'];
			if ( is_array( $newIds ) ) {
				$ids = array_merge( $ids, $newIds );
			}
		}
		// Recent Blog Posts
		if ( !empty( $widget_data[$instance_id]['thumbnail'] ) ) {
			$id = $widget_data[$instance_id]['thumbnail'];
			array_push( $ids, $id );
		}
	}

	public function scan_widget( $widget ) {
		global $wpmc;
		$widgets_ids = array();
		$widgets_urls = array();
		$this->get_images_from_widget( $widget, $widgets_ids, $widgets_urls );
		$wpmc->add_reference_id( $widgets_ids, 'WIDGET' );
		$wpmc->add_reference_url( $widgets_urls, 'WIDGET' );
	}

	public function get_post_galleries_ids( $id ) {
		global $post;
		$content_post = get_post( $id );
		$content = $content_post->post_content;
		$ids = array();
		if ( preg_match_all('/\[gallery.*ids=.(.*).\]/', $content, $foundArrayIds ) ) {
			if ( $foundArrayIds ) {
				foreach ( $foundArrayIds[1] as $foundIds ) {
					$newIds = explode( ',', $foundIds );
					$ids = array_merge( $ids, $newIds );
				}
			}
		}
		return $ids;
	}

	public function scan_post( $html, $id ) {
		global $wpmc;
		$posts_images_urls = array();
		$posts_images_ids = array();
		$galleries_images = array();

		// Check URLs in HTML
		$new_urls = $wpmc->get_urls_from_html( $html );
		$posts_images_urls = array_merge( $posts_images_urls, $new_urls );

		// Check URLs in the Excerpt
		$excerpt = get_post_field( 'post_excerpt', $id );
		if ( !empty( $excerpt ) ) {
			$new_urls = $wpmc->get_urls_from_html( $excerpt );
			$posts_images_urls = array_merge( $posts_images_urls, $new_urls );
		}

		// Check for images IDs through classes in in posts
		preg_match_all( "/wp-image-([0-9]+)/", $html, $res );
		if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
			$posts_images_ids = array_merge( $posts_images_ids, $res[1] );

		// Standard WP Gallery
		$ids = $this->get_post_galleries_ids( $id );
		$posts_images_ids = array_merge( $posts_images_ids, $ids );
		$galleries = get_post_galleries_images( $id );
		foreach ( $galleries as $gallery ) {
			foreach ( $gallery as $image ) {
				array_push( $galleries_images, $wpmc->clean_url( $image ) );
			}
		}

		$wpmc->add_reference_id( $posts_images_ids, "CONTENT (ID)", $id );
		$wpmc->add_reference_url( $posts_images_urls, "CONTENT (URL)", $id );
		$wpmc->add_reference_url( $galleries_images, "GALLERY (URL)", $id );
	}

	public function scan_postmeta( $id ) {
		global $wpdb, $wpmc;

		$likes = array();
		foreach ($this->metakeys as $metakey) {
				$likes[] = "OR meta_key LIKE '{$metakey}'";
		}
		$likes = implode( ' ', $likes );
		$q = "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d";
		// Since WordPress 6.2, $wpdb->prepare seems fail with the AND/OR.
		$sql = $wpdb->prepare( $q, $id ) . " AND (meta_key = '_thumbnail_id' {$likes})";
		$metas = $wpdb->get_col( $sql );
		if ( count( $metas ) > 0 ) {
			$postmeta_images_ids = array();
			$postmeta_images_urls = array();
			foreach ( $metas as $meta ) {
				// Just a number, let's assume it's a Media ID
				if ( is_numeric( $meta ) ) {
					if ( $meta > 0 )
						array_push( $postmeta_images_ids, $meta );
					continue;
				}
				else if ( is_serialized( $meta ) ) {
					$decoded = @unserialize( $meta );
					if ( is_array( $decoded ) ) {
						$wpmc->array_to_ids_or_urls( $decoded, $postmeta_images_ids, $postmeta_images_urls );
						continue;
					}
				}
				else {
					$exploded = explode( ',', $meta );
					if ( is_array( $exploded ) ) {
						$wpmc->array_to_ids_or_urls( $exploded, $postmeta_images_ids, $postmeta_images_urls );
						continue;
					}
				}
			}
			$wpmc->add_reference_id( $postmeta_images_ids, 'META (ID)', $id );
			$wpmc->add_reference_id( $postmeta_images_urls, 'META (URL)', $id );
		}
	}
}

new MeowApps_WPMC_Parser_Common();
