<?php
header('Content-Type: application/json');

class Stripe {
    public $headers;
    public $url = 'https://api.stripe.com/v1/';
    public $method = null;
    public $fields = array();

    function __construct () {
        $this->headers = array(
          'Content-Type: application/json'
        );
    }

    function call () {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url, //"https://api.stripe.com/v1/payment_intents?amount=1099&currency=usd&payment_method_types[]=card&capture_method=manual",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->fields,
            CURLOPT_HTTPHEADER => $this->headers,
        ));

        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true); // return php array with api response
    }
}

function calculateAmount( $amount, $currency ) {

    // Replace this constant with a calculation of the order's amount
    // Calculate the order total on the server to prevent
    // customers from directly manipulating the amount on the client

    $multipling = '1';
    $fixed = 2;
    if( $currency == 'jpy' ){
        $fixed = 0;
    }

    for( $i = 0; $i < $fixed; $i++ ){
        $multipling .= '0';
    }

    $amount = floatval( $amount ) * intval( $multipling );

    return $amount;
}

try {
    // retrieve JSON from POST body
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str);

    // create customer subscription with credit card and plan
    $s = new Stripe();

    $options = isset( $json_obj->stripeOptions ) && $json_obj->stripeOptions != '' ? $json_obj->stripeOptions : (object)array();

    $secret_key = isset( $json_obj->secretKey ) && $json_obj->secretKey != '' ? $json_obj->secretKey : '';

    $s->headers[] = 'Authorization: Bearer ' . $secret_key;
    $s->headers[] = 'Content-Type: application/json';

    $currency = isset( $options->currency ) && $options->currency != '' ? $options->currency : '';
    $amount = isset( $options->amount ) && $options->amount != '' ? floatval( $options->amount ) : 0;
    $amount = calculateAmount( $amount, $currency );

    if( $amount > 1 ){
        $s->fields = array();
        $s->fields['amount'] = $amount;
        $s->fields['currency'] = $currency;

        $s->method = "POST";

        $s->url .= 'payment_intents' . '?' . http_build_query($s->fields);

        $s->fields = "";

        $subscription = $s->call();

        $output = array(
            'clientSecret' => $subscription['client_secret'],
        );
    }else{
        $output = array(
            'clientSecret' => '',
        );
    }

    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(array( 'error' => $e->getMessage() ));
}
