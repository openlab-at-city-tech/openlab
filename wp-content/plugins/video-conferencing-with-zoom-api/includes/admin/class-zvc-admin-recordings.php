<?php

/**
 * Class Recordings
 *
 * @author  Deepen
 * @since   3.5.0
 */
class Zoom_Video_Conferencing_Recordings {

	private static $instance;

	public function __construct() {
	}

	static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Zoom Recordings View
	 *
	 * @since   3.5.0
	 * @changes in CodeBase
	 * @author  Deepen Bajracharya
	 */
	public static function zoom_recordings() {
		wp_enqueue_script( 'video-conferencing-with-zoom-api-js' );
		wp_enqueue_script( 'video-conferencing-with-zoom-api-select2-js' );
		wp_enqueue_script( 'video-conferencing-with-zoom-api-datable-js' );

		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		//Check if any transient by name is available
		if ( isset( $_GET['host_id'] ) ) {
			$recordings = json_decode( zoom_conference()->listRecording( $_GET['host_id'] ) );
		}

		if ( ! empty( $recordings ) && ! empty( $recordings->code ) ) {
			echo '<p>' . $recordings->message . '</p>';
		} else {
			//Get Template
			require_once ZVC_PLUGIN_VIEWS_PATH . '/live/tpl-list-recordings.php';
		}
	}

	/**
	 * Get Host selection HTML block
	 *
	 * @param $host_id
	 */
	public function get_hosts( $host_id ) {
		$users = video_conferencing_zoom_api_get_user_transients();
		?>
        <div class="select_zvc_user_listings_wrapp">
            <div class="alignright">
                <select onchange="location = this.value;" class="zvc-hacking-select">
                    <option value="?post_type=zoom-meetings&page=zoom-video-conferencing"><?php _e( 'Select a User', 'video-conferencing-with-zoom-api' ); ?></option>
					<?php
					foreach ( $users as $user ) {
						$host_recordings_link = add_query_arg( array(
							'post_type' => 'zoom-meetings',
							'page'      => 'zoom-video-conferencing-recordings',
							'host_id'   => $user->id
						), admin_url( 'edit.php' ) );
						?>
                        <option value="<?php echo esc_url( $host_recordings_link ); ?>" <?php echo $host_id == $user->id ? 'selected' : false; ?>><?php echo $user->first_name . ' ( ' . $user->email . ' )'; ?></option>
					<?php } ?>
                </select>
            </div>
            <div class="clear"></div>
        </div>
		<?php
	}

}

function zvc_recordings() {
	return Zoom_Video_Conferencing_Recordings::getInstance();
}

zvc_recordings();