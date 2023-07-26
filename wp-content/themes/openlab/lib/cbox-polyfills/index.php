<?php

/**
 * CBOX polyfills.
 *
 * Provides mocks and libraries from CBOX-OL, including the site-template feature.
 */


require_once __DIR__ . '/class-cbox-group-type.php';
require_once __DIR__ . '/class-cbox-sites-endpoint.php';

function cbox_get_main_site_id() {
	return 1;
}

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

		$types[ $type_slug ] = $type;
	}

	return $types;
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
		<input type="checkbox" style="display:none;" id="set-up-site-toggle" />
		<?php
	}
);
