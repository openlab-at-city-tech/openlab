<?php

/**
 * CBOX polyfills.
 *
 * Provides mocks and libraries from CBOX-OL, including the site-template feature.
 */


require_once __DIR__ . '/class-cbox-group-type.php';
require_once __DIR__ . '/class-cbox-member-type.php';
require_once __DIR__ . '/class-cbox-academic-unit.php';
require_once __DIR__ . '/class-cbox-academic-unit-type.php';

require_once __DIR__ . '/class-cbox-sites-endpoint.php';

function cbox_get_main_site_id() {
	return 1;
}

function cbox_is_main_site() {
	return get_current_blog_id() === cbox_get_main_site_id();
}

function cboxol_communication_settings_markup( $settings ) {}

function cboxol_get_group_group_type( $group_id ) {
	if ( bp_is_group_create() ) {
		$type = isset( $_GET['type'] ) ? wp_unslash( $_GET['type'] ) : '';
	} else {
		$type = openlab_get_group_type( $group_id );
	}

	if ( ! $type ) {
		return null;
	}

	return cboxol_get_group_type( $type );
}

function cboxol_get_group_type( $type ) {
	$all_types = cboxol_get_group_types();

	if ( ! isset( $all_types[ $type ] ) ) {
		return null;
	}

	return $all_types[ $type ];
}

function cboxol_get_group_types() {
	$ol_types = openlab_group_types();

	$types = [];
	foreach ( $ol_types as $type_slug ) {
		$type = new CBOX_Group_Type();
		$type->slug = $type_slug;
		$type->name = ucfirst( $type_slug );

		$types[ $type_slug ] = $type;
	}

	return $types;
}

function cboxol_get_course_group_type() {
	return cboxol_get_group_type( 'course' );
}

function cboxol_get_member_types() {
	$ol_member_types = openlab_get_member_types();
	$member_types = [];

	foreach ( $ol_member_types as $type ) {
		$member_type = new CBOX_Member_Type(
			[
				'slug' => $type->slug,
				'name' => $type->name,
				'can_create_courses' => in_array( $type->slug, [ 'faculty', 'staff' ], true ),
			]
		);

		$member_types[ $type->slug ] = $member_type;
	}

	return $member_types;
}

function cboxol_get_member_type( $slug ) {
	$member_types = cboxol_get_member_types();

	if ( ! isset( $member_types[ $slug ] ) ) {
		return null;
	}

	return $member_types[ $slug ];
}

function cboxol_get_user_member_type( $user_id ) {
	$member_type = openlab_get_user_member_type( $user_id );
	return cboxol_get_member_type( $member_type );
}

function cboxol_get_clone_source_group_id( $group_id ) {
	return (int) groups_get_groupmeta( $group_id, 'clone_source_group_id' );
}

/**
 * The /sites/ endpoint is needed for the search function when creating templates.
 */
add_action(
	'rest_api_init',
	function() {
		$sites_endpoint = new \CBOX\OL\API\Sites();
		$sites_endpoint->register_routes();
	}
);

/**
 * The CBOX-OL site template picker needs the 'new_group_type' variable.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! bp_is_group() && ! bp_is_group_create() ) {
			return;
		}

		$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );

		$js_data = array(
			'new_group_type' => $group_type->get_slug(),
		);

		?>

		<script type="text/javascript">var CBOXOL_Group_Create = <?php echo wp_json_encode( $js_data ); ?></script>

		<?php
	}
);

/**
 * The following functions create a 'Group Type Templates', mimicking what's in the CBOX-OL Group Type management UI.
 */
add_action(
	'admin_menu',
	function() {
		add_submenu_page(
			'edit.php?post_type=cboxol_site_template',
			'Group Type Templates',
			'Group Type Templates',
			'manage_network_options',
			'group-type-templates',
			'openlab_group_type_templates_cb'
		);
	}
);

add_action(
	'admin_init',
	function() {
		if ( empty( $_POST['openlab-default-site-template-ids-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'openlab_default_site_template_ids', 'openlab-default-site-template-ids-nonce' );

		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		if ( empty( $_POST['default-template'] ) ) {
			return;
		}

		$submitted = wp_unslash( $_POST['default-template'] );

		update_option( 'openlab_group_type_default_site_template_ids', $submitted );
	}
);

function openlab_group_type_templates_cb() {
	$group_types = cboxol_get_group_types();

	?>

	<style type="text/css">
.site-templates-list-item {
	display: flex;
	padding: 8px 16px;
}

.site-templates-list-header {
	font-weight: 700;
}

.site-templates-list-item:nth-child(even) {
	background: #ccc;
}

.site-template-radio {
	flex: 0 0 30px;
}

.site-template-name {
	flex: 0 0 40%;
}
	</style>

	<div class="wrap">
		<form method="post">

		<h1>Group Type Templates</h1>

		<p>Use this page to view the templates associated with a group type, and to select the default for that group type.</p>

		<?php foreach ( $group_types as $group_type ) : ?>
			<h2><?php echo esc_html( $group_type->get_label() ); ?></h2>

			<div class="group-type-templates">
				<div class="site-templates-list-item site-templates-list-header">
					<div class="site-template-radio">&nbsp;</div>
					<div class="site-template-name">Templates</div>
					<div class="site-template-links">Links</div>
				</div>


				<?php $type_templates = $group_type->get_site_templates(); ?>

				<?php foreach ( $type_templates as $type_template ) : ?>
					<div class="site-templates-list-item">
						<div class="site-template-radio"><input id="default-template-<?php echo esc_attr( $group_type->get_slug() ); ?>-<?php echo esc_attr( $type_template['id'] ); ?>" type="radio" name="default-template[<?php echo esc_attr( $group_type->get_slug() ); ?>]" value="<?php echo esc_attr( $type_template['id'] ); ?>" <?php checked( $type_template['id'] === $group_type->get_site_template_id() ); ?>></div>
						<div class="site-template-name"><label for="default-template-<?php echo esc_attr( $group_type->get_slug() ); ?>-<?php echo esc_attr( $type_template['id'] ); ?>"><?php echo esc_html( $type_template['name'] ); ?></label></div>
						<div class="site-template-links">
							<a href="<?php echo esc_url( $type_template['adminUrl'] ); ?>">Dashboard</a> |
							<a href="<?php echo esc_url( $type_template['url'] ); ?>">View Template</a>

						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<br /><hr />
		<?php endforeach; ?>

		<?php wp_nonce_field( 'openlab_default_site_template_ids', 'openlab-default-site-template-ids-nonce' ); ?>

		<?php submit_button( 'Save Changes' ); ?>

		</form>

	</div>

	<?php
}

/**
 * CBOX-OL registers the following fields on the site-template REST endpoint.
 */
function cboxol_register_rest_fields() {
	register_rest_field(
		'cboxol_site_template',
		'site_id',
		[
			'get_callback' => function( $object ) {
				return (int) get_post_meta( $object['id'], '_template_site_id', true );
			},
			'schema'       => array(
				'description' => __( 'Template site ID.', 'commons-in-a-box' ),
				'type'        => 'integer',
			),
		]
	);

	register_rest_field(
		'cboxol_site_template',
		'image',
		[
			'get_callback' => function( $object ) {
				return wp_get_attachment_image_url( $object['featured_media'], 'medium_large' );
			},
			'schema'       => array(
				'description' => __( 'Template site image.', 'commons-in-a-box' ),
				'type'        => 'string',
			),
		]
	);

	register_rest_field(
		'cboxol_site_template',
		'categories',
		[
			'get_callback' => function( $object ) {
				$data = [];

				foreach ( $object['template_category'] as $term_id ) {
					$term   = get_term_by( 'id', $term_id, 'cboxol_template_category' );
					$data[] = $term ? $term->name : '';
				}

				return $data;
			},
			'schema'       => array(
				'description' => __( 'Template site categories.', 'commons-in-a-box' ),
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
			),
		]
	);
}
add_action( 'init', 'cboxol_register_rest_fields', 100 );

/**
 * These hidden inputs are needed to mimic the markup of the CBOX-OL group create/edit panel.
 */
add_action(
	'openlab_after_group_site_markup',
	function() {
		?>
		<input type="hidden" name="source_blog" />
		<label for="set-up-site-toggle" class="sr-only">Set up site toggle (hidden)</label>
		<input type="checkbox" style="display:none;" id="set-up-site-toggle" />
		<?php
	}
);

function cboxol_get_academic_unit_types() {
	$group_types = openlab_group_types();

	$type_data = [
		'school' => [
			'label' => 'Schools',
			'parent' => '',
			'group_types' => $group_types,
		],
		'department' => [
			'label' => 'Departments',
			'parent' => 'school',
			'group_types' => $group_types,
		],
		'office' => [
			'label' => 'Offices',
			'parent' => '',
			'group_types' => $group_types,
		],
	];

	$types = [];
	foreach ( $type_data as $slug => $data ) {
		$type = new CBOX_Academic_Unit_Type(
			[
				'slug' => $slug,
				'name' => $data['label'],
				'parent' => $data['parent'],
				'group_types' => $data['group_types'],
			]
		);

		$types[ $slug ] = $type;
	}

	return $types;
}

function cboxol_get_academic_units( $args ) {
	$type = $args['type'];

	switch ( $type ) {
		case 'school':
		case 'office':
			$schools = 'school' === $type ? openlab_get_school_list() : openlab_get_office_list();
			$units = [];

			foreach ( $schools as $slug => $name ) {
				$unit = new CBOX_Academic_Unit(
					[
						'slug' => $slug,
						'name' => $name,
						'type' => $type,
					]
				);

				$units[] = $unit;
			}
			break;

		case 'department':
			$departments = openlab_get_entity_departments();
			$units = [];

			foreach ( $departments as $parent_unit => $unit_departments ) {
				foreach ( $unit_departments as $ud_slug => $ud_data ) {
					$units[] = new CBOX_Academic_Unit(
						[
							'slug' => $ud_slug,
							'name' => $ud_data['label'],
							'parent' => $parent_unit,
							'type' => $type,
						]
					);
				}
			}

			break;

		default:
			$units = cboxol_get_academic_units( [ 'type' => 'school' ] );
			$units = array_merge( $units, cboxol_get_academic_units( [ 'type' => 'department' ] ) );
			$units = array_merge( $units, cboxol_get_academic_units( [ 'type' => 'office' ] ) );
			break;
	}

	return $units;
}

/**
 * Get a specific academic unit.
 *
 * Assumes unique slugs.
 *
 * @param string Unit slug.
 * @return \WP_Error|\CBOX\OL\AcademicUnit
 */
function cboxol_get_academic_unit( $slug ) {
	if ( $slug ) {
		$units = cboxol_get_academic_units( [] );
		foreach ( $units as $unit ) {
			if ( $unit->get_slug() === $slug ) {
				return $unit;
			}
		}
	}

	return new WP_Error( 'no_academic_unit_found', __( 'No academic unit found.', 'commons-in-a-box' ) );
}

function cboxol_get_object_academic_units( $args ) {
	$r = array_merge(
		array(
			'object_id'   => null,
			'object_type' => null,
		),
		$args
	);

	if ( ! $r['object_id'] || ! $r['object_type'] ) {
		return false;
	}

	$taxonomy = '';
	switch ( $r['object_type'] ) {
		case 'user':
			$taxonomy = 'cboxol_member_in_acadunit';
			break;

		case 'group':
			$taxonomy = 'cboxol_group_in_acadunit';
			break;
	}

	if ( ! $taxonomy ) {
		return false;
	}

	$terms = wp_get_object_terms( $r['object_id'], $taxonomy );

	$units = array();
	foreach ( $terms as $term ) {
		$unit_slug = substr( $term->name, 10 );

		// Pretty elegant.
		$unit_post = get_post( $unit_slug );

		if ( $unit_post ) {
			$unit = cboxol_get_academic_unit( $unit_post->post_name );
			if ( ! is_wp_error( $unit ) ) {
				$units[ $unit->get_slug() ] = $unit;
			}
		}
	}

	if ( $units ) {
		uasort(
			$units,
			function( $a, $b ) {
				$a_order = $a->get_order();
				$b_order = $b->get_order();

				if ( $a_order === $b_order ) {
					$a_name = $a->get_name();
					$b_name = $b->get_name();

					return strcasecmp( $a_name, $b_name );
				} else {
					return $a_order > $b_order ? 1 : -1;
				}
			}
		);
	}

	return $units;
}

/**
 * Saves visibility data on template save.
 *
 * We have our own routine because academic units are not posts on the OpenLab,
 * and so don't pass the validation routine in CBOX-OL.
 *
 * @param int      $post_id The site template ID.
 * @param \WP_Post $post    The site template object.
 * @return void
 */
function openlab_cboxol_save_template_visibility( $post_id, \WP_Post $post ) {
	if ( ! current_user_can( 'manage_network_options' ) ) {
		return;
	}

	if ( empty( $_POST['cboxol-template-visibility-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'cboxol-template-visibility', 'cboxol-template-visibility-nonce' );

	$all_departments = openlab_get_entity_departments();

	$selected_academic_units = isset( $_POST['template-visibility-limit-to-academic-unit'] ) ? array_map( 'sanitize_text_field', $_POST['template-visibility-limit-to-academic-unit'] ) : [];
	$selected_academic_units = array_filter(
		$selected_academic_units,
		function( $unit_slug ) use ( $all_departments ) {
			$unit_exists = false;

			if ( isset( $all_departments[ $unit_slug ] ) ) {
				$unit_exists = true;
			}

			if ( ! $unit_exists ) {
				foreach ( $all_departments as $parent_unit => $unit_departments ) {
					if ( isset( $unit_departments[ $unit_slug ] ) ) {
						$unit_exists = true;
						break;
					}
				}
			}

			return $unit_exists;
		}
	);

	delete_post_meta( $post->ID, 'cboxol_template_academic_unit' );

	foreach ( $selected_academic_units as $selected_academic_unit_slug ) {
		add_post_meta( $post->ID, 'cboxol_template_academic_unit', $selected_academic_unit_slug );
	}
}
add_action( 'save_post', 'openlab_cboxol_save_template_visibility', 20, 2 );

/**
 * cboxol_get_template_academic_units() relies on WP post IDs for academic units.
 */
add_filter(
	'cboxol_get_template_academic_units',
	function( $units, $template_id ) {
		$selected_academic_units = (array) get_post_meta( $template_id, 'cboxol_template_academic_unit' );

		$departments = openlab_get_entity_departments();
		$schools = openlab_get_school_list();
		$offices = openlab_get_office_list();

		$new_units = [];
		foreach ( $selected_academic_units as $selected_academic_unit_slug ) {
			// Identify the type of the selected academic unit.
			$type = '';
			$name = '';
			if ( isset( $schools[ $selected_academic_unit_slug ] ) ) {
				$type = 'school';
				$name = $schools[ $selected_academic_unit_slug ];
			} elseif ( isset( $offices[ $selected_academic_unit_slug ] ) ) {
				$type = 'office';
				$name = $offices[ $selected_academic_unit_slug ];
			} else {
				foreach ( $departments as $parent_unit => $unit_departments ) {
					if ( isset( $unit_departments[ $selected_academic_unit_slug ] ) ) {
						$type = 'department';
						$name = $unit_departments[ $selected_academic_unit_slug ]['label'];
						break;
					}
				}
			}

			$new_units[] = new CBOX_Academic_Unit(
				[
					'slug' => $selected_academic_unit_slug,
					'name' => $name,
					'type' => 'department',
				]
			);
		}

		return $new_units;
	},
	10,
	2
);

/**
 * Modifies site template REST requests to restrict based on template visibility settings.
 *
 * Can't use the one from CBOX for a variety of reasons. For one, at the time of
 * template selection, no group has yet been created.
 *
 * @param array            $args    Array of query arguments.
 * @param \WP_REST_Request $request The request object.
 * @return array
 */
function openlab_cboxol_site_templates_rest_api_restrict_visibility( $args, $request ) {
	$member_type_meta_query = [
		'relation'  => 'OR',
		'all_types' => [
			[
				'key'     => 'cboxol_limit_template_by_member_type',
				'compare' => 'NOT EXISTS',
			],
		],
	];

	$allowed_member_types = [ 0 ];
	if ( is_user_logged_in() ) {
		$current_member_type = cboxol_get_user_member_type( bp_loggedin_user_id() );

		if ( $current_member_type ) {
			$member_type_meta_query['limited_types'] = [
				'relation' => 'AND',
				[
					'key'     => 'cboxol_limit_template_by_member_type',
					'compare' => 'EXISTS',
				],
				[
					'key'   => 'cboxol_template_member_type',
					'value' => $current_member_type->get_slug(),
				],
			];
		}
	}

	$academic_unit_meta_query = [
		'relation'  => 'OR',
		'all_types' => [
			[
				'key'     => 'cboxol_limit_template_by_academic_unit',
				'compare' => 'NOT EXISTS',
			],
		],
	];

	$allowed_academic_units = [ 0 ];
	$current_group_id       = $request->get_param( 'group_id' );
	if ( $current_group_id ) {
		// Get the group's academic units.
		$group_units = cboxol_get_object_academic_units(
			[
				'object_type' => 'group',
				'object_id'   => $current_group_id,
			]
		);

		if ( $group_units ) {
			$group_unit_ids = array_map(
				function( $unit ) {
					return $unit->get_wp_post_id();
				},
				$group_units
			);

			$academic_unit_meta_query['limited_types'] = [
				'relation' => 'AND',
				[
					'key'     => 'cboxol_limit_template_by_academic_unit',
					'compare' => 'EXISTS',
				],
				[
					'key'   => 'cboxol_template_academic_unit',
					'value' => $group_unit_ids,
				],
			];
		}
	}

	$meta_query   = isset( $args['meta_query'] ) ? $args['meta_query'] : [];
	$meta_query[] = $member_type_meta_query;
	$meta_query[] = $academic_unit_meta_query;

	$args['meta_query'] = $meta_query;

	return $args;
}
add_filter( 'rest_cboxol_site_template_query', 'openlab_cboxol_site_templates_rest_api_restrict_visibility', 10, 2 );
remove_filter( 'rest_cboxol_site_template_query', 'cboxol_site_templates_rest_api_restrict_visibility' );
