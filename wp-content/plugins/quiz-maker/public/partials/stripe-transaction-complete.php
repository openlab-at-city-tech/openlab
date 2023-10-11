<?php
    require_once explode("wp-content", __FILE__)[0] . "wp-load.php";

    if(!session_id()) {
        session_start();
    }

    function calculateAmount( $amount, $currency ) {

        // Replace this constant with a calculation of the order's amount
        // Calculate the order total on the server to prevent
        // customers from directly manipulating the amount on the client

        $dividing = '1';
        $fixed = 2;
        if( $currency == 'jpy' ){
            $fixed = 0;
        }

        for( $i = 0; $i < $fixed; $i++ ){
            $dividing .= '0';
        }

        $amount = floatval( $amount ) / intval( $dividing );

        return $amount;
    }

    $request_body = file_get_contents('php://input');
    $primaryResponse = json_decode( $request_body, true );
    global $wpdb;

    $user_id = get_current_user_id();

    $quiz_id = isset( $primaryResponse['quizId'] ) ? $primaryResponse['quizId'] : 0;
    $payment_type = $primaryResponse['paymentType'];
    $payment_terms = isset( $primaryResponse['paymentTerms'] ) ? $primaryResponse['paymentTerms'] : 'lifetime';
    $order_id = isset( $primaryResponse['data']['id'] ) ? $primaryResponse['data']['id'] : '';
    $payment_date = isset( $primaryResponse['data']['created'] ) ? current_time( 'Y-m-d H:i:s', $primaryResponse['data']['created'] ) : current_time( 'mysql' );
    $order_full_name = ''; //$primaryResponse['details']['payer']['name']['given_name'] . " " . $primaryResponse['details']['payer']['name']['surname'];
    $order_email = ''; //$primaryResponse['details']['payer']['email_address'];
    $order_amount = isset( $primaryResponse['data']['amount'] ) ? floatval( $primaryResponse['data']['amount'] ) : 0; //$primaryResponse['details']['payer']['email_address'];
    $order_currency = isset( $primaryResponse['data']['currency'] ) ? $primaryResponse['data']['currency'] : ''; //$primaryResponse['details']['payer']['email_address'];
    $amount = calculateAmount( $order_amount, $order_currency );
    $amount = $amount . strtoupper( $order_currency );
    $options = array(
        'stripe_data' => $primaryResponse['data'],
    );
    $order = array(
        'type' => 'stripe',
        'order_id' => $order_id,
        'quiz_id' => $quiz_id,
        'user_id' => $user_id,
        'order_full_name' => $order_full_name,
        'order_email' => $order_email,
        'payment_date' => $payment_date,
        'payment_type' => $payment_type,
        'amount' => $amount,
        'status' => 'created',
        'options' => json_encode( $options )
    );
    $result = $wpdb->insert(
        $wpdb->prefix . "aysquiz_orders",
        $order,
        array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );
    if( $result >= 0  ) {
        switch($payment_terms){
            case "onetime":
                $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] = true;
                $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] = 'created';
                $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['order_id'] = $wpdb->insert_id;
                $user_meta = true;
            break;
            case "lifetime":
                $_SESSION['ays_quiz_stripe_purchase'][$quiz_id] = true;
                $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['status'] = 'created';
                $_SESSION['ays_quiz_stripe_purchased_item'][$quiz_id]['order_id'] = $wpdb->insert_id;
                $current_usermeta = get_user_meta($user_id, "quiz_stripe_purchase");
                if($current_usermeta !== false && !empty($current_usermeta)){
                    foreach($current_usermeta as $key => $usermeta){
                        if($quiz_id == json_decode($usermeta, true)['quizId']){
                            $opts = json_encode(array(
                                'quizId' => $quiz_id,
                                'purchased' => true
                            ));
                            $user_meta = update_user_meta($user_id, 'quiz_stripe_purchase', $opts, $usermeta);
                            break;
                        }
                    }
                }
            break;
        }
    }else{
        $user_meta = false;
    }
    if($user_meta){
        echo json_encode(array(
            'status' => true,
        ));
    }else{
        echo json_encode(array(
            'status' => false
        ));
    }
    die();
?>
