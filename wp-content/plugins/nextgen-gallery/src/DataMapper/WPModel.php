<?php

namespace Imagely\NGG\DataMapper;

/**
 * Abstract WordPress model class.
 *
 * Extends the base Model class to provide WordPress-specific functionality
 * and properties for working with WordPress post data.
 */
abstract class WPModel extends Model {

	/**
	 * WordPress post ID.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * Number of comments on the post.
	 *
	 * @var int
	 */
	public $comment_count;

	/**
	 * Comment status for the post.
	 *
	 * @var string
	 */
	public $comment_status;

	/**
	 * Extra post ID reference.
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Post filter.
	 *
	 * @var string
	 */
	public $filter;

	/**
	 * Post GUID.
	 *
	 * @var string
	 */
	public $guid;

	/**
	 * ID field name.
	 *
	 * @var string
	 */
	public $id_field;

	/**
	 * Menu order for the post.
	 *
	 * @var int
	 */
	public $menu_order;

	/**
	 * Ping status for the post.
	 *
	 * @var string
	 */
	public $ping_status;

	/**
	 * URLs that have been pinged.
	 *
	 * @var string
	 */
	public $pinged;

	/**
	 * Post author ID.
	 *
	 * @var int
	 */
	public $post_author;

	/**
	 * Post content.
	 *
	 * @var string
	 */
	public $post_content;

	/**
	 * Filtered post content.
	 *
	 * @var string
	 */
	public $post_content_filtered;

	/**
	 * Post creation date.
	 *
	 * @var string
	 */
	public $post_date;

	/**
	 * Post creation date in GMT.
	 *
	 * @var string
	 */
	public $post_date_gmt;

	/**
	 * Post excerpt.
	 *
	 * @var string
	 */
	public $post_excerpt;

	/**
	 * Post ID reference.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Post MIME type.
	 *
	 * @var string
	 */
	public $post_mime_type;

	/**
	 * Post modification date.
	 *
	 * @var string
	 */
	public $post_modified;

	/**
	 * Post modification date in GMT.
	 *
	 * @var string
	 */
	public $post_modified_gmt;

	/**
	 * Post slug/name.
	 *
	 * @var string
	 */
	public $post_name;

	/**
	 * Parent post ID.
	 *
	 * @var int
	 */
	public $post_parent;

	/**
	 * Post password.
	 *
	 * @var string
	 */
	public $post_password;

	/**
	 * Post status.
	 *
	 * @var string
	 */
	public $post_status;

	/**
	 * Post title.
	 *
	 * @var string
	 */
	public $post_title;

	/**
	 * Post type.
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Pricelist ID reference.
	 *
	 * @var int
	 */
	public $pricelist_id;

	/**
	 * URLs to ping.
	 *
	 * @var string
	 */
	public $to_ping;

	/**
	 * Gets the primary key column name for WordPress posts.
	 *
	 * @return string The primary key column name.
	 */
	public function get_primary_key_column() {
		return 'ID';
	}
}
