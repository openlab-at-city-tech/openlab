<div class="attr-input attr-input-radio ekit-admin-input-radio <?php echo esc_attr( $class ); ?>">
	<div class="ekit-admin-input-switch ekit-admin-card-shadow attr-card-body">
		<input <?php echo esc_attr( $options['checked'] === true ? 'checked' : '' ); ?> 
			type="radio" value="<?php echo esc_attr( $value ); ?>" 
			class="ekit-admin-control-input" 
			name="<?php echo esc_attr( $name ); ?>" 
			id="ekit-admin-radio__<?php echo esc_attr( self::strify( $name ) . $value ); ?>"

			<?php 
			if ( isset( $attr ) ) {
				foreach ( $attr as $k => $v ) {
					echo esc_attr($k) .'='. esc_attr($v);
				}
			}
			?>
		>

		<label class="ekit-admin-control-label"  for="ekit-admin-radio__<?php echo esc_attr( self::strify( $name ) . $value ); ?>">
			<?php echo esc_html( $label ); ?>
			<?php if ( ! empty( $description ) ) : ?>
				<span class="ekit-admin-control-desc"><?php echo esc_html( $description ); ?></span>
			<?php endif; ?>
		</label>
	</div>
</div>
