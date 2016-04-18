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

if (!class_exists('SZGoogleActionDriveSave'))
{
	class SZGoogleActionDriveSave extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'url'          => '', // default value
				'filename'     => '', // default value
				'sitename'     => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'align'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'action'       => 'shortcode',
			),$atts),$content);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'url'          => '', // default value
				'filename'     => '', // default value
				'sitename'     => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'align'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'action'       => '', // default value
			),$atts));

			$DEFAULT_ALIGN      = 'none';
			$DEFAULT_POSITION   = 'outside';

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$url          = trim($url);
			$filename     = trim($filename);
			$sitename     = trim($sitename);
			$text         = trim($text);
			$img          = trim($img);

			$position     = strtolower(trim($position));
			$align        = strtolower(trim($align));
			$margintop    = strtolower(trim($margintop));
			$marginright  = strtolower(trim($marginright));
			$marginbottom = strtolower(trim($marginbottom));
			$marginleft   = strtolower(trim($marginleft));
			$marginunit   = strtolower(trim($marginunit));

			// Se non specifico un URL valido per la creazione del bottone
			// esco dalla funzione e ritorno una stringa vuota

			if (empty($url)) { return ''; }

			// Imposto i valori di default nel caso siano specificati dei valori
			// che non appartengono al range dei valori accettati

			if (!in_array($align,array('none','left','right','center'))) $align = $DEFAULT_ALIGN; 
			if (!in_array($position,array('top','center','bottom','outside'))) $position = $DEFAULT_POSITION; 

			if (empty($sitename)) $sitename = get_bloginfo('name'); 
			if (empty($sitename)) $sitename = 'Website'; 
			if (empty($filename)) $filename = basename($url);

			// Calcolo il nome host attuale di wordpress in maniera da preparare
			// la stringa per la sostituzione sonlo se link è sullo stesso dominio

			$URLBlog = home_url('/');
			$URLBlog = parse_url($URLBlog);

			if(isset($URLBlog['host'])) {
				$url = preg_replace('#^https?://'.$URLBlog['host'].'#','', $url);
			}

			// Creazione codice HTML per embed code da inserire nella pagina wordpress

			$HTML  = '<div class="g-savetodrive"';
			$HTML .= ' data-src="'     .$url     .'"';
			$HTML .= ' data-filename="'.$filename.'"';
			$HTML .= ' data-sitename="'.$sitename.'"';
			$HTML .= '></div>';

			$HTML = SZGoogleCommonButton::getButton(array(
				'html'         => $HTML,
				'text'         => $text,
				'image'        => $img,
				'content'      => $content,
				'align'        => $align,
				'position'     => $position,
				'margintop'    => $margintop,
				'marginright'  => $marginright,
				'marginbottom' => $marginbottom,
				'marginleft'   => $marginleft,
				'marginunit'   => $marginunit,
				'class'        => 'sz-google-savetodrive',
			));

			// Aggiunta del codice javascript per il rendering dei widget, questo codice		 
			// viene aggiungo anche dalla sidebar però viene inserito una sola volta

			$this->getModuleObject('SZGoogleModuleDrive')->addCodeJavascriptFooter();

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}