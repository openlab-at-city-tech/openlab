<?php
    require_once explode("wp-content", __FILE__)[0] . "wp-load.php";
    if(!session_id()) {
        session_start();
    }

    $request_body = file_get_contents('php://input');
    $primaryResponse = json_decode( $request_body, true );
    global $wpdb;
    $paypal_settings = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
    $payment_terms = isset($paypal_settings['payment_terms']) ? $paypal_settings['payment_terms'] : 'lifetime';

    $user_id = get_current_user_id();

    $quiz_id = $primaryResponse['quizId'];
    $payment_type = $primaryResponse['paymentType'];
    $order_id = $primaryResponse['data']['orderID'];
    $payment_date = $primaryResponse['details']['create_time'];
    $order_full_name = $primaryResponse['details']['payer']['name']['given_name'] . " " . $primaryResponse['details']['payer']['name']['surname'];
    $order_email = $primaryResponse['details']['payer']['email_address'];
    $amount = $primaryResponse['details']['purchase_units'][0]['amount']['value'].$primaryResponse['details']['purchase_units'][0]['amount']['currency_code'];
    $options = array(
        'paypal_data' => $primaryResponse['data'],
        'paypal_details' => $primaryResponse['details'],
    );

    $order = array(
        'type' => 'paypal',
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

    if( $result >= 0 ) {
        switch($payment_terms){
            case "onetime":
                $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] = true;
                $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['status'] = 'created';
                $_SESSION['ays_quiz_paypal_purchased_item'][$quiz_id]['order_id'] = $wpdb->insert_id;
                $user_meta = true;
            break;
            case "lifetime":
                $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] = true;
                $current_usermeta = get_user_meta($user_id, "quiz_paypal_purchase");
                if($current_usermeta !== false && !empty($current_usermeta)){
                    foreach($current_usermeta as $key => $usermeta){
                        if($quiz_id == json_decode($usermeta, true)['quizId']){                   
                            $opts = json_encode(array(
                                'quizId' => $quiz_id,
                                'purchased' => true
                            ));
                            $user_meta = update_user_meta($user_id, 'quiz_paypal_purchase', $opts, $usermeta);
                            break;
                        }
                    }
                }
            break;
            case "subscribtion":
                $_SESSION['ays_quiz_paypal_purchase'][$quiz_id] = true;
                $current_usermeta = get_user_meta($user_id, "quiz_paypal_purchase");
                if($current_usermeta !== false && !empty($current_usermeta)){
                    foreach($current_usermeta as $key => $usermeta){
                        if($quiz_id == json_decode($usermeta, true)['quizId']){
                            $opts = json_encode(array(
                                'quizId' => $quiz_id,
                                'purchased' => true,
                                'purchaseDate' => current_time( 'Y-m-d H:i:s' ),
                            ));
                            $user_meta = update_user_meta($user_id, 'quiz_paypal_purchase', $opts, $usermeta);
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
