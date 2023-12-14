<p>
	<label for='<?php echo $self->get_field_id( 'title' ); ?>'>
		<?php _e( 'Title', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo $self->get_field_id( 'title' ); ?>'
				name='<?php echo $self->get_field_name( 'title' ); ?>'
				type='text'
				value='<?php echo $title; ?>'/>
	</label>
</p>

<p>
	<label for='<?php echo $self->get_field_id( 'show_icon' ); ?>'>
		<input id='<?php echo $self->get_field_id( 'show_icon' ); ?>'
				name='<?php echo $self->get_field_name( 'show_icon' ); ?>'
				type='checkbox'
				value='1'
				<?php checked( true, $instance['show_icon'] ); ?>/>
		<?php _e( 'Show Media RSS icon', 'nggallery' ); ?>
	</label>
</p>

<p>
	<label for='<?php echo $self->get_field_id( 'show_global_mrss' ); ?>'>
		<input id='<?php echo $self->get_field_id( 'show_global_mrss' ); ?>'
				name='<?php echo $self->get_field_name( 'show_global_mrss' ); ?>'
				type='checkbox'
				value='1'
				<?php checked( true, $instance['show_global_mrss'] ); ?>/>
		<?php _e( 'Show the Media RSS link', 'nggallery' ); ?>
	</label>
</p>

<p>
	<label for='<?php echo $self->get_field_id( 'mrss_text' ); ?>'>
		<?php _e( 'Text for Media RSS link', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo $self->get_field_id( 'mrss_text' ); ?>'
				name='<?php echo $self->get_field_name( 'mrss_text' ); ?>'
				type='text'
				value='<?php echo $mrss_text; ?>'/>
	</label>
</p>

<p>
	<label for='<?php echo $self->get_field_id( 'mrss_title' ); ?>'>
		<?php _e( 'Tooltip text for Media RSS link', 'nggallery' ); ?>:<br/>
		<input class='widefat'
				id='<?php echo $self->get_field_id( 'mrss_title' ); ?>'
				name='<?php echo $self->get_field_name( 'mrss_title' ); ?>'
				type='text'
				value='<?php echo $mrss_title; ?>'/>
	</label>
</p>
