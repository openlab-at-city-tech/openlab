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

<h2>Description</h2>

<p>There may be cases where it is not possible to automatically fill in the fields and the control functions of the authenticator 
code automatically, for example when changes have been made to the current theme of heavy customizations. In this case, the 
developer can continue to use the plugin, it must implement PHP functions directly in your theme or plugin.</p>

<p>When using PHP functions available to the plugin always used first check to see if the function exists, in fact if the plugin 
<b>SZ-Google</b> proves disabled or uninstalled your theme or your plugin will not go wrong. Obviously, you should include in 
logical flow of the program the terms of these functions when they are called.</p>

<h2>PHP functions</h2>

<table>
	<tr><td>szgoogle_authenticator_get_object()</td><td>Object reference SZGoogleModuleAuthenticator.</td></tr>
	<tr><td>szgoogle_authenticator_get_secret(\$user)</td><td>Get secret code for user.</td></tr>
	<tr><td>szgoogle_authenticator_get_login_field()</td><td>Get HTML field to add to the login.</td></tr>
	<tr><td>szgoogle_authenticator_verify_code(\$user,\$code)</td><td>Verification code authenticator.</td></tr>
	<tr><td>szgoogle_authenticator_create_secret()</td><td>Creating a secret key.</td></tr>
	<tr><td>szgoogle_authenticator_create_secret_backup()</td><td>Creating a secret key backup.</td></tr>
</table>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
if (function_exists('szgoogle_authenticator_verify_code')) {
    \$check = szgoogle_authenticator_verify_code(\$user,'289597');
}
</pre>

<p>Below we see an example of how to enter the authenticator code field in a custom form, you can of course also use a name and 
a custom HTML output without using this function, it is important that the correct information is then passed to the PHP for 
code verification <b>szgoogle_authenticator_verify_code()</b>.</p>

<pre>
&lt;form id="login"&gt;
    &lt;input id="username" type="text"/&gt;
    &lt;input id="password" type="password"/&gt;
&lt;?php
    if (function_exists('szgoogle_authenticator_get_login_field')) {
        echo szgoogle_authenticator_get_login_field();
    }
?&gt;
&lt;/form&gt;
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('authenticator PHP','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));