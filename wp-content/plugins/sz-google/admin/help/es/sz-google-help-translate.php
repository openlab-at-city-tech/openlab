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

<p>With this option we can put on our website a language selector that performs the automatic translation of the page without 
leaving the site. Obviously, we can not expect a quality translation such as those performed manually. It is still a useful 
tool for those who publish articles and do not have the time to translate them. Surely it is better than nothing ...</p>

<p>To add this module you have to use the shortcode <b>[sz-gtranslate]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_translate_get_code(\$options)</b>.</p>

<h2>Configuration</h2>

<p>Before using the module google translate you need to register the site on your google account using the steps that are on
official page <a target="_blank" href="https://translate.google.com/manager‎">Google Translate Tool</a>. 
Once you have configured your site perform the action "get code" and copy the specified "meta".
Take care to only enter the numeric code and not all of the HTML code.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th>  <th>Descripción</th>             <th>Valores</th>        <th>Defecto</th></tr>
	<tr><td>language</td>   <td>language of the widget</td>  <td>cadena</td>         <td>configuración</td></tr>
	<tr><td>mode</td>       <td>display mode</td>            <td>V,H,D</td>          <td>configuración</td></tr>
	<tr><td>automatic</td>  <td>automatic banner</td>        <td>y=yes,n=no</td>     <td>configuración</td></tr>
	<tr><td>analytics</td>  <td>google analytics</td>        <td>y=yes,n=no</td>     <td>configuración</td></tr>
	<tr><td>uacode</td>     <td>google analytics UA</td>     <td>cadena</td>         <td>configuración</td></tr>
</table>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-gtranslate mode="V" language="it_IT" automatic="yes"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
\$options = array(
  'language'  => 'it_IT',
  'mode'      => 'V',
  'automatic' => 'yes',
);

if (function_exists('szgoogle_translate_get_code')) {
  echo szgoogle_translate_get_code(\$options);
}
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('translate setup','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));