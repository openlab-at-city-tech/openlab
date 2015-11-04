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

<p>Through this feature you can place a text link or a single image with a direct link to the youtube channel . The advantage of using this link 
is to be able to indicate the subscription automatic once the user reaches the channel page.</p>

<p>To add this module you have to use the shortcode <b>[sz-ytlink]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_youtube_get_code_link(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify option = "value".</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>LINK</th>         <th>Description</th>        <th>Allowed values</th> <th>Default</th></tr>
	<tr><td>channel</td>      <td>channel name or ID</td> <td>string</td>         <td>configuration</td></tr>
	<tr><td>subscription</td> <td>subscription</td>       <td>y=yes,n=no</td>     <td>y=yes</td></tr>
	<tr><td>text</td>         <td>link text</td>          <td>string</td>         <td>configuration</td></tr>
	<tr><td>image</td>        <td>link image</td>         <td>string URL</td>     <td>none</td></tr>
	<tr><td>newtab</td>       <td>open link</td>          <td>y=yes,n=no</td>     <td>n=no</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-ytlink text="iscriviti al mio canale youtube"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'channel'      => 'cloudaws',
  'subscription' => 'yes',
  'text'         => 'iscriviti al mio canale youtube',
);

if (function_exists('szgoogle_youtube_get_code_link')) {
  echo szgoogle_youtube_get_code_link(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('youtube link','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));