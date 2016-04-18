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

<p>Per utilizzare questa funzionalità bisogna installare sul proprio smartphone l'applicazione messa a disposizione da google in
maniera gratuita sia in ambiente IOS che Android e Blackberry. Quindi per prima cosa scaricate l'applicazione e installatela sul
vostro smartphone, come prima operazione eseguite l'operazione "configura account" e selezionate la modalità che viene indicata 
come "leggi codice a barre", a questo punto configurate il vostro profilo su wordpress e inquadrate il QR Code generato, se 
tutto è andato bene vedrete nel vostro smartphone un nuovo account con il codice a tempo in primo piano.</p>

<h2>Risorse e documentazione</h2>

<ul>
<li><a target="_blank" href="https://itunes.apple.com/it/app/google-authenticator/id388497605?mt=8">Google Authenticator su Apple Store</a></li>
<li><a target="_blank" href="https://support.google.com/accounts/answer/1066447">Google Authenticator su Blackberry</a></li>
<li><a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Google Authenticator su Google Play</a></li>
</ul>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, 
quindi prima di utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica 
risulti attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('authenticator device','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));