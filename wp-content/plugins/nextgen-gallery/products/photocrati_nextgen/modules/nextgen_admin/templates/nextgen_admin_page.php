<?php
/**
 * Template for NextGEN admin page.
 *
 * @var array $errors
 * @var array $forms
 * @var string $logo
 * @var bool $show_save_button
 * @var bool $success
 * @var string $header_message
 * @var string $page_heading
 * @var string $url
 */

if ( $errors ) {
	foreach ( $errors as $msg ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $msg contains safe HTML for error messages
		print $msg;
	}
}
if ( $success && empty( $errors ) ) { ?>
	<div class='success updated'>
		<p><?php echo esc_html( $success ); ?></p>
	</div>
<?php } ?>

<div class="wrap ngg_settings_page"
	id='ngg_page_content'
	style='position: relative; visibility: hidden;'>

	<div class="ngg_page_content_header">
		<h3><?php echo esc_html( $page_heading ); ?></h3>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $header_message contains safe HTML for admin page header
		echo $header_message;
		?>
	</div>

	<form method="POST"
			action="
			<?php
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Value is properly unslashed and sanitized via sanitize_text_field(wp_unslash()) on next line
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
			echo \Imagely\NGG\Util\Router::esc_url( sanitize_text_field( wp_unslash( $request_uri ) ) );
			?>
			">
		<?php
		if ( isset( $form_header ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $form_header contains safe HTML for form header
			print $form_header . "\n";
		}

		// Nonce is output below in the hidden input field.
		?>
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>"/>

		<input type="hidden" name="action"/>

		<div class="ngg_page_content_menu">
			<?php foreach ( $forms as $form ) { ?>
				<a href='javascript:void(0)'
					data-id='<?php echo esc_attr( $form->get_id() ); ?>'>
					<?php echo esc_html( str_replace( [ 'NextGEN ', 'NextGen ' ], '', $form->get_title() ) ); ?>
				</a>
			<?php } ?>
		</div>

		<div class="ngg_page_content_main">
			<?php foreach ( $forms as $form ) { ?>
				<div data-id='<?php echo esc_attr( $form->get_id() ); ?>'>
					<?php $this->start_element( 'admin_page.content_main_form', 'container', $form ); ?>
						<h3><?php print esc_html( str_replace( [ 'NextGEN ', 'NextGen ' ], '', $form->get_title() ) ); ?></h3>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $form->render() returns safe HTML for form rendering
						echo $form->render( true );
						?>
					<?php $this->end_element(); ?>
				</div>
			<?php } ?>
		</div>

		<?php if ( $show_save_button ) { ?>
			<p>
				<button type="submit"
						name='action_proxy'
						data-proxy-value="save"
						value="Save"
						class="button-primary ngg_save_settings_button">
					<?php esc_html_e( 'Save Options', 'nggallery' ); ?>
				</button>
				<input type="hidden" name="ngg_errors_in_tab" id="ngg_errors_in_tab">
			</p>
		<?php } ?>
	</form>
</div>
