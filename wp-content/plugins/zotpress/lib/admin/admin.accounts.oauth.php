<?php

/** 
* Code provided by and adapted from: http://www.zotero.org/support/dev/server_api/v2/oauth
* Note that this example uses the php OAuth extension http://php.net/manual/en/book.oauth.php
* but there are various php libraries that provide similar functionality.
* OAuth acts over multiple pages, so we save variables we need to remember in $state in a temp file
*
* The OAuth handshake has 3 steps:
* 1: Make a request to the provider to get a temporary token
* 2: Redirect user to provider with a reference to the temporary token. The provider will ask them to authorize it
* 3: When the user is sent back by the provider and the temporary token is authorized, exchange it for a permanent
*    token then save the permanent token for use in all future requests on behalf of this user.
*
* So an OAuth consumer needs to deal with 3 states which this example covers:
* State 0: We need to start a fresh OAuth handshake for a user to authorize us to get their information.
*         We get a request token from the provider and send the user off to authorize it
* State 1: The provider just sent the user back after they authorized the request token
*         We use the request token + secret we stored for this user and the verifier the provider just sent back to
*         exchange the request token for an access token.
* State 2: We have an access token stored for this user from a past handshake, so we use that to make data requests
*         to the provider.
**/


// KS: Let's not use raw GET values
$oauth_user = false;

if ( isset($_GET['oauth_user'])
        && preg_match("/^[a-zA-Z]{1,25}$/", $_GET['oauth_user']) !== 1 )
{
    $oauth_user = $_GET['oauth_user'];
}
else // not valid
{
    exit("One or more matters related to authentication are incorrect.");
}


// KS: Load in WP basics
require(dirname(__FILE__) . '/../../../../../wp-load.php');
$wp_did_header = false;
wp();
global $wpdb;


//initialize some variables to start with.
//clientkey, clientSecret, and callbackurl should correspond to http://www.zotero.org/oauth/apps
$clientKey = 'f8daeb1c6ec190ef3db1';
$clientSecret = 'a52d8706611b7b612e83';
$callbackUrl = get_bloginfo('url') . '/wp-content/plugins/zotpress/lib/admin/admin.accounts.oauth.php?oauth_user='.$oauth_user.'&callingback=true';

//the endpoints are specific to the OAuth provider, in this case Zotero
$request_token_endpoint = 'https://www.zotero.org/oauth/request';
$access_token_endpoint = 'https://www.zotero.org/oauth/access';
$zotero_authorize_endpoint = 'https://www.zotero.org/oauth/authorize';


// KS: Moved up, otherwise tries to process without OAuth installed
//Make sure we have OAuth installed depending on what library you're using
if(!class_exists('OAuth')){
    die("Class OAuth does not exist. Make sure PHP OAuth extension is installed and enabled.");
}

//Functions to save state to temp file between requests, DB should replace this functionality
function read_state(){
    global $wpdb;
    $oa_cache = $wpdb->get_results("SELECT cache FROM ".$wpdb->prefix."zotpress_oauth");
    return unserialize( $oa_cache[0]->cache );
}
function write_state($state)
{
    global $wpdb;
    $oa_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_oauth");
    $query = "UPDATE ".$wpdb->prefix."zotpress_oauth ";
    $query .= "SET cache='".serialize($state)."' WHERE id='".$oa_cache[0]->id."';";
    $wpdb->query($query);
}
function save_request_token($request_token_info, $state){
    // Make sure the request token has all the information we need
    if(isset($request_token_info['oauth_token']) && isset($request_token_info['oauth_token_secret'])){
        // save the request token for when the user comes back
        $state['request_token_info'] = $request_token_info;
        $state['oauthState'] = 1;
        write_state($state);
    }
    else{
        die("Request token did not return all the information we need.");
    }
}
function get_request_token($state){

    $oauth_token = false;

    if ( isset($_GET['oauth_token'])
            && preg_match("/^[a-zA-Z0-9]{1,50}$/", $_GET['oauth_token']) === 1 ) {
        $oauth_token = $_GET['oauth_token'];
    }

    if( $oauth_token != $state['request_token_info']['oauth_token']){
        // KS: Hiding; just for dev
        // echo $oauth_token;
        // echo "<br><br>\n";
        // echo $state['request_token_info']['oauth_token'];
        // echo "<br><br>\n";
        die("Could not find referenced OAuth request token");
    }
    else{
        return $state['request_token_info'];
    }
}
function save_access_token($access_token_info, $state){

    if(!isset($access_token_info['oauth_token']) 
            || !isset($access_token_info['oauth_token_secret'])){
        //Something went wrong with the access token request and we didn't get the information we need
        throw new Exception("OAuth access token did not contain expected information");
    }
    //we got the access token, so save it for future use
    $state['oauthState'] = 2;
    $state['access_token_info'] = $access_token_info;
    write_state($state); //save the access token for all subsequent resquests, in Zotero's case the token and secret are just the same Zotero API key
}
function get_access_token($state){
    if(empty($state['access_token_info'])){
        die("Could not retrieve access token from storage.");
    }
    return $state['access_token_info'];
}


//Initialize our environment
//check if there is a transaction in progress
//for testing purpose, start with a fresh state to perform a new handshake
if(empty($_GET['reset'])){
    $state = read_state();
}
else{
    $state = array();
    $state['localUser'] = htmlentities( urlencode( $oauth_user ) );
    $state['oauthState'] = 0; //we do not have an oauth transaction in process yet
    write_state($state);
}
// If we are in state=1 there should be an oauth_token, if not go back to 0
if($state['oauthState'] == 1 && !isset($_GET['oauth_token'])){
    $state['oauthState'] = 0;
}


//set up a new OAuth object initialized with client credentials and methods accepted by the provider
$oauth = new OAuth($clientKey, $clientSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_FORM);
//$oauth->enableDebug(); //get feedback if something goes wrong. Should not be used in production

//Handle different parts of the OAuth handshake depending on what state we're in
switch($state['oauthState'])
{
case 0:
    // State 0 - Get request token from Zotero and redirect user to Zotero to authorize
    $oauth->disableSSLChecks();
    try{
        $request_token_info = $oauth->getRequestToken($request_token_endpoint, $callbackUrl);
    }
    catch(OAuthException $E){
        echo "Problem getting request token<br />";
        echo $E->lastResponse; echo "<br />";
        die;
    }
    save_request_token($request_token_info, $state);

    // Send the user off to the provider to authorize your request token (could also be a link the user follows)
    $redirectUrl = "{$zotero_authorize_endpoint}?oauth_token={$request_token_info['oauth_token']}";
    wp_redirect($redirectUrl, 301);

    break;

case 1:
    // State 1 - Handle callback from Zotero and get and store an access token
    // Make sure the token we got sent back matches the one we have
    // In practice we would look up the stored token and whatever local user information we have tied to it
    
    $oauth_token = false;

    if ( isset($_GET['oauth_token'])
            && preg_match("/^[a-zA-Z0-9]{1,50}$/", $_GET['oauth_token']) === 1 ) {
        $oauth_token = $_GET['oauth_token'];
    }

    $oauth->disableSSLChecks();
    $request_token_info = get_request_token($state);
    //if we found the temp token, try to exchange it for a permanent one
    try{
        //set the token we got back from the provider and the secret we saved previously for the exchange.
        $oauth->setToken($oauth_token, $request_token_info['oauth_token_secret']);
        //make the exchange request to the provider's given endpoint
        $access_token_info = $oauth->getAccessToken($access_token_endpoint);
        save_access_token($access_token_info, $state);
    }
    catch(Exception $e){
        //Handle error getting access token
        die("Caught exception on access token request");
    }
    // Continue on to authorized state outside switch
    break;

case 2:
    //get previously stored access token if we didn't just get it from a handshack
    $access_token_info = get_access_token($state);
    break;
}

if ( isset( $access_token_info ) )
{
    // ADD PRIVATE KEY TO THE USER'S ACCOUNT IN ZOTPRESS
    global $wpdb;
    $query = "UPDATE ".$wpdb->prefix."zotpress ";
    $query .= "SET public_key='".$access_token_info['oauth_token_secret']."' WHERE api_user_id='".$oauth_user."';";
    $wpdb->query($query);

    // EMPTY THE CACHE
    $oa_cache = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress_oauth");
    $query = "UPDATE ".$wpdb->prefix."zotpress_oauth ";
    $query .= "SET cache='empty' WHERE id='".$oa_cache[0]->id."';";
    $wpdb->query($query);

    // KS: Redirect
    header("Location: ".admin_url("/admin.php?page=Zotpress&accounts=true"));
    die();
}


?>
