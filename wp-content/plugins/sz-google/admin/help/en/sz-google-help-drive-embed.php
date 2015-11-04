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

<p>Through <b>Google Drive Embed</b> component, present in the SZ-Google plugin you can insert into a wordpress web page a document 
present on google drive. We can insert a presentation, a spreadsheet, the contents a folder, etc, etc. The important thing is that the 
document is stored on google drive is that it is published, so before you use this component entries in the document from the File 
menu and choose the option "Publish to Web".</p>

<p>To add this module you have to use the shortcode <b>[sz-drive-embed]</b>, but if you want to use it in a sidebar then you have to use 
the widget developed for this function in menu appearance -> widgets. For the most demanding there is also another possibility, 
in fact just use a PHP function provided by the plugin <b>szgoogle_drive_get_embed(\$options)</b>.</p>

<h2>Customization</h2>

<p>The component can be customized in many ways, just use the parameters listed in the table provided below. Regarding the widget 
parameters are obtained directly from the GUI, but if you use the shortcode or PHP function you must specify them manually in the 
format option = "value". If you would like additional information you can visit 
<a target="_blank" href="https://support.google.com/drive/topic/2811739?hl=en&ref_topic=2799627">Use Docs, Sheets, and Slides</a>.</p>

<h2>Parameters and options</h2>

<table>
	<tr><th>Parameter</th>    <th>Description</th>            <th>Allowed values</th>         <th>Default</th></tr>
	<tr><td>type</td>         <td>document type</td>          <td>document,folder,spreadsheet,<br/>presentation,forms,pdf,video,image</td> <td>document</td></tr>
	<tr><td>id</td>           <td>document id</td>            <td>string</td>                 <td>null</td></tr>
	<tr><td>width</td>        <td>width</td>                  <td>value</td>                  <td>configuration</td></tr>
	<tr><td>height</td>       <td>height</td>                 <td>value</td>                  <td>configuration</td></tr>
	<tr><td>single</td>       <td>spreadsheet single</td>     <td>true,false</td>             <td>false</td></tr>
	<tr><td>gid</td>          <td>spreadsheet id</td>         <td>0,1,2,3,4,5,6 etc</td>      <td>0</td></tr>
	<tr><td>range</td>        <td>spreadsheet range</td>      <td>string</td>                 <td>null</td></tr>
	<tr><td>start</td>        <td>presentation start</td>     <td>true,false</td>             <td>false</td></tr>
	<tr><td>loop</td>         <td>presentation loop</td>      <td>true.false</td>             <td>false</td></tr>
	<tr><td>delay</td>        <td>presentation delay sec</td> <td>1,2,3,4,5,10,15,30,60</td>  <td>3</td></tr>
	<tr><td>folderview</td>   <td>folder view</td>            <td>list,grid</td>              <td>list</td></tr>
	<tr><td>margintop</td>    <td>margin top</td>             <td>value,none</td>             <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margin right</td>           <td>value,none</td>             <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margin bottom</td>          <td>value,none</td>             <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margin left</td>            <td>value,none</td>             <td>none</td></tr>
	<tr><td>marginunit</td>   <td>margin unit</td>            <td>em,pt,px</td>               <td>em</td></tr>
</table>

<h2>Shortcode example</h2>

<p>The shortcodes are macros that are inserted in to post requires some additional processing that have been made ​​available by plugins,
themes, or directly from the core. The plugin <b>SZ-Google</b> provides several shortcode beings that can be used in the classical 
form and with the customization options allowed. To insert a shortcode in our post we have to use the code:</p>

<pre>[sz-drive-embed type="document" id="1nIKhA_U41fGLC_99hp_uB8lM6Ef0IffspkwTp2Sk_eI"/]</pre>

<h2>PHP code example</h2>

<p>If you want to use PHP functions of the plugin you need to be sure that the specific module is active, when you have verified this,
include the functions in your theme and specifies the various options through an array. It is advisable to use before the function 
check if this exists, in this way you will not have PHP errors when plugin disabled or uninstalled.</p>

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

<h2>Warnings</h2>

<p>The plugin <b>SZ-Google</b> has been developed with a technique of loading individual modules to optimize overall performance, 
so before you use a shortcode, a widget, or a PHP function you should check that the module general and the specific option appears 
enabled via the field dedicated option that you find in the admin panel.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('drive embed','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));