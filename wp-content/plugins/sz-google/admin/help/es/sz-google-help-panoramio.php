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

<p>This module of the <b>SZ-Google</b> plugin allows you to enter in our articles a photo gallery present in Panoramio, you must 
specify the desired template and the required options. You can use four different templates, photo, list, slideshow and photo_list.
For more information about the parameters read the official page <a href="http://www.panoramio.com/api/widget/api.html">Panoramio Widget API</a>.</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-panoramio]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_panoramio_get_code(\$options)</b>.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th>   <th>Descripción</th>      <th>Valores</th>                         <th>Defecto</th></tr>
	<tr><td>template</td>    <td>widget type</td>      <td>photo,slideshow,list,photo_list</td> <td>photo</td></tr>
	<tr><td>user</td>        <td>search by user</td>   <td>cadena</td>                          <td>null</td></tr>
	<tr><td>group</td>       <td>search by group</td>  <td>cadena</td>                          <td>null</td></tr>
	<tr><td>tag</td>         <td>search by tag</td>    <td>cadena</td>                          <td>null</td></tr>
	<tr><td>set</td>         <td>select set</td>       <td>all,public,recent</td>               <td>all</td></tr>
	<tr><td>widht</td>       <td>widget widht</td>     <td>valor</td>                           <td>auto</td></tr>
	<tr><td>height</td>      <td>widget height</td>    <td>valor</td>                           <td>300</td></tr>
	<tr><td>bgcolor</td>     <td>background color</td> <td>hexadecimal</td>                     <td>null</td></tr>
	<tr><td>columns</td>     <td>columns</td>          <td>valor</td>                           <td>4</td></tr>
	<tr><td>rows</td>        <td>rows</td>             <td>valor</td>                           <td>1</td></tr>
	<tr><td>orientation</td> <td>orientation</td>      <td>horizontal,vertical</td>             <td>horizontal</td></tr>
	<tr><td>list_size</td>   <td>list size</td>        <td>numeric</td>                         <td>6</td></tr>
	<tr><td>position</td>    <td>position</td>         <td>left,top,right,bottom</td>           <td>bottom</td></tr>
	<tr><td>delay</td>       <td>delay seconds</td>    <td>valor</td>                           <td>2</td></tr>
	<tr><td>paragraph</td>   <td>dummy paragraph</td>  <td>true,false</td>                      <td>true</td></tr>
</table>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-panoramio template="list" columns="6" rows="3" height="300" bgcolor="#e1e1e1"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

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

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('widget panoramio','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));