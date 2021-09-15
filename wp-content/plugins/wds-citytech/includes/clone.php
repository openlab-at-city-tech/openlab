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
	$history = [];

	$clone_source_group_id = groups_get_groupmeta( $group_id, 'clone_source_group_id', true );
	if ( ! $clone_source_group_id ) {
		return $history;
	}

	$history[] = $clone_source_group_id;

	$source_history = openlab_get_group_clone_history( $clone_source_group_id );

	$history = array_merge( $source_history, $history );

	return array_map( 'intval', $history );
}

/**
 * Gets all clones of a group.
 *
 * Returns only direct children.
 *
 * @param int $group_id ID of the parent group.
 * @return array Array of IDs.
 */
function openlab_get_clones_of_group( $group_id ) {
	global $wpdb, $bp;

	$clone_ids = wp_cache_get( $group_id, 'openlab_clones_of_group' );
	if ( false === $clone_ids ) {
		$clone_ids = $wpdb->get_col( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'clone_source_group_id' AND meta_value = %s", $group_id ) );

		wp_cache_set( $group_id, $clone_ids, 'openlab_clones_of_group' );
	}

	return array_map( 'intval', $clone_ids );
}

/**
 * Returns all clone descendants of a group.
 *
 * @param int   $group_id            ID of the group.
 * @param array $exclude_creator_ids Exclude groups created by these users.
 * @param bool  $exclude_hidden      Whether to exclude hidden groups.
 * @return array Array of IDs.
 */
function openlab_get_clone_descendants_of_group( $group_id, $exclude_creator_ids = [], $exclude_hidden = false ) {
	$descendants = openlab_get_clones_of_group( $group_id );
	if ( ! $descendants ) {
		return [];
	}

	foreach ( $descendants as $descendant ) {
		$descendants = array_merge( $descendants, openlab_get_clone_descendants_of_group( $descendant, $exclude_creator_ids, $exclude_hidden ) );
	}

	if ( $exclude_creator_ids ) {
		$descendants = array_filter(
			$descendants,
			function( $descendant_id ) use ( $exclude_creator_ids ) {
				$descendant = groups_get_group( $descendant_id );
				return ! in_array( $descendant->creator_id, $exclude_creator_ids, true );
			}
		);
	}

	if ( $exclude_hidden ) {
		$descendants = array_filter(
			$descendants,
			function( $descendant_id ) {
				$descendant = groups_get_group( $descendant_id );
				return 'hidden' !== $descendant->status;
			}
		);
	}

	return $descendants;
}

/**
 * Returns clone descendants count of a group.
 *
 * @param int  $group_id       ID of the group.
 * @param bool $exclude_hidden Whether to exclude hidden groups from the count.
 * @return int
 */
function openlab_get_clone_descendant_count_of_group( $group_id, $exclude_hidden = false ) {
	$group = groups_get_group( $group_id );

	$descendants = openlab_get_clone_descendants_of_group( $group_id, [ $group->creator_id ], $exclude_hidden );

	return count( $descendants );
}

/**
 * Busts the cache of ancestor clone caches.
 */
function openlab_invalidate_ancestor_clone_cache( $group_id ) {
	$ancestor_ids = openlab_get_group_clone_history( $group_id );
	foreach ( $ancestor_ids as $ancestor_id ) {
		wp_cache_delete( $ancestor_id, 'openlab_clones_of_group' );
	}
}

/**
 * Ensures that the cache of ancestor clones is invalidated on group deletion.
 */
add_action( 'groups_before_delete_group', 'openlab_invalidate_ancestor_clone_cache' );

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
		$source_datas[] = openlab_get_group_data_for_clone_history( $source_id );
	}

	// Trim exclude_creator groups.
	if ( $source_datas && null !== $exclude_creator ) {
		$exclude_creator = intval( $exclude_creator );
		$source_count    = count( $source_datas );
		for ( $i = 0; $i <= $source_count; $i++ ) {
			if ( $source_datas[ $i ]['group_creator_id'] !== $exclude_creator ) {
				continue;
			}

			unset( $source_datas[ $i ] );
		}
	}

	return $source_datas;
}

/**
 * Gets the formatted data for a group, for use in clone history.
 *
 * @param int $source_id
 * @return array
 */
function openlab_get_group_data_for_clone_history( $source_id ) {
	$source_group = groups_get_group( $source_id );

	$course_code = groups_get_groupmeta( $source_id, 'wds_course_code' );
	$group_type  = openlab_get_group_type_label(
		array(
			'group_id' => $source_id,
			'case'     => 'upper',
		)
	);

	$group_creators = openlab_get_group_creators( $source_id );

	$admins = [];
	foreach ( $group_creators as $group_creator ) {
		switch ( $group_creator['type'] ) {
			case 'member' :
				$user = get_user_by( 'slug', $group_creator['member-login'] );

				if ( $user ) {
					$admins[] = [
						'name' => bp_core_get_user_displayname( $user->ID ),
						'url'  => bp_core_get_user_domain( $user->ID ),
					];
				}
			break;

			case 'non-member' :
				$admins[] = [
					'name' => $group_creator['non-member-name'],
					'url'  => '',
				];
			break;
		}
	};

	$source_data = array(
		'group_id'           => $source_id,
		'group_url'          => bp_get_group_permalink( $source_group ),
		'group_name'         => $course_code ? $course_code : $group_type,
		'group_admins'       => $admins,
		'group_creator_id'   => $source_group->creator_id,
		'group_creator_name' => bp_core_get_user_displayname( $source_group->creator_id ),
		'group_creator_url'  => bp_core_get_user_domain( $source_group->creator_id ),
	);

	return $source_data;
}

/**
 * Formats the clone history as unordered list items of structured links.
 *
 * Note that you need to provide the <ul> wrapper yourself.
 */
function openlab_format_group_clone_history_data_list( $history ) {
	$credits_groups = array_map(
		function( $clone_group ) {
			$admin_names = array_map(
				function( $admin ) {
					if ( ! empty( $admin['url'] ) ) {
						return sprintf(
							'<a href="%s">%s</a>',
							esc_attr( $admin['url'] ),
							esc_html( $admin['name'] )
						);
					} else {
						return $admin['name'];
					}
				},
				$clone_group['group_admins']
			);

			return sprintf(
				'<li><a href="%s">%s</a> by %s</li>',
				esc_attr( $clone_group['group_url'] ),
				esc_html( $clone_group['group_name'] ),
				implode( ', ', $admin_names )
			);
		},
		$history
	);

	return implode( "\n", $credits_groups );
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
 * @param int    $index
 */
function openlab_add_widget_to_main_sidebar( $widget, $index = null ) {
	switch ( get_template() ) {
		case 'hemingway' :
		case 'genesis' :
			$sidebar = 'sidebar';
			break;

		case 'twentyten':
			$sidebar = 'primary-widget-area';
			break;

		case 'gillian' :
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
			'index'      => $index,
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
			'Acknowledgments',
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

		$group_type_label = openlab_get_group_type_label( [ 'group_id' => $group_id ] );

		$credits = openlab_get_credits( $group_id );

		if ( ! $credits['show_acknowledgements'] ) {
			return;
		}

		echo $args['before_widget'];

		echo $args['before_title'] . 'Acknowledgments' . $args['after_title'];

		foreach ( $credits['credits_chunks'] as $credits_chunk ) {
			if ( ! empty( $credits_chunk['intro'] ) ) {
				echo '<p>' . $credits_chunk['intro'] . '</p>';
			}

			if ( ! empty( $credits_chunk['items'] ) ) {
				echo '<ul class="clone-credits">';
				echo $credits_chunk['items'];
				echo '</ul>';
			}
		}

		if ( ! empty( $credits['post_credits_markup'] ) ) {
			echo $credits['post_credits_markup'];
		}

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
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

		// Don't show any widget content if Sharing is not enabled.
		if ( ! openlab_group_can_be_cloned( $group_id ) ) {
			return;
		}

		$group_type_label = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
				'case'     => 'upper',
			)
		);

		$group_type = openlab_get_group_type( $group_id );

		$clone_link = add_query_arg(
			array(
				'clone' => $group_id,
				'type'  => $group_type,
			),
			bp_get_groups_directory_permalink() . 'create/step/group-details/'
		);

		echo $args['before_widget'];

		echo $args['before_title'] . 'Sharing' . $args['after_title'];

		$can_clone = false;
		if ( is_user_logged_in() ) {
			if ( 'course' === $group_type ) {
				$user_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
				$can_clone = 'faculty' === strtolower( $user_type );
			} else {
				$can_clone = true;
			}
		}

		echo '<p>';
		if ( $can_clone ) {
			echo sprintf( '<a class="btn btn-default btn-block btn-primary link-btn" href="%s"><i class="fa fa-clone" aria-hidden="true"></i> Clone this %s</a>', esc_attr( $clone_link ), esc_html( $group_type_label ) );
		} else {
			if ( 'course' === $group_type ) {
				echo sprintf( 'Logged-in faculty members can clone this course. <a href="%s">Learn More!</a>', esc_attr( 'https://openlab.citytech.cuny.edu/blog/help/shared-cloning-for-faculty-only/' ) );
			} else {
				echo sprintf( 'Logged-in OpenLab members can clone this %s. <a href="%s">Learn More!</a>', esc_html( $group_type_label ), esc_attr( 'https://openlab.citytech.cuny.edu/blog/help/shared-cloning-for-projects-and-clubs/' ) );
			}
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
