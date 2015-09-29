<?php
/**
 * URL Module
 * Can't use DWOpts object because value = the serialized values
 *
 * @version $Id: date_module.php 437634 2011-09-13 19:19:13Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

 	class DW_URL extends DWModule {
		public static $option = array( 'url' => 'URL' );
		protected static $overrule = TRUE;
		protected static $type = 'custom';

		public static function admin() {
			$DW = $GLOBALS['DW'];

			parent::admin();

			$url_yes_selected = 'checked="checked"';
			$opt_url = $DW->getOpt($GLOBALS['widget_id'], 'url');
			$prefix = $DW->getURLPrefix();

			foreach ( $opt_url as $opt ) {
				if ( $opt->name == 'default' ) {
					$url_no_selected = $url_yes_selected;
					unset($url_yes_selected);
				} else {
					$urls = unserialize($opt->value);
				}
			}
?>
<h4 id="url" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b><?php _e('URL'); ?></b><?php echo ( count($opt_url) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div id="url_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
<?php _e('Show widget at this URL?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('url_info');" /><br />
<?php $DW->dumpOpt($opt_url); ?>
<div>
	<div id="url_info" class="infotext">
		Separate URLs on each line.<br />
		Use an asterisk ( * ) at the end of an URL as 'Starts with'.<br />
		Use an asterisk at the start of an URL as 'Ends with'.<br />
		Using an asterisk at the start and end of an url means 'Somewhere within'.<br />
		Without any asterisk means 'Exact match'.<br />
		When you don't start with an asterisk, start with a slash ( / ).<br />
		Beware of double rules! Especially when you set the default to 'No'. This means the widget will be shown NOWHERE.
	</div>
</div>
<br />
<input type="radio" name="url" value="yes" id="url-yes" <?php echo ( isset($url_yes_selected) ) ? $url_yes_selected : ''; ?> /> <label for="url-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="url" value="no" id="url-no" <?php echo ( isset($url_no_selected) ) ? $url_no_selected : ''; ?> /> <label for="url-no"><?php _e('No'); ?></label><br />
<?php _e('Except the URLs', DW_L10N_DOMAIN); ?>: <?php echo (! empty($prefix) ) ? '<br />Note: Do not include ' . $prefix : '';  ?><br />
<div id="url-select" class="condition-select">
<textarea name="url_value" style="width:300px;height:150px;"><?php echo ( isset($urls) ) ? implode("\n", $urls) : ''; ?></textarea>
</div>

</div><!-- end dynwid_conf -->
<?php
		}
	}
?>