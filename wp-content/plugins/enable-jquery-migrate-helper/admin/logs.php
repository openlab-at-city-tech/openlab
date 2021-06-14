<?php
/**
 * Admin logs page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

if ( ! function_exists( 'get_plugins' ) ) {
	require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/plugin.php';
}

$plugins = array();

foreach ( get_plugins() as $slug => $plugin ) {
	$slug = explode( '/', $slug );
	$plugins[ $slug[0] ] = $plugin;
}

$themes = wp_get_themes();

$logs = get_option( 'jqmh_logs', array() );
?>

<h2>Logs</h2>

<div class="notice notice-info inline">
    <p>
	    <?php _e( 'The following are deprecations logged from the front-end of your site, or while live deprecation notices were disabled in the admin area.', 'enable-jquery-migrate-helper' ); ?>
    </p>
</div>

<div style="text-align:right;">
    <button type="button" class="button jqmh-clear-deprecation-notices button-default"><?php _e( 'Clear logs', 'enable-jquery-migrate-helper' ); ?></button>
</div>

<table class="widefat striped">
    <thead>
    <tr>
        <th><?php _ex( 'Time', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Notice', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Plugin or theme', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'File location', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Triggered on page', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
    </tr>
    </thead>

    <tbody id="jqmh-logged-notices">
	<?php if ( empty( $logs ) ) : ?>
        <tr>
            <td colspan="5">
				<?php _e( 'No deprecations have been logged', 'enable-jquery-migrate-helper' ); ?>
            </td>
        </tr>
	<?php endif; ?>

	<?php
    foreach ( $logs as $log ) :

		preg_match( '/\/plugins\/(?P<slug>.+?)\/.+?: (?P<notice>.+)/', $log['notice'], $plugin );
		preg_match( '/\/themes\/(?P<slug>.+?)\/.+?: (?P<notice>.+)/', $log['notice'], $theme );
		preg_match( '/\/wp-(admin|includes)\/.+?: (?P<notice>.+)/', $log['notice'], $core );

		$notice = $log['notice'];
		$source = __( 'Undetermined', 'enable-jquery-migrate-helper' );
		$file   = __( 'Inline code, unknown file location', 'enable-jquery-migrate-helper' );

		if ( ! empty( $plugin ) ) {
			preg_match( '/(?P<path>https?:\/\/.+?):/', $log['notice'], $file );
			$file = $file['path'];

			$plugin_link = '#';

			if ( isset( $plugins[ $plugin['slug'] ] ) ) {
				$plugin_link = ( isset( $plugins[ $plugin['slug'] ]['PluginURI'] ) ? $plugins[ $plugin['slug'] ]['PluginURI'] : $plugins[ $plugin['slug'] ]['AuthorURI'] );
			}

			$notice = $plugin['notice'];
			$source = sprintf(
			// translators: 1: Linked name of the plugin throwing notices.
				__( 'Plugin: %s', 'enable-jquery-migrate-helper' ),
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( $plugin_link ),
					esc_html( ( isset( $plugins[ $plugin['slug'] ] ) ? $plugins[ $plugin['slug'] ]['Name'] : $plugin['slug'] ) )
				)
			);
		} elseif ( ! empty( $theme ) ) {
			preg_match( '/(?P<path>https?:\/\/.+?):/', $log['notice'], $file );
			$file = $file['path'];

			$theme_link = '#';

			if ( isset( $themes[ $theme['slug'] ] ) ) {
				$theme_link = $themes[ $theme['slug'] ]->get( 'ThemeURI' );
			}

			$notice = $theme['notice'];
			$source = sprintf(
			// translators: 1: Linked name of the theme throwing notices.
				__( 'Theme: %s', 'enable-jquery-migrate-helper' ),
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( $theme_link ),
					esc_html( ( isset( $themes[ $theme['slug'] ] ) ? $themes[ $theme['slug'] ]->get( 'Name' ) : $theme['slug'] ) )
				)
			);
		} elseif ( ! empty( $core ) ) {
			preg_match( '/(?P<path>https?:\/\/.+?):/', $log['notice'], $file );
			$file = $file['path'];

			$notice = $core['notice'];
			$source = __( 'WordPress core', 'enable-jquery-migrate-helper' );
        }

		?>

        <tr>
            <td><?php echo esc_html( $log['registered'] ); ?></td>
            <td><?php echo esc_html( $notice ); ?></td>
            <td><?php echo $source; ?></td>
            <td><?php echo esc_html( $file ); ?></td>
            <td><?php echo esc_html( $log['page'] ); ?></td>
        </tr>

	<?php endforeach; ?>
    </tbody>

    <tfoot>
    <tr>
        <th><?php _ex( 'Time', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Notice', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Plugin or theme', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'File location', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
        <th><?php _ex( 'Page', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
    </tr>
    </tfoot>
</table>

<div style="text-align:right;">
    <button type="button" class="button jqmh-clear-deprecation-notices button-default"><?php _e( 'Clear logs', 'enable-jquery-migrate-helper' ); ?></button>
</div>

<script type="text/javascript">
    var i = 0,
        clear_nonce = '<?php echo esc_js( wp_create_nonce( 'jquery-migrate-previous-deprecations' ) ); ?>',
        clear_buttons = document.getElementsByClassName( 'jqmh-clear-deprecation-notices' );

    for ( i = 0; i < clear_buttons.length; i++ ) {
    	clear_buttons[ i ].addEventListener( 'click', function() {
			var o,
                xhr = new XMLHttpRequest(),
                displays = document.getElementsByClassName( 'jqmh-deprecations' );

			xhr.open( 'POST', '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>' );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			xhr.onload = function () {};

			xhr.send( encodeURI( 'action=jquery-migrate-dismiss-notice&notice=jquery-migrate-previous-deprecations&dismiss-notice-nonce=' + clear_nonce ) );

            for ( o = 0; o < displays.length; o++ ) {
            	displays[ o ].parentNode.removeChild( displays[ o ] );
            }

            document.getElementById( 'jqmh-logged-notices' ).innerText = '';

        } );
    }
</script>