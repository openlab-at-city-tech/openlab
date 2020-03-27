<?php

class TidioOneApi {

    private $publicKey;
    private $apiUrl = 'https://api.tidio.co/';

    public function __construct($publicKey) {
        $this->publicKey = $publicKey;
    }

    public function request($action = 'track', $data = array()) {
        return $this->getContentData($this->apiUrl . $action, $data);
    }

    private function getContentData($url, $data = array()) {
						
		if(!function_exists('json_encode') || !function_exists('file_get_contents')){
			return false;
		}

        $ch = curl_init();
		
		//
		
		$data['projectPublicKey'] = $this->publicKey;
		
        $url = $url . '?data=' . base64_encode(json_encode($data));
		
		//
								
        $response = file_get_contents($url);
		
        return $response;
    }

}
