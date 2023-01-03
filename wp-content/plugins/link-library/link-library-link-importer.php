<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function link_library_import_links( $genoptions, &$row, &$successfulimport, &$successfulupdate ) {

	wp_defer_term_counting( true );
	wp_defer_comment_counting( true );
	wp_suspend_cache_addition( true );

	$message = '';

	define( 'WP_IMPORTING', true );
	set_time_limit( 1800 );

	if ( !empty( $_FILES['linksfile']['tmp_name'] ) || ( $genoptions['enableautolinksimport'] && !empty( $genoptions['importlinksurl'] ) ) ) {
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  

		if ( !empty( $_FILES['linksfile']['tmp_name'] ) ) {
			$file_contents = file_get_contents( $_FILES['linksfile']['tmp_name'] );
		} else {
			$file_contents = file_get_contents( $genoptions['importlinksurl'], false, stream_context_create( $arrContextOptions ) );
		}

		if ( $file_contents ) {
			$skiprow = 1;
			$import_columns = array();

			$imported_lines = explode( "\n", $file_contents ); // this is your array of words

			foreach( $imported_lines as $imported_line ) {
				if ( empty( $imported_line ) ) continue;
				$data = str_getcsv( $imported_line, ',' );

				$row += 1;
				if ( $skiprow == 1 && $row >= 2 ) {
					$skiprow = 0;
				}

				if ( 1 == $row ) {
					foreach ( $data as $index => $column_name ) {
						$import_columns[$column_name] = $index;
					}
				} else {
					$existing_link_post_id = '';
					$matched_link_cats = array();
					$matched_link_tags = array();

					if ( ( isset( $import_columns['Category Slugs'] ) && !empty( $data[$import_columns['Category Slugs']] ) ) ) {
						$new_link_cats_slugs_array = array();
						if ( isset( $import_columns['Category Slugs'] ) ) {
							$new_link_cats_slugs_array = explode( ',', $data[$import_columns['Category Slugs']] );
						}

						if ( ( isset( $import_columns['Category Names'] ) && !empty( $data[$import_columns['Category Names']] ) ) || ( isset( $import_columns['cat_name'] ) && !empty( $data[$import_columns['cat_name']] ) ) ) {
							if ( isset( $import_columns['Category Names'] ) ) {
								$new_link_cats_array = explode( ',', $data[$import_columns['Category Names']] );
							} elseif( isset( $import_columns['cat_name'] ) ) {
								$new_link_cats_array = explode( ',', $data[$import_columns['cat_name']] );
							}
						}

						foreach ( $new_link_cats_slugs_array as $index => $new_link_cat_slug ) {
							$cat_matched_term = get_term_by( 'slug', $new_link_cat_slug, $genoptions['cattaxonomy'] );

							if ( false !== $cat_matched_term ) {
								$matched_link_cats[] = $cat_matched_term->term_id;
							} else {
								$new_link_cat = '';
								if ( !empty( $new_link_cats_array ) && isset( $new_link_cats_array[$index] ) ) {
									$new_link_cat = $new_link_cats_array[$index];
								} else {
									$new_link_cat = $new_link_cat_slug;
								}

								$new_cat_term_data   = wp_insert_term( $new_link_cat, $genoptions['cattaxonomy'], array( 'slug' => $new_link_cat_slug ) );
								if ( is_wp_error( $new_cat_term_data ) ) {
									print_r( 'Failed creating category ' . $new_link_cat );
								} else {
									$matched_link_cats[] = $new_cat_term_data['term_id'];
								}
							}
						}
					}

					if ( ( isset( $import_columns['Tag Slugs'] ) && !empty( $data[$import_columns['Tag Slugs']] ) ) ) {
						$new_link_tags_slugs_array = array();
						if ( isset( $import_columns['Tag Slugs'] ) ) {
							$new_link_tags_slugs_array = explode( ',', $data[$import_columns['Tag Slugs']] );
						}

						if ( ( isset( $import_columns['Tag Names'] ) && !empty( $data[$import_columns['Tag Names']] ) ) ) {
							if ( isset( $import_columns['Tag Names'] ) ) {
								$new_link_tags_array = explode( ',', $data[$import_columns['Tag Names']] );
							}
						}

						foreach ( $new_link_tags_slugs_array as $index => $new_link_tag_slug ) {
							$tag_matched_term = get_term_by( 'slug', $new_link_tag_slug, $genoptions['tagtaxonomy'] );

							if ( false !== $tag_matched_term ) {
								$matched_link_tags[] = $tag_matched_term->term_id;
							} else {
								$new_link_tag = '';
								if ( !empty( $new_link_tags_array ) && isset( $new_link_tags_array[$index] ) ) {
									$new_link_tag = $new_link_tags_array[$index];
								} else {
									$new_link_tag = $new_link_tag_slug;
								}

								$new_tag_term_data   = wp_insert_term( $new_link_tag, $genoptions['tagtaxonomy'], array( 'slug' => $new_link_tag_slug ) );
								if ( is_wp_error( $new_tag_term_data ) ) {
									print_r( 'Failed creating tag ' . $new_link_tag );
								} else {
									$matched_link_tags[] = $new_tag_term_data['term_id'];
								}
							}
						}
					}

					$link_url = '';
					$url_labels = array( 'Address', 'link_url' );
					foreach( $url_labels as $url_label ) {
						if ( isset( $import_columns[$url_label] ) ) {
							if ( !empty( $data[$import_columns[$url_label]] ) ) {
								$link_url = esc_url( $data[$import_columns[$url_label]] );
							}
						}
					}

					if ( isset( $_POST['updatesameurl'] ) ) {
						$search_link_url = preg_replace("(^https?://)", "", $link_url );
						$find_post_args = array( 'post_type' => 'link_library_links',
												 'meta_key' => 'link_url',
												 'meta_value' => $search_link_url,
												 'meta_compare' => 'LIKE',
												 'numberposts' => 1 );

						$posts_same_url_array = get_posts( $find_post_args );

						if ( !empty( $posts_same_url_array ) ) {
							$existing_link_post_id = $posts_same_url_array[0]->ID;
						}
					}

					$post_status = 'publish';
					$post_status_import_value = '';

					$visible_labels = array( 'Status', 'Visible', 'link_visible' );
					foreach( $visible_labels as $visible_label ) {
						if ( isset( $import_columns[$visible_label] ) ) {
							$post_status_import_value = $data[$import_columns[$visible_label]];
						}
					}

					if ( in_array( $post_status_import_value, array( 'publish', 'draft', 'private' ) ) ) {
						$post_status = $post_status_import_value;
					} elseif ( 'N' == $post_status_import_value ) {
						$post_status = 'private';
					}

					$post_title = '';
					$title_labels = array( 'Link Name', 'link_name', 'Name' );
					foreach( $title_labels as $title_label ) {
						if ( isset( $import_columns[$title_label] ) ) {
							if ( ! empty( $data[ $import_columns[$title_label] ] ) ) {
								$post_title = sanitize_text_field( $data[ $import_columns[$title_label] ] );
							}
						}
					}


					$link_publication = current_time( 'mysql' );
					$publication_labels = array( 'Publication Date' );
					foreach( $publication_labels as $publication_label ) {
						if ( isset( $import_columns[$publication_label] ) ) {
							if ( !empty( $import_columns[$publication_label] ) ) {
								$link_publication = $data[ $import_columns[$publication_label] ];
							}
						}
					}

					$new_link_data = array(
						'post_type' => 'link_library_links',
						'post_content' => '',
						'post_title' => $post_title,
						'tax_input' => array( $genoptions['cattaxonomy'] => $matched_link_cats, $genoptions['tagtaxonomy'] => $matched_link_tags ),
						'post_status' => $post_status,
						'post_date' => $link_publication
					);

					if ( !empty( $existing_link_post_id ) ) {
						$new_link_data['ID'] = $existing_link_post_id;
						$new_link_ID = wp_insert_post( $new_link_data );
						$successfulupdate++;
					} else {
						$new_link_ID = wp_insert_post( $new_link_data );
						$successfulimport++;
					}

					update_post_meta( $new_link_ID, 'link_url', $link_url );

					$link_image = '';
					$image_labels = array( 'Image Address', 'link_image' );
					foreach( $image_labels as $image_label ) {
						if ( isset( $import_columns[$image_label] ) ) {
							$link_image = esc_url( $data[$import_columns[$image_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_image', $link_image );

					if ( empty( $link_image ) ) {
						delete_post_thumbnail( $new_link_ID );
					} else {
						$wpFileType = wp_check_filetype( $link_image, null);

						$attachment = array(
							'post_mime_type' => $wpFileType['type'],  // file type
							'post_title' => sanitize_file_name( $link_image ),  // sanitize and use image name as file name
							'post_content' => '',  // could use the image description here as the content
							'post_status' => 'inherit'
						);

						// insert and return attachment id
						$attachmentId = wp_insert_attachment( $attachment, $link_image, $new_link_ID );
						$attachmentData = wp_generate_attachment_metadata( $attachmentId, $link_image );
						wp_update_attachment_metadata( $attachmentId, $attachmentData );
						set_post_thumbnail( $new_link_ID, $attachmentId );
					}

					$link_target = '';
					$target_labels = array( 'Link Target', 'link_target' );
					foreach( $target_labels as $target_label ) {
						if ( isset( $import_columns[$target_label] ) ) {
							$link_target = sanitize_text_field( $data[$import_columns[$target_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_target', $link_target );

					$link_description = '';
					$description_labels = array( 'Description', 'link_description' );
					foreach( $description_labels as $description_label ) {
						if ( isset( $import_columns[$description_label] ) ) {
							$link_description = sanitize_text_field( $data[$import_columns[$description_label]] );
							$link_description = str_replace( '(LinkLibrary:AwaitingModeration:RemoveTextToApprove)', '', $link_description );
						}
					}
					update_post_meta( $new_link_ID, 'link_description', $link_description );

					$link_rating = '';
					$rating_labels = array( 'Rating', 'rating' );
					foreach( $rating_labels as $rating_label ) {
						if ( isset( $import_columns[$rating_label] ) ) {
							$newrating = intval( $data[$import_columns[$rating_label]] );
							if ( $newrating < 0 ) {
								$newrating = 0;
							}
							$link_rating = $newrating;
						}
					}
					update_post_meta( $new_link_ID, 'link_rating', $link_rating );

					update_post_meta( $new_link_ID, '_thumbs_rating_up', 0 );

					$link_updated = current_time( 'timestamp' );
					$updated_labels = array( 'Updated Date - Empty for none', 'link_updated' );
					foreach( $updated_labels as $updated_label ) {
						if ( isset( $import_columns[$updated_label] ) ) {
							if ( !empty( $import_columns[$updated_label] ) ) {
								$link_updated = strtotime( $data[ $import_columns[$updated_label] ] );
							}
						}
					}
					update_post_meta( $new_link_ID, 'link_updated', $link_updated );

					$link_notes = '';
					$notes_labels = array( 'Notes', 'link_notes' );
					foreach( $notes_labels as $notes_label ) {
						if ( isset( $import_columns[$notes_label] ) ) {
							$link_notes = sanitize_text_field( $data[$import_columns[$notes_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_notes', $link_notes );

					$link_rss = '';
					$rss_labels = array( 'RSS', 'link_rss' );
					foreach( $rss_labels as $rss_label ) {
						if ( isset( $import_columns[$rss_label] ) ) {
							$link_rss = esc_url( $data[$import_columns[$rss_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_rss', $link_rss );

					$link_second_url = '';
					$second_url_labels = array( 'Secondary URL', 'link_second_url' );
					foreach( $second_url_labels as $second_url_label ) {
						if ( isset( $import_columns[$second_url_label] ) ) {
							$link_second_url = esc_url( $data[$import_columns[$second_url_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_second_url',  $link_second_url );

					$link_telephone = '';
					$telephone_labels = array( 'Telephone', 'link_telephone' );
					foreach( $telephone_labels as $telephone_label ) {
						if ( isset( $import_columns[$telephone_label] ) ) {
							$link_telephone = sanitize_text_field( $data[$import_columns[$telephone_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_telephone', $link_telephone );

					$link_email = '';
					$email_labels = array( 'E-mail', 'link_email' );
					foreach( $email_labels as $email_label ) {
						if ( isset( $import_columns[$email_label] ) ) {
							$link_email = sanitize_email( $data[$import_columns[$email_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_email', $link_email );

					if ( empty( $existing_link_post_id ) ) {
						$link_visits = 0;
						$link_visits_labels = array( 'Link Visits' );
						foreach( $link_visits_labels as $link_visits_label ) {
							if ( isset( $import_columns[$link_visits_label] ) ) {
								$link_visits = intval( $data[$import_columns[$link_visits_label]] );
							}
						}

						update_post_meta( $new_link_ID, 'link_visits', $link_visits );
					}

					$link_reciprocal = '';
					$reciprocal_labels = array( 'Reciprocal Link', 'link_reciprocal' );
					foreach( $reciprocal_labels as $reciprocal_label ) {
						if ( isset( $import_columns[$reciprocal_label] ) ) {
							$link_reciprocal = esc_url( $data[$import_columns[$reciprocal_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_reciprocal', $link_reciprocal );

					$link_large_description = '';
					$large_description_labels = array( 'Large Description', 'link_textfield' );
					foreach( $large_description_labels as $large_description_label ) {
						if ( isset( $import_columns[$large_description_label] ) ) {
							$link_large_description = sanitize_text_field( $data[$import_columns[$large_description_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_textfield', $link_large_description );

					$link_no_follow = 0;
					$no_follow_labels = array( 'No Follow', 'link_no_follow' );
					foreach( $no_follow_labels as $no_follow_labels ) {
						if ( isset( $import_columns[$no_follow_labels] ) ) {
							$link_no_follow = $data[$import_columns[$no_follow_labels]];
						}
					}

					if ( '1' == $link_no_follow || 'Y' == $link_no_follow ) {
						update_post_meta( $new_link_ID, 'link_no_follow', true );
					} else {
						update_post_meta( $new_link_ID, 'link_no_follow', false );
					}

					$link_featured = 0;
					$featured_labels = array( 'Link Featured' );
					foreach( $featured_labels as $featured_label ) {
						if ( isset( $import_columns[$featured_label] ) ) {
							$link_featured = $data[$import_columns[$featured_label]];
						}
					}

					if ( '1' == $link_featured || 'Y' == $link_featured ) {
						update_post_meta( $new_link_ID, 'link_featured', true );
					} else {
						update_post_meta( $new_link_ID, 'link_featured', false );
					}

					$link_submitter_name = '';
					$submitter_name_labels = array( 'Link Submitter Name' );
					foreach( $submitter_name_labels as $submitter_name_label ) {
						if ( isset( $import_columns[$submitter_name_label] ) ) {
							$link_submitter_name = sanitize_text_field( $data[$import_columns[$submitter_name_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_submitter_name', $link_submitter_name );

					$link_submitter_email = '';
					$submitter_email_labels = array( 'Link Submitter E-mail' );
					foreach( $submitter_email_labels as $submitter_email_label ) {
						if ( isset( $import_columns[$submitter_email_label] ) ) {
							$link_submitter_email = sanitize_email( $data[$import_columns[$submitter_email_label]] );
						}
					}
					update_post_meta( $new_link_ID, 'link_submitter_email', $link_submitter_email );

					for ( $customurlfieldnumber = 1; $customurlfieldnumber < 6; $customurlfieldnumber++ ) {
						$custom_link = '';
						if ( $genoptions['customurl' . $customurlfieldnumber . 'active'] ) {
							$valuelabel = 'Custom URL ' . $customurlfieldnumber;
							$valuefield = 'link_custom_url_' . $customurlfieldnumber;
							if ( isset( $import_columns[$valuelabel] ) ) {
								$custom_link = esc_url( $data[$import_columns[$valuelabel]] );
							}

							update_post_meta( $new_link_ID, $valuefield, $custom_link );
						}
					}

					for ( $customtextfieldnumber = 1; $customtextfieldnumber < 6; $customtextfieldnumber++ ) {
						$custom_text = '';
						if ( $genoptions['customtext' . $customtextfieldnumber . 'active'] ) {
							$valuelabel = 'Custom Text ' . $customtextfieldnumber;
							$valuefield = 'link_custom_text_' . $customtextfieldnumber;
							if ( isset( $import_columns[$valuelabel] ) ) {
								$custom_text = sanitize_text_field( $data[$import_columns[$valuelabel]] );
							}

							update_post_meta( $new_link_ID, $valuefield, $custom_text );
						}
					}

					for ( $customlistnumber = 1; $customlistnumber < 6; $customlistnumber++ ) {
						$custom_list = '';
						if ( $genoptions['customlist' . $customlistnumber . 'active'] ) {
							$valuelabel = 'Custom List ' . $customlistnumber;
							$valuefield = 'link_custom_list_' . $customlistnumber;
							if ( isset( $import_columns[$valuelabel] ) ) {
								$custom_list = intval( $data[$import_columns[$valuelabel]] );
							}

							update_post_meta( $new_link_ID, $valuefield, $custom_list );
						}
					}
				}
			}
		}
	}			

	$row -= 1;

	$message = '9';

	wp_suspend_cache_addition( false );
	wp_defer_term_counting( false );
	wp_defer_comment_counting( false );

	return $message;
}

