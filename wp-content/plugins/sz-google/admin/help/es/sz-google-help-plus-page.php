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

<p>If you have a page on Google+ and want to add it on your website or integrate it with your badge publisher then this is
the right tool . The badge can be added and customized via several different methods options put at our disposal by the plugin itself. 
The badge is inserted through an iframe technique, in this manner are complied with all the features defined by google.</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-gplus-page]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_gplus_get_badge_page(\$options)</b>.</p>

<h2>Publisher</h2>

<p>This badge can also be used to activate the publisher. Just use the parameter id="page" and publisher="true" in the badge and 
put it in the pages of the site. Remember to certify that this function must be included in its Google+ page the name of the 
website with your own domain. Once all the necessary passages can try on the page
<a target="_blank" href="http://www.google.com/webmasters/tools/richsnippets">Structured Data Testing Tool</a>.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th> <th>Descripción</th>           <th>Valores</th>                <th>Defecto</th></tr>
	<tr><td>id</td>        <td>page</td>                  <td>cadena</td>                 <td>configuración</td></tr>
	<tr><td>type</td>      <td>mode</td>                  <td>standard,popup</td>         <td>standard</td></tr>
	<tr><td>width</td>     <td>width</td>                 <td>valor,auto</td>             <td>configuración</td></tr>
	<tr><td>align</td>     <td>alignment</td>             <td>left,center,right,none</td> <td>none</td></tr>
	<tr><td>layout</td>    <td>layout</td>                <td>portrait,landscape</td>     <td>portrait</td></tr>
	<tr><td>theme</td>     <td>theme</td>                 <td>light,dark</td>             <td>light</td></tr>
	<tr><td>cover</td>     <td>cover</td>                 <td>true,false</td>             <td>true</td></tr>
	<tr><td>tagline</td>   <td>tagline</td>               <td>true,false</td>             <td>true</td></tr>
	<tr><td>publisher</td> <td>rel=publisher in HTML</td> <td>true,false</td>             <td>false</td></tr>
	<tr><td>text</td>      <td>pop-up text</td>           <td>cadena</td>                 <td>null</td></tr>
	<tr><td>image</td>     <td>pop-up URL image</td>      <td>cadena</td>                 <td>null</td></tr>
</table>

<h2>Standard or popup</h2>

<p>As you can see from the table of options is called a <b>type</b> parameter with which you can choose to display the badge in a 
standard way and then immediately draw the badge in the HTML page or request a viewing mode only popup by passing the cursor over a 
<b>text</b> or <b>image</b>. In this case you have to specify the parameters to the function pop-up text and image.</p>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-gplus-page id="117259631219963935481" type="standard" width="auto"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
\$options = array(
  'id'     => '117259631219963935481',
  'type'   => 'standard',
  'width'  => 'auto',
  'theme'  => 'dark',
  'layout' => 'portrait'
);

if (function_exists('szgoogle_gplus_get_badge_page')) {
  echo szgoogle_gplus_get_badge_page(\$options);
}
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge page','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));