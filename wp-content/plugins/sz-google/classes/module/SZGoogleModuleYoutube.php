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

if (!class_exists('SZGoogleModuleYoutube'))
{
	class SZGoogleModuleYoutube extends SZGoogleModule
	{
		protected $setJavascriptPlusone  = false;
		protected $SZ_GOOGLE_YOUTUBE_API = array();

		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_youtube');

			// Definition shortcode connected to the module with an array where you
			// have to specify the name activation option with the shortcode and function

			$this->moduleSetShortcodes(array(
				'youtube_shortcode'          => array('sz-ytvideo'   ,array(new SZGoogleActionYoutubeVideo()   ,'getShortcode')),
				'youtube_shortcode_playlist' => array('sz-ytplaylist',array(new SZGoogleActionYoutubePlaylist(),'getShortcode')),
				'youtube_shortcode_badge'    => array('sz-ytbadge'   ,array(new SZGoogleActionYoutubeBadge()   ,'getShortcode')),
				'youtube_shortcode_link'     => array('sz-ytlink'    ,array(new SZGoogleActionYoutubeLink()    ,'getShortcode')),
				'youtube_shortcode_button'   => array('sz-ytbutton'  ,array(new SZGoogleActionYoutubeButton()  ,'getShortcode')),
			));

			// Definition widgets connected to the module with an array where you
			// have to specify the name option of activating and class to be loaded

			$this->moduleSetWidgets(array(
				'youtube_widget'             => 'SZGoogleWidgetYoutubeVideo',
				'youtube_widget_playlist'    => 'SZGoogleWidgetYoutubePlaylist',
				'youtube_widget_badge'       => 'SZGoogleWidgetYoutubeBadge',
				'youtube_widget_link'        => 'SZGoogleWidgetYoutubeLink',
				'youtube_widget_button'      => 'SZGoogleWidgetYoutubeButton',
			));
		}

		/**
		 * Funzione per controllare dalla stringa se il canale 
		 * è rappresentato tramite ID o nome in chiaro
		 *
		 * @return void
		 */
		function youtubeCheckChannel($channel) {
			if (strlen($channel) == 24 and substr($channel,0,2) == 'UC') return "ID";
				else return "NAME";
		}

		/**
		 * Funzione per aggiungere il codice javascript dei vari
		 * componenti di google plus nel footer e controllo se 
		 * la richiesta è stata eseguita già in qualche parte diversa
		 *
		 * @return void
		 */
		function addCodeJavascriptFooter()
		{
			// Se ho già inserito il codice javascript nella sezione footer
			// esco dalla funzione altrimenti setto la variabile e continuo

			if ($this->setJavascriptPlusone) return;
				else $this->setJavascriptPlusone = true;

			// Caricamento azione nel footer del plugin per il caricamento
			// del framework javascript messo a disposizione da google

			add_action('SZ_FOOT_BODY',array($this,'setJavascriptPlusOne'));
		}

		/**
		 * Funzione per aggiungere le opzioni in un array globale che
		 * verrà utilizzato nella creazione di codice javascript sul footer
		 *
		 * @return string
		 */
		function addYoutubeVideoAPI($opts=array())
		{
			if (is_array($opts)) {
				$this->SZ_GOOGLE_YOUTUBE_API[] = $opts;
				add_action('SZ_FOOT_BODY',array($this,'addYoutubeScriptFooter'));
			}
		}

		/**
		 * Creazione codice javascript in footer per inserire il codice embed
		 * di youtube con tutti i parametri di personalizzazione richiesti
		 *
		 * @return string
		 */
		function addYoutubeScriptFooter()
		{
			if (isset($this->SZ_GOOGLE_YOUTUBE_API) and is_array($this->SZ_GOOGLE_YOUTUBE_API)) 
			{
				// Codice javascript per il rendering iframe tramite API
	
				$HTML  = '<script type="text/javascript">';
				$HTML .= "var element = document.createElement('script');";
				$HTML .= 'element.src = "https://www.youtube.com/player_api";';
				$HTML .= "var myscript = document.getElementsByTagName('script')[0];";
				$HTML .= 'myscript.parentNode.insertBefore(element,myscript);';

				// Creazione variabile per ogni player inserito nella pagina web
				// utilizzo l'identificativo univoco per il nome variabile
 
				foreach ($this->SZ_GOOGLE_YOUTUBE_API as $value) {
					if (is_array($value) and isset($value['video'])) { 
						$HTML .= 'var myplayer_'.$value['unique'].';';
					}
				}

				// Creazione funzione per caricamento dei player inseriti nella
				// pagina web. creazione del codice javascript per ogni player univoco

				$HTML .= 'function onYouTubePlayerAPIReady() {';

				foreach ($this->SZ_GOOGLE_YOUTUBE_API as $value) 
				{
					if (is_array($value) and isset($value['video'])) { 
						if (!isset($value['delayed']) or $value['delayed'] == '0') { 
							$HTML .= 'onYouTubePlayerAPIReady_'.$value['unique'].'();';
						}
					}

					if (is_array($value) and isset($value['playlist'])) { 
						if (!isset($value['delayed']) or $value['delayed'] == '0') { 
							$HTML .= 'onYouTubePlayerAPIReady_'.$value['unique'].'();';
						}
					}
				}

				$HTML .= '}';

				// Creazione funzione per caricamento dei player inseriti nella
				// pagina web. creazione del codice javascript per ogni player univoco

				foreach ($this->SZ_GOOGLE_YOUTUBE_API as $value) 
				{
					// Creazione codice per inserimento in embed del video specificato
					// nelle opzioni passate senza utilizzare la tecnica iframe

					if (is_array($value) and isset($value['video'])) 
					{ 
						$HTML .= 'function onYouTubePlayerAPIReady_'.$value['unique'].'() {';
						$HTML .=		"myplayer_".$value['unique']." = new YT.Player('".$value['keyID']."', {";
						$HTML .=			"width:'100%',";
						$HTML .=			"height:'100%',";
						$HTML .=			"videoId:'".$value['video']."',";
						$HTML .=			'playerVars: {';
						$HTML .= 			"'controls':1,";
						$HTML .= 			"'iv_load_policy':3,";
						$HTML .= 			"'autoplay':".$value['autoplay'].",";
						$HTML .= 			"'loop':".$value['loop'].",";
						$HTML .= 			"'rel':".$value['disablerelated'].",";
						$HTML .= 			"'fs':".$value['fullscreen'].",";
						$HTML .= 			"'disablekb':".$value['disablekeyboard'].",";
						$HTML .= 			"'theme':'".$value['theme']."',";
						$HTML .= 			"'start':'".$value['start']."',";
						$HTML .= 			"'wmode':'opaque'";
						$HTML .=			'},';     			
						$HTML .=			'events: {';
						$HTML .= 			"'onStateChange':callbackPlayerStatus_".$value['unique'];
						$HTML .=			'}';     			
						$HTML .= 	'});';
						$HTML .= '}';
					}

					// Creazione codice per inserimento in embed della playlist
					// nelle opzioni passate senza utilizzare la tecnica iframe

					if (is_array($value) and isset($value['playlist'])) 
					{ 
						$HTML .= 'function onYouTubePlayerAPIReady_'.$value['unique'].'() {';
						$HTML .=		"myplayer_".$value['unique']." = new YT.Player('".$value['keyID']."', {";
						$HTML .=			"width:'100%',";
						$HTML .=			"height:'100%',";
						$HTML .=			'playerVars: {';
						$HTML .= 			"'listType':'playlist',";
						$HTML .= 			"'list':'".$value['playlist']."',";
						$HTML .= 			"'controls':1,";
						$HTML .= 			"'iv_load_policy':3,";
						$HTML .= 			"'autoplay':".$value['autoplay'].",";
						$HTML .= 			"'loop':".$value['loop'].",";
						$HTML .= 			"'rel':".$value['disablerelated'].",";
						$HTML .= 			"'fs':".$value['fullscreen'].",";
						$HTML .= 			"'disablekb':".$value['disablekeyboard'].",";
						$HTML .= 			"'theme':'".$value['theme']."',";
						$HTML .= 			"'wmode':'opaque'";
						$HTML .=			'},';     			
						$HTML .=			'events: {';
						$HTML .= 			"'onStateChange':callbackPlayerStatus_".$value['unique'];
						$HTML .=			'}';     			
						$HTML .= 	'});';
						$HTML .= '}';
					}
				}

				// Creazione funzione per caricamento codice google analytics da
				// collegare ad ogni singolo player presente sulla pagina web

				foreach ($this->SZ_GOOGLE_YOUTUBE_API as $value) 
				{
					// Creazione codice per inserimento in embed del video specificato
					// nelle opzioni passate senza utilizzare la tecnica iframe

					if (is_array($value) and isset($value['video'])) 
					{ 
						$HTML .= 'function callbackPlayerStatus_'.$value['unique'].'(event) {';

						if (isset($value['analytics']) and $value['analytics'] == '1') 
						{
							$HTML .=		'switch (event.data){';
							$HTML .=			'case YT.PlayerState.PLAYING:';
							$HTML .=				"_gaq.push(['_trackEvent','Video','Playing',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=				'break;';
							$HTML .= 		'case YT.PlayerState.ENDED:';
							$HTML .=				"_gaq.push(['_trackEvent','Video','Ended',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=				'break;';
							$HTML .=			'case YT.PlayerState.PAUSED:';
							$HTML .=				"_gaq.push(['_trackEvent','Video','Paused',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=			"break;";
							$HTML .=		'}';
						}

						$HTML .= '}';
					}

					// Creazione codice per inserimento in embed del video specificato
					// nelle opzioni passate senza utilizzare la tecnica iframe

					if (is_array($value) and isset($value['playlist'])) 
					{ 
						$HTML .= 'function callbackPlayerStatus_'.$value['unique'].'(event) {';

						if (isset($value['analytics']) and $value['analytics'] == '1') 
						{
							$HTML .=		'switch (event.data){';
							$HTML .=			'case YT.PlayerState.PLAYING:';
							$HTML .=				"_gaq.push(['_trackEvent','Playlist','Playing',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=				'break;';
							$HTML .= 		'case YT.PlayerState.ENDED:';
							$HTML .=				"_gaq.push(['_trackEvent','Playlist','Ended',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=				'break;';
							$HTML .=			'case YT.PlayerState.PAUSED:';
							$HTML .=				"_gaq.push(['_trackEvent','Playlist','Paused',myplayer_".$value['unique'].".getVideoUrl()]);";
							$HTML .=			"break;";
							$HTML .=		'}';
						}

						$HTML .= '}';
					}
				}

				$HTML .= '</script>'."\n";
	
				// Scrittura codice javascript creato per youtube API

				echo $HTML;
			}
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsYoutube.php');
}
