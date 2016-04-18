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

<p>Using this function in the <b>SZ-Google</b> plugin you can place the widget groups inside a wordpress post or in a sidebar. To get more 
information about google groups read official documentation on <a target="_blank" href="https://groups.google.com">https://groups.google.com</a>.</p>    

<p>To add this module you have to use the shortcode <b>[sz-ggroups]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_groups_get_code(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit 
<a target="_blank" href="https://support.google.com/groups/answer/1191206?hl=it">Insert a forum into a webpage</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th>      <th>Description</th>      <th>Allowed values</th> <th>Default</th></tr>
	<tr><td>name</td>           <td>group name</td>       <td>string</td>         <td>configuration</td></tr>
	<tr><td>domain</td>         <td>domain name APPs</td> <td>string</td>         <td>configuration</td></tr>
	<tr><td>width</td>          <td>width</td>            <td>value</td>          <td>configuration</td></tr>
	<tr><td>height</td>         <td>height</td>           <td>value</td>          <td>configuration</td></tr>
	<tr><td>showsearch</td>     <td>show search</td>      <td>true,false</td>     <td>configuration</td></tr>
	<tr><td>showtabs</td>       <td>show tabs</td>        <td>true,false</td>     <td>configuration</td></tr>
	<tr><td>hideforumtitle</td> <td>hide forum title</td> <td>true,false</td>     <td>configuration</td></tr>
	<tr><td>hidesubject</td>    <td>hide subject</td>     <td>true,false</td>     <td>configuration</td></tr>
	<tr><td>hl</td>             <td>language</td>         <td>language code</td>  <td>configuration</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-ggroups height="1200"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'name'    => 'comp.sys.ibm.as400.misc',
  'height'  => '1200',
  'showtabs'=> 'true',
);

if (function_exists('szgoogle_groups_get_code')) {
  echo szgoogle_groups_get_code(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google groups','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));