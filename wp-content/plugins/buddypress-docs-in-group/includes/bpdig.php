<?php

function bpdig_filter_group_extension_class( $class ) {
	require BPDIG_PLUGIN_DIR . 'includes/bpdig-group-extension.php';
	return 'BPDIG_Group_Extension';
}
add_action( 'bp_docs_group_extension_class_name', 'bpdig_filter_group_extension_class' );

/**
 * Redirect away from CPT URLs
 */
function bpdig_redirect_away_from_cpt_urls() {
	$o = get_queried_object();

	if ( ! is_a( $o, 'WP_Post' ) ) {
		return;
	}

	if ( bp_docs_get_post_type_name() !== $o->post_type ) {
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	$group_id = bp_docs_get_associated_group_id( $o->ID );
	if ( empty( $group_id ) ) {
		return;
	}

	bp_core_redirect( bp_docs_get_doc_link( $o->ID ) );
	die();
}
add_action( 'template_redirect', 'bpdig_redirect_away_from_cpt_urls' );

/**
 * Catch and process Delete requests.
 *
 * buddypress-docs does this in a terrible way, using `get_queried_object()`. Should be fixed upstream, but until then,
 * we'll override.
 *
 * Hooked at bp_actions:0 because buddypress-docs runs at bp_actions:1.
 */
function bpdig_catch_delete_request() {
	if ( bp_is_group() && bp_docs_is_existing_doc() && ! empty( $_GET['delete'] ) ) {
		check_admin_referer( 'bp_docs_delete' );

		if ( current_user_can( 'bp_docs_manage' ) ) {
			$current_doc = bp_docs_get_current_doc();

			if ( bp_docs_trash_doc( $current_doc->ID ) ) {
				bp_core_add_message( __( 'Doc successfully deleted!', 'bp-docs' ) );
			} else {
				bp_core_add_message( __( 'Could not delete doc.', 'bp-docs' ) );
			}
		} else {
			bp_core_add_message( __( 'You do not have permission to delete that doc.', 'bp-docs' ), 'error' );
		}

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . bp_docs_get_slug() );
		die();
	}
}
add_action( 'bp_actions', 'bpdig_catch_delete_request', 0 );

/**
 * Short-circuit Docs's theme compat logic when viewing a Doc create page.
 */
function bpdig_disable_theme_compat( $theme_compat ) {
	if ( ! bp_is_group() ) {
		return;
	}

	foreach ( $GLOBALS['wp_filter']['bp_replace_the_content'] as $priority => $priority_cbs ) {
		foreach ( $priority_cbs as $priority_cb ) {
			if ( ! is_array( $priority_cb['function'] ) ) {
				continue;
			}

			if ( ! ( $priority_cb['function'][0] instanceof BP_Docs_Theme_Compat ) ) {
				continue;
			}

			remove_filter( 'bp_replace_the_content', $priority_cb['function'], $priority, $priority_cb['accepted_args'] );
		}
	}

	$cbs = isset( $GLOBALS['bp_template_include_reset_dummy_post_data'] ) ? $GLOBALS['bp_template_include_reset_dummy_post_data'] : null;
	if ( $cbs ) {
		foreach ( $cbs as $priority => $priority_cbs ) {
			foreach ( $priority_cbs as $priority_cb ) {
				if ( ! is_array( $priority_cb['function'] ) ) {
					continue;
				}

				if ( ! ( $priority_cb['function'][0] instanceof BP_Docs_Theme_Compat ) ) {
					continue;
				}

				remove_filter( 'bp_template_include_reset_dummy_post_data', $priority_cb['function'], $priority, $priority_cb['accepted_args'] );
			}
		}
	}
}
add_action( 'bp_docs_setup_theme_compat', 'bpdig_disable_theme_compat' );

/**
 * During theme compat, Docs does some juggling with get_queried_object().
 * In the Groups context, fetching content is more straightforward.
 */
function bpdig_filter_the_content( $content ) {
	if ( bp_is_active( 'groups' ) && bp_is_group() ) {
		if ( function_exists( 'bp_restore_all_filters' ) ) {
			bp_restore_all_filters( 'the_content' );
		}

		$content = apply_filters( 'the_content', get_the_content() );

		if ( function_exists( 'bp_remove_all_filters' ) ) {
			bp_remove_all_filters( 'the_content' );
		}
	}

	return $content;
}
add_filter( 'bp_docs_get_the_content', 'bpdig_filter_the_content' );

/**
 * Correct the Doc permalink.
 */
function bpdig_filter_doc_link( $link, $doc_id ) {
	$group_id = bp_docs_get_associated_group_id( $doc_id );
	if ( ! empty( $group_id ) ) {
		$group = groups_get_group( array(
			'group_id' => $group_id,
		) );

		$doc = get_post( $doc_id );

		$link = bp_get_group_permalink( $group ) . bp_docs_get_docs_slug() . '/' . $doc->post_name . '/';
	}

	return $link;
}
add_filter( 'bp_docs_get_doc_link', 'bpdig_filter_doc_link', 100, 2 );

/**
 * Correct the Create link.
 */
function bpdig_get_create_link( $link ) {
	if ( ! bp_is_group() ) {
		return $link;
	}

	$link = bp_get_group_permalink( groups_get_current_group() ) . bp_docs_get_docs_slug() . '/' . BP_DOCS_CREATE_SLUG . '/';
	return $link;
}
add_filter( 'bp_docs_get_create_link', 'bpdig_get_create_link', 1000 );

/**
 * Correct tag links
 */
function bpdig_get_tag_link_url( $url, $args ) {
	if ( bp_is_group() ) {
		$base = bp_get_group_permalink( groups_get_current_group() ) . bp_docs_get_docs_slug() . '/';

		// I HAVE NO IDEA WHAT IS HAPPENING HERE
		if ( is_array( $args ) ) {
			foreach ( $args as $k => $v ) {
				if ( 'tag' === $k ) {
					$tag = $v;
					break;
				}
			}
		}

		if ( ! empty( $tag ) ) {
			$url = add_query_arg( 'bpd_tag', $tag, $base );
		}
	}

	return $url;
}
add_filter( 'bp_docs_get_tag_link_url', 'bpdig_get_tag_link_url', 100, 2 );

/**
 * Remove the Associated Group and Access Settings sections.
 */
add_filter( 'bp_docs_allow_associated_group', '__return_false' );
add_filter( 'bp_docs_allow_access_settings', '__return_false' );

/**
 * "doc_slug" is usually sniffed from the current object.
 */
function bpdig_filter_this_doc_slug( $slug, $query ) {
	if ( ! bp_is_group() ) {
		return $slug;
	}

	if ( ! bp_is_current_action( bp_docs_get_docs_slug() ) ) {
		return $slug;
	}

	if ( BP_DOCS_CREATE_SLUG === bp_action_variable( 0 ) ) {
		return '';
	}

	return bp_action_variable( 0 );
}
add_filter( 'bp_docs_this_doc_slug', 'bpdig_filter_this_doc_slug', 100, 2 );

function bpdig_filter_is_doc_edit( $is_doc_edit ) {
	if ( ! bp_is_group() ) {
		return $is_doc_edit;
	}

	if ( bp_is_action_variable( BP_DOCS_EDIT_SLUG, 1 ) ) {
		$is_doc_edit = true;
	}

	return $is_doc_edit;
}

function bpdig_filter_is_doc_history( $is_doc_history ) {
	if ( ! bp_is_group() ) {
		return $is_doc_edit;
	}

	if ( bp_is_action_variable( BP_DOCS_HISTORY_SLUG, 1 ) ) {
		$is_doc_history = true;
	}

	return $is_doc_history;
}

/**
 * Base for the redirect URL after saving a Doc.
 */
function bpdig_filter_post_save_redirect_base( $redirect_base ) {
	if ( ! function_exists( 'bp_is_group' ) ) {
		return $redirect_base;
	}

	if ( bp_is_group() ) {
		$redirect_base = bp_get_group_permalink( groups_get_current_group() ) . bp_docs_get_docs_slug() . '/';
	}

	return $redirect_base;
}
add_filter( 'bp_docs_post_save_redirect_base', 'bpdig_filter_post_save_redirect_base' );

/**
 * Ensure that bp_docs_is_existing_doc() returns true in a group context.
 *
 * Various features of Docs depend on this - see eg addon-history.php
 */
function bpdig_is_existing_doc( $is_existing_doc ) {
	// Only need to correct false negatives
	if ( $is_existing_doc ) {
		return $is_existing_doc;
	}

	if ( ! bp_is_group() ) {
		return $is_existing_doc;
	}

	if ( ! bp_is_current_action( bp_docs_get_docs_slug() ) ) {
		return $is_existing_doc;
	}

	if ( ! bp_action_variable( 0 ) || bp_is_action_variable( BP_DOCS_CREATE_SLUG, 0 ) ) {
		return $is_existing_doc;
	}

	return true;
}
add_filter( 'bp_docs_is_existing_doc', 'bpdig_is_existing_doc' );
add_filter( 'bp_docs_is_single_doc', 'bpdig_is_existing_doc' );

/**
 * bp_docs_is_doc_edit()
 */
function bpdig_is_doc_edit( $is_doc_edit ) {
	if ( ! bp_is_group() ) {
		return $is_doc_edit;
	}

	if ( ! bp_docs_is_existing_doc() ) {
		return $is_doc_edit;
	}

	return bp_is_action_variable( BP_DOCS_EDIT_SLUG, 1 );
}
add_filter( 'bp_docs_is_doc_edit', 'bpdig_is_doc_edit' );

/**
 * bp_docs_is_doc_create()
 */
function bpdig_is_doc_create( $is_doc_create ) {
	if ( ! bp_is_group() || ! bp_is_current_action( bp_docs_get_docs_slug() ) ) {
		return $is_doc_create;
	}

	return bp_is_action_variable( BP_DOCS_CREATE_SLUG, 0 );
}
add_filter( 'bp_docs_is_doc_create', 'bpdig_is_doc_create' );

/**
 * Filter the doc used for implicit capability mapping.
 */
function bpdig_get_doc_for_caps( $doc, $args ) {
	return bpdig_get_current_doc( $doc );
}
add_filter( 'bp_docs_get_doc_for_caps', 'bpdig_get_doc_for_caps', 10, 2 );

function bpdig_get_current_doc( $doc ) {
	static $_doc;

	if ( ! bp_docs_is_existing_doc() ) {
		return $doc;
	}

	if ( ! bp_is_group() ) {
		return $doc;
	}

	// Only need to do this once
	if ( ! is_null( $_doc ) ) {
		return $_doc;
	}

	global $wpdb;
	$doc_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_name = %s LIMIT 1", bp_docs_get_post_type_name(), bp_action_variable( 0 ) ) );

	if ( $doc_id ) {
		$_doc = get_post( $doc_id );
	}

	return $_doc;
}
add_filter( 'bp_docs_get_current_doc', 'bpdig_get_current_doc' );

/**
 * Protect access to Create page.
 */
function bpdig_protect_create_page() {
	if ( bp_is_group() && bp_is_current_action( bp_docs_get_docs_slug() ) && bp_is_action_variable( BP_DOCS_CREATE_SLUG, 0 ) ) {
		if ( ! current_user_can( 'bp_docs_associate_with_group', bp_get_current_group_id() ) ) {
			bp_core_add_message( 'You do not have permission to do that.', 'error' );

			$redirect = bp_get_group_permalink( groups_get_current_group() ) . bp_docs_get_docs_slug() . '/';
			bp_core_redirect( $redirect );
			die();
		}
	}
}
add_action( 'bp_screens', 'bpdig_protect_create_page' );

/**
 * Force docs to be associated with the current group.
 */
function bpdig_force_group_association( $query ) {
	$doc_id = $query->doc_id;

	if ( ! bp_is_group() ) {
		return $doc_id;
	}

	bp_docs_set_associated_group_id( $doc_id, bp_get_current_group_id() );

	// Force the access settings, which will not have been passed in the $_POST.
	$group = groups_get_current_group();
	switch ( $group->status ) {
		case 'private' :
		case 'hidden' :
			$access_setting = 'group-members';
			break;

		case 'public' :
			$access_setting = 'anyone';
			break;
	}

	bp_docs_update_doc_access( $doc_id, $access_setting );
}
add_filter( 'bp_docs_doc_saved', 'bpdig_force_group_association', 5 );

/**
 * Parent dropdown should show only docs in the group.
 */
function bpdig_parent_dropdown_args( $args ) {
	if ( bp_is_group() ) {
		$args['group_id'] = bp_get_current_group_id();
	}

	return $args;
}
add_filter( 'bp_docs_parent_dropdown_query_args', 'bpdig_parent_dropdown_args' );

/**
 * Ensure comments are open when viewing a Doc.
 */
function bpdig_comments_open( $open, $post_id ) {
	$post = get_post( $post_id );
	if ( ! isset( $post->post_type ) || bp_docs_get_post_type_name() !== $post->post_type ) {
		return $open;
	}

	return true;
}
add_filter( 'comments_open', 'bpdig_comments_open', 10, 2 );

/**
 * Don't show an 'Unlink from group' link in Docs directories.
 */
add_action(
	'bp_screens',
	function() {
		$bp = buddypress();
		if ( empty( $bp->bp_docs ) || empty( $bp->bp_docs->groups_integration ) ) {
			return;
		}

		remove_filter( 'bp_docs_doc_action_links', [ buddypress()->bp_docs->groups_integration, 'add_doc_action_unlink_from_group_link' ], 10, 2 );
	},
	5
);
