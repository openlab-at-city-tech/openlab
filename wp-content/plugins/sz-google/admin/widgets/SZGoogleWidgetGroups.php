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
<table id="SZGoogleWidgetGroups" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field placement with group name) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_name ?>"><?php echo ucfirst(__('group','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_name ?>" name="<?php echo $NAME_name ?>" type="text" value="<?php echo $VALUE_name ?>" placeholder="<?php echo __('insert group name','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field with insertion domain name) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_domain ?>"><?php echo ucfirst(__('domain APPs','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_domain ?>" name="<?php echo $NAME_domain ?>" type="text" value="<?php echo $VALUE_domain ?>" placeholder="<?php echo __('insert domain name','sz-google') ?>"/></td>
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

<!-- WIDGETS (Field to specify the parameter showsearch) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('search','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showsearch ?>" value="true"  <?php if ($VALUE_showsearch == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showsearch ?>" value="false" <?php if ($VALUE_showsearch != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter showtabs) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('tabs','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtabs ?>" value="true"  <?php if ($VALUE_showtabs == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_showtabs ?>" value="false" <?php if ($VALUE_showtabs != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter hideforumtitle) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('hide title','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_hideforumtitle ?>" value="true"  <?php if ($VALUE_hideforumtitle == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_hideforumtitle ?>" value="false" <?php if ($VALUE_hideforumtitle != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter hidesubject) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('hide subject','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_hidesubject ?>" value="true"  <?php if ($VALUE_hidesubject == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_hidesubject ?>" value="false" <?php if ($VALUE_hidesubject != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetGroups'); }
	});
</script>