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

if (!class_exists('SZGoogleActionPlusAuthorBadge'))
{
	class SZGoogleActionPlusAuthorBadge extends SZGoogleAction
	{
		static private $sequence = 0;

		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'mode'      => '',  // default value
				'link'      => '',  // default value
				'cover'     => '',  // default value
				'photo'     => '',  // default value
				'biography' => '',  // default value
				'width'     => '',  // default value
				'action'    => 'S', // default value
			),$atts),$content);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// If not page single post or not page author archive
			// return from function and not execute processs HTML

			if (!is_singular('post') and !is_author()) return NULL;

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			$options = shortcode_atts(array(
				'mode'      => '', // default value
				'link'      => '', // default value
				'cover'     => '', // default value
				'photo'     => '', // default value
				'biography' => '', // default value
				'width'     => '', // default value
				'action'    => '', // default value
			),$atts);

			$keyatts = $this->checkOptions($options);

			// Check the numerical value of the scale and add the code to pixels
			// This string will be used to create the code of the CSS style

			if ($keyatts->width == 'auto' or $keyatts->width == '') $keyatts->width = "100%";
			if (ctype_digit($keyatts->width)) $keyatts->width .= 'px'; 

			// Create HTML code only after check the page displayed
			// Check single post or author page and options widget

			if (!($keyatts->mode == '2' or ($keyatts->mode == '1' and is_singular('post'))))  return NULL;

			// Calcolate Author ID in author page or single post
			// using the different methods of calculation

			if (is_singular()) {
				$postid = get_queried_object_id();
				$author = get_post_field('post_author',$postid);
			}

			if (is_author()) {
				if(get_query_var('author_name')) $curauth = get_user_by('slug',get_query_var('author_name'));
					else $curauth = get_userdata(get_query_var('author'));
				$author = $curauth->ID;
			}

			// Calculating all the fields that affect the information
			// on the profile of the badge on google plus (added by plugin) 

			$AUTH_DESC = trim(get_the_author_meta('description'             ,$author));
			$AUTH_NAME = trim(get_the_author_meta('display_name'            ,$author));
			$AUTH_PLUS = trim(get_the_author_meta('googleplus'              ,$author));
			$AUTH_PHOT = trim(get_the_author_meta('googleplusprofilephoto'  ,$author));
			$AUTH_COVE = trim(get_the_author_meta('googleplusprofilecover'  ,$author));
			$AUTH_LINE = trim(get_the_author_meta('googleplusprofiletagline',$author));

			// Calculating the link to use the author based on the configuration options
			// You can use the link in the field googleplus or the standard wordpress

			if ($AUTH_PLUS != '') { $AUTH_HREF = $AUTH_PLUS; $AUTH_TARG = ' target="_blank"'; }
				else { $AUTH_HREF = get_author_posts_url($author); $AUTH_TARG = '';}

			if ($keyatts->link == '2') { $AUTH_HREF = get_author_posts_url($author); $AUTH_TARG = '';}

			// Creating a unique identifier to recognize the embed code, in the
			// case where the function is called multiple times in the same page

			$keyatts->unique = ++self::$sequence;
			$keyatts->idHTML = 'sz-author-badge-'.$keyatts->unique;

			// CSS code generation for the elements that make me badge author,
			// the code will be inserted in the page HTML section footer

			$AUTH_CSS1  = '.sz-author-badge { margin-bottom: 1em; }';
			$AUTH_CSS1 .= '.sz-author-badge-image { position:relative; }';
			$AUTH_CSS1 .= '.sz-author-badge-cover { width:100%; margin-bottom:70px; }';
			$AUTH_CSS1 .= '.sz-author-badge-cover a { display:block;padding-bottom:56%; }';
			$AUTH_CSS1 .= '.sz-author-badge-cover a { background-position:center center;background-size:cover;background-repeat:no-repeat; }';
			$AUTH_CSS1 .= '.sz-author-badge-photo { text-align:center; }';
			$AUTH_CSS1 .= '.sz-author-badge-photo a { display:inline-block;border:6px solid white;border-radius:50% }';
			$AUTH_CSS1 .= '.sz-author-badge-photo img { width:100px;height:100px;border-radius:50% }';
			$AUTH_CSS1 .= '.sz-author-badge-name h3 { margin:0 0 1em 0; font-size:1.2em; font-weight:bold; }';
			$AUTH_CSS1 .= '.sz-author-badge-body { text-align:center; }';
			$AUTH_CSS1 .= '.sz-author-badge-desc { font-size:0.9em; }';
			$AUTH_CSS1 .= '.sz-author-badge-wrap.sz-cover .sz-author-badge-photo { position:absolute;left:50%;bottom:-50px;margin-left:-53px; }';

			// If it is the first time I run the creation of a badge insert
			// the CSS code in the footer, the next time will be ignored

			if ($keyatts->unique == 1) $this->addCodeCSSInlineFoot($AUTH_CSS1);

			// If you have disabled the views of some component, I reset the
			// variables connected and do not create the indicated sections

			if ($keyatts->cover     == 'N') $AUTH_COVE = '';         // Omit Cover image
			if ($keyatts->photo     == 'N') $AUTH_PHOT = '';         // Omit Photo
			if ($keyatts->biography == 'N') $AUTH_DESC = '';         // Omit Biografy
			if ($keyatts->biography == '2') $AUTH_DESC = $AUTH_LINE; // Author Tagline

			// Creation of classes to set the options based on the items that will be displayed
			// the different settings of the items are in the pay code inline CSS

			$AUTH_CLAS = 'sz-author-badge-wrap';

			if ($AUTH_COVE == '') $AUTH_CLAS .= " sz-no-cover";       else $AUTH_CLAS .= " sz-cover";
			if ($AUTH_PHOT == '') $AUTH_CLAS .= " sz-no-photo";       else $AUTH_CLAS .= " sz-photo";
			if ($AUTH_NAME == '') $AUTH_CLAS .= " sz-no-name";        else $AUTH_CLAS .= " sz-name";
			if ($AUTH_DESC == '') $AUTH_CLAS .= " sz-no-description"; else $AUTH_CLAS .= " sz-description";

			// Create html code for displaying a badge author with required sections
			// Check the CSS rules to see the behavior of the different components

			$HTML  = '<div id="'.$keyatts->idHTML.'" class="sz-author-badge" style="width:'.$keyatts->width.'">';
			$HTML .= '<div class="'.$AUTH_CLAS.'">';
			$HTML .= '<div class="sz-author-badge-image">';

			if ($AUTH_COVE != '') $HTML .= '<div class="sz-author-badge-cover"><a href="'.$AUTH_HREF.'"'.$AUTH_TARG.' style="background-image:url(\''.$AUTH_COVE.'\')"></a></div>';
			if ($AUTH_PHOT != '') $HTML .= '<div class="sz-author-badge-photo"><a href="'.$AUTH_HREF.'"'.$AUTH_TARG.'><img src="'.$AUTH_PHOT.'" alt=""/></a></div>';

			$HTML .= '</div>';
			$HTML .= '<div class="sz-author-badge-body">';

			if ($AUTH_NAME != '') $HTML .= '<div class="sz-author-badge-name"><h3><a href="'.$AUTH_HREF.'"'.$AUTH_TARG.'>'.$AUTH_NAME.'</a></h3></div>';
			if ($AUTH_DESC != '') $HTML .= '<div class="sz-author-badge-desc">'.$AUTH_DESC.'</div>';

			$HTML .= '</div>';
			$HTML .= '</div>';
			$HTML .= '</div>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}

		/**
		 * Create HTML for the component called to be
		 * used in common for both widgets and shortcodes
		 */

		function checkOptions($options=array())
		{
			// Loading options for the configuration variables that
			// contain the default values for shortcodes and widgets

			$check = (object) $options;

			// Deleting spaces added too and execute the transformation to a
			// string lowercase for the control of special values ​​such as "auto"

			$check->mode      = trim($check->mode);
			$check->link      = trim($check->link);
			$check->cover     = trim($check->cover);
			$check->photo     = trim($check->photo);
			$check->biography = trim($check->biography);
			$check->width     = strtolower(trim($check->width));

			// Setting any of the default parameters for
			// fields that contain invalid values or inconsistent

			if (!in_array($check->mode     ,array('1','2')))     $check->mode      = '1'; // Single Post
			if (!in_array($check->link     ,array('1','2')))     $check->link      = '1'; // Google+
			if (!in_array($check->cover    ,array('1','N')))     $check->cover     = '1'; // Profile
			if (!in_array($check->photo    ,array('1','N')))     $check->photo     = '1'; // Profile
			if (!in_array($check->biography,array('1','2','N'))) $check->biography = '1'; // Profile

			// Check the values passed in arrays that specify the size
			// if the dimension must be calculated automatically

			if (!ctype_digit($check->width) and $check->width != 'auto') $check->width = 'auto'; 

			if ($check->width == 'auto') $check->width_auto = '1';

			// Return the correct parameters to the calling function
			// The format of the return is an object not an array

			return $check;
		}
	}
}