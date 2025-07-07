<?php declare( strict_types=1 );
/**
 * @var Form $form The Form object.
 */

use TEC\Common\StellarWP\Uplink\Admin\Fields\Field;
use TEC\Common\StellarWP\Uplink\Admin\Fields\Form;
use TEC\Common\StellarWP\Uplink\Config;

if ( empty( $form->get_fields() ) ) {
	return;
}

$action_postfix = Config::get_hook_prefix_underscored();

?>
<div class="stellarwp-uplink" data-js="stellarwp-uplink">
	<div class="stellarwp-uplink__settings">
		<?php
		/**
		 * Fires before the form.
		 *
		 * @since 2.0.0
		 */
		do_action( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/license_field_before_form' );
		?>
		<form method="post" action="options.php">
			<?php foreach ( $form->get_fields() as $field ) : ?>
				<?php $field->render(); ?>
			<?php endforeach; ?>
			<?php if ( $form->should_show_button() ) : ?>
				<?php submit_button( $form->get_button_text() );?>
			<?php endif; ?>
		</form>
		<?php
		/**
		 * Fires after the form.
		 *
		 * @since 2.0.0
		 */
		do_action( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/license_field_after_form' );
		?>
	</div>
</div>
<?php
