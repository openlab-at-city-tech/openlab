<?php

class OpenLab_Group_Announcements_Extension extends BP_Group_Extension {
	public function __construct() {
		$enabled = bp_is_group() ? openlab_is_announcements_enabled_for_group( bp_get_current_group_id() ) : false;

		$access = 'noone';
		if ( $enabled && bp_is_group() ) {
			if ( 'public' === groups_get_current_group()->status ) {
				$access = 'anyone';
			} else {
				$access = 'member';
			}
		}

		parent::init(
			[
				'slug'              => 'announcements',
				'name'              => 'Announcements',
				'access'            => $access,
				'nav_item_position' => 15,
			]
		);
	}

	public function display( $group_id = null ) {
		wp_enqueue_script( 'openlab-group-announcements' );
		wp_enqueue_style( 'openlab-quill-style' );
		bp_get_template_part( 'groups/single/announcements/index' );
	}
}
