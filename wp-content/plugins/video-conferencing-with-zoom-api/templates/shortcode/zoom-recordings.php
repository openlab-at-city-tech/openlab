<?php
/**
 * The Template for displaying list of recordings via Host ID
 *
 * This template can be overridden by copying it to yourtheme/video-conferencing-zoom/shortcode/zoom-recordings.php.
 *
 * @package    Video Conferencing with Zoom API/Templates
 * @version     3.5.0
 */

global $zoom_recordings;
?>
    <table id="vczapi-recordings-list-table" class="vczapi-recordings-list-table vczapi-user-meeting-list">
        <thead>
        <tr>
            <th><?php _e( 'Meeting ID', 'video-conferencing-with-zoom-api' ); ?></th>
            <th><?php _e( 'Topic', 'video-conferencing-with-zoom-api' ); ?></th>
            <th><?php _e( 'Duration', 'video-conferencing-with-zoom-api' ); ?></th>
            <th><?php _e( 'Recorded', 'video-conferencing-with-zoom-api' ); ?></th>
            <th><?php _e( 'Size', 'video-conferencing-with-zoom-api' ); ?></th>
            <th><?php _e( 'Action', 'video-conferencing-with-zoom-api' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php
		foreach ( $zoom_recordings->meetings as $recording ) {
			?>
            <tr>
                <td><?php echo $recording->id; ?></td>
                <td><?php echo $recording->topic; ?></td>
                <td><?php echo $recording->duration; ?></td>
                <td><?php echo date( 'F j, Y, g:i a', strtotime( $recording->start_time ) ); ?></td>
                <td><?php echo vczapi_filesize_converter( $recording->total_size ); ?></td>
                <td>
					<?php if ( ! empty( $recording->recording_files ) ) {
						foreach ( $recording->recording_files as $files ) {
							if ( $files->file_type === "MP4" ) {
								?>
                                <a href="<?php echo $files->play_url; ?>" target="_blank"><?php _e( 'Play', 'video-conferencing-with-zoom-api' ); ?></a>
								<?php if ( $zoom_recordings->downloadable ) { ?>
                                    <a href="<?php echo $files->download_url; ?>" target="_blank"><?php _e( 'Download', 'video-conferencing-with-zoom-api' ); ?></a>
									<?php
								}
							}
						}
					} else {
						echo "N/A";
					} ?>
                </td>
            </tr>
			<?php
		}
		?>
        </tbody>
    </table>

<?php
if ( ! empty( $zoom_recordings ) ) {
	vczapi_zoom_api_paginator( $zoom_recordings, 'recordings' );
}
?>