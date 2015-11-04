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

if (!class_exists('SZGoogleActionHangoutsStart'))
{
	class SZGoogleActionHangoutsStart extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'type'         => '', // default value
				'width'        => '180',
				'topic'        => '', // default value
				'float'        => '', // default value
				'align'        => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'profile'      => '', // default value
				'email'        => '', // default value
				'logged'       => '', // default value
				'guest'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'class'        => '', // default value
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
				'type'         => '', // default value
				'width'        => '', // default value
				'topic'        => '', // default value
				'float'        => '', // default value
				'align'        => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'profile'      => '', // default value
				'email'        => '', // default value
				'logged'       => '', // default value
				'guest'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'class'        => '', // default value
				'action'       => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleHangouts');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$text         = trim($text);
			$img          = trim($img);
			$class        = trim($class);
			$topic        = htmlspecialchars(trim($topic),ENT_QUOTES);

			$type         = strtolower(trim($type));
			$width        = strtolower(trim($width));
			$align        = strtolower(trim($align));
			$float        = strtolower(trim($float));
			$position     = strtolower(trim($position));
			$profile      = strtolower(trim($profile));
			$email        = strtolower(trim($email));
			$logged       = strtolower(trim($logged));
			$guest        = strtolower(trim($guest));
			$margintop    = strtolower(trim($margintop));
			$marginright  = strtolower(trim($marginright));
			$marginbottom = strtolower(trim($marginbottom));
			$marginleft   = strtolower(trim($marginleft));
			$marginunit   = strtolower(trim($marginunit));

			// Conversione dei valori specificati direttamete nei parametri con
			// i valori usati per la memorizzazione dei valori di default

			if ($logged == 'yes' or $logged == 'y') $logged = '1'; 
			if ($guest  == 'yes' or $guest  == 'y') $guest  = '1'; 

			if ($logged == 'no'  or $logged == 'n') $logged = '0'; 
			if ($guest  == 'no'  or $guest  == 'n') $guest  = '0'; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			$YESNO = array('1','0');

			if ($logged == '') $logged = $options->hangouts_start_logged;
			if ($guest  == '') $guest  = $options->hangouts_start_guest;

			if (!in_array($logged,$YESNO)) $logged = $options->hangouts_start_logged;
			if (!in_array($guest ,$YESNO)) $guest  = $options->hangouts_start_guest;

			// Controllo se devo saltare elaborazione per opzioni che riguardano
			// il controllo su utente loggato o utente guest. Ritorno NULL.

			if (!current_user_can('manage_options')) {
				if ( current_user_can('read') and $logged != '1') return NULL;
				if (!current_user_can('read') and $guest  != '1') return NULL;
			}

			// Imposto i valori di default nel caso siano specificati dei valori
			// che non appartengono al range dei valori accettati

			if (!in_array($type,array('normal','onair','party','moderate')))   $type     = 'normal';
			if (!in_array($float,array('none','left','right')))                $float    = 'none';
			if (!in_array($align,array('none','left','right','center')))       $align    = 'none';
			if (!in_array($position,array('top','center','bottom','outside'))) $position = 'outside';

			if ($class == '') $class = 'sz-google-hangouts-button';
			if (!is_numeric($width) and $width != 'auto') $width = '';

			// Controllo la dimensione del widget e controllo formale dei valori numerici
			// se trovo qualche incongruenza applico i valori di default prestabiliti

			if ($width == '')     $width = "'+w+'";
			if ($width == 'auto') $width = "'+w+'";

			$uniqueID = 'sz-google-hangouts-'.md5(uniqid(),false);

			// Creazione codice HTML per embed code da inserire nella pagina wordpress
			// prima praparo il codice del bottone singolo e poi chiamo funzione di wrapping

			$HTML  = '<script type="text/javascript">';
			$HTML .= "var w=document.getElementById('".$uniqueID."').offsetWidth;";
			$HTML .= "var h='<'+'";

			$HTML .= 'div class="g-hangout"';
			$HTML .= ' data-render="createhangout"';
			$HTML .= ' data-hangout_type="'.$type .'"';
			$HTML .= ' data-widget_size="' .$width.'"';

			if ($topic != '') $HTML .= ' data-topic="'.$topic.'"';

			// Check field for email or profile to prepare the html 
			// code and allow invitations specified in options

			$invite    = array();
			$profili   = explode(',',$profile);
			$indirizzi = explode(',',$email);

			if (is_array($profili) and !empty($profili)) {
				foreach ($profili as $key => $value) {
					$invite[] = "{id:\'".$value."\',invite_type:\'PROFILE\'}";
				}
			}

			// Check field for email or profile to prepare the html 
			// code and allow invitations specified in options

			if (is_array($indirizzi) and !empty($indirizzi)) {
				foreach ($indirizzi as $key => $value) {
					$invite[] = "{id:\'".$value."\',invite_type:\'EMAIL\'}";
				}
			}

			if (!empty($invite)) $HTML .= ' data-invites="['.implode(',',$invite).']"';

			// Close code javascript for the creation of the 
			// required component. In this way the load is delayed

			$HTML .= "></'+'div'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';

			// Creazione codice HTML con funzione di wrapping comune a tutti i bottoni in maniera
			// da essere utilizzati anche come dei piccoli badge con immagine e posizionamento

			$HTML = SZGoogleCommonButton::getButton(array(
				'html'         => $HTML,
				'text'         => $text,
				'image'        => $img,
				'content'      => $content,
				'float'        => $float,
				'align'        => $align,
				'position'     => $position,
				'margintop'    => $margintop,
				'marginright'  => $marginright,
				'marginbottom' => $marginbottom,
				'marginleft'   => $marginleft,
				'marginunit'   => $marginunit,
				'class'        => $class,
				'uniqueID'     => $uniqueID,
			));

			// Aggiunta del codice javascript per il rendering dei widget, questo codice		 
			// viene aggiungo anche dalla sidebar però viene inserito una sola volta
			
			add_action('SZ_FOOT_BODY',array($this->getModuleObject('SZGoogleModuleHangouts'),'setJavascriptPlatform'));

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}