<?php if ( $wrap ) {
	?><table><?php } ?>
	<?php foreach ( $fields as $field ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $field contains safe HTML form elements required for UI rendering
		echo $field;
		?>
	<?php endforeach ?>
<?php
if ( $wrap ) {
	?>
	</table><?php } ?>