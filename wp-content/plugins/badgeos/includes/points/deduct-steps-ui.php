<?php
/**
 * Custom Points Steps UI.
 *
 * @package Badgeos
 * @subpackage Points
 * @author LearningTimes
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com/
 */

/**
 * Add our Steps JS to the Achievement post editor.
 *
 * @return void
 */
function badgeos_deduct_steps_ui_admin_scripts() {

	global $post_type;
	$settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();

	if ( trim( $post_type ) === $settings['points_main_post_type'] ) {
		wp_enqueue_script( 'badgeos-deduct-steps-ui', $GLOBALS['badgeos']->directory_url . 'js/deduct-steps-ui.js', array( 'jquery-ui-sortable' ), BadgeOS::$version, true );
	}
}
add_action( 'admin_print_scripts-post-new.php', 'badgeos_deduct_steps_ui_admin_scripts', 11 );
add_action( 'admin_print_scripts-post.php', 'badgeos_deduct_steps_ui_admin_scripts', 11 );

/**
 * Adds our Steps metabox to the Badge post editor.
 *
 * @return void
 */
function badgeos_add_deduct_steps_ui_meta_box() {

	$settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	add_meta_box( 'badgeos_deduct_steps_ui', apply_filters( 'badgeos_deduct_steps_ui_title', esc_html__( 'Points Deducts', 'badgeos' ) ), 'badgeos_deduct_steps_ui_meta_box', trim( $settings['points_main_post_type'] ), 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'badgeos_add_deduct_steps_ui_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new step is added.
 *
 * @param  object $post The current post object.
 * @return void
 */
function badgeos_deduct_steps_ui_meta_box( $post = null ) {

	global $wpdb;
	$settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();

	$required_steps = $wpdb->get_results( $wpdb->prepare( "SELECT p.*, pp.* FROM $wpdb->p2p as pp inner join $wpdb->posts as p on(pp.p2p_from = p.ID) WHERE pp.p2p_to = %d and p.post_type = %s", $post->ID, trim( $settings['points_deduct_post_type'] ) ) );

	/**
	 * Loop through each step and set the sort order.
	 */
	foreach ( $required_steps as $required_step ) {
		$required_step->order = get_deduct_step_menu_order( $required_step->ID );
	}

	/**
	 * Sort the steps by their order.
	 */
	uasort( $required_steps, 'badgeos_compare_step_order' );

	echo '<p>' . esc_html__( 'Defined points will be deducted when any of the following "steps" will be considered as complete. Use the "Label" field to optionally customize the titles of each step.', 'badgeos' ) . '</p>';

	/**
	 * Concatenate our step output.
	 */
	echo '<ul id="deduct_steps_list">';
	foreach ( $required_steps as $step ) {
		badgeos_deduct_steps_ui_html( $step->ID, $post->ID );
	}
	echo '</ul>';

	/**
	 * Render our buttons
	 */
	echo '<input style="margin-right: 1em" class="button" type="button" onclick="badgeos_add_new_deduct_step(' . esc_attr( $post->ID ) . ');" value="' . esc_attr( apply_filters( 'badgeos_deduct_steps_ui_add_new', esc_html__( 'Add New Step', 'badgeos' ) ) ) . '">';
	echo '<input class="button-primary" type="button" onclick="badgeos_update_deduct_steps();" value="' . esc_attr( apply_filters( 'badgeos_deduct_steps_ui_save_all', esc_html__( 'Save All Steps', 'badgeos' ) ) ) . '">';
	echo '<img class="save-steps-spinner save-deduct-steps-spinner" src="' . esc_url( admin_url( '/images/wpspin_light.gif' ) ) . '" style="margin-left: 10px; display: none;" />';
}

/**
 * Helper function for generating the HTML output for configuring a given step.
 *
 * @param  integer $step_id The given step's ID.
 * @param  integer $post_id The given step's parent $post ID.
 */
function badgeos_deduct_steps_ui_html( $step_id = 0, $post_id = 0 ) {
	global $wpdb;
	/**
	 * Grab our step's requirements and measurement.
	 */
	$requirements      = badgeos_get_deduct_step_requirements( $step_id );
	$count             = ! empty( $requirements['count'] ) ? $requirements['count'] : 1;
	$achievement_types = badgeos_get_achievement_types_slugs();
	$settings          = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();

	$dynamic_triggers         = array();
	$badgeos_subtrigger_value = $requirements['badgeos_subtrigger_value'];
	$badgeos_subtrigger_id    = $requirements['badgeos_subtrigger_id'];
	$badgeos_fields_data      = $requirements['badgeos_fields_data'];
	?>

	<li class="step-row step-<?php echo esc_attr( $step_id ); ?>" data-step-id="<?php echo esc_attr( $step_id ); ?>">
		<div class="step-handle"></div>
		<a class="delete-step" href="javascript: badgeos_delete_deduct_step( <?php echo esc_attr( $step_id ); ?> );"><?php esc_html_e( 'Delete', 'badgeos' ); ?></a>
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
		<input type="hidden" name="order" value="<?php echo esc_attr( get_deduct_step_menu_order( $step_id ) ); ?>" />

		<?php echo esc_html( apply_filters( 'badgeos_deduct_steps_ui_html_require_text', esc_html__( 'Require', 'badgeos' ), $step_id, $post_id ) ); ?>

		<?php do_action( 'badgeos_deduct_steps_ui_html_after_require_text', $step_id, $post_id ); ?>

		<select class="select-trigger-type" data-step-id="<?php echo esc_attr( $step_id ); ?>">
			<?php
			foreach ( get_badgeos_points_deduct_activity_triggers() as $value => $label ) {
				if ( is_array( $label ) ) {
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $requirements['trigger_type'], $value, false ) . '>' . esc_html( $label['label'] ) . '</option>';
					$dynamic_triggers[ $value ] = $label;
				} else {
					echo '<option value="' . esc_attr( $value ) . '" ' . selected( $requirements['trigger_type'], $value, false ) . '>' . esc_html( $label ) . '</option>';
				}
			}
			?>
		</select>

		<?php do_action( 'badgeos_deduct_steps_ui_html_after_trigger_type', $step_id, $post_id ); ?>

		<?php
		if ( count( $dynamic_triggers ) > 0 ) {
			foreach ( $dynamic_triggers as $key => $data ) {
				$fields_group = array();
				?>
		<div id="badgeos_dedpoint_step_dynamic_section_<?php echo esc_attr( $key ); ?>" style="display:inline-block">
			<select id="badgeos_dedpoint_step_ddl_dynamic_<?php echo esc_attr( $key ); ?>" data-trigger="<?php echo esc_attr( $key ); ?>" class="badgeos_dedpoint_step_fields badgeos_dedpoint_step_ddl_dynamic" name="badgeos_dedpoint_step_ddl_dynamic_<?php echo esc_attr( $key ); ?>">
				<?php
				foreach ( $data['sub_triggers'] as $key2 => $data2 ) {
					$sub_fields = array();
					echo '<option value="' . esc_attr( $data2['trigger'] ) . '" ' . selected( $data2['trigger'], $badgeos_subtrigger_value, false ) . ' >' . esc_html( $data2['label'] ) . '</option>';

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
								echo '<select id="' . esc_attr( $data2['trigger'] ) . '" class="badgeos_dedpoint_step_fields badgeos_dedpoint_step_subddl_dynamic badgeos_dedpoint_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_dedpoint_step_subddl_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $data2['trigger'] ) . '">';
								foreach ( $field['options'] as $ddlkey => $ddlval ) {
									echo '<option value="' . esc_attr( $ddlkey ) . '" ' . ( $ddlkey === $sel_val ? 'selected' : '' ) . '>' . esc_html( $ddlval ) . '</option>';
								}
								echo '</select>';
								break;
							case 'text':
								echo '<input value="' . esc_attr( $sel_val ) . '" type="' . esc_attr( $field['type'] ) . '" size="4" id="' . esc_attr( $data2['trigger'] ) . '"  class="badgeos_dedpoint_step_fields badgeos_dedpoint_step_subtxt_dynamic badgeos_dedpoint_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_dedpoint_step_subtxt_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $data2['trigger'] ) . '" />';
								break;
							case 'number':
								echo '<input value="' . esc_attr( $sel_val ) . '" type="' . esc_attr( $field['type'] ) . '" size="4" step="1" min="0" id="' . esc_attr( $data2['trigger'] ) . '" class="badgeos_dedpoint_step_fields badgeos_dedpoint_step_subtxt_dynamic badgeos_dedpoint_step_fields_' . esc_attr( $fields_groupkey ) . ' badgeos_dedpoint_step_subtxt_' . esc_attr( $fields_groupkey ) . '" name="' . esc_attr( $data2['trigger'] ) . '" />';
								break;
						}
					}
				}
				echo '</div>';
			}
		}
		?>
				<?php do_action( 'badgeos_deduct_steps_ui_html_after_dynamic_trigger_type', $step_id, $post_id ); ?>

				<select class="select-achievement-type select-achievement-type-<?php echo esc_attr( $step_id ); ?>">
					<?php
					foreach ( $achievement_types as $achievement_type ) {
						if ( $settings['points_deduct_post_type'] === $achievement_type ) {
							continue;
						}
						echo '<option value="' . esc_attr( $achievement_type ) . '" ' . selected( $requirements['achievement_type'], $achievement_type, false ) . '>' . esc_html( ucfirst( $achievement_type ) ) . '</option>';
					}
					?>
				</select> 

		<?php do_action( 'badgeos_deduct_steps_ui_html_after_achievement_type', $step_id, $post_id ); ?>

		<select class="select-achievement-post select-achievement-post-<?php echo esc_attr( $step_id ); ?>">
			<option value=""></option>
		</select>

		<input type="text" size="5" placeholder="<?php esc_attr_e( 'Post ID', 'badgeos' ); ?>" value="<?php esc_attr( $requirements['achievement_post'] ); ?>" class="select-achievement-post select-achievement-post-<?php echo esc_attr( $step_id ); ?>">

		<?php do_action( 'badgeos_deduct_steps_ui_html_after_achievement_post', $step_id, $post_id ); ?>
		<?php do_action( 'badgeos_steps_ui_html_before_pdeduct_visit_post', $step_id, $post_id ); ?>
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
		<?php do_action( 'badgeos_steps_ui_html_before_remove_achivement_post', $step_id, $post_id ); ?>
		<select class="badgeos-select-remove-achivement badgeos-select-remove-achivement-<?php echo esc_attr( $step_id ); ?>">
			<?php
				$achievement_types = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT ID, post_title, post_name FROM %s 
						WHERE post_type = %s',
						$wpdb->posts,
						$settings['achievement_main_post_type']
					)
				);
			if ( is_array( $achievement_types ) && ! empty( $achievement_types ) && ! is_null( $achievement_types ) ) {
				foreach ( $achievement_types as $achievement_type ) {
					echo '<optgroup label="' . esc_attr( $achievement_type->post_title ) . '">';
						$achievements = get_posts(
							array(
								'post_type'      => $achievement_type->post_name,
								'posts_per_page' => -1,
							)
						);
					foreach ( $achievements as $achievement ) {
						echo '<option value="' . esc_attr( $achievement->ID ) . '" ' . selected( $achievement->ID, $requirements['remove_achivement'], false ) . '>' . esc_html( ucfirst( $achievement->post_title ) ) . '</option>';
					}
					echo '</optgroup>';
				}
			}
			?>
		</select>
		<?php do_action( 'badgeos_steps_ui_html_before_remove_rank_post', $step_id, $post_id ); ?>
		<select class="badgeos-select-remove-rank badgeos-select-remove-rank-<?php echo esc_attr( $step_id ); ?>">
			<?php
				$rank_types = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT ID, post_title, post_name FROM %s 
						WHERE post_type = %s',
						$wpdb->posts,
						$settings['ranks_main_post_type']
					)
				);

			if ( is_array( $rank_types ) && ! empty( $rank_types ) && ! is_null( $rank_types ) ) {
				foreach ( $rank_types as $rank_type ) {
					echo '<optgroup label="' . esc_attr( $rank_type->post_title ) . '">';
						$ranks = get_posts(
							array(
								'post_type'      => $rank_type->post_name,
								'posts_per_page' => -1,
							)
						);
					foreach ( $ranks as $rank ) {
						echo '<option value="' . esc_attr( $rank->ID ) . '" ' . selected( $rank->ID, $requirements['remove_achivement'], false ) . '>' . esc_html( ucfirst( $rank->post_title ) ) . '</option>';
					}
					echo '</optgroup>';
				}
			}
			?>
		</select>
		<?php do_action( 'badgeos_steps_ui_html_after_pdeduct_visit_post', $step_id, $post_id ); ?>
		<select class="badgeos-select-visit-page badgeos-select-visit-page-<?php echo esc_attr( $step_id ); ?>">
			<?php
			$pages = get_pages();
				echo '<option value="" selected>' . esc_html__( 'Any Page', 'badgeos' ) . '</option>';
			foreach ( $pages as $page ) {
				echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $page->ID, trim( $requirements['visit_page'] ), false ) . '>' . esc_html( ucfirst( $page->post_title ) ) . '</option>';
			}
			?>
		</select>
		
		<?php do_action( 'badgeos_steps_ui_html_after_pdeduct_visit_page', $step_id, $post_id ); ?>
		
		<input type="number" size="5" min="1" placeholder="<?php esc_attr_e( 'Days', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_days'] ) > 0 ? intval( $requirements['num_of_days'] ) : '1' ); ?>" class="badgeos-num-of-days badgeos-num-of-days-<?php echo esc_attr( $step_id ); ?>">
		<?php do_action( 'badgeos_point_deduct_steps_ui_html_after_num_of_days', esc_attr( $step_id ), $post_id ); ?>

		<input type="number" size="5" min="0" placeholder="<?php esc_attr_e( 'Years', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_years'] ) > 0 ? intval( $requirements['num_of_years'] ) : '1' ); ?>" class="badgeos-num-of-years badgeos-num-of-years-<?php echo esc_attr( $step_id ); ?>">
		<?php do_action( 'badgeos_point_deduct_steps_ui_html_after_num_of_years', esc_attr( $step_id ), $post_id ); ?>
		
		<input type="number" size="5" min="0" placeholder="<?php esc_attr_e( 'X Users', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['x_number_of_users'] ) > 0 ? intval( $requirements['x_number_of_users'] ) : '' ); ?>" class="badgeos-x-number-of-users badgeos-x-number-of-users-<?php echo esc_attr( $step_id ); ?>">
		
		<?php do_action( 'badgeos_rank_steps_ui_html_after_x_number_of_users', $step_id, $post_id ); ?>

		<input type="number" size="5" min="0" placeholder="<?php esc_attr_e( 'Months', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_months'] ) > 0 ? intval( $requirements['num_of_months'] ) : '1' ); ?>" class="badgeos-num-of-months badgeos-num-of-months-<?php echo esc_attr( $step_id ); ?>">
		<?php do_action( 'badgeos_point_deduct_steps_ui_html_after_num_of_months', esc_attr( $step_id ), $post_id ); ?>

		<input type="number" size="5" min="0" placeholder="<?php esc_attr_e( 'Months', 'badgeos' ); ?>" value="<?php echo esc_attr( intval( $requirements['num_of_months'] ) > 0 ? intval( $requirements['num_of_months'] ) : '1' ); ?>" class="badgeos-num-of-months badgeos-num-of-months-<?php echo esc_attr( $step_id ); ?>">
		<?php do_action( 'badgeos_point_deduct_steps_ui_html_after_num_of_months', esc_attr( $step_id ), $post_id ); ?>

		<input class="point-value" type="number" size="3" maxlength="3" value="<?php echo esc_attr( $requirements['_point_value'] ); ?>" placeholder="<?php esc_attr_e( 'Points', 'badgeos' ); ?>">		
		<input class="required-count" type="text" size="3" maxlength="3" value="<?php echo esc_attr( $count ); ?>" placeholder="1">
		<?php echo esc_html( apply_filters( 'badgeos_deduct_steps_ui_html_count_text', esc_html__( 'time(s).', 'badgeos' ), $step_id, $post_id ) ); ?>

		<?php do_action( 'badgeos_deduct_steps_ui_html_after_count_text', $step_id, $post_id ); ?>

		<div class="step-title"><label for="step-<?php echo esc_attr( $step_id ); ?>-title"><?php esc_html_e( 'Label', 'badgeos' ); ?>:</label> <input type="text" name="step-title" id="step-<?php echo esc_attr( $step_id ); ?>-title" class="title" value="<?php echo esc_attr( get_the_title( $step_id ) ); ?>" /></div>
		<span class="spinner spinner-step-<?php echo esc_attr( $step_id ); ?>"></span>
	</li>
	<?php
}

/**
 * Get all the requirements of a given step.
 *
 * @param  integer $step_id The given step's post ID.
 * @return array|bool       An array of all the step requirements if it has any, false if not.
 */
function badgeos_get_deduct_step_requirements( $step_id = 0 ) {

	/**
	 * Setup our default requirements array, assume we require nothing.
	 */
	$requirements = array(
		'count'                    => absint( badgeos_utilities::get_post_meta( $step_id, '_badgeos_count', true ) ),
		'_point_value'             => absint( badgeos_utilities::get_post_meta( $step_id, '_point_value', true ) ),
		'trigger_type'             => badgeos_utilities::get_post_meta( $step_id, '_deduct_trigger_type', true ),
		'achievement_type'         => badgeos_utilities::get_post_meta( $step_id, '_badgeos_achievement_type', true ),
		'achievement_post'         => absint( badgeos_utilities::get_post_meta( $step_id, '_badgeos_achievement_post', true ) ),
		'badgeos_subtrigger_id'    => badgeos_utilities::get_post_meta( $step_id, '_badgeos_subtrigger_id', true ),
		'badgeos_subtrigger_value' => badgeos_utilities::get_post_meta( $step_id, '_badgeos_pdeduct_subtrigger_value', true ),
		'badgeos_fields_data'      => badgeos_utilities::get_post_meta( $step_id, '_badgeos_fields_data', true ),
		'visit_post'               => badgeos_utilities::get_post_meta( $step_id, '_badgeos_visit_post', true ),
		'visit_page'               => badgeos_utilities::get_post_meta( $step_id, '_badgeos_visit_page', true ),
		'num_of_days'              => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_days', true ),
		'num_of_months'            => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_months', true ),
		'num_of_years'             => badgeos_utilities::get_post_meta( $step_id, '_badgeos_num_of_years', true ),
		'x_number_of_users'        => badgeos_utilities::get_post_meta( $step_id, '_badgeos_x_number_of_users', true ),
		'x_number_of_users_date'   => badgeos_utilities::get_post_meta( $step_id, '_badgeos_x_number_of_users_date', true ),
		'remove_rank'              => badgeos_utilities::get_post_meta( $step_id, '_badgeos_remove_rank', true ),
		'remove_achivement'        => badgeos_utilities::get_post_meta( $step_id, '_badgeos_remove_achivement', true ),
	);

	if ( ! empty( $requirements['badgeos_fields_data'] ) ) {

		$requirements['badgeos_fields_data'] = badgeos_extract_array_from_query_params( $requirements['badgeos_fields_data'] );
	}

	/**
	 * If the step requires a specific achievement
	 */
	if ( ! empty( $requirements['achievement_type'] ) ) {
		$connected_activities = @get_posts(
			array(
				'post_type'        => $requirements['achievement_type'],
				'posts_per_page'   => 1,
				'suppress_filters' => false,
				'connected_type'   => $requirements['achievement_type'] . '-to-step',
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

	/**
	 * Available filter for overriding elsewhere.
	 */
	return apply_filters( 'badgeos_get_deduct_step_requirements', $requirements, $step_id );
}

/**
 * AJAX Handler for adding a new step.
 *
 * @return void
 */
function badgeos_add_deduct_step_ajax_handler() {

	/**
	 * Create a new Step post and grab it's ID.
	 */
	$settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	$step_id  = wp_insert_post(
		array(
			'post_type'   => $settings['points_deduct_post_type'],
			'post_status' => 'publish',
		)
	);

	$achievement_post_id = isset( $_POST['achievement_id'] ) ? absint( $_POST['achievement_id'] ) : 0;
	/**
	 * Output the edit step html to insert into the Steps metabox.
	 */
	badgeos_deduct_steps_ui_html( $step_id, $achievement_post_id );

	/**
	 * Grab the post object for our Achievement.
	 */
	$achievement = badgeos_utilities::badgeos_get_post( $achievement_post_id );

	/**
	 * Create the P2P connection from the step to the Achievement.
	 */
	$p2p_id = p2p_create_connection(
		'point_deduct-to-' . $achievement->post_type,
		array(
			'from' => $step_id,
			'to'   => $achievement_post_id,
			'meta' => array(
				'date' => current_time( 'mysql' ),
			),
		)
	);

	/**
	 * Add relevant meta to our P2P connection.
	 */
	p2p_add_meta( $p2p_id, 'order', '0' );

	/**
	 * Die here, because it's AJAX.
	 */
	die;
}
add_action( 'wp_ajax_add_deduct_step', 'badgeos_add_deduct_step_ajax_handler' );

/**
 * AJAX Handler for deleting a step.
 *
 * @return void
 */
function badgeos_delete_deduct_step_ajax_handler() {
	if ( isset( $_POST['step_id'] ) ) {
		wp_delete_post( absint( $_POST['step_id'] ) );
	}
	die;
}
add_action( 'wp_ajax_delete_deduct_step', 'badgeos_delete_deduct_step_ajax_handler' );

/**
 * AJAX Handler for saving all steps.
 *
 * @return void
 */
function badgeos_update_deduct_steps_ajax_handler() {

	/**
	 * Only continue if we have any steps.
	 */
	if ( isset( $_POST['steps'] ) ) {

		/**
		 * Grab our $wpdb global.
		 */
		global $wpdb;

		/**
		 * Setup an array for storing all our step titles.
		 * This lets us dynamically update the Label field when steps are saved.
		 */
		$new_titles = array();
		$steps      = isset( $_POST['steps'] ) ? sanitize_associative_array( $_POST['steps'] ) : array();

		/**
		 * Loop through each of the created steps.
		 */
		foreach ( $steps as $key => $step ) {

			/**
			 * Grab all of the relevant values of that step.
			 */
			$step_id           = sanitize_text_field( $step['step_id'] );
			$required_count    = ( ! empty( $step['required_count'] ) ) ? sanitize_text_field( $step['required_count'] ) : 1;
			$point_value       = ( ! empty( $step['point_value'] ) ) ? sanitize_text_field( $step['point_value'] ) : 0;
			$visit_post        = sanitize_text_field( $step['visit_post'] );
			$visit_page        = sanitize_text_field( $step['visit_page'] );
			$num_of_years      = sanitize_text_field( $step['num_of_years'] );
			$remove_achivement = sanitize_text_field( $step['remove_achivement'] );
			$remove_rank       = sanitize_text_field( $step['remove_rank'] );
			$x_number_of_users = sanitize_text_field( $step['x_number_of_users'] );
			$num_of_days       = sanitize_text_field( $step['num_of_days'] );
			$num_of_months     = sanitize_text_field( $step['num_of_months'] );
			$trigger_type      = sanitize_text_field( $step['trigger_type'] );
			$achievement_type  = sanitize_text_field( $step['achievement_type'] );

			$badgeos_subtrigger_id    = '';
			$badgeos_subtrigger_value = '';
			$badgeos_fields_data      = '';
			if ( isset( $step['badgeos_subtrigger_id'] ) ) {
				$badgeos_subtrigger_id = sanitize_text_field( $step['badgeos_subtrigger_id'] );
			}
			if ( isset( $step['badgeos_subtrigger_value'] ) ) {
				$badgeos_subtrigger_value = sanitize_text_field( $step['badgeos_subtrigger_value'] );
			}
			if ( isset( $step['badgeos_fields_data'] ) ) {
				$badgeos_fields_data = sanitize_text_field( $step['badgeos_fields_data'] );
			}

			/**
			 * Clear all relation data.
			 */
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->p2p WHERE p2p_to=%d", $step_id ) );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_achievement_post' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_years' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_days' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_num_of_months' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_x_number_of_users' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_remove_rank' );
			badgeos_utilities::del_post_meta( $step_id, '_badgeos_remove_achivement' );
			/**
			 * Flip between our requirement types and make an appropriate connection.
			 */
			switch ( $trigger_type ) {

				/**
				 * Connect the step to ANY of the given achievement type.
				 */
				case 'any-achievement':
					$title = sprintf( esc_html__( 'any %s', 'badgeos' ), $achievement_type );
					break;
				case 'all-achievements':
					$title = sprintf( esc_html__( 'all %s', 'badgeos' ), $achievement_type );
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
				case 'badgeos_award_author_on_visit_page':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_visit_page', absint( $visit_page ) );
					if ( ! empty( $visit_page ) ) {
						$title = sprintf( esc_html__( 'Author on Visit a Page#%d', 'badgeos' ), $visit_page );
					} else {
						$title = esc_html__( 'Author on Visit a Page', 'badgeos' );
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
				case 'badgeos_on_completing_num_of_year':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_years', absint( $num_of_years ) );
					if ( ! empty( $num_of_years ) ) {
						$title = sprintf( esc_html__( 'on completing %d year(s)', 'badgeos' ), $num_of_years );
					} else {
						$title = esc_html__( 'on completing number of year(s)', 'badgeos' );
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
				case 'badgeos_remove_achievment_on_point_deduct':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_remove_achivement', absint( $remove_achivement ) );
					if ( ! empty( $remove_achivement ) ) {
						$title = sprintf( esc_html__( "Remove '%1\$s' achievement on '%2\$d' points deduction", 'badgeos' ), get_the_title( $remove_achivement ), $point_value );
					} else {
						$title = esc_html__( 'Remove achievement on points deduction', 'badgeos' );
					}
					break;
				case 'badgeos_remove_rank_on_point_deduct':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_remove_rank', absint( $remove_rank ) );
					if ( ! empty( $remove_rank ) ) {
						$title = sprintf( esc_html__( "Remove '%1\$s' rank on '%2\$d' points deduction", 'badgeos' ), get_the_title( $remove_rank ), $point_value );
					} else {
						$title = esc_html__( 'Remove rank on points deduction', 'badgeos' );
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
				case 'badgeos_wp_not_login':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_num_of_days', absint( $num_of_days ) );
					if ( ! empty( $num_of_days ) ) {
						$title = sprintf( esc_html__( 'Not logging in for %d day(s)', 'badgeos' ), $num_of_days );
					} else {
						$title = esc_html__( 'Not logging in for %d day(s)', 'badgeos' );
					}
					break;
				case 'specific-achievement':
					p2p_create_connection(
						$step['achievement_type'] . '-to-step',
						array(
							'from' => absint( $step['achievement_post'] ),
							'to'   => $step_id,
							'meta' => array(
								'date' => current_time( 'mysql' ),
							),
						)
					);
					$title = '"' . get_the_title( $step['achievement_post'] ) . '"';
					break;
				case 'badgeos_specific_new_comment':
					badgeos_utilities::update_post_meta( $step_id, '_badgeos_achievement_post', absint( $step['achievement_post'] ) );
					$title = sprintf( esc_html__( 'comment on post %d', 'badgeos' ), $step['achievement_post'] );
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

			/**
			 * Update the step order.
			 */
			p2p_update_meta( badgeos_get_p2p_id_from_child_id( $step_id ), 'order', $key );

			/**
			 * Update our relevant meta
			 */
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_count', $required_count );
			badgeos_utilities::update_post_meta( $step_id, '_point_value', $point_value );
			badgeos_utilities::update_post_meta( $step_id, '_deduct_trigger_type', $trigger_type );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_achievement_type', $achievement_type );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_subtrigger_id', $badgeos_subtrigger_id );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_pdeduct_subtrigger_value', $badgeos_subtrigger_value );
			badgeos_utilities::update_post_meta( $step_id, '_badgeos_fields_data', $badgeos_fields_data );

			/**
			 * Available hook for custom Activity Triggers.
			 */
			$custom_title = sprintf( esc_html__( 'Earn %1$s %2$s.', 'badgeos' ), $title, sprintf( _n( '%d time', '%d times', $required_count ), $required_count ) );
			$custom_title = apply_filters( 'badgeos_save_step', $custom_title, $step_id, $step );

			/**
			 * Update our original post with the new title.
			 */
			$post_title = ! empty( $step['title'] ) ? $step['title'] : $custom_title;
			wp_update_post(
				array(
					'ID'         => $step_id,
					'post_title' => $post_title,
				)
			);

			/**
			 * Add the title to our AJAX return.
			 */
			$new_titles[ $step_id ] = stripslashes( $post_title );

		}

		/**
		 * Send back all our step titles.
		 */
		echo json_encode( $new_titles );

	}

	die;
}
add_action( 'wp_ajax_update_deduct_steps', 'badgeos_update_deduct_steps_ajax_handler' );

/**
 * AJAX helper for getting our posts and returning select options.
 *
 * @return void
 */
function badgeos_activity_trigger_post_deduct_select_ajax_handler() {

	/**
	 * Grab our achievement type from the AJAX request.
	 */
	$achievement_type = isset( $_REQUEST['achievement_type'] ) ? sanitize_text_field( $_REQUEST['achievement_type'] ) : '';

	$exclude_posts = isset( $_REQUEST['excluded_posts'] ) ?
	array_map( 'sanitize_text_field', (array) $_REQUEST['excluded_posts'] ) : array();

	$requirements = isset( $_REQUEST['step_id'] ) ? badgeos_get_award_step_requirements( absint( $_REQUEST['step_id'] ) ) : 0;

	/**
	 * If we don't have an achievement type, bail now.
	 */
	if ( empty( $achievement_type ) ) {
		die();
	}

	/**
	 * Grab all our posts for this achievement type.
	 */
	$achievements = get_posts(
		array(
			'post_type'      => $achievement_type,
			'post__not_in'   => $exclude_posts,
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	/**
	 * Setup our output.
	 */
	$output = '<option></option>';
	foreach ( $achievements as $achievement ) {
		$output .= '<option value="' . $achievement->ID . '" ' . selected( $requirements['achievement_post'], $achievement->ID, false ) . '>' . $achievement->post_title . '</option>';
	}

	/**
	 * Send back our results and die like a man.
	 */
	echo wp_kses_post( $output );
	die();
}
add_action( 'wp_ajax_post_deduct_select_ajax', 'badgeos_activity_trigger_post_deduct_select_ajax_handler' );

/**
 * Get the the ID of a post connected to a given child post ID.
 *
 * @param  integer $child_id The given child's post ID.
 * @return integer           The resulting connected post ID.
 */
function badgeos_get_p2p_deduct_id_from_child_id( $child_id = 0 ) {

	global $wpdb;
	$p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d ", $child_id ) );
	return $p2p_id;
}

/**
 * Get the sort order for a given step.
 *
 * @param  integer $step_id The given step's post ID.
 * @return integer          The step's sort order.
 */
function get_deduct_step_menu_order( $step_id = 0 ) {

	global $wpdb;
	$p2p_id     = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d", $step_id ) );
	$menu_order = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->p2pmeta WHERE p2p_id=%d AND meta_key='order'", $p2p_id ) );
	if ( ! $menu_order || 'NaN' === $menu_order ) {
		$menu_order = '0';
	}
	return $menu_order;
}

/**
 * Helper function for comparing our step sort order (used in uasort() in badgeos_create_steps_meta_box()).
 *
 * @param  integer $step1 The order number of our given step.
 * @param  integer $step2 The order number of the step we're comparing against.
 * @return integer        0 if the order matches, -1 if it's lower, 1 if it's higher.
 */
function badgeos_compare_deduct_step_order( $step1 = 0, $step2 = 0 ) {
	if ( $step1->order === $step2->order ) {
		return 0;
	}
	return ( $step1->order < $step2->order ) ? -1 : 1;
}
