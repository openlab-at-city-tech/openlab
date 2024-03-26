<?php

/*
 * Media-oriented functionality
 */

function openlab_get_home_slider() {
	global $post;
	$slider_mup = '';
	$slider_sr_mup = '';

	$slider_args = array(
		'post_type' => 'slider',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);

	$legacy = $post;
	$slider_query = new WP_Query($slider_args);

	if ($slider_query->have_posts()):
		$slider_mup = '<div class="camera_wrap clearfix" tabindex="-1" aria-hidden="true">';
		$slider_sr_mup = '<div class="camera_wrap_sr" role="widget"><h2 class="sr-only">Slideshow Content</h2><ul class="list-unstyled">';
		while ($slider_query->have_posts()) : $slider_query->the_post();
			//if the featured image is not set, slider will not be added
			if (get_post_thumbnail_id()) {

				$img_obj = wp_get_attachment_image_src(get_post_thumbnail_id(), 'front-page-slider');

				$slider_mup .= '<div data-alt="' . get_the_title() . '" data-src="' . $img_obj[0] . '"><div class="fadeIn camera_content"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</div></div>';
				$slider_sr_mup .= '<li class="sr-only sr-only-focusable camera_content" tabindex="0"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</li>';
			}
		endwhile;
		$slider_mup .= '</div>';
		$slider_sr_mup .= '</ul></div>';
	endif;

	$post = $legacy;

	return $slider_mup . $slider_sr_mup;
}

/**
 * WordPress adds dimensions to embedded images; this is totally not responsive WordPress
 * @param type $html
 * @return type
 */
function openlab_remove_thumbnail_dimensions($html) {

	$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);

	return $html;
}

add_filter('post_thumbnail_html', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('the_content', 'openlab_remove_thumbnail_dimensions', 10);

function openlab_activity_user_avatar() {
	global $activities_template;
	$current_activity_item = isset($activities_template->activity->current_comment) ? $activities_template->activity->current_comment : $activities_template->activity;
	$item_id = $current_activity_item->user_id;
	$item_id = apply_filters('bp_get_activity_avatar_item_id', $item_id);

	$alt = bp_core_get_user_displayname( $activities_template->activity->user_id );

	return '<img class="img-responsive" src ="' . bp_core_fetch_avatar(array('item_id' => $item_id, 'object' => 'user', 'type' => 'full', 'html' => false)) . '" alt="' . esc_attr( $alt ) . '"/>';
}

/**
 * Gets the group ID corresponding to an activity item.
 *
 * @param BP_Activity_Activity $activity
 * @return int
 */
function openlab_get_group_id_for_activity_item( $activity ) {
	switch ( $activity->type ) {
		case 'new_blog' :
			$item_id = openlab_get_group_id_by_blog_id( $activity->item_id );
		break;

		case 'bpeo_create_event' :
			$groups  = bpeo_get_event_groups( $activity->secondary_item_id );
			$item_id = empty( $groups ) ? 0 : reset( $groups );
		break;

		default :
			$item_id = $activity->item_id;
		break;
	}

	return $item_id;
}

function openlab_activity_group_avatar( $current_activity_item = null ) {
	global $activities_template;

	if ( null === $current_activity_item ) {
		$current_activity_item = isset($activities_template->activity->current_comment) ? $activities_template->activity->current_comment : $activities_template->activity;
	}

	$item_id = openlab_get_group_id_for_activity_item( $current_activity_item );

	$group = groups_get_group(array('group_id' => $item_id));

	return '<img class="img-responsive" src ="' . bp_core_fetch_avatar(array('item_id' => $item_id, 'object' => 'group', 'type' => 'full', 'html' => false)) . '" alt="' . $group->name . '"/>';
}

function openlab_activity_group_link( $current_activity_item = null ) {
	global $bp, $activities_template;

	if ( null === $current_activity_item ) {
		$current_activity_item = isset($activities_template->activity->current_comment) ? $activities_template->activity->current_comment : $activities_template->activity;
	}

	$item_id = openlab_get_group_id_for_activity_item( $current_activity_item );

	$group = groups_get_group(array('group_id' => $item_id));

	return get_site_url(0, $bp->groups->slug . '/' . $group->slug);
}

/**
 * Get the list of activity types that should appear in the What's Happening feed.
 *
 * @return array
 */
function openlab_whats_happening_activity_types() {
	return array( 'created_group', 'added_group_document', 'bbp_reply_create', 'bbp_topic_create', 'bpeo_create_event', 'bpeo_edit_event', 'bp_doc_comment', 'bp_doc_created', 'bp_doc_edited', 'deleted_group_document', 'joined_group', 'new_blog_comment', 'new_blog_post', 'new_forum_post', 'new_forum_topic', 'group_details_updated' );
}
/**
 * Get activity items for the What's Happening feed.
 *
 * @return array
 */
function openlab_whats_happening_activity_items() {
	$cached = wp_cache_get( 'whats_happening_items', 'openlab' );
	if ( ! $cached ) {
		$now = new DateTime();
		$activity_args = array(
			'per_page' => 10,
			'filter' => array(
				'action' => openlab_whats_happening_activity_types(),
			),
			'update_meta_cache' => false, //we'll be hitting this alot
			'date_query' => array(
				'before' => $now->format( 'Y-m-d H:i:s' ),
			),
			'show_hidden' => false,
		);

		$a = bp_activity_get( $activity_args );
		$cached = $a['activities'];
		wp_cache_set( 'whats_happening_items', $cached, 'openlab' );
	}

	return $cached;
}

/**
 * Invalidate whats_happening cache when a new activity item is posted.
 */
function openlab_invalidate_whats_happening_cache( $args ) {
	if ( in_array( $args['type'], openlab_whats_happening_activity_types(), true ) ) {
		wp_cache_delete( 'whats_happening_items', 'openlab' );
	}
}
add_action( 'bp_activity_add', 'openlab_invalidate_whats_happening_cache' );

/**
 * Generates activity feed
 * Filters by group-centric actions
 * @return type
 */
function openlab_whats_happening() {
	$cached = wp_cache_get( 'whats_happening', 'openlab' );
	if ( $cached ) {
		return $cached;
	}

	$whats_happening_out = '';

	ob_start();
	get_template_part( 'parts/home/whats-happening' );
	$whats_happening_out = ob_get_clean();

	wp_cache_set( 'whats_happening', $whats_happening_out, 'openlab', 5 * 60 );

	return $whats_happening_out;
}

/**
 * Gets most recent 10 news feed items for What's Happening At City Tech.
 *
 * @return array
 */
function openlab_whats_happening_at_city_tech_news_feed_items() {
	$items = [];

	$news_feed_url = 'https://www.citytech.cuny.edu/news/dashboard/odata/News';

	$news_feed_items = get_transient( 'whats_happening_at_city_tech_news' );
	if ( false === $news_feed_items ) {
		$news_request = wp_remote_get( $news_feed_url );
		if ( 200 === wp_remote_retrieve_response_code( $news_request ) ) {
			$news_feed_cached = wp_remote_retrieve_body( $news_request );
			$news_feed_cached = json_decode( $news_feed_cached, true );
			if ( $news_feed_cached && ! empty( $news_feed_cached['value'] ) ) {
				$news_feed_items  = $news_feed_cached['value'];
				set_transient( 'whats_happening_at_city_tech_news_items', $news_feed_items, 5 * 60 );
			}
		}
	}

	if ( ! is_countable( $news_feed_items ) ) {
		return $items;
	}

	// Get the last 10 items.
	for ( $i = count( $news_feed_items ) - 1; $i >= 0; $i-- ) {
		$news_feed_item = $news_feed_items[ $i ];

		$item_url = 'https://www.citytech.cuny.edu/news/?id=' . $news_feed_item['ID'];

		$items[] = [
			'content' => sprintf( '<a href="%s" target="_blank">%s</a>', $item_url, $news_feed_item['Title'] ),
			'date'    => strtotime( $news_feed_item['post_date'] ),
		];

		if ( count( $items ) >= 10 ) {
			break;
		}
	}

	// Enforce a sort by date, newest to oldest.
	usort(
		$items,
		function( $a, $b ) {
			return $b['date'] - $a['date'];
		}
	);

	return $items;
}

/**
 * Gets a list of active alerts from the City Tech alerts feed.
 *
 * @return array
 */
function openlab_whats_happening_at_city_tech_alerts_feed_items() {
	$alerts_feed_url = 'https://www.citytech.cuny.edu/alert/odata/alertAPI';

	$items = get_transient( 'whats_happening_at_city_tech_alerts_items' );
	if ( false === $items ) {
		$alerts_request = wp_remote_get( $alerts_feed_url );
		if ( 200 === wp_remote_retrieve_response_code( $alerts_request ) ) {
			$alerts_feed_cached = wp_remote_retrieve_body( $alerts_request );
			$alerts_feed_cached = json_decode( $alerts_feed_cached, true );
			if ( $alerts_feed_cached && ! empty( $alerts_feed_cached['value'] ) ) {
				$alerts_feed_items  = $alerts_feed_cached['value'];

				$categories_to_exclude = [
					// 100, // College open/closed; emergency maintenance on website.
					101, // Low-priority IT announcements
					104, // Maintenance for IT systems.
					105, // Software vulnerabilities.
					// 106, // President/CUNY announcements (COVID-19 policies, etc).
					// 107, // Public safety alerts.
					108, // Workshop announcements?
				];

				// Get all items that are marked 'active' and for which we're between the startDate and endDate.
				$now   = time();
				$items = [];
				for ( $i = count( $alerts_feed_items ) - 1; $i >= 0; $i-- ) {
					$alerts_feed_item = $alerts_feed_items[ $i ];

					if ( 'active' !== $alerts_feed_item['status'] ) {
						continue;
					}

					if ( in_array( (int) $alerts_feed_item['categoryID'], $categories_to_exclude, true ) ) {
						continue;
					}

					$start_date = strtotime( $alerts_feed_item['startDate'] );
					$end_date   = strtotime( $alerts_feed_item['endDate'] );

					if ( $now < $start_date || $now > $end_date ) {
						continue;
					}

					$message = wp_kses_post( bp_create_excerpt( $alerts_feed_item['message'], 300 ) );
					$items[] = [
						'content' => $message,
						'date'    => strtotime( $alerts_feed_item['lastUpdated'] ),
					];
				}

				// Enforce a sort by date, newest to oldest.
				usort(
					$items,
					function( $a, $b ) {
						return $b['date'] - $a['date'];
					}
				);

				set_transient( 'whats_happening_at_city_tech_alerts_items', $items, 5 * 60 );
			}
		}
	}

	// Only return the 5 most recent.
	$items = array_slice( $items, 0, 5 );

	return $items;
}

/**
 * Generates activity feed for City Tech feeds.
 *
 * @return string
 */
function openlab_whats_happening_at_city_tech() {
	$cached = wp_cache_get( 'whats_happening_at_city_tech', 'openlab' );
	if ( $cached ) {
		return $cached;
	}

	$whats_happening_out = '';

	ob_start();
	get_template_part( 'parts/home/whats-happening-at-city-tech' );
	$whats_happening_out = ob_get_clean();

	wp_cache_set( 'whats_happening_at_city_tech', $whats_happening_out, 'openlab', 5 * 60 );

	return $whats_happening_out;
}

add_action( 'rest_api_init', function () {
    register_rest_route(
		'openlab/v1',
		'/whats-happening-at-city-tech/',
		[
			'methods' => 'GET',
			'callback' => 'openlab_whats_happening_at_city_tech',
			'permission_callback' => '__return_true',
		]
	);
} );
