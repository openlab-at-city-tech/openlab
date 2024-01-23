<?php

namespace Imagely\NGG\DataMapper;

abstract class WPModel extends Model {

	public $ID;
	public $comment_count;
	public $comment_status;
	public $extras_post_id;
	public $filter;
	public $guid;
	public $id_field;
	public $menu_order;
	public $ping_status;
	public $pinged;
	public $post_author;
	public $post_content;
	public $post_content_filtered;
	public $post_date;
	public $post_date_gmt;
	public $post_excerpt;
	public $post_id;
	public $post_mime_type;
	public $post_modified;
	public $post_modified_gmt;
	public $post_name;
	public $post_parent;
	public $post_password;
	public $post_status;
	public $post_title;
	public $post_type;
	public $pricelist_id;
	public $to_ping;

	public function get_primary_key_column() {
		return 'ID';
	}
}
