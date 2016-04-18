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

<p>Questa funzione permette l'inserimento di un <b>video youtube</b> su una pagina web. Il modulo youtube ha molti parametri che servono per 
aggiungere funzionalità o personalizzare alcuni aspetti che riguardano la modalità di inserimento, ad esempio possiamo decidere tra una dimensione 
fissa o responsive, è possibile scegliere tra un tema “dark” e uno “light”, agganciare il codice di google analytics, impostare alcuni parametri come fullscreen, disablekeyboard, autoplay e loop e molto altro ancora.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-ytvideo]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata 
<b>szgoogle_youtube_get_code_video(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://developers.google.com/youtube/player_parameters">YouTube Embedded Players</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>       <th>Descrizione</th>                 <th>Valori ammessi</th>       <th>Default</th></tr>
	<tr><td>url</td>             <td>indirizzo URL youtube</td>       <td>stringa</td>              <td>null</td></tr>
	<tr><td>responsive</td>      <td>modalità responsive</td>         <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>width</td>           <td>larghezza in pixel</td>          <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>height</td>          <td>altezza in pixel</td>            <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>margintop</td>       <td>margine alto</td>                <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>marginright</td>     <td>margine destro</td>              <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>marginbottom</td>    <td>margine basso</td>               <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>marginleft</td>      <td>margine sinistro</td>            <td>valore</td>               <td>configurazione</td></tr>
	<tr><td>marginunit</td>      <td>misura per margine</td>          <td>px,em</td>                <td>configurazione</td></tr>
	<tr><td>autoplay</td>        <td>attivazione autoplay</td>        <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>loop</td>            <td>attivazione loop</td>            <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>fullscreen</td>      <td>schermo intero</td>              <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>disablekeyboard</td> <td>disabilitare tastiera</td>       <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>theme</td>           <td>nome del tema</td>               <td>dark,light</td>           <td>configurazione</td></tr>
	<tr><td>cover</td>           <td>immagine copertina</td>          <td>local,youtube,URL,ID</td> <td>configurazione</td></tr>
	<tr><td>title</td>           <td>titolo del video</td>            <td>stringa</td>              <td>configurazione</td></tr>
	<tr><td>disableiframe</td>   <td>disattivare IFRAME</td>          <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>analytics</td>       <td>google analytics</td>            <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>delayed</td>         <td>caricamento ritardato</td>       <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>start</td>           <td>tempo di inizio</td>             <td>seconds</td>              <td>null</td></tr>
	<tr><td>end</td>             <td>tempo di fine</td>               <td>seconds</td>              <td>null</td></tr>
	<tr><td>disablerelated</td>  <td>disattivare video correlati</td> <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>schemaorg</td>       <td>abilitare schema.org</td>        <td>y=yes,n=no</td>           <td>configurazione</td></tr>
	<tr><td>name</td>            <td>schema.org nome</td>             <td>stringa</td>              <td>video youtube</td></tr>
	<tr><td>description</td>     <td>schema.org descrizione</td>      <td>stringa</td>              <td>valore del titolo</td></tr>
	<tr><td>duration</td>        <td>schema.org durata</td>           <td><a target="_blank" href="http://it.wikipedia.org/wiki/ISO_8601">format ISO 8601</a></td><td>null</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-ytvideo url="http://www.youtube.com/watch?v=gUdKmGASz3g"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'        => 'http://www.youtube.com/watch?v=gUdKmGASz3g',
  'responsive' => 'yes',
  'delayed'    => 'yes',
  'schemaorg'  => 'yes',
);

if (function_exists('szgoogle_youtube_get_code_video')) {
  echo szgoogle_youtube_get_code_video(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('youtube video','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));