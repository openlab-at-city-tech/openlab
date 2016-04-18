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

<p>Se hai una community su Google+ e vuoi inserirla sul tuo sito o integrarla con un badge allora questo è 
lo strumento adatto. Il badge può essere inserito in differenti metodi e personalizzato tramite
opzioni messe a disposizione dal plugin stesso. Il badge viene inserito tramite una tecnica iframe, in questa maniera vengono
rispettate tutte le caratteristiche definite da google.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-gplus-community]</b>, se invece desiderate utilizzarlo in una sidebar 
allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più esigenti esiste 
anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata <b>szgoogle_gplus_get_badge_community(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore".</p>

<h2>Parametri e opzioni</h2>

<table
	<tr><th>Parametro</th> <th>Descrizione</th>   <th>Valori ammessi</th>         <th>Default</th></tr>
	<tr><td>id</td>        <td>community</td>     <td>stringa</td>                <td>configurazione</td></tr>
	<tr><td>width</td>     <td>larghezza</td>     <td>integer,auto</td>           <td>configurazione</td></tr>
	<tr><td>align</td>     <td>allineamento</td>  <td>left,center,right,none</td> <td>none</td></tr>
	<tr><td>layout</td>    <td>layout</td>        <td>portrait,landscape</td>     <td>portrait</td></tr>
	<tr><td>theme</td>     <td>tema</td>          <td>light,dark</td>             <td>light</td></tr>
	<tr><td>photo</td>     <td>fotografia</td>    <td>true,false</td>             <td>true</td></tr>
	<tr><td>owner</td>     <td>proprietari</td>   <td>true,false</td>             <td>false</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-community id="109254048492234113886" width="auto"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'id'     => '109254048492234113886',
  'width'  => 'auto',
  'theme'  => 'dark',
  'layout' => 'portrait'
);

if (function_exists('szgoogle_gplus_get_badge_community')) {
  echo szgoogle_gplus_get_badge_community(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge community','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));