<?php

class OpenLab_Group_Announcements_Extension extends BP_Group_Extension {
	public function __construct() {
		parent::init(
			[
				'slug' => 'announcements',
				'name' => 'Announcements',
				'visibility' => 'public',
				'nav_item_position' => 30,
			]
		);
	}

	public function display( $group_id = null ) {
		wp_enqueue_script( 'openlab-group-announcements' );
		wp_enqueue_style( 'openlab-quill-style' );
		bp_get_template_part( 'groups/single/announcements/index' );
	}
}
