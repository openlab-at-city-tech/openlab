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
<table id="SZGoogleWidgetCalendar" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('widget title','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field with inclusion of the calendar) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_calendar ?>"><?php echo ucfirst(__('calendar','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_calendar ?>" name="<?php echo $NAME_calendar ?>" type="text" value="<?php echo $VALUE_calendar ?>" placeholder="<?php echo __('configuration','sz-google') ?>"/></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_calendarT ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_calendarT ?>" name="<?php echo $NAME_calendarT ?>" type="text" value="<?php echo $VALUE_calendarT ?>" placeholder="<?php echo __('configuration','sz-google') ?>"/></td>
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

<!-- WIDGETS (Field with entering values ​​SHOW) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtitle ?>" value="y" <?php if ($VALUE_showtitle == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtitle ?>" value="n" <?php if ($VALUE_showtitle != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('tabs','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtabs ?>" value="y" <?php if ($VALUE_showtabs == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtabs ?>" value="n" <?php if ($VALUE_showtabs != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('date','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showdate ?>" value="y" <?php if ($VALUE_showdate == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showdate ?>" value="n" <?php if ($VALUE_showdate != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('navigation','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_shownavs ?>" value="y" <?php if ($VALUE_shownavs == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_shownavs ?>" value="n" <?php if ($VALUE_shownavs != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('print','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showprint ?>" value="y" <?php if ($VALUE_showprint == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showprint ?>" value="n" <?php if ($VALUE_showprint != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('list','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showcalendars ?>" value="y" <?php if ($VALUE_showcalendars == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showcalendars ?>" value="n" <?php if ($VALUE_showcalendars != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('time zone','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtimezone ?>" value="y" <?php if ($VALUE_showtimezone == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtimezone ?>" value="n" <?php if ($VALUE_showtimezone != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field for input type view) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_mode ?>"><?php echo ucfirst(__('mode','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_mode ?>" name="<?php echo $NAME_mode ?>">
			<option value="MONTH"   <?php echo selected("MONTH" ,$VALUE_mode) ?>><?php echo __('monthly','sz-google') ?></option>
			<option value="WEEK"    <?php echo selected("WEEK"  ,$VALUE_mode) ?>><?php echo __('weekly' ,'sz-google') ?></option>
			<option value="AGENDA"  <?php echo selected("AGENDA",$VALUE_mode) ?>><?php echo __('agenda' ,'sz-google') ?></option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_weekstart ?>"><?php echo ucfirst(__('week start','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_weekstart ?>" name="<?php echo $NAME_weekstart ?>">
			<option value="1" <?php echo selected("1",$VALUE_weekstart) ?>><?php echo __('sunday'  ,'sz-google') ?></option>
			<option value="2" <?php echo selected("2",$VALUE_weekstart) ?>><?php echo __('monday'  ,'sz-google') ?></option>
			<option value="7" <?php echo selected("7",$VALUE_weekstart) ?>><?php echo __('saturday','sz-google') ?></option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_language ?>"><?php echo ucfirst(__('language','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_language ?>" name="<?php echo $NAME_language ?>">
<?php foreach (SZGoogleCommon::getLanguages() as $key=>$value): ?>
			<option value="<?php echo $key ?>" <?php echo selected($key,$VALUE_language) ?>><?php echo $value ?></option>
<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_timezone ?>"><?php echo ucfirst(__('timezone','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_timezone ?>" name="<?php echo $NAME_timezone ?>">
<?php foreach (SZGoogleCommon::getTimeZone() as $key=>$value): ?>
			<option value="<?php echo $key ?>" <?php echo selected($key,$VALUE_timezone) ?>><?php echo $value ?></option>
<?php endforeach; ?>
		</select>
	</td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetCalendar'); }
		if (typeof(szgoogle_switch_hidden_onload) == 'function') { szgoogle_switch_hidden_onload('SZGoogleWidgetCalendar'); }
	});
</script>