<?php
/**
 * Custom Achievement Steps UI
 *
 * @package BadgeOS
 * @subpackage Achievements
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com
 */

/**
 * Add our Steps JS to the Badge post editor
 *
 * @since 1.0.0
 * @return void
 */
function badgeos_steps_ui_admin_scripts() {
	global $post_type;
	if ( in_array( $post_type, badgeos_get_achievement_types_slugs(), true ) ) {
		wp_enqueue_script( 'badgeos-steps-ui', $GLOBALS['badgeos']->directory_url . 'js/steps-ui.js', array( 'jquery-ui-sortable' ), '2.0.0', true );
	}
}
add_action( 'admin_print_scripts-post-new.php', 'badgeos_steps_ui_admin_scripts', 11 );
add_action( 'admin_print_scripts-post.php', 'badgeos_steps_ui_admin_scripts', 11 );

/**
 * Adds our Steps metabox to the Badge post editor
 *
 * @since  1.0.0
 * @return void
 */
function badgeos_add_steps_ui_meta_box() {
	$achievement_types_temp = badgeos_get_achievement_types_slugs();
	$achievement_types      = array();
	$badgeos_settings       = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	if ( $achievement_types_temp ) {
		foreach ( $achievement_types_temp as $key => $ach ) {
			if ( ! empty( $ach ) && trim( $badgeos_settings['achievement_step_post_type'] ) !== $ach ) {
				$achievement_types[] = $ach;
			}
		}
	}

	if ( $achievement_types ) {
		foreach ( $achievement_types as $achievement_type ) {
			add_meta_box( 'badgeos_steps_ui', apply_filters( 'badgeos_steps_ui_title', esc_html__( 'Required Steps', 'badgeos' ) ), 'badgeos_steps_ui_meta_box', $achievement_type, 'advanced', 'high' );
		}
	}
}
add_action( 'add_meta_boxes', 'badgeos_add_steps_ui_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new step is added.
 *
 * @since  1.0.0
 * @param  object $post The current post object.
 * @return void
 */
function badgeos_steps_ui_meta_box( $post = null ) {

	// Grab our Badge's required steps.
	$badgeos_settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	$required_steps   = get_posts(
		array(
			'post_type'           => trim( $badgeos_settings['achievement_step_post_type'] ),
			'posts_per_page'      => -1,
			'suppress_filters'    => false,
			'connected_direction' => 'to',
			'connected_type'      => trim( $badgeos_settings['achievement_step_post_type'] ) . '-to-' . $post->post_type,
			'connected_items'     => $post->ID,
		)
	);

	// Loop through each step and set the sort order.
	foreach ( $required_steps as $required_step ) {
		$required_step->order = get_step_menu_order( $required_step->ID );
	}

	// Sort the steps by their order.
	uasort( $required_steps, 'badgeos_compare_step_order' );

	echo '<p>' . esc_html__( 'Define the required "steps" for this achievement to be considered complete. Use the "Label" field to optionally customize the titles of each step.', 'badgeos' ) . '</p>';

	// Concatenate our step output.
	echo '<ul id="steps_list">';
	foreach ( $required_steps as $step ) {
		badgeos_steps_ui_html( $step->ID, $post->ID );
	}
	echo '</ul>';

	// Render our buttons.
	echo '<input style="margin-right: 1em" class="button" type="button" onclick="badgeos_add_new_step(' . esc_attr( $post->ID ) . ');" value="' . esc_html( apply_filters( 'badgeos_steps_ui_add_new', esc_html__( 'Add New Step', 'badgeos' ) ) ) . '">';
	echo '<input class="button-primary" type="button" onclick="badgeos_update_steps();" value="' . esc_html( apply_filters( 'badgeos_steps_ui_save_all', esc_html__( 'Save All Steps', 'badgeos' ) ) ) . '">';
	echo '<img class="save-steps-spinner" src="' . esc_url( admin_url( '/images/wpspin_light.gif' ) ) . '" style="margin-left: 10px; display: none;" />';

}

/**
 * Helper function for generating the HTML output for configuring a given step.
 *
 * @since  1.0.0
 * @param  integer $step_id The given step's ID.
 * @param  integer $post_id The given step's parent $post ID.
 */
function badgeos_steps_ui_html( $step_id = 0, $post_id = 0 ) {

	// Grab our step's requirements and measurement.
	$requirements             = badgeos_get_step_requirements( $step_id );
	$count                    = ! empty( $requirements['count'] ) ? $requirements['count'] : 1;
	$achievement_types        = badgeos_get_achievement_types_slugs();
	$badgeos_settings         = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	$dynamic_triggers         = array();
	$badgeos_subtrigger_value = $requirements['badgeos_subtrigger_value'];
	$badgeos_subtrigger_id    = $requirements['badgeos_subtrigger_id'];
	$badgeos_fields_data      = $requirements['badgeos_fields_data'];
	?>

	<li class="step-row step-<?php echo esc_attr( $step_id ); ?>" data-step-id="<?php echo esc_attr( $step_id ); ?>">
		<div class="step-handle"></div>
		<a class="delete-step" href="javascript: badgeos_delete_step( <?php echo esc_attr( $step_id ); ?> );"><?php echo esc_html__( 'Delete', 'badgeos' ); ?></a>
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
		<input type="hidden" name="order" value="<?php echo esc_attr( get_step_menu_order( $step_id ) ); ?>" />

		<?php echo esc_html( apply_filters( 'badgeos_steps_ui_html_require_text', esc_html__( 'Require', 'badgeos' ), $step_id, $post_id ) ); ?>

		<?php do_action( 'badgeos_steps_ui_html_after_require_text', $step_id, $post_id ); ?>

		<select class="select-trigger-type" data-step-id="<?php echo esc_attr( $step_id ); ?>">
			<?php
			foreach ( badgeos_get_activity_triggers() as $value => $label ) {
				if ( is_array( $label ) ) {
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $requirements['trigger_type'], $value, false ) . '>' . esc_html( $label['label'] ) . '</option>';
					$dynamic_triggers[ $value ] = $label;
				} else {
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $requirements['trigger_type'], $value, false ) . '>' . esc_html( $label ) . '</option>';
				}
			}
			?>
		</select>

		<?php do_action( 'badgeos_steps_ui_html_after_trigger_type', $step_id, $post_id ); ?>

		<?php
		if ( count( $dynamic_triggers ) > 0 ) {
			foreach ( $dynamic_triggers as $key => $data ) {
				$fields_group = array();
				?>
		<div id="badgeos_achievements_step_dynamic_section_<?php echo esc_attr( $key ); ?>" style="display:inline-block">
			<select id="badgeos_achievements_step_ddl_dynamic_<?php echo esc_attr( $key ); ?>" data-trigger="<?php echo esc_attr( $key ); ?>" class="badgeos_achievements_step_fields badgeos_achievements_step_ddl_dynamic" name="badgeos_achievements_step_ddl_dynamic_<?php echo esc_attr( $key ); ?>">
				<?php
				foreach ( $data['sub_triggers'] as $key2 => $data2 ) {
					$sub_fields = array();
					echo '<option value="' . esc_attr( $data2['trigger'] ) . '" ' . selected( $data2['trigger'], $badgeos_subtrigger_value, false ) . ' >' . esc_attr( $data2['label'] ) . '</option>';

					foreach ( $data2['fields'] as $fieldkey => $field ) {
						$sub_fields[] = $field;
					}

					$fields_group[ $data2['trigger'] ] = $sub_fields;
				}

				echo '</select>';

				foreach ( $fields_group as $fields_groupkey => $fields ) {
					foreach ( $fields as $fieldkey => $field ) {
						$sel_val = '';
						if ( isset( $badgeos_fields_data[ $field['id'] ] ) ) {
							$sel_val = $badgeos_fields_data[ $field['id'] ];
						}

						switch ( trim( $field['type'] ) ) {
							case 'select':
								echo '<select id="' . esc_attr( $field['id'] ) . '" class="badgeos_achievements_step_fields badgeos_achievements_step_subddl_dynamic badgeos_achievements_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_achievements_step_subddl_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $field['id'] ) . '">';
								foreach ( $field['options'] as $ddlkey => $ddlval ) {
									echo '<option value="' . esc_attr( $ddlkey ) . '" ' . ( $ddlkey === $sel_val ? 'selected' : '' ) . '>' . esc_attr( $ddlval ) . '</option>';
								}
								echo '</select>';
								break;
							case 'text':
								echo '<input value="' . esc_attr( $sel_val ) . '" type="' . esc_attr( $field['type'] ) . '" size="4" id="' . esc_attr( $field['id'] ) . '"  class="badgeos_achievements_step_fields badgeos_achievements_step_subtxt_dynamic badgeos_achievements_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_achievements_step_subtxt_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $field['id'] ) . '" />';
								break;
							case 'number':
								echo '<input value="' . esc_attr( $sel_val ) . '" type="' . esc_attr( $field['type'] ) . '" size="4" step="1" min="0" id="' . esc_attr( $field['id'] ) . '" class="badgeos_achievements_step_fields badgeos_achievements_step_subtxt_dynamic badgeos_achievements_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_achievements_step_subtxt_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $field['id'] ) . '" />';
								break;
						}
					}
				}
				echo '</div>';
			}
		}
		?>
				<?php do_action( 'badgeos_steps_ui_html_after_dynamic_trigger_type', $step_id, $post_id ); ?>

				<select class="select-achievement-type select-achievement-type-<?php echo esc_attr( $step_id ); ?>">
					<?php
					foreach ( $achievement_types as $achievement_type ) {
						if ( trim( $badgeos_settings['achievement_step_post_type'] ) === $achievement_type ) {
							continue;
						}
						echo '<option value="' . esc_attr( $achievement_type ) . '" ' . selected( $requirements['achievement_type'], $achievement_type, false ) . '>' . esc_html( ucfirst( $achievement_type ) ) . '</option>';
					}
					?>
				</select>
				<?php do_action( 'badgeos_steps_ui_html_after_achievement_type', $step_id, $post_id ); ?>
				<select class="badgeos-select-visit-post badgeos-select-visit-post-<?php echo esc_attr( $step_id ); ?>">
					<?php
						$defaults = array(
							'post_type'   => 'post',
							'numberposts' => -1,
							'orderby'     => 'menu_order',
						);
						$posts    = get_posts( $defaults );
						echo '<option value="" selected>' . esc_html__( 'Any Post', 'badgeos' ) . '</option>';
						foreach ( $posts as $post ) {
							echo '<option value="' . esc_attr( $post->ID ) . '" ' . selected( $post->ID, $requirements['visit_post'], false ) . '>' . esc_html( ucfirst( $post->post_title ) ) . '</option>';
						}
						?>
				</select>
				<?php do_action( 'badgeos_steps_ui_html_after_visit_post', $step_id, $post_id ); ?>
				<select class="badgeos-select-visit-page badgeos-select-visit-page-<?php echo esc_attr( $step_id ); ?>">
					<?php
						$pages = get_pages();
						echo '<option value="" selected>' . esc_html__( 'Any Page', 'badgeos' ) . '</option>';
					foreach ( $pages as $page ) {
						echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $page->ID, trim( $requirements['visit_page'] ), false ) . '>' . esc_html( ucfirst( $page->post_title ) ) . '</option>';
					}
					?>
				</select>
				<?php do_action( 'badgeos_steps_ui_html_after_visit_page', $step_id, $post_id ); ?>
				<select class="select-achievement-post select-achievement-post-<?php echo esc_attr( $step_id ); ?>"></select>

				<input type="text" size="5" placeholder="<?php echo esc_html__( 'Post ID', 'badgeos' ); ?>" value="<?php echo esc_attr( $requirements['achievement_post'] ); ?>" class="select-achievement-post select-achievement-post-<?php echo esc_attr( $step_id ); ?>">

				<?php do_action( 'badgeos_steps_ui_html_after_achievement_post', $step_id, $post_id ); ?>
				
				<input type="number" size="5" min="1" placeholder="<?php echo esc_html__( 'Years', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_years'] ) > 0 ? intval( $requirements['num_of_years'] ) : '1' ); ?>" class="badgeos-num-of-years badgeos-num-of-years-<?php echo esc_attr( $step_id ); ?>">
				<?php do_action( 'badgeos_steps_ui_html_after_num_of_years', $step_id, $post_id ); ?>
				
				<input type="number" size="5" min="1" placeholder="<?php echo esc_html__( 'X Users', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['x_number_of_users'] ) > 0 ? intval( $requirements['x_number_of_users'] ) : '' ); ?>" class="badgeos-x-number-of-users badgeos-x-number-of-users-<?php echo esc_attr( $step_id ); ?>">
				<?php do_action( 'badgeos_steps_ui_html_after_x_number_of_users', $step_id, $post_id ); ?>

				<input type="number" size="5" min="1" placeholder="<?php echo esc_html__( 'days', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_days'] ) > 0 ? intval( $requirements['num_of_days'] ) : '1' ); ?>" class="badgeos-num-of-days badgeos-num-of-days-<?php echo esc_attr( $step_id ); ?>">
				<?php do_action( 'badgeos_steps_ui_html_after_num_of_days', $step_id, $post_id ); ?>

				<input type="number" size="5" min="1" placeholder="<?php echo esc_html__( 'months', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_months'] ) > 0 ? intval( $requirements['num_of_months'] ) : '1' ); ?>" class="badgeos-num-of-months badgeos-num-of-months-<?php echo esc_attr( $step_id ); ?>">
				<?php do_action( 'badgeos_steps_ui_html_after_num_of_months', $step_id, $post_id ); ?>

				<input type="number" size="5" min="1" placeholder="<?php echo esc_html__( 'days', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_days_login'] ) > 0 ? intval( $requirements['num_of_days_login'] ) : '1' ); ?>" class="badgeos-num-of-days-login badgeos-num-of-days-login-<?php echo esc_attr( $step_id ); ?>">
				<?php do_action( 'badgeos_steps_ui_html_after_num_of_days_login', $step_id, $post_id ); ?>

				<input class="required-count" type="text" size="3" maxlength="3" value="<?php echo esc_attr( $count ); ?>" placeholder="1">
				<?php echo esc_html( apply_filters( 'badgeos_steps_ui_html_count_text', esc_html__( 'time(s).', 'badgeos' ), $step_id, $post_id ) ); ?>

				<?php do_action( 'badgeos_steps_ui_html_after_count_text', $step_id, $post_id ); ?>

				<div class="step-title"><label for="step-<?php echo esc_attr( $step_id ); ?>-title"><?php echo esc_html__( 'Label', 'badgeos' ); ?>:</label> <input type="text" name="step-title" id="step-<?php echo esc_attr( $step_id ); ?>-title" class="title" value="<?php echo esc_attr( get_the_title( $step_id ) ); ?>" /></div>
				<span class="spinner spinner-step-<?php echo esc_attr( $step_id ); ?>"></span>
			</li>
	<?php
}

/**
 * Get all the requirements of a given step.
 *
 * @since  1.0.0
 * @param  integer $step_id The given step's post ID.
 * @return array|bool       An array of all the step requirements if it has any, false if not.
 */
function badgeos_get_step_requirements( $step_id = 0 ) {

	// Setup our default requirements array, assume we require nothing
	$badgeos_settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	$requirements     = array(
		'count'                    => absint( badgeos_utilities::get_post_meta( $step_id, '_badgeos_count', true ) ),
		'trigger_type'             => badgeos_utilities::get_post_meta( $step_id, '_badgeos_trigger_type', true ),
		'achievement_type'         => badgeos_utilities::get_post_meta( $step_id, '_badgeos_achievement_type', true ),
		'num_of_days'              => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_days', true ),
		'num_of_days_login'        => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_days_login', true ),
		'num_of_years'             => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_years', true ),
		'x_number_of_users'        => badgeos_utilities::get_post_meta( $step_id, '_badgeos_x_number_of_users', true ),
		'num_of_months'            => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_months', true ),
		'achievement_post'         => badgeos_utilities::get_post_meta( $step_id, '_badgeos_achievement_post', true ),
		'badgeos_subtrigger_id'    => badgeos_utilities::get_post_meta( $step_id, '_badgeos_subtrigger_id', true ),
		'badgeos_subtrigger_value' => badgeos_utilities::get_post_meta( $step_id, '_badgeos_subtrigger_value', true ),
		'badgeos_fields_data'      => badgeos_utilities::get_post_meta( $step_id, '_badgeos_fields_data', true ),
		'visit_post'               => badgeos_utilities::get_post_meta( $step_id, '_badgeos_visit_post', true ),
		'visit_page'               => badgeos_utilities::get_post_meta( $step_id, '_badgeos_visit_page', true ),
	);

	if ( ! empty( $requirements['badgeos_fields_data'] ) ) {

		$requirements['badgeos_fields_data'] = badgeos_extract_array_from_query_params( $requirements['badgeos_fields_data'] );
	}

	// If the step requires a specific achievement.
	if ( ! empty( $requirements['achievement_type'] ) ) {
		$connected_activities = get_posts(
			array(
				'post_type'        => $requirements['achievement_type'],
				'posts_per_page'   => 1,
				'suppress_filters' => false,
				'connected_type'   => $requirements['achievement_type'] . '-to-' . trim( $badgeos_settings['achievement_step_post_type'] ),
				'connected_to'     => $step_id,
			)
		);
		if ( ! empty( $connected_activities ) ) {
			$requirements['achievement_post'] = $connected_activities[0]->ID;
		}
	} elseif ( 'badgeos_specific_new_comment' === $requirements['trigger_type'] ) {
		$achievement_post = absint( badgeos_utilities::get_post_meta( $step_id, '_badgeos_achievement_post', true ) );
		if ( 0 < $achievement_post ) {
			$requirements['achievement_post'] = $achievement_post;
		}
	}

	// Available filter for overriding elsewhere
	return apply_filters( 'badgeos_get_step_requirements', $requirements, $step_id );
}

/**
 * AJAX Handler for adding a new step
 *
 * @since 1.0.0
 * @return void
 */
function badgeos_add_step_ajax_handler() {

	// Create a new Step post and grab it's ID
	$badgeos_settings = ( $exists = badgeos_utilities::get_option( 'badgeos_settings' ) ) ? $exists : array();
	$step_id          = wp_insert_post(
		array(
			'post_type'   => trim( $badgeos_settings['achievement_step_post_type'] ),
			'post_status' => 'publish',
		)
	);

	$badgeos_achievement_id = isset( $_POST['achievement_id'] ) ? absint( $_POST['achievement_id'] ) : '';
	// Output the edit step html to insert into the Steps metabox
	badgeos_steps_ui_html( $step_id, $badgeos_achievement_id );

	// Grab the post object for our Badge
	$achievement = badgeos_utilities::badgeos_get_post( $badgeos_achievement_id );

	// Create the P2P connection from the step to the badge
	$p2p_id = p2p_create_connection(
		trim( $badgeos_settings['achievement_step_post_type'] ) . '-to-' . $achievement->post_type,
		array(
			'from' => $step_id,
			'to'   => $badgeos_achievement_id,
			'meta' => array(
				'date' => current_time( 'mysql' ),
			),
		)
	);

	// Add relevant meta to our P2P connection
	p2p_add_meta( $p2p_id, 'order', '0' );

	// Die here, because it's AJAX
	die;
}
add_action( 'wp_ajax_add_step', 'badgeos_add_step_ajax_handler' );

/**
 * AJAX Handler for deleting a step
 *
 * @since 1.0.0
 * @return void
 */
function badgeos_delete_step_ajax_handler() {
	if ( isset( $_POST['step_id'] ) ) {
		wp_delete_post( absint( $_POST['step_id'] ) );
	}
	die;
}
add_action( 'wp_ajax_delete_step', 'badgeos_delete_step_ajax_handler' );


function sanitize_associative_array( array &$array, $filter = false ){
    array_walk_recursive( $array, function ( &$value ) use ( $filter ) {
        $value = trim( $value );
        if ( $filter ) {
            $value = filter_var( $value, FILTER_SANITIZE_STRING );
        }
    });
    return $array;
}

/**
 * AJAX Handler for saving all steps.
 *
 * @since 1.0.0
 * @return void
 */
function badgeos_update_steps_ajax_handler() {

	// Only continue if we have any steps.
	$badgeos_settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	if ( isset( $_POST['steps'] ) ) {

		// Grab our $wpdb global.
		global $wpdb;

		// Setup an array for storing all our step titles.
		// This lets us dynamically update the Label field when steps are saved.
		$new_titles = array();

		$steps = isset( $_POST['steps'] ) ? sanitize_associative_array( $_POST['steps'] ) : array();

		// Loop through each of the created steps.
		foreach ( $steps as $key => $step ) {

			// Grab all of the relevant values of that step.
			$step_id                  = $step['step_id'];
			$required_count           = ( ! empty( $step['required_count'] ) ) ? sanitize_text_field( $step['required_count'] ) : 1;
			$trigger_type             = $step['trigger_type'];
			$achievement_type         = $step['achievement_type'];
			$visit_post               = $step['visit_post'];
			$visit_page               = $step['visit_page'];
			$num_of_years             = $step['num_of_years'];
			$x_number_of_users        = $step['x_number_of_users'];
			$num_of_months            = $step['num_of_months'];
			$num_of_days              = $step['num_of_days'];
			$num_of_days_login        = $step['num_of_days_login'];
			$badgeos_subtrigger_id    = '';
			$badgeos_subtrigger_value = '';
			$badgeos_fields_data      = '';
			if ( isset( $step['badgeos_subtrigger_id'] ) ) {
				$badgeos_subtrigger_id = $step['badgeos_subtrigger_id'];
			}
			if ( isset( $step['badgeos_subtrigger_value'] ) ) {
				$badgeos_subtrigger_value = $step['badgeos_subtrigger_value'];
			}
			if ( isset( $step['badgeos_fields_data'] ) ) {
				$badgeos_fields_data = $step['badgeos_fields_data'];
			}

			// Clear all relation data
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->p2p WHERE p2p_to=%d", $step_id ) );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_achievement_post' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_days' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_days_login' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_years' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_x_number_of_users' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_months' );
			// Flip between our requirement types and make an appropriate connection
			switch ( $trigger_type ) {

				// Connect the step to ANY of the given achievement type
				case 'any-achievement':
					$title = sprintf( __( 'any %s', 'badgeos' ), $achievement_type );
					break;
				case 'all-achievements':
					$title = sprintf( __( 'all %s', 'badgeos' ), $achievement_type );
					break;
				case 'specific-achievement':
					p2p_create_connection(
						$step['achievement_type'] . '-to-' . trim( $badgeos_settings['achievement_step_post_type'] ),
						array(
							'from' => absint( $step['achievement_post'] ),
							'to'   => $step_id,
							'meta' => array(
								'date' => current_time( 'mysql' ),
							),
						)
					);

					badgeos_utilities::update_post_meta( $step_id, '_badgeos_achievement_post', absint( $step['achievement_post'] ) );
					$title = '"' . get_the_title( $step['achievement_post'] ) . '"';
					break;
				case 'badgeos_specific_new_comment':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_achievement_post', absint( $step['achievement_post'] ) );
					$title = sprintf( esc_html__( 'comment on post %d', 'badgeos' ), $step['achievement_post'] );
					break;
				case 'badgeos_wp_not_login':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_days', absint( $step['num_of_days'] ) );
					$title = sprintf( esc_html__( 'Not login for %d days', 'badgeos' ), $step['num_of_days'] );
					break;
				case 'badgeos_wp_login_x_days':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_days_login', absint( $step['num_of_days_login'] ) );
					$title = sprintf( esc_html__( 'login for %d day(s)', 'badgeos' ), $step['num_of_days_login'] );
					break;
				case 'badgeos_on_completing_num_of_year':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_years', absint( $num_of_years ) );
					if ( ! empty( $num_of_years ) ) {
						$title = sprintf( esc_html__( 'on completing %d year(s)', 'badgeos' ), $num_of_years );
					} else {
						$title = esc_html__( 'on completing number of year(s)', 'badgeos' );
					}
					break;
				case 'badgeos_on_the_first_x_users':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_x_number_of_users', absint( $x_number_of_users ) );

					$x_number_of_users_date = badgeos_utilities::get_post_meta( $step_id, '_badgeos_x_number_of_users_date', true );
					if ( empty( $x_number_of_users_date ) ) {
						badgeos_utilities::update_post_meta( $step_id, '_badgeos_x_number_of_users_date', date( 'Y-m-d' ) );
					}

					if ( ! empty( $x_number_of_users ) ) {
						$title = sprintf( esc_html__( 'The first %d user(s)', 'badgeos', 'badgeos' ), $x_number_of_users );
					} else {
						$title = esc_html__( 'The first X user(s)', 'badgeos' );
					}
					break;
				case 'badgeos_on_completing_num_of_month':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_months', absint( $num_of_months ) );
					if ( ! empty( $num_of_months ) ) {
						$title = sprintf( esc_html__( 'on completing %d month(s)', 'badgeos' ), $num_of_months );
					} else {
						$title = esc_html__( 'on completing number of month(s)', 'badgeos' );
					}
					break;
				case 'badgeos_on_completing_num_of_day':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_days', absint( $num_of_days ) );
					if ( ! empty( $num_of_days ) ) {
						$title = sprintf( esc_html__( 'on completing %d day(s)', 'badgeos' ), $num_of_days );
					} else {
						$title = esc_html__( 'on completing number of day(s)', 'badgeos' );
					}
					break;
				case 'badgeos_visit_a_post':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_visit_post', absint( $visit_post ) );
					if ( ! empty( $visit_post ) ) {
						$title = sprintf( esc_html__( 'Visit a Post#%d', 'badgeos' ), $visit_post );
					} else {
						$title = esc_html__( 'Visit a Post', 'badgeos' );
					}
					break;
				case 'badgeos_award_author_on_visit_post':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_visit_post', absint( $visit_post ) );
					if ( ! empty( $visit_post ) ) {
						$title = sprintf( esc_html__( 'Author on Visit a Post#%d', 'badgeos' ), $visit_post );
					} else {
						$title = esc_html__( 'Author on Visit a Post', 'badgeos' );
					}
					break;
				case 'badgeos_visit_a_page':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_visit_page', absint( $visit_page ) );
					if ( ! empty( $visit_page ) ) {
						$title = sprintf( esc_html__( 'Visit a Page#%d', 'badgeos' ), $visit_page );
					} else {
						$title = esc_html__( 'Visit a Page', 'badgeos' );
					}
					break;
				case 'badgeos_award_author_on_visit_page':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_visit_page', absint( $visit_page ) );
					if ( ! empty( $visit_page ) ) {
						$title = sprintf( esc_html__( 'Author on Visit a Page#%d', 'badgeos' ), $visit_page );
					} else {
						$title = esc_html__( 'Author on Visit a Page', 'badgeos' );
					}
					break;
				default:
					$triggers = badgeos_get_activity_triggers();
					$title    = $triggers[ $trigger_type ];
					if ( is_array( $title ) ) {
						if ( ! empty( $badgeos_subtrigger_value ) ) {
							$title = $badgeos_subtrigger_value;
						} else {
							$title = $title['label'];
						}
					}
					break;

			}

			// Update the step order
			p2p_update_meta( badgeos_get_p2p_id_from_child_id( $step_id ), 'order', $key );

			// Update our relevant meta
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_count', $required_count );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_trigger_type', $trigger_type );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_achievement_type', $achievement_type );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_subtrigger_id', $badgeos_subtrigger_id );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_subtrigger_value', $badgeos_subtrigger_value );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_fields_data', $badgeos_fields_data );

			// Available hook for custom Activity Triggers
			$custom_title = sprintf( esc_html__( 'Earn %1$s %2$s.', 'badgeos' ), $title, sprintf( _n( '%d time', '%d times', $required_count ), $required_count ) );
			$custom_title = apply_filters( 'badgeos_save_step', $custom_title, $step_id, $step );

			// Update our original post with the new title
			$post_title = ! empty( $step['title'] ) ? $step['title'] : $custom_title;
			wp_update_post(
				array(
					'ID'         => $step_id,
					'post_title' => $post_title,
				)
			);

			// Add the title to our AJAX return
			$new_titles[ $step_id ] = stripslashes( $post_title );

		}

		// Send back all our step titles
		echo wp_json_encode( $new_titles );

	}

	// Cave Johnson. We're done here.
	die;

}
add_action( 'wp_ajax_update_steps', 'badgeos_update_steps_ajax_handler' );

/**
 * AJAX helper for getting our posts and returning select options
 *
 * @since  1.0.0
 * @return void
 */
function badgeos_activity_trigger_post_select_ajax_handler() {

	// Grab our achievement type from the AJAX request.
	$achievement_type = isset( $_REQUEST['achievement_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['achievement_type'] ) ) : '';

	$exclude_posts = isset( $_REQUEST['excluded_posts'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_REQUEST['excluded_posts'] ) ) : array();

	$requirements = isset( $_REQUEST['step_id'] ) ? badgeos_get_step_requirements( sanitize_text_field( wp_unslash( $_REQUEST['step_id'] ) ) ) : '';

	// If we don't have an achievement type, bail now.
	if ( empty( $achievement_type ) ) {
		die();
	}

	// Grab all our posts for this achievement type.
	$achievements = get_posts(
		array(
			'post_type'      => $achievement_type,
			'post__not_in'   => $exclude_posts,
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	// Setup our output.
	$output = '';
	foreach ( $achievements as $achievement ) {
		$output .= '<option value="' . $achievement->ID . '" ' . selected( $requirements['achievement_post'], $achievement->ID, false ) . '>' . $achievement->post_title . '</option>';
	}

	// Send back our results and die like a man.
	echo $output;
	die();
}
add_action( 'wp_ajax_post_select_ajax', 'badgeos_activity_trigger_post_select_ajax_handler' );

/**
 * Get the the ID of a post connected to a given child post ID
 *
 * @since  1.0.0
 * @param  integer $child_id The given child's post ID
 * @return integer           The resulting connected post ID
 */
function badgeos_get_p2p_id_from_child_id( $child_id = 0 ) {
	global $wpdb;
	$p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d ", $child_id ) );
	return $p2p_id;
}

/**
 * Get the sort order for a given step
 *
 * @since  1.0.0
 * @param  integer $step_id The given step's post ID
 * @return integer          The step's sort order
 */
function get_step_menu_order( $step_id = 0 ) {
	global $wpdb;
	$p2p_id     = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d", $step_id ) );
	$menu_order = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->p2pmeta WHERE p2p_id=%d AND meta_key='order'", $p2p_id ) );
	if ( ! $menu_order || 'NaN' === $menu_order ) {
		$menu_order = '0';
	}
	return $menu_order;
}

/**
 * Helper function for comparing our step sort order (used in uasort() in badgeos_create_steps_meta_box())
 *
 * @since  1.0.0
 * @param  integer $step1 The order number of our given step
 * @param  integer $step2 The order number of the step we're comparing against
 * @return integer        0 if the order matches, -1 if it's lower, 1 if it's higher
 */
function badgeos_compare_step_order( $step1 = 0, $step2 = 0 ) {
	if ( $step1->order === $step2->order ) {
		return 0;
	}
	return ( $step1->order < $step2->order ) ? -1 : 1;
}
