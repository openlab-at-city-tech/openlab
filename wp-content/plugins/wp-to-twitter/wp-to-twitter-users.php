<?php
/**
 * User settings WP to Twitter
 *
 * @category Users
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( '1' === get_option( 'jd_individual_twitter_users' ) ) {
	add_action( 'show_user_profile', 'wpt_twitter_profile' );
	add_action( 'edit_user_profile', 'wpt_twitter_profile' );
	add_action( 'profile_update', 'wpt_twitter_save_profile' );
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show user profile data on Edit User pages.
 */
function wpt_twitter_profile() {
	global $user_ID;
	$current_user = wp_get_current_user();
	$user_edit    = ( isset( $_GET['user_id'] ) ) ? (int) $_GET['user_id'] : $user_ID;

	$is_enabled       = get_user_meta( $user_edit, 'wp-to-twitter-enable-user', true );
	$twitter_username = get_user_meta( $user_edit, 'wp-to-twitter-user-username', true );
	$wpt_remove       = get_user_meta( $user_edit, 'wpt-remove', true );
	if ( $current_user->ID === $user_ID || current_user_can( 'manage_options' ) ) {
		?>
		<h3><?php _e( 'WP Tweets User Settings', 'wp-to-twitter' ); ?></h3>
		<?php
		if ( function_exists( 'wpt_connect_oauth_message' ) ) {
			wpt_connect_oauth_message( $user_edit );
		}
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Use My Twitter Username', 'wp-to-twitter' ); ?></th>
				<td>
					<input type="radio" name="wpt-enable-user" id="wpt-enable-user-3" value="mainAtTwitter"<?php checked( $is_enabled, 'mainAtTwitter' ); ?> /> <label for="wpt-enable-user-3"><?php _e( 'Tweet my posts with an @ reference to my username.', 'wp-to-twitter' ); ?></label><br/>
					<input type="radio" name="wpt-enable-user" id="wpt-enable-user-4" value="mainAtTwitterPlus"<?php checked( $is_enabled, 'mainAtTwitterPlus' ); ?> /> <label for="wpt-enable-user-4"><?php _e( 'Tweet my posts with an @ reference to both my username and to the main site username.', 'wp-to-twitter' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="wpt-username"><?php _e( 'Your Twitter Username', 'wp-to-twitter' ); ?></label>
				</th>
				<td>
					<input type="text" name="wpt-username" id="wpt-username" value="<?php echo esc_attr( $twitter_username ); ?>"/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="wpt-remove"><?php _e( 'Hide account name in Tweets', 'wp-to-twitter' ); ?></label></th>
				<td>
					<input type="checkbox" name="wpt-remove" id="wpt-remove" aria-describedby="wpt-remove-desc" value="on"<?php checked( 'on', $wpt_remove ); ?> /> <span id="wpt-remove-desc"><?php _e( 'Do not display my account in the #account# template tag.', 'wp-to-twitter' ); ?></span>
				</td>
			</tr>
			<?php echo apply_filters( 'wpt_twitter_user_fields', $user_edit ); ?>
		</table>
		<?php
		if ( current_user_can( 'wpt_twitter_oauth' ) || current_user_can( 'manage_options' ) ) {
			if ( function_exists( 'wtt_connect_oauth' ) ) {
				wtt_connect_oauth( $user_edit );
			}
		}
	} else {
		// hidden fields. If function is enabled, but this user does not have privileges to edit.
		?>
		<input type="hidden" name="wp-to-twitter-enable-user" value="<?php echo esc_attr( $is_enabled ); ?>" />
		<input type="hidden" name="wp-to-twitter-user-username" value="<?php echo esc_attr( $twitter_username ); ?>" />
		<input type="hidden" name="wpt-remove" value="<?php echo esc_attr( $wpt_remove ); ?>" />
		<?php
	}
}

/**
 * This compensates for an old error where the user ID is echoed directly into the page.
 */
add_filter( 'wpt_twitter_user_fields', 'wpt_basic_user_fields', 100, 1 );
/**
 * Return empty string if value is an integer.
 *
 * @param int $user_edit User ID.
 *
 * @return empty string.
 */
function wpt_basic_user_fields( $user_edit ) {
	if ( is_int( $user_edit ) ) {
		return '';
	}

	return $user_edit;
}

/**
 * Save user profile data
 */
function wpt_twitter_save_profile() {
	global $user_ID;
	$current_user = wp_get_current_user();
	if ( isset( $_POST['user_id'] ) ) {
		$edit_id = (int) $_POST['user_id'];
	} else {
		$edit_id = $user_ID;
	}
	if ( current_user_can( 'wpt_twitter_oauth' ) || current_user_can( 'manage_options' ) ) {
		$enable     = ( isset( $_POST['wpt-enable-user'] ) ) ? $_POST['wpt-enable-user'] : '';
		$username   = ( isset( $_POST['wpt-username'] ) ) ? $_POST['wpt-username'] : '';
		$wpt_remove = ( isset( $_POST['wpt-remove'] ) ) ? 'on' : '';
		update_user_meta( $edit_id, 'wp-to-twitter-enable-user', $enable );
		update_user_meta( $edit_id, 'wp-to-twitter-user-username', $username );
		update_user_meta( $edit_id, 'wpt-remove', $wpt_remove );
	}
	// WPT PRO.
	apply_filters( 'wpt_save_user', $edit_id, $_POST );
}

add_action( 'admin_head', 'wpt_css' );
/**
 * Output CSS governing styles for authorized users column.
 */
function wpt_css() {
	?>
	<style type="text/css">
		th#wpt {
			width: 60px;
		}

		.wpt_twitter .authorized {
			padding: 1px 3px;
			border-radius: 3px;
			background: #070;
			color: #fff;
		}
	</style>
	<?php
}
