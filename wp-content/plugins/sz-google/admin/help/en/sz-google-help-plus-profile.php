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

<p>If you have an account on Google+ and want to add it on your website or integrate it with your badge author then this is
the right tool. The badge of the profile can be inserted in different ways and through different custom
options put at our disposal by the plugin itself. The badge is inserted through an iframe with all features defined by google.</p>

<p>To add this component you have to use the shortcode <b>[sz-gplus-profile]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_gplus_get_badge_profile(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit the official page 
<a target="_blank" href="https://developers.google.com/+/web/badge/?hl=it">Google+ Badge</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th> <th>Description</th>        <th>Allowed values</th>         <th>Default</th></tr>
	<tr><td>id</td>        <td>profile</td>            <td>string</td>                 <td>configuration</td></tr>
	<tr><td>type</td>      <td>mode</td>               <td>standard,popup</td>         <td>standard</td></tr>
	<tr><td>width</td>     <td>width</td>              <td>value,auto</td>             <td>configuration</td></tr>
	<tr><td>align</td>     <td>alignment</td>          <td>left,center,right,none</td> <td>none</td></tr>
	<tr><td>layout</td>    <td>layout</td>             <td>portrait,landscape</td>     <td>portrait</td></tr>
	<tr><td>theme</td>     <td>theme</td>              <td>light,dark</td>             <td>light</td></tr>
	<tr><td>cover</td>     <td>cover</td>              <td>true,false</td>             <td>true</td></tr>
	<tr><td>tagline</td>   <td>tagline</td>            <td>true,false</td>             <td>true</td></tr>
	<tr><td>author</td>    <td>rel=author in HTML</td> <td>true,false</td>             <td>false</td></tr>
	<tr><td>text</td>      <td>pop-up text</td>        <td>string</td>                 <td>null</td></tr>
	<tr><td>image</td>     <td>pop-up URL image</td>   <td>string</td>                 <td>null</td></tr>
</table>

<h2>Standard or popup</h2>

<p>As you can see from the table of options is called a <b>type</b> parameter with which you can choose to display the badge in a 
standard way and then immediately draw the badge in the HTML page or request a viewing mode only popup by passing the cursor over a 
<b>text</b> or <b>image</b>. In this case you have to specify the parameters for function pop-up text and image.</p>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-gplus-profile id="106189723444098348646" type="standard" width="auto"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'id'     => '106189723444098348646',
  'type'   => 'standard',
  'width'  => 'auto',
  'theme'  => 'dark',
  'layout' => 'portrait'
);

if (function_exists('szgoogle_gplus_get_badge_profile')) {
  echo szgoogle_gplus_get_badge_profile(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge profile','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));