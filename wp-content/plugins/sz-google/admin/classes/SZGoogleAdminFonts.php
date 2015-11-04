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

if (!class_exists('SZGoogleAdminFonts'))
{
	class SZGoogleAdminFonts extends SZGoogleAdmin
	{
		/**
		 * Definizione delle variabili che contengono le configurazioni
		 * specifiche sulla creazione dell'istanza corrente
		 */
		protected $fontslist = '';

		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-fonts.php';
			$this->pagetitle  = ucwords(__('google fonts','sz-google'));
			$this->menutitle  = ucwords(__('google fonts','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general' ,'description' => __('general','sz-google')),
				'02' => array('anchor' => 'HTML'    ,'description' => __('HTML'   ,'sz-google')),
				'03' => array('anchor' => 'header'  ,'description' => __('header' ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-fonts.php'   ,'title' => ucwords(__('fonts tinyMCE' ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-fonts-LO.php','title' => ucwords(__('fonts loader'  ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-fonts-BX.php','title' => ucwords(__('fonts HTML'    ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-fonts-HX.php','title' => ucwords(__('fonts headings','sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_fonts');

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section
			
			parent::moduleAddMenu();
		}

		/**
		 * Function to add sections and the corresponding options in the configuration
		 * page, each option belongs to a section, which is linked to a general tab 
		 */

		function moduleAddFields()
		{
			// General definition array containing a list of sections
			// On every section you have to define an array to list fields

			$this->sectionsmenu = array(
				'01' => array('section' => 'sz_google_fonts_tinymce'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-fonts.php'),
				'02' => array('section' => 'sz_google_fonts_section'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-fonts-LO.php'),
				'03' => array('section' => 'sz_google_fonts_section_BX','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-fonts-BX.php'),
				'04' => array('section' => 'sz_google_fonts_section_HX','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-fonts-HX.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'fonts_tinyMCE_F','title' => ucfirst(__('tinyMCE Font Family','sz-google'))                                  ,'callback' => array($this,'callback_fonts_tinymce_f')),
				              array('field' => 'fonts_tinyMCE_S','title' => ucfirst(__('tinyMCE Font Size'  ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_tinymce_s')),),
				'02' => array(array('field' => 'fonts_family_L1','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L1')),
				              array('field' => 'fonts_family_L2','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L2')),
				              array('field' => 'fonts_family_L3','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L3')),
				              array('field' => 'fonts_family_L4','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L4')),
				              array('field' => 'fonts_family_L5','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L5')),
				              array('field' => 'fonts_family_L6','title' => ucfirst(__('font family'        ,'sz-google'))                                  ,'callback' => array($this,'callback_fonts_family_L6')),),
				'03' => array(array('field' => 'fonts_family_B1','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <body>'))      ,'callback' => array($this,'callback_fonts_family_B1')),
				              array('field' => 'fonts_family_P1','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <p>'))         ,'callback' => array($this,'callback_fonts_family_P1')),
				              array('field' => 'fonts_family_B2','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <blockquote>')),'callback' => array($this,'callback_fonts_family_B2')),),
				'04' => array(array('field' => 'fonts_family_H1','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h1>'))        ,'callback' => array($this,'callback_fonts_family_H1')),
				              array('field' => 'fonts_family_H2','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h2>'))        ,'callback' => array($this,'callback_fonts_family_H2')),
				              array('field' => 'fonts_family_H3','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h3>'))        ,'callback' => array($this,'callback_fonts_family_H3')),
				              array('field' => 'fonts_family_H4','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h4>'))        ,'callback' => array($this,'callback_fonts_family_H4')),
				              array('field' => 'fonts_family_H5','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h5>'))        ,'callback' => array($this,'callback_fonts_family_H5')),
				              array('field' => 'fonts_family_H6','title' => ucfirst(__('font family'        ,'sz-google').htmlspecialchars(' <h6>'))        ,'callback' => array($this,'callback_fonts_family_H6')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Function to read from the database json all font families
		 * available on the CDN google font list and return arrays
		 */

		function getGoogleFontsList()
		{
			// If you 've already done this list of fonts, return
			// array connected to the variable and jump processing

			if($this->fontslist != '')
				return $this->fontslist;

			// If this is the first time it is called this work
			// I run the file processing data in json format with fonts

			$file = file_get_contents(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/data/webfonts.json');
			$file = json_decode($file,true);

			$this->fontslist = array(
				'nofonts' => __('no fonts','sz-google')
			);

			foreach ($file['items'] as $key=>$name) {
				$fontsfamily = $name['family'];
				$this->fontslist[$fontsfamily] = $fontsfamily;
			}

			return $this->fontslist;
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_fonts_tinymce_f() {
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_fonts','fonts_tinyMCE_family');
			$this->moduleCommonFormDescription(__('enabling this option will be added in the main menu of TinyMCE selector with a list of family fonts, which can be associated with the paragraphs of your article. The list addition is defined in the standard software of TinyMCE.','sz-google'));
		}

		function callback_fonts_tinymce_s() {
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_fonts','fonts_tinyMCE_size');
			$this->moduleCommonFormDescription(__('enabling this option will be added in the main menu of TinyMCE selector with a list of family size, which can be associated with the paragraphs of your article. The list addition is defined in the standard software of TinyMCE.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_fonts_family_L1() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L1_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		function callback_fonts_family_L2() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L2_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		function callback_fonts_family_L3() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L3_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		function callback_fonts_family_L4() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L4_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		function callback_fonts_family_L5() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L5_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		function callback_fonts_family_L6() 
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_L6_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the name of a font to create download from google CDN. This option is only concerned of loading, the assignment you have to manually enter in your CSS file. If you want to do it all automatically use the options found below.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_fonts_family_B1()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_B1_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_P1()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_P1_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_B2()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_B2_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_fonts_family_H1()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H1_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_H2()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H2_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_H3()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H3_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_H4()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H4_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_H5()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H5_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}

		function callback_fonts_family_H6()
		{
			$values = $this->getGoogleFontsList();
			$this->moduleCommonFormSelect('sz_google_options_fonts','fonts_family_H6_name',$values,'medium','');
			$this->moduleCommonFormDescription(__('choose the font name to associate with the HTML indicated, the plugin will generate the code to download the font from the google CDN and CSS code to link with the specified HTML element. It is not necessary to change the original CSS file.','sz-google'));
		}
	}
}