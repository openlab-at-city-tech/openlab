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

<p>Tramite questa opzione possiamo inserire nel nostro sito un selettore di lingua che esegua la traduzione automatica della
pagina web visualizzata senza abbandonare il sito di origine. Ovviamente non possiamo pretendere una traduzione di qualità come
quelle eseguite manualmente, però è comunque uno strumento che ritorna utile a chi pubblica articoli che possono interessare
a persone che parlano altre lingue e non si ha la possibilità di eseguire delle traduzioni accurate.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-gtranslate]</b>, se invece volete utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP messa a disposizione dal plugin 
<b>szgoogle_translate_get_code(\$options)</b>.</p>

<h2>Configurazione</h2>

<p>Prima di usare il modulo di google translate bisogna registrare il sito sul proprio account google utilizzando 
<a target="_blank" href="https://translate.google.com/manager‎">Google Translate Tool</a>. 
Una volta inserito il proprio sito eseguire l'azione "get code" e prendere il codice meta specificato. Mi raccomando che nel campo che
trovate nel pannello di amministrazione dovete inserire solo il codice numerico e non tutto il codice HTML.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://translate.google.com/manager‎">Google Translate Manager</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>  <th>Descrizione</th>              <th>Valoria ammessi</th> <th>Default</th></tr>
	<tr><td>language</td>   <td>lingua del widget</td>        <td>stringa</td>         <td>configurazione</td></tr>
	<tr><td>mode</td>       <td>Modalità visualizzazione</td> <td>V,H,D</td>           <td>configurazione</td></tr>
	<tr><td>automatic</td>  <td>banner automatico</td>        <td>y=yes,n=no</td>      <td>configurazione</td></tr>
	<tr><td>analytics</td>  <td>google analytics</td>         <td>y=yes,n=no</td>      <td>configurazione</td></tr>
	<tr><td>uacode</td>     <td>google analytics UA</td>      <td>stringa</td>         <td>configurazione</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gtranslate mode="V" language="it_IT" automatic="yes"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'language'  => 'it_IT',
  'mode'      => 'V',
  'automatic' => 'yes',
);

if (function_exists('szgoogle_translate_get_code')) {
  echo szgoogle_translate_get_code(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, 
quindi prima di utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica 
risulti attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('translate setup','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));