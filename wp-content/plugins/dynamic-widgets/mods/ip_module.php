<?php
/**
 * IP Module
 * Can't use DWOpts object because value = the serialized values
 *
 * @version $Id$
 * @copyright 2014 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

 	class DW_IP extends DWModule {
		public static $option = array( 'ip' => 'IP Address' );
		protected static $overrule = TRUE;
		protected static $type = 'custom';

		public static function admin() {
			$DW = $GLOBALS['DW'];

			parent::admin();

			$ip_yes_selected = 'checked="checked"';
			$opt_ip = $DW->getOpt($GLOBALS['widget_id'], 'ip');

			foreach ( $opt_ip as $opt ) {
				if ( $opt->name == 'default' ) {
					$ip_no_selected = $ip_yes_selected;
					unset($ip_yes_selected);
				} else {
					$ips = unserialize($opt->value);
				}
			}
?>
<h4 id="ip" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b><?php _e('IP Address'); ?></b><?php echo ( count($opt_ip) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
<div id="ip_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
<?php _e('Show widget for this IP (range)?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('ip_info');" /><br />
<?php $DW->dumpOpt($opt_ip); ?>
<div>
	<div id="ip_info" class="infotext">
		Works only with IPv4, not IPv6! No checks are performed for overlapping addresses, invalid ranges, etc.<br />
		Separate IP (ranges) on each line.<br />
		<br />
		IP format notation can be...
		<div style="position:relative;left:20px;">
		<ul>
			<li>single IP: 192.168.1.1</li>
			<li>in <a href="http://en.wikipedia.org/wiki/Cidr" target="_blank">CIDR</a><sup> [WARNING: techie!]</sup> (recommended): 192.168.1.1/32, 192.168.1.0/24 or 192.168.1.0/255.255.255.0</li>
			<li>in wildcard: 192.168.1.*</li>
			<li>in range: 192.168.1.1-192.168.1.254</li>
		</ul>
		</div>
	</div>
</div>
<br />
<input type="radio" name="ip" value="yes" id="ip-yes" <?php echo ( isset($ip_yes_selected) ) ? $ip_yes_selected : ''; ?> /> <label for="ip-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="ip" value="no" id="ip-no" <?php echo ( isset($ip_no_selected) ) ? $ip_no_selected : ''; ?> /> <label for="ip-no"><?php _e('No'); ?></label><br />
<?php _e('Except the IP (ranges)', DW_L10N_DOMAIN); ?>:<br />
<div id="ip-select" class="condition-select">
<textarea name="ip_value" style="width:300px;height:150px;"><?php echo ( isset($ips) ) ? implode("\n", $ips) : ''; ?></textarea>
</div>

</div><!-- end dynwid_conf -->
<?php
		}
	}
?>