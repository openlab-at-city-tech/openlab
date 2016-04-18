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

<p>Anche Google+ come tutti gli altri social network mette a disposizione un bottone per seguire un profilo o una pagina. A differenza
degli altri social network però il bottone follow di G+ presenterà l'elenco delle nostre cerchie dove dovremmo selezionare quelle 
desiderate e inserire il profilo o la pagina da seguire. Tramite questo plugin il bottone può essere inserito in un sito web e fare 
riferimento a qualsiasi profilo o pagina presente su G+. É possibile anche inserire più bottoni sulla stessa pagina.</p>

<p>Per inserire questo bottone dovete usare lo shortcode <b>[sz-gplus-follow]</b>, se invece volete utilizzarlo
in una sidebar allora dovete utilizzare il widget sviluppato per questa funzione che trovate nel menu aspetto -> widgets. Per i più 
esigenti esiste anche un'altra possibilità, infatti basta utilizzare una funzione PHP messa a disposizione dal plugin 
<b>szgoogle_gplus_get_button_follow(\$options)</b>.</p>

<h2>Personalizzazione</h2>

<p>A prescindere dalla forma che utilizzerete, il componente potrà essere personalizzato in diverse maniere, basterà usare i parametri
messi a disposizione elencati nella tabella. Per quanto riguarda il widget i parametri vengono richiesti
direttamente dall'interfaccia grafica, mentre se utilizzate lo shortcode o la funzione PHP dovete specificarli manualmente nel 
formato opzione="valore".</p>

<h2>Parametri e opzioni</h2>

<table>
	<tr><th>Parametro</th>    <th>Descrizione</th>            <th>Valori ammessi</th>            <th>Default</th></tr>
	<tr><td>url</td>          <td>URL pagina o profilo</td>   <td>stringa,page,profile</td>                   <td>configurazione</td></tr>
	<tr><td>size</td>         <td>dimensione</td>             <td>small,medium,large</td>        <td>medium</td></tr>
	<tr><td>width</td>        <td>larghezza</td>              <td>valore</td>                    <td>null</td></tr>
	<tr><td>annotation</td>   <td>annotazione</td>            <td>inline,bubble,none</td>        <td>none</td></tr>
	<tr><td>float</td>        <td>float</td>                  <td>left,right,none</td>           <td>none</td></tr>
	<tr><td>align</td>        <td>allineamento</td>           <td>left,center,right,none</td>    <td>none</td></tr>
	<tr><td>rel</td>          <td>relazione</td>              <td>author,publisher,none</td>     <td>none</td></tr>
	<tr><td>text</td>         <td>testo</td>                  <td>stringa</td>                   <td>null</td></tr>
	<tr><td>img</td>          <td>immagine</td>               <td>stringa</td>                   <td>null</td></tr>
	<tr><td>position</td>     <td>posizione</td>              <td>top,center,bottom,outside</td> <td>outside</td></tr>
	<tr><td>margintop</td>    <td>margine alto</td>           <td>valore,none</td>               <td>none</td></tr>
	<tr><td>marginrigh</td>   <td>margine destro</td>         <td>valore,none</td>               <td>none</td></tr>
	<tr><td>marginbottom</td> <td>margine basso</td>          <td>valore,none</td>               <td>1</td></tr>
	<tr><td>marginleft</td>   <td>margine sinistro</td>       <td>valore,none</td>               <td>none</td></tr>
	<tr><td>marginunit</td>   <td>misura per margine</td>     <td>em,pt,px</td>                  <td>em</td></tr>
</table>


<h2>Contenitore bottone</h2>

<p>Il comportamento standard del bottone di google è quello di disegnare solo il bottone e collegare ad esso le azioni interattive 
permesse. Il plugin <b>SZ-Google</b> ha cercato di migliorare questo comportamento ed ha aggiunto dei parametri per permettere 
il disegno di un contenitore su cui il bottone può essere positionato. Ad esempio possiamo specificare un'immagine e posizionare il
bottone all'interno di essa in overlay e nella posizione che vogliamo. Qui di seguito un'esempio per chiarire:</p>

<pre>[sz-gplus-follow url="URL" img="http://dominio.com/image.jpg" position="bottom"/]</pre>

<h2>Esempio shortcode</h2>

<p>Gli shortcode sono delle macro che vengono inserite nei post per richiede alcune elaborazioni che sono state messe a 
disposizione dai plugin, dai temi o direttamente dal core. <b>SZ-Google</b> mette a disposizione diversi shortcode che possono esseri 
utilizzati nella forma classica e con delle opzioni di personalizzazione. Per inserire uno shortcode dobbiamo utilizzare il codice 
in questa forma:</p>

<pre>[sz-gplus-follow url="https://plus.google.com/+wpitalyplus" size="medium"/]</pre>

<h2>Esempio codice PHP</h2>

<p>Potete utilizzare le funzioni PHP messe a disposizione dal plugin in qualsiasi punto del vostro tema, basta preparate un array con le
opzioni desiderate e richiamare la funzione richiesta. É consigliabile utilizzare prima della funzione un controllo di esistenza,
in questa maniera non si riceveranno errori PHP nel caso in cui il plugin risulti disattivato o disinstallato.</p> 

<pre>
\$options = array(
  'url'        => 'https://plus.google.com/+wpitalyplus',
  'size'       => 'medium',
  'annotation' => 'bubble',
);

if (function_exists('szgoogle_gplus_get_button_follow')) {
  echo szgoogle_gplus_get_button_follow(\$options);
}
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ button follow','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));