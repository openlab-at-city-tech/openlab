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
<table id="SZGoogleWidgetPanoramio" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field selection for configuration ID or specific) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_template ?>"><?php echo ucfirst(__('template','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_template ?>" name="<?php echo $NAME_template ?>">
			<option value="photo" <?php selected("photo",$VALUE_template) ?>><?php echo ucfirst(__('template photo','sz-google')) ?></option>
			<option value="slideshow" <?php selected("slideshow",$VALUE_template) ?>><?php echo ucfirst(__('template slideshow','sz-google')) ?></option>
			<option value="list" <?php selected("list",$VALUE_template) ?>><?php echo ucfirst(__('template list','sz-google')) ?></option>
			<option value="photo_list" <?php selected("photo_list",$VALUE_template) ?>><?php echo ucfirst(__('template photo_list','sz-google')) ?></option>
		</select>
	</td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field selection for lookup fields in the widget photographs) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_user ?>"><?php echo ucfirst(__('user','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_user ?>" name="<?php echo $NAME_user ?>" type="text" value="<?php echo $VALUE_user ?>" placeholder="<?php echo __('specify search user','sz-google') ?>"/></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_group ?>"><?php echo ucfirst(__('group','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_group ?>" name="<?php echo $NAME_group ?>" type="text" value="<?php echo $VALUE_group ?>" placeholder="<?php echo __('specify search group','sz-google') ?>"/></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_tag ?>"><?php echo ucfirst(__('tag','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_tag ?>" name="<?php echo $NAME_tag ?>" type="text" value="<?php echo $VALUE_tag ?>" placeholder="<?php echo __('specify search tag','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field selection for SET field) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_set ?>"><?php echo ucfirst(__('set','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_set ?>" name="<?php echo $NAME_set ?>">
			<option value="all" <?php selected("all",$VALUE_set) ?>><?php echo ucfirst(__('all','sz-google')) ?></option>
			<option value="public" <?php selected("public",$VALUE_set) ?>><?php echo ucfirst(__('public','sz-google')) ?></option>
			<option value="recent" <?php selected("recent",$VALUE_set) ?>><?php echo ucfirst(__('recent','sz-google')) ?></option>
		</select>
	</td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the size) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_width ?>"><?php echo ucfirst(__('width','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width ?>" class="sz-google-checks-width widefat" name="<?php echo $NAME_width ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_width ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-width" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_width_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_width_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_height ?>"><?php echo ucfirst(__('height','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_height ?>" class="sz-google-checks-height widefat" name="<?php echo $NAME_height ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_height ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_height_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-height" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_height_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_height_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_columns ?>"><?php echo ucfirst(__('columns','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input id="<?php echo $ID_columns ?>" name="<?php echo $NAME_columns ?>" type="number" size="5" step="1" min="1" max="100" value="<?php echo $VALUE_columns ?>"/></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_rows ?>"><?php echo ucfirst(__('rows','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input id="<?php echo $ID_rows ?>" name="<?php echo $NAME_rows ?>" type="number" size="5" step="1" min="1" max="100" value="<?php echo $VALUE_rows ?>"/></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field selection fields for orientation and position) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_orientation ?>"><?php echo ucfirst(__('orientation','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_orientation ?>" name="<?php echo $NAME_orientation ?>">
			<option value="horizontal" <?php selected("horizontal",$VALUE_orientation) ?>><?php echo ucfirst(__('horizontal','sz-google')) ?></option>
			<option value="vertical" <?php selected("vertical",$VALUE_orientation) ?>><?php echo ucfirst(__('vertical','sz-google')) ?></option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_position ?>"><?php echo ucfirst(__('position','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_position ?>" name="<?php echo $NAME_position ?>">
			<option value="bottom" <?php selected("bottom",$VALUE_position) ?>><?php echo ucfirst(__('bottom','sz-google')) ?></option>
			<option value="top" <?php selected("top",$VALUE_position) ?>><?php echo ucfirst(__('top','sz-google')) ?></option>
			<option value="left" <?php selected("left",$VALUE_position) ?>><?php echo ucfirst(__('right','sz-google')) ?></option>
			<option value="right" <?php selected("right",$VALUE_position) ?>><?php echo ucfirst(__('right','sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetPanoramio'); }
		if (typeof(szgoogle_switch_hidden_onload) == 'function') { szgoogle_switch_hidden_onload('SZGoogleWidgetPanoramio'); }
	});
</script>