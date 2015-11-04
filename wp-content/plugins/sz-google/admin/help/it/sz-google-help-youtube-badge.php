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

<p>Con questo componente è possibile inserire un badge legato al canale youtube ed eseguire l'azione di iscrizione direttamente dal componente 
senza andare nella pagina ufficiale presente su youtube. Potete indicare il nome del canale o il canale ID, quando specificate le dimensioni potete 
utilizzare i valori speciali "auto" e ottenere un dimensionamento automatico del contenitore.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-ytbadge]</b>, se invece desiderate utilizzarlo in una sidebar allora dovete 
utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più esigenti esiste anche un'altra 
possibilità, infatti basta utilizzare una funzione PHP chiamata <b>szgoogle_youtube_get_code_badge(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore".</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>BADGE</th>        <th>Descrizione</th>          <th>Valori ammessi</th> <th>Default</th></tr>
	<tr><td>channel</td>      <td>nome del canale o ID</td> <td>stringa</td>        <td>configurazione</td></tr>
	<tr><td>width</td>        <td>dimensione pixel</td>     <td>valore,auto</td>    <td>300</td></tr>
	<tr><td>height</td>       <td>dimensione pixel</td>     <td>valore,auto</td>    <td>150</td></tr>
	<tr><td>widthunit</td>    <td>unità dimensione</td>     <td>px,em,%</td>        <td>px</td></tr>
	<tr><td>heightunit</td>   <td>unità dimensione</td>     <td>px,em,%</td>        <td>px</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>[sz-ytbadge channel="TuttosuYTChannel" width="100" widthunit="%"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Se volete utilizzare le funzioni PHP messe a disposizione dal plugin dovete accertarvi che il modulo corrispondente sia attivo, una 
volta verificato inserite nel punto desiderato del vostro tema un codice simile al seguente esempio, quindi preparate un array con le
opzioni desiderate e richiamate la funzione richiesta. É consigliabile utilizzare prima della funzione il controllo se questa esista,
in questa maniera non si avranno errori PHP in caso di plugin disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'channel'   => 'TuttosuYTChannel',
  'width'     => 'yes',
  'widthunit' => '%',
);

if (function_exists('szgoogle_youtube_get_code_badge')) {
  echo szgoogle_youtube_get_code_badge(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('youtube badge','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));