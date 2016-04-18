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

if (!class_exists('SZGoogleActionYoutubeButton'))
{
	class SZGoogleActionYoutubeButton extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'channel'    => '', // default value
				'layout'     => '', // default value
				'theme'      => '', // default value
				'subscriber' => '', // default value
				'align'      => '', // default value
			),$atts),$content,true);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null,$shortcode=false)
		{
			if (!is_array($atts)) $atts = array();

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'channel'    => '', // default value
				'layout'     => '', // default value
				'theme'      => '', // default value
				'subscriber' => '', // default value
				'align'      => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleYoutube');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$channel    = trim($channel);
			$layout     = strtolower(trim($layout));
			$theme      = strtolower(trim($theme));
			$subscriber = strtolower(trim($subscriber));
			$align      = strtolower(trim($align));

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($channel == '') $channel = $options->youtube_channel;
			if ($layout  == '') $layout  = 'default';
			if ($theme   == '') $theme   = 'default';
			if ($align   == '') $align   = 'none';

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if (!in_array($layout,array('default','full'))) $layout = 'default'; 
			if (!in_array($theme, array('default','dark'))) $theme  = 'default'; 
			if (!in_array($align, array('none','left','center','right'))) $align = 'none'; 

			// Conversione dei valori specificati direttamete nei parametri con
			// i valori usati per la memorizzazione dei valori di default

			if ($subscriber == 'yes' or $subscriber == 'y') $subscriber = '1'; 
			if ($subscriber == 'no'  or $subscriber == 'n') $subscriber = '0'; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			$YESNO = array('1','0');

			if (!in_array($subscriber,$YESNO)) $subscriber = '1'; 

			// Verifico se canale è un nome o identificativo univoco 
			// come ad esempio il canale wordpress italy+ UCJqiM61oRRvhTD5il2n56xg

			$type = $this->getModuleObject('SZGoogleModuleYoutube')->youtubeCheckChannel($channel);

			// Creazione contenitore principale per eseguire un metodo
			// di allineamento personalizzato usando le opzioni disponibili

			$style = '';

			if ($align != 'none')   $style .= 'text-align:'.$align.';';
			if ($shortcode == true) $style .= 'margin-bottom:1em;';

			// Creazione codice HTML per embed code da inserire nella pagina wordpress
			// ricordarsi di aggiungere il codice javascript per il rendering

			$HTML  = '<div class="s-ytsubscribe" style="'.$style.'">';
			$HTML .= '<div class="g-ytsubscribe" ';

			if ($type == 'ID') $HTML .= 'data-channelid="'.$channel.'" ';
				else $HTML .= 'data-channel="'.$channel.'" ';

			if ($subscriber == '1') $HTML .= 'data-count="default" ';
				else $HTML .= 'data-count="hidden" ';

			$HTML .= 'data-layout="'.$layout.'" ';
			$HTML .= 'data-theme="'.$theme.'"';
			$HTML .= '></div>';

			// Chiusura contenitore principale per eseguire un metodo
			// di allineamento personalizzato usando le opzioni disponibili

			$HTML .= '</div>';

			// Aggiunta del codice javascript per il rendering dei widget, questo codice		 
			// viene aggiungo anche dalla sidebar però viene inserito una sola volta

			$this->getModuleObject('SZGoogleModuleYoutube')->addCodeJavascriptFooter();

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}