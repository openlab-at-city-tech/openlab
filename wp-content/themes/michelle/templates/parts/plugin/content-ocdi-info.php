<?php
/**
 * One Click Demo Import integration: Info content.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="theme-demo-info">

	<h2><?php esc_html_e( 'Manual import procedure', 'michelle' ); ?></h2>

	<p>
		<?php esc_html_e( 'By importing this demo content you get the exact copy of the theme demo website.', 'michelle' ); ?>
		(<a href="https://themedemos.webmandesign.eu/michelle/"><?php esc_html_e( 'Preview the theme demo website &raquo;', 'michelle' ); ?></a>)
		<br>

		<?php esc_html_e( 'For instructions on importing theme demo content please visit GitHub repository.', 'michelle' ); ?>
		(<a href="https://github.com/webmandesign/demo-content/blob/master/michelle/readme.md#what-is-this"><?php esc_html_e( 'GitHub repository instructions &raquo;', 'michelle' ); ?></a>)
	</p>

</div>

<div class="media-files-quality-info">
	<h3><?php esc_html_e( 'Media files quality', 'michelle' ); ?></h3>

	<p>
		<?php esc_html_e( 'Please note that imported media files (such as images, video and audio files) are of low quality to prevent copyright infringement.', 'michelle' ); ?>
		<?php esc_html_e( 'Please read "Credits" section of theme documentation for reference where the demo media files were obtained from.', 'michelle' ); ?>

		<a href="https://webmandesign.github.io/docs/michelle/#credits"><?php
			esc_html_e( 'Get media for your website &raquo;', 'michelle' );
		?></a>
	</p>
</div>

<div class="ocdi__demo-import-notice">
	<h3><?php esc_html_e( 'Install demo required plugins!', 'michelle' ); ?></h3>

	<p>
		<?php esc_html_e( 'Please read the information about the theme demo required plugins first.', 'michelle' ); ?>
		<?php esc_html_e( 'If you do not install and activate the demo required plugins, some of the content will not be imported.', 'michelle' ); ?>

		<a href="https://github.com/webmandesign/demo-content/blob/master/michelle/readme.md#required-plugins" title="<?php esc_attr_e( 'Read the information before you run the theme demo content import process.', 'michelle' ); ?>"><strong><?php
			esc_html_e( 'View the list of required plugins &raquo;', 'michelle' );
		?></strong></a>
	</p>

	<p><em>
		<?php esc_html_e( '(Note that this set of plugins may differ from plugins recommended under Appearance &rarr; Install Plugins!)', 'michelle' ); ?>
	</em></p>
</div>
