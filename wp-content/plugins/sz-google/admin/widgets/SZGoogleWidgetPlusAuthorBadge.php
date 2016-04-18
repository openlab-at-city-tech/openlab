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
<table id="SZGoogleWidgetPlusAuthorBadge" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field for entering display) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_mode ?>"><?php echo ucfirst(__('display','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_mode ?>" name="<?php echo $NAME_mode ?>">
			<option value="1" <?php echo selected("1",$VALUE_mode) ?>><?php echo __('author post','sz-google') ?></option>
			<option value="2" <?php echo selected("2",$VALUE_mode) ?>><?php echo __('author post and archive','sz-google') ?></option>
		</select>
	</td>
</tr>

<tr class="only-widgets"><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the size) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_width ?>"><?php echo ucfirst(__('width','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_width ?>" class="sz-google-checks-width widefat" name="<?php echo $NAME_width ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_width ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input  id="<?php echo $ID_width_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-width" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_width_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_width_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field for entering link) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_link ?>"><?php echo ucfirst(__('link','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_link ?>" name="<?php echo $NAME_link ?>">
			<option value="1" <?php echo selected("1",$VALUE_link) ?>><?php echo __('link to google+','sz-google') ?></option>
			<option value="2" <?php echo selected("2",$VALUE_link) ?>><?php echo __('link to author page','sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for entering cover) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_cover ?>"><?php echo ucfirst(__('cover','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_cover ?>" name="<?php echo $NAME_cover ?>">
			<option value="1" <?php echo selected("1",$VALUE_cover) ?>><?php echo __('profile','sz-google') ?></option>
			<option value="N" <?php echo selected("N",$VALUE_cover) ?>><?php echo __('none','sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for entering photo) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_photo ?>"><?php echo ucfirst(__('photo','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_photo ?>" name="<?php echo $NAME_photo ?>">
			<option value="1" <?php echo selected("1",$VALUE_photo) ?>><?php echo __('profile','sz-google') ?></option>
			<option value="N" <?php echo selected("N",$VALUE_photo) ?>><?php echo __('none','sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for entering biography) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_biography ?>"><?php echo ucfirst(__('biography','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_biography ?>" name="<?php echo $NAME_biography ?>">
			<option value="1" <?php echo selected("1",$VALUE_biography) ?>><?php echo __('profile','sz-google') ?></option>
			<option value="2" <?php echo selected("2",$VALUE_biography) ?>><?php echo __('author tagline','sz-google') ?></option>
			<option value="N" <?php echo selected("N",$VALUE_biography) ?>><?php echo __('none','sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetPlusAuthorBadge'); }
	});
</script>