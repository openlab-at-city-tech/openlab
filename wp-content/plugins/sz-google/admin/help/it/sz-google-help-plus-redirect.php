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

<p>Il formato degli URL utilizzato da Google+ per identificare le sue pagine non è sicuramente un friendly url, infatti vengono
utilizzati degli id numerici molto lunghi che rendono la stringa URL impossibile da ricordare o memorizzare. Per questo motivo
Google+ ha messo a disposizione per i profili e le pagine un URL personalizzato da associare al proprio profilo o pagina.</p> 

<p>Purtroppo il sistema adottato non è sempre efficace, infatti specialmente nelle pagine vengono richiesti dei caratteri aggiuntivi che molti
siti web non apprezzano perchè non coerenti con il proprio nome originale. Ad esempio un'azienda che si chiama <b>skydrive</b> non
accetta volentieri di stampare su un materiale pubblicitario un'indirizzo tipo <b>https://plus.google.com/+skydrive9876</b>.</p>

<h2>Redirect da dominio</h2>

<p>Il plugin <b>SZ-Google</b> mette a disposizione un feature di redirect dal nome del proprio dominio per risolvere questo problema, 
infatti se ad esempio il plugin è installato sul sito che abbiamo preso come esempio <b>skydrive.com</b> è possibile creare un URL 
personalizzato del tipo <b>https://skydrive.com/+</b> che vi porterà direttamente sulla pagina di google+, sicuramente una forma più 
elegante e personale che può essere utilizzata senza problemi su materiale pubblicitario o gadgets vari.</p>

<pre>
Google+ URL ==> https://plus.google.com/123456789012345
Google+ URL ==> https://plus.google.com/+skydrive9876
Plugin+ URL ==> https://skydrive.com/+
</pre>

<p>Nella sezione di configurazione Google+ Redirect presente nel pannello di amministrazione troverete anche la possibilità di identificare
un redirect per la stringa <b>/plus</b> e per una di vostra scelta. Ad esempio se avete una community collegata alla vostra pagina potreste
utilizzare la stringa <b>community/+</b> per un redirect diretto sulla community di google+.</p>

<pre>
Plugin+ URL ==> https://skydrive.com/+
Plugin+ URL ==> https://skydrive.com/plus
Plugin+ URL ==> https://skydrive.com/community/+
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, quindi prima di 
utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica risulti 
attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ redirect','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));