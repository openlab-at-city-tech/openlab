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

<p>Using this function in the <b>SZ-Google</b> plugin you can place button start hangout inside a wordpress post or in a sidebar.
This button is used to start a video session with google hangout and the ability to launch a specific application. This feature can be 
very useful to develop an application on hangout and invite users to use.</p>

<p>To add this module you have to use the shortcode <b>[sz-hangouts-start]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_hangouts_get_code_start(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit 
<a target="_blank" href="https://developers.google.com/+/hangouts/button?hl=it">Hangouts with the button</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th>    <th>Description</th>   <th>Allowed values</th>               <th>Default</th></tr>
	<tr><td>type</td>         <td>type</td>          <td>normal,onair,party,moderated</td> <td>normal</td></tr>
	<tr><td>topic</td>        <td>topic</td>         <td>string</td>                       <td>null</td></tr>
	<tr><td>width</td>        <td>width</td>         <td>value,auto</td>                   <td>auto</td></tr>
	<tr><td>float</td>        <td>float</td>         <td>left,right,none</td>              <td>none</td></tr>
	<tr><td>align</td>        <td>alignment</td>     <td>left,center,right,none</td>       <td>none</td></tr>
	<tr><td>text</td>         <td>text</td>          <td>string</td>                       <td>null</td></tr>
	<tr><td>img</td>          <td>image</td>         <td>string</td>                       <td>null</td></tr>
	<tr><td>position</td>     <td>position</td>      <td>top,center,bottom,outside</td>    <td>outside</td></tr>
	<tr><td>profile</td>      <td>call profile</td>  <td>id (separated by commas)</td>     <td>null</td></tr>
	<tr><td>email</td>        <td>call email</td>    <td>address (separated by commas)</td><td>null</td></tr>
	<tr><td>logged</td>       <td>user logged</td>   <td>y=yes,n=no</td>                   <td>configuration</td></tr>
	<tr><td>guest</td>        <td>user guest</td>    <td>y=yes,n=no</td>                   <td>configuration</td></tr>
	<tr><td>margintop</td>    <td>margin top</td>    <td>value,none</td>                   <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margin righ</td>   <td>value,none</td>                   <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margin bottom</td> <td>value,none</td>                   <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margin left</td>   <td>value,none</td>                   <td>none</td></tr>
	<tr><td>marginunit</td>   <td>margin unit</td>   <td>em,pt,px</td>                     <td>em</td></tr>
</table>

<h2>Button wrapper</h2>

<p>The behavior of the button of google is to draw the component and connect it to the permitted actions. The <b>SZ-Google</b>
plugin has improved this feature and added parameters to allow the drawing of a container on which the button can be placed. For 
example, we can specify an image and place the button within it and specifying the position.</p>

<pre>[sz-hangouts-start img="http://domain.com/image.jpg" position="bottom"/]</pre>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-hangouts-start type="normal"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'type'  => 'normal',
  'align' => 'center',
);

if (function_exists('szgoogle_hangouts_get_code_start')) {
  echo szgoogle_hangouts_get_code_start(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('hangout start button','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));