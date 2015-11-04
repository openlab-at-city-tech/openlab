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

<p>Questa funzionalità permette di inserire un badge autore per il post visualizzato. Nel badge autore è possibile specificare alcuni 
parametri come ad esempio il link su google plus, l'immagine di copertina e la fotografia del profilo. Tutti questi campi devono essere
indicati nella pagina di configurazione collegata al profilo definito in wordpress.</p>

<p>Per inserire questo componente dovete usare lo shortcode <b>[sz-gplus-author]</b>, se invece desiderate utilizzarlo in una sidebar 
allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più esigenti esiste 
anche un'altra possibilità, infatti basta utilizzare una funzione PHP chiamata <b>szgoogle_gplus_get_badge_author(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore".</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th> <th>Descrizione</th>  <th>Valori ammessi</th>              <th>Default</th></tr>
	<tr><td>width</td>     <td>larghezza</td>    <td>valore,auto</td>                 <td>auto</td></tr>
	<tr><td>mode</td>      <td>modalità</td>     <td>1=post, 2=post e archivio</td>   <td>1=post</td></tr>
	<tr><td>cover</td>     <td>copertina</td>    <td>1=profilo, N=none</td>           <td>1=profilo</td></tr>
	<tr><td>biografy</td>  <td>biografia</td>    <td>1=profilo, 2=autore, N=none</td> <td>1=profilo</td></tr>
	<tr><td>link</td>      <td>collegamento</td> <td>1=google+, 2=pagina autore</td>  <td>1=google+</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-author width="300" cover="1"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'width'    => 'auto',
  'cover'    => '1',
  'biografy' => '2',
  'mode'     => '1',
);

if (function_exists('szgoogle_gplus_get_badge_author')) {
  echo szgoogle_gplus_get_badge_author(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge author','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));