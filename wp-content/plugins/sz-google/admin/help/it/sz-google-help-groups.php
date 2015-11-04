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

<p>Tramite questa funzione presente nel plugin <b>SZ-Google</b> è possibile inserire in un post di wordpress o su una sidebar il 
widget dei gruppi presenti su google. Per ottenere ulteriori informazioni sui gruppi leggi la pagina ufficiale su <a target="_blank" href="https://groups.google.com">https://groups.google.com</a>.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-ggroups]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata 
<b>szgoogle_groups_get_code(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://support.google.com/groups/answer/1191206?hl=it">Insert a forum into a webpage</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>      <th>Descrizione</th>           <th>Valori ammessi</th> <th>Default</th></tr>
	<tr><td>name</td>           <td>nome del gruppo</td>       <td>stringa</td>        <td>configurazione</td></tr>
	<tr><td>domain</td>         <td>nome del dominio APPs</td> <td>stringa</td>        <td>configurazione</td></tr>
	<tr><td>width</td>          <td>larghezza</td>             <td>valore</td>         <td>configurazione</td></tr>
	<tr><td>height</td>         <td>altezza</td>               <td>valore</td>         <td>configurazione</td></tr>
	<tr><td>showsearch</td>     <td>visualizzare ricerca</td>  <td>true,false</td>     <td>configurazione</td></tr>
	<tr><td>showtabs</td>       <td>visualizzare tabs</td>     <td>true,false</td>     <td>configurazione</td></tr>
	<tr><td>hideforumtitle</td> <td>nascondi titolo</td>       <td>true,false</td>     <td>configurazione</td></tr>
	<tr><td>hidesubject</td>    <td>nascondi soggetto</td>     <td>true,false</td>     <td>configurazione</td></tr>
	<tr><td>hl</td>             <td>linguaggio</td>            <td>codice lingua</td>  <td>configurazione</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-ggroups height="1200"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'name'    => 'comp.sys.ibm.as400.misc',
  'height'  => '1200',
  'showtabs'=> 'true',
);

if (function_exists('szgoogle_groups_get_code')) {
  echo szgoogle_groups_get_code(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google groups','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));