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

<p>Con questa funzione è possibile inserire un post di google plus completamente funzionante in una 
pagina web. Infatti una volta inserito sarà possibile eseguire tutte le azioni e inserire i commenti senza lasciare la 
pagina e rimanendo nel post. Praticamente come un video youtube in embed, solamente che questa volta viene
inserito il post pubblicato su G+ invece che un video.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-gplus-post]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP messa a disposizione dal plugin 
<b>szgoogle_gplus_get_post(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella a seguire. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore". Se volete avere delle informazioni aggiuntive potete visitare la pagina ufficiale
<a target="_blank" href="https://developers.google.com/+/web/embedded-post/?hl=it">Google+ Embedded Posts</a>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th> <th>Descrizione</th>            <th>Valori ammessi</th>         <th>Default</th></tr>
	<tr><td>url</td>       <td>indirizzo URL completo</td> <td>stringa</td>                <td>post corrente</td></tr>
	<tr><td>align</td>     <td>allineamento</td>           <td>left,center,right,none</td> <td>none</td></tr>
</table>

<h2>Parametro URL</h2>
<p>Attenzione nello specificare il valore URL che deve essere indicato nella sua forma canonical.</p>

<pre>
CORRETTO   => https://plus.google.com/110174288943220639247/posts/cfjDgZ7zK8o
NON VALIDO => https://plus.google.com/+LarryPage/posts/MtVcQaAi684
NON VALIDO => https://plus.google.com/u/0/106189723444098348646/posts/MtVcQaAi684
</pre>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-post url="https://plus.google.com/106567288702045182616/posts/9LHCj2ybzhn"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'   => 'https://plus.google.com/106567288702045182616/posts/9LHCj2ybzhn',
  'align' => 'center',
);

if (function_exists('szgoogle_gplus_get_post')) {
  echo szgoogle_gplus_get_post(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ embedded post','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));