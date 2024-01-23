<?php

/**
 * Tag PHP class for the WordPress plugin NextGEN Gallery
 * nggallery.lib.php
 *
 * @author Alex Rabe
 */
class nggTags {

	/**
	 * Copy tags
	 */
	static function copy_tags( $src_pid, $dest_pid ) {
		$tags = wp_get_object_terms( $src_pid, 'ngg_tag', 'fields=ids' );
		$tags = array_map( 'intval', $tags );
		wp_set_object_terms( $dest_pid, $tags, 'ngg_tag', true );

		return implode( ',', $tags );
	}

	/**
	 * Rename tags
	 */
	static function rename_tags( $old = '', $new = '' ) {

		$return_value = array(
			'status' => 'ok',
			'message' => '',
		);

		if ( trim( str_replace( ',', '', stripslashes( $new ) ) ) == '' ) {
			$return_value['message'] = __( 'No new tag specified!', 'nggallery' );
			$return_value['status']  = 'error';
			return $return_value;
		}

		// String to array
		$old_tags = explode( ',', $old );
		$new_tags = explode( ',', $new );

		// Remove empty element and trim
		$old_tags = array_filter( $old_tags, 'nggtags_delete_empty_element' );
		$new_tags = array_filter( $new_tags, 'nggtags_delete_empty_element' );

		// If old/new tag are empty => exit !
		if ( empty( $old_tags ) || empty( $new_tags ) ) {
			$return_value['message'] = __( 'No new/old valid tag specified!', 'nggallery' );
			$return_value['status']  = 'error';
			return $return_value;
		}

		$counter = 0;
		if ( count( $old_tags ) == count( $new_tags ) ) { // Rename only
			foreach ( (array) $old_tags as $i => $old_tag ) {
				$new_name = $new_tags[$i];

				// Get term by name
				$term = get_term_by( 'name', $old_tag, 'ngg_tag' );
				if ( !$term ) {
					continue;
				}

				// Get objects from term ID
				$objects_id = get_objects_in_term( $term->term_id, 'ngg_tag', array( 'fields' => 'all_with_object_id' ) );

				// Delete old term
				wp_delete_term( $term->term_id, 'ngg_tag' );

				// Set objects to new term ! (Append no replace)
				foreach ( (array) $objects_id as $object_id ) {
					wp_set_object_terms( $object_id, $new_name, 'ngg_tag', true );
				}

				// Clean cache
				clean_object_term_cache( $objects_id, 'ngg_tag' );
				clean_term_cache( $term->term_id, 'ngg_tag' );

				// Increment
				++$counter;
			}

			if ( $counter == 0  ) {
				$return_value['message'] = __( 'No tag renamed.', 'nggallery' );
			} else {
				$return_value['message'] = sprintf( __( 'Renamed tag(s) &laquo;%1$s&raquo; to &laquo;%2$s&raquo;', 'nggallery' ), $old, $new );
			}
		} elseif ( count( $new_tags ) == 1  ) { // Merge
			// Set new tag
			$new_tag = $new_tags[0];
			if ( empty( $new_tag ) ) {
				$return_value['message'] = __( 'No valid new tag.', 'nggallery' );
				$return_value['status']  = 'error';
				return $return_value;
			}

			// Get terms ID from old terms names
			$terms_id = array();
			foreach ( (array) $old_tags as $old_tag ) {
				$term       = get_term_by( 'name', addslashes( $old_tag ), 'ngg_tag' );
				$terms_id[] = (int) $term->term_id;
			}

			// Get objects from terms ID
			$objects_id = get_objects_in_term( $terms_id, 'ngg_tag', array( 'fields' => 'all_with_object_id' ) );

			// No objects ? exit !
			if ( !$objects_id ) {
				$return_value['message'] = __( 'No objects (post/page) found for specified old tags.', 'nggallery' );
				$return_value['status']  = 'error';
				return $return_value;
			}

			// Delete old terms
			foreach ( (array) $terms_id as $term_id ) {
				wp_delete_term( $term_id, 'ngg_tag' );
			}

			// Set objects to new term ! (Append no replace)
			foreach ( (array) $objects_id as $object_id ) {
				wp_set_object_terms( $object_id, $new_tag, 'ngg_tag', true );
				++$counter;
			}

			// Test if term is also a category
			if ( term_exists( $new_tag, 'category' ) ) {
				// Edit the slug to use the new term
				$slug = sanitize_title( $new_tag );
				self::edit_tag_slug( $new_tag, $slug );
				unset( $slug );
			}

			// Clean cache
			clean_object_term_cache( $objects_id, 'ngg_tag' );
			clean_term_cache( $terms_id, 'ngg_tag' );

			if ( $counter == 0  ) {
				$return_value['message'] = __( 'No tag merged.', 'nggallery' );
			} else {
				$return_value['message'] = sprintf( __( 'Merge tag(s) &laquo;%1$s&raquo; to &laquo;%2$s&raquo;. %3$s objects edited.', 'nggallery' ), $old, $new, $counter );
			}
		} else { // Error
			$return_value['message'] = sprintf( __( 'Error. Not enough tags provided to rename or merge.', 'nggallery' ), $old );
			$return_value['status']  = 'error';
		}

		do_action( 'ngg_manage_tags', $new_tags );

		return $return_value;
	}

	/**
	 * Delete tags
	 */
	static function delete_tags( $delete ) {
		$return_value = array(
			'status' => 'ok',
			'message' => '',
		);

		if ( trim( str_replace( ',', '', stripslashes( $delete ) ) ) == '' ) {
			$return_value['message'] = __( 'No tag specified!', 'nggallery' );
			$return_value['status']  = 'error';
			return $return_value;
		}

		// In array + filter
		$delete_tags = explode( ',', $delete );
		$delete_tags = array_filter( $delete_tags, 'nggtags_delete_empty_element' );

		// Delete tags
		$counter = 0;
		foreach ( (array) $delete_tags as $tag ) {
			$term    = get_term_by( 'name', $tag, 'ngg_tag' );
			$term_id = (int) $term->term_id;

			if ( $term_id != 0 ) {
				wp_delete_term( $term_id, 'ngg_tag' );
				clean_term_cache( $term_id, 'ngg_tag' );
				++$counter;
			}
		}

		if ( $counter == 0  ) {
			$return_value['message'] = __( 'No tag deleted.', 'nggallery' );
		} else {
			$return_value['message'] = sprintf( __( '%1s tag(s) deleted.', 'nggallery' ), $counter );
		}

		do_action( 'ngg_manage_tags', $delete_tags );

		return $return_value;
	}

	/**
	 * Edit tag slug given the name of the tag
	 */
	static function edit_tag_slug( $names = '', $slugs = '' ) {
		$return_value = array(
			'status' => 'ok',
			'message' => '',
		);

		if ( trim( str_replace( ',', '', stripslashes( $slugs ) ) ) == '' ) {
			$return_value['message'] = __( 'No new slug(s) specified!', 'nggallery' );
			$return_value['status']  = 'error';
			return $return_value;
		}

		$match_names = explode( ',', $names );
		$new_slugs   = explode( ',', $slugs );

		$match_names = array_filter( $match_names, 'nggtags_delete_empty_element' );
		$new_slugs   = array_filter( $new_slugs, 'nggtags_delete_empty_element' );

		if ( count( $match_names ) != count( $new_slugs ) ) {
			$return_value['message'] = __( 'Tags number and slugs number isn\'t the same!', 'nggallery' );
			$return_value['status']  = 'error';
			return $return_value;
		} else {
			$counter = 0;
			foreach ( (array) $match_names as $i => $match_name ) {
				// Sanitize slug + Escape
				$new_slug = sanitize_title( $new_slugs[$i] );

				// Get term by name
				$term = get_term_by( 'name', $match_name, 'ngg_tag' );
				if ( !$term ) {
					continue;
				}

				// Increment
				++$counter;

				// Update term
				wp_update_term( $term->term_id, 'ngg_tag', array( 'slug' => $new_slug ) );

				// Clean cache
				clean_term_cache( $term->term_id, 'ngg_tag' );
			}
		}

		if ( $counter == 0  ) {
			$return_value['message'] = __( 'No slug edited.', 'nggallery' );
		} else {
			$return_value['message'] = sprintf( __( '%s slug(s) edited.', 'nggallery' ), $counter );
		}

		return $return_value;
	}

	/**
	 * Get a list of the tags used by the images
	 */
	static function find_all_tags() {
		return get_terms( 'ngg_tag', '' );
	}

	/**
	 *
	 */
	static function find_tags( $args = '', $skip_cache = false ) {
		$taxonomy = 'ngg_tag';

		if ( $skip_cache == true ) {
			$terms = get_terms( $taxonomy, $args );
		} else {
			$key = md5( serialize( $args ) );

			// Get cache if exist
			// --
			if ( $cache = wp_cache_get( 'ngg_get_tags', 'nggallery' ) ) {
				if ( isset( $cache[$key] ) ) {
					return apply_filters( 'get_tags', $cache[$key], $args );
				}
			}

			// Get tags
			// --
			$terms = get_terms( $taxonomy, $args );
			if ( empty( $terms ) ) {
				return array();
			}

			$cache[$key] = $terms;
			wp_cache_set( 'ngg_get_tags', $cache, 'nggallery' );
		}

		$terms = apply_filters( 'get_tags', $terms, $args );
		return $terms;
	}

	/**
	 * Get images corresponding to a list of tags
	 *
	 * nggTags::find_images_for_tags()
	 *
	 * @param mixed  $taglist
	 * @param string $mode could be 'ASC', 'DESC' or 'RAND'
	 *
	 * @return array of images
	 */
	static function find_images_for_tags( $taglist, $mode = "ASC" ) {
		// return the images based on the tag
		global $wpdb;

		// extract it into a array
		$taglist = explode( ",", $taglist );

		if ( ! is_array( $taglist ) ) {
			$taglist = array( $taglist );
		}

		$taglist       = array_map( 'trim', $taglist );
		$new_slugarray = array_map( 'sanitize_title', $taglist );
		$sluglist      = implode( "', '", $new_slugarray );

		// Treat % as a literal in the database, for unicode support
		$sluglist = str_replace( "%", "%%", $sluglist );

		// first get all $term_ids with this tag
		$term_ids = $wpdb->get_col( $wpdb->prepare( "SELECT term_id FROM $wpdb->terms WHERE slug IN (%s) ORDER BY term_id ASC ", $sluglist ) );
		$picids   = get_objects_in_term( $term_ids, 'ngg_tag' );

		if ( $mode == 'RAND' ) {
			shuffle( $picids );
		}

		// Now lookup in the database
		$mapper = \Imagely\NGG\DataMappers\Image::get_instance();
		$images = array();
		foreach ( $picids as $image_id ) {
			$images[] = $mapper->find( $image_id );
		}

		if ( 'DESC' == $mode ) {
			$images = array_reverse( $images );
		}

		return $images;
	}
}

/**
 * trim and remove empty element
 *
 * @param string $element
 * @return null|string
 */
if (!function_exists( 'nggtags_delete_empty_element' )) {
	function nggtags_delete_empty_element( $element ) {
		$element = stripslashes( $element );
		$element = trim( $element );
		if (!empty( $element )) {
			return $element;
		}
		return null;
	}
}
