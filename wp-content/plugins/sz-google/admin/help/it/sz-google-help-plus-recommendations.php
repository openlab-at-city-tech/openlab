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

<p>Con questa feature è possibile inserire sul proprio sito web un widget che visualizza le raccomandazioni legate alle
pagine del vostro sito web in base alle iterazioni sociali. Questa funzionalità sarà visualizzata solo sulla versione
mobile del sito web e ignorata su device diversi. Per attivare questa opzione bisogna selezionare il campo specifico che
trovate del pannello di amministrazione ma dovete anche eseguire delle operazioni sulla pagina google+ collegata al vostro sito.</p>

<h2>Configurazione</h2>

<p>Nella sezione delle impostazioni della pagina G+ è possibile controllare il comportamento del widget che riguarda i consigli 
e la modalità di visualizzazione. Quindi per cambiare queste impostazioni non cercate le opzioni nel plugin ma usate la configurazione
della pagina direttamente su google plus. Per ulteriori informazioni leggete 
<a target="_blank" href="https://developers.google.com/+/web/recommendations/?hl=it">Content recommendations for mobile websites</a>.</p>

<p><b>Sono disponibili le seguenti opzioni dalla pagina delle impostazioni:</b></p>

<ul>
<li>Attivare o disattivare raccomandazioni.</li>
<li>Scegli pagine o percorsi che non dovrebbero mostrare raccomandazioni.</li>
<li>Scegliere le pagine o percorsi per impedire la visualizzazione nella barra di raccomandazioni.</li>
</ul>

<p><b>Scegliere quando visualizzare la barra raccomandazioni:</b></p>

<ul>
<li>Quando l'utente scorre in su.</li>
<li>Quando l'utente scorre ultimi un elemento con un ID specificato.</li>
<li>Quando l'utente scorre passato un elemento che corrisponda un selettore di query DOM.</li>
</ul>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, 
quindi prima di utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica 
risulti attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('google+ recommendations','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));