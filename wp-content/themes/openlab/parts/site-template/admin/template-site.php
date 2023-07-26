<?php
if ( $site_id ) {
	$option_name = sprintf(
		// translators: 1. Numeric ID of site, 2. Name of site, 3. URL of site
		__( '#%1$s %2$s (%3$s)', 'commons-in-a-box' ),
		$site_id,
		$site_name,
		$site_url
	);
} else {
	$option_name = '';
}

?>

<label class="screen-reader-text" for="template-site-id"><?php esc_html_e( 'Template Site', 'commons-in-a-box' ); ?></label>

<p class="description">
	<?php esc_html_e( 'Below you can select an existing site whose settings and content will be copied to new sites when using this template.', 'commons-in-a-box' ); ?>&nbsp;

	<?php if ( $is_create ) : ?>
		<?php esc_html_e( 'If no site is selected, a new site will be created automatically to serve as the template.', 'commons-in-a-box' ); ?>
	<?php endif; ?>
</p>

<select class="widefat" name="template-site-id" id="template-site-id" required>
	<option value="<?php echo esc_attr( $site_id ); ?>" selected="selected"><?php echo esc_html( $option_name ); ?></option>
</select>

<p><?php esc_html_e( 'To search, click into the dropdown and begin typing the name or URL of the desired site.', 'commons-in-a-box' ); ?></p>

<?php if ( $site_id ) : ?>
	<p>
		<?php
		echo sprintf(
			// translators: 1. Name of currently selected Template Site, 2. View link for the site, 3. Dashboard link for the site
			esc_html__( 'The currently selected Template Site is: %1$s - %2$s | %3$s', 'commons-in-a-box' ),
			'<strong>' . esc_html( $site_name ) . '</strong>',
			sprintf( '<a href="%s">%s</a>', esc_url( $site_url ), esc_html__( 'View', 'commons-in-a-box' ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( get_admin_url( $site_id ) ), esc_html__( 'Dashboard', 'commons-in-a-box' ) )
		);
		?>
	</p>
<?php endif; ?>

<?php wp_nonce_field( 'cboxol-template-site', 'cboxol-template-site-nonce', false ); ?>
