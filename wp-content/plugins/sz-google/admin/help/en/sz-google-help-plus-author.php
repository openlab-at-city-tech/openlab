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

<p>This feature allows you to place a badge author for post displayed. In badge author can specify some parameters such as the link 
on google plus, the cover image and the photograph of the profile. All these fields must be listed on the configuration page
and connected to the user profile defined in wordpress.</p>

<p>To add this component you have to use the shortcode <b>[sz-gplus-author]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_gplus_get_badge_author(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table. The widget parameters are obtained 
directly from the GUI, but if you use the shortcode or PHP function you must specify in the format option="value".</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th> <th>Description</th> <th>Allowed values</th>              <th>Default</th></tr>
	<tr><td>width</td>     <td>width</td>       <td>valore,auto</td>                 <td>auto</td></tr>
	<tr><td>mode</td>      <td>mode</td>        <td>1=post, 2=post and archive</td>  <td>1=post</td></tr>
	<tr><td>cover</td>     <td>cover</td>       <td>1=profile, N=none</td>           <td>1=profile</td></tr>
	<tr><td>biografy</td>  <td>biografy</td>    <td>1=profile, 2=author, N=none</td> <td>1=profile</td></tr>
	<tr><td>link</td>      <td>link</td>        <td>1=google+, 2=author page</td>    <td>1=google+</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-gplus-author width="300" cover="1"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'width'    => 'auto',
  'cover'    => '1',
  'biografy' => '2',
  'mode'     => '1',
);

if (function_exists('szgoogle_gplus_get_badge_author')) {
  echo szgoogle_gplus_get_badge_author(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge author','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));