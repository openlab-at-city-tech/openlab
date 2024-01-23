<form rel="<?php echo esc_attr( $display_type_name ); ?>"
		class="<?php echo esc_attr( $css_class ); ?>"
		method='POST'
		action='<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>'
		data-defaults="<?php print esc_attr( json_encode( $defaults ) ); ?>">
	<?php echo $settings; ?>
</form>