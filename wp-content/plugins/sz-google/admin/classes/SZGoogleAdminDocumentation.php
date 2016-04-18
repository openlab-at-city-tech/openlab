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

if (!class_exists('SZGoogleAdminDocumentation'))
{
	class SZGoogleAdminDocumentation extends SZGoogleAdmin
	{
		/**
		 * Definition of variables that contain the configurations
		 * to be applied to various processing of the current class
		 */

		protected $HelpIndexItems = '';

		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-documentation.php';
			$this->pagetitle  = ucwords(__('documentation','sz-google'));
			$this->menutitle  = ucwords(__('documentation','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general','description' => __('general','sz-google')),
				'02' => array('anchor' => 'reviews','description' => __('reviews','sz-google')),
				'03' => array('anchor' => 'modules','description' => __('modules','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-documentation-gplus.php'        ,'title' => ucwords(__('google+'             ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-analytics.php'    ,'title' => ucwords(__('google analytics'    ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-authenticator.php','title' => ucwords(__('google authenticator','sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-calendar.php'     ,'title' => ucwords(__('google calendar'     ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-drive.php'        ,'title' => ucwords(__('google drive'        ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-groups.php'       ,'title' => ucwords(__('google groups'       ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-hangouts.php'     ,'title' => ucwords(__('google hangouts'     ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-panoramio.php'    ,'title' => ucwords(__('google panoramio'    ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-translate.php'    ,'title' => ucwords(__('google translate'    ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-documentation-youtube.php'      ,'title' => ucwords(__('youtube'             ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-documentation-reviews.php'      ,'title' => ucwords(__('reviews'             ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-documentation-modules.php'      ,'title' => ucwords(__('modules'             ,'sz-google'))),
			);

			$this->formsavebutton  = '0';
			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_documentation');

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddMenu();

			// Definition array containing the main structure of the documentation
			// Creating index of documentation for the composition of the navigator

			$this->HelpIndexItems = array(
					array('slug'=>'sz-google-help-plus-author.php'            ,'title'=>__('google+ badge author'      ,'sz-google')),
					array('slug'=>'sz-google-help-plus-profile.php'           ,'title'=>__('google+ badge profile'     ,'sz-google')),
					array('slug'=>'sz-google-help-plus-page.php'              ,'title'=>__('google+ badge page'        ,'sz-google')),
					array('slug'=>'sz-google-help-plus-community.php'         ,'title'=>__('google+ badge community'   ,'sz-google')),
					array('slug'=>'sz-google-help-plus-plusone.php'           ,'title'=>__('google+ button +1'         ,'sz-google')),
					array('slug'=>'sz-google-help-plus-share.php'             ,'title'=>__('google+ button share'      ,'sz-google')),
					array('slug'=>'sz-google-help-plus-follow.php'            ,'title'=>__('google+ button follow'     ,'sz-google')),
					array('slug'=>'sz-google-help-plus-comments.php'          ,'title'=>__('google+ widget comments'   ,'sz-google')),
					array('slug'=>'sz-google-help-plus-post.php'              ,'title'=>__('google+ embedded post'     ,'sz-google')),
					array('slug'=>'sz-google-help-plus-followers.php'         ,'title'=>__('google+ badge followers'   ,'sz-google')),
					array('slug'=>'sz-google-help-plus-author-publisher.php'  ,'title'=>__('google+ author & publisher','sz-google')),
					array('slug'=>'sz-google-help-plus-redirect.php'          ,'title'=>__('google+ redirect'          ,'sz-google')),
					array('slug'=>'sz-google-help-plus-recommendations.php'   ,'title'=>__('google+ recommendations'   ,'sz-google')),
					array('slug'=>'sz-google-help-ga-setup.php'               ,'title'=>__('analytics setup'           ,'sz-google')),
					array('slug'=>'sz-google-help-ga-functions.php'           ,'title'=>__('analytics PHP functions'   ,'sz-google')),
					array('slug'=>'sz-google-help-authenticator-setup.php'    ,'title'=>__('authenticator setup'       ,'sz-google')),
					array('slug'=>'sz-google-help-authenticator-functions.php','title'=>__('authenticator PHP'         ,'sz-google')),
					array('slug'=>'sz-google-help-authenticator-device.php'   ,'title'=>__('authenticator device'      ,'sz-google')),
					array('slug'=>'sz-google-help-calendar.php'               ,'title'=>__('widget calendar'           ,'sz-google')),
					array('slug'=>'sz-google-help-drive-embed.php'            ,'title'=>__('drive embed'               ,'sz-google')),
					array('slug'=>'sz-google-help-drive-viewer.php'           ,'title'=>__('drive viewer'              ,'sz-google')),
					array('slug'=>'sz-google-help-drive-save.php'             ,'title'=>__('drive save button'         ,'sz-google')),
					array('slug'=>'sz-google-help-groups.php'                 ,'title'=>__('widget groups'             ,'sz-google')),
					array('slug'=>'sz-google-help-hangout-start.php'          ,'title'=>__('hangout start button'      ,'sz-google')),
					array('slug'=>'sz-google-help-panoramio.php'              ,'title'=>__('widget panoramio'          ,'sz-google')),
					array('slug'=>'sz-google-help-translate.php'              ,'title'=>__('translate setup'           ,'sz-google')),
					array('slug'=>'sz-google-help-translate-functions.php'    ,'title'=>__('translate PHP functions'   ,'sz-google')),
					array('slug'=>'sz-google-help-youtube-video.php'          ,'title'=>__('youtube video'             ,'sz-google')),
					array('slug'=>'sz-google-help-youtube-playlist.php'       ,'title'=>__('youtube playlist'          ,'sz-google')),
					array('slug'=>'sz-google-help-youtube-badge.php'          ,'title'=>__('youtube badge'             ,'sz-google')),
					array('slug'=>'sz-google-help-youtube-link.php'           ,'title'=>__('youtube link'              ,'sz-google')),
					array('slug'=>'sz-google-help-youtube-button.php'         ,'title'=>__('youtube button'            ,'sz-google')),
			);
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
				'01' => array('section' => 'sz_google_documentation_gplus'        ,'title' => $this->null,'callback' => array($this,'moduleAddHelpPlus')         ,'slug' => 'sz-google-admin-documentation-gplus.php'),
				'02' => array('section' => 'sz_google_documentation_analytics'    ,'title' => $this->null,'callback' => array($this,'moduleAddHelpAnalytics')    ,'slug' => 'sz-google-admin-documentation-analytics.php'),
				'03' => array('section' => 'sz_google_documentation_authenticator','title' => $this->null,'callback' => array($this,'moduleAddHelpAuthenticator'),'slug' => 'sz-google-admin-documentation-authenticator.php'),
				'04' => array('section' => 'sz_google_documentation_calendar'     ,'title' => $this->null,'callback' => array($this,'moduleAddHelpCalendar')     ,'slug' => 'sz-google-admin-documentation-calendar.php'),
				'05' => array('section' => 'sz_google_documentation_drive'        ,'title' => $this->null,'callback' => array($this,'moduleAddHelpDriveSave')    ,'slug' => 'sz-google-admin-documentation-drive.php'),
				'06' => array('section' => 'sz_google_documentation_groups'       ,'title' => $this->null,'callback' => array($this,'moduleAddHelpGroups')       ,'slug' => 'sz-google-admin-documentation-groups.php'),
				'07' => array('section' => 'sz_google_documentation_hangouts'     ,'title' => $this->null,'callback' => array($this,'moduleAddHelpHangouts')     ,'slug' => 'sz-google-admin-documentation-hangouts.php'),
				'08' => array('section' => 'sz_google_documentation_panoramio'    ,'title' => $this->null,'callback' => array($this,'moduleAddHelpPanoramio')    ,'slug' => 'sz-google-admin-documentation-panoramio.php'),
				'09' => array('section' => 'sz_google_documentation_translate'    ,'title' => $this->null,'callback' => array($this,'moduleAddHelpTranslate')    ,'slug' => 'sz-google-admin-documentation-translate.php'),
				'10' => array('section' => 'sz_google_documentation_youtube'      ,'title' => $this->null,'callback' => array($this,'moduleAddHelpYoutube')      ,'slug' => 'sz-google-admin-documentation-youtube.php'),
				'11' => array('section' => 'sz_google_documentation_reviews'      ,'title' => $this->null,'callback' => array($this,'moduleAddHelpReviews')      ,'slug' => 'sz-google-admin-documentation-reviews.php'),
				'12' => array('section' => 'sz_google_documentation_modules'      ,'title' => $this->null,'callback' => array($this,'moduleAddHelpModules')      ,'slug' => 'sz-google-admin-documentation-modules.php'),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Call the general function for the creation of the general form
		 * sections must be passed as an array of name = > title
		 */

		function moduleCallback()
		{
			// Check if you specify a section of the help documentation
			// in the directory of the file and if it is existing

			if (isset($_GET['help'])) 
			{
				$LANGUAGE = get_bloginfo('language');
				$FILENAM1 = dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/help/'.$LANGUAGE.'/'.trim($_GET['help']);
				$FILENAM2 = dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/help/'.substr($LANGUAGE,0,2).'/'.trim($_GET['help']);
				$FILENAM3 = dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/help/en/'.trim($_GET['help']);

				if (is_readable($FILENAM1)) { @include($FILENAM1); return; }
				if (is_readable($FILENAM2)) { @include($FILENAM2); return; }
				if (is_readable($FILENAM3)) { @include($FILENAM3); return; }
			}

			// If you can not find any specific
			// documentation files is called the main page

			parent::moduleCallback();
		}

		/**
		 * Function to add the icons section with arrays containing
		 * the slug of the link and the title of the document
		 */

		function moduleAddHelpLinks($options)
		{
			echo '<div class="help-index">';

			foreach ($options as $key => $value) 
			{
				echo '<div class="help-items">';
				echo '<div class="help-image"><a href="'.menu_page_url($this->menuslug,false).'&amp;help='.$value['slug'].'"><img src="'.plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/images/help/'.basename($value['slug'],".php").'.jpg" alt=""></a></div>';
				echo '<div class="help-title"><a href="'.menu_page_url($this->menuslug,false).'&amp;help='.$value['slug'].'">'.ucwords($value['title']).'</a></div>';
				echo '</div>';
			}

			echo '</div>';
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE PLUS
		 */

		function moduleAddHelpPlus()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-plus-author.php'           ,'title'=>__('badge author'      ,'sz-google')),
				array('slug'=>'sz-google-help-plus-profile.php'          ,'title'=>__('badge profile'     ,'sz-google')),
				array('slug'=>'sz-google-help-plus-page.php'             ,'title'=>__('badge page'        ,'sz-google')),
				array('slug'=>'sz-google-help-plus-community.php'        ,'title'=>__('badge community'   ,'sz-google')),
				array('slug'=>'sz-google-help-plus-plusone.php'          ,'title'=>__('button +1'         ,'sz-google')),
				array('slug'=>'sz-google-help-plus-share.php'            ,'title'=>__('button share'      ,'sz-google')),
				array('slug'=>'sz-google-help-plus-follow.php'           ,'title'=>__('button follow'     ,'sz-google')),
				array('slug'=>'sz-google-help-plus-comments.php'         ,'title'=>__('widget comments'   ,'sz-google')),
				array('slug'=>'sz-google-help-plus-post.php'             ,'title'=>__('embedded post'     ,'sz-google')),
				array('slug'=>'sz-google-help-plus-followers.php'        ,'title'=>__('badge followers'   ,'sz-google')),
				array('slug'=>'sz-google-help-plus-author-publisher.php' ,'title'=>__('author & publisher','sz-google')),
				array('slug'=>'sz-google-help-plus-redirect.php'         ,'title'=>__('redirect +'        ,'sz-google')),
				array('slug'=>'sz-google-help-plus-recommendations.php'  ,'title'=>__('recommendations'   ,'sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE ANALYTICS
		 */

		function moduleAddHelpAnalytics()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-ga-setup.php'    ,'title'=>__('analytics setup','sz-google')),
				array('slug'=>'sz-google-help-ga-functions.php','title'=>__('analytics PHP functions','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE AUTHENTICATOR
		 */

		function moduleAddHelpAuthenticator()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-authenticator-setup.php'    ,'title'=>__('authenticator setup','sz-google')),
				array('slug'=>'sz-google-help-authenticator-functions.php','title'=>__('authenticator PHP','sz-google')),
				array('slug'=>'sz-google-help-authenticator-device.php'   ,'title'=>__('authenticator device','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE CALENDAR
		 */

		function moduleAddHelpCalendar()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-calendar.php','title'=>__('widget calendar','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE DRIVE
		 */

		function moduleAddHelpDriveSave()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-drive-embed.php' ,'title'=>__('drive embed','sz-google')),
				array('slug'=>'sz-google-help-drive-viewer.php','title'=>__('drive viewer','sz-google')),
				array('slug'=>'sz-google-help-drive-save.php'  ,'title'=>__('drive save button','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE GROUPS
		 */

		function moduleAddHelpGroups()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-groups.php','title'=>__('widget groups','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE HANGOUTS
		 */

		function moduleAddHelpHangouts()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-hangout-start.php','title'=>__('hangout start button','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE PANORAMIO
		 */

		function moduleAddHelpPanoramio()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-panoramio.php','title'=>__('widget panoramio','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE TRANSLATE
		 */

		function moduleAddHelpTranslate()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-translate.php'          ,'title'=>__('translate setup','sz-google')),
				array('slug'=>'sz-google-help-translate-functions.php','title'=>__('translate PHP functions','sz-google')),
			));
		}

		/**
		 * Functions to add the various sections with the index
		 * for icons of the documents module GOOGLE YOUTUBE
		 */

		function moduleAddHelpYoutube()
		{
			$this->moduleAddHelpLinks(array(
				array('slug'=>'sz-google-help-youtube-video.php'   ,'title'=>__('youtube video'   ,'sz-google')),
				array('slug'=>'sz-google-help-youtube-playlist.php','title'=>__('youtube playlist','sz-google')),
				array('slug'=>'sz-google-help-youtube-badge.php'   ,'title'=>__('youtube badge'   ,'sz-google')),
				array('slug'=>'sz-google-help-youtube-link.php'    ,'title'=>__('youtube link'    ,'sz-google')),
				array('slug'=>'sz-google-help-youtube-button.php'  ,'title'=>__('youtube button'  ,'sz-google')),
			));
		}

		/**
		 * Function to add the various sections on tabs reviews
		 * present in the official documentation of the plugin
		 */

		function moduleAddHelpReviews() {
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/templates/sz-google-template-reviews.php');
		}

		/**
		 * Function to add the various sections on tabs modules
		 * present in the official documentation of the plugin
		 */

		function moduleAddHelpModules() {
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/templates/sz-google-template-modules.php');
		}

		/**
		 * Call the general function for the creation of the general form
		 * sections must be passed as an array of name = > title
		 */

		function moduleAddNavs($name)
		{
			// Calculating the keys to elements in the array 
			// that correspond to the current, previous and next

			$KeyPrecedente = false;
			$KeyAttuale    = false;
			$KeySeguente   = false;

			foreach ($this->HelpIndexItems as $key => $value) {
				if($value['slug'] == $name) {
					$KeyAttuale = $key; break;
				}
			}

			if ($KeyAttuale > 0) $KeyPrecedente = $KeyAttuale - 1;
			if ($KeyAttuale < (count($this->HelpIndexItems)-1)) $KeySeguente = $KeyAttuale + 1;

			// Creating a link to the previous and next
			// Create HTML for the browser to help index

			$LINKPREV = ''; $LINKNEXT = '';

			if ($KeyPrecedente !== false) $LINKPREV = '<a href="'.menu_page_url($this->menuslug,false).'&amp;help='.$this->HelpIndexItems[$KeyPrecedente]['slug'].'""><-- '.ucfirst($this->HelpIndexItems[$KeyPrecedente]['title']).'</a>';
			if ($KeySeguente   !== false) $LINKNEXT = '<a href="'.menu_page_url($this->menuslug,false).'&amp;help='.$this->HelpIndexItems[$KeySeguente]['slug'].'"">'.ucfirst($this->HelpIndexItems[$KeySeguente]['title']).' --></a>';

			$HTML  = '<div class="navs">';
			$HTML .= '<div class="prev">'.$LINKPREV.'</div>';
			$HTML .= '<div class="capo"><a href="'.menu_page_url($this->menuslug,false).'">'.$this->pagetitle.'</a></div>';
			$HTML .= '<div class="next">'.$LINKNEXT.'</div>';
			$HTML .= '</div>';

			return $HTML;
		}

		/**
		 * Call the general function for the creation of the general form
		 * sections must be passed as an array of name = > title
		 */

		function moduleCommonFormHelp($title,$setting,$sections,$formsavebutton,$HTML,$slug)
		{
			$NAVS = $this->moduleAddNavs($slug);
			$HTML = $NAVS.$HTML.$NAVS;
			$this->moduleCommonForm($title,$setting,$sections,$formsavebutton,$HTML);
		}
	}
}