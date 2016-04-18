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

if (!class_exists('SZGoogleActionGroups'))
{
	class SZGoogleActionGroups extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'name'           => '', // default value
				'domain'         => '', // default value
				'width'          => '', // default value
				'height'         => '', // default value
				'showsearch'     => '', // default value
				'showtabs'       => '', // default value
				'hideforumtitle' => '', // default value
				'hidesubject'    => '', // default value
				'hl'             => '', // default value
				'action'         => 'shortcode',
			),$atts),$content);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// Caricamento opzioni per le variabili di configurazione che 
			// contengono i valori di default per shortcode e widgets

			$options = $this->getModuleOptions('SZGoogleModuleGroups');

			if ($options['groups_showsearch']  == '1') $options['groups_showsearch']  = 'true'; else $options['groups_showsearch']  = 'false';  
			if ($options['groups_showtabs']    == '1') $options['groups_showtabs']    = 'true'; else $options['groups_showtabs']    = 'false';  
			if ($options['groups_hidetitle']   == '1') $options['groups_hidetitle']   = 'true'; else $options['groups_hidetitle']   = 'false';  
			if ($options['groups_hidesubject'] == '1') $options['groups_hidesubject'] = 'true'; else $options['groups_hidesubject'] = 'false';  

			// Se non è specificvata nessuna lingua o quella del tema richiamo
			// la funzione nativa di wordpress per calcolare la lingua di sistema

			if ($options['groups_language'] == '99') $language = substr(get_bloginfo('language'),0,2);	
				else $language = trim($options['groups_language']);

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'name'           => '', // default value
				'domain'         => '', // default value
				'width'          => '', // default value
				'height'         => '', // default value
				'showsearch'     => '', // default value
				'showtabs'       => '', // default value
				'hideforumtitle' => '', // default value
				'hidesubject'    => '', // default value
				'hl'             => '', // default value
				'action'         => '', // default value
			),$atts));

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$hl             = trim($hl);
			$name           = trim($name);
			$domain         = trim($domain);

			$showsearch     = strtolower(trim($showsearch));
			$showtabs       = strtolower(trim($showtabs));
			$hideforumtitle = strtolower(trim($hideforumtitle));
			$hidesubject    = strtolower(trim($hidesubject));

			if (!in_array($showsearch,    array('true','false'))) $showsearch     = $options['groups_showsearch']; 
			if (!in_array($showtabs,      array('true','false'))) $showtabs       = $options['groups_showtabs']; 
			if (!in_array($hideforumtitle,array('true','false'))) $hideforumtitle = $options['groups_hidetitle']; 
			if (!in_array($hidesubject,   array('true','false'))) $hidesubject    = $options['groups_hidesubject']; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($name == '') $name = $options['groups_name'];
			if ($name == '') $name = 'adsense-api';

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($width  == '' or $width  == '0' or !is_numeric($width))  $width  = $options['groups_width'];
			if ($height == '' or $height == '0' or !is_numeric($height)) $height = $options['groups_height'];

			if ($width  == '' or $width  == '0' or !is_numeric($width))  $width  = '100%';
			if ($height == '' or $height == '0' or !is_numeric($height)) $height = '700';

			// Creazione codice HTML per embed code da inserire nella pagina wordpress
			// prima praparo il codice del bottone singolo e poi chiamo funzione di wrapping

			$HTML  = '<script type="text/javascript">';
			$HTML .= "var h='<'+'";
			$HTML .= 'iframe src="https://groups.google.com/forum/embed/?place='.urlencode('forum/'.$name);
			$HTML .= '&amp;hl='.urlencode($hl);
			$HTML .= '&amp;showsearch='.urlencode($showsearch);
			$HTML .= '&amp;showtabs='.urlencode($showtabs);
			$HTML .= '&amp;hideforumtitle='.urlencode($hideforumtitle);
			$HTML .= '&amp;hidesubject='.urlencode($hidesubject);
			$HTML .= '&amp;showpopout=true';

			if ($domain != '') $HTML .= '&amp;domain='.urlencode($domain);

			// Se sono in locahost non calcolo URL della pagina attuale, in caso
			// contrario allego la funzione javascript per inserire il parametro in URL

			if (isset($_SERVER['HTTP_HOST']) and strtolower($_SERVER['HTTP_HOST']) == 'localhost') 
			{
				$HTML .= '" ';

			} else {

				$HTML .= "&amp;parenturl=' + encodeURIComponent(window.location.href) + '\"' + ";
				$HTML .= "' ";
			}

			$HTML .= 'width="' .$width .'" ';
			$HTML .= 'height="'.$height.'" ';
			$HTML .= 'style="border-width:0" ';
			$HTML .= 'frameborder="0" scrolling="no"';
			$HTML .= "></'+'iframe'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}