<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Modules
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleModuleTranslate'))
{
	class SZGoogleModuleTranslate extends SZGoogleModule
	{
		private $options = false;

		static protected $NUMBERCALLS = 0;
		static protected $JAVASCRIPTS = array();

		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_translate');
			
			// Definition shortcode connected to the module with an array where you
			// have to specify the name activation option with the shortcode and function

			$this->moduleSetShortcodes(array(
				'translate_shortcode' => array('sz-gtranslate',array($this,'getTranslateShortcode')),
			));

			// Definition widgets connected to the module with an array where you
			// have to specify the name option of activating and class to be loaded

			$this->moduleSetWidgets(array(
				'translate_widget'    => 'SZGoogleWidgetTranslate',
			));
		}

		/**
		 * Calcolo le opzioni legate al modulo con esecuzione dei 
		 * controlli formali di coerenza e impostazione dei default
		 *
		 * @return array
		 */
		function getOptions()
		{
			if ($this->options) return $this->options;
				else $this->options = parent::getOptionsSet('sz_google_options_translate');

			// Controllo opzione di codice GA-UA nel caso debba pendere il valore
			// specificato nel modulo corrispondente se risulta attivo.

			if ($object = self::getObject('SZGoogleModuleAnalytics') and
				$this->options['translate_analytics_ua'] == '')
			{
				$options_ga = $object->getOptions();
				$this->options['translate_analytics_ua'] = $options_ga['ga_uacode'];
			}

			// Ritorno indietro il gruppo di opzioni corretto dai
			// controlli formali eseguito dalla funzione di controllo

			return $this->options;
		}

		/**
		 * Add the actions of the current module, this function must be
		 * implemented in the case of a non-standard customization via array
		 */

		function moduleAddActions() {
			add_action('SZ_HEAD_HEAD',array($this,'getTranslateMetaHead'));
			add_action('SZ_FOOT_BODY',array($this,'addJavascriptToFooter'));
		}

		/**
		 * Funzione per generazione codice HTML da inserire nella
		 * sezione HEAD come <meta name="google-translate-customization">
		 *
		 * @return string
		 */
		function getTranslateMeta() {
			if ($this->getTranslateMetaID() == '') return NULL;
				else return '<meta name="google-translate-customization" content="'.$this->getTranslateMetaID().'"/>'."\n";
		}

		/**
		 * Funzione per calcolare il codice ID univoco di google da aggiungere 
		 * alla sezione HEAD come <meta name="google-translate-customization">
		 *
		 * @return void
		 */
		function getTranslateMetaID() 
		{
			$options = (object) $this->getOptions();

			// Check if user insert all meta code con html tags
			// or insert value for only identification meta ID

			if (preg_match('/content=\"(.*)\"/',$options->translate_meta,$matches)) {
				if (isset($matches[1])) return $matches[1];
			}

			return trim($options->translate_meta);
		}

		/**
		 * Funzione per generazione codice HTML da inserire nella
		 * sezione HEAD come <meta name="google-translate-customization">
		 *
		 * @return void
		 */
		function getTranslateMetaHead() {
			echo $this->getTranslateMeta();
		}

		/**
		 * Funzione per shortcode [sz-gtranslate] che permette di eseguire
		 * un selettore di lingua da utilizzare nella traduzione automatica
		 *
		 * @return string
		 */
		function getTranslateShortcode($atts,$content=null)
		{
			return $this->getTranslateCode(shortcode_atts(array(
				'language'  => '',
				'mode'      => '',
				'automatic' => '',
				'multiple'  => '',
				'analytics' => '',
				'uacode'    => '',
				'action'    => 'shortcode',
			),$atts),$content);
		}

		/**
		 * Aggiungo il modulo generale con lo shortcode e il widget per
		 * eseguire la funzione di start button hangout su google+
		 * Questa funzione definisce il codice HTML che deve essere generato.
		 *
		 * @return string
		 */
		function getTranslateCode($atts=array(),$content=null)
		{
			$options = $this->getOptions();

			// Estrazione dei valori specificati nello shortcode, i valori ritornati
			// sono contenuti nei nomi di variabili corrispondenti alla chiave

			if (!is_array($atts)) $atts = array();

			extract(shortcode_atts(array(
				'language'  => '',
				'mode'      => '',
				'automatic' => '',
				'multiple'  => '',
				'analytics' => '',
				'uacode'    => '',
				'action'    => '',
			),$atts));

			// Elimino spazi aggiunti di troppo ed esegui la trasformazione in
			// stringa minuscolo per il controllo di valori speciali come "auto"

			$uacode    = trim($uacode);
			$language  = strtolower(trim($language));
			$mode      = strtolower(trim($mode));
			$automatic = strtolower(trim($automatic));
			$multiple  = strtolower(trim($multiple));
			$analytics = strtolower(trim($analytics));

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($language  == '') $language  = $options['translate_language'];
			if ($mode      == '') $mode      = $options['translate_mode'];
			if ($automatic == '') $automatic = $options['translate_automatic'];
			if ($multiple  == '') $multiple  = $options['translate_multiple'];
			if ($analytics == '') $analytics = $options['translate_analytics'];
			if ($uacode    == '') $uacode    = $options['translate_analytics_ua'];

			if ($options['translate_language'] == '99') $language = substr(get_bloginfo('language'),0,2);	
				else $language = trim($options['translate_language']);

			// Incremento la variabile che tiene il conto del numero dei componenti
			// elaborati e creo un id univoco da assegnare al contenitore HTML

			self::$NUMBERCALLS++;
			$uniqueID = 'sz-google-translate-unique-'.self::$NUMBERCALLS;

			// Creazione codice HTML per inserimento javascript di google 
			// inserimento del risultato dentro array globale per scrittura footer

			$JAVA  = '<script type="text/javascript">';
			$JAVA .= 'function szgoogleTranslateElementInit_'.self::$NUMBERCALLS.'() {';
			$JAVA .= 'new google.translate.TranslateElement({';
			$JAVA .= "pageLanguage:'".$language."'";

			if ($options['translate_mode'] == 'I2') $JAVA .= ",layout:google.translate.TranslateElement.InlineLayout.HORIZONTAL";
			if ($options['translate_mode'] == 'I3') $JAVA .= ",layout:google.translate.TranslateElement.InlineLayout.SIMPLE";

			if ($automatic <> '1' ) $JAVA .= ",autoDisplay:false";
			if ($multiple  == '1' ) $JAVA .= ",multilanguagePage:true";
			if ($analytics == '1' ) $JAVA .= ",gaTrack:true";

			if ($options['translate_analytics_ua'] <> '' ) $JAVA .= ",gaID:'".$options['translate_analytics_ua']."'";

			$JAVA .= "},'$uniqueID');}";
			$JAVA .= '</script>';

			// Creazione codice HTML per inserimento javascript di google 
			// inserimento del risultato dentro array globale per scrittura footer

			self::$JAVASCRIPTS[self::$NUMBERCALLS] = $JAVA;

			// Ritorno come codice HTML solo il contenitore con ID univoco in quanto
			// il resto del codcie javascript sarà inserito in fondo alla pagina

			return '<div id="'.$uniqueID.'"></div>';
		}

		/**
		 * Funzione per aggiungere il codice javascript della composizione
		 * widget di traduzione in fondo alla pagina e gestendo anche i casi
		 * di inserimento componenti multipli sulla stessa pagina.
		 *
		 * @return void
		 */
		function addJavascriptToFooter() 
		{
			if(is_array(self::$JAVASCRIPTS) && count(self::$JAVASCRIPTS) > 0) 
			{
				// Per ogni elemento presente in array scrivo la riga singola
				// di codice Javascript con la definizione della funzione

				foreach (self::$JAVASCRIPTS as $key=>$value) {
					echo $value."\n";
				}

				// Creazione di una funzione generica di callback per 
				// richiamare le singole funzioni precedentemente definite

				echo '<script type="text/javascript">';
				echo 'function szgoogleTranslateElementInit_0() {';

				foreach (self::$JAVASCRIPTS as $key=>$value) {
					echo 'szgoogleTranslateElementInit_'.$key.'(); '; 
				}

				echo "}";
				echo '</script>'."\n";

				// Richiamo dello script ufficiale di google specificando una
				// funzione di callback che corrisponde a quella generica

				echo '<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=szgoogleTranslateElementInit_0"></script>'."\n";
			}
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsTranslate.php');
}