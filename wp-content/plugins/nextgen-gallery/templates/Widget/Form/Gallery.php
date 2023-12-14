<!-- title -->
<p>
	<label for='<?php echo $self->get_field_id( 'title' ); ?>'>
		<?php _e( 'Title', 'nggallery' ); ?>:
		<input id='<?php echo $self->get_field_id( 'title' ); ?>'
				name='<?php echo $self->get_field_name( 'title' ); ?>'
				type='text'
				class='widefat'
				value='<?php echo $title; ?>'/>
	</label>
</p>

<!-- count & source -->
<p>
	<?php _e( 'Show', 'nggallery' ); ?>:<br/>
	<label for='<?php echo $self->get_field_id( 'items' ); ?>'>
		<input style='width: 50px;'
				id='<?php echo $self->get_field_id( 'items' ); ?>'
				name='<?php echo $self->get_field_name( 'items' ); ?>'
				type='text'
				value='<?php echo $items; ?>'/>
	</label>
	<select id='<?php echo $self->get_field_id( 'show' ); ?>'
			name='<?php echo $self->get_field_name( 'show' ); ?>'>
		<option <?php selected( 'thumbnail', $instance['show'] ); ?> value='thumbnail'>
			<?php _e( 'Thumbnails', 'nggallery' ); ?>
		</option>
		<option <?php selected( 'original', $instance['show'] ); ?> value='original'>
			<?php _e( 'Original images', 'nggallery' ); ?>
		</option>
	</select>
</p>

<!-- random or recent -->
<p>
	<label for='<?php echo $self->get_field_id( 'type' ); ?>_random'>
		<input id='<?php echo $self->get_field_id( 'type' ); ?>_random'
				name='<?php echo $self->get_field_name( 'type' ); ?>'
				type='radio'
				value='random'
				<?php checked( 'random', $instance['type'] ); ?>/>
		<?php _e( 'random', 'nggallery' ); ?>
	</label>
	<label for='<?php echo $self->get_field_id( 'type' ); ?>_recent'>
		<input id='<?php echo $self->get_field_id( 'type' ); ?>_recent'
				name='<?php echo $self->get_field_name( 'type' ); ?>'
				type='radio'
				value='recent'
				<?php checked( 'recent', $instance['type'] ); ?>/>
		<?php _e( 'recently added', 'nggallery' ); ?>
	</label>
</p>

<!-- IE8 web slices -->
<p>
	<label for='<?php echo $self->get_field_id( 'webslice' ); ?>'>
		<input id='<?php echo $self->get_field_id( 'webslice' ); ?>'
				name='<?php echo $self->get_field_name( 'webslice' ); ?>'
				type='checkbox'
				value='1'
				<?php checked( true, $instance['webslice'] ); ?>/>
		<?php _e( 'Enable IE8 Web Slices', 'nggallery' ); ?>
	</label>
</p>

<!-- dimensions -->
<p>
	<?php _e( 'Width x Height', 'nggallery' ); ?>:<br/>
	<?php
	$thumbnails_template_height_value = $height;
	$thumbnails_template_width_value  = $width;
	$thumbnails_template_height_id    = $self->get_field_id( 'height' );
	$thumbnails_template_width_id     = $self->get_field_id( 'width' );
	$thumbnails_template_height_name  = $self->get_field_name( 'height' );
	$thumbnails_template_width_name   = $self->get_field_name( 'width' );
	require implode(
		DIRECTORY_SEPARATOR,
		[
			rtrim( NGGALLERY_ABSPATH, '/\\' ),
			'admin',
			'thumbnails-template.php',
		]
	);
	?>
</p>

<!-- which galleries -->
<p>
	<label for='<?php echo $self->get_field_id( 'exclude' ); ?>'>
		<?php _e( 'Select', 'nggallery' ); ?>:
		<select id='<?php echo $self->get_field_id( 'exclude' ); ?>'
				name='<?php echo $self->get_field_name( 'exclude' ); ?>'
				class='widefat'>
			<option <?php selected( 'all', $instance['exclude'] ); ?>  value='all'>
				<?php _e( 'All galleries', 'nggallery' ); ?>
			</option>
			<option <?php selected( 'denied', $instance['exclude'] ); ?> value='denied'>
				<?php _e( 'Only which are not listed', 'nggallery' ); ?>
			</option>
			<option <?php selected( 'allow', $instance['exclude'] ); ?>  value='allow'>
				<?php _e( 'Only which are listed', 'nggallery' ); ?>
			</option>
		</select>
	</label>
</p>

<!-- gallery ids -->
<p>
	<label for='<?php echo $self->get_field_id( 'list' ); ?>'>
		<?php _e( 'Gallery ID', 'nggallery' ); ?>:
		<input id='<?php echo $self->get_field_id( 'list' ); ?>'
				name='<?php echo $self->get_field_name( 'list' ); ?>'
				type='text' class='widefat'
				value='<?php echo $instance['list']; ?>'/>
		<br/>
		<small>
			<?php _e( 'Gallery IDs, separated by commas.', 'nggallery' ); ?>
		</small>
	</label>
</p>
