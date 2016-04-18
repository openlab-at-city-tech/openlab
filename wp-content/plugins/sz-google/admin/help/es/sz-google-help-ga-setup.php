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

<h2>Descripción</h2>

<p>Google Analytics is a free service made ​​available by Google to control access statistics that relate to a website, this tool is 
specially used by web marketers and webmasters who use the service by adding a small HTML code to your web pages, which allows 
him monitoring and collection of information related to visitors.</p>

<p>Through this module present in the SZ-plugin Google can do the same thing without knowing any aspect of programming that covers 
HTML or PHP. In fact, just enter the required information and the code will be entered manually on your web pages. Obviously you 
already have a valid account on google analytics. See <a target="_blank" href="http://www.google.com/analytics/">http://www.google.com/analytics/</a>.</p>

<h2>Module activation</h2>

<p>Once you verified you have got a valid Google Analytics account you can go on activating the specific module into plugin’s 
general section and you can insert the monitoring related UA code. Double check your options to choose when the plugin should put 
the monitoring on, do you need it only in front-end pages or into administration panel too? You can also exclude administrators 
access or logged users access from monitoring in order not to have them counted into your statistics and falsify your results.</p>

<h2>Tracking code</h2>

<p>By default the code is written into the &lt;head&gt; section of your HTML page, in the exact position recommended by Google, 
anyway you can modify this feature and decide to put it on the bottom of page or manually using a PHP function you can insert 
anywhere in your page code, even adding some custom PHP conditions to include or exclude monitoring. Manual insert can be made 
using <b>szgoogle_analytics_get_code()</b> function; it doesn’t need parameters and can be invoked anywhere in your theme.</p>

<pre>
if (function_exists('szgoogle_analytics_get_code')) {
  echo szgoogle_analytics_get_code();
}
</pre>

<h2>Universal Analytics</h2>

<p>Google has released a new tracking code called the Universal Analytics, which introduces a number of features that change the way 
in which data are collected and organized in your Google Analytics account, so you can get a better understanding of online content. 
For all the websites that have been configured in the old method is the need for a conversion that is made directly from the admin 
panel of the GA. Only after this conversion can activate the option of Universal Analytics on plugin SZ-Google which in any case 
automatically manages both the old and the new code.</p>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('analytics setup','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));