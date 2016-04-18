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

<p>Google+ mette a disposizione oltre ai badge e ai bottoni anche un widget per gestire un sistema di commenti completo che
viene collegato al valore URL di una pagina web. Una volta che il widget viene visualizzato sarà possibile eseguire tutte
le funzioni interattive di un sistema di commenti tradizionale, può essere utilizzato solo se l'utente ha effettuato
il login su google+.</p> 

<p>Per inserire un widget di commenti dovete usare lo shortcode <b>[sz-gplus-comments]</b>, se invece desiderate utilizzarlo
in una sidebar dovete utilizzare il widget che trovate nel menu aspetto -> widgets. Per i più esigenti esiste anche un'altra 
possibilità, infatti basta utilizzare una funzione PHP messa a disposizione dal plugin <b>szgoogle_gplus_get_comments(\$options)</b>.</p>

<h2>Configurazione commenti</h2>

<p>Il sistema di commenti di google+ può essere inserito anche dal plugin, utilizzando
la posizione standard di wordpress e sostituendo il sistema di commenti standard. Nel menu di configurazione presente sul pannello di 
amministrazione chiamato <b>Google+</b> potete trovare una sezione <b>"Commenti"</b> con diverse opzioni che possono 
essere impostate secondo le vostre esigenze.</p>

<p>Ad esempio troverete la possibilità di attivare o disattivare l'automatismo, decidere se
sostituire il sistema standard o aggiungere quello di G+ ad esso, decidere la posizione dei commenti dopo il contenuto del
post o dopo il sistema standard e infine la possibilità di inserire una data dopo la quale il sistema di commenti deve
essere attivato. Quest'ultima opzione può essere utile se si vuole tenere un vecchio sistema di commenti
per i post passati e usare quello di google+ partendo da una data precisa.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th> <th>Descrizione</th>            <th>Valori ammessi</th>          <th>Default</th></tr>
	<tr><td>url</td>       <td>indirizzo URL completo</td> <td>stringa</td>                 <td>post corrente</td></tr>
	<tr><td>width</td>     <td>larghezza fissa</td>        <td>valore,auto</td>             <td>auto</td></tr>
	<tr><td>align</td>     <td>allineamento</td>           <td>left,center,right,none</td>  <td>none</td></tr>
</table>

<h2>Dimensione widget</h2>

<p>Il plugin <b>SZ-Google</b> può inserire il widget dei commenti con una dimensione fissa o utilizzare la tecnica del responsive
design adattandosi automaticamente alla dimensione del contenitore generale. Se volete una dimensione fissa basta che utilizzate il
valore width="larghezza", se invece specificate width="auto" il plugin utilizzerà il metodo responsive.</p>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-comments url="http://domain.com/post.html"]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'   => 'http://domain.com/post.html',
  'width' => 'auto',
  'align' => 'center',
);

if (function_exists('szgoogle_gplus_get_comments')) {
  echo szgoogle_gplus_get_comments(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ widget comments','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));