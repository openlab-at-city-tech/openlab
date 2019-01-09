<?php defined( 'ABSPATH' ) or exit; ?>

<textarea name="<?php echo esc_attr( $data['id'] ); ?>" id="<?php echo esc_attr( $data['id'] ); ?>" cols="50" rows="15" class="large-text"><?php echo esc_textarea( get_option( $data['id'] ) ); ?></textarea>

<p class="description"><?php echo $data['description']; ?></p>

<h4 class="title"><?php _e( 'Available variables', 'shortcodes-ultimate' ); ?></h4>
<table class="widefat striped" style="width:auto">
	<thead>
		<tr>
			<td><?php esc_html_e( 'Variable', 'shortcodes-ultimate' ); ?></td>
			<td><?php esc_html_e( 'Will be replaced with', 'shortcodes-ultimate' ); ?>&hellip;</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><code contenteditable>%home_url%</code></td>
			<td><?php printf( '%s (%s)', __( 'the URL of the site home page', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></td>
		</tr>
		<tr>
			<td><code contenteditable>%theme_url%</code></td>
			<td><?php printf( '%s (%s)', __( 'the URL of the directory of the current theme', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></td>
		</tr>
		<tr>
			<td><code contenteditable>%plugin_url%</code></td>
			<td><?php printf( '%s (%s)', __( 'the URL of the directory of the plugin', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></td>
		</tr>
	</tbody>
</table>

<h4 class="title"><?php _e( 'More information', 'shortcodes-ultimate' ); ?></h4>

<ul class="ul-disc">
	<?php // Translators: %s - link to the shortcodes.css file ?>
	<li><?php printf( __( 'Open %s file to see default styles', 'shortcodes-ultimate' ), '<a href="' . $this->plugin_url . 'includes/css/shortcodes.css" target="_blank">shortcodes.css</a>' ); ?></li>
	<li><?php esc_html_e( 'Help article', 'shortcodes-ultimate' ); ?>: <a href="http://docs.getshortcodes.com/article/33-custom-css-editor" target="_blank"><?php esc_html_e( 'How to use Custom CSS editor', 'shortcodes-ultimate' ); ?></a></li>
</ul>
