<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleAdmin'))
{
	class SZGoogleAdmin
	{
		/**
		 * Definition of variables containing the configuration
		 * to be applied to the various function calls wordpress
		 */

		protected $titlefix        = 'Google for WordPress - ';
		protected $capability      = 'manage_options';
		protected $parentslug      = 'sz-google-admin.php';

		/**
		 * Definition of variables containing the configuration
		 * to be applied to the various function calls wordpress
		 */

		protected $null            = '';
		protected $pagetitle       = '';
		protected $menutitle       = '';
		protected $menuslug        = '';
		protected $sections        = '';
		protected $sectionsmenu    = '';
		protected $sectionsfields  = '';
		protected $sectionstabs    = '';
		protected $sectionstitle   = '';
		protected $sectionsgroup   = '';
		protected $sectionsoptions = '';
		protected $validate        = '';
		protected $callback        = '';
		protected $callbackstart   = '';
		protected $callbacksection = '';
		protected $formHTML        = '';
		protected $formsavebutton  = '1';

		/**
		 * Definition of variables containing the configuration
		 * set during the call to the function moduleAddSetup()
		 */

		private $moduleClassName  = false;
		private $moduleOptions    = false;
		private $moduleOptionSet  = false;

		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			// Setting variables for the correct processing of module,
			// options can be redefined in the function moduleSetup()

			$this->validate        = array($this,'moduleValidate');
			$this->callback        = array($this,'moduleCallback');
			$this->callbackstart   = array($this,'moduleCallbackStart');
			$this->callbacksection = array($this,'moduleCallbackSection');

			// Definition actions for creating the wordpress admin menu 
			// and options page for variables of configuration module 

			add_action('admin_menu',array($this,'moduleAddMenu'));
			add_action('admin_init',array($this,'moduleAddFields'));
 		}

		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			if (function_exists('add_submenu_page')) 
			{
				$pagehook = add_submenu_page($this->parentslug,$this->titlefix.$this->pagetitle,
					$this->menutitle,$this->capability,$this->menuslug,$this->callback);
				add_action('admin_print_scripts-'.$pagehook,array($this,'moduleAddJavascriptPlugins'));
			}
 		}

		/**
		 * Function to add sections and the corresponding options in the configuration
		 * page, each option belongs to a section, which is linked to a general tab 
		 */

		function moduleAddFields()
		{
			if (!is_array($this->sectionsoptions)) $this->sectionsoptions = array();
			if (!is_array($this->sectionsmenu))    $this->sectionsmenu    = array();
			if (!is_array($this->sectionsfields))  $this->sectionsfields  = array();

			// Loading options in reference to a predefined group 
			// that will be invoked in the final form

			foreach ($this->sectionsoptions as $value) { 
				register_setting($this->sectionsoptions[0],$value); 
			}				

			// General reading array containing a list of sections
			// On every section you have to define an array to list fields

			foreach($this->sectionsmenu as $key=>$value) {
				add_settings_section($value['section'],$value['title'],$value['callback'],$value['slug']);
			}

			// General reading array containing a list of fields
			// that must be added to the sections previously defined

			foreach($this->sectionsfields as $key=>$sectionsfield) {
				foreach($sectionsfield as $value) {
					add_settings_field($value['field'],$value['title'],$value['callback'],$this->sectionsmenu[$key]['slug'],$this->sectionsmenu[$key]['section']);
				}
			}
 		}

		/**
		 * Call the general function for creating the general form,
		 * sections must be passed as an array of name = > title
		 */

		function moduleCallback()
		{
			$this->moduleCommonForm($this->sectionstitle,$this->sectionsoptions,
				$this->sections,$this->formsavebutton,$this->formHTML
			); 
		}

		/**
		 * Defining a callback dummy for the section that is developed
		 * during the definition of the sections and input fields		
		 */

		function moduleCallbackStart()
		{
		}

		/**
		 * Defining a callback dummy for the section that is developed
		 * during the definition of the sections and input fields		
		 */

		function moduleCallbackSection()
		{
		}

		/**
		 * Defining a callback dummy for the section that is developed
		 * during the definition of the sections and input fields		
		 */

		function moduleValidate($options) {
	  		return $options;
		}

		/**
		 * Add the plugin javascript required only in admin pages that
		 * are related to the plugin sz-google and associated loading
		 */

		function moduleAddJavascriptPlugins() 
		{
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('postbox');
			wp_enqueue_script('utils');
			wp_enqueue_script('dashboard');
			wp_enqueue_script('thickbox');
		}

		/**
		 * Calculate the name of the current administration page
		 * which can be useful for loading specific modules
		 */

		function moduleAdminGetPageNow() {
			global $pagenow;
			return $pagenow;
		}

		/**
		 * Calculate the name of the current administration page
		 * which can be useful for loading specific modules
		 */

		function moduleAdminGetAdminPage() {
			if (isset($_GET['page'])) return $_GET['page']; 
				else return '';
		}

		/**
		 * Calculation options related to the module with execution 
		 * of formal checks of consistency and setting the default
		 */

		function getOptions()
		{ 
			if ($this->moduleOptions) return $this->moduleOptions;
				else $this->moduleOptions = $this->getOptionsSet($this->moduleOptionSet);

			// Return back the correct set of options from
			// formal checks executed by the control function

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

			// Control options in the event of non-existing
			// function call to the control isset()

			foreach($optionsList as $key => $item) 
			{
				// Control options in the event of non-existing
				// otherwise add the field in the original array

				if (!isset($optionsDB[$key])) $optionsDB[$key] = $item['value'];

				// Check if the option field contains a value of NULL
				// in this case check the value option the default

				if (isset($item['N']) and $item['N'] == '1') {
					if ($optionsDB[$key] == '') $optionsDB[$key] = $item['value'];
				}

				// Check if the option field contains a value of ZERO
				// in this case check the value option the default

				if (isset($item['Z']) and $item['Z'] == '1') {
					if ($optionsDB[$key] == '0') $optionsDB[$key] = $item['value'];
				}

				// Check if the option field contains a value of YES/NO
				// in this case check the value option the default

				if (isset($item['Y']) and $item['Y'] == '1') {
					if (!in_array($optionsDB[$key],array('1','0'))) $optionsDB[$key] = '0';
				}
			}

			// Back to list the options related to the specified set,
			// convert array to object for direct access

			return (object) $optionsDB;
		}

		/**
		 * Functions for assigning values ​​that are used for initial 
		 * setup of the module as the class name and the set of options
		 */

		function moduleSetClassName($classname) { $this->moduleClassName = $classname; }
		function moduleSetOptionSet($nameset)   { $this->moduleOptionSet = $nameset;   }

		/**
		 * Defining the function to draw the general form of the
		 * pages present in the admin panel with plugin options
		 */

		function moduleCommonForm($title,$group,$sections,$formsavebutton,$HTML)
		{
			// HTML code creation for the main container to which
			// you add a title, system notifications and any tabs

			echo '<div id="sz-google-wrap" class="wrap">';
			echo '<h2>'.ucwords($title).'</h2>';

			// Issue messages after setting the title, such as
			// the update message of the options in configuration

			settings_errors();

			// Main container area designated for configuration parameters
			// defined by the calls of individual modules enabled from admin panel

			echo '<div class="postbox-container" id="sz-google-admin-options">';
			echo '<div class="metabox-holder">';
			echo '<div class="meta-box-sortables ui-sortable" id="sz-google-box">';

			// If the call contains an array of documentation do I turn off
			// the form for editing parameters, because it is read-only

			if ($formsavebutton == '1') {
				echo '<form method="post" action="options.php" enctype="multipart/form-data">';
				echo '<input type="hidden" name="sz_google_options_plus[plus_redirect_flush]" value="0">';
			}

			// If the call does not contain an array of documentation, 
			// I run the creation of the HTML with all fields option to edit

			if ($HTML != '') 
			{
				echo '<div class="postbox">'; 
				echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
				echo '<h3 class="hndle"><span>'.ucfirst(__('documentation','sz-google')).'</span></h3>';
				echo '<div class="inside">';
				echo '<div class="help">';
				echo $HTML;
				echo '</div>';
				echo '</div>';
				echo '</div>';

			} else {

				// Creating session of the form to add the list of
				// fields in hidden that are needed to submission

				settings_fields($group[0]);

				// Composition model with the specified tab in sectionstabs
				// otherwise execute the composition of the HTML sections

				if (is_array($this->sectionstabs)) 
				{
					// Composition of the HTML title H2 with various tabs
					// that make up the configuration page of the module

					echo '<h2 id="sz-google-tab" class="nav-tab-wrapper">';

					foreach ($this->sectionstabs as $TABKey => $TABValue) {
						echo '<a class="nav-tab" ';
						echo 'id="sz-google-tab-'.$TABValue['anchor'].'" ';
						echo 'href="#'.$TABValue['anchor'].'"';
						echo '>'.ucfirst($TABValue['description']).'</a>';
					}
				
					echo '</h2>';

					// For each tab that I find in array design section HTML
					// in hidden mode that will be activated by selecting the tab

					foreach ($this->sectionstabs as $TABKey => $TABValue) 
					{
						echo '<div class="sz-google-tab-div" id="sz-google-tab-div-'.$TABValue['anchor'].'">';

						foreach ($sections as $key => $value) 
						{
							if ($TABKey == $value['tab']) {
								echo '<div class="postbox">'; 
								echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
								echo '<h3 class="hndle"><span>'.$value['title'].'</span></h3>';
								echo '<div class="inside">';
								do_settings_sections($value['section']);
								echo '</div>';
								echo '</div>';
							}
						}

						echo '</div>';
					}

				// Composition simple model without reading array of tabs,
				// I write the HTML sections in order to define standard

				} else {

					foreach ($sections as $key => $value)	
					{
						echo '<div class="postbox">'; 
						echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
						echo '<h3 class="hndle"><span>'.$value['title'].'</span></h3>';
						echo '<div class="inside">';
						do_settings_sections($value['section']);
						echo '</div>';
						echo '</div>';
					}
				}
			}

			// If the call contains an array of documentation do I turn off the
			// form for the modification of parameters, because it is read-only

			if ($formsavebutton == '1') {
				echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="'.ucfirst(__('save changes','sz-google')).'"/></p>';
				echo '</form>';
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';

			// Secondary container with information of the authors and some
			// links, such as the community of Italian wordpress for ever :)

			echo '<div class="postbox-container" id="sz-google-admin-sidebar">';
			echo '<div class="metabox-holder">';
			echo '<div class="meta-box-sortables ui-sortable">';

			// Section on the sidebar menu present on the right side
			// Definition of the menu that will contain "Give us a little help"

			echo '<div id="help-us" class="postbox">';
			echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
			echo '<h3 class="hndle"><span><strong>'.ucwords(__('give us a little help','sz-google')).'</strong></span></h3>';
			echo '<div class="inside">';
			echo '<ul>';
			echo '<li><a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/sz-google">'.ucfirst(__('rate the plugin','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="https://plus.google.com/communities/109254048492234113886">'.ucfirst(__('join our community','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="https://plus.google.com/+wpitalyplus">'.ucfirst(__('join our google+ page','sz-google')).'</a></li>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';

			// Section on the sidebar menu present on the right side
			// Definition of the menu that will contain "Official page"

			echo '<div id="official-plugin" class="postbox">';
			echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
			echo '<h3 class="hndle"><span><strong>'.ucwords(__('official page','sz-google')).'</strong></span></h3>';
			echo '<div class="inside">';
			echo '<a target="_blank" href="https://plus.google.com/+wpitalyplus"><img src="'.plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'frontend/files/images/wpitalyplus.png'.'" alt="WordPress Italy+" style="width:100%;height:auto;vertical-align:bottom;"></a>';
			echo '</div>';
			echo '</div>';

			// Section on the sidebar menu present on the right side
			// Definition of the menu that will contain "Request for support"

			echo '<div id="support-plugin" class="postbox">';
			echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
			echo '<h3 class="hndle"><span><strong>'.ucwords(__('support','sz-google')).'</strong></span></h3>';
			echo '<div class="inside">';
			echo '<ul>';
			echo '<li><a target="_blank" href="http://wordpress.org/support/plugin/sz-google">'.ucfirst(__('support for bugs and reports','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="https://plus.google.com/communities/109254048492234113886">'.ucfirst(__('support for new requests','sz-google')).'</a></li>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';

			// Section on the sidebar menu present on the right side
			// Definition of the menu that will contain "Authors of the plugin"

			echo '<div id="authors-plugin" class="postbox">';
			echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
			echo '<h3 class="hndle"><span><strong>'.ucwords(__('authors','sz-google')).'</strong></span></h3>';
			echo '<div class="inside">';
			echo '<ul>';
			echo '<li><a target="_blank" href="https://plus.google.com/+MassimoDellaRovere">Massimo Della Rovere</a></li>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';

			// Section on the sidebar menu present on the right side
			// Definition of the menu that will contain "About the plugin"

			echo '<div id="info-plugin" class="postbox">';
			echo '<div class="handlediv" title="'.ucfirst(__('click to toggle','sz-google')).'"><br></div>';
			echo '<h3 class="hndle"><span><strong>'.ucwords(__('latest news','sz-google')).'</strong></span></h3>';
			echo '<div class="inside">';
			echo '<ul>';
			echo '<li><a target="_blank" href="https://plus.google.com/+wpitalyplus">'.ucfirst(__('news:','sz-google'))."&nbsp;".ucfirst(__('official page','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="https://otherplus.com/tech/wordpress-google/">'.ucfirst(__('news:','sz-google'))."&nbsp;".ucfirst(__('official website','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="https://plus.google.com/communities/109254048492234113886">'.ucfirst(__('news:','sz-google'))."&nbsp;".ucfirst(__('community WordPress','sz-google')).'</a></li>';
			echo '<li><a target="_blank" href="http://www.youtube.com/user/wpitalyplus?sub_confirmation=1">'.ucfirst(__('news:','sz-google'))."&nbsp;".ucfirst(__('youtube channel','sz-google')).'</a></li>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';

			// Section on the sidebar menu present on the right side
			// Closing the main HTML container for main sidebar right

			echo '</div>';
			echo '</div>';
			echo '</div>';

			echo '</div>';
		}

		/**
		 * Description option to be included in the input field
		 * present in the general form of the pages with the options
		 */

		function moduleCommonFormDescription($description) 
		{
			echo '</td></tr>';
			echo '<tr><td class="description" colspan="2">';
			echo ucfirst(trim($description));
		}

		/**
		 * Description TEXT field to be inserted in the input field
		 * present in the general form of the pages with the options
		 */

		function moduleCommonFormText($optionset,$name,$class='medium',$placeholder='') 
		{	
			$options = get_option($optionset);

			if (!isset($options[$name])) $options[$name] = '';
				else $options[$name] =  esc_html($options[$name]);

			echo '<input name="'.$optionset.'['.$name.']" type="text" class="'.$class.'" ';
			echo 'value="'.$options[$name].'" placeholder="'.$placeholder.'"/>';
		}

		/**
		 * Description SELECT field to be inserted in the input field
		 * present in the general form of the pages with the options
		 */

		function moduleCommonFormSelect($optionset,$name,$values,$class='medium',$placeholder='') 
		{
			$options = get_option($optionset);

			if (!isset($options[$name])) $options[$name] = ""; 
			if (!isset($options['plus_language'])) $options['plus_language'] = '99';

			echo '<select name="'.$optionset.'['.$name.']" class="'.$class.'">';

			foreach ($values as $key=>$value) {
				$selected = ($options[$name] == $key) ? ' selected = "selected"' : '';
				echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
			}

			echo '</select>';
		}

		/**
		 * Description YES/NO field to be inserted in the input field
		 * present in the general form of the pages with the options
		 */

		function moduleCommonFormCheckboxYesNo($optionset,$name,$class='medium') 
		{
			$options = get_option($optionset);

			if (!isset($options[$name])) $options[$name] = '0';

			echo '<input type="hidden" name="'.$optionset.'['.$name.']" value="0"/>';
			echo '<label class="sz-google"><input name="'.$optionset.'['.$name.']" type="checkbox" value="1" ';
			echo 'class="'.$class.'" '.checked(1,$options[$name],false).'/><span class="checkbox" style="display:none">'.__('YES / NO','sz-google').'</span></label>';
		}

		/**
		 * Description NUMERIC field to be inserted in the input field
		 * present in the general form of the pages with the options
		 */

		function moduleCommonFormNumberStep1($optionset,$name,$class='medium',$placeholder='') 
		{
			$options = get_option($optionset);

			if (!isset($options[$name])) $options[$name]=""; 

			echo '<input name="'.$optionset.'['.$name.']" type="number" step="1" class="'.$class.'" ';
			echo 'value="'.$options[$name].'" placeholder="'.$placeholder.'"/>';
		}
	}
}