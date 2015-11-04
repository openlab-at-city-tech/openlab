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

<p>With this plugin function <b>SZ-Google</b> you can enter a post from google plus fully functional in a web page. In fact, once 
inserted will be able to perform all social actions and comment without leaving the page and remaining in the original post of 
wordpress. Almost like a youtube embed video, only this time it is inserted into the post published on Google+ instead of a video.</p>

<p>To add this button you have to use the shortcode <b>[sz-gplus-post]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_gplus_get_post(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit the official page 
<a target="_blank" href="https://developers.google.com/+/web/embedded-post/?hl=it">Google+ Embedded Posts</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parametro</th> <th>Description</th>          <th>Allowed values</th>         <th>Default</th></tr>
	<tr><td>url</td>       <td>complete address URL</td> <td>string</td>                 <td>current post</td></tr>
	<tr><td>align</td>     <td>alignment</td>            <td>left,center,right,none</td> <td>none</td></tr>
</table>

<h2>URL parameter</h2>

<p>Careful to specify the URL value that must be entered in its canonical form.</p>

<pre>
CORRECT    => https://plus.google.com/110174288943220639247/posts/cfjDgZ7zK8o
NO CORRECT => https://plus.google.com/+LarryPage/posts/MtVcQaAi684
NO CORRECT => https://plus.google.com/u/0/106189723444098348646/posts/MtVcQaAi684
</pre>

<h2>Unsupported posts</h2>

<ul>
<li>Posts that are restricted to a Google Apps domain.</li>
<li>Private posts.</li>
<li>Events posts.</li>
<li>Hangout on Air posts.</li>
<li>Posts from within a community, including publicly reshared posts from a community.</li>
</ul>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-gplus-post url="https://plus.google.com/106567288702045182616/posts/9LHCj2ybzhn"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

<pre>
\$options = array(
  'url'   => 'https://plus.google.com/106567288702045182616/posts/9LHCj2ybzhn',
  'align' => 'center',
);

if (function_exists('szgoogle_gplus_get_post')) {
  echo szgoogle_gplus_get_post(\$options);
}
</pre>

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ embedded post','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));