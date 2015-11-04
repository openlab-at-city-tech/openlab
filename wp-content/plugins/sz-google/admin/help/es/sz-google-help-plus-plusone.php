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

<p>If you are comfortable with Google+ you know the +1 button, the function that suggests your appreciacion to a post pubblished
on Google+. Using this plugin you can easely put a +1 button pointing at every url of your website. If you simply insert the button
it will be linked to the url of the actual page you are in. If you want to change the pointment of the +1 button use the parameter
url="URL" in the shortcode, in this way you can create multiple buttons into the same page pointing to different links.</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-gplus-one]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_gplus_get_button_one(\$options)</b>.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th>    <th>Descripción</th>          <th>Valores</th>                    <th>Defecto</th></tr>
	<tr><td>url</td>          <td>complete address URL</td> <td>cadena</td>                     <td>entrada actual</td></tr>
	<tr><td>size</td>         <td>size</td>                 <td>small,medium,standard,tail</td> <td>standard</td></tr>
	<tr><td>width</td>        <td>width</td>                <td>valor</td>                      <td>null</td></tr>
	<tr><td>annotation</td>   <td>annotation</td>           <td>inline,bubble,none</td>         <td>none</td></tr>
	<tr><td>float</td>        <td>float</td>                <td>left,right,none</td>            <td>none</td></tr>
	<tr><td>align</td>        <td>alignment</td>            <td>left,center,right,none</td>     <td>none</td></tr>
	<tr><td>text</td>         <td>text</td>                 <td>cadena</td>                     <td>null</td></tr>
	<tr><td>img</td>          <td>image</td>                <td>cadena</td>                     <td>null</td></tr>
	<tr><td>position</td>     <td>position</td>             <td>top,center,bottom,outside</td>  <td>outside</td></tr>
	<tr><td>margintop</td>    <td>margin top</td>           <td>valor,none</td>                 <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margin right</td>         <td>valor,none</td>                 <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margin bottom</td>        <td>valor,none</td>                 <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margin left</td>          <td>valor,none</td>                 <td>none</td></tr>
	<tr><td>marginunit</td>   <td>margin unit</td>          <td>em,pt,px</td>                   <td>em</td><tr>
</table>

<h2>Button wrapper</h2>

<p>The behavior of the button of google is to draw the component and connect it to the permitted actions. The <b>SZ-Google</b>
plugin has improved this feature and added parameters to allow the drawing of a container on which the button can be placed. For 
example, we can specify an image and place the button within it and specifying the position.</p>

<pre>[sz-gplus-one img="http://dominio.com/image.jpg" position="bottom" align="right"/]</pre>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-gplus-one size="medium" annotation="bubble"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
\$options = array(
  'url'        => 'http://domain.com/article.php',
  'size'       => 'medium',
  'annotation' => 'bubble',
);

if (function_exists('szgoogle_gplus_get_button_one')) {
  echo szgoogle_gplus_get_button_one(\$options);
}
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ button +1','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));