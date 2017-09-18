<?php
/**
 * URL Module
 * Can't use DWOpts object because value = the serialized values
 *
 * @version $Id: domain_module.php 1698398 2017-07-18 19:34:08Z qurl $
 * @copyright 2017 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

 	class DW_Domain extends DWModule {
	   public static $option = array( 'domain' => 'Domain' );
	   protected static $overrule = TRUE;
	   protected static $type = 'custom';

	   public static function admin() {
		   $DW = $GLOBALS['DW'];

		   parent::admin();

		   $domain_yes_selected = 'checked="checked"';
		   $opt_domain = $DW->getOpt($GLOBALS['widget_id'], 'domain');

		   foreach ( $opt_domain as $opt ) {
			   if ( $opt->name == 'default' ) {
				   $domain_no_selected = $domain_yes_selected;
				   unset($domain_yes_selected);
			   } else {
				   $domains = unserialize($opt->value);
			   }
		   }
		   ?>
		   <h4 id="domain" title=" Click to toggle " class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><b><?php _e('Domain'); ?></b><?php echo ( count($opt_domain) > 0 ) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : ''; ?></h4>
		   <div id="domain_conf" class="dynwid_conf ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
			   <?php _e('Show widget at this domain?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('domain_info');" /><br />
			   <?php $DW->dumpOpt($opt_domain); ?>
			   <div>
				   <div id="domain_info" class="infotext">
					   Separate domains on each line.<br />
					   Only use the domain from the URL. No "http://", only e.g. qurl.nl or www.dynamic-widgets.com<br />
					   Beware of double rules! Especially when you set the default to 'No'. This means the widget will be shown NOWHERE.
				   </div>
			   </div>
			   <br />
			   <input type="radio" name="domain" value="yes" id="domain-yes" <?php echo ( isset($domain_yes_selected) ) ? $domain_yes_selected : ''; ?> /> <label for="domain-yes"><?php _e('Yes'); ?></label>
			   <input type="radio" name="domain" value="no" id="url-no" <?php echo ( isset($domain_no_selected) ) ? $domain_no_selected : ''; ?> /> <label for="domain-no"><?php _e('No'); ?></label><br />
			   <?php _e('Except the domains', DW_L10N_DOMAIN); ?>:<br />
			   <div id="domain-select" class="condition-select">
				   <textarea name="domain_value" style="width:300px;height:150px;"><?php echo ( isset($domains) ) ? implode("\n", $domains) : ''; ?></textarea>
			   </div>

		   </div><!-- end dynwid_conf -->
		   <?php
	   }
   }
?>
