<?php
	// Polygon values
	$weights = array();
	for ($i = 1; $i <= 20; $i++)
		$weights[$i] = $i . "px";

	$opacities = array();
	for ($i = 100; $i >= 0; $i-= 10)
		$opacities[$i] = $i . "%";
?>

<div id='mapp_e_infobox' class='mapp-e-infobox'>
	<div id='mapp_e_poi_fields'>
		<div>
			<input id='mapp_e_poi_title' type='text' />
			<input id='mapp_e_poi_iconid' type='hidden' />
		</div>

		<div id='mapp_e_poi_poly_fields' style='display: none;'>
			<?php _e('Color', 'mappress-google-maps-for-wordpress'); ?>:
			<span id='mapp_e_poi_color' class='mapp-colorpicker-toggle' tabindex="0"></span>
			<?php _e('Opacity', 'mappress-google-maps-for-wordpress'); ?>:
			<?php echo Mappress_Settings::dropdown($opacities, '', '', array('id' => 'mapp_e_poi_opacity', 'title' => __('Opacity', 'mappress-google-maps-for-wordpress')) ); ?>
			<?php _e('Line', 'mappress-google-maps-for-wordpress'); ?>:
			<?php echo Mappress_Settings::dropdown($weights, '', '', array('id' => 'mapp_e_poi_weight', 'title' => __('Weight', 'mappress-google-maps-for-wordpress')) ); ?>
		</div>

		<div id='mapp_e_poi_kml_fields' style='display: none'>
			<input id='mapp_e_poi_kml_url' type='text' readonly='readonly'/>
		</div>

		<div>
			<a id="mapp_e_visual"><?php _e('Visual', 'mappress-google-maps-for-wordpress'); ?></a> | <a id="mapp_e_html"><?php _e('HTML', 'mappress-google-maps-for-wordpress');?></a>
			<textarea id='mapp_e_poi_body' class='mapp-e-poi-body' rows='10'></textarea>
		</div>

		<div>
			<input id='mapp_e_save_poi' class='button button-primary' type='button' value='<?php esc_attr_e('Save', 'mappress-google-maps-for-wordpress'); ?>' />
			<input id='mapp_e_cancel_poi' class='button' type='button' value='<?php esc_attr_e('Cancel', 'mappress-google-maps-for-wordpress'); ?>' />
		</div>
	</div>
</div>