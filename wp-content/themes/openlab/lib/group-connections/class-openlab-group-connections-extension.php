<?php

/**
 * Connections group extension.
 */
class OpenLab_Group_Connections_Extension extends BP_Group_Extension {
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$enabled = bp_is_group() ? openlab_is_connections_enabled_for_group( bp_get_current_group_id() ) : false;

		if ( $enabled && bp_is_group() ) {
			$access = openlab_user_can_initiate_group_connections() ? 'public' : 'noone';
		} else {
			$access = 'noone';
		}

		parent::init(
			[
				'slug'              => 'connections',
				'name'              => 'Connections',
				'access'            => $access,
				'nav_item_position' => 95,
			]
		);
	}

	/**
	 * Template loader.
	 *
	 * @param int $group_id ID of the group.
	 * @return void
	 */
	public function display( $group_id = null ) {
		wp_enqueue_script( 'openlab-group-connections' );

		switch ( bp_action_variable( 0 ) ) {
			case 'new' :
			case 'invitations' :
				$template_name = bp_action_variable( 0 );
			break;

			default :
				$template_name = 'index';
			break;
		}

		bp_get_template_part( 'groups/single/connections/' . $template_name );
	}
}
