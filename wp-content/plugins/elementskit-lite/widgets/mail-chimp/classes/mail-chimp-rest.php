<?php
if(!function_exists('ekit_mail_chimp_rest')){

	function ekit_mail_chimp_rest(WP_REST_Request $request ){
		$return = ['success' => [], 'error' => [] ];
		
		$token 		= $request['token'];
		$list  		= $request['list'];
		$email  	= $request['email'];
	    $firstname  = $request['firstname'];
	    $lastname  	= $request['lastname'];
	    $phone  	= $request['phone'];
		
		$url = 'https://us20.api.mailchimp.com/3.0/lists/'.$list.'/members/';
		
		$margeFiled = [];
		if(strlen($firstname) > 1):
			$margeFiled['FNAME'] = $firstname;
		endif;
		if(strlen($lastname) > 1):
			$margeFiled['LNAME'] = $lastname;
		endif;
		if(strlen($phone) > 1):
			$margeFiled['PHONE'] = $phone;
		endif;
		
		$postData = [];
		$postData['email_address'] = $email;
		$postData['status'] =  'subscribed';
		if(sizeof($margeFiled) > 0):
			$postData['merge_fields'] =  $margeFiled;
		endif;
		$postData['status_if_new'] =  'subscribed';
		
		$response = wp_remote_post( $url, [
			'method' => 'POST',
			'data_format' => 'body',
			'timeout' => 45,
			'headers' => [
							
							'Authorization' => 'apikey '.$token,
							'Content-Type' => 'application/json; charset=utf-8'
					],
			'body' => wp_json_encode($postData	)
			]
		);
		
		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
			$return['error'] = "Something went wrong: $error_message";
		} else {
			$return['success'] = $response;
		}
		
		return $return;
	}
}

/**
 * wp rest api add action
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'elementskit-lite', '/mailchimp/',
		array(
			'methods' => 'GET',
			'callback' => 'ekit_mail_chimp_rest',
			'permission_callback' => '__return_true',
		)
	);
});