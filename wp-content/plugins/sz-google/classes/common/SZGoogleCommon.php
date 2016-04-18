<?php

/**
 * Class for executing functions of general use or 
 * calculation of variables to be used in plugin
 *
 * @package SZGoogle
 * @subpackage Classes
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleCommon'))
{
	class SZGoogleCommon
	{
		/**
		 * Calculating the name of Domnio current used 
		 * by the page displayed. Using the get_site_url()
		 */

		static function getCurrentDomain()
		{
			$pieces = parse_url(get_site_url());
  			$domain = isset($pieces['host']) ? $pieces['host'] : '';

			if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i',$domain,$regs)) {
    			return $regs['domain'];
  			}

			return false;
		}

		/**
		 * Execution flush for actual rewrite rules. This function should
		 * not be called always but only when functions are activated
		 */

		static function rewriteFlushRules() 
		{
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}

		/**
		 * Translation of the strings that relate to the frontend, in fact domains
		 * between admin and frontend are different (szgoogle and sz-google)
		 */

		static function getTranslate($string) {
			return __($string,'szgooglefront');
		}

		/**
		 * List of languages ​​present in google to be used
		 * in many forms in the plugin as a list standard
		 */

		static function getLanguages()
		{
			// Preparation array with the code language supported by google,
			// this array can be used for different modules

			$languages = array(
				'99'     => ' '.self::getTranslate('same language theme'),
				'af'     => ucfirst(self::getTranslate('afrikaans')),
				'am'     => ucfirst(self::getTranslate('amharic')),
				'ar'     => ucfirst(self::getTranslate('arabic')),
				'eu'     => ucfirst(self::getTranslate('basque')),
				'bn'     => ucfirst(self::getTranslate('bengali')),
				'bg'     => ucfirst(self::getTranslate('bulgarian')),
				'ca'     => ucfirst(self::getTranslate('catalan')),
				'zh-HK'  => ucfirst(self::getTranslate('chinese (Hong Kong)')),
				'zh-CN'  => ucfirst(self::getTranslate('chinese (Simplified)')),
				'zh-TW'  => ucfirst(self::getTranslate('chinese (Traditional)')),
				'hr'     => ucfirst(self::getTranslate('croatian')),
				'cs'     => ucfirst(self::getTranslate('czech')),
				'da'     => ucfirst(self::getTranslate('danish')),
				'nl'     => ucfirst(self::getTranslate('dutch')),
				'en-GB'  => ucfirst(self::getTranslate('english (UK)')),
				'en-US'  => ucfirst(self::getTranslate('english (US)')),
				'et'     => ucfirst(self::getTranslate('estonian')),
				'fil'    => ucfirst(self::getTranslate('filipino')),
				'fi'     => ucfirst(self::getTranslate('finnish')),
				'fr'     => ucfirst(self::getTranslate('french')),
				'fr-CA'  => ucfirst(self::getTranslate('french (Canadian)')),
				'gl'     => ucfirst(self::getTranslate('galician')),
				'de'     => ucfirst(self::getTranslate('german')),
				'el'     => ucfirst(self::getTranslate('greek')),
				'gu'     => ucfirst(self::getTranslate('gujarati')),
				'iw'     => ucfirst(self::getTranslate('hebrew')),
				'hi'     => ucfirst(self::getTranslate('hindi')),
				'hu'     => ucfirst(self::getTranslate('hungarian')),
				'is'     => ucfirst(self::getTranslate('icelandic')),
				'id'     => ucfirst(self::getTranslate('indonesian')),
				'it'     => ucfirst(self::getTranslate('italian')),
				'ja'     => ucfirst(self::getTranslate('japanese')),
				'kn'     => ucfirst(self::getTranslate('kannada')),
				'ko'     => ucfirst(self::getTranslate('korean')),
				'lv'     => ucfirst(self::getTranslate('latvian')),
				'lt'     => ucfirst(self::getTranslate('lithuanian')),
				'ms'     => ucfirst(self::getTranslate('malay')),
				'ml'     => ucfirst(self::getTranslate('malayalam')),
				'mr'     => ucfirst(self::getTranslate('marathi')),
				'no'     => ucfirst(self::getTranslate('norwegian')),
				'fa'     => ucfirst(self::getTranslate('persian')),
				'pl'     => ucfirst(self::getTranslate('polish')), 	
				'pt-BR'  => ucfirst(self::getTranslate('portuguese (Brazil)')),
				'pt-PT'  => ucfirst(self::getTranslate('portuguese (Portugal)')),
				'ro'     => ucfirst(self::getTranslate('romanian')),
				'ru'     => ucfirst(self::getTranslate('russian')),
				'sr'     => ucfirst(self::getTranslate('serbian')),
				'sk'     => ucfirst(self::getTranslate('slovak')),
				'sl'     => ucfirst(self::getTranslate('slovenian')),
				'es'     => ucfirst(self::getTranslate('spanish')),
				'es-419' => ucfirst(self::getTranslate('spanish (Latin America)')),
				'sw'     => ucfirst(self::getTranslate('swahili')),
				'sv'     => ucfirst(self::getTranslate('swedish')),
				'ta'     => ucfirst(self::getTranslate('tamil')),
				'te'     => ucfirst(self::getTranslate('telugu')),
				'th'     => ucfirst(self::getTranslate('thai')),
				'tr'     => ucfirst(self::getTranslate('turkish')),
				'uk'     => ucfirst(self::getTranslate('ukrainian')),
				'ur'     => ucfirst(self::getTranslate('urdu')),
				'vi'     => ucfirst(self::getTranslate('vietnamese')),
				'zu'     => ucfirst(self::getTranslate('zulu')),
			);

			// I execute sorting array according to the nation and
			// to the string translation performed after rendering

			asort($languages);
			return $languages;
		}

		/**
		 * List of time zones to be used in the components of google
		 * This list was taken from creation function calendar
		 */

		static function getTimeZone()
		{
			// Preparation array with the code language supported by google,
			// this array can be used for different modules

			$timezone = array(
				'none'                           => self::getTranslate('default time zone'),
				'Pacific/Midway'                 => self::getTranslate('(GMT-11:00) Midway'), 
				'Pacific/Niue'                   => self::getTranslate('(GMT-11:00) Niue'), 
				'Pacific/Pago_Pago'              => self::getTranslate('(GMT-11:00) Pago Pago'), 
				'Pacific/Honolulu'               => self::getTranslate('(GMT-10:00) Hawaii Time'), 
				'Pacific/Rarotonga'              => self::getTranslate('(GMT-10:00) Rarotonga'), 
				'Pacific/Tahiti'                 => self::getTranslate('(GMT-10:00) Tahiti'), 
				'Pacific/Marquesas'              => self::getTranslate('(GMT-09:30) Marquesas'), 
				'America/Anchorage'              => self::getTranslate('(GMT-09:00) Alaska Time'), 
				'Pacific/Gambier'                => self::getTranslate('(GMT-09:00) Gambier'), 
				'America/Los_Angeles'            => self::getTranslate('(GMT-08:00) Pacific Time'), 
				'America/Tijuana'                => self::getTranslate('(GMT-08:00) Pacific Time - Tijuana'), 
				'America/Vancouver'              => self::getTranslate('(GMT-08:00) Pacific Time - Vancouver'), 
				'America/Whitehorse'             => self::getTranslate('(GMT-08:00) Pacific Time - Whitehorse'), 
				'Pacific/Pitcairn'               => self::getTranslate('(GMT-08:00) Pitcairn'), 
				'America/Dawson_Creek'           => self::getTranslate('(GMT-07:00) Mountain Time - Dawson Creek'), 
				'America/Denver'                 => self::getTranslate('(GMT-07:00) Mountain Time'), 
				'America/Edmonton'               => self::getTranslate('(GMT-07:00) Mountain Time - Edmonton'), 
				'America/Hermosillo'             => self::getTranslate('(GMT-07:00) Mountain Time - Hermosillo'), 
				'America/Mazatlan'               => self::getTranslate('(GMT-07:00) Mountain Time - Chihuahua, Mazatlan'), 
				'America/Phoenix'                => self::getTranslate('(GMT-07:00) Mountain Time - Arizona'), 
				'America/Yellowknife'            => self::getTranslate('(GMT-07:00) Mountain Time - Yellowknife'), 
				'America/Belize'                 => self::getTranslate('(GMT-06:00) Belize'), 
				'America/Chicago'                => self::getTranslate('(GMT-06:00) Central Time'), 
				'America/Costa_Rica'             => self::getTranslate('(GMT-06:00) Costa Rica'), 
				'America/El_Salvador'            => self::getTranslate('(GMT-06:00) El Salvador'), 
				'America/Guatemala'              => self::getTranslate('(GMT-06:00) Guatemala'), 
				'America/Managua'                => self::getTranslate('(GMT-06:00) Managua'), 
				'America/Mexico_City'            => self::getTranslate('(GMT-06:00) Central Time - Mexico City'), 
				'America/Regina'                 => self::getTranslate('(GMT-06:00) Central Time - Regina'), 
				'America/Tegucigalpa'            => self::getTranslate('(GMT-06:00) Central Time - Tegucigalpa'), 
				'America/Winnipeg'               => self::getTranslate('(GMT-06:00) Central Time - Winnipeg'), 
				'Pacific/Easter'                 => self::getTranslate('(GMT-06:00) Easter Island'), 
				'Pacific/Galapagos'              => self::getTranslate('(GMT-06:00) Galapagos'), 
				'America/Bogota'                 => self::getTranslate('(GMT-05:00) Bogota'), 
				'America/Cayman'                 => self::getTranslate('(GMT-05:00) Cayman'), 
				'America/Grand_Turk'             => self::getTranslate('(GMT-05:00) Grand Turk'), 
				'America/Guayaquil'              => self::getTranslate('(GMT-05:00) Guayaquil'), 
				'America/Havana'                 => self::getTranslate('(GMT-05:00) Havana'), 
				'America/Iqaluit'                => self::getTranslate('(GMT-05:00) Eastern Time - Iqaluit'), 
				'America/Jamaica'                => self::getTranslate('(GMT-05:00) Jamaica'), 
				'America/Lima'                   => self::getTranslate('(GMT-05:00) Lima'), 
				'America/Montreal'               => self::getTranslate('(GMT-05:00) Eastern Time - Montreal'), 
				'America/Nassau'                 => self::getTranslate('(GMT-05:00) Nassau'), 
				'America/New_York'               => self::getTranslate('(GMT-05:00) Eastern Time'), 
				'America/Panama'                 => self::getTranslate('(GMT-05:00) Panama'), 
				'America/Port-au-Prince'         => self::getTranslate('(GMT-05:00) Port-au-Prince'), 
				'America/Rio_Branco'             => self::getTranslate('(GMT-05:00) Rio Branco'), 
				'America/Toronto'                => self::getTranslate('(GMT-05:00) Eastern Time - Toronto'), 
				'America/Caracas'                => self::getTranslate('(GMT-04:30) Caracas'), 
				'America/Antigua'                => self::getTranslate('(GMT-04:00) Antigua'), 
				'America/Asuncion'               => self::getTranslate('(GMT-04:00) Asuncion'), 
				'America/Barbados'               => self::getTranslate('(GMT-04:00) Barbados'), 
				'America/Boa_Vista'              => self::getTranslate('(GMT-04:00) Boa Vista'), 
				'America/Campo_Grande'           => self::getTranslate('(GMT-04:00) Campo Grande'), 
				'America/Cuiaba'                 => self::getTranslate('(GMT-04:00) Cuiaba'), 
				'America/Curacao'                => self::getTranslate('(GMT-04:00) Curacao'), 
				'America/Guyana'                 => self::getTranslate('(GMT-04:00) Guyana'), 
				'America/Halifax'                => self::getTranslate('(GMT-04:00) Atlantic Time - Halifax'), 
				'America/La_Paz'                 => self::getTranslate('(GMT-04:00) La Paz'), 
				'America/Manaus'                 => self::getTranslate('(GMT-04:00) Manaus'), 
				'America/Martinique'             => self::getTranslate('(GMT-04:00) Martinique'), 
				'America/Port_of_Spain'          => self::getTranslate('(GMT-04:00) Port of Spain'), 
				'America/Porto_Velho'            => self::getTranslate('(GMT-04:00) Porto Velho'), 
				'America/Puerto_Rico'            => self::getTranslate('(GMT-04:00) Puerto Rico'), 
				'America/Santiago'               => self::getTranslate('(GMT-04:00) Santiago'), 
				'America/Santo_Domingo'          => self::getTranslate('(GMT-04:00) Santo Domingo'), 
				'America/Thule'                  => self::getTranslate('(GMT-04:00) Thule'), 
				'Antarctica/Palmer'              => self::getTranslate('(GMT-04:00) Palmer'), 
				'Atlantic/Bermuda'               => self::getTranslate('(GMT-04:00) Bermuda'), 
				'America/St_Johns'               => self::getTranslate('(GMT-03:30) Newfoundland Time - St. Johns'), 
				'America/Araguaina'              => self::getTranslate('(GMT-03:00) Araguaina'), 
				'America/Argentina/Buenos_Aires' => self::getTranslate('(GMT-03:00) Buenos Aires'),
				'America/Bahia'                  => self::getTranslate('(GMT-03:00) Salvador'), 
				'America/Belem'                  => self::getTranslate('(GMT-03:00) Belem'), 
				'America/Cayenne'                => self::getTranslate('(GMT-03:00) Cayenne'), 
				'America/Fortaleza'              => self::getTranslate('(GMT-03:00) Fortaleza'), 
				'America/Godthab'                => self::getTranslate('(GMT-03:00) Godthab'), 
				'America/Maceio'                 => self::getTranslate('(GMT-03:00) Maceio'), 
				'America/Miquelon'               => self::getTranslate('(GMT-03:00) Miquelon'), 
				'America/Montevideo'             => self::getTranslate('(GMT-03:00) Montevideo'), 
				'America/Paramaribo'             => self::getTranslate('(GMT-03:00) Paramaribo'), 
				'America/Recife'                 => self::getTranslate('(GMT-03:00) Recife'), 
				'America/Sao_Paulo'              => self::getTranslate('(GMT-03:00) Sao Paulo'), 
				'Antarctica/Rothera'             => self::getTranslate('(GMT-03:00) Rothera'), 
				'Atlantic/Stanley'               => self::getTranslate('(GMT-03:00) Stanley'), 
				'America/Noronha'                => self::getTranslate('(GMT-02:00) Noronha'), 
				'Atlantic/South_Georgia'         => self::getTranslate('(GMT-02:00) South Georgia'), 
				'America/Scoresbysund'           => self::getTranslate('(GMT-01:00) Scoresbysund'), 
				'Atlantic/Azores'                => self::getTranslate('(GMT-01:00) Azores'), 
				'Atlantic/Cape_Verde'            => self::getTranslate('(GMT-01:00) Cape Verde'), 
				'Africa/Abidjan'                 => self::getTranslate('(GMT+00:00) Abidjan'), 
				'Africa/Accra'                   => self::getTranslate('(GMT+00:00) Accra'), 
				'Africa/Bamako'                  => self::getTranslate('(GMT+00:00) Bamako'), 
				'Africa/Banjul'                  => self::getTranslate('(GMT+00:00) Banjul'), 
				'Africa/Bissau'                  => self::getTranslate('(GMT+00:00) Bissau'),
				'Africa/Casablanca'              => self::getTranslate('(GMT+00:00) Casablanca'),
				'Africa/Conakry'                 => self::getTranslate('(GMT+00:00) Conakry'),
				'Africa/Dakar'                   => self::getTranslate('(GMT+00:00) Dakar'),
				'Africa/El_Aaiun'                => self::getTranslate('(GMT+00:00) El Aaiun'),
				'Africa/Freetown'                => self::getTranslate('(GMT+00:00) Freetown'),
				'Africa/Lome'                    => self::getTranslate('(GMT+00:00) Lome'),
				'Africa/Monrovia'                => self::getTranslate('(GMT+00:00) Monrovia'), 
				'Africa/Nouakchott'              => self::getTranslate('(GMT+00:00) Nouakchott'),
				'Africa/Ouagadougou'             => self::getTranslate('(GMT+00:00) Ouagadougou'),
				'Africa/Sao_Tome'                => self::getTranslate('(GMT+00:00) Sao Tome'),
				'America/Danmarkshavn'           => self::getTranslate('(GMT+00:00) Danmarkshavn'),
				'Atlantic/Canary'                => self::getTranslate('(GMT+00:00) Canary Islands'), 
				'Atlantic/Faroe'                 => self::getTranslate('(GMT+00:00) Faeroe'), 
				'Atlantic/Reykjavik'             => self::getTranslate('(GMT+00:00) Reykjavik'), 
				'Atlantic/St_Helena'             => self::getTranslate('(GMT+00:00) St Helena'), 
				'Etc/GMT'                        => self::getTranslate('(GMT+00:00) GMT (no daylight saving)'), 
				'Europe/Dublin'                  => self::getTranslate('(GMT+00:00) Dublin'), 
				'Europe/Lisbon'                  => self::getTranslate('(GMT+00:00) Lisbon'), 
				'Europe/London'                  => self::getTranslate('(GMT+00:00) London'), 
				'Africa/Algiers'                 => self::getTranslate('(GMT+01:00) Algiers'), 
				'Africa/Bangui'                  => self::getTranslate('(GMT+01:00) Bangui'), 
				'Africa/Brazzaville'             => self::getTranslate('(GMT+01:00) Brazzaville'), 
				'Africa/Ceuta'                   => self::getTranslate('(GMT+01:00) Ceuta'), 
				'Africa/Douala'                  => self::getTranslate('(GMT+01:00) Douala'), 
				'Africa/Kinshasa'                => self::getTranslate('(GMT+01:00) Kinshasa'), 
				'Africa/Lagos'                   => self::getTranslate('(GMT+01:00) Lagos'), 
				'Africa/Libreville'              => self::getTranslate('(GMT+01:00) Libreville'), 
				'Africa/Luanda'                  => self::getTranslate('(GMT+01:00) Luanda'), 
				'Africa/Malabo'                  => self::getTranslate('(GMT+01:00) Malabo'), 
				'Africa/Ndjamena'                => self::getTranslate('(GMT+01:00) Ndjamena'), 
				'Africa/Niamey'                  => self::getTranslate('(GMT+01:00) Niamey'), 
				'Africa/Porto-Novo'              => self::getTranslate('(GMT+01:00) Porto-Novo'), 
				'Africa/Tunis'                   => self::getTranslate('(GMT+01:00) Tunis'), 
				'Africa/Windhoek'                => self::getTranslate('(GMT+01:00) Windhoek'), 
				'Europe/Amsterdam'               => self::getTranslate('(GMT+01:00) Amsterdam'), 
				'Europe/Andorra'                 => self::getTranslate('(GMT+01:00) Andorra'), 
				'Europe/Belgrade'                => self::getTranslate('(GMT+01:00) Central European Time - Belgrade'), 
				'Europe/Berlin'                  => self::getTranslate('(GMT+01:00) Berlin'), 
				'Europe/Brussels'                => self::getTranslate('(GMT+01:00) Brussels'), 
				'Europe/Budapest'                => self::getTranslate('(GMT+01:00) Budapest'), 
				'Europe/Copenhagen'              => self::getTranslate('(GMT+01:00) Copenhagen'), 
				'Europe/Gibraltar'               => self::getTranslate('(GMT+01:00) Gibraltar'), 
				'Europe/Luxembourg'              => self::getTranslate('(GMT+01:00) Luxembourg'), 
				'Europe/Madrid'                  => self::getTranslate('(GMT+01:00) Madrid'), 
				'Europe/Malta'                   => self::getTranslate('(GMT+01:00) Malta'), 
				'Europe/Monaco'                  => self::getTranslate('(GMT+01:00) Monaco'), 
				'Europe/Oslo'                    => self::getTranslate('(GMT+01:00) Oslo'), 
				'Europe/Paris'                   => self::getTranslate('(GMT+01:00) Paris'), 
				'Europe/Prague'                  => self::getTranslate('(GMT+01:00) Central European Time - Prague'), 
				'Europe/Rome'                    => self::getTranslate('(GMT+01:00) Rome'), 
				'Europe/Stockholm'               => self::getTranslate('(GMT+01:00) Stockholm'), 
				'Europe/Tirane'                  => self::getTranslate('(GMT+01:00) Tirane'), 
				'Europe/Vienna'                  => self::getTranslate('(GMT+01:00) Vienna'), 
				'Europe/Warsaw'                  => self::getTranslate('(GMT+01:00) Warsaw'), 
				'Europe/Zurich'                  => self::getTranslate('(GMT+01:00) Zurich'), 
				'Africa/Blantyre'                => self::getTranslate('(GMT+02:00) Blantyre'), 
				'Africa/Bujumbura'               => self::getTranslate('(GMT+02:00) Bujumbura'), 
				'Africa/Cairo'                   => self::getTranslate('(GMT+02:00) Cairo'), 
				'Africa/Gaborone'                => self::getTranslate('(GMT+02:00) Gaborone'), 
				'Africa/Harare'                  => self::getTranslate('(GMT+02:00) Harare'), 
				'Africa/Johannesburg'            => self::getTranslate('(GMT+02:00) Johannesburg'), 
				'Africa/Kigali'                  => self::getTranslate('(GMT+02:00) Kigali'), 
				'Africa/Lubumbashi'              => self::getTranslate('(GMT+02:00) Lubumbashi'), 
				'Africa/Lusaka'                  => self::getTranslate('(GMT+02:00) Lusaka'), 
				'Africa/Maputo'                  => self::getTranslate('(GMT+02:00) Maputo'), 
				'Africa/Maseru'                  => self::getTranslate('(GMT+02:00) Maseru'), 
				'Africa/Mbabane'                 => self::getTranslate('(GMT+02:00) Mbabane'), 
				'Africa/Tripoli'                 => self::getTranslate('(GMT+02:00) Tripoli'), 
				'Asia/Amman'                     => self::getTranslate('(GMT+02:00) Amman'), 
				'Asia/Beirut'                    => self::getTranslate('(GMT+02:00) Beirut'), 
				'Asia/Damascus'                  => self::getTranslate('(GMT+02:00) Damascus'), 
				'Asia/Gaza'                      => self::getTranslate('(GMT+02:00) Gaza'), 
				'Asia/Jerusalem'                 => self::getTranslate('(GMT+02:00) Jerusalem'), 
				'Asia/Nicosia'                   => self::getTranslate('(GMT+02:00) Nicosia'), 
				'Europe/Athens'                  => self::getTranslate('(GMT+02:00) Athens'), 
				'Europe/Bucharest'               => self::getTranslate('(GMT+02:00) Bucharest'), 
				'Europe/Chisinau'                => self::getTranslate('(GMT+02:00) Chisinau'), 
				'Europe/Helsinki'                => self::getTranslate('(GMT+02:00) Helsinki'), 
				'Europe/Istanbul'                => self::getTranslate('(GMT+02:00) Istanbul'), 
				'Europe/Kiev'                    => self::getTranslate('(GMT+02:00) Kiev'), 
				'Europe/Riga'                    => self::getTranslate('(GMT+02:00) Riga'), 
				'Europe/Sofia'                   => self::getTranslate('(GMT+02:00) Sofia'), 
				'Europe/Tallinn'                 => self::getTranslate('(GMT+02:00) Tallinn'), 
				'Europe/Vilnius'                 => self::getTranslate('(GMT+02:00) Vilnius'), 
				'Africa/Addis_Ababa'             => self::getTranslate('(GMT+03:00) Addis Ababa'), 
				'Africa/Asmara'                  => self::getTranslate('(GMT+03:00) Asmera'), 
				'Africa/Dar_es_Salaam'           => self::getTranslate('(GMT+03:00) Dar es Salaam'), 
				'Africa/Djibouti'                => self::getTranslate('(GMT+03:00) Djibouti'), 
				'Africa/Kampala'                 => self::getTranslate('(GMT+03:00) Kampala'), 
				'Africa/Khartoum'                => self::getTranslate('(GMT+03:00) Khartoum'), 
				'Africa/Mogadishu'               => self::getTranslate('(GMT+03:00) Mogadishu'), 
				'Africa/Nairobi'                 => self::getTranslate('(GMT+03:00) Nairobi'), 
				'Antarctica/Syowa'               => self::getTranslate('(GMT+03:00) Syowa'), 
				'Asia/Aden'                      => self::getTranslate('(GMT+03:00) Aden'), 
				'Asia/Baghdad'                   => self::getTranslate('(GMT+03:00) Baghdad'), 
				'Asia/Bahrain'                   => self::getTranslate('(GMT+03:00) Bahrain'), 
				'Asia/Kuwait'                    => self::getTranslate('(GMT+03:00) Kuwait'), 
				'Asia/Qatar'                     => self::getTranslate('(GMT+03:00) Qatar'), 
				'Asia/Riyadh'                    => self::getTranslate('(GMT+03:00) Riyadh'), 
				'Europe/Kaliningrad'             => self::getTranslate('(GMT+03:00) Moscow-01 - Kaliningrad'), 
				'Europe/Minsk'                   => self::getTranslate('(GMT+03:00) Minsk'), 
				'Indian/Antananarivo'            => self::getTranslate('(GMT+03:00) Antananarivo'), 
				'Indian/Comoro'                  => self::getTranslate('(GMT+03:00) Comoro'), 
				'Indian/Mayotte'                 => self::getTranslate('(GMT+03:00) Mayotte'), 
				'Asia/Tehran'                    => self::getTranslate('(GMT+03:30) Tehran'), 
				'Asia/Baku'                      => self::getTranslate('(GMT+04:00) Baku'), 
				'Asia/Dubai'                     => self::getTranslate('(GMT+04:00) Dubai'), 
				'Asia/Muscat'                    => self::getTranslate('(GMT+04:00) Muscat'), 
				'Asia/Tbilisi'                   => self::getTranslate('(GMT+04:00) Tbilisi'), 
				'Asia/Yerevan'                   => self::getTranslate('(GMT+04:00) Yerevan'), 
				'Europe/Moscow'                  => self::getTranslate('(GMT+04:00) Moscow+00'), 
				'Europe/Samara'                  => self::getTranslate('(GMT+04:00) Moscow+00 - Samara'), 
				'Indian/Mahe'                    => self::getTranslate('(GMT+04:00) Mahe'), 
				'Indian/Mauritius'               => self::getTranslate('(GMT+04:00) Mauritius'), 
				'Indian/Reunion'                 => self::getTranslate('(GMT+04:00) Reunion'), 
				'Asia/Kabul'                     => self::getTranslate('(GMT+04:30) Kabul'), 
				'Antarctica/Mawson'              => self::getTranslate('(GMT+05:00) Mawson'), 
				'Asia/Aqtau'                     => self::getTranslate('(GMT+05:00) Aqtau'), 
				'Asia/Aqtobe'                    => self::getTranslate('(GMT+05:00) Aqtobe'), 
				'Asia/Ashgabat'                  => self::getTranslate('(GMT+05:00) Ashgabat'), 
				'Asia/Dushanbe'                  => self::getTranslate('(GMT+05:00) Dushanbe'), 
				'Asia/Karachi'                   => self::getTranslate('(GMT+05:00) Karachi'), 
				'Asia/Tashkent'                  => self::getTranslate('(GMT+05:00) Tashkent'), 
				'Indian/Kerguelen'               => self::getTranslate('(GMT+05:00) Kerguelen'), 
				'Indian/Maldives'                => self::getTranslate('(GMT+05:00) Maldives'), 
				'Asia/Calcutta'                  => self::getTranslate('(GMT+05:30) India Standard Time'), 
				'Asia/Colombo'                   => self::getTranslate('(GMT+05:30) Colombo'), 
				'Asia/Katmandu'                  => self::getTranslate('(GMT+05:45) Katmandu'), 
				'Antarctica/Vostok'              => self::getTranslate('(GMT+06:00) Vostok'), 
				'Asia/Almaty'                    => self::getTranslate('(GMT+06:00) Almaty'), 
				'Asia/Bishkek'                   => self::getTranslate('(GMT+06:00) Bishkek'), 
				'Asia/Dhaka'                     => self::getTranslate('(GMT+06:00) Dhaka'), 
				'Asia/Thimphu'                   => self::getTranslate('(GMT+06:00) Thimphu'),
				'Asia/Yekaterinburg'             => self::getTranslate('(GMT+06:00) Moscow+02 - Yekaterinburg'), 
				'Indian/Chagos'                  => self::getTranslate('(GMT+06:00) Chagos'), 
				'Asia/Rangoon'                   => self::getTranslate('(GMT+06:30) Rangoon'), 
				'Indian/Cocos'                   => self::getTranslate('(GMT+06:30) Cocos'), 
				'Antarctica/Davis'               => self::getTranslate('(GMT+07:00) Davis'), 
				'Asia/Bangkok'                   => self::getTranslate('(GMT+07:00) Bangkok'), 
				'Asia/Hovd'                      => self::getTranslate('(GMT+07:00) Hovd'), 
				'Asia/Jakarta'                   => self::getTranslate('(GMT+07:00) Jakarta'),
				'Asia/Omsk'                      => self::getTranslate('(GMT+07:00) Moscow+03 - Omsk, Novosibirsk'), 
				'Asia/Phnom_Penh'                => self::getTranslate('(GMT+07:00) Phnom Penh'), 
				'Asia/Saigon'                    => self::getTranslate('(GMT+07:00) Hanoi'),
				'Asia/Vientiane'                 => self::getTranslate('(GMT+07:00) Vientiane'), 
				'Indian/Christmas'               => self::getTranslate('(GMT+07:00) Christmas'), 
				'Antarctica/Casey'               => self::getTranslate('(GMT+08:00) Casey'), 
				'Asia/Brunei'                    => self::getTranslate('(GMT+08:00) Brunei'), 
				'Asia/Choibalsan'                => self::getTranslate('(GMT+08:00) Choibalsan'),
				'Asia/Hong_Kong'                 => self::getTranslate('(GMT+08:00) Hong Kong'), 
				'Asia/Krasnoyarsk'               => self::getTranslate('(GMT+08:00) Moscow+04 - Krasnoyarsk'),
				'Asia/Kuala_Lumpur'              => self::getTranslate('(GMT+08:00) Kuala Lumpur'), 
				'Asia/Macau'                     => self::getTranslate('(GMT+08:00) Macau'), 
				'Asia/Makassar'                  => self::getTranslate('(GMT+08:00) Makassar'), 
				'Asia/Manila'                    => self::getTranslate('(GMT+08:00) Manila'), 
				'Asia/Shanghai'                  => self::getTranslate('(GMT+08:00) China Time - Beijing'), 
				'Asia/Singapore'                 => self::getTranslate('(GMT+08:00) Singapore'),
				'Asia/Taipei'                    => self::getTranslate('(GMT+08:00) Taipei'), 
				'Asia/Ulaanbaatar'               => self::getTranslate('(GMT+08:00) Ulaanbaatar'), 
				'Australia/Perth'                => self::getTranslate('(GMT+08:00) Western Time - Perth'), 
				'Asia/Dili'                      => self::getTranslate('(GMT+09:00) Dili'),
				'Asia/Irkutsk'                   => self::getTranslate('(GMT+09:00) Moscow+05 - Irkutsk'), 
				'Asia/Jayapura'                  => self::getTranslate('(GMT+09:00) Jayapura'), 
				'Asia/Pyongyang'                 => self::getTranslate('(GMT+09:00) Pyongyang'),
				'Asia/Seoul'                     => self::getTranslate('(GMT+09:00) Seoul'), 
				'Asia/Tokyo'                     => self::getTranslate('(GMT+09:00) Tokyo'), 
				'Pacific/Palau'                  => self::getTranslate('(GMT+09:00) Palau'), 
				'Australia/Adelaide'             => self::getTranslate('(GMT+09:30) Central Time - Adelaide'), 
				'Australia/Darwin'               => self::getTranslate('(GMT+09:30) Central Time - Darwin'), 
				'Antarctica/DumontDUrville'      => self::getTranslate('(GMT+10:00) Dumont D&#39;Urville'), 
				'Asia/Yakutsk'                   => self::getTranslate('(GMT+10:00) Moscow+06 - Yakutsk'), 
				'Australia/Brisbane'             => self::getTranslate('(GMT+10:00) Eastern Time - Brisbane'), 
				'Australia/Hobart'               => self::getTranslate('(GMT+10:00) Eastern Time - Hobart'), 
				'Australia/Sydney'               => self::getTranslate('(GMT+10:00) Eastern Time - Melbourne, Sydney'), 
				'Pacific/Chuuk'                  => self::getTranslate('(GMT+10:00) Truk'), 
				'Pacific/Guam'                   => self::getTranslate('(GMT+10:00) Guam'),
				'Pacific/Port_Moresby'           => self::getTranslate('(GMT+10:00) Port Moresby'), 
				'Pacific/Saipan'                 => self::getTranslate('(GMT+10:00) Saipan'),
				'Asia/Vladivostok'               => self::getTranslate('(GMT+11:00) Moscow+07 - Yuzhno-Sakhalinsk'),
				'Pacific/Efate'                  => self::getTranslate('(GMT+11:00) Efate'),
				'Pacific/Guadalcanal'            => self::getTranslate('(GMT+11:00) Guadalcanal'),
				'Pacific/Kosrae'                 => self::getTranslate('(GMT+11:00) Kosrae'),
				'Pacific/Noumea'                 => self::getTranslate('(GMT+11:00) Noumea'), 
				'Pacific/Pohnpei'                => self::getTranslate('(GMT+11:00) Ponape'), 
				'Pacific/Norfolk'                => self::getTranslate('(GMT+11:30) Norfolk'), 
				'Asia/Kamchatka'                 => self::getTranslate('(GMT+12:00) Moscow+08 - Petropavlovsk-Kamchatskiy'), 
				'Asia/Magadan'                   => self::getTranslate('(GMT+12:00) Moscow+08 - Magadan'), 
				'Pacific/Auckland'               => self::getTranslate('(GMT+12:00) Auckland'), 
				'Pacific/Fiji'                   => self::getTranslate('(GMT+12:00) Fiji'), 
				'Pacific/Funafuti'               => self::getTranslate('(GMT+12:00) Funafuti'), 
				'Pacific/Kwajalein'              => self::getTranslate('(GMT+12:00) Kwajalein'), 
				'Pacific/Majuro'                 => self::getTranslate('(GMT+12:00) Majuro'),
				'Pacific/Nauru'                  => self::getTranslate('(GMT+12:00) Nauru'), 
				'Pacific/Tarawa'                 => self::getTranslate('(GMT+12:00) Tarawa'), 
				'Pacific/Wake'                   => self::getTranslate('(GMT+12:00) Wake'),
				'Pacific/Wallis'                 => self::getTranslate('(GMT+12:00) Wallis'), 
				'Pacific/Apia'                   => self::getTranslate('(GMT+13:00) Apia'),
				'Pacific/Enderbury'              => self::getTranslate('(GMT+13:00) Enderbury'),
				'Pacific/Fakaofo'                => self::getTranslate('(GMT+13:00) Fakaofo'), 
				'Pacific/Tongatapu'              => self::getTranslate('(GMT+13:00) Tongatapu'), 
				'Pacific/Kiritimati'             => self::getTranslate('(GMT+14:00) Kiritimati'),
			);

			return $timezone;
		}
	}
}