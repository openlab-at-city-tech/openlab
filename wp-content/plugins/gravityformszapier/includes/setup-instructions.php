<?php
/**
 * Zapier Connection Setup Instructions
 *
 * @since 4.0
 *
 * @package Gravity_Forms_Zapier
 */

?>
<style>
	.settings_api_instructions{
		padding: 20px;
		margin-top: 10px;
		margin-bottom: 5px;
	}
</style>
<p><?php esc_html_e( 'Connect to Zapier in 3 easy steps.', 'gravityformszapier' ); ?></p>
<ol>
	<li>
		<?php esc_html_e( 'Enable the REST API and create a Gravity Forms API Key.', 'gravityformszapier' ); ?>

		<a href="#" class="gravityformszapier-api-instructions"><?php esc_html_e( 'View Instructions', 'gravityformszapier' ); ?></a>

		<div class="alert_yellow settings_api_instructions" style="display:none;">
			<ol>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing link tag.
						esc_html__( 'Navigate to the %1$sREST API settings page%2$s.', 'gravityformszapier' ),
						'<a href="admin.php?page=gf_settings&subview=gravityformswebapi" target="_blank">',
						'</a>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Check %1$sEnable access to the API%2$s.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Click %1$sAdd Key%2$s under the %1$sAuthentication (API version 2)%2$s section.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Enter a %1$sDescription%2$s to uniquely identify the key.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Select a %1$sUser%2$s account with permissions to view and edit entries.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Select the %1$sRead/Write%2$s permission.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php
					printf(
						// translators: Placeholders represent opening and closing strong tag.
						esc_html__( 'Save the new key and copy the %1$sConsumer Key%2$s and %1$sConsumer Secret%2$s. You will need them to create your Zap.', 'gravityformszapier' ),
						'<strong>',
						'</strong>'
					);
				?></li>
				<li><?php esc_html_e( 'Save the REST API settings.', 'gravityformszapier' ); ?></li>
			</ol>
		</div>
	</li>
	<li><?php esc_html_e( 'Create a form.', 'gravityformszapier' ); ?></li>
	<li><?php
		printf(
			// translators: Placeholders represent opening and closing link tag.
			esc_html__( '%1$sCreate a Zap%2$s on zapier.com.', 'gravityformszapier' ),
			'<a href="https://zapier.com/app/editor" target="_blank">',
			'</a>'
		);
	?></li>
</ol>

<script>
	jQuery( function( $ ) {
		$( '.gravityformszapier-api-instructions' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '.settings_api_instructions' ).slideToggle( 'fast' );
		});
	});
</script>
