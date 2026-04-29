<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'title' ) ); ?>'>
		<?php esc_html_e( 'Title', 'nggallery' ); ?>:
	</label>

	<input class='widefat'
			id='<?php echo esc_attr( $self->get_field_id( 'title' ) ); ?>'
			name='<?php echo esc_attr( $self->get_field_name( 'title' ) ); ?>'
			type='text'
			value='<?php echo esc_attr( $title ); ?>'/>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'galleryid' ) ); ?>'>
		<?php esc_html_e( 'Select Gallery', 'nggallery' ); ?>:
	</label>

	<select size='1'
			name='<?php echo esc_attr( $self->get_field_name( 'galleryid' ) ); ?>'
			id='<?php echo esc_attr( $self->get_field_id( 'galleryid' ) ); ?>'
			class='widefat'>
		<option value='0' 
		<?php
		if ( 0 === $instance['galleryid'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe hardcoded HTML attribute
			echo 'selected="selected" ';}
		?>
		>
			<?php esc_html_e( 'All images', 'nggallery' ); ?>
		</option>
		<?php
		if ( $tables ) {
			foreach ( $tables as $table ) {
				echo '<option value="' . esc_attr( $table->gid ) . '" ';
				if ( $table->gid === $instance['galleryid'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe hardcoded HTML attribute
					echo 'selected="selected" ';
				}
				echo '>' . esc_html( $table->title ) . '</option>';
			}
		}
		?>
	</select>
</p>

<p id ='<?php echo esc_attr( $self->get_field_id( 'limit' ) ); ?>_container' 
					<?php
					if ( 0 !== $instance['galleryid'] ) {
						?>
	style="display: none;" <?php } ?>>
	<label for='<?php echo esc_attr( $self->get_field_id( 'limit' ) ); ?>'>
		<?php esc_html_e( 'Limit', 'nggallery' ); ?>:
	</label>
	<input id='<?php echo esc_attr( $self->get_field_id( 'limit' ) ); ?>'
			name='<?php echo esc_attr( $self->get_field_name( 'limit' ) ); ?>'
			type='number'
			min='0'
			step='1'
			style="padding: 3px; width: 45px;"
			value="<?php echo esc_attr( $limit ); ?>"/>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'height' ) ); ?>'>
		<?php esc_html_e( 'Height', 'nggallery' ); ?>:
	</label>

	<input id='<?php echo esc_attr( $self->get_field_id( 'height' ) ); ?>'
			name='<?php echo esc_attr( $self->get_field_name( 'height' ) ); ?>'
			type='text'
			style='padding: 3px; width: 45px;'
			value='<?php echo esc_attr( $height ); ?>'/>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'width' ) ); ?>'>
		<?php esc_html_e( 'Width', 'nggallery' ); ?>:
	</label>

	<input id='<?php echo esc_attr( $self->get_field_id( 'width' ) ); ?>'
			name='<?php echo esc_attr( $self->get_field_name( 'width' ) ); ?>'
			type='text'
			style='padding: 3px; width: 45px;'
			value='<?php echo esc_attr( $width ); ?>'/>
</p>

<!-- only show the limit field when 'all images' is selected -->
<script type="text/javascript">
	(function($) {
		$('#<?php echo esc_attr( $self->get_field_id( 'galleryid' ) ); ?>').on('change', function() {
			if ($(this).val() == 0) {
				$('#<?php echo esc_attr( $self->get_field_id( 'limit' ) ); ?>_container').show();
			} else {
				$('#<?php echo esc_attr( $self->get_field_id( 'limit' ) ); ?>_container').hide();
			}
		});
	})(jQuery);
</script>