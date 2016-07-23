
<div class='mapp-table mapp-searchbox'>
	<div>
		<div class='mapp-max'>
			<input style='width:100%' type='text' id='mapp_e_saddr' placeholder='<?php _e('Add POI', 'mappress-google-maps-for-wordpress'); ?>'/>
		</div>
		<div>
			<button id='mapp_e_search' class='button button-primary mapp-search-button'></button>
		</div>
		<div>
			<button id='mapp_e_myloc' class='button mapp-geolocate-button' title='<?php _e('My location', 'mappress-google-maps-for-wordpress');?>'></button>
		</div>
	</div>
</div>
<div id='mapp_e_saddr_err' style='display:none'></div>

<div class='mapp-e-edit-panel'>
	<table class='mapp-e-editor'>
		<tr>
			<td style='width: 25%'>
				<div id='mapp_e_poi_list' class='mapp-e-poi-list'></div>
			</td>
			<td style='width: 75%'>
				<div id='mapp_e_top_toolbar' class='mapp-e-top-toolbar'>
					<a href='#' id='mapp_e_recenter'><?php _e('Center map', 'mappress-google-maps-for-wordpress'); ?></a> |
					<?php _e('Click map for lat/lng: ', 'mappress-google-maps-for-wordpress'); ?><span id='mapp_e_latlng'>0,0</span>
				</div>
				<div id='mapp_edit' class='mapp-e-canvas'></div>
			</td>
		</tr>
	</table>
</div>

<?php require Mappress::$basedir . "/forms/map_editor_infobox.php"; ?>