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

if (!class_exists('SZGoogleActionFontsTinyMCE'))
{
	class SZGoogleActionFontsTinyMCE extends SZGoogleAction
	{
		/**
		 * Aggiungo nella fase del costruttore i filtri e le azioni
		 * necessarie a controllare il login con codice a tempo
		 */
		function __construct() 
		{		
			// Calcolo per opzioni di configurazione collegate al modulo
			// richiesto e specificate nel pannello di amministrazione
			
			$options = (object) $this->getModuleOptions('SZGoogleModuleFonts');

			// Controllo i componenti che devo aggiungere a TinyMCE
			// come il selettore della famiglia da applicare alla selezione

			if ($options->fonts_tinyMCE_family == '1') {
				add_filter('mce_buttons_2',array($this,'add_mce_fonts_family'));
			}

			// Controllo i componenti che devo aggiungere a TinyMCE
			// come il selettore della dimensione da applicare alla selezione

			if ($options->fonts_tinyMCE_size == '1') {
				add_filter('mce_buttons_2',array($this,'add_mce_fonts_size'));
			}
		}

		/**
		 * Definizione della funzione che verrà richiamata nel
		 * caso bisognerà aggiungere il selettore del Font Family
		 */ 
		function add_mce_fonts_family($buttons) {
			array_unshift($buttons,'fontselect');
			return $buttons;
		}
 
		/**
		 * Definizione della funzione che verrà richiamata nel
		 * caso bisognerà aggiungere il selettore del Font Size
		 */ 
		function add_mce_fonts_size($buttons) {
			array_unshift($buttons,'fontsizeselect');
			return $buttons;
		}
	}
}