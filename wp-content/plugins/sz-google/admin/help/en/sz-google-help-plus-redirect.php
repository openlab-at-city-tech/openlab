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

<p>The format of the URL used by Google to identify its pages is definitely not a friendly url, it uses the numeric id of the very long 
URL string that make it impossible to remember or store. For this reason, G+ has made available for profiles and pages a custom URL to 
associate with your profile or page. Unfortunately, however, the system adopted is not always effective, in fact, especially in the pages 
are requested of additional characters that many web sites do not appreciate why not consistent with its original name. For example, 
a company called <b>skydrive</b> not agrees to print an address as <b>https://plus.google.com/+skydrive9876</b>.</p>

<h2>Domain redirect</h2>

<p>The plugin <b>SZ-Google</b> provides a feature to redirect your domain name, for example if the plugin is installed on the site 
that we took as an example <b>skydrive.com</b> you can create a custom URL as <b>https://skydrive.com/+</b> that will take you 
directly to google+ page, certainly more elegant that can be used without problems on various advertising materials or gadgets.</p>

<pre>
Google+ URL ==> https://plus.google.com/123456789012345
Google+ URL ==> https://plus.google.com/+skydrive9876
Plugin+ URL ==> https://skydrive.com/+
</pre>

<p>In the configuration section Google+ redirect present in the admin panel there is also the possibility of identifying a redirect 
to the string URL <b>/plus</b> and one of your choice. For example if you have a community attached to your page you could use the 
string URL <b>/community+</b> to redirect a direct bearing on google+ community page.</p>

<pre>
Plugin+ URL ==> https://skydrive.com/+
Plugin+ URL ==> https://skydrive.com/plus
Plugin+ URL ==> https://skydrive.com/community/+
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ redirect','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));