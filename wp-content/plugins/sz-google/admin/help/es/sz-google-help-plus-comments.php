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

<p>In addition to badges and buttons Google+ offers a widget to manage a complete comment system linked to a web page URL. Once you 
got your widget on line it will look as simple as a traditional comment system, except for the necessary login to a Google+ profile 
in order to comment a post. When in use this widget automatically links the URL of the current page.</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-gplus-comments]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_gplus_get_comments(\$options)</b>.</p>

<h2>Comments configuration</h2>

<p>Google+ comments can also be published automatically by the plugin, using wordpress standard position and overriding the standard 
comments at all. On the SZ-Google plugin configuration menu, look for the “Google+” panel, inside which you can find a “Comments” 
section where you can set up various options according to your needs. For example you can activate / deactivate the automatic 
standard comments override feature, choosing to completely substitute standard comments.</p>

<p>You can choose to put Google+ comments right after the post content or after the standard wordpress comments. You can insert a starting 
date after which Google+ comment system will be activated, useful if you need to keep alive older posts’ standard comments and you 
want to start using Google+ comments from a precise date on.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th> <th>Descripción</th>          <th>Valores</th>                 <th>Defecto</th></tr>
	<tr><td>url</td>       <td>complete address URL</td> <td>cadena</td>                  <td>entrada actual</td></tr>
	<tr><td>width</td>     <td>width</td>                <td>valor,auto</td>              <td>auto</td></tr>
	<tr><td>align</td>     <td>alignment</td>            <td>left,center,right,none</td>  <td>none</td></tr>
</table>

<h2>Widget size</h2>

<p>The Plugin <b>SZ-Google</b> may place the widget comments with a fixed size or use the technique of responsive design automatically 
adapts to the size of the overall container. If you want a fixed size that you just used the value width="width", but if you specify 
width="auto" the plugin will use the method responsive.</p>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-gplus-comments url="http://domain.com/post.html"]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
\$options = array(
  'url'   => 'http://domain.com/post.html',
  'width' => 'auto',
  'align' => 'center',
);

if (function_exists('szgoogle_gplus_get_comments')) {
  echo szgoogle_gplus_get_comments(\$options);
}
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ widget comments','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));