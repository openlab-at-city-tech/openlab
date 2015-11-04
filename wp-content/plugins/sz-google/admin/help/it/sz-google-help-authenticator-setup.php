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

<p>Il plugin <b>SZ-Google</b> mette a disposizione il processo di autorizzazione a due fasi progettato da google authenticator, infatti
è possibile rafforzare la sicurezza del nostro pannello di login chiedendo un codice a tempo oltre alle credenziali normali. Questa
operazione è resa possibile grazie all'applicazione di Google Authenticator che è possibile installare sul nostro smartphone sia che 
questo sia un iphone, un android o un blackberry. Come vedremo in seguito la configurazione e la sincronizzazione della chiave verrà 
eseguita in maniera veloce e semplice utilizzando un codice QR Code da visualizzare sul proprio device.</p>

<h2>Configurazione</h2>

<p>Per prima cosa dovete attivare il modulo di Google Authenticator dal pannello di amministrazione che riguarda il plugin, una volta
attivato controllate che nella schermata di configurazione del modulo sia attiva la funzione di <b>"attivazione login"</b>. A questo punto
nella pagina del profilo utente verranno aggiunte delle informazioni per attivare l'autenticazione con chiave a tempo.</p>

<p>Quindi collegatevi con il vostro account e andate sulla pagina del vostro profilo, attivate la funzione di Google Authenticator, generate con
il pulsante apposito un nuovo "<b>codice segreto</b>" e visualizzate il codice <b>QR Code</b>, una volta visualizzato aggiungete un 
nuovo account sulla vostra applicazione mobile, se questa operazione termina correttamente aggiornate il profilo. Il fatto di 
aggiornare il profilo solo dopo la configurazione dello smartphone è dettato solo dal fatto che se aggiornate prima il profilo e
qualcosa va male sulla sincronizzazione del telefono dopo avrete problemi di login che devono essere risolti dall'amministratore.

<h2>File di emergenza</h2>

<p>Attivando questa opzione presente nel pannello di amministrazione del plugin è possibile disattivare il controllo del codice a tempo
eseguendo un FTP di un determinato file nella directory principale di wordpress. Questa funzione può essere utile nel caso qualche utente
abbia grossi problemi di collegamento o l'amministratore non abbia più accesso al suo device. In questo caso il proprietario del sito
potrebbe fare un FTP di un file vuoto ad esempio chiamato <b>google-authenticator-disable.php</b> nella directory root e sospendere
temporaneamente il controllo del codice, questo perchè ovviamente anche lui potrebbe non riuscire a fare il login.</p>

<h2>Codici di emergenza</h2>

<p>Abbiamo già visto che è possibile attivare un file di emergenza per la forzatura di un login nel caso in cui non si possa 
ottenere la password a tempo. In realtà esiste un’altra maniera che ci permette di associare ad un profilo dei codici di emergenza che 
possono essere utilizzati al posto del codice a tempo. Come lo stesso meccanismo che mette a disposizione google per i propri account.</p>

<p>La creazione dei codici di emergenza è possibile eseguirla dal menu del profilo, nella stessa maniera del codice 
segreto. Tutti i codici che verranno generati potranno essere usati una sola volta, infatti i codici di color rosso che trovate nella 
tabella sono quelli già utilizzati e gli altri sono quelli ancora liberi. In ogni caso potete generare una nuova tabelle di 12 nuovi codici quando volete.</p>

<h2>Avvertenze</h2>

<p>Il plugin <b>SZ-Google</b> è stato sviluppato con una tecnica di caricamento moduli singoli per ottimizzare le performance generali, 
quindi prima di utilizzare uno shortcode, un widget o una funzione PHP bisogna controllare che il modulo generale e l'opzione specifica 
risulti attivata tramite il campo opzione dedicato che trovate nel pannello di amministrazione.</p>

EOD;

// Call function for creating the page of standard
// documentation based on the contents of the HTML variable

$this->moduleCommonFormHelp(__('authenticator setup','sz-google'),NULL,NULL,false,$HTML,basename(__FILE__));