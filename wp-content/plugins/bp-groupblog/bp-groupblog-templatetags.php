<?php
/**
 * BuddyPress Groupblog template functions.
 *
 * @package BP_Groupblog
 */

/**
 * BuddyPress Groupblog has blog posts.
 *
 * Unused and broken since there's no BP_groupblog_Template class.
 *
 * @since 1.0
 */
function bp_groupblog_has_blog_posts() {
	global $bp, $blog_posts_template;
	$blog_posts_template = new BP_groupblog_Template( $bp->displayed_user->id );
	return $blog_posts_template->has_blog_posts();
}

/**
 * BuddyPress Groupblog the blog post.
 *
 * Unused and broken since there's no BP_groupblog_Template class.
 *
 * @since 1.0
 */
function bp_groupblog_the_blog_post() {
	global $blog_posts_template;
	return $blog_posts_template->the_blog_post();
}

/**
 * BuddyPress Groupblog blog posts.
 *
 * Unused and broken since there's no BP_groupblog_Template class.
 *
 * @since 1.0
 */
function bp_groupblog_blog_posts() {
	global $blog_posts_template;
	return $blog_posts_template->user_blog_posts();
}

/**
 * BuddyPress Groupblog blog post name.
 *
 * Unused and broken since there's no BP_groupblog_Template class.
 *
 * @since 1.0
 */
function bp_groupblog_blog_post_name() {
	global $blog_posts_template;
	echo '';
}

/**
 * BuddyPress Groupblog post pagination.
 *
 * Unused and broken since there's no BP_groupblog_Template class.
 *
 * @since 1.0
 */
function bp_groupblog_blog_post_pagination() {
	global $blog_posts_template;
	echo $blog_posts_template->pag_links;
}

/**
 * Makes a checkbox checked when a group has a groupblog.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 */
function bp_groupblog_show_enabled( $group_id ) {
	if ( groups_get_groupmeta( $group_id, 'groupblog_enable_blog' ) == '1' ) {
		echo ' checked="checked"';
	}
}

/**
 * Checks if a group has an enabled blog.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 * @return bool True if a group has a groupblog, false otherwise.
 */
function bp_groupblog_is_blog_enabled( $group_id ) {
	if ( groups_get_groupmeta( $group_id, 'groupblog_enable_blog' ) == '1' ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Checks if a group has an existing blog.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 * @return bool True if a group has an existing groupblog, false otherwise.
 */
function bp_groupblog_blog_exists( $group_id ) {
	if ( ! groups_get_groupmeta( $group_id, 'groupblog_blog_id' ) == '' ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Checks if a group has silent add enabled.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 * @return bool True if a group has silent add enabled, false otherwise.
 */
function bp_groupblog_silent_add( $group_id ) {
	if ( ! groups_get_groupmeta( $group_id, 'groupblog_silent_add' ) == '' ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Echos the blog id of the current group's blog unless $group_id is explicitly passed in.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 */
function groupblog_blog_id( $group_id = '' ) {
	echo get_groupblog_blog_id( $group_id );
}

/**
 * Gets the blog ID for a given group ID.
 *
 * @since 1.0
 *
 * @param int $group_id The ID of a group.
 * @return int The ID of a blog.
 */
function get_groupblog_blog_id( $group_id = '' ) {
	global $bp;
	if ( $group_id == '' ) {
		$group_id = $bp->groups->current_group->id;
	}
	return groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
}

/**
 * Echos the group ID of the group associated with the blog ID that is passed in.
 *
 * @since 1.0
 *
 * @param int $blog_id The ID of a blog.
 */
function groupblog_group_id( $blog_id ) {
	echo get_groupblog_group_id( $blog_id );
}

/**
 * Gets the group ID for a given blog ID.
 *
 * @since 1.0
 *
 * @param int $blog_id The ID of a blog.
 * @return int|bool $group_id The ID of a group, or false on failure.
 */
function get_groupblog_group_id( $blog_id ) {
	global $bp, $wpdb;

	if ( ! isset( $blog_id ) ) {
		return false;
	}

	// table_name_groupmeta is not defined on first install.
	if ( ! isset( $bp->groups->table_name_groupmeta ) ) {
		return false;
	}

	$group_id = 0;

	$get = BP_Groups_Group::get(
		[
			'fields'     => 'ids',
			'meta_query' => [
				[
					'key'   => 'groupblog_blog_id',
					'value' => (int) $blog_id
				]
			]
		]
	);

	if ( ! empty( $get['groups'] ) ) {
		$group_id = $get['groups'][0];
	}

	return $group_id;
}

/**
 * Echos the group id of the group associated with the blog id.
 *
 * @since 1.0
 */
function bp_groupblog_id() {
	echo bp_get_groupblog_id();
}

/**
 * Returns the group id of the group associated with the current blog.
 *
 * @since 1.0
 *
 * @return int $group_id The ID of a group.
 */
function bp_get_groupblog_id() {
	global $current_blog;
	return apply_filters( 'bp_get_groupblog_id', get_groupblog_group_id( $current_blog->blog_id ) );
}

/**
 * Echos the group slug of the group associated with the blog id.
 *
 * @since 1.0
 */
function bp_groupblog_slug() {
	echo bp_get_groupblog_slug();
}

/**
 * Gets the group slug of the group associated with the blog id.
 *
 * @since 1.0
 *
 * @return str $slug The group slug.
 */
function bp_get_groupblog_slug() {
	$group = groups_get_group( array( 'group_id' => bp_get_groupblog_id() ) );
	return apply_filters( 'bp_get_groupblog_slug', $group->slug );
}

/**
 * Echos the ID of the forum associated with the current group id.
 *
 * @since 1.0
 */
function bp_groupblog_forum() {
	echo bp_get_groupblog_forum();
}

/**
 * Gets the ID of the forum associated with the current group id.
 *
 * @since 1.0
 *
 * @return int $forum_id The ID of the forum.
 */
function bp_get_groupblog_forum() {
	global $bp;
	$forum_id = groups_get_groupmeta( bp_get_groupblog_id(), 'forum_id' );
	return apply_filters( 'bp_get_groupblog_forum', $forum_id );
}

/**
 * Gets the current layout.
 *
 * @since 1.0
 *
 * @return str $template_name The current layout.
 */
function groupblog_current_layout() {
	$checks = get_site_option( 'bp_groupblog_blog_defaults_options' );
	$template_name = $checks['page_template_layout'];
	return $template_name;
}

/**
 * Allows the group admin layout.
 *
 * @since 1.0
 *
 * @return bool True if allowed, false otherwise.
 */
function bp_groupblog_allow_group_admin_layout() {
	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );
	if ( ! empty( $opt ) && $opt['group_admin_layout'] == 1 && $opt['theme'] == 'p2|p2-buddypress' ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Gets the page template layout option.
 *
 * @since 1.0
 *
 * @return str The page template layout option.
 */
function groupblog_get_page_template_layout() {
	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );
	return $opt['page_template_layout'];
}

/**
 * Locates the page template layout option.
 *
 * @since 1.0
 */
function groupblog_locate_layout() {
	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );
	if ( ( $opt['group_admin_layout'] != 1 ) || ! ( $template_name = groups_get_groupmeta( bp_get_groupblog_id(), 'page_template_layout' ) ) ) {
		$template_name = $opt['page_template_layout'];
	}
	locate_template( array( 'groupblog/layouts/' . $template_name . '.php' ), true );
}

/**
 * Echoes the admin form URL.
 *
 * @since 1.0
 *
 * @param str $page The page slug.
 * @param object $group The group.
 */
function bp_groupblog_admin_form_action( $page, $group = false ) {
	global $bp, $groups_template;
	if ( ! $group ) {
		$group =& $groups_template->group;
	}
	echo apply_filters( 'bp_groupblog_admin_form_action', bp_group_permalink( $group, false ) . '/admin/' . $page );
}
