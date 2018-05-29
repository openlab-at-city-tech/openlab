<?php

namespace OpenLab\Badges;

class Template {
	public static function init() {
		wp_register_style( 'openlab-badges', OLBADGES_PLUGIN_URL . '/assets/css/openlab-badges.css' );
		wp_register_script( 'openlab-badges', OLBADGES_PLUGIN_URL . '/assets/js/openlab-badges.js', array( 'jquery' ), false, true );
		add_action( 'bp_group_header_after_avatar', array( __CLASS__, 'avatar_links' ) );
		add_action( 'bp_group_directory_after_avatar', array( __CLASS__, 'avatar_links' ) );
	}

	public static function avatar_links() {
		wp_enqueue_style( 'openlab-badges' );
		wp_enqueue_script( 'openlab-badges' );

		$group_id = bp_get_group_id();

		$badge_group = new Group( $group_id );
		$group_badges = $badge_group->get_badges();

		if ( $group_badges ) {
			$html = '<ul class="badge-links">';
			foreach ( $group_badges as $group_badge ) {
				$html .= '<li>' . $group_badge->get_avatar_badge_html( $group_id ) . '</li>';
			}
			$html .= '</ul>';
		}

		echo $html;
	}
}
