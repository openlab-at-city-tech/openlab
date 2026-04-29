<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'title' ) ); ?>'>
		<?php esc_html_e( 'Title', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo esc_attr( $self->get_field_id( 'title' ) ); ?>'
				name='<?php echo esc_attr( $self->get_field_name( 'title' ) ); ?>'
				type='text'
				value='<?php echo esc_attr( $title ); ?>'/>
	</label>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'show_icon' ) ); ?>'>
		<input id='<?php echo esc_attr( $self->get_field_id( 'show_icon' ) ); ?>'
				name='<?php echo esc_attr( $self->get_field_name( 'show_icon' ) ); ?>'
				type='checkbox'
				value='1'
				<?php checked( true, $instance['show_icon'] ); ?>/>
		<?php esc_html_e( 'Show Media RSS icon', 'nggallery' ); ?>
	</label>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'show_global_mrss' ) ); ?>'>
		<input id='<?php echo esc_attr( $self->get_field_id( 'show_global_mrss' ) ); ?>'
				name='<?php echo esc_attr( $self->get_field_name( 'show_global_mrss' ) ); ?>'
				type='checkbox'
				value='1'
				<?php checked( true, $instance['show_global_mrss'] ); ?>/>
		<?php esc_html_e( 'Show the Media RSS link', 'nggallery' ); ?>
	</label>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'mrss_text' ) ); ?>'>
		<?php esc_html_e( 'Text for Media RSS link', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo esc_attr( $self->get_field_id( 'mrss_text' ) ); ?>'
				name='<?php echo esc_attr( $self->get_field_name( 'mrss_text' ) ); ?>'
				type='text'
				value='<?php echo esc_attr( $mrss_text ); ?>'/>
	</label>
</p>

<p>
	<label for='<?php echo esc_attr( $self->get_field_id( 'mrss_title' ) ); ?>'>
		<?php esc_html_e( 'Tooltip text for Media RSS link', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo esc_attr( $self->get_field_id( 'mrss_title' ) ); ?>'
				name='<?php echo esc_attr( $self->get_field_name( 'mrss_title' ) ); ?>'
				type='text'
				value='<?php echo esc_attr( $mrss_title ); ?>'/>
	</label>
</p>
