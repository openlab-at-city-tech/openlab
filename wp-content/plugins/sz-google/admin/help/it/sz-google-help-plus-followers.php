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

<p>Tramite questa feature è possibile inserire in una pagina un badge contenente la lista dei followers collegati ad una pagina
o ad un profilo presente su g+. Nel badge verranno visualizzate le miniature dei profili che seguono la risorsa su google+ e viene
anche inserito un bottone per aggiungere la pagina o il profilo direttamente ad una cerchia. In questo momento il badge rilasciato
da google non è responsive, però il plugin <b>SZ-Google</b> aggiunge un parametro di width="auto" che tramite javascript 
cercherà di calcolare la larghezza e passarla al codice di google+. Ovviamente non funzionerà in caso di
ridimensionamento finestra.</p>

<p>Per inserire il badge dovete usare lo shortcode <b>[sz-gplus-followers]</b>, se invece desiderate utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, basta utilizzare una funzione PHP messa a disposizione dal plugin 
<b>szgoogle_gplus_get_badge_followers(\$options)</b>.</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th> <th>Descrizione</th>      <th>Valori ammessi</th>         <th>Default</th></tr>
	<tr><td>id</td>        <td>pagina o profilo</td> <td>stringa</td>                <td>configurazione</td></tr>
	<tr><td>align</td>     <td>allineamento</td>     <td>left,center,right,none</td> <td>none</td></tr>
	<tr><td>width</td>     <td>larghezza</td>        <td>valore,auto</td>            <td>configurazione</td></tr>
	<tr><td>height</td>    <td>altezza</td>          <td>valore,auto</td>            <td>configurazione</td></tr>
</table>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-followers url="https://plus.google.com/+wpitalyplus"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'    => 'https://plus.google.com/+wpitalyplus',
  'width'  => 'auto',
  'height' => 'auto',
);

if (function_exists('szgoogle_gplus_get_badge_followers')) {
  echo szgoogle_gplus_get_badge_followers(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ badge followers','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));