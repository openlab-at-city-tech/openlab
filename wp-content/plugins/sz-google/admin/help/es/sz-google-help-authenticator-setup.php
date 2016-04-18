<?php

/**
 * This file contains information related to a help section 
 * of the plugin. Each directory is a specific language
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die(); 

// Variable definition HTML for the preparation of the
// string which contains the documentation of this feature

$HTML = <<<EOD

<h2>Descripción</h2>

<p>The <b>SZ-Google</b> plugin provides the authorization process in two phases designed by google authenticator, it is possible to 
strengthen the security of our login screen asking for a code-time in addition to the normal credentials. This is made ​​possible by 
the Google Authenticator that you can install on our smartphones whether it is an iphone, android or blackberry. As we will see below 
the configuration and synchronization of the key will be performed quickly and easily using a code QR Code.</p>

<h2>Configuration</h2>

<p>First you must activate the form of Google Authenticator from the admin panel that covers the plugin, once activated check that 
the configuration screen of the module is activated "<b>active login</b>". At this point in the user profile page will add the 
information to enable key authentication in time. So connect with your account and go to the page of your profile, activate the Google 
Authenticator, generated with the appropriate button a new "secret code" and check the code QR Code, once displayed, add a new 
account on your mobile application if this operation completes successfully updated your profile. The fact that we update your profile 
only after the configuration of the smartphone is dictated only by the fact that if  you update your profile, and before something 
goes wrong on the timing of your phone after you have login problems that must be solved by the administrator.</p>

<h2>Emergency file</h2>

<p>Enabling this option in the admin panel of the plugin you can disable the control of the time code running an FTP of a file in the 
root directory of wordpress. This feature can be useful if you have some big problems connecting or the administrator has more access 
to its devices. In this case the owner of the site could do an FTP to an empty file, for example <b>google-authenticator-disable.php</b>
in root directory and temporarily suspend code control, this is because he may not be able to login.</p>

<h2>Emergency codes</h2>

<p>We have already seen that it is possible to activate a file of emergency for forcing a login in case you can not
get the password at the time. In reality there is another way that allows us to associate a user profile of emergency codes that
can be used in place of the code in time. As the same mechanism that provides google for their own account.</p>

<p>The establishment of emergency codes you can run it from the menu of the user profile, in the same way as the code
secret. All codes to be generated may be used once each, in fact, the codes you find in the color red table are those already used 
and the others are those still free. In any case, you can create a new table of 12 new codes whenever you want.</p>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('authenticator setup','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));
