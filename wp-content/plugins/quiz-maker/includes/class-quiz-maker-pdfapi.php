<?php

class Quiz_PDF_API{

    public function generate_PDF($data){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ays-pro.com/pdfapi/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response,true);
            if($response["code"] == 1 && $response['msg'] == "Success"){
                $fileContent = base64_decode($response["data"]);
                $fileName = AYS_QUIZ_DIR . 'public/certificate.pdf';
                file_put_contents($fileName, $fileContent);
                return true;
            }else{
                return false;
            }
        }
    }
}
