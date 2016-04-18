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

<p>Through <b>Google Drive Embed</b> component, present in the SZ-Google plugin you can insert into a wordpress web page a document 
present on google drive. We can insert a presentation, a spreadsheet, the contents a folder, etc, etc. The important thing is that the 
document is stored on google drive is that it is published, so before you use this component entries in the document from the File 
menu and choose the option "Publish to Web".</p>

<p>Para insertar este componente debe utilizar el código corto <b>[sz-drive-embed]</b>, si desea utilizarlo en una barra lateral,
usted tiene que utilizar el widget desarrollado para esta función que se encuentran en el menú apariencia => widgets. Para los más 
exigentes hay otra posibilidad, tiene que utilizar una función llamada PHP <b>szgoogle_drive_get_embed(\$options)</b>.</p>

<h2>Personalización</h2>

<p>Independientemente de la forma que va a utilizar, el componente se puede personalizar de diferentes maneras, sólo tiene que 
utilizar los parámetros puesto a disposición y listada en la tabla. En cuanto el widgets, se requieren los parámetros directamente
desde la interfaz gráfica de usuario, mientras que si se utiliza la función PHP o shortcode tiene que especificar manualmente.</p>

<h2>Parámetros y opciones</h2>

<table>
	<tr><th>Parámetro</th>    <th>Descripción</th>            <th>Valores</th>                <th>Defecto</th></tr>
	<tr><td>type</td>         <td>document type</td>          <td>document,folder,spreadsheet,<br/>presentation,forms,pdf,video,image</td> <td>document</td></tr>
	<tr><td>id</td>           <td>document id</td>            <td>cadena</td>                 <td>null</td></tr>
	<tr><td>width</td>        <td>width</td>                  <td>valor</td>                  <td>configuración</td></tr>
	<tr><td>height</td>       <td>height</td>                 <td>valor</td>                  <td>configuración</td></tr>
	<tr><td>single</td>       <td>spreadsheet single</td>     <td>true,false</td>             <td>false</td></tr>
	<tr><td>gid</td>          <td>spreadsheet id</td>         <td>0,1,2,3,4,5,6 etc</td>      <td>0</td></tr>
	<tr><td>range</td>        <td>spreadsheet range</td>      <td>cadena</td>                 <td>null</td></tr>
	<tr><td>start</td>        <td>presentation start</td>     <td>true,false</td>             <td>false</td></tr>
	<tr><td>loop</td>         <td>presentation loop</td>      <td>true.false</td>             <td>false</td></tr>
	<tr><td>delay</td>        <td>presentation delay sec</td> <td>1,2,3,4,5,10,15,30,60</td>  <td>3</td></tr>
	<tr><td>folderview</td>   <td>folder view</td>            <td>list,grid</td>              <td>list</td></tr>
	<tr><td>margintop</td>    <td>margin top</td>             <td>valor,none</td>             <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margin right</td>           <td>valor,none</td>             <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margin bottom</td>          <td>valor,none</td>             <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margin left</td>            <td>valor,none</td>             <td>none</td></tr>
	<tr><td>marginunit</td>   <td>margin unit</td>            <td>em,pt,px</td>               <td>em</td></tr>
</table>

<h2>Ejemplo de Shortcode</h2>

<p>Los shortcodes son códigos de macro que se insertan en un artículo de wordpress. Son procesados ​​por los plugins,
temas o incluso el núcleo. El plugin de SZ-Google tiene una gama de shortcodes que se pueden utilizar para las 
funciones previstas. Cada shortcode tiene varias opciones de configuración para las personalizaciones.</p>

<pre>[sz-drive-embed type="document" id="1nIKhA_U41fGLC_99hp_uB8lM6Ef0IffspkwTp2Sk_eI"/]</pre>

<h2>Ejemplo de código PHP</h2>

<p>Si desea utilizar las funciones de PHP del plugin, asegurarse de que el módulo específico está activo, cuando se ha 
verificado esto, incluir las funciones en su tema y especifica las distintas opciones a través de una matriz. Es recomendable
comprobar si hay la función, de esta manera no tendrá errores de PHP cuando el Plugin es deshabilitado o desinstalado.</p>

<pre>
\$options = array(
  'type'   => 'presentation',
  'id'     => 'bJr41NtMdfvD5pOZL9ZeNfeUvK8Gg4gZFyeqM8',
  'width'  => 'auto',
  'height' => '300',
  'delay'  => '5',
  'start'  => 'true',
  'loop'   => 'false',
);

if (function_exists('szgoogle_drive_get_embed')) {
  echo szgoogle_drive_get_embed(\$options);
}
</pre>

<h2>Supported formats</h2>

<p>At present, these formats are supported by the plugin to run an embed code from google drive to the web page, I leave here 
some shortcode that you can try to control the correct functioning.</p>

<pre>
[sz-drive-embed type="document" id="1nIKhA_U41fGLC_99hp_uB8lM6Ef0IffspkwTp2Sk_eI"/]
[sz-drive-embed type="spreadsheet" id="0AsB1V5PwB8NjdGdLRm1MYW9SSUNWRWNrVXdqQ2hKTmc"/]
[sz-drive-embed type="presentation" id="1BS67-bJr41NtMdfvD5pOZL9ZeNfeUvK8Gg4gZFyeqM8"/]
[sz-drive-embed type="forms" id="1XK4lmkJ1_DPrrxhF8zY7QCpyfX7Ux2_W_DBkgbMTzeo"/]
[sz-drive-embed type="pdf" id="0B8B1V5PwB8NjTDhMckQ5MlVENjQ"/]
[sz-drive-embed type="video" id="0B8B1V5PwB8NjZFpNNG0tS3dmNTQ" height="300"/]
[sz-drive-embed type="folder" id="0B8B1V5PwB8NjdHpXR0dhck1EaW8" folderview="list"/]
[sz-drive-embed type="image" id="0B8B1V5PwB8NjQ0ZYbVozWTVEbjA"/]
</pre>

<h2>Advertencias</h2>

<p>El plugin <b>SZ-Google</b> se ha desarrollado con una técnica de módulos de carga individuales para optimizar el rendimiento general,
así que antes de utilizar un shortcode, un widget o una función PHP debe comprobar que aparece el módulo general y la opción específica
permitido a través de la opción que se encuentra en el panel de administración.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('drive embed','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));