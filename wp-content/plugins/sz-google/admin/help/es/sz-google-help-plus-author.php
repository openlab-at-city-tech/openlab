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

<p>Esta característica le permite colocar un autor insignia para que aparezca el entrada. En insignia autor puede especificar ciertos 
parámetros como el enlace en google+, la imagen de la portada y la foto del perfil. Todos estos campos se deben especificar en 
la página de configuración conectado al perfil definido en wordpress.</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-gplus-author]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_gplus_get_badge_author(\$options)</b>.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th> <th>Descripción</th> <th>Valores</th>                         <th>Defecto</th></tr>
	<tr><td>width</td>     <td>ancho</td>       <td>valore, auto</td>                    <td>auto</td></tr>
	<tr><td>mode</td>      <td>modalidad</td>   <td>1=entrada, 2=entrata y archivio</td> <td>1=entrada</td></tr>
	<tr><td>cover</td>     <td>portada</td>     <td>1=perfil, N=none</td>                <td>1=perfil</td></tr>
	<tr><td>biografy</td>  <td>biografía</td>   <td>1=perfil, 2=autor, N=none</td>       <td>1=perfil</td></tr>
	<tr><td>link</td>      <td>enlace</td>      <td>1=google+, 2=pagina autor</td>       <td>1=google+</td></tr>
</table>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-gplus-author width="300" cover="1"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

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

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge author','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));