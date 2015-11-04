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
bottone per salvare una risorsa statica presente nel nostro sito su Google Drive. Ad esempio possiamo pubblicare un link di un 
manuale in PDF e richiedere all’utente se vuole salvarlo sul suo spazio Google Drive o potremmo visualizzare una foto e dare 
la possibilità di memorizzarla in Drive in differenti formati ad alta risoluzione per una consultazione in differita.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-drive-save]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata 
<b>szgoogle_drive_get_savebutton(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://developers.google.com/drive/web/savetodrive?hl=it">Save to Drive Button</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>    <th>Descrizione</th>            <th>Valori ammessi</th>             <th>Default</th></tr>
	<tr><td>url</td>          <td>indirizzo URL del file</td> <td>stringa</td>                    <td>post corrente</td></tr>
	<tr><td>filename</td>     <td>nome del file</td>          <td>valore</td>                     <td>null</td></tr>
	<tr><td>sitename</td>     <td>nome del sito</td>          <td>valore</td>                     <td>null</td></tr>
	<tr><td>text</td>         <td>testo</td>                  <td>stringa</td>                    <td>null</td></tr>
	<tr><td>img</td>          <td>immagine</td>               <td>stringa</td>                    <td>null</td></tr>
	<tr><td>position</td>     <td>posizione</td>              <td>top,center,bottom,outside</td>  <td>outside</td></tr>
	<tr><td>align</td>        <td>allineamento</td>           <td>left,center,right,none</td>     <td>none</td></tr>
	<tr><td>margintop</td>    <td>margine alto</td>           <td>valore,none</td>                <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margine destro</td>         <td>valore,none</td>                <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margine basso</td>          <td>valore,none</td>                <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margine sinistro</td>       <td>valore,none</td>                <td>none</td></tr>
	<tr><td>marginunit</td>   <td>misura per margine</td>     <td>em,pt,px</td>                   <td>em</td></tr>
</table>

<h2>Contenitore bottone</h2>

<p>Il comportamento standard del bottone di google è quello di disegnare solo il bottone e collegare ad esso le azioni interattive 
permesse. Il plugin <b>SZ-Google</b> ha cercato di migliorare questo comportamento ed ha aggiunto dei parametri per permettere 
il disegno di un contenitore su cui il bottone può essere positionato. Ad esempio possiamo specificare un'immagine e posizionare il
bottone all'interno di essa in overlay e nella posizione che vogliamo. Qui di seguito un'esempio per chiarire:</p>

<pre>[sz-drive-save url="URL" img="http://domain.com/image.jpg" position="bottom"/]</pre>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-drive-save filename="aws-web-application.pdf" url="URL_TO_PDF"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'      => 'http:/domain.com/filetosave.pdf',
  'filename' => 'medium',
  'align'    => 'center',
);

if (function_exists('szgoogle_drive_get_savebutton')) {
  echo szgoogle_drive_get_savebutton(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('drive save button','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));