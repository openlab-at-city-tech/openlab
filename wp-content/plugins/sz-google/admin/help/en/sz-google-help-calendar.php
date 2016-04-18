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

<p>With this function you can put in on your site embed the google calendar. You can also specify different calendars, 
just specify the parameter <b>"calendar"</b> a string with the calendar names separated by a comma. If you do not specify a calendar 
that will be used is stored in the general configuration.</p>

<p>To add this module you have to use the shortcode <b>[sz-calendar]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_calendar_get_widget(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information 
<a target="_blank" href="https://www.google.com/calendar/embedhelper">Google Embeddable Calendar Helper</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th>     <th>Description</th>       <th>Allowed values</th>    <th>Default</th></tr>
	<tr><td>calendar</td>      <td>calendar</td>          <td>string</td>            <td>configuration</td></tr>
	<tr><td>title</td>         <td>title</td>             <td>string</td>            <td>configuration</td></tr>
	<tr><td>mode</td>          <td>agenda mode</td>       <td>AGENDA,WEEK,MONTH</td> <td>configuration</td></tr>
	<tr><td>weekstart</td>     <td>day start of week</td> <td>1,2,7</td>             <td>configuration</td></tr>
	<tr><td>language</td>      <td>language</td>          <td>string</td>            <td>configuration</td></tr>
	<tr><td>timezone</td>      <td>timezone</td>          <td>string</td>            <td>configuration</td></tr>
	<tr><td>width</td>         <td>width</td>             <td>value,auto</td>        <td>configuration</td></tr>
	<tr><td>height</td>        <td>height</td>            <td>value</td>             <td>configuration</td></tr>
	<tr><td>showtitle</td>     <td>display title</td>     <td>yes,no</td>            <td>configuration</td></tr>
	<tr><td>shownavs</td>      <td>display navigator</td> <td>yes,no</td>            <td>configuration</td></tr>
	<tr><td>showdate</td>      <td>display date</td>      <td>yes,no</td>            <td>configuration</td></tr>
	<tr><td>showprint</td>     <td>display print</td>     <td>yes,no</td>            <td>configuration</td></tr>
	<tr><td>showcalendars</td> <td>display calendar</td>  <td>yes,no</td>            <td>configuration</td></tr>
	<tr><td>showtimezone</td>  <td>display timezone</td>  <td>yes,no</td>            <td>configuration</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-calendar showprint="no"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'calendar'  => 'gt0ejukbb55l7xxcl4qi1j62ng@group.calendar.google.com',
  'title'     => 'My Calendar',
  'mode'      => 'AGENDA',
  'showtitle' => 'no',
  'showdate'  => 'no'
);

if (function_exists('szgoogle_calendar_get_widget')) {
  echo szgoogle_calendar_get_widget(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('widget calendar','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));