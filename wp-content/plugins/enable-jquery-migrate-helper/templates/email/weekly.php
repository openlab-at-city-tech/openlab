<?php
/**
 * Template for weekly scheduled email.
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

<p>
	<?php _e( 'Greetings!', 'enable-jquery-migrate-helper' ); ?>
</p>

<p>
	<?php _e( 'This is a weekly summary of the warnings still present on your site, relating to the jQuery library. These errors should be addressed as soon as possible.', 'enable-jquery-migrate-helper' ); ?>
</p>

<?php if ( 'yes' === get_option( '_jquery_migrate_downgrade_version', 'no' ) ) : ?>

<p>
	<strong>
		 <?php _e( 'Your site is running a legacy version of jQuery, modern functionality is currently not available to your plugins, themes or WordPress itself.', 'enable-jquery-migrate-helper' ); ?>
	</strong>
</p>

<?php endif; ?>

<?php if ( jQuery_Migrate_Helper::logged_migration_notice_count() < 1 ) : ?>

<p>
	<?php _e( 'There have been no reported deprecations logged in the past week, maybe you no longer need this plugin?', 'enable-jquery-migrate-helper' ); ?>
</p>

<?php else : ?>

<p>
	<?php _e( 'The following deprecations have been logged from the front-end of your site, or from your admin area while live deprecation notices were disabled.', 'enable-jquery-migrate-helper' ); ?>
</p>

<table style="background: #fff; border: 1px solid #ccd0d4;">
	<thead>
		<tr>
			<th style="border-bottom: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Time', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-bottom: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Notice', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-bottom: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Plugin or theme', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-bottom: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'File location', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-bottom: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Triggered on page', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
		</tr>
	</thead>

	<?php
	$odd = true;
	foreach ( $logs as $log ) :

		preg_match( '/\/plugins\/(?P<slug>.+?)\/.+?: (?P<notice>.+)/', $log['notice'], $plugin );
		preg_match( '/\/themes\/(?P<slug>.+?)\/.+?: (?P<notice>.+)/', $log['notice'], $theme );
		preg_match( '/\/wp-(admin|includes)\/.+?: (?P<notice>.+)/', $log['notice'], $core );

		$notice = $log['notice'];
		// Translators: Undetermined source
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

		<tr style="<?php echo ( $odd ? 'background-color: #f9f9f9;' : '' ); ?>">
			<td style="padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php echo esc_html( $log['registered'] ); ?></td>
			<td style="padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php echo esc_html( $notice ); ?></td>
			<td style="padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php echo $source; ?></td>
			<td style="padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php echo esc_html( $file ); ?></td>
			<td style="padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php echo esc_html( $log['page'] ); ?></td>
		</tr>

	<?php if ( $odd ) { $odd = false; } else { $odd = true; } ?>

	<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr>
			<th style="border-top: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Time', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-top: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Notice', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-top: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Plugin or theme', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-top: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'File location', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
			<th style="border-top: 1px solid #ccd0d4; text-align: left; padding-top: 8px; padding-bottom: 8px; padding-left: 10px; padding-right: 10px;"><?php _ex( 'Page', 'Admin deprecation notices', 'enable-jquery-migrate-helper' ); ?></th>
		</tr>
	</tfoot>
</table>

<p>
    <?php
    printf(
        // translators: 1: Link to deprecation log page.
        __( 'You can view captured and logged deprecations at %s', 'enable-jquery-migrate-helper' ),
        admin_url( 'tools.php?page=jqmh&tab=logs' )
    );
    ?>
</p>

<?php endif; ?>

<p>
	&nbsp;
</p>

<p>
	<span style="font-style: italic;">
        <?php
        printf(
            // translators: 1: The website name as a link.
	        __( 'This email was automatically generated by the Enable jQuery Migrate Helper plugin on your website %s', 'enable-jquery-migrate-helper' ),
	        sprintf(
		        '<a href="%s">%s</a>',
		        esc_url( get_site_url() ),
		        esc_html( get_bloginfo( 'name' ) )
	        )
        );
        ?>
	</span>
</p>
