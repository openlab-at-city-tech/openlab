<?php

/**
 * HTML code of this widget in the administration section
 * This code is on a separate file to exclude it from the frontend
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

?>
<!-- WIDGETS (Table for the FORM widget) -->
<table id="SZGoogleWidgetMaps" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('widget title','sz-google') ?>"/></td>
</tr>

<tr class="only-widgets"><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify lat and lng) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_lat ?>"><?php echo ucfirst(__('latitude','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_lat ?>" name="<?php echo $NAME_lat ?>" type="text" value="<?php echo $VALUE_lat ?>" placeholder="<?php echo __('enter map latitude','sz-google') ?>"/></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_lng ?>"><?php echo ucfirst(__('longitude','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_lng ?>" name="<?php echo $NAME_lng ?>" type="text" value="<?php echo $VALUE_lng ?>" placeholder="<?php echo __('enter map longitude','sz-google') ?>"/></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the size) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_width ?>"><?php echo ucfirst(__('width','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_width ?>" class="sz-google-checks-width widefat" name="<?php echo $NAME_width ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_width ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_width_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-width" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_width_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_width_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_height ?>"><?php echo ucfirst(__('height','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_height ?>" class="sz-google-checks-height widefat" name="<?php echo $NAME_height ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_height ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_height_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-height" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_height_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_height_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify zoom) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_zoom ?>"><?php echo ucfirst(__('zoom','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_zoom ?>" name="<?php echo $NAME_zoom ?>">
			<option value=""   <?php echo selected(""  ,$VALUE_zoom) ?>><?php echo SZGOOGLE_UPPER(__('default','sz-google')) ?></option>
			<option value="01" <?php echo selected("01",$VALUE_zoom) ?>>1</option>
			<option value="02" <?php echo selected("02",$VALUE_zoom) ?>>2</option>
			<option value="03" <?php echo selected("03",$VALUE_zoom) ?>>3</option>
			<option value="04" <?php echo selected("04",$VALUE_zoom) ?>>4</option>
			<option value="05" <?php echo selected("05",$VALUE_zoom) ?>>5</option>
			<option value="06" <?php echo selected("06",$VALUE_zoom) ?>>6</option>
			<option value="07" <?php echo selected("07",$VALUE_zoom) ?>>7</option>
			<option value="08" <?php echo selected("08",$VALUE_zoom) ?>>8</option>
			<option value="09" <?php echo selected("09",$VALUE_zoom) ?>>9</option>
			<option value="10" <?php echo selected("10",$VALUE_zoom) ?>>10</option>
			<option value="11" <?php echo selected("11",$VALUE_zoom) ?>>11</option>
			<option value="12" <?php echo selected("12",$VALUE_zoom) ?>>12</option>
			<option value="13" <?php echo selected("13",$VALUE_zoom) ?>>13</option>
			<option value="14" <?php echo selected("14",$VALUE_zoom) ?>>14</option>
			<option value="15" <?php echo selected("15",$VALUE_zoom) ?>>15</option>
			<option value="16" <?php echo selected("16",$VALUE_zoom) ?>>16</option>
			<option value="17" <?php echo selected("17",$VALUE_zoom) ?>>17</option>
			<option value="18" <?php echo selected("18",$VALUE_zoom) ?>>18</option>
			<option value="19" <?php echo selected("19",$VALUE_zoom) ?>>19</option>
			<option value="20" <?php echo selected("20",$VALUE_zoom) ?>>20</option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field to specify view) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_view ?>"><?php echo ucfirst(__('view','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_view ?>" name="<?php echo $NAME_view ?>">
			<option value=""          <?php echo selected(""         ,$VALUE_view) ?>><?php echo SZGOOGLE_UPPER(__('default'  ,'sz-google')) ?></option>
			<option value="ROADMAP"   <?php echo selected("ROADMAP"  ,$VALUE_view) ?>><?php echo SZGOOGLE_UPPER(__('roadmap'  ,'sz-google')) ?></option>
			<option value="SATELLITE" <?php echo selected("SATELLITE",$VALUE_view) ?>><?php echo SZGOOGLE_UPPER(__('satellite','sz-google')) ?></option>
			<option value="HYBRID"    <?php echo selected("HYBRID"   ,$VALUE_view) ?>><?php echo SZGOOGLE_UPPER(__('hybrid'   ,'sz-google')) ?></option>
			<option value="TERRAIN"   <?php echo selected("TERRAIN"  ,$VALUE_view) ?>><?php echo SZGOOGLE_UPPER(__('terrain'  ,'sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field to specify layer) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_layer ?>"><?php echo ucfirst(__('layer','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_layer ?>" name="<?php echo $NAME_layer ?>">
			<option value=""        <?php echo selected(""       ,$VALUE_layer) ?>><?php echo SZGOOGLE_UPPER(__('default','sz-google')) ?></option>
			<option value="NOTHING" <?php echo selected("NOTHING",$VALUE_layer) ?>><?php echo SZGOOGLE_UPPER(__('nothing','sz-google')) ?></option>
			<option value="TRAFFIC" <?php echo selected("TRAFFIC",$VALUE_layer) ?>><?php echo SZGOOGLE_UPPER(__('traffic','sz-google')) ?></option>
			<option value="TRANSIT" <?php echo selected("TRANSIT",$VALUE_layer) ?>><?php echo SZGOOGLE_UPPER(__('transit','sz-google')) ?></option>
			<option value="BICYCLE" <?php echo selected("BICYCLE",$VALUE_layer) ?>><?php echo SZGOOGLE_UPPER(__('bicycle','sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field to specify wheel) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_wheel ?>"><?php echo ucfirst(__('wheel','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_wheel ?>" name="<?php echo $NAME_wheel ?>">
			<option value=""  <?php echo selected("" ,$VALUE_wheel) ?>><?php echo SZGOOGLE_UPPER(__('default' ,'sz-google')) ?></option>
			<option value="0" <?php echo selected("0",$VALUE_wheel) ?>><?php echo SZGOOGLE_UPPER(__('disabled','sz-google')) ?></option>
			<option value="1" <?php echo selected("1",$VALUE_wheel) ?>><?php echo SZGOOGLE_UPPER(__('enabled' ,'sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field to specify marker) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_marker ?>"><?php echo ucfirst(__('marker','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_marker ?>" name="<?php echo $NAME_marker ?>">
			<option value=""  <?php echo selected("" ,$VALUE_marker) ?>><?php echo SZGOOGLE_UPPER(__('default','sz-google')) ?></option>
			<option value="0" <?php echo selected("0",$VALUE_marker) ?>><?php echo SZGOOGLE_UPPER(__('nothing','sz-google')) ?></option>
			<option value="1" <?php echo selected("1",$VALUE_marker) ?>><?php echo SZGOOGLE_UPPER(__('display','sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field to lazy load) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_lazyload ?>"><?php echo ucfirst(__('lazy load','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_lazyload ?>" name="<?php echo $NAME_lazyload ?>">
			<option value=""  <?php echo selected("" ,$VALUE_lazyload) ?>><?php echo SZGOOGLE_UPPER(__('default' ,'sz-google')) ?></option>
			<option value="0" <?php echo selected("0",$VALUE_lazyload) ?>><?php echo SZGOOGLE_UPPER(__('disabled','sz-google')) ?></option>
			<option value="1" <?php echo selected("1",$VALUE_lazyload) ?>><?php echo SZGOOGLE_UPPER(__('enabled' ,'sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetMaps'); }
	});
</script>