<div class="form-group ekit-admin-input-text ekit-admin-input-text-<?php echo esc_attr( self::strify( $name ) ); ?>">
	<label for="ekit-admin-option-text<?php echo esc_attr( self::strify( $name ) ); ?>">
		<?php echo esc_html( $label ); ?>
	</label>
	<input
		type="date"
		class="attr-form-control"
		id="ekit-admin-option-text<?php echo esc_attr( self::strify( $name ) ); ?>"
		aria-describedby="ekit-admin-option-text-help<?php echo esc_attr( self::strify( $name ) ); ?>"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		<?php echo esc_attr( $disabled ); ?>
	>
	<small id="ekit-admin-option-text-help<?php echo esc_attr( self::strify( $name ) ); ?>" class="form-text text-muted"><?php echo esc_html( $info ); ?></small>
</div>
