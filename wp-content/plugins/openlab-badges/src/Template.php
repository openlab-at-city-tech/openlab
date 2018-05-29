<?php

namespace OpenLab\Badges;

class Template {
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );

		add_action( 'bp_group_header_after_avatar', array( __CLASS__, 'avatar_links' ) );
		add_action( 'bp_group_directory_after_avatar', array( __CLASS__, 'avatar_links' ) );

		add_filter( 'bp_before_has_groups_parse_args', array( __CLASS__, 'filter_group_args' ) );
	}

	public static function register_scripts() {
		wp_register_style( 'openlab-badges', OLBADGES_PLUGIN_URL . '/assets/css/openlab-badges.css' );
		wp_register_script( 'openlab-badges', OLBADGES_PLUGIN_URL . '/assets/js/openlab-badges.js', array( 'jquery' ), false, true );
	}

	public static function avatar_links() {
		wp_enqueue_style( 'openlab-badges' );
		wp_enqueue_script( 'openlab-badges' );

		$group_id = bp_get_group_id();

		$badge_group = new Group( $group_id );
		$group_badges = $badge_group->get_badges();

		$html = '';
		if ( $group_badges ) {
			$html .= '<ul class="badge-links">';
			foreach ( $group_badges as $group_badge ) {
				$html .= '<li>' . $group_badge->get_avatar_badge_html( $group_id ) . '</li>';
			}
			$html .= '</ul>';
		}

		echo $html;
	}

	public static function filter_group_args( $args ) {
		if ( ! isset( $_GET['group_badge'] ) || 'all' === $_GET['group_badge'] ) {
			return $args;
		}

		$badge_id = intval( $_GET['group_badge'] );

		// Tax query not currently supported for groups. See https://buddypress.trac.wordpress.org/ticket/4017.
		/*
		$tq = isset( $args['tax_query'] ) ? $args['tax_query'] : array();

		$tq['openlab_badge'] = array(
			'taxonomy' => 'openlab_badge',
			'term'     => $badge_id,
			'field'    => 'term_id',
		);

		$args['tax_query'] = $tq;
		*/

		$objects_in_term = bp_get_objects_in_term( $badge_id, 'openlab_badge' );
		if ( ! $objects_in_term ) {
			$objects_in_term = array( 0 );
		}

		if ( empty( $args['include'] ) ) {
			$args['include'] = $objects_in_term;
		} else {
			$args['include'] = array_intersect( (array) $args['include'], $objects_in_term );
		}

		return $args;
	}
}
