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

<p>Tramite questa funzionalità è possibile inserire un link testuale o una singola immagine con un link diretto al canale. Il vantaggio 
di utilizzare questo link è quello di poter indicare la sottoscrizione automatica una volta che l'utente raggiunge la pagina del canale.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-ytlink]</b>, se invece desiderate utilizzarlo in una sidebar allora dovete 
utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più esigenti esiste anche un'altra 
possibilità, infatti basta utilizzare una funzione PHP chiamata <b>szgoogle_youtube_get_code_link(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore".</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>LINK</th>         <th>Descrizione</th>          <th>Valori ammessi</th> <th>Default</th></tr>
	<tr><td>channel</td>      <td>nome del canale o ID</td> <td>stringa</td>        <td>configurazione</td></tr>
	<tr><td>subscription</td> <td>sottoscrizione</td>       <td>y=yes,n=no</td>     <td>y=yes</td></tr>
	<tr><td>text</td>         <td>testo per il link</td>    <td>stringa</td>        <td>configurazione</td></tr>
	<tr><td>image</td>        <td>immagine per link</td>    <td>stringa URL</td>    <td>nessuna</td></tr>
	<tr><td>newtab</td>       <td>apertura link</td>        <td>y=yes,n=no</td>     <td>n=no</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-ytlink text="iscriviti al mio canale youtube"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'channel'      => 'cloudaws',
  'subscription' => 'yes',
  'text'         => 'iscriviti al mio canale youtube',
);

if (function_exists('szgoogle_youtube_get_code_link')) {
  echo szgoogle_youtube_get_code_link(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('youtube link','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));