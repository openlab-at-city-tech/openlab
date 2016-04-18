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

if (!class_exists('SZGoogleActionAuthenticatorProfile'))
{
	class SZGoogleActionAuthenticatorProfile extends SZGoogleAction
	{
		/**
		 * Add in the builder phase filters and 
		 * actions necessary to control login coded time
		 */

		function __construct() 
		{
			// Add to profile options that are seen only if user=current
			// will be inserted button to create a configuration synchronization

			add_action('profile_personal_options',array($this,'addAuthenticatorProfileField'));
			add_action('personal_options_update' ,array($this,'addAuthenticatorProfileFieldUpdate'));
			add_action('edit_user_profile'       ,array($this,'addAuthenticatorEditProfileField'));
			add_action('edit_user_profile_update',array($this,'addAuthenticatorEditProfileFieldUpdate'));

			// If I'm running an AJAX request I add an action to perform
			// the function of secret code generation via button and javascript

			if (defined('DOING_AJAX') && DOING_AJAX) {
				add_action('wp_ajax_SZGoogleAuthenticatorCodes' ,array($this,'getAuthenticatorCreateCodesAjax'));
				add_action('wp_ajax_SZGoogleAuthenticatorSecret',array($this,'getAuthenticatorCreateSecretAjax'));
			}

			// I add the javascript file for creating a code QR Code compared
			// to a containere <div> using the name for a hook load targeted

			add_action('admin_enqueue_scripts', array($this,'addAuthenticatorQRCodeScript'));
		}

		/**
		 * Feature to add fields to activate google authenticator
		 * user level, these options are visible from the current user
		 */

		function addAuthenticatorProfileField($user) 
		{
			// If administrator hides information from google authenticator
			// for this user profile does not run any updates database

			if (trim(get_user_option('sz_google_authenticator_hidden',$user->ID)) == '1') {
				return;
			}

			// Loading options for the configuration variables that
			// contain options that need to be turned on or off

			$options = (object) $this->getModuleOptions('SZGoogleModuleAuthenticator');

			// Reading user options to load the form values
			// After loading run some consistency checks on the data

			$sz_google_authenticator_codes       = trim(get_user_option('sz_google_authenticator_codes'      ,$user->ID));
			$sz_google_authenticator_enabled     = trim(get_user_option('sz_google_authenticator_enabled'    ,$user->ID));
			$sz_google_authenticator_description = trim(get_user_option('sz_google_authenticator_description',$user->ID));
			$sz_google_authenticator_secret      = trim(get_user_option('sz_google_authenticator_secret'     ,$user->ID));

			// Control defaults if nothing is specified, secret value is
			// regenerated while the description is taken from the configuration

			if ($sz_google_authenticator_secret == '') {
				$sz_google_authenticator_secret = $this->getAuthenticatorCreateSecret();
			}

			if ($sz_google_authenticator_description == '') {
				$sz_google_authenticator_description = ucfirst($user->display_name);
			}

			// Control codes emergency. If not specified at all or the value returned
			// does not contain an array call the function for a new generation

			if ($sz_google_authenticator_codes == '') {
				$sz_google_authenticator_codes = serialize($this->getAuthenticatorCreateCodes());
			}

			if (!is_array(unserialize($sz_google_authenticator_codes))) {
				$sz_google_authenticator_codes = serialize($this->getAuthenticatorCreateCodes());
			}

			// Deserialize the contents of the emergency codes to 
			// associate the table the initial values ​​and define those used

			$em = unserialize($sz_google_authenticator_codes);
			$ec = array_keys($em);

			// Creating HTML code for creating the profile section
			// Add option to enable authenticator of current user

			echo '<h3>'.ucwords(__( 'google authenticator','sz-google' )).'</h3>';
			echo '<table class="form-table">';
			echo '<tbody>';

			echo '<tr>';
			echo '<th scope="row">'.ucfirst(__('active','sz-google')).'</th>';
			echo '<td><input name="sz_google_authenticator_enabled" id="sz_google_authenticator_enabled" class="tog" type="checkbox" value="1" '.checked($sz_google_authenticator_enabled,'1',false ).'/>'; echo '</td>';
			echo '</tr>';

			// Add option to be used for the description of the application device
			// This value is used as a title by authenticator for smartphone

			echo '<tr>';
			echo '<th><label for="sz_google_authenticator_description">'.ucfirst(__('description','sz-google')).'</label></th>';
			echo '<td><input name="sz_google_authenticator_description" id="sz_google_authenticator_description" value="'.$sz_google_authenticator_description.'" type="text" size="25" class="sz-google-input"/></td>';
			echo '</tr>';

			// Add option for de secret code string to be used in synchronization
			// Near the field I enter the two buttons for the actions of code generation

			echo '<tr><th><label for="sz_google_authenticator_secret">'.ucfirst(__('secret','sz-google')).'</label></th><td>';
			echo '<input name="sz_google_authenticator_generate" id="sz_google_authenticator_generate" value="'.ucfirst(__('create new code','sz-google')).'" type="button" class="sz-google-input button"/><br/>';
			echo '<input name="sz_google_authenticator_shqrcode" id="sz_google_authenticator_shqrcode" value="'.__('SHOW/HIDE','sz-google').'" type="button" class="sz-google-input button" onclick="SZGoogleSwitchQRCode();"/>';
			echo '</td></tr>';

			// Add the session hidden for displaying the QR Code
			// Must press the appropriate buttons in to view

			echo '<tr id="sz_google_authenticator_wrap" style="display:none">';
			echo '<th>'.__('QR Code','sz-google').'</th><td>';
			echo '<input name="sz_google_authenticator_secret" id="sz_google_authenticator_secret" value="'.$sz_google_authenticator_secret.'" readonly="readonly" type="text" size="25" class="sz-google-input"/><br/><br/>';
			echo '<div id="sz_google_authenticator_qrcode"/></div>';
			echo '<span class="description"><br/> '.ucfirst(__( 'scan this QR Code with<br/> the Google Authenticator App.','sz-google')).'</span></td>';
			echo '</tr>';

			// Add option for the generation and display of the emergency codes
			// to be used instead of the password -time generated by the device

			if ($options->authenticator_emergency_codes == '1') {

				echo '<tr><th><label for="sz_google_authenticator_codes">'.ucfirst(__('emergency codes','sz-google')).'</label></th><td>';
				echo '<input name="sz_google_authenticator_codeg" id="sz_google_authenticator_codeg" value="'.ucfirst(__('create new codes','sz-google')).'" type="button" class="sz-google-input button"/><br/>';
				echo '<input name="sz_google_authenticator_coded" id="sz_google_authenticator_coded" value="'.__('SHOW/HIDE','sz-google').'" type="button" class="sz-google-input button" onclick="SZGoogleSwitchTableCode();"/>';
				echo '</td></tr>';

				// Control array of emergency codes if they contain false or the
				// date of use. Under the control imposed by the red color used

				if ($em[$ec[0]]  == false) $style01 = ''; else $style01 = ' style="color:red;"';
				if ($em[$ec[1]]  == false) $style02 = ''; else $style02 = ' style="color:red;"';
				if ($em[$ec[2]]  == false) $style03 = ''; else $style03 = ' style="color:red;"';
				if ($em[$ec[3]]  == false) $style04 = ''; else $style04 = ' style="color:red;"';
				if ($em[$ec[4]]  == false) $style05 = ''; else $style05 = ' style="color:red;"';
				if ($em[$ec[5]]  == false) $style06 = ''; else $style06 = ' style="color:red;"';
				if ($em[$ec[6]]  == false) $style07 = ''; else $style07 = ' style="color:red;"';
				if ($em[$ec[7]]  == false) $style08 = ''; else $style08 = ' style="color:red;"';
				if ($em[$ec[8]]  == false) $style09 = ''; else $style09 = ' style="color:red;"';
				if ($em[$ec[9]]  == false) $style10 = ''; else $style10 = ' style="color:red;"';
				if ($em[$ec[10]] == false) $style11 = ''; else $style11 = ' style="color:red;"';
				if ($em[$ec[11]] == false) $style12 = ''; else $style12 = ' style="color:red;"';

				// Add the table hidden from view under request containing all 
				// emergency codes generated manually or loaded from profile

				echo '<tr id="sz_google_authenticator_table" style="display:none">';
				echo '<th>'.ucfirst(__('table codes','sz-google')).'</th><td>';
				echo '<table class="sz-google-codes">';
				echo '<tr><td><div id="szga01"'.$style01.'>'.$ec[0].'</div></td><td><div id="szga02"'.$style02.'>'.$ec[1] .'</div></td><td><div id="szga03"'.$style03.'>'.$ec[2] .'</div></td></tr>';
				echo '<tr><td><div id="szga04"'.$style04.'>'.$ec[3].'</div></td><td><div id="szga05"'.$style05.'>'.$ec[4] .'</div></td><td><div id="szga06"'.$style06.'>'.$ec[5] .'</div></td></tr>';
				echo '<tr><td><div id="szga07"'.$style07.'>'.$ec[6].'</div></td><td><div id="szga08"'.$style08.'>'.$ec[7] .'</div></td><td><div id="szga09"'.$style09.'>'.$ec[8] .'</div></td></tr>';
				echo '<tr><td><div id="szga10"'.$style10.'>'.$ec[9].'</div></td><td><div id="szga11"'.$style11.'>'.$ec[10].'</div></td><td><div id="szga12"'.$style12.'>'.$ec[11].'</div></td></tr>';
				echo '</table>';
				echo '<div style="display:none"><input name="sz_google_authenticator_codes" id="sz_google_authenticator_codes" value="'.$sz_google_authenticator_codes.'" readonly="readonly" type="text" size="25" class="sz-google-input"/></div>';
				echo '</td></tr>';

			} else {

				echo '<tr id="sz_google_authenticator_table" style="display:none"><th></th>';
				echo '<td><div style="display:none"><input name="sz_google_authenticator_codes" id="sz_google_authenticator_codes" value="'.$sz_google_authenticator_codes.'" readonly="readonly" type="text" size="25" class="sz-google-input"/></div></td>';
				echo '</tr>';
			}	

			echo '</tbody></table>'."\n";

			// Start javascript code to perform actions on the buttons added
			// in the user profile that affect code generation and the QR Code

			echo '<script type="text/javascript">'."\n";
			echo "var SZGAction='SZGoogleAuthenticatorSecret';\n";
			echo "var SZHAction='SZGoogleAuthenticatorCodes';\n";
			echo "var SZGAnonce='".wp_create_nonce('SZGoogleAuthenticatorSecret')."';\n";
			echo "var SZHAnonce='".wp_create_nonce('SZGoogleAuthenticatorCodes')."';\n";

echo <<<ENDOFJS

				// Evento CLICK sulla generazione del codice segreto con QR Code
				// chiamata AJAX alla funzione Wordpress precedentemente definita

				jQuery('#sz_google_authenticator_generate').bind('click', function() {

					jQuery('#sz_google_authenticator_qrcode').html('');

					var data=new Object();

					data['action'] = SZGAction;
					data['nonce']  = SZGAnonce;

					jQuery.post(ajaxurl,data,function(response) {
						jQuery('#sz_google_authenticator_secret').val(response['secret']);
						var qrcode="otpauth://totp/WordPress:"+escape(jQuery('#sz_google_authenticator_description').val())+"?secret="+jQuery('#sz_google_authenticator_secret').val()+"&issuer=WordPress";
						jQuery('#sz_google_authenticator_qrcode').qrcode(qrcode);
						jQuery('#sz_google_authenticator_wrap').show('slow');
					});  	

				});

				// Funzione legata all'evento click del bottone legato alla
				// richiesta della generazione di un nuovo codici segreto

				function SZGoogleSwitchQRCode() {
					if (jQuery('#sz_google_authenticator_wrap').is(':hidden')) {
						var qrcode="otpauth://totp/WordPress:"+escape(jQuery('#sz_google_authenticator_description').val())+"?secret="+jQuery('#sz_google_authenticator_secret').val()+"&issuer=WordPress";
						jQuery('#sz_google_authenticator_qrcode').qrcode(qrcode);
						jQuery('#sz_google_authenticator_wrap').show('slow');
					} else {
						jQuery('#sz_google_authenticator_wrap').hide('slow');
						jQuery('#sz_google_authenticator_qrcode').html('');
					}
				}

				// Evento CLICK sulla generazione dei codici segreti di emergenza
				// chiamata AJAX alla funzione Wordpress precedentemente definita

				jQuery('#sz_google_authenticator_codeg').bind('click', function() {

					var prog = 0;
					var data = new Object();

					data['action'] = SZHAction;
					data['nonce']  = SZHAnonce;

					jQuery.post(ajaxurl,data,function(response) {
						for (i in response['codici']) { 
							prog = prog + 1; if (prog < 10) chars = '0' + prog; else chars = prog;
							jQuery('#szga'+chars).html(i);

						};
						jQuery('#sz_google_authenticator_codes').val(response['serial']);
						jQuery('#sz_google_authenticator_table').show('slow');
					});  	
				});

				// Funzione legata all'evento click del bottone legato alla
				// richiesta della generazione di nuovi codici di emergenza

				function SZGoogleSwitchTableCode() {
					if (jQuery('#sz_google_authenticator_table').is(':hidden')) {
						jQuery('#sz_google_authenticator_table').show('slow');
					} else {
						jQuery('#sz_google_authenticator_table').hide('slow');
					}
				}

			</script>
ENDOFJS;

		}

		/**
		 * Function to update the fields of google authenticator
		 * in wordpress database for the user record modified
		 */

		function addAuthenticatorProfileFieldUpdate($user) 
		{
			// If administrator hides information from google authenticator
			// for this user profile does not run any updates database

			if (trim(get_user_option('sz_google_authenticator_hidden',$user)) == '1') {
				return;
			}

			// If current user can change the profile run update
			// concerning the modified user profile options

			if (current_user_can('edit_user',$user))
			{
				if (!isset($_POST['sz_google_authenticator_codes']))   $_POST['sz_google_authenticator_codes']   = '';
				if (!isset($_POST['sz_google_authenticator_enabled'])) $_POST['sz_google_authenticator_enabled'] = '0';

				$sz_google_authenticator_codes	     = trim($_POST['sz_google_authenticator_codes']);
				$sz_google_authenticator_enabled	 = trim($_POST['sz_google_authenticator_enabled']);
				$sz_google_authenticator_secret	     = trim($_POST['sz_google_authenticator_secret']);

				$sz_google_authenticator_description = trim(sanitize_text_field($_POST['sz_google_authenticator_description']));

				update_user_option($user,'sz_google_authenticator_codes'      ,$sz_google_authenticator_codes      ,true);
				update_user_option($user,'sz_google_authenticator_enabled'    ,$sz_google_authenticator_enabled    ,true);
				update_user_option($user,'sz_google_authenticator_description',$sz_google_authenticator_description,true);
				update_user_option($user,'sz_google_authenticator_secret'     ,$sz_google_authenticator_secret     ,true);
			}
		}

		/**
		 * Feature to add fields to activate google authenticator user
		 * level, these options are visible by the user administrator
		 */

		function addAuthenticatorEditProfileField($user) 
		{
			$sz_google_authenticator_hidden  = trim(get_user_option('sz_google_authenticator_hidden' ,$user->ID));
			$sz_google_authenticator_enabled = trim(get_user_option('sz_google_authenticator_enabled',$user->ID));

			echo '<h3>'.ucfirst(__('Google Authenticator Settings','sz-google')).'</h3>';
			echo '<table class="form-table">';
			echo '<tbody>';
			echo '<tr>';
			echo '<th scope="row">'.ucfirst(__('hide settings from user','sz-google')).'</th>';
			echo '<td><div><input name="sz_google_authenticator_hidden" id="sz_google_authenticator_hidden" class="tog" type="checkbox" value="1" '.checked($sz_google_authenticator_hidden,'1',false).'/></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th scope="row">'.ucfirst(__('active','sz-google')).'</th>';
			echo '<td><input name="sz_google_authenticator_enabled" id="sz_google_authenticator_enabled" class="tog" type="checkbox" value="1" '.checked($sz_google_authenticator_enabled,'1',false ).'/>'; echo '</td>';
			echo '</tr>';
			echo '</tbody>';
			echo '</table>';
		}

		/**
		 * Function to update the fields of google authenticator
		 * in wordpress database for the user record modified
		 */

		function addAuthenticatorEditProfileFieldUpdate($user) 
		{
			if (current_user_can('edit_user',$user)) 
			{
				if (!isset($_POST['sz_google_authenticator_hidden']))  $_POST['sz_google_authenticator_hidden']  = '0';
				if (!isset($_POST['sz_google_authenticator_enabled'])) $_POST['sz_google_authenticator_enabled'] = '0';

				$sz_google_authenticator_hidden	 = trim($_POST['sz_google_authenticator_hidden']);
				$sz_google_authenticator_enabled = trim($_POST['sz_google_authenticator_enabled']);

				update_user_option($user,'sz_google_authenticator_hidden' ,$sz_google_authenticator_hidden ,true);
				update_user_option($user,'sz_google_authenticator_enabled',$sz_google_authenticator_enabled,true);
			}
		}

		/**
		 * Function for the creation of a secret key randomly
		 * using 16 characters and the native function of wordpress
		 */

		function getAuthenticatorCreateSecret($secretLength=16) 
		{
			$validChars = $this->_getBase32LookupTable();
			$secret = ''; unset($validChars[32]);

			for ($i = 0; $i < $secretLength; $i++) {
				$secret .= $validChars[array_rand($validChars)];
			}

			return $secret;
		}

		/**
		 * Function for the creation of a secret key randomly
		 * with an AJAX call to the regeneration request
		 */

		function getAuthenticatorCreateSecretAjax() 
		{
			check_ajax_referer('SZGoogleAuthenticatorSecret','nonce');

			header('Content-Type: application/json');
			echo json_encode(array('secret' => $this->getAuthenticatorCreateSecret()));

			die(); 
		}

		/**
		 * Function for the creation of emergency codes to be used in case
		 * of problems with the device that generates the password time
		 */

		function getAuthenticatorCreateCodes() 
		{
			$codes = array();

			// Creation of ten codes and emergency
			// storage array to be serialized

			while (count($codes) < 12) {
				$random = rand(100000,999999);
				$codes[$random] = false;
			}

			// Sorting array key that stores
			// codes for Emergency login

			ksort($codes);

			return $codes;
		}

		/**
		 * Function for the creation of a secret key randomly
		 * with an AJAX call to the regeneration request
		 */

		function getAuthenticatorCreateCodesAjax() 
		{
			check_ajax_referer('SZGoogleAuthenticatorCodes','nonce');

			$codici = $this->getAuthenticatorCreateCodes();

			header('Content-Type: application/json');
			echo json_encode(array('codici' => $codici,'serial' => serialize($codici)));

			die(); 
		}

		/**
		 * Add jquery library for creating a code QR Code
		 * The library is used for the button present on profile
		 */

		function addAuthenticatorQRCodeScript($hook)
		{
			if ($hook == 'profile.php' or $hook == 'user-edit.php') {
				wp_enqueue_script('jquery');
				wp_register_script('sz_google_qrcode_script',plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/js/jquery.qrcode.min.js',array('jquery'));
				wp_enqueue_script('sz_google_qrcode_script');
			}
		}

		/**
		 * Helper class to decode base32
		 * is used to decrypt the string that must be verified
		 */

		private function _base32Encode($secret,$padding=true)
		{
			if (empty($secret)) return '';

			$base32chars = $this->_getBase32LookupTable();

			$secret = str_split($secret);
			$binaryString = "";

			for ($i = 0; $i < count($secret); $i++) {
				$binaryString .= str_pad(base_convert(ord($secret[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
			}

			$fiveBitBinaryArray = str_split($binaryString, 5);
			$base32 = "";
			$i = 0;

			while ($i < count($fiveBitBinaryArray)) {
				$base32 .= $base32chars[base_convert(str_pad($fiveBitBinaryArray[$i], 5, '0'), 2, 10)];
				$i++;
			}

			if ($padding && ($x = strlen($binaryString) % 40) != 0) {
				if ($x == 8) $base32 .= str_repeat($base32chars[32], 6);
					elseif ($x == 16) $base32 .= str_repeat($base32chars[32], 4);
					elseif ($x == 24) $base32 .= str_repeat($base32chars[32], 3);
					elseif ($x == 32) $base32 .= $base32chars[32];
			}

			return $base32;
		}

		/**
		 * Table 32 characters with the set that is to be used
		 * during the encoding or decoding function in base32()
		 */

		private function _getBase32LookupTable()
		{
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
				'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
				'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
				'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
				'='  // padding char
			);
		}
	}
}