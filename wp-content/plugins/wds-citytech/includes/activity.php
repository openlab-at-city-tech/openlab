<?php

/**
 * Modifications to the BP Activity component.
 */

/**
 * Rewrites the action string for new activity items when they are saved.
 *
 * @param BP_Activity_Activity $activity Activity object. Passed by reference.
 */
function openlab_modify_activity_action_on_save( BP_Activity_Activity $activity ) {
	switch ( $activity->type ) {
		case 'added_group_document' :
			$document = new BP_Group_Documents( $activity->secondary_item_id );

			$group_link = openlab_get_group_link( $activity->item_id );

			$file_link = sprintf(
				'<a href="%s">%s</a>',
				$document->get_url( 0 ),
				esc_html( $document->name )
			);

			$activity->action = sprintf(
				'%s uploaded the file %s in %s',
				bp_core_get_userlink( $activity->user_id ),
				$file_link,
				$group_link
			);
		break;

		case 'bbp_reply_create' :
			$group_link = openlab_get_group_link( $activity->item_id );

			$activity->action = preg_replace( '| in the forum <.*|', ' in ' . $group_link, $activity->action );
			$activity->action = str_replace( 'replied to the topic <', 'replied to the discussion topic <', $activity->action );
		break;

		case 'bbp_topic_create' :
			$group_link = openlab_get_group_link( $activity->item_id );

			$activity->action = preg_replace( '| in the forum <.*|', ' in ' . $group_link, $activity->action );
		break;

		case 'bp_doc_created' :
			// See openlab_filter_activity_action_bp_docs
		break;

		case 'bpeo_create_event' :
			$activity->action = str_replace( 'in the group <', 'in <', $activity->action );

			// Remove trailing period.
			$activity->action = rtrim( $activity->action, '.' );
		break;
	}

	// Always remove 'in the group'.
	$activity->action = str_replace( 'in the group <', 'in <', $activity->action );
}
add_action( 'bp_activity_before_save', 'openlab_modify_activity_action_on_save', 20 );

/**
 * Filters the activity action string for buddypress-docs activity items.
 */
function openlab_filter_activity_action_bp_docs( $action, $user_link, $doc_link, $is_new_doc, $query ) {
	$doc_id   = $query->doc_id;
	$group_id = bp_docs_get_associated_group_id( $doc_id );

	if ( ! $group_id ) {
		return $action;
	}

	$group_link = openlab_get_group_link( $group_id );

	$base_text = $is_new_doc ? '%1$s created the doc %2$s in %3$s' : '%1$s edited the doc %2$s in %3$s';

	return sprintf(
		$base_text,
		$user_link,
		$doc_link,
		$group_link
	);
}
add_filter( 'bp_docs_activity_action', 'openlab_filter_activity_action_bp_docs', 10, 5 );
