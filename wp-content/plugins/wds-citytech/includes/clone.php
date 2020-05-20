<?php

/**
 * Network functionality related to group cloning.
 */

/**
 * Get the clone history of a group.
 *
 * Returns an array of group IDs, ordered from oldest to newest.
 *
 * @param int $group_id ID of the group.
 * @return array
 */
function openlab_get_group_clone_history( $group_id ) {
	$history = groups_get_groupmeta( $group_id, 'clone_history', true );
	if ( empty( $history ) ) {
		$history = array();

		// Legacy.
		$clone_source_group = groups_get_groupmeta( $group_id, 'clone_source_group_id', true );
		if ( $clone_source_group ) {
			$history[] = $clone_source_group;
		}
	}

	return array_map( 'intval', $history );
}

/**
 * Get more complete data about the clone history of a group.
 *
 * Returns an array of arrays, each of which has information about group names,
 * URLs, and creators.
 *
 * @param int $group_id        ID of the group.
 * @param int $exclude_creator Whether to exclude groups created by the specified user.
 *                             These groups are trimmed only from the end of the ancestry chain.
 *                             Default true.
 * @return array
 */
function openlab_get_group_clone_history_data( $group_id, $exclude_creator = null ) {
	$source_ids = openlab_get_group_clone_history( $group_id );

	$source_datas = array();
	foreach ( $source_ids as $source_id ) {
		$source_group = groups_get_group( $source_id );

		$course_code = groups_get_groupmeta( $source_id, 'wds_course_code' );
		$group_type  = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
				'case'     => 'upper',
			)
		);

		$source_data = array(
			'group_id'           => $source_id,
			'group_url'          => bp_get_group_permalink( $source_group ),
			'group_name'         => $course_code ? $course_code : $group_type,
			'group_creator_id'   => $source_group->creator_id,
			'group_creator_name' => bp_core_get_user_displayname( $source_group->creator_id ),
			'group_creator_url'  => bp_core_get_user_domain( $source_group->creator_id ),
		);

		$source_datas[] = $source_data;
	}

	// Trim exclude_creator groups.
	if ( $source_datas && null !== $exclude_creator ) {
		$exclude_creator = intval( $exclude_creator );
		$source_count    = count( $source_datas ) - 1;
		for ( $i = $source_count; $i >= 0; $i-- ) {
			if ( $source_datas[ $i ]['group_creator_id'] !== $exclude_creator ) {
				break;
			}

			unset( $source_datas[ $i ] );
		}
	}

	return $source_datas;
}

/**
 * Determines whether a group can be cloned.
 *
 * @param int $group_id ID of the group.
 */
function openlab_group_can_be_cloned( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	if ( ! $group_id ) {
		return false;
	}

	if ( openlab_is_portfolio( $group_id ) ) {
		return false;
	}

	$sharing_enabled_for_group = groups_get_groupmeta( $group_id, 'enable_sharing', true );

	return ! empty( $sharing_enabled_for_group );
}

/**
 * Determines whether a user can clone current group.
 *
 * @param int $user_id  Optional. User ID. Default current user ID.
 * @param int $group_id Optional. Group ID. Default current group ID.
 * @return bool
 */
function openlab_user_can_clone_group( $user_id = null, $group_id = null ) {
	if ( is_super_admin() ) {
		return true;
	}

	$user_id  = $user_id ?: get_current_user_id();
	$group_id = $group_id ?: bp_get_current_group_id();

	$group_type = openlab_get_group_type( $group_id );
	$user_type = xprofile_get_field_data( 'Account Type', $user_id );

	if ( 'course' === $group_type && 'Faculty' === $user_type ) {
		return true;
	}

	if ( 'course' !== $group_type && ! empty( $user_type ) ) {
		return true;
	}

	return false;
}

/** WIDGETS ******************************************************************/

/**
 * Initialize widgets.
 */
add_action(
	'widgets_init',
	function() {
		register_widget( 'OpenLab_Clone_Credits_Widget' );
		register_widget( 'OpenLab_Shareable_Content_Widget' );
	},
	20
);

/**
 * Load after BP and the rest of the application to selectively unregister or modify widgets.
 */
add_action(
	'bp_init',
	function() {
		if ( bp_is_root_blog() ) {
			return;
		}

		// Credits widget.
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$history  = openlab_get_group_clone_history( $group_id );

		if ( ! $history ) {
			foreach ( $GLOBALS['wp_registered_widgets'] as $widget_id => $_ ) {
				if ( 0 === strpos( $widget_id, 'openlab_clone_credits' ) ) {
					unset( $GLOBALS['wp_registered_widgets'][ $widget_id ] );
				}
			}
		} else {
			$group_type_label = openlab_get_group_type_label(
				array(
					'group_id' => $group_id,
					'case'     => 'upper',
				)
			);

			foreach ( $GLOBALS['wp_registered_widgets'] as $widget_id => $_ ) {
				if ( 0 === strpos( $widget_id, 'openlab_clone_credits' ) ) {
					$GLOBALS['wp_registered_widgets'][ $widget_id ]['description'] = 'Credits for your ' . $group_type_label . '.';
				}
			}
		}
	},
	1000
);

/**
 * Add a widget to the "main" sidebar.
 *
 * This function includes some guesswork about what the "main" sidebar is, based on the theme.
 *
 * @param string $widget
 */
function openlab_add_widget_to_main_sidebar( $widget ) {
	switch ( get_template() ) {
		case 'twentyten':
			$sidebar = 'primary-widget-area';
			break;

		case 'twentyfifteen':
		case 'twentyfourteen':
		case 'twentyeleven':
		case 'twentyseventeen':
		case 'twentysixteen':
		case 'twentythirteen':
		case 'twentytwelve':
			$sidebar = 'sidebar-1';
			break;

		default:
			$sidebar = reset( array_keys( $GLOBALS['wp_registered_sidebars'] ) );
			break;
	}

	// No doubles.
	$sidebars = get_option( 'sidebars_widgets', array() );
	$already  = false;
	if ( ! empty( $sidebars[ $sidebar ] ) ) {
		foreach ( $sidebars[ $sidebar ] as $widget_id ) {
			if ( 0 === strpos( $widget_id, $widget ) ) {
				$already = true;
				break;
			}
		}
	}

	if ( $already ) {
		return;
	}

	if ( ! class_exists( 'CBox_Widget_Setter' ) ) {
		include 'cbox-widget-setter.php';
	}

	CBox_Widget_Setter::set_widget(
		array(
			'id_base'    => $widget,
			'sidebar_id' => $sidebar,
		)
	);
}

class OpenLab_Clone_Credits_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'openlab_clone_credits_widget',
			'Credits',
			array(
				'description' => '',
			)
		);
	}

	/**
	 * Outputs the widget content.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$group    = groups_get_group( $group_id );
		$history  = openlab_get_group_clone_history_data( $group_id, $group->creator_id );

		$credits_groups = array_map(
			function( $clone_group ) {
					return sprintf(
						'<li><a href="%s">%s</a> &mdash; <a href="%s">%s</a></li>',
						esc_attr( $clone_group['group_url'] ),
						esc_html( $clone_group['group_name'] ),
						esc_attr( $clone_group['group_creator_url'] ),
						esc_html( $clone_group['group_creator_name'] )
					);
			},
			$history
		);

		echo $args['before_widget'];

		echo $args['before_title'] . 'Credits' . $args['after_title'];
		echo '<ul class="clone-credits">';
		echo implode( '', $credits_groups );
		echo '</ul>';
		echo $args['after_widget'];
	}

	/**
	 * Admin form.
	 */
	public function form( $instance ) {
		$group_id   = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$group_type = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
				'case'     => 'upper',
			)
		);

		?>
		<p>A list of the <?php echo $group_type; ?>s that contributed to your <?php echo $group_type; ?>.</p>
		<?php
	}

	/**
	 * Process form options.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

class OpenLab_Shareable_Content_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'openlab_shareable_content_widget',
			'Sharing',
			array(
				'description' => '',
			)
		);
	}

	/**
	 * Outputs the widget content.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Don't show if the user can't clone.
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

		$group_type_label = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
				'case'     => 'upper',
			)
		);

		$clone_link = add_query_arg(
			array(
				'clone' => $group_id,
				'type'  => openlab_get_group_type( $group_id ),
			),
			bp_get_groups_directory_permalink() . 'create/step/group-details/'
		);

		echo $args['before_widget'];

		echo $args['before_title'] . 'Sharing' . $args['after_title'];

		$can_clone = false;
		if ( is_user_logged_in() ) {
			$user_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
			$can_clone = 'faculty' === strtolower( $user_type );
		}

		echo '<p>';
		if ( $can_clone ) {
			echo sprintf( '<a class="btn btn-default btn-block btn-primary link-btn" href="%s"><i class="fa fa-clone" aria-hidden="true"></i> Clone this %s</a>', esc_attr( $clone_link ), esc_html( $group_type_label ) );
		} else {
			echo sprintf( 'Logged-in faculty members can clone this course. <a href="%s">Learn More!</a>', 'https://openlab.citytech.cuny.edu/blog/help/shared-cloning-for-faculty-only/' );
		}
		echo '</p>';

		echo $args['after_widget'];
	}

	/**
	 * Admin form.
	 */
	public function form( $instance ) {
		$group_id   = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$group_type = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
				'case'     => 'upper',
			)
		);

		?>
		<p>Provides a link for others to clone your <?php echo $group_type; ?>.</p>
		<?php
	}

	/**
	 * Process form options.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
