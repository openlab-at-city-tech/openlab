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

<p>To configure a web page with the attribution of the author or publisher just use the badge page/profile specifying the activation 
with options author="true" and/or publisher="true". If you do not want to use google+ badge on your website, the plugin 
provides an alternative method. In fact, just turn on the options that you find in the admin panel call HEAD Publisher.</p>

<h2>HEAD code</h2>

<p>The code added by the plugin will be similar to the example shown below, the id of the profile and the page will be picked up by 
the general configuration of the module google+ present in the admin panel. The only thing you have to keep in mind is that while 
the publisher there is no problem to define it globally to the author are okay if your blog is single author, if by chance the 
website in question should not write different authors activate the function HEAD Author.</p>

<pre>
&lt;head&gt;
  &lt;link rel="author" href="https://plus.google.com/106189723444098348646"/&gt;
  &lt;link rel="publisher" href="https://plus.google.com/116899029375914044550"/&gt;
&lt;/head&gt;
</pre>

<h2>PHP functions</h2>

<table>
	<tr><td>szgoogle_gplus_get_contact_page()</td><td>Reperimento del campo profilo per google+ pagina.</td></tr>
	<tr><td>szgoogle_gplus_get_contact_community()</td><td>Reperimento del campo profilo per google+ community</td></tr>
	<tr><td>szgoogle_gplus_get_contact_betspost()</td><td>Reperimento del campo profilo per google+ best post.</td></tr>
</table>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
echo '&lt;div id="author"&gt;';

echo '&lt;div class="image"&gt;';
echo '&lt;img src="http://domain.com/image.jpg" alt="author"/&gt;';
echo '&lt;/div&gt;';'

if (function_exists('szgoogle_gplus_get_contact_page')) {
  echo '&lt;div class="link"&gt;';
  echo '&lt;a href="'.szgoogle_gplus_get_contact_page().'"&gt;My G+ Page&lt;/a&gt;';
  echo '&lt;/div&gt;';'
} 

echo '&lt;/div&gt;';
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ author & publisher','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));