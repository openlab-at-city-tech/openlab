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

<h2>Descrizione</h2>

<p>Questo modulo del plugin <b>SZ-Google</b> permette di inserire nei propri articoli delle gallerie fotografiche presenti in panoramio, 
basta specificare il template desiderato e le opzioni richieste. Potete usare quattro template diversi, photo, list, slideshow e 
photo_list. Per ulteriori informazioni sui parametri richiesti leggete la pagina ufficiale <a href="http://www.panoramio.com/api/widget/api.html">Panoramio Widget API </a>.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-panoramio]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata 
<b>szgoogle_panoramio_get_code(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="http://www.panoramio.com/api/widget/api.html">Panoramio Widget API </a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>   <th>Descrizione</th>                   <th>Valori ammessi</th>                  <th>Default</th></tr>
	<tr><td>template</td>    <td>tipo di widget</td>                <td>photo,slideshow,list,photo_list</td> <td>photo</td></tr>
	<tr><td>user</td>        <td>ricerca per utente</td>            <td>stringa</td>                         <td>null</td></tr>
	<tr><td>group</td>       <td>ricerca per gruppo</td>            <td>stringa</td>                         <td>null</td></tr>
	<tr><td>tag</td>         <td>ricerca per tag</td>               <td>stringa</td>                         <td>null</td></tr>
	<tr><td>set</td>         <td>selezionare tipo</td>              <td>all,public,recent</td>               <td>all</td></tr>
	<tr><td>widht</td>       <td>larghezza per widget in pixel</td> <td>valore</td>                          <td>auto</td></tr>
	<tr><td>height</td>      <td>altezza per widget in pixel</td>   <td>valore</td>                          <td>300</td></tr>
	<tr><td>bgcolor</td>     <td>colore di sfondo</td>              <td>hexadecimal</td>                     <td>null</td></tr>
	<tr><td>columns</td>     <td>colonne per foto</td>              <td>valore</td>                          <td>4</td></tr>
	<tr><td>rows</td>        <td>righe per foto</td>                <td>valore</td>                          <td>1</td></tr>
	<tr><td>orientation</td> <td>orientamento della lista</td>      <td>horizontal,vertical</td>             <td>horizontal</td></tr>
	<tr><td>list_size</td>   <td>lista di fotografie</td>           <td>numeric</td>                         <td>6</td></tr>
	<tr><td>position</td>    <td>posizione della lista di foto</td> <td>left,top,right,bottom</td>           <td>bottom</td></tr>
	<tr><td>delay</td>       <td>attesa in secondi</td>             <td>valore</td>                          <td>2</td></tr>
	<tr><td>paragraph</td>   <td>paragrafo dummy</td>               <td>true,false</td>                      <td>true</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-panoramio template="list" columns="6" rows="3" height="300" bgcolor="#e1e1e1"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

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

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('widget panoramio','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));