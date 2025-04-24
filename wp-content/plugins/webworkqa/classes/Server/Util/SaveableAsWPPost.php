<?php

namespace WeBWorK\Server\Util;

/**
 * Interface for items that are savable as a WP Post.
 *
 * @since 1.0.0
 */
interface SaveableAsWPPost {
	public function exists();

	public function get_id();
	public function get_author_id();
	public function get_content();
	public function get_post_date();

	public function set_id( $id );
	public function set_author_id( $id );
	public function set_content( $content );
	public function set_post_date( $post_date );
}
