<?php declare( strict_types=1 );
/**
 * @var Field $field The Field object.
 * @var Resource $resource The Field resource.
 * @var string $group The group name.
 */

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Admin\Fields\Field;
use TEC\Common\StellarWP\Uplink\Resources\Resource;
use TEC\Common\StellarWP\Uplink\Components\Admin\Authorize_Button_Controller;
?>

<?php
/**
 * Fires before the field.
 *
 * @since 2.0.0
 *
 * @param string $slug The slug of the field.
 */
do_action( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/license_field_before_field', $field->get_slug() );
?>
<?php if ( $field->should_show_label() ) : ?>
	<table class="form-table" role="presentation">
	<tr class="stellarwp-uplink-license-key-field">
	<th scope="row">
		<label for="<?php echo esc_attr( $field->get_field_id() ); ?>"><?php echo esc_html( $field->get_label() ); ?></label>
	</th>
	<td>
<?php endif; ?>
	<div class="stellarwp-uplink__license-field">
		<div
			class="<?php echo esc_attr( $field->get_classes() ); ?>"
			id="<?php echo esc_attr( $field->get_product() ); ?>"
			data-slug="<?php echo esc_attr( $field->get_product() ); ?>"
			data-plugin="<?php echo esc_attr( $field->get_product() ); ?>"
			data-plugin-slug="<?php echo esc_attr( $field->get_product_slug() ); ?>"
			data-action="<?php echo esc_attr( $field->get_nonce_action() ); ?>"
		>
			<fieldset class="stellarwp-uplink__settings-group">
				<?php settings_fields( $group ); ?>

				<?php
				/**
				 * Fires before the license key input is printed on the page.
				 *
				 * @since 2.2.0
				 *
				 * @param string $slug The slug of the field.
				 */
				do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_field_before_input', $field->get_slug() );
				?>

				<input
					type="text"
					name="<?php echo esc_attr( $field->get_field_name() ); ?>"
					value="<?php echo esc_attr( $field->get_field_value() ); ?>"
					placeholder="<?php echo esc_attr( $field->get_placeholder() ); ?>"
					class="regular-text stellarwp-uplink__settings-field"
				/>
				<?php if ( $resource->is_using_oauth() ) : ?>
					<?php
					try {
						Config::get_container()->get( Authorize_Button_Controller::class )->render( [
							'slug' => $field->get_slug(),
							'domain' => get_site_url(),
							'license' => $resource->license_key ?? '',
						] );
					} catch ( Throwable $e ) {
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log( "Unable to render authorize button: {$e->getMessage()} {$e->getFile()}:{$e->getLine()} {$e->getTraceAsString()}" );
						}
					}
					?>
				<?php endif; ?>
				<?php echo $field->get_key_status_html(); ?>
			</fieldset>
			<?php echo $field->get_nonce_field(); ?>
		</div>
	</div>
<?php if ( $field->should_show_label() ) : ?>
	</td>
	</tr>
	</table>
<?php endif; ?>
<?php
/**
 * Fires after the field.
 *
 * @since 2.0.0
 */
do_action( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/license_field_after_field', $field->get_slug() );
