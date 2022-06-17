<?php

namespace ElementsKit_Lite;

use \Elementor\ElementsKit_Widget_Mail_Chimp_Handler;

class ElementsKit_Widget_Mail_Chimp_Api extends Core\Handler_Api {

	public function config(){
        $this->prefix = 'widget/mailchimp';
    }

    public function get_sendmail(){

        $return = ['success' => [], 'error' => [] ];
		$dataApi 	= ElementsKit_Widget_Mail_Chimp_Handler::get_data();

		$token 		= isset($dataApi['token']) ? $dataApi['token'] : '';
		$listed 	=  $this->request['listed'];
		
		$email  	= $this->request['email'];
	    $firstname  = $this->request['firstname'];
	    $lastname  	= $this->request['lastname'];
	    $phone  	= $this->request['phone'];

		$data = [
			'email_address' => (($email != '') ? $email : ''),
			'status_if_new' => 'subscribed',
			'merge_fields' => [
				'FNAME' => (($firstname != '') ? $firstname : ''),
				'LNAME' => (($lastname != '') ? $lastname : ''),
				'PHONE' => (($phone != '') ? $phone : ''),
			],
		];
		
		if(!empty($this->request['double_opt_in']) && $this->request['double_opt_in'] === 'yes') {
			$data['status'] = 'pending';
		} else {
			$data['status'] = 'subscribed';
		}

		$server = explode('-', $token);
		if( !is_array($server) || empty($token) || !isset($server[1]) ){
			$return['error'] = esc_html__( 'Please set API Key into Dashboard User Data. ', 'elementskit-lite' );
			return $return;
		}
		
		$url = 'https://'.$server[1].'.api.mailchimp.com/3.0/lists/'.$listed.'/members/';

		$response = wp_remote_post( $url, [
			'method' => 'POST',
			'data_format' => 'body',
			'timeout' => 45,
			'headers' => [
							
							'Authorization' => 'apikey '.$token,
							'Content-Type' => 'application/json; charset=utf-8'
					],
			'body' => json_encode($data	)
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
//https://us20.api.mailchimp.com/3.0/lists?apikey=24550c8cb06076781d51a80274a52878-us20
