<?php

if (!class_exists('anr_admin_class'))
{
  class anr_admin_class
  {
	private static $instance;
	
	public static function init()
		{
			if(!self::$instance instanceof self) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
	function actions_filters()
	{
		if ( is_multisite() ) {
			$same_settings = apply_filters( 'anr_same_settings_for_all_sites', false );
		} else {
			$same_settings = false;
		}
		if ( $same_settings ) {
			add_action('network_admin_menu', array($this, 'MenuPage'));
		} else {
			add_action('admin_menu', array($this, 'MenuPage'));
		}
		
	add_filter('plugin_action_links', array($this, 'add_settings_link'), 10, 2 );
	}



/******************************************ADMIN SETTINGS PAGE BEGIN******************************************/

	function MenuPage()
	{
	//add_menu_page('Advanced noCaptcha reCaptcha', 'Advanced noCaptcha', 'manage_options', 'anr-admin-settings', array($this, 'admin_settings'),plugins_url( 'advanced-nocaptcha-recaptcha/images/advanced-nocaptcha-recaptcha.jpg' ));	
	
	//add_submenu_page('anr-admin-settings', 'Advanced noCaptcha reCaptcha - ' .__('Settings','advanced-nocaptcha-recaptcha'), __('Settings','advanced-nocaptcha-recaptcha'), 'manage_options', 'anr-admin-settings', array($this, 'admin_settings'));
	
	//add_submenu_page('anr-admin-settings', 'Advanced noCaptcha reCaptcha - ' .__('Instruction','fepcf'), __('Instruction','fepcf'), 'manage_options', 'anr-instruction', array($this, "InstructionPage"));
	
	add_options_page( __('Advanced noCaptcha & invisible captcha Settings','advanced-nocaptcha-recaptcha'), __('Advanced noCaptcha & invisible captcha','advanced-nocaptcha-recaptcha'), 'manage_options', 'anr-admin-settings', array($this, 'admin_settings') );
	
	}
	

	function admin_settings()
	{
	  $token = wp_create_nonce( 'anr-admin-settings' );
	  $url = 'https://www.shamimsplugins.com/contact-us/';
	  $ReviewURL = 'https://wordpress.org/support/plugin/advanced-nocaptcha-recaptcha/reviews/?filter=5#new-post';
	  echo "<style>
			input[type='text'], textarea, select {
				width: 100%;
			}
		</style>";
	  $languages = array(
							__( 'Auto Detect', 'advanced-nocaptcha-recaptcha' )         	=> '',
							__( 'Arabic', 'advanced-nocaptcha-recaptcha' )              	=> 'ar',
							__( 'Bulgarian', 'advanced-nocaptcha-recaptcha' )           	=> 'bg',
							__( 'Catalan', 'advanced-nocaptcha-recaptcha' )             	=> 'ca',
							__( 'Chinese (Simplified)', 'advanced-nocaptcha-recaptcha' )	=> 'zh-CN',
							__( 'Chinese (Traditional)', 'advanced-nocaptcha-recaptcha' ) => 'zh-TW',
							__( 'Croatian', 'advanced-nocaptcha-recaptcha' )           	=> 'hr',
							__( 'Czech', 'advanced-nocaptcha-recaptcha' )             	=> 'cs',
							__( 'Danish', 'advanced-nocaptcha-recaptcha' )             	=> 'da',
							__( 'Dutch', 'advanced-nocaptcha-recaptcha' )              	=> 'nl',
							__( 'English (UK)', 'advanced-nocaptcha-recaptcha' )         => 'en-GB',
							__( 'English (US)', 'advanced-nocaptcha-recaptcha' )         => 'en',
							__( 'Filipino', 'advanced-nocaptcha-recaptcha' )				=> 'fil',
							__( 'Finnish', 'advanced-nocaptcha-recaptcha' ) 				=> 'fi',
							__( 'French', 'advanced-nocaptcha-recaptcha' )           	=> 'fr',
							__( 'French (Canadian)', 'advanced-nocaptcha-recaptcha' )   	=> 'fr-CA',
							__( 'German', 'advanced-nocaptcha-recaptcha' )   			=> 'de',
							__( 'German (Austria)', 'advanced-nocaptcha-recaptcha' )		=> 'de-AT',
							__( 'German (Switzerland)', 'advanced-nocaptcha-recaptcha' ) => 'de-CH',
							__( 'Greek', 'advanced-nocaptcha-recaptcha' )           		=> 'el',
							__( 'Hebrew', 'advanced-nocaptcha-recaptcha' )             	=> 'iw',
							__( 'Hindi', 'advanced-nocaptcha-recaptcha' )             	=> 'hi',
							__( 'Hungarain', 'advanced-nocaptcha-recaptcha' )            => 'hu',
							__( 'Indonesian', 'advanced-nocaptcha-recaptcha' )         	=> 'id',
							__( 'Italian', 'advanced-nocaptcha-recaptcha' )         		=> 'it',
							__( 'Japanese', 'advanced-nocaptcha-recaptcha' )				=> 'ja',
							__( 'Korean', 'advanced-nocaptcha-recaptcha' ) 				=> 'ko',
							__( 'Latvian', 'advanced-nocaptcha-recaptcha' )           	=> 'lv',
							__( 'Lithuanian', 'advanced-nocaptcha-recaptcha' )   		=> 'lt',
							__( 'Norwegian', 'advanced-nocaptcha-recaptcha' )   			=> 'no',
							__( 'Persian', 'advanced-nocaptcha-recaptcha' )           	=> 'fa',
							__( 'Polish', 'advanced-nocaptcha-recaptcha' )   			=> 'pl',
							__( 'Portuguese', 'advanced-nocaptcha-recaptcha' )   		=> 'pt',
							__( 'Portuguese (Brazil)', 'advanced-nocaptcha-recaptcha' )  => 'pt-BR',
							__( 'Portuguese (Portugal)', 'advanced-nocaptcha-recaptcha' )=> 'pt-PT',
							__( 'Romanian', 'advanced-nocaptcha-recaptcha' )         	=> 'ro',
							__( 'Russian', 'advanced-nocaptcha-recaptcha' )         		=> 'ru',
							__( 'Serbian', 'advanced-nocaptcha-recaptcha' )				=> 'sr',
							__( 'Slovak', 'advanced-nocaptcha-recaptcha' ) 				=> 'sk',
							__( 'Slovenian', 'advanced-nocaptcha-recaptcha' )           	=> 'sl',
							__( 'Spanish', 'advanced-nocaptcha-recaptcha' )   			=> 'es',
							__( 'Spanish (Latin America)', 'advanced-nocaptcha-recaptcha' )=> 'es-419',
							__( 'Swedish', 'advanced-nocaptcha-recaptcha' )           	=> 'sv',
							__( 'Thai', 'advanced-nocaptcha-recaptcha' )   				=> 'th',
							__( 'Turkish', 'advanced-nocaptcha-recaptcha' )   			=> 'tr',
							__( 'Ukrainian', 'advanced-nocaptcha-recaptcha' )   			=> 'uk',
							__( 'Vietnamese', 'advanced-nocaptcha-recaptcha' )   		=> 'vi'
							
							);
							
		$locations = array(	 
							__( 'Login Form', 'advanced-nocaptcha-recaptcha' )   				=> 'login',
							__( 'Registration Form', 'advanced-nocaptcha-recaptcha' )   			=> 'registration',
							__( 'Multisite User Signup Form', 'advanced-nocaptcha-recaptcha' )   => 'ms_user_signup',
							__( 'Lost Password Form', 'advanced-nocaptcha-recaptcha' )   		=> 'lost_password',
							__( 'Reset Password Form', 'advanced-nocaptcha-recaptcha' )  		=> 'reset_password',
							__( 'Comment Form', 'advanced-nocaptcha-recaptcha' )   				=> 'comment',
							__( 'bbPress New topic', 'advanced-nocaptcha-recaptcha' )   			=> 'bb_new',
							__( 'bbPress reply to topic', 'advanced-nocaptcha-recaptcha' )		=> 'bb_reply',
							__( 'WooCommerce Checkout', 'advanced-nocaptcha-recaptcha' )		=> 'wc_checkout',
									
							);
									
	  
	  if(isset($_POST['anr-admin-settings-submit'])){ 
			$errors = $this->admin_settings_action();
			if(count($errors->get_error_messages())>0){
				echo'<div id="message" class="error fade"><p>' . implode( '<br />', $errors->get_error_messages() ). '</p></div>';
			} else {
				echo'<div id="message" class="updated fade"><p>' .__("Options successfully saved.", 'advanced-nocaptcha-recaptcha'). '</p></div>';
			}
		}
		echo "<div id='poststuff'>

		<div id='post-body' class='metabox-holder columns-2'>

		<!-- main content -->
		<div id='post-body-content'>
		<div class='postbox'><div class='inside'>
		  <h2>".__("Advanced noCaptcha reCaptcha Settings", 'advanced-nocaptcha-recaptcha')."</h2>
		  <h5>".sprintf(__("If you like this plugin please <a href='%s' target='_blank'>Review in Wordpress.org</a> and give 5 star", 'advanced-nocaptcha-recaptcha'),esc_url($ReviewURL))."</h5>
		  <form method='post' action=''>
		  <table>
		  <thead>
		  <tr><th width = '50%'>".__("Setting", 'advanced-nocaptcha-recaptcha')."</th><th width = '50%'>".__("Value", 'advanced-nocaptcha-recaptcha')."</th></tr>
		  </thead>
		  <tr><td>".__("Site Key", 'advanced-nocaptcha-recaptcha')."<br/><small><a href='https://www.google.com/recaptcha/admin' target='_blank'>Get From Google</a></small></td><td><input type='text' size = '40' name='site_key' value='".esc_attr( anr_get_option('site_key') )."' /></td></tr>
		  <tr><td>".__("Secret key", 'advanced-nocaptcha-recaptcha')."<br/><small><a href='https://www.google.com/recaptcha/admin' target='_blank'>Get From Google</a></small></td><td><input type='text' size = '40' name='secret_key' value='".esc_attr( anr_get_option('secret_key') )."' /></td></tr>
		  
		  <tr><td>".__("Language", 'advanced-nocaptcha-recaptcha')."</td><td><select name='language'>";
		  
		  foreach ( $languages as $language => $code ) {
		  
		  echo "<option value='". esc_attr( $code ) ."' ".selected(anr_get_option('language'), $code,false).">".esc_html( $language )."</option>";
		  
		  }
		  
		  echo "</select></td></tr>
		  <tr><td>".__("Theme", 'advanced-nocaptcha-recaptcha')."</td><td><select name='theme'>
		  
		  <option value='light' ".selected(anr_get_option('theme'), 'light',false).">".__("Light", 'advanced-nocaptcha-recaptcha')."</option>
		  <option value='dark' ".selected(anr_get_option('theme'), 'dark',false).">".__("Dark", 'advanced-nocaptcha-recaptcha')."</option>
		  
		  </select></td></tr>
		  <tr><td>".__("Size", 'advanced-nocaptcha-recaptcha')."</td><td><select name='size'>
		  
		  <option value='normal' ".selected(anr_get_option('size'), 'normal',false).">".__("Normal", 'advanced-nocaptcha-recaptcha')."</option>
		  <option value='compact' ".selected(anr_get_option('size'), 'compact',false).">".__("Compact", 'advanced-nocaptcha-recaptcha')."</option>
		  <option value='invisible' ".selected(anr_get_option('size'), 'invisible',false).">".__("Invisible", 'advanced-nocaptcha-recaptcha')."</option>
		  
		  </select>
		  <div class='description'>".__("For invisible captcha set this as Invisible. Make sure to use site key and secret key for invisible captcha", 'advanced-nocaptcha-recaptcha')."</div>
		  </td></tr>
		  
		  <tr><td>".__("Badge", 'advanced-nocaptcha-recaptcha')."</td><td><select name='badge'>
		  
		  <option value='bottomright' ".selected(anr_get_option('badge'), 'bottomright',false).">".__("Bottom Right", 'advanced-nocaptcha-recaptcha')."</option>
		  <option value='bottomleft' ".selected(anr_get_option('badge'), 'bottomleft',false).">".__("Bottom Left", 'advanced-nocaptcha-recaptcha')."</option>
		  <option value='inline' ".selected(anr_get_option('badge'), 'inline',false).">".__("Inline", 'advanced-nocaptcha-recaptcha')."</option>
		  
		  </select>
		  <div class='description'>".__("Badge shows for invisible captcha", 'advanced-nocaptcha-recaptcha')."</div>
		  </td></tr>
		  
		  <tr><td>".__("Error Message", 'advanced-nocaptcha-recaptcha')."</td><td><input type='text' size = '40' name='error_message' value='".wp_kses_post( anr_get_option('error_message', '<strong>ERROR</strong>: Please solve Captcha correctly.') )."' /></td></tr>
		  <tr><td>".__("Show login Captcha after how many failed attempts", 'advanced-nocaptcha-recaptcha')."</td><td><input type='number' size = '40' name='failed_login_allow' value='".absint(anr_get_option('failed_login_allow', 0 ))."' /></td></tr>
		  
		  <tr><td>".__("Show Captcha on", 'advanced-nocaptcha-recaptcha')."</td><td>";
		  
		  foreach ( $locations as $location => $slug ) {
		  
		  echo "<ul colspan='2'><label><input type='checkbox' name='" . esc_attr( $slug ) . "' value='1' ".checked(anr_get_option($slug), '1', false)." /> ". esc_html( $location ) ."</label></ul>";
		  
		  }
		  /**
		  if ( function_exists('fepcf_plugin_activate'))
		  echo "<ul colspan='2'><label><input type='checkbox' name='fep_contact_form' value='1' ".checked(anr_get_option('fep_contact_form'), '1', false)." /> FEP Contact Form</label></ul>";
		  else
		  echo "<ul colspan='2'><label><input type='checkbox' name='fep_contact_form' disabled value='1' ".checked(anr_get_option('fep_contact_form'), '1', false)." /> FEP Contact Form (is not installed) <a href='https://wordpress.org/plugins/fep-contact-form/' target='_blank'>Install Now</a></label></ul>";
		  */
		  
		  //echo "<ul colspan='2'> For other forms see <a href='".esc_url(admin_url( 'admin.php?page=anr-instruction' ))."'>Instruction</a></ul>";
		  echo "</td></tr>";
		  
		  do_action('anr_admin_setting_form');
		  
		  echo "<tr><td colspan='2'><label><input type='checkbox' name='loggedin_hide' value='1' ".checked(anr_get_option('loggedin_hide'), '1', false)." /> ".__("Hide Captcha for logged in users?", 'advanced-nocaptcha-recaptcha')."</label></td></tr>
		  <tr><td colspan='2'><label><input type='checkbox' name='remove_css' value='1' ".checked(anr_get_option('remove_css'), '1', false)." /> ".__("Remove this plugin's css from login page?", 'advanced-nocaptcha-recaptcha')."<br/><small>".__("This css increase login page width to adjust with Captcha width.", 'advanced-nocaptcha-recaptcha')."</small></label></td></tr>
		  <tr><td colspan='2'><label><input type='checkbox' name='no_js' value='1' ".checked(anr_get_option('no_js'), '1', false)." /> ".__("Show captcha if javascript disabled?", 'advanced-nocaptcha-recaptcha')."<br/><small>".__("If JavaScript is a requirement for your site, we advise that you do NOT check this.", 'advanced-nocaptcha-recaptcha')."</small></label></td></tr>
		  <tr><td colspan='2'><span><input class='button-primary' type='submit' name='anr-admin-settings-submit' value='".__("Save Options", 'advanced-nocaptcha-recaptcha')."' /></span></td><td><input type='hidden' name='token' value='$token' /></td></tr>
		  </table>
		  </form>
		  <ul>".sprintf(__("For paid support pleasse visit <a href='%s' target='_blank'>Advanced noCaptcha reCaptcha</a>", 'advanced-nocaptcha-recaptcha'),esc_url($url))."</ul>
		  </div></div></div>
		  ". $this->anr_admin_sidebar(). "
		  </div></div>";
		  }

function anr_admin_sidebar()
	{
		return '<div id="postbox-container-1" class="postbox-container">


				<div class="postbox">
					<h3 class="hndle" style="text-align: center;">
						<span>'. __( "Plugin Author", "anr" ). '</span>
					</h3>

					<div class="inside">
						<div style="text-align: center; margin: auto">
						<strong>Shamim Hasan</strong><br />
						Know php, MySql, css, javascript, html. Expert in WordPress. <br /><br />
								
						You can hire for plugin customization, build custom plugin or any kind of wordpress job via <br> <a
								href="https://www.shamimsplugins.com/contact-us/"><strong>Contact Form</strong></a>
					</div>
				</div>
			</div>
				</div>';
	}
		

	function admin_settings_action()
	{
		if (isset($_POST['anr-admin-settings-submit']))
		{
			$errors = new WP_Error();
			$options = $_POST;

			if( !current_user_can('manage_options'))
			$errors->add('noPermission', __('No Permission!', 'advanced-nocaptcha-recaptcha'));


			if ( !wp_verify_nonce($options['token'], 'anr-admin-settings'))
			$errors->add('invalidToken', __('Sorry, your nonce did not verify!', 'advanced-nocaptcha-recaptcha'));
			
			unset( $options['token'], $options['anr-admin-settings-submit'] );

			$options['site_key'] = isset( $options['site_key'] ) ? sanitize_text_field( $options['site_key'] ) : '';
			$options['secret_key'] = isset( $options['secret_key'] ) ? sanitize_text_field( $options['secret_key'] ) : '';
			$options['error_message'] = isset( $options['error_message'] ) ? wp_kses_post( $options['error_message'] ) : '';
			
			$options = apply_filters('anr_filter_admin_setting_before_save', $options, $errors);
			//var_dump($options);

			if ( count( $errors->get_error_codes() ) == 0 ){
				if ( is_multisite() && apply_filters( 'anr_same_settings_for_all_sites', false ) ){
					update_site_option( 'anr_admin_options', $options );
				} else {
					update_option( 'anr_admin_options', $options );
				}
			}
			return $errors;
		}
		return false;
	}
	
	function InstructionPage()
	{
	$url = 'https://www.shamimsplugins.com/contact-us/';
	echo '<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

		<!-- main content -->
		<div id="post-body-content">';
		
	  echo 	"<div class='postbox'><div class='inside'>
		  <h2>".__("Advanced noCaptcha reCaptcha Setup Instruction", 'advanced-nocaptcha-recaptcha')."</h2>
		  <p><ul>
		  <li>".sprintf(__("Get your site key and secret key from <a href='%s' target='_blank'>GOOGLE</a> if you do not have already.", 'advanced-nocaptcha-recaptcha'),esc_url('https://www.google.com/recaptcha/admin'))."</li>
		  <li>".__("Goto SETTINGS page of this plugin and set up as you need. and ENJOY...", 'advanced-nocaptcha-recaptcha')."</li><br/>
		  
		  <h3>".__("Implement noCaptcha in Contact Form 7", 'advanced-nocaptcha-recaptcha')."</h3><br />
		  <li>".__("To show noCaptcha use ", 'advanced-nocaptcha-recaptcha')."<code>[anr_nocaptcha g-recaptcha-response]</code></li><br />
		  
		  <h3>".__("Implement noCaptcha in WooCommerce", 'advanced-nocaptcha-recaptcha')."</h3><br />
		  <li>".__("If Login Form, Registration Form, Lost Password Form, Reset Password Form is selected in SETTINGS page of this plugin they will show and verify Captcha in WooCommerce respective forms also.", 'advanced-nocaptcha-recaptcha')."</li><br />
		  
		  <h3>".__("If you want to implement noCaptcha in any other custom form", 'advanced-nocaptcha-recaptcha')."</h3><br />
		  <li>".__("To show form field use ", 'advanced-nocaptcha-recaptcha')."<code>do_action( 'anr_captcha_form_field' )</code></li>
		  <li>".__("To verify use ", 'advanced-nocaptcha-recaptcha')."<code>anr_verify_captcha()</code> it will return true on success otherwise false</li><br />
		  <li>".sprintf(__("For paid support pleasse visit <a href='%s' target='_blank'>Advanced noCaptcha reCaptcha</a>", 'advanced-nocaptcha-recaptcha'),esc_url($url))."</li>
		  </ul></p></div></div></div>
		  ". $this->anr_admin_sidebar(). "
		  </div></div>";
		  }
	
	
function add_settings_link( $links, $file ) {
	//add settings link in plugins page
	$plugin_file = 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php';
	if ( $file == $plugin_file ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=anr-admin-settings' ) . '">' .__( 'Settings', 'advanced-nocaptcha-recaptcha' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
/******************************************ADMIN SETTINGS PAGE END******************************************/


  } //END CLASS
} //ENDIF

add_action('wp_loaded', array(anr_admin_class::init(), 'actions_filters'));
