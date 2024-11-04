<?php
/**
 * Shortcode Module
 *
 * @version $Id: shortcode_module.php 1218814 2015-08-12 06:37:21Z qurl $
 * @copyright 2015 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Shortcode extends DWModule {
		public static $option = array( 'shortcode' => 'Shortcode' );
		protected static $overrule = TRUE;
		protected static $type = 'custom';

		public static function admin() {
			$DW = $GLOBALS['DW'];

			parent::admin();

			$shortcode_yes_selected = 'checked="checked"';
			$opt_shortcode = $DW->getOpt($GLOBALS['widget_id'], 'shortcode');

			foreach ( $opt_shortcode as $opt ) {
				if ( $opt->name == 'default' ) {
					$shortcode_no_selected = $shortcode_yes_selected;
					unset($shortcode_yes_selected);
				} else {
					$shortcode = unserialize($opt->value);
				}
			}
?>

			<h4 id="shortcode" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b><?php _e('Shortcode'); ?></b><?php echo ( count($opt_shortcode) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
			<div id="shortcode_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
			<?php _e('Show widget when the shortcode ...', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('shortcode_info');" /><br />
			<?php $DW->dumpOpt($opt_shortcode); ?>
			<div>
				<div id="shortcode_info" class="infotext">
					The shortcode is executed on the page where the widget is configured.<br />
					The match needs to be exact and is case sensative. So, "a match" is not "A match" is not "a Match".
				</div>
			</div>
			<br />
			<input type="radio" name="shortcode" value="yes" id="shortcode-yes" <?php echo ( isset($shortcode_yes_selected) ) ? $shortcode_yes_selected : ''; ?> /> <label for="shortcode-yes"><?php _e('Yes'); ?></label>
			<input type="radio" name="shortcode" value="no" id="shortcode-no" <?php echo ( isset($shortcode_no_selected) ) ? $shortcode_no_selected : ''; ?> /> <label for="shortcode-no"><?php _e('No'); ?></label><br />

			<?php _e('Except when the...', DW_L10N_DOMAIN); ?>:<br />
			Shortcode <input type="text" name="shortcode_value" value="<?php echo ( isset($shortcode['value']) ) ? $shortcode['value'] : ''; ?>" />

			<select name="shortcode_operator">
			<?php
				$options = array( '=' => 'matches', '!=' => 'NOT matches' );
				foreach ( $options as $key => $value ) {
					echo '<option value="' . $key . '"';
					echo ( isset($shortcode['operator']) && $shortcode['operator'] == $key ) ? ' selected="selected"' : '';
					echo '>' . $value . '</option>';
				}
			?>
			</select>

			<input type="text" name="shortcode_match" value="<?php echo ( isset($shortcode['match']) ) ? $shortcode['match'] : ''; ?>" />

			</div><!-- end dynwid_conf -->

<?php
		}
	}
?>