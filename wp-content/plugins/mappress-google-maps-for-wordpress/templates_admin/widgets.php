<script type='text/html' id='mapp-tmpl-color-picker'>
    <# var colors = ['#F4EB37','#CDDC39','#62AF44','#009D57','#0BA9CC','#4186F0','#3F5BA9','#7C3592','#A61B4A','#DB4436','#F8971B','#F4B400','#795046','#F9F7A6','#E6EEA3','#B7DBAB','#7CCFA9','#93D7E8','#9FC3FF','#A7B5D7','#C6A4CF','#D698AD','#EE9C96','#FAD199','#FFDD5E','#B29189','#FFFFFF','#CCCCCC','#777777','#000000']; #>
    <div class='mapp-colorpicker' tabindex='0'>
        <# _.each(colors, function(color, i) { #>
            <span data-mapp-color='{{color}}' class='mapp-color' style='background-color: {{color}}' tabindex='0'></span>
        <# }); #>
        <?php _e('Opacity', 'mappress-google-maps-for-wordpress'); ?>
        <select class='mapp-opacity'>
            <# for (var i = 100; i >= 0; i -= 10) { #>
                <option value='{{i}}'>{{i}}</option>
            <# } #>
        </select>
        <?php _e('Weight', 'mappress-google-maps-for-wordpress'); ?>
        <select class='mapp-weight'>
            <# for (var i = 1; i <= 20; i++) { #>
                <option value='{{i}}'>{{i}}</option>
            <# } #>
        </select>
    </div>
</script>

<script type='text/html' id='mapp-tmpl-icon-editor'>
    <div class='mapp-icon-editor'>
        <div class='mapp-icon-editor-wrapper'>
            <ul class='mapp-icon-editor-list'>
                <# if (!mappl10n.options.userIcons.length) { #>
                    <h2 class='mapp-icon-editor-msg'>
                        <?php _e('No custom icons have been added yet.', 'mappress-google-maps-for-wordpress');?>
                    </h2>
                <# } #>
                <# _.each(mappl10n.options.userIcons, function(icon, i) { #>
                    <li data-mapp-iconid='{{icon}}'>
                        <span><img class='mapp-icon' title='{{icon}}' alt='{{icon}}' src='{{mappl10n.options.iconsUrl}}/{{icon}}'></span>
                        <span>{{icon}}</span>
                        <span data-mapp-action='delete' class='dashicons dashicons-trash'></span>
                    </li>
                <# }); #>
            </ul>
        </div>
        <div class='mapp-icon-editor-toolbar'>
            <input type='file' multiple name='mapp-icon-files' class='mapp-icon-files'>
            <button data-mapp-action='add' class='button mapp-icon-add'><?php _e('Add', 'mappress-google-maps-for-wordpress');?></button>
        </div>
    </div>
</script>

<script type='text/html' id='mapp-tmpl-icon-picker'>
    <div class='mapp-iconpicker' tabindex='0'>
        <div class='mapp-iconpicker-wrapper'>
            <# _.each(mappl10n.options.userIcons, function(iconid, i) { #>
                <img class='mapp-icon' data-mapp-iconid='{{iconid}}' src='{{mappl10n.options.iconsUrl}}{{iconid}}' alt='iconid' title='iconid'>
            <# }); #>
            <br/>
            <# _.each(mappl10n.options.standardIcons, function(iconid, i) { #>
                <span data-mapp-iconid='{{iconid}}' class='mapp-icon-sprite' style='background-position: <# print(i * -24) #>px 0px' alt='{{iconid}}' title='{{iconid}}'></span>
            <# }); #>
        </div>
        <div class='mapp-iconpicker-toolbar'>
                <input class='button' data-mapp-iconid='' type='button' value='<?php _e('Use default icon', 'mappress-google-maps-for-wordpress');?>'>
            </div>
        </div>
    </div>
</script>

<script type='text/html' id='mapp-tmpl-tp-master'>
	<div class='mapp-tp-editor'>
		<?php echo Mappress_Controls::select('', array('map-popup' => __('Map popup', 'mappress-google-maps-for-wordpress'), 'map-item' => __('Map list item', 'mappress-google-maps-for-wordpress'), 'mashup-popup' => __('Mashup popup', 'mappress-google-maps-for-wordpress'), 'mashup-item' => __('Mashup list item', 'mappress-google-maps-for-wordpress')),  'map-tmpl-poi', array('class' => 'mapp-tp-select')); ?>
		<?php echo Mappress_Controls::button('', __('Edit', 'mappress-google-maps-for-wordpress'), array('class' => 'button button-primary', 'data-mapp-action' => 'edit')); ?>
	</div>
</script>

<script type='text/html' id='mapp-tmpl-tp-detail'>
	<div class='mapp-tp-detail'>
		<div class='mapp-tp-name'>{{ model.name }} <# if (!model.exists) { #>(<?php _e('New', 'mappress-google-maps-for-wordpress');?>)<# } else { #>(<?php _e('Custom', 'mappress-google-maps-for-wordpress');?>)<# } #></div>
		<div class='mapp-tp-path'>{{ model.path }}</div>
		<div class='mapp-tabs'>
			<div class='mapp-tab-label mapp-active' class='mapp-active'><?php _e('Editor', 'mappress-google-maps-for-wordpress');?></div>
			<div class='mapp-tab-label'><?php _e('Default', 'mappress-google-maps-for-wordpress');?></div>
			<div class='mapp-tab mapp-tab-content mapp-active'>
				<# _.each(tokens, function (token, i) { #>
					<code data-mapp-action='insert' data-mapp-insert='{{{i}}}' title='{{{i}}}'>{{{token}}}</code>
				<# }); #>
				<hr/>
				<textarea class='mapp-tp-content'>{{{ model.content }}}</textarea>
			</div>
			<div class='mapp-tab mapp-tab-standard'>
				<textarea readonly class='mapp-tp-standard'>{{{ model.standard }}}</textarea>
			</div>
		</div>
		<div class='mapp-tp-toolbar'>
			<input type='button' class='button button-primary' data-mapp-action='save' value='<?php _e('Save', 'mappress-google-maps-for-wordpress');?>'>
			<input type='button' class='button' data-mapp-action='cancel' value='<?php _e('Cancel', 'mappress-google-maps-for-wordpress');?>'>
			<# if (model.exists) { #>
				<a href='#' data-mapp-action='destroy'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
			<# } #>
		</div>
	</div>
</script>
