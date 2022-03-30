<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \WP_User_Query;
use \DateTime;
use Inc\Core\Utillities;
use Inc\Base\BaseController;

class Members {

	public static function get_teams() {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );

		foreach ( $teams as $team_key => $team ) {
			foreach ( $team['members'] as $key => $value ) {
				$teams[$team_key]['members'][$key] = Utillities::get_user_settings( $value );
			}
		}

		return (array) $teams;
	}

	public static function get_team( $id ) {
		$teams = Members::get_teams();

		foreach ( $teams as $team ) {
			if ( $team['id'] == $id ) {
				return $team;
			}
		}

		return;
	}

	public static function add_team( $name, $description, $members ) {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );

		$last_team = end( $teams );
		$id = !empty( $last_team ) ? (int) $last_team['id'] + 1 : '0';

		$new_team = array(
			'id' 		  => $id,
			'name' 		  => $name,
			'description' => $description,
			'members' 	  => $members
		);

		reset( $teams );
		$teams[] = $new_team;
		update_option( 'zpm_teams', serialize( $teams ) );
		return $id;
	}

	public static function update_team( $id, $name, $description, $members ) {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );
		
		foreach ($teams as $key => $value) {
			if ( $value['id'] == $id ) {
				$update_team = array(
					'id' 		  => $id,
					'name' 		  => $name,
					'description' => $description,
					'members' 	  => $members
				);
				$teams[$key] = $update_team;
			}
		}

		update_option( 'zpm_teams', serialize( $teams ) );
	}

	public static function delete_team( $id ) {
		$teams = Members::get_teams();

		foreach ($teams as $key => $value) {
			if ( $value['id'] == $id ) {
				unset($teams[$key]);
			}
		}

		update_option( 'zpm_teams', serialize( $teams ) );
	}

	public static function get_members( $limit = null, $paged = null ) {
		
		$manager = ZephyrProjectManager();
		$members = array();

		// Get paginated users
		if ( !is_null( $limit ) && !is_null( $paged ) ) {
			if ( $paged == 1 ){
				$offset = 0;
			} else {
			   $offset = ( $paged - 1 ) * $limit;
			}

		    $args = array(
		        'number' => $limit,
		        'offset' => $offset
		    );

		    $user_query = new WP_User_Query( $args );
		    $users = $user_query->results;
		} else {
			// Get all users
			$users = $manager::get_users( false );
		}
	    
		foreach ($users as $user) {
			if (apply_filters( 'zpm_should_show_user', true, $user )) {
				$settings = Utillities::get_user_settings( $user->ID );
				$members[] = $settings;
			}
		}

		return $members;
	}

	public static function get_member( $member_id ) {
		$member = Utillities::get_user_settings( $member_id );
		return $member;
	}

	public static function get_member_name( $member_id ) {
		$member = Utillities::get_user_settings( $member_id );
		return isset( $member['name'] ) ? $member['name'] : "";
	}

	public static function getMemberByName($name) {
		$members = self::get_members();
		foreach ($members as $member) {
			if (strtolower($member['name']) == strtolower($name)) {
				return $member;
			}
		}
		return false;
	}

	public static function getTeamByName($name) {
		$teams = self::get_teams();
		foreach ($teams as $team) {
			if (strtolower($team['name']) == strtolower($name)) {
				return $team;
			}
		}
		return false;
	}

	/**
	* Returns an array of all users that have access to Zephyr
	*/
	public static function get_zephyr_members() {
		$manager = ZephyrProjectManager();
		$users = $manager::get_users( false );
		$members = array();

		foreach ($users as $user) {
			
			$settings = Utillities::get_user_settings( $user->ID );

			if (( isset( $settings['can_zephyr'] ) && $settings['can_zephyr'] == "true" ) || !isset( $settings['can_zephyr'] )) {
				$members[] = $settings;
			}
		}

		return $members;
	}

	public static function getManagers() {
		$members = Members::get_zephyr_members();
		$results = [];

		foreach ($members as $member) {
			if (user_can( $member['id'], 'zpm_manager' )) {
				$results[] = $member;
			}
		}

		return $results;
	}

	public static function team_single_html( $team ) {
		ob_start();
		?>
		<div class="zpm_team_member" data-team-id="<?php echo $team['id']; ?>">
			<div class="zpm_member_details" data-ripple="rgba(0,0,0,0.1)" zpm-ripple>
				<h3 class="zpm-team-name"><?php echo $team['name']; ?></h3>
				<p class="zpm-description-text"><?php echo $team['description'] !== "" ? $team['description'] : "<p class='zpm-no-description-error'>No description added.</p>"; ?></p>
				<h3 class="zpm-team-members-title"><?php _e( 'Members', 'zephyr-project-manager' ); ?></h3>

				<ul class="zpm-team-member-list">
					<?php $member_count = 0; ?>
					<?php foreach ($team['members'] as $member) : ?>
						<?php if (!isset($member['name'])) { continue; } ?>
						<li><?php echo $member['name']; ?></li>
						<?php $member_count++; ?>
					<?php endforeach; ?>
				</ul>

				<?php if ($member_count <= 0) : ?>
					<p class="zpm-team-no-members"><?php _e( 'No members have been added to this team', 'zephyr-project-manager' ); ?></p>
				<?php endif; ?>

				<div class="zpm-team-options-btns">
					<button class="zpm_button zpm-delete-team" data-team-id="<?php echo $team['id']; ?>">Delete</button>
					<button class="zpm_button zpm-edit-team" data-team-id="<?php echo $team['id']; ?>" data-zpm-modal-trigger="zpm-edit-team-modal"><?php _e( 'Edit Team', 'zephyr-project-manager' ); ?></button>
				</div>
			</div>
		</div>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function team_dropdown_html( $id = '', $selected = null) {
		ob_start();

		$teams = Members::get_teams();
		
		?>
			<select id="<?php echo $id !== '' ? $id : 'zpm-team-select-dropdown'; ?>" class="zpm-team-select-dropdown zpm_input zpm-input-chosen">
				<option value="-1"><?php _e( 'Select Team', 'zephyr-project-manager' ); ?></option>
				<?php foreach ($teams as $team) : ?>
					<option value="<?php echo $team['id']; ?>" <?php echo !is_null( $selected ) && $selected == $team['id'] ? 'selected' : ''; ?>><?php echo $team['name']; ?></option>
				<?php endforeach; ?>
			</select>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function add_new_user( $username, $email, $password, $role, $unique_id ) {

		$WP_array = array (
	        'user_login'    =>  $username,
	        'user_email'    =>  $email,
	        'user_pass'     =>  $password,
	        'user_url'      =>  '',
	        'first_name'    =>  $username,
	        'last_name'     =>  '',
	        'nickname'      =>  $username,
	        'description'   =>  '',
	    ) ;

	    $id = wp_insert_user( $WP_array ) ;

	    if (!is_wp_error( $id )) {
	    	wp_update_user( array ( 'ID' => $id, 'role' => $role ) ) ;
	    	add_user_meta( $id, '_zpm_unique_id', $unique_id );
	    }

	    return $id;
	}

	public static function get_user_by_meta_data( $meta_key, $meta_value ) {

		// Query for users based on the meta data
		$user_query = new WP_User_Query(
			array(
				'meta_key'	  =>	$meta_key,
				'meta_value'	=>	$meta_value
			)
		);

		// Get the results from the query, returning the first user
		$users = $user_query->get_results();

		if ( sizeof( $users ) > 0 && isset( $users[0]) ) {
			return $users[0];
		} else {
			return array();
		}
	}

	/**
	* Checks whether a user is in a team
	*/
	public static function is_user_in_team( $user_id, $team_id ) {
		
		$team = Members::get_team( $team_id );

		if ( !is_null( $team ) ) {
			foreach ( (array) $team['members'] as $key => $member) {
				if ( $member['id'] == $user_id ) {
					return true;
				}
			}
			return false;
		}
		return false;
	}

	public static function list_html( $member ) {
		$edit_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_teams_members')) . '&action=edit_member&user_id=' . $member['id'];
		ob_start();
		?>
			<a class="zpm-table__row zpm-member-list__row zpm-table__full-link <?php echo $member['can_zephyr'] == "true" ? 'zpm-user-can-zephyr' : ''; ?>" <?php echo current_user_can( 'administrator' ) ? "href='" . $edit_url . "'" : ''; ?>>
				<span class="zpm-table__avatar" style="background-image: url(<?php echo $member['avatar']; ?>);"></span>
				<span class="zpm-table__name"><?php echo $member['name']; ?></span>
				<span class="zpm-table__label zpm-table__label-email"><?php echo $member['email']; ?></span>

				<?php if (current_user_can( 'administrator' )) : ?>
					<span class="zpm-user-row__access-controls">
						<label for="zpm-can-zephyr-<?php echo $member['id']; ?>" class="zpm_checkbox_label">
							<input type="checkbox" id="zpm-can-zephyr-<?php echo $member['id']; ?>" name="zpm_can_zephyr" class="zpm-can-zephyr zpm_toggle invisible" value="1" data-user-id="<?php echo $member['id']; ?>" <?php echo $member['can_zephyr'] == "true" ? 'checked' : ''; ?>>

							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'Allow Access', 'zephyr-project-manager' ); ?>
					    </label>
					</span>
				<?php endif; ?>
			</a>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function memberIdStringToNameString($string) {
		$ids = explode(',', $string);
		$names = [];
		foreach ($ids as $id) {
			if (!is_numeric($id)) {
				$names[] = $id;
				continue;
			}
			$member = Members::get_member($id);
			$names[] = $member['name'];
		}
		return implode(',', $names);
	}

	public static function memberNameStringToIdString($string) {
		$names = explode(',', $string);
		$ids = [];
		foreach ($names as $name) {
			if (is_numeric($name)) {
				$ids[] = $name;
				continue;
			}
			$member = Members::getMemberByName($name);
			if ($member !== false) {
				$ids[] = $member['id'];
			}
		}
		return implode(',', $ids);
	}

	public static function teamIdStringToNameString($string) {
		$ids = explode(',', $string);
		$names = [];
		foreach ($ids as $id) {
			if (!is_numeric($id)) {
				$names[] = $id;
				continue;
			}
			$team = Members::get_team($id);
			$names[] = $team['name'];
		}
		return implode(',', $names);
	}

	public static function teamNameStringToIdString($string) {
		$names = explode(',', $string);
		$ids = [];
		foreach ($names as $name) {
			if (is_numeric($name)) {
				$ids[] = $name;
				continue;
			}
			$team = Members::getTeamByName($name);
			if ($team !== false) {
				$ids[] = $team['id'];
			}
		}
		return implode(',', $ids);
	}
}