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

<p>Tramite questa funzione è possibile inserire in embed sul proprio sito il calendario di google. Potete specificare anche diversi 
calendari, basta specificare nel parametro <b>"calendar"</b> una stringa con il nomi dei calendari separati da una virgola. Se non
viene specificato nessun calendario sarà utilizzato quello memorizzato nella configurazione generale.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-calendar]</b>, se invece volete utilizzarlo in una sidebar 
allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP messa a disposizione dal plugin 
<b>szgoogle_calendar_get_widget(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://www.google.com/calendar/embedhelper">Google Embeddable Calendar Helper</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>     <th>Descrizione</th>            <th>Valori ammessi</th>    <th>Default</th></tr>
	<tr><td>calendar</td>      <td>calendario</td>             <td>stringa</td>           <td>configurazione</td></tr>
	<tr><td>title</td>         <td>titolo</td>                 <td>stringa</td>           <td>configurazione</td></tr>
	<tr><td>mode</td>          <td>modalità</td>               <td>AGENDA,WEEK,MONTH</td> <td>configurazione</td></tr>
	<tr><td>weekstart</td>     <td>partenza su settimana</td>  <td>1,2,7</td>             <td>configurazione</td></tr>
	<tr><td>language</td>      <td>lingua</td>                 <td>stringa</td>           <td>configurazione</td></tr>
	<tr><td>timezone</td>      <td>zona oraria</td>            <td>stringa</td>           <td>configurazione</td></tr>
	<tr><td>width</td>         <td>larghezza</td>              <td>valore,auto</td>       <td>configurazione</td></tr>
	<tr><td>height</td>        <td>altezza</td>                <td>valore</td>            <td>configurazione</td></tr>
	<tr><td>showtitle</td>     <td>visualizza titolo</td>      <td>yes,no</td>            <td>configurazione</td></tr>
	<tr><td>shownavs</td>      <td>visualizza navigatore</td>  <td>yes,no</td>            <td>configurazione</td></tr>
	<tr><td>showdate</td>      <td>visualizza data</td>        <td>yes,no</td>            <td>configurazione</td></tr>
	<tr><td>showprint</td>     <td>visualizza stampa</td>      <td>yes,no</td>            <td>configurazione</td></tr>
	<tr><td>showcalendars</td> <td>visualizza calendario</td>  <td>yes,no</td>            <td>configurazione</td></tr>
	<tr><td>showtimezone</td>  <td>visualizza zona oraria</td> <td>yes,no</td>            <td>configurazione</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-calendar showprint="no"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'calendar'  => 'gt0ejukbb55l7xxcl4qi1j62ng@group.calendar.google.com',
  'title'     => 'My Calendar',
  'mode'      => 'AGENDA',
  'showtitle' => 'no',
  'showdate'  => 'no'
);

if (function_exists('szgoogle_calendar_get_widget')) {
  echo szgoogle_calendar_get_widget(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('widget calendar','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));