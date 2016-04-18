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

if (!class_exists('SZGoogleActionYoutubeLink'))
{
	class SZGoogleActionYoutubeLink extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'channel'      => '', // default value
				'subscription' => '', // default value
				'text'         => '', // default value
				'image'        => '', // default value
				'newtab'       => '', // default value
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
				'channel'      => '', // default value
				'subscription' => '', // default value
				'text'         => '', // default value
				'image'        => '', // default value
				'newtab'       => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleYoutube');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$channel      = trim($channel);
			$subscription = trim($subscription);
			$text         = trim($text);
			$image        = trim($image);
			$newtab       = trim($newtab);

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($channel      == '') $channel       = $options->youtube_channel;
			if ($subscription == '') $subscription  = '1';
			if ($newtab       == '') $newtab        = '0';
			if ($text         == '') $text          = SZGoogleCommon::getTranslate('channel youtube');

			// Conversione dei valori specificati direttamete nei parametri con
			// i valori usati per la memorizzazione dei valori di default

			if ($newtab       == 'yes' or $newtab       == 'y') $newtab       = '1'; 
			if ($newtab       == 'no'  or $newtab       == 'n') $newtab       = '0'; 

			if ($subscription == 'yes' or $subscription == 'y') $subscription = '1'; 
			if ($subscription == 'no'  or $subscription == 'n') $subscription = '0'; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			$YESNO = array('1','0');

			if (!in_array($newtab,$YESNO))       $newtab       = '1'; 
			if (!in_array($subscription,$YESNO)) $subscription = '1'; 

			// Verifico se canale è un nome o identificativo univoco 
			// come ad esempio il canale wordpress italy+ UCJqiM61oRRvhTD5il2n56xg

			$type = $this->getModuleObject('SZGoogleModuleYoutube')->youtubeCheckChannel($channel);

			if ($type == 'ID') $ytURL = 'http://www.youtube.com/channel/';
				else $ytURL = 'http://www.youtube.com/user/';

			// Creazione HREF per il canale youtube con il controllo
			// per aggiungere il parametro che riguarda la sottoscrizione

			if ($newtab == '0') $NEWTAB = ''; else $NEWTAB = ' target="_blank"';

			if ($subscription == '0') $HREF = '<a href="'.$ytURL.$channel.'"'.$NEWTAB.'>'; 
				else $HREF = '<a href="'.$ytURL.$channel.'?sub_confirmation='.$subscription.'"'.$NEWTAB.'>';

			// Se viene indicata un'immagine vado sostituire la stringa text 
			// inmaniera da dare priorità all'immagine rispetto al testo

			if ($image != '') $text = '<img src="'.$image.'" alt=""/>'; 

			// Creazione codice HTML per embed code da inserire nella pagina wordpress
			// Se esiste il contenuto tra lo shortcode o prendo i valori delle opzioni

			if (empty($content)) $HTML = $HREF.$text.'</a>';
				else $HTML  = $HREF.$content.'</a>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}