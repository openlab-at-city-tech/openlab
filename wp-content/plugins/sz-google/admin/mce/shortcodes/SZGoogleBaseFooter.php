<?php

/**
 * Script to implement the HTML code shared with widgets 
 * in the function pop-up insert shortcodes via GUI
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Closing the FORM to contain the parameters that must be
// specified in the shortcode that we would go to compose OK

echo '<div style="text-align:right">';
echo '<input type="submit" onclick="javascript:SZGoogleDialog.insert(SZGoogleDialog.local_ed)" style="margin-left:5px" class="button button-primary" value="'  .ucfirst(__('confirm','sz-google')).'"/>';
echo '<input type="submit" onclick="javascript:SZGoogleDialog.cancel(SZGoogleDialog.local_ed)" style="margin-left:5px" class="button button-secondary" value="'.ucfirst(__('cancel' ,'sz-google')).'"/>';
echo '</div>';

echo "</form>\n";

// Loading Footer common part of the administration in a
// manner such as to load the styles that are used to FORM

require(ABSPATH.'/wp-admin/admin-footer.php');