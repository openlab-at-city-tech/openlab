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

if (!class_exists('SZGoogleActionYoutubePlaylist'))
{
	class SZGoogleActionYoutubePlaylist extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'id'              => '', // default value
				'responsive'      => '', // default value
				'width'           => '', // default value
				'height'          => '', // default value
				'margintop'       => '', // default value
				'marginright'     => '', // default value
				'marginbottom'    => '', // default value
				'marginleft'      => '', // default value
				'marginunit'      => '', // default value
				'autoplay'        => '', // default value
				'loop'            => '', // default value
				'fullscreen'      => '', // default value
				'disablekeyboard' => '', // default value
				'theme'           => '', // default value
				'cover'           => '', // default value
				'delayed'         => '', // default value
				'title'           => '', // default value
				'analytics'       => '', // default value
				'disableiframe'   => '', // default value
				'disablerelated'  => '', // default value
				'action'          => 'shortcode',
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
				'id'              => '', // default value
				'responsive'      => '', // default value
				'width'           => '', // default value
				'height'          => '', // default value
				'margintop'       => '', // default value
				'marginright'     => '', // default value
				'marginbottom'    => '', // default value
				'marginleft'      => '', // default value
				'marginunit'      => '', // default value
				'autoplay'        => '', // default value
				'loop'            => '', // default value
				'fullscreen'      => '', // default value
				'disablekeyboard' => '', // default value
				'theme'           => '', // default value
				'cover'           => '', // default value
				'delayed'         => '', // default value
				'title'           => '', // default value
				'analytics'       => '', // default value
				'disableiframe'   => '', // default value
				'disablerelated'  => '', // default value
				'action'          => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleYoutube');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$id              = trim($id);
			$title           = trim($title);
			$cover           = trim($cover);
			$width           = strtolower(trim($width));
			$height          = strtolower(trim($height));
			$responsive      = strtolower(trim($responsive));
			$margintop       = strtolower(trim($margintop));
			$marginright     = strtolower(trim($marginright));
			$marginbottom    = strtolower(trim($marginbottom));
			$marginleft      = strtolower(trim($marginleft));
			$marginunit      = strtolower(trim($marginunit));
			$autoplay        = strtolower(trim($autoplay));
			$loop            = strtolower(trim($loop));
			$fullscreen      = strtolower(trim($fullscreen));
			$disablekeyboard = strtolower(trim($disablekeyboard));
			$theme           = strtolower(trim($theme));
			$delayed         = strtolower(trim($delayed));
			$analytics       = strtolower(trim($analytics));
			$disableiframe   = strtolower(trim($disableiframe));
			$disablerelated  = strtolower(trim($disablerelated));

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($width           == '') $width           = $options->youtube_width;
			if ($height          == '') $height          = $options->youtube_height;
			if ($responsive      == '') $responsive      = $options->youtube_responsive;
			if ($margintop       == '') $margintop       = $options->youtube_margin_top;
			if ($marginright     == '') $marginright     = $options->youtube_margin_right;
			if ($marginbottom    == '') $marginbottom    = $options->youtube_margin_bottom;
			if ($marginleft      == '') $marginleft      = $options->youtube_margin_left;
			if ($marginunit      == '') $marginunit      = $options->youtube_margin_unit;
			if ($autoplay        == '') $autoplay        = $options->youtube_autoplay;
			if ($loop            == '') $loop            = $options->youtube_loop;
			if ($fullscreen      == '') $fullscreen      = $options->youtube_fullscreen;
			if ($disablekeyboard == '') $disablekeyboard = $options->youtube_disablekeyboard;
			if ($theme           == '') $theme           = $options->youtube_theme;
			if ($cover           == '') $cover           = $options->youtube_cover;
			if ($delayed         == '') $delayed         = $options->youtube_delayed;
			if ($analytics       == '') $analytics       = $options->youtube_analytics;
			if ($disableiframe   == '') $disableiframe   = $options->youtube_disableiframe;
			if ($disablerelated  == '') $disablerelated  = $options->youtube_disablerelated;

			// Conversione dei valori specificati direttamete nei parametri con
			// i valori usati per la memorizzazione dei valori di default

			if ($responsive      == 'yes' or $responsive      == 'y') $responsive      = '1'; 
			if ($autoplay        == 'yes' or $autoplay        == 'y') $autoplay        = '1'; 
			if ($loop            == 'yes' or $loop            == 'y') $loop            = '1'; 
			if ($fullscreen      == 'yes' or $fullscreen      == 'y') $fullscreen      = '1'; 
			if ($disablekeyboard == 'yes' or $disablekeyboard == 'y') $disablekeyboard = '1'; 
			if ($delayed         == 'yes' or $delayed         == 'y') $delayed         = '1'; 
			if ($analytics       == 'yes' or $analytics       == 'y') $analytics       = '1'; 
			if ($disableiframe   == 'yes' or $disableiframe   == 'y') $disableiframe   = '1'; 
			if ($disablerelated  == 'yes' or $disablerelated  == 'y') $disablerelated  = '1'; 

			if ($responsive      == 'no'  or $responsive      == 'n') $responsive      = '0'; 
			if ($autoplay        == 'no'  or $autoplay        == 'n') $autoplay        = '0'; 
			if ($loop            == 'no'  or $loop            == 'n') $loop            = '0'; 
			if ($fullscreen      == 'no'  or $fullscreen      == 'n') $fullscreen      = '0'; 
			if ($disablekeyboard == 'no'  or $disablekeyboard == 'n') $disablekeyboard = '0'; 
			if ($delayed         == 'no'  or $delayed         == 'n') $delayed         = '0'; 
			if ($analytics       == 'no'  or $analytics       == 'n') $analytics       = '0'; 
			if ($disableiframe   == 'no'  or $disableiframe   == 'n') $disableiframe   = '0'; 
			if ($disablerelated  == 'no'  or $disablerelated  == 'n') $disablerelated  = '0'; 

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			$YESNO = array('1','0');

			if (!in_array($responsive,$YESNO))      $responsive      = $options->youtube_responsive;
			if (!in_array($autoplay,$YESNO))        $autoplay        = $options->youtube_autoplay;
			if (!in_array($loop,$YESNO))            $loop            = $options->youtube_loop;
			if (!in_array($fullscreen,$YESNO))      $fullscreen      = $options->youtube_fullscreen;
			if (!in_array($disablekeyboard,$YESNO)) $disablekeyboard = $options->youtube_disablekeyboard;
			if (!in_array($delayed,$YESNO))         $delayed         = $options->youtube_delayed;
			if (!in_array($analytics,$YESNO))       $analytics       = $options->youtube_analytics;
			if (!in_array($disableiframe,$YESNO))   $disableiframe   = $options->youtube_disableiframe;
			if (!in_array($disablerelated,$YESNO))  $disablerelated  = $options->youtube_disablerelated;

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if (!is_numeric($width))        $width        = $options->youtube_width;
			if (!is_numeric($height))       $height       = $options->youtube_height;
			if (!is_numeric($margintop))    $margintop    = $options->youtube_margin_top;
			if (!is_numeric($marginbottom)) $marginbottom = $options->youtube_margin_bottom;

			if (!is_numeric($width))        $width        = '600';
			if (!is_numeric($height))       $height       = '400';
			if (!is_numeric($margintop))    $margintop    = '0';
			if (!is_numeric($marginbottom)) $marginbottom = '0';

			if (!is_numeric($marginright) and strtolower(trim($marginright)) <> 'auto') $marginright = $options->youtube_margin_right;
			if (!is_numeric($marginleft)  and strtolower(trim($marginleft))  <> 'auto') $marginleft  = $options->youtube_margin_left;

			if (!is_numeric($marginright) and strtolower(trim($marginright)) <> 'auto') $marginright = '';
			if (!is_numeric($marginleft)  and strtolower(trim($marginleft))  <> 'auto') $marginleft  = '';

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if (!in_array($marginunit,array('em','px')))    $marginunit = $options->youtube_margin_unit;
			if (!in_array($theme,array('dark','light')))    $theme      = $options->youtube_theme;

			if (!in_array($marginunit,array('em','px')))    $marginunit = 'em'; 
			if (!in_array($theme,array('dark','light')))    $theme      = 'dark'; 

			// Se ho impostato la modalità responsive la dimensione è sempre 100%
			// per occupare tutto lo spazio del contenitore genitore, stesso controllo per valore=0

			if ($responsive == '1' or $width == '0') $CSS = 'width:100%;';
				else $CSS = 'width:'.$width.'px;';

			if ($responsive == '1') { $marginright = '0';$marginleft  = '0'; }

			if ($autoplay        == '1') $AUTOPLAY        = '1'; else $AUTOPLAY        = '0';
			if ($loop            == '1') $LOOP            = '1'; else $LOOP            = '0';
			if ($fullscreen      == '1') $FULLSCREEN      = '1'; else $FULLSCREEN      = '0';
			if ($disablekeyboard == '1') $DISABLEKEYBOARD = '1'; else $DISABLEKEYBOARD = '0';

			// Creazione del codice CSS per la composizione dei margini
			// usando le opzioni specificate negli shortcode o nelle funzioni PHP

			$CSS .= $this->getModuleObject('SZGoogleModuleYoutube')->getStyleCSSfromMargins(
				$margintop,$marginright,$marginbottom,$marginleft,$marginunit);

			// Se non ho trovato nessuna playlist ID durante l'analisi URL
			// preparo codice HTML per indicare errore di elaborazione funzione

			if ($id == '') 
			{
				$HTML  = '<div class="sz-youtube-main" style="'.$CSS.'">';
				$HTML .= '<div class="sz-youtube-warn" style="display:block;padding:1em 0;text-align:center;background-color:#e1e1e1;border:1px solid #b1b1b1;">';
				$HTML .= ucfirst(SZGoogleCommon::getTranslate('youtube playlist specified is not valid.'));
				$HTML .= '</div>';
				$HTML .= '</div>';

				return $HTML;
			}

			// Creazione identificativo univoco per riconoscere il codice embed 
			// nel caso la funzioine venga richiamata più volte nella stessa pagina

			$unique = md5(uniqid(),false);
			$keyID  = 'sz-youtube-'.$unique;

			// Creazione variabili per gestire le immagini di copertina e 
			// la modalità di caricamento codice embed ritardato

			$ONCLICK    = '';
			$CSSIMAGE_1 = 'display:block;';
			$CSSIMAGE_2 = 'display:block;';
			$COVERIMAGE = trim($cover);
			$COVERPLAYS = plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'frontend/files/images/youtube-play.png';

			// Creazione variabili per gestire le immagini di copertina e 
			// la modalità di caricamento codice embed ritardato

			if (ctype_digit($COVERIMAGE)) {
				$COVERSRC = wp_get_attachment_image_src($COVERIMAGE,'full');
				if (isset($COVERSRC[0])) $COVERIMAGE = $COVERSRC[0]; else $COVERIMAGE = 'local'; 
			}

			if (strtolower($COVERIMAGE) == 'youtube' or strtolower($COVERIMAGE) == 'local') {
				$COVERIMAGE = plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'frontend/files/images/youtube-playlist.jpg';
			}

			// Creazione variabili per gestire le immagini di copertina e 
			// la modalità di caricamento codice embed ritardato

			if ($delayed == '1') 
			{
				$CSSIMAGE_1 .= 'cursor:pointer;';
				$CSSIMAGE_1 .= 'background-color:#f1f1f1;';
				$CSSIMAGE_1 .= 'background-image:url('.$COVERIMAGE.');';
				$CSSIMAGE_1 .= "background-repeat:no-repeat;";
				$CSSIMAGE_1 .= "background-position:center center;";
				$CSSIMAGE_1 .= "background-size:100% 100%;";

				$CSSIMAGE_2 .= 'background-image:url('.$COVERPLAYS.');';
				$CSSIMAGE_2 .= "background-repeat:no-repeat;";
				$CSSIMAGE_2 .= "background-position:center center;";
				$CSSIMAGE_2 .= "background-size:20% auto";

				$ONCLICK     = ' onclick="javascript:onYouTubePlayerAPIReady_'.$unique.'();"';

				$AUTOPLAY = '1'; 
				$disableiframe = '1'; 
			}

			// SE ATTIVATA FUNZIONE PER STATISTICHE ANALYTICS DEVO FORZARE 
			// ESECUZIONE DEL CODICE EMBED TRAMITE YOUTUBE API 

			if ($analytics == '1') {
				$disableiframe = '1'; 
			}

			// Creazione variabile da usare per lo schema.org in caso di attivazione
			// opzione, vengono usate le specifiche di http://schema.org/VideoObject 

			$EMBEDURL = 'https://www.youtube.com/embed/videoseries?list='.$id;

			if ($disablerelated == '1') $DISABLERELATED = '0';
				else $DISABLERELATED = '1';

			// Creazione codice HTML con controllo inserimento schema.org, se il sistema
			// è abilitato vengono usate le specifiche di http://schema.org/VideoObject 

			$HTML = '<div class="sz-youtube-main" style="'.$CSS.'">';

			// Creazione codice HTML per inserimento nella pagina, la tecnica usata
			// può essere la definizione di un IFRAME e la chiamata ad una funzione API 

			$HTML .= '<div class="sz-youtube-play" style="'.$CSSIMAGE_1.'"'.$ONCLICK.'>';

			if ($responsive == '1')
			{
				$HTML .= '<div class="sz-youtube-cont" ';
				$HTML .= 'style="';
				$HTML .= 'position:relative;';
				$HTML .= 'padding-bottom:56.25%;';
				$HTML .= 'height:0;';
				$HTML .= 'overflow:hidden;';
				$HTML .= $CSSIMAGE_2;
				$HTML .= '">';

			} else {

				$HTML .= '<div class="sz-youtube-cont" ';
				$HTML .= 'style="';
				$HTML .= 'position:relative;';
				$HTML .= 'height:'.$height.'px;';
				$HTML .= $CSSIMAGE_2;
				$HTML .= '">';
			}

			// Creazione codice HTML per embed code, normalmente utilizzo IFRAME
			// ma se questo è stato disattivato specificatamente utilizzo javascript API 

			if ($disableiframe == '1') 
			{
				$HTML .= '<div class="sz-youtube-wrap" style="display:block;">';
				$HTML .= '<div class="sz-youtube-japi" id="'.$keyID.'" style="position:absolute;top:0;left:0;display:block;"></div>';
				$HTML .= '</div>';

				$object = $this->getModuleObject('SZGoogleModuleYoutube');

				$object->addYoutubeVideoAPI(array(
					'unique'          => $unique,
					'keyID'           => $keyID,
					'playlist'        => $id,
					'autoplay'        => $AUTOPLAY,
					'loop'            => $LOOP,
					'fullscreen'      => $FULLSCREEN,
					'disablekeyboard' => $DISABLEKEYBOARD,
					'theme'           => $theme,
					'cover'           => $cover,
					'delayed'         => $delayed,
					'analytics'       => $analytics,
					'disablerelated'  => $DISABLERELATED,
				)
			);

			// Creazione codice HTML per embed code, normalmente utilizzo IFRAME
			// ma se questo è stato disattivato specificatamente utilizzo javascript API 

			} else { 

				$HTML .= '<div class="sz-youtube-wrap" id="'.$keyID.'" style="display:block;">';
				$HTML .= '<iframe ';
				$HTML .= 'src="'.$EMBEDURL;
				$HTML .= '&amp;wmode=opaque';
				$HTML .= '&amp;controls=1';
				$HTML .= '&amp;iv_load_policy=3';
				$HTML .= '&amp;autoplay='.$AUTOPLAY;
				$HTML .= '&amp;loop='.$LOOP;
				$HTML .= '&amp;fs='.$FULLSCREEN;
				$HTML .= '&amp;disablekb='.$DISABLEKEYBOARD;
				$HTML .= '&amp;rel='.$DISABLERELATED;
				$HTML .= '&amp;theme='.$theme;
				$HTML .= '" ';
				$HTML .= 'style="position:absolute;top:0;left:0;width:100%;height:100%;"';
				$HTML .= '>';
				$HTML .= '</iframe>';
				$HTML .= '</div>';
			}

			$HTML .= '</div>';
			$HTML .= '</div>';

			// Creazione blocco del titolo sotto il video youtube, la stringa
			// viene passata tramite il paramtero "title" dello shortcode. 

			if ($title != '') 
			{
				$HTML .= '<div class="sz-youtube-capt" ';
				$HTML .= 'style="background-color:#e8e8e8;padding:0.5em 1em;text-align:center;font-weight:bold;margin-top:5px;"';
				$HTML .= '>';
				$HTML .= $title;
				$HTML .= '</div>';
			}

			$HTML .= '</div>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}