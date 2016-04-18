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

if (!class_exists('SZGoogleActionDriveViewer'))
{
	class SZGoogleActionDriveViewer extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'url'           => '', // default value
				'title'         => '', // default value
				'titleposition' => '', // default value
				'titlealign'    => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'pre'           => '', // default value
				'margintop'     => '', // default value
				'marginright'   => '', // default value
				'marginbottom'  => '', // default value
				'marginleft'    => '', // default value
				'marginunit'    => '', // default value
				'action'        => 'shortcode',
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
				'url'           => '', // default value
				'title'         => '', // default value
				'titleposition' => '', // default value
				'titlealign'    => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'pre'           => '', // default value
				'margintop'     => '', // default value
				'marginright'   => '', // default value
				'marginbottom'  => '', // default value
				'marginleft'    => '', // default value
				'marginunit'    => '', // default value
				'action'        => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = $this->getModuleOptions('SZGoogleModuleDrive');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$url           = trim($url);
			$title         = esc_html(trim($title));

			$pre           = strtolower(trim($pre));
			$titleposition = strtolower(trim($titleposition));
			$titlealign    = strtolower(trim($titlealign));
			$width         = strtolower(trim($width));
			$height        = strtolower(trim($height));
			$margintop     = strtolower(trim($margintop));
			$marginright   = strtolower(trim($marginright));
			$marginbottom  = strtolower(trim($marginbottom));
			$marginleft    = strtolower(trim($marginleft));
			$marginunit    = strtolower(trim($marginunit));

			// Se non specifico un URL valido per la creazione del bottone
			// esco dalla funzione e ritorno una stringa vuota

			if (empty($url)) { return ''; }

			// Configurazione delle variabili per la creazione del codice
			// HTML da utilizzare rispettando le opzioni richieste

			$optionSRC = 'https://docs.google.com/viewer?url='.rawurlencode($url).'&embedded=true';

			// Controllo le variabili usate come opzioni da utilizzare nel caso
			// non esistano valori specificati o valori non coerenti con quelli ammessi

			if ($action == 'widget') {
				if ($width         == '') $width         = $options['drive_viewer_w_width'];
				if ($height        == '') $height        = $options['drive_viewer_w_height'];
				if ($titleposition == '') $titleposition = $options['drive_viewer_w_t_position'];
				if ($titlealign    == '') $titlealign    = $options['drive_viewer_w_t_align'];
				if ($pre           == '') $pre           = $options['drive_viewer_w_wrap_pre'];
			} else {
				if ($width         == '') $width         = $options['drive_viewer_s_width'];
				if ($height        == '') $height        = $options['drive_viewer_s_height'];
				if ($titleposition == '') $titleposition = $options['drive_viewer_s_t_position'];
				if ($titlealign    == '') $titlealign    = $options['drive_viewer_s_t_align'];
				if ($pre           == '') $pre           = $options['drive_viewer_s_wrap_pre'];
			}

			if (!in_array($titleposition,array('top','bottom'))) $titleposition = 'top'; 
			if (!in_array($titlealign,array('none','left','right','center'))) $titlealign = 'none'; 

			// Conversione dei valori specificati direttamete nei parametri con
			// i valori usati per la memorizzazione dei valori di default

			if ($pre == 'yes' or $pre == 'y') $pre = '1'; 
			if ($pre == 'no'  or $pre == 'n') $pre = '0'; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			$YESNO = array('1','0');

			if (!in_array($pre,$YESNO)) $pre = '0';

			// Controllo la dimensione del widget e controllo formale dei valori numerici
			// se trovo qualche incongruenza applico i valori di default prestabiliti

			if ($width  == '')     $width = "100%";
			if ($width  == 'auto') $width = "100%";

			if ($height == '')     $height = '400';
			if ($height == 'auto') $height = '400';

			// Creazione del codice CSS per la composizione dei margini
			// usando le opzioni specificate negli shortcode o nelle funzioni PHP

			$marginCSS = $this->getModuleObject('SZGoogleModuleDrive')->getStyleCSSfromMargins(
				$margintop,$marginright,$marginbottom,$marginleft,$marginunit);

			$talignCSS = $this->getModuleObject('SZGoogleModuleDrive')->getStyleCSSfromAlign($titlealign);

			$TITLE  = '<div class="sz-google-drive-viewer-title" style="padding:0.5em;'.$talignCSS.'">'.$title.'</div>';

			// Apertura delle divisioni che rappresentano il wrapper
			// comune per eventuali personalizzazioni di visualizzazione

			$HTML  = '<div class="sz-google-drive">';
			$HTML .= '<div class="sz-google-drive-viewer" style="'.$marginCSS.'">';

			if ($pre   == '1') $HTML .= '<pre>';
			if ($title != "" and $titleposition == 'top') $HTML .= $TITLE;

			$HTML .= '<div class="sz-google-drive-viewer-embed">';
			$HTML .= '<script type="text/javascript">';
			$HTML .= "var h='<'+'";

			$HTML .= 'iframe src="'.$optionSRC.'"';
			$HTML .= ' width="' .$width .'"';
			$HTML .= ' height="'.$height.'"';
			$HTML .= ' frameborder="0"';
			$HTML .= ' style="border:none;"';

			$HTML .= "></'+'iframe'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';
			$HTML .= '</div>';

			// Chiusura delle divisioni che rappresentano il wrapper

			if ($title != "" and $titleposition == 'bottom') $HTML .= $TITLE;
			if ($pre   == '1') $HTML .= '</pre>';

			$HTML .= '</div>';
			$HTML .= '</div>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}