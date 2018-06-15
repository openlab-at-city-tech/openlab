<?php /*
--------------------------------------------------------------------------------
BuddyPress_Event_Organiser_EO Class
--------------------------------------------------------------------------------
*/
class BuddyPress_Event_Organiser_EO {

	/**
	 * properties
	 */

	// parent object
	public $plugin;

	// group IDs
	public $group_ids;



	/**
	 * @description: initialises this object
	 * @return object
	 */
	public function __construct() {
		if ( ! $this->is_active() ) {
			add_action( 'admin_notices', array( $this, 'eo_active_notice' ) );
			return;
		}

		// register hooks
		$this->register_hooks();

		// --<
		return $this;

	}

	/**
	 * Throw an admin notice about the Event Organiser requirement.
	 */
	public function eo_active_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		?>
		<div class="error">
			<p><?php _e( 'BP Event Organiser requires version 2+ of Event Organiser.', 'bp-event-organiser' ) ?></p>
		</div>
		<?php
	}

	/**
	 * @description: set references to other objects
	 * @return nothing
	 */
	public function set_references( $parent ) {

		// store
		$this->plugin = $parent;

	}



	/**
	 * @description: register hooks on BuddyPress loaded
	 * @return nothing
	 */
	public function register_hooks() {

		// check for Event Organiser
		if ( !$this->is_active() ) return;

		// add our event meta box
		add_action( 'add_meta_boxes', array( $this, 'event_meta_box' ) );

		// ajax handler for meta box autocomplete
		add_action( 'wp_ajax_bpeo_get_groups', array( $this, 'ajax_get_groups' ) );

		// intercept save event
		add_action( 'save_post', array( $this, 'intercept_save_event' ), 15, 3 );

		// intercept before break occurrence
		add_action( 'eventorganiser_pre_break_occurrence', array( $this, 'pre_break_occurrence' ), 10, 2 );

		// intercept after break occurrence, because a new post is created
		//add_action( 'eventorganiser_occurrence_broken', array( $this, 'occurrence_broken' ), 10, 3 );

		// intercept post content - try to catch calendar shortcodes
		add_filter( 'the_content', array( $this, 'intercept_content' ), 10, 1 );
	}



	/**
	 * @description: utility to check if Event Organiser is present and active
	 * @return bool
	 */
	public function is_active() {
		// only check once
		static $eo_active = false;
		if ( $eo_active ) { return true; }

		// access Event Organiser option
		$installed_version = get_option( 'eventorganiser_version' );

		// this plugin will not work without EO version 2+.
		if ( $installed_version !== false && $installed_version >= '2' ) {
			$eo_active = true;
		}

		return $eo_active;
	}



	/**
	 * @description: utility to check if BP Group Hierarchy is present and active
	 * @return bool
	 */
	public function is_group_hierarchy_active() {

		// only check once
		static $bpgh_active = false;
		if ( $bpgh_active ) { return true; }

		// do we have the BP Group Hierarchy plugin constant and tree method?
		if (
			defined( 'BP_GROUP_HIERARCHY_IS_INSTALLED' ) AND
			method_exists( 'BP_Groups_Hierarchy', 'get_tree' )
		) {

			// set flag
			$bpgh_active = true;

		}

		// --<
		return $bpgh_active;

	}



 	//##########################################################################



	/**
	 * @description: intercept save event
	 * @param int $post_id the numeric ID of the WP post
	 * @return nothing
	 */
	public function intercept_save_event( $post_id, $post, $update ) {
		if ( empty( $_REQUEST['bp_event_organiser_nonce_field'] ) ) {
			return;
		}

		check_admin_referer( 'bp_event_organiser_meta_save', 'bp_event_organiser_nonce_field' );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( 'event' !== $post->post_type ) {
			return;
		}

		// Save BP groups for this EO event.
		$this->update_event_groups( $post_id );
	}



	/**
	 * @description: intercept before break occurrence
	 * @param int $post_id the numeric ID of the WP post
	 * @param int $occurrence_id the numeric ID of the occurrence
	 * @return nothing
	 */
	public function pre_break_occurrence( $post_id, $occurrence_id ) {

		/*
		// eg ( [post_id] => 31 [occurrence_id] => 2 )
		print_r( array(
			'method' => 'bpeo->pre_break_occurrence',
			'post_id' => $post_id,
			'occurrence_id' => $occurrence_id,
		) ); die();
		*/

		// init or die
		if ( ! $this->is_active() ) return;

		// unhook eventorganiser_save_event, because EO copies across post-meta
		remove_action( 'eventorganiser_save_event', array( $this, 'intercept_save_event' ), 10 );

	}



	/**
	 * @description: intercept after break occurrence
	 * @param int $post_id the numeric ID of the WP post
	 * @param int $occurrence_id the numeric ID of the occurrence
	 * @param int $new_event_id the numeric ID of the new WP post
	 * @return nothing
	 */
	public function occurrence_broken( $post_id, $occurrence_id, $new_event_id ) {

		/*
		print_r( array(
			'method' => 'bpeo->occurrence_broken',
			'post_id' => $post_id,
			'occurrence_id' => $occurrence_id,
			'new_event_id' => $new_event_id,
		) ); die();
		*/

		// --<
		return;

		// get existing event groups
		$existing = bpeo_get_event_groups( $post_id );

		// transfer to new event
		$this->set_event_groups( $new_event_id, $existing );

	}



	/**
	 * @description: register event meta box
	 * @return nothing
	 */
	public function event_meta_box() {

		// create it
		add_meta_box(
			'bp_event_organiser_metabox',
			is_admin() ? __( 'BuddyPress Groups', 'bp-event-organiser' ) : __( 'Groups', 'bp-event-organiser' ),
			array( $this, 'event_meta_box_render' ),
			'event',
			'side', //'normal',
			'core' //'high'
		);
	}



	/**
	 * @description: define venue meta box
	 * @return nothing
	 */
	public function event_meta_box_render( $event ) {

		// add nonce
		wp_nonce_field( 'bp_event_organiser_meta_save', 'bp_event_organiser_nonce_field' );


		// get array of checked IDs for this event
		$this->group_ids = (array) bpeo_get_event_groups( $event->ID );

		if ( bp_is_group() ) {
			$this->group_ids = array_unique( array_merge( $this->group_ids, (array) bp_get_current_group_id() ) );
		}

	?>

		<p class="bp_event_organiser_desc"><?php _e( 'Enter the names of each group this event should appear in.', 'bp-event-organiser' ); ?></p>

		<select name="bp_group_organizer_groups[]" multiple="multiple" style="width:100%;">
			<?php
				foreach( $this->group_ids as $gid ) {
					$group = groups_get_group( array( 'group_id' => $gid ) );
					$public = 'public' === $group->status ? 'title="Public"' : '';
					echo "<option value='{$gid}' selected='selected' {$public}>{$group->name}</option>";
				}
			?>
		</select>

		<?php if ( ! empty( $this->group_ids ) ) : ?>
			<p class="howto"><?php _e( 'To remove a group, click on the "x" link.', 'bp-event-organiser' ); ?></p>
		<?php endif; ?>
	<?php
	}

	/**
	 * AJAX handler for meta box autocomplete using the Select2 JS library.
	 *
	 * @see BuddyPress_Event_Organiser_EO::event_meta_box_render()
	 */
	public function ajax_get_groups() {
		global $groups_template;

		check_ajax_referer( 'bp_event_organiser_meta_save', 'nonce' );

		$groups = groups_get_groups( array(
			'user_id' => is_super_admin() ? 0 : bp_loggedin_user_id(),
			'search_terms' => $_POST['s'],
			'show_hidden' => true,
			'populate_extras' => false
		) );

		$json = array();
		$groups_template = new stdClass;
		$groups_template->group = new stdClass;

		foreach ( $groups['groups'] as $group ) {
			$groups_template->group = $group;
			$json[] = array(
				'id'          => $group->id,
				'name'        => stripslashes( $group->name ),
				'type'        => bp_get_group_type(),
				'description' => bp_create_excerpt( strip_tags( stripslashes( $group->description ) ), 90, array(
					'ending' => '&hellip;',
					'filter_shortcodes' => false
				) ),
				'avatar' => bp_get_group_avatar_mini(),
				'total_member_count' => $group->total_member_count,
				'public' => $group->status === 'public'
			);
		}

		echo json_encode( $json );
		exit();
	}


	//##########################################################################



	/**
	 * @description: update event groups array
	 * @param int $event_id the numeric ID of the event
	 * @return nothing
	 */
	public function update_event_groups( $event_id ) {
		$group_ids = array();

		// If this $_POST item is empty, group associations are being unset.
		if ( isset( $_POST['bp_group_organizer_groups'] ) && is_array( $_POST['bp_group_organizer_groups'] ) ) {
			$group_ids = array_map( 'intval', $_POST['bp_group_organizer_groups'] );
		}

		$this->set_event_groups( $event_id, $group_ids );
	}

	/**
	 * @description: update event groups array
	 * @param int   $event_id  The numeric ID of the event.
	 * @param array $group_ids IDs of the groups being set. Will overwrite existing connected groups.
	 * @return nothing
	 */
	protected function set_event_groups( $event_id, $group_ids ) {
		$existing_group_ids = bpeo_get_event_groups( $event_id );

		$groups_to_add = array_diff( $group_ids, $existing_group_ids );
		foreach ( $groups_to_add as $group_to_add ) {
			bpeo_connect_event_to_group( $event_id, $group_to_add );
		}

		$groups_to_remove = array_diff( $existing_group_ids, $group_ids );
		foreach ( $groups_to_remove as $group_to_remove ) {
			bpeo_disconnect_event_from_group( $event_id, $group_to_remove );
		}
	}

	/**
	 * @description: delete event groups
	 * @param int $post_id the numeric ID of the WP post
	 * @return nothing
	 */
	public function clear_event_groups( $post_id ) {

		// delete the meta value
		delete_post_meta( $post_id, '_bpeo_event_groups' );

	}



	//##########################################################################



	/**
	 * @description: getter method for accessing group IDs for metabox list walker
	 * @return array $group_ids array of group IDs
	 */
	public function get_group_ids() {

		// do we have the property?
		if ( isset( $this->group_ids ) AND is_array( $this->group_ids ) ) {

			// yup, send it back
			return $this->group_ids;

		}

		// return an empty array by default
		return array();

	}



	//##########################################################################

	/**
	 * @description: get all event groups
	 * @param int $event_id the numeric ID of the WP post
	 * @return bool $event_groups_array the event groups event
	 */
	public function get_calendar_groups( $event_id ) {
		return bpeo_get_event_groups( $event_id );
	}



	//##########################################################################



	/**
	 * @description: intercept content and clear calendar cache if shortcode is present
	 * @return string $content the post content
	 */
	public function intercept_content( $content ) {

		// do we have the shortcode?
		if ( has_shortcode( $content, 'eo_fullcalendar' ) ) {

			// yup, bust cache
			delete_transient( 'eo_full_calendar_public' );

		}

		// pass content on
		return $content;

	}



	//##########################################################################



	/**
	 * @description: debugging
	 * @param array $msg
	 * @return string
	 */
	private function _debug( $msg ) {

		// add to internal array
		$this->messages[] = $msg;

		// do we want output?
		if ( BUDDYPRESS_EVENT_ORGANISER_DEBUG ) print_r( $msg );

	}



} // class ends

/**
 * @description: get list of group IDs for an event's metabox
 */
function bp_event_organiser_get_group_ids() {

	// access plugin global
	global $buddypress_event_organiser;

	// --<
	return $buddypress_event_organiser->eo->get_group_ids();

}


