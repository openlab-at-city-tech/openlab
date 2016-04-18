<?php

/**
 * Define a class that identifies an action called by the
 * main module based on the options that have been activated
 *
 * @package SZGoogle
 * @subpackage Actions
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleActionAnalytics'))
{
	class SZGoogleActionAnalytics extends SZGoogleAction
	{
		/**
		 * The function that is normally invoked by hook
		 * presents in add_action e add_filter in wordpress
		 */

		function action() {
			echo $this->getMonitorCode(array());
		}

		/**
		 * The function that is normally invoked by hook
		 * presents in add_action e add_filter in wordpress
		 */

		function getMonitorCode($atts=array())
		{
			if (!is_array($atts)) $atts = array();

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets
			
			$options = (object) $this->getModuleOptions('SZGoogleModuleAnalytics');

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'ga_type'                 => $options->ga_type,
				'ga_uacode'               => $options->ga_uacode,
				'ga_position'             => $options->ga_position,
				'ga_compression'          => $options->ga_compression,
				'ga_enable_front'         => $options->ga_enable_front,
				'ga_enable_admin'         => $options->ga_enable_admin,
				'ga_enable_administrator' => $options->ga_enable_administrator,
				'ga_enable_logged'        => $options->ga_enable_logged,
				'ga_enable_subdomain'     => $options->ga_enable_subdomain,
				'ga_enable_multiple'      => $options->ga_enable_multiple,
				'ga_enable_advertiser'    => $options->ga_enable_advertiser,
				'ga_enable_features'      => $options->ga_enable_features,
				'ga_enable_ip_none_cl'    => $options->ga_enable_ip_none_cl,
				'ga_enable_ip_none_ad'    => $options->ga_enable_ip_none_ad,
				'ga_enable_cl_proxy'      => $options->ga_enable_cl_proxy,
				'ga_enable_cl_proxy_url'  => $options->ga_enable_cl_proxy_url,
				'ga_enable_cl_proxy_adv'  => $options->ga_enable_cl_proxy_adv,
				'ga_enable_un_proxy'      => $options->ga_enable_un_proxy,
				'ga_enable_un_proxy_url'  => $options->ga_enable_un_proxy_url,
			),$atts));

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$ga_uacode               = trim($ga_uacode);
			$ga_enable_cl_proxy_url  = trim($ga_enable_cl_proxy_url);
			$ga_enable_cl_proxy_adv  = trim($ga_enable_cl_proxy_adv);
			$ga_enable_un_proxy_url  = trim($ga_enable_un_proxy_url);

			$ga_type                 = strtolower(trim($ga_type));
			$ga_position             = strtoupper(trim($ga_position));
			$ga_compression          = strtolower(trim($ga_compression));
			$ga_enable_front         = strtolower(trim($ga_enable_front));
			$ga_enable_admin         = strtolower(trim($ga_enable_admin));
			$ga_enable_administrator = strtolower(trim($ga_enable_administrator));
			$ga_enable_logged        = strtolower(trim($ga_enable_logged));
			$ga_enable_subdomain     = strtolower(trim($ga_enable_subdomain));
			$ga_enable_multiple      = strtolower(trim($ga_enable_multiple));
			$ga_enable_advertiser    = strtolower(trim($ga_enable_advertiser));
			$ga_enable_features      = strtolower(trim($ga_enable_features));
			$ga_enable_ip_none_cl    = strtolower(trim($ga_enable_ip_none_cl));
			$ga_enable_ip_none_ad    = strtolower(trim($ga_enable_ip_none_ad));
			$ga_enable_cl_proxy      = strtolower(trim($ga_enable_cl_proxy));
			$ga_enable_un_proxy      = strtolower(trim($ga_enable_un_proxy));

			// Conversion of the values ​​specified directly covered in the
			// parameters with the values ​​used for storing the default values

			if ($ga_compression          == 'yes' or $ga_compression          == 'y') $ga_compression          = '1';
			if ($ga_enable_front         == 'yes' or $ga_enable_front         == 'y') $ga_enable_front         = '1';
			if ($ga_enable_admin         == 'yes' or $ga_enable_admin         == 'y') $ga_enable_admin         = '1';
			if ($ga_enable_administrator == 'yes' or $ga_enable_administrator == 'y') $ga_enable_administrator = '1';
			if ($ga_enable_logged        == 'yes' or $ga_enable_logged        == 'y') $ga_enable_logged        = '1';
			if ($ga_enable_subdomain     == 'yes' or $ga_enable_subdomain     == 'y') $ga_enable_subdomain     = '1';
			if ($ga_enable_multiple      == 'yes' or $ga_enable_multiple      == 'y') $ga_enable_multiple      = '1';
			if ($ga_enable_advertiser    == 'yes' or $ga_enable_advertiser    == 'y') $ga_enable_advertiser    = '1';
			if ($ga_enable_features      == 'yes' or $ga_enable_features      == 'y') $ga_enable_features      = '1';
			if ($ga_enable_ip_none_cl    == 'yes' or $ga_enable_ip_none_cl    == 'y') $ga_enable_ip_none_cl    = '1';
			if ($ga_enable_ip_none_ad    == 'yes' or $ga_enable_ip_none_ad    == 'y') $ga_enable_ip_none_ad    = '1';
			if ($ga_enable_cl_proxy      == 'yes' or $ga_enable_cl_proxy      == 'y') $ga_enable_cl_proxy      = '1';
			if ($ga_enable_un_proxy      == 'yes' or $ga_enable_un_proxy      == 'y') $ga_enable_un_proxy      = '1';

			if ($ga_compression          == 'no'  or $ga_compression          == 'n') $ga_compression          = '0';
			if ($ga_enable_front         == 'no'  or $ga_enable_front         == 'n') $ga_enable_front         = '0';
			if ($ga_enable_admin         == 'no'  or $ga_enable_admin         == 'n') $ga_enable_admin         = '0';
			if ($ga_enable_administrator == 'no'  or $ga_enable_administrator == 'n') $ga_enable_administrator = '0';
			if ($ga_enable_logged        == 'no'  or $ga_enable_logged        == 'n') $ga_enable_logged        = '0';
			if ($ga_enable_subdomain     == 'no'  or $ga_enable_subdomain     == 'n') $ga_enable_subdomain     = '0';
			if ($ga_enable_multiple      == 'no'  or $ga_enable_multiple      == 'n') $ga_enable_multiple      = '0';
			if ($ga_enable_advertiser    == 'no'  or $ga_enable_advertiser    == 'n') $ga_enable_advertiser    = '0';
			if ($ga_enable_features      == 'no'  or $ga_enable_features      == 'n') $ga_enable_features      = '0';
			if ($ga_enable_ip_none_cl    == 'no'  or $ga_enable_ip_none_cl    == 'n') $ga_enable_ip_none_cl    = '0';
			if ($ga_enable_ip_none_ad    == 'no'  or $ga_enable_ip_none_ad    == 'n') $ga_enable_ip_none_ad    = '0';
			if ($ga_enable_cl_proxy      == 'no'  or $ga_enable_cl_proxy      == 'n') $ga_enable_cl_proxy      = '0';
			if ($ga_enable_un_proxy      == 'no'  or $ga_enable_un_proxy      == 'n') $ga_enable_un_proxy      = '0';

			// Check if they are logged in as an administrator or registered
			// user and off loading the code if the options are disabled

			$ENDLINE = "\n";
			$USERACT = true;

			if (current_user_can('manage_options')) {
				if ($ga_enable_administrator == '0') $USERACT = false;
			} else {
				if (current_user_can('read') and $ga_enable_logged == '0') $USERACT = false;
			}

			// Check if they are in the backend or frontend and I enable code execution
			// only if the corresponding options have been activated in the configuration

			if ( is_admin() and $ga_enable_admin == '0') $USERACT = false;
			if (!is_admin() and $ga_enable_front == '0') $USERACT = false;

			// If the code does not have to be activated based on the options passed
			// return a value of false and not elaborate the creation of monitoring

			if (!$USERACT or strlen($ga_uacode) <= 0) {
				return false;
			}

			// Conversion of the values ​​specified directly covered in the
			// parameters with the values ​​used for storing the default values

			if ($ga_position    == '' ) $ga_position = 'H';
			if ($ga_uacode      == '' ) $ga_uacode   = $this->getGAId();
			if ($ga_compression == '1') $ENDLINE = '';

			if (!in_array($ga_type,array('classic','universal'))) $ga_type = 'classic';

			// Creating code for comments to be blocked 
			// if proves active code generation GA

			$HTML = '';

			if ($ga_compression != '1' and $ga_position != 'F' and ($ga_type == 'universal' or $ga_type == 'classic')) {
				$HTML .= "\n";
				$HTML .= "<!-- GA tracking code with SZ-Google ".SZ_PLUGIN_GOOGLE_VERSION." : activated mode ".strtoupper($ga_type)."      -->\n";
				$HTML .= "<!-- ===================================================================== -->\n";
			}

			// Creating code google analytics UNIVERSAL be inserted on HTML page
			// which can be different according to google analytics classic or universal

			if ($ga_type == 'universal') 
			{
				// Check if you have activated the function PROXY HTTP
				// to create a local demand that points to the original script

				if ($ga_enable_un_proxy == '1' and trim($ga_enable_un_proxy_url) != '') $SCRIPTNAME = $ga_enable_un_proxy_url;
					else $SCRIPTNAME = "www.google-analytics.com/analytics.js";

				$SCRIPTNAME = preg_replace('#^https?://#','',$SCRIPTNAME);

				// Creation of HTML code to insert in the page WEB
				// for the result check placement options and compression

				$HTML .= '<script type="text/javascript">'.$ENDLINE;
				$HTML .= "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){".$ENDLINE;
				$HTML .= "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),".$ENDLINE;
				$HTML .= "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)".$ENDLINE;
				$HTML .= "})(window,document,'script','//$SCRIPTNAME','ga');".$ENDLINE;
				$HTML .= "ga('create','".trim($ga_uacode)."','".trim(SZGoogleCommon::getCurrentDomain())."');".$ENDLINE;

				if ($ga_enable_features   == '1') $HTML .= "ga('require','displayfeatures');".$ENDLINE;
				if ($ga_enable_ip_none_ad == '1') $HTML .= "ga('set','anonymizeIp',true);".$ENDLINE;

				$HTML .= "ga('send','pageview');".$ENDLINE;
				$HTML .= "</script>".$ENDLINE;
			}

			// Creating code google analytics CLASSIC be inserted on HTML page which
			// can be different according to google analytics classic or universal

			if ($ga_type == 'classic') 
			{
				$HTML .= '<script type="text/javascript">'.$ENDLINE;
				$HTML .= "var _gaq = _gaq || [];".$ENDLINE;
				$HTML .= "_gaq.push(['_setAccount','".$ga_uacode."']);".$ENDLINE;

				// If option is activated multiple subdomains or add a new row
				// code containing the current displayed _setDomainName domino

				if ($ga_enable_subdomain == '1' or $ga_enable_multiple  == '1') {
					$HTML .= "_gaq.push(['_setDomainName','".trim(SZGoogleCommon::getCurrentDomain())."']);".$ENDLINE;
				}

				// If multiple option is enabled add a new row with the code
				// javascript for google analytics with setup for _setAllowLinker

				if ($ga_enable_multiple   == '1') $HTML .= "_gaq.push(['_setAllowLinker',true]);".$ENDLINE;
				if ($ga_enable_ip_none_cl == '1') $HTML .= "_gaq.push(['_gat._anonymizeIp']);".$ENDLINE;

				$HTML .= "_gaq.push(['_trackPageview']);".$ENDLINE;

				$HTML .= "(function () {".$ENDLINE;
				$HTML .= "var ga = document.createElement('script');".$ENDLINE;
				$HTML .= "ga.type = 'text/javascript';".$ENDLINE;
				$HTML .= "ga.async = true;".$ENDLINE;

				// Check if you have activated the function PROXY HTTP
				// to create a local demand that points to the original script

				$SCRIPT_NOR = ".google-analytics.com/ga.js"; $WWS = 'ssl'; $WWW = 'www';
				$SCRIPT_ADV = "stats.g.doubleclick.net/dc.js";

				if ($ga_enable_cl_proxy == '1' and trim($ga_enable_cl_proxy_url) != '') {
					$SCRIPT_NOR = $ga_enable_cl_proxy_url; $WWS = ''; $WWW = '';
					$SCRIPT_NOR = preg_replace('#^https?://#','',$SCRIPT_NOR);
				}

				if ($ga_enable_cl_proxy == '1' and trim($ga_enable_cl_proxy_adv) != '') {
					$SCRIPT_ADV = $ga_enable_cl_proxy_adv;
					$SCRIPT_ADV = preg_replace('#^https?://#','',$SCRIPT_ADV);
				}

				// Creation of HTML code to insert in the page WEB
				// for the result check placement options and compression

				if ($ga_enable_advertiser == '1') $HTML .= "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + '$SCRIPT_ADV';".$ENDLINE;
					else $HTML .= "ga.src = ('https:' == document.location.protocol ? 'https://$WWS' : 'http://$WWW') + '$SCRIPT_NOR';".$ENDLINE;

				// Creation of HTML code to insert in the page WEB
				// for the result check placement options and compression

				$HTML .= "var s = document.getElementsByTagName('script')[0];".$ENDLINE;
				$HTML .= "s.parentNode.insertBefore(ga, s);".$ENDLINE;
				$HTML .= "})();".$ENDLINE;
				$HTML .= "</script>".$ENDLINE;
			}

			// Creating code for comments to be blocked 
			// if proves active code generation GA

			if ($ga_compression != '1' and $ga_position != 'F' and ($ga_type == 'universal' or $ga_type == 'classic')) {
				$HTML .= "<!-- ===================================================================== -->\n\n";
			}

			return $HTML;
		}
	}
}
