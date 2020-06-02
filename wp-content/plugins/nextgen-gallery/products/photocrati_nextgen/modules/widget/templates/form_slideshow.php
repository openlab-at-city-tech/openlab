<p>
    <label for='<?php echo $self->get_field_id('title'); ?>'>
        <?php _e('Title', 'nggallery'); ?>:
    </label>

    <input class='widefat'
           id='<?php echo $self->get_field_id('title'); ?>'
           name='<?php echo $self->get_field_name('title'); ?>'
           type='text'
           value='<?php echo $title; ?>'/>
</p>

<p>
    <label for='<?php echo $self->get_field_id('galleryid'); ?>'>
        <?php _e('Select Gallery', 'nggallery'); ?>:
    </label>

    <select size='1'
            name='<?php echo $self->get_field_name('galleryid'); ?>'
            id='<?php echo $self->get_field_id('galleryid'); ?>'
            class='widefat'>
        <option value='0' <?php if (0 == $instance['galleryid']) echo 'selected="selected" '; ?>>
            <?php _e('All images', 'nggallery'); ?>
        </option>
        <?php
        if ($tables)
        {
            foreach($tables as $table) {
                echo '<option value="' . $table->gid . '" ';
                if ($table->gid == $instance['galleryid'])
                    echo 'selected="selected" ';
                echo '>' . $table->title . '</option>';
            }
        } ?>
    </select>
</p>

<p id ='<?php echo $self->get_field_id('limit'); ?>_container' <?php if (0 != $instance['galleryid']) { ?>style="display: none;" <?php } ?>>
    <label for='<?php echo $self->get_field_id('limit'); ?>'>
        <?php _e('Limit', 'nggallery'); ?>:
    </label>
    <input id='<?php echo $self->get_field_id('limit'); ?>'
           name='<?php echo $self->get_field_name('limit'); ?>'
           type='number'
           min='0'
           step='1'
           style="padding: 3px; width: 45px;"
           value="<?php echo $limit; ?>"/>
</p>

<p>
    <label for='<?php echo $self->get_field_id('height'); ?>'>
        <?php _e('Height', 'nggallery'); ?>:
    </label>

    <input id='<?php echo $self->get_field_id('height'); ?>'
           name='<?php echo $self->get_field_name('height'); ?>'
           type='text'
           style='padding: 3px; width: 45px;'
           value='<?php echo $height; ?>'/>
</p>

<p>
    <label for='<?php echo $self->get_field_id('width'); ?>'>
        <?php _e('Width', 'nggallery'); ?>:
    </label>

    <input id='<?php echo $self->get_field_id('width'); ?>'
           name='<?php echo $self->get_field_name('width'); ?>'
           type='text'
           style='padding: 3px; width: 45px;'
           value='<?php echo $width; ?>'/>
</p>

<!-- only show the limit field when 'all images' is selected -->
<script type="text/javascript">
    (function($) {
        $('#<?php echo $self->get_field_id('galleryid'); ?>').on('change', function() {
            if ($(this).val() == 0) {
                $('#<?php echo $self->get_field_id('limit'); ?>_container').show();
            } else {
                $('#<?php echo $self->get_field_id('limit'); ?>_container').hide();
            }
        });
    })(jQuery);
</script>