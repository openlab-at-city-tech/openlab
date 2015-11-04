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

<p>This module of the <b>SZ-Google</b> plugin allows you to enter in our articles a photo gallery present in Panoramio, you must 
specify the desired template and the required options. You can use four different templates, photo, list, slideshow and photo_list.
For more information about the parameters read the official page <a href="http://www.panoramio.com/api/widget/api.html">Panoramio Widget API</a>.</p>

<p>To add this module you have to use the shortcode <b>[sz-panoramio]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_panoramio_get_code(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit 
<a target="_blank" href="http://www.panoramio.com/api/widget/api.html">Panoramio Widget API </a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th>   <th>Description</th>      <th>Allowed values</th>                  <th>Default</th></tr>
	<tr><td>template</td>    <td>widget type</td>      <td>photo,slideshow,list,photo_list</td> <td>photo</td></tr>
	<tr><td>user</td>        <td>search by user</td>   <td>string</td>                          <td>null</td></tr>
	<tr><td>group</td>       <td>search by group</td>  <td>string</td>                          <td>null</td></tr>
	<tr><td>tag</td>         <td>search by tag</td>    <td>string</td>                          <td>null</td></tr>
	<tr><td>set</td>         <td>select set</td>       <td>all,public,recent</td>               <td>all</td></tr>
	<tr><td>widht</td>       <td>widget widht</td>     <td>value</td>                           <td>auto</td></tr>
	<tr><td>height</td>      <td>widget height</td>    <td>value</td>                          <td>300</td></tr>
	<tr><td>bgcolor</td>     <td>background color</td> <td>hexadecimal</td>                     <td>null</td></tr>
	<tr><td>columns</td>     <td>columns</td>          <td>value</td>                           <td>4</td></tr>
	<tr><td>rows</td>        <td>rows</td>             <td>value</td>                           <td>1</td></tr>
	<tr><td>orientation</td> <td>orientation</td>      <td>horizontal,vertical</td>             <td>horizontal</td></tr>
	<tr><td>list_size</td>   <td>list size</td>        <td>numeric</td>                         <td>6</td></tr>
	<tr><td>position</td>    <td>position</td>         <td>left,top,right,bottom</td>           <td>bottom</td></tr>
	<tr><td>delay</td>       <td>delay seconds</td>    <td>value</td>                           <td>2</td></tr>
	<tr><td>paragraph</td>   <td>dummy paragraph</td>  <td>true,false</td>                      <td>true</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-panoramio template="list" columns="6" rows="3" height="300" bgcolor="#e1e1e1"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'template' => 'list',
  'columns'  => '6',
  'rows'     => '3',
  'height'   => '300',
  'bgcolor'  => '#e1e1e1',
);

if (function_exists('szgoogle_panoramio_get_code')) {
  echo szgoogle_panoramio_get_code(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('widget panoramio','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));