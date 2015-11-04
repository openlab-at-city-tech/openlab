<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Modules
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleModule'))
{
	class SZGoogleModule
	{
		// Definition of the variables that contain the pointer to
		// the object of the reference module if this is activated

		static private $SZGoogleModulePlus          = false;
		static private $SZGoogleModuleAjax          = false;
		static private $SZGoogleModuleAuthenticator = false;
		static private $SZGoogleModuleAnalytics     = false;
		static private $SZGoogleModuleCalendar      = false;
		static private $SZGoogleModuleDrive         = false;
		static private $SZGoogleModuleGroups        = false;
		static private $SZGoogleModuleFonts         = false;
		static private $SZGoogleModuleHangouts      = false;
		static private $SZGoogleModuleMaps          = false;
		static private $SZGoogleModulePanoramio     = false;
		static private $SZGoogleModuleRecaptcha     = false;
		static private $SZGoogleModuleTranslate     = false;
		static private $SZGoogleModuleYoutube       = false;

		// Definition of variables to see if the javascript 
		// code has already been loaded previously

		static $JavascriptLazyLoad = false;
		static $JavascriptMapsCSS  = false;
		static $JavascriptMapsCode = false;
		static $JavascriptPlusone  = false;
		static $JavascriptPlatform = false;

		// Definition of variables that contain settings
		// created during the function call moduleAddSetup()

		private $moduleClassName  = false;
		private $moduleOptions    = false;
		private $moduleOptionSet  = false;

		// Definition of variables containing the configurations
		// objects related to the current module as widgets and shortcodes

		private $moduleActions    = array();
		private $moduleShortcodes = array();
		private $moduleWidgets    = array();

		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			// When the class is used by a definition of a module, this 
			// function must be implemented by configuring the options

			$this->moduleAddSetup();

			// If you define a class name memorize the reference object
			// in a static variable for use in external functions

			if (isset($this->moduleClassName)) 
			{
				if ($this->moduleClassName == 'SZGoogleModulePlus')          self::$SZGoogleModulePlus          = $this;
				if ($this->moduleClassName == 'SZGoogleModuleAjax')          self::$SZGoogleModuleAjax          = $this;
				if ($this->moduleClassName == 'SZGoogleModuleAnalytics')     self::$SZGoogleModuleAnalytics     = $this;
				if ($this->moduleClassName == 'SZGoogleModuleAuthenticator') self::$SZGoogleModuleAuthenticator = $this;
				if ($this->moduleClassName == 'SZGoogleModuleCalendar')      self::$SZGoogleModuleCalendar      = $this;
				if ($this->moduleClassName == 'SZGoogleModuleDrive')         self::$SZGoogleModuleDrive         = $this;
				if ($this->moduleClassName == 'SZGoogleModuleFonts')         self::$SZGoogleModuleFonts         = $this;
				if ($this->moduleClassName == 'SZGoogleModuleGroups')        self::$SZGoogleModuleGroups        = $this;
				if ($this->moduleClassName == 'SZGoogleModuleHangouts')      self::$SZGoogleModuleHangouts      = $this;
				if ($this->moduleClassName == 'SZGoogleModuleMaps')          self::$SZGoogleModuleMaps          = $this;
				if ($this->moduleClassName == 'SZGoogleModulePanoramio')     self::$SZGoogleModulePanoramio     = $this;
				if ($this->moduleClassName == 'SZGoogleModuleRecaptcha')     self::$SZGoogleModuleRecaptcha     = $this;
				if ($this->moduleClassName == 'SZGoogleModuleTranslate')     self::$SZGoogleModuleTranslate     = $this;
				if ($this->moduleClassName == 'SZGoogleModuleYoutube')       self::$SZGoogleModuleYoutube       = $this;
			}

			// Implementation of existing components related to the module as
			// the general operations and the generation of shortcodes and widgets

			if ($this->moduleOptionSet) 
			{
				$this->moduleAddActions();
				$this->moduleAddShortcodes();
				$this->moduleAddWidgets();
	 		}
 		}

		/**
		 * Calculation options related to the module with execution 
		 * of formal checks of consistency and setting the default
		 */

		function getOptions()
		{ 
			if ($this->moduleOptions) return $this->moduleOptions;
				else $this->moduleOptions = $this->getOptionsSet($this->moduleOptionSet);

			// Ritorno indietro il gruppo di opzioni corretto dai
			// controlli formali eseguito dalla funzione di controllo

			return $this->moduleOptions;
		}

		/**
		 * Calculation options related to the module with execution 
		 * of formal checks of consistency and setting the default
		 */

		function getOptionsSet($nameset)
		{
			$optionsDB   = get_option($nameset);
			$optionsList = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/{$nameset}.php");

			// if options do not exist control these with
			// the function isset() and create an array

			foreach($optionsList as $key => $item) 
			{
				// Checking existence field in the options list
				// wordpress otherwise add the field original file array

				if (!isset($optionsDB[$key])) $optionsDB[$key] = $item['value'];

				// Check if the option field contains a value of NULL
				// In this case check the value of the default option

				if (isset($item['N']) and $item['N'] == '1') {
					if ($optionsDB[$key] == '') $optionsDB[$key] = $item['value'];
				}

				// Check if the option field contains a value of zero
				// In this case check the value of the default option

				if (isset($item['Z']) and $item['Z'] == '1') {
					if ($optionsDB[$key] == '0') $optionsDB[$key] = $item['value'];
				}

				// Check if the option field contains a value of YES/NO
				// In this case check the value of the default option

				if (isset($item['Y']) and $item['Y'] == '1') {
					if (!in_array($optionsDB[$key],array('1','0'))) $optionsDB[$key] = '0';
				}
			}

			return $optionsDB;
		}

		/**
		 * Function to add configuration variables 
		 * that allow you to upload subsequent modules
		 */

		function moduleAddSetup() {}

		/**
		 * Function to add the actions to be performed  
		 * according to the options on the admin panel
		 */

		function moduleAddActions() {}

		/**
		 * Add all the shortcodes that are present in the protected
		 * variable configuration of module $moduleShortcodes
		 */

		function moduleAddShortcodes()
		{
			$options = $this->getOptions();

			foreach($this->moduleShortcodes as $optionName=>$shortcode) {
				if (isset($options[$optionName]) and $options[$optionName] == '1') {
					add_shortcode($shortcode[0],$shortcode[1]);
				}
			}
		}

		/**
		 * Add all the widgets that are present in the protected
		 * variable configuration of module $moduleWidgets
		 */

		function moduleAddWidgets()
		{
			$options = $this->getOptions();

			foreach($this->moduleWidgets as $optionName=>$classWidgetName) {
				if (isset($options[$optionName]) and $options[$optionName] == '1') {
					add_action('widgets_init',create_function('','return register_widget("'.$classWidgetName.'");'));
				}
			}
		}

		/**
		 * Functions that are used to assign values to the initial 
		 * configuration of the module as the class name and the set of options
		 */

		function moduleSetClassName($classname) { $this->moduleClassName = $classname; }
		function moduleSetOptionSet($nameset)   { $this->moduleOptionSet = $nameset;   }

		/**
		 * Add all the shortcodes to be loaded by
		 * storing the private variable of the class
		 */

		function moduleSetShortcodes($items) {
			if (is_array($items)) $this->moduleShortcodes = $items;
		}

		/**
		 * Add all the widgets to be loaded by
		 * storing the private variable of the class
		 */

		function moduleSetWidgets($items) {
			if (is_array($items)) $this->moduleWidgets = $items;
		}

		/**
		 * Function to add javascript code in the footer of wordpress
		 * with asynchronous loading method according to google
		 */

		function setJavascriptPlatform()
		{
			// If you've already entered the Javascript code in the footer section
			// leave the partition function otherwise the variable and constant

			if (self::$JavascriptPlatform) return; 
				else self::$JavascriptPlatform = true;

			// Javascript code to render the google platform components
			// for example call this script for the buttons hangouts

			$javascript  = '<script type="text/javascript">';
			$javascript .= "(function(){";
			$javascript .= "var po=document.createElement('script');po.type='text/javascript';po.async=true;";
			$javascript .= "po.src='https://apis.google.com/js/platform.js';";
			$javascript .=  "var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(po,s);";
			$javascript .=  "})();";
			$javascript .=	"</script>"."\n";

			// Running echo on the footer of the javascript code generated
			// This code is added to a single block together with other functions

			echo $javascript;
		}

		/**
		 * Function to add javascript code in the footer of wordpress
		 * with asynchronous loading method according to google
		 */

		function setJavascriptPlusOne()
		{
			// If you've already entered the Javascript code in the footer section
			// leave the partition function otherwise the variable and constant

			if (self::$JavascriptPlusone) return;
				else self::$JavascriptPlusone = true;

			$addLanguage     = '';
			$addURLforScript = '';
	
			// Check if instance of google plus is active otherwise
			// insert the code without customization parameters

			if ($object = self::getObject('SZGoogleModulePlus')) 
			{
				$options = (object) $object->getOptions();

				// If in the form of Google+ has been shown not to load 
				// the javascript framework of google leave the function

				if ($options->plus_system_javascript == '1') return;

				// Check the language code to associate with the javascript
				// framework, if you see "99" I take the language code of wordpress

				if ($options->plus_language == '99') $addLanguage = substr(get_bloginfo('language'),0,2);	
					else $addLanguage = $options->plus_language;

				// Checking if I have to turn the recommendations of the mobile, then add publisher id
				// without the publisher's default function turned off or do not add anything

				if ($options->plus_enable_recommendations == '1' and $options->plus_page != '') {
					$addURLforScript = "?publisherid=".trim($options->plus_page);
				}
			}

			// Javascript code to render the component google+
			// this method is used for asynchronous loading

			$javascript  = '<script type="text/javascript">';

			if ($addLanguage != '') $javascript .= "window.___gcfg = {lang:'".trim($addLanguage)."'};";

			$javascript .= "(function() {";
			$javascript .= "var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;";
			$javascript .= "po.src = 'https://apis.google.com/js/plusone.js".$addURLforScript."';";
			$javascript .=  "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);";
			$javascript .=  "})();";
			$javascript .=	"</script>"."\n";

			// Running echo on the footer of the javascript code generated
			// This code is added to a single block together with other functions

			echo $javascript;
		}

		/**
		 * Function to add javascript code in the footer
		 * to add a function that controls the lazy load
		 */

		function setJavascriptLazyLoad()
		{
			// If you've already entered the Javascript code in the footer section
			// leave the partition function otherwise the variable and constant

			if (self::$JavascriptLazyLoad) return;
				else self::$JavascriptLazyLoad = true;

			// Javascript code to render the component google+
			// this method is used for asynchronous loading

			$javascript  = '<script type="text/javascript">';

			$javascript .= 'function szgooglecheckviewport(el) {';

			$javascript .=   'var top=el.offsetTop;';
			$javascript .=   'var left=el.offsetLeft;';
			$javascript .=   'var width=el.offsetWidth;';
			$javascript .=   'var height=el.offsetHeight;';

			$javascript .=   'while(el.offsetParent) {';
			$javascript .=     'el=el.offsetParent;';
			$javascript .=     'top+=el.offsetTop;';
			$javascript .=     'left+=el.offsetLeft;';
			$javascript .=   '}';

			$javascript .=   'return (';
			$javascript .=     'top<(window.pageYOffset+window.innerHeight) && ';
			$javascript .=     'left<(window.pageXOffset+window.innerWidth) && ';
			$javascript .=     '(top+height)>window.pageYOffset && ';
			$javascript .=     '(left+width)>window.pageXOffset';
			$javascript .=   ');';

			$javascript .= '}';

			$javascript .=	"</script>"."\n";

			// Running echo on the footer of the javascript code generated
			// This code is added to a single block together with other functions

			echo $javascript;
		}

		/**
		 * Creating the CSS code for the composition of the margins
		 * using the options specified in the shortcode or PHP functions
		 */

		function getStyleCSSfromAlign($align)
		{
			// If you do not specify a valid alignment values is set to
			// the special value "left" and will be applied to the text

			if (!in_array(strtolower($align),array('left','right','center'))) $align = 'none'; 
				else $align = strtolower($align);

			if (empty($align) or $align == 'none') return ''; 
				else return 'text-align:'.$align.';';
		}

		/**
		 * Creating the CSS code for the composition of the margins
		 * using the options specified in the shortcode or PHP functions
		 */

		function getStyleCSSfromMargins($margintop,$marginright,$marginbottom,$marginleft,$marginunit)
		{
			// If you do not specify a correct unit of measure will be
			// set to the special value "em" and will be applied at the edge

			if (!in_array(strtolower($marginunit),array('pt','px','em'))) $marginunit = 'em'; 
				else $marginunit = strtolower($marginunit);

			// Enforced default values if they are specified values
			// that do not belong to the range of acceptable values

			if (!ctype_digit($margintop)    and $margintop    != 'none') $margintop    = ''; 
			if (!ctype_digit($marginright)  and $marginright  != 'none') $marginright  = ''; 
			if (!ctype_digit($marginbottom) and $marginbottom != 'none') $marginbottom = '1';
			if (!ctype_digit($marginleft)   and $marginleft   != 'none') $marginleft   = ''; 

			// Creating the CSS code for the composition of the margins
			// using the options specified in the shortcode or PHP functions

			$HTML = '';

			if (!empty($margintop)    and $margintop    != 'none') $HTML .= 'margin-top:'   .$margintop   .$marginunit.';';
			if (!empty($marginright)  and $marginright  != 'none') $HTML .= 'margin-right:' .$marginright .$marginunit.';';
			if (!empty($marginbottom) and $marginbottom != 'none') $HTML .= 'margin-bottom:'.$marginbottom.$marginunit.';';
			if (!empty($marginleft)   and $marginleft   != 'none') $HTML .= 'margin-left:'  .$marginleft  .$marginunit.';';

			return $HTML;
		}

		/**
		 * Static function to retrieve the pointer object of a
		 * specific module in order to call methods from the outside
		 */

		static function getObject($object) {
			if (is_a(self::${$object},$object)) return self::${$object};
				else return false;
		}
	}
}