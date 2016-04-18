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

<p>Il plugin <b>SZ-Google</b> mette a disposizione delle funzioni per inserire automaticamente il codice del selettore di lingua
nel proprio tema, però se per qualche esigenza particolare volete utilizzare l'inserimento del codice manualmente sul vostro sito 
ma continuando ad utilizzare il pannello di amministrazione per i parametri relativi all'account, potete utilizzare le funzioni 
PHP messe a disposizione dal plugin e implementarle con il vostro codice. Le funzioni messe a disposizione sono le seguenti:</p>

<ul>
<li>szgoogle_translate_get_code()</li>
<li>szgoogle_translate_get_meta()</li>
<li>szgoogle_translate_get_meta_ID()</li>
</ul>

<p>Ad esempio se volessimo inserire il codice nel nostro tema e prendere solo le opzioni che riguardano l'account potremmo utilizzare
un codice PHP simile al seguente dove viene utilizzata la funzione <b>szgoogle_translate_get_meta_ID()</b>.</p>
<pre>
&lt;meta name="google-translate-customization" content="&lt;?php echo szgoogle_translate_get_meta_ID() ?&gt"/&gt;
</pre>

<p>Se invece volessimo inserire il codice generato automaticamente dal plugin ma in una posizione ben definita del nostro tema possiamo
utilizzare la funzione PHP <b>szgoogle_translate_get_meta()</b> e inserirla nel punto preciso che desideriamo.</p>

<pre>
&lt;head&gt;
  if (function_exists('szgoogle_translate_get_meta')) {
    echo szgoogle_translate_get_meta();
  }
&lt;/head&gt;
</pre>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, 
quindi prima di utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica 
risulti attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('translate PHP functions','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));