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

<p>Se per esigenze particolari non volete utilizzare i badge di g+ sul vostro sito, il plugin <b>SZ-Google</b> mette a disposizione 
un metodo alternativo. Infatti basta attivare le opzioni che trovate nel pannello di amministrazione chiamate HEAD Author e HEAD Publisher.</p>

<h2>Codice in HEAD</h2>

<p>Il codice aggiunto dal plugin sarà simile all'esempio qui di seguito riportato, gli id del profilo e della pagina verrano presi dalla
configurazione generale del modulo google+ presente nel pannello di amministrazione. L'unica cosa che dovete tenere conto è che mentre
per il <b>publisher</b> non ci sono problemi a definirlo a livello globale per l'<b>autore</b> va bene sono se il blog è mono autore, se per caso
sul sito web in questione dovessero scrivere autori diversi non attivate la funzione HEAD Author.

<pre>
&lt;head&gt;
  &lt;link rel="author" href="https://plus.google.com/106189723444098348646"/&gt;
  &lt;link rel="publisher" href="https://plus.google.com/116899029375914044550"/&gt;
&lt;/head&gt;
</pre>

<h2>Funzioni PHP</h2>

<table>
	<tr><td>szgoogle_gplus_get_contact_page()</td><td>Reperimento del campo profilo per google+ pagina.</td></tr>
	<tr><td>szgoogle_gplus_get_contact_community()</td><td>Reperimento del campo profilo per google+ community</td></tr>
	<tr><td>szgoogle_gplus_get_contact_betspost()</td><td>Reperimento del campo profilo per google+ best post.</td></tr>
</table>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
echo '&lt;div id="author"&gt;';

echo '&lt;div class="image"&gt;';
echo '&lt;img src="http://domain.com/image.jpg" alt="author"/&gt;';
echo '&lt;/div&gt;';'

if (function_exists('szgoogle_gplus_get_contact_page')) {
  echo '&lt;div class="link"&gt;';
  echo '&lt;a href="'.szgoogle_gplus_get_contact_page().'"&gt;My G+ Page&lt;/a&gt;';
  echo '&lt;/div&gt;';'
} 

echo '&lt;/div&gt;';
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ author & publisher','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));