<?php

class Quiz_PDF_API {

    public function generate_PDF($data){
        $curl = curl_init();

        $url = "https://ays-pro.com/pdfapi/";
        $url = "https://poll-plugin.com/pdfapi/";
//        $url = "https://tt-soft.com/pdfapi/";
        //$url = "http://localhost/pdfapi/";

        $api_url = apply_filters( 'ays_quiz_pdfapi_api_url', $url );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_SSL_VERIFYHOST => false,
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
                // $fileContent = base64_decode($response["data"]);
                // $fileName = AYS_QUIZ_DIR . 'public/certificate.pdf';

                $date = current_time('Y-m-d-H-i-s');
                $fileContent = base64_decode($response["data"]);
                $fileName = AYS_QUIZ_DIR . 'public/certificate-'.$date.'.pdf';
                $fileUrl = AYS_QUIZ_PUBLIC_URL . '/certificate-'.$date.'.pdf';
                $cert_path = AYS_QUIZ_PUBLIC_PATH . '/certificate-'.$date.'.pdf';
                $files = preg_grep( '~^certificate.*\.pdf$~', scandir( AYS_QUIZ_DIR . "public/" ) );
                foreach( $files as $file ){
                    unlink( AYS_QUIZ_DIR . "public/" . $file );
                    //if ( !in_array( $file, array( ".", ".." ) ) ){
                    //}
                }

                file_put_contents($fileName, $fileContent);
                $result = array(
                    'status' => true,
                    'cert_url' => $fileUrl,
                    'cert_path' => $cert_path,
                );
                if(is_dir(AYS_QUIZ_CERTIFICATES_SAVE_PATH)){
                    $quiz_id = (isset($data['cert_quiz_id']) && $data['cert_quiz_id'] != null) ? intval($data['cert_quiz_id']) : 0;
                    $user_name = (isset($data['cert_data']['user_name']) && $data['cert_data']['user_name'] != '') ? $data['cert_data']['user_name'] : __('Guest', AYS_QUIZ_NAME);
                    $unique_code = (isset($data['cert_data']['unique_code']) && $data['cert_data']['unique_code'] != '') ? $data['cert_data']['unique_code'] : uniqid();
                    $current_date = (isset($data['current_date']) && Quiz_Maker_Admin::validateDate($data['current_date'])) ? date('Y-m-d-H-i-s', strtotime($data['current_date'])) : current_time('Y-m-d-H-i-s');

                    $cert_filename = '';
                    if($user_name != ''){
                        $user_name = str_replace(' ', '-', $user_name);
                        $cert_filename .= str_replace('"', "'", $user_name);
                    }
                    if($quiz_id !== 0){
                        if($cert_filename != ''){
                            $cert_filename .= '-';
                        }
                        $cert_filename .= $quiz_id;
                    }

                    if($cert_filename != ''){
                        $cert_filename .= '-';
                    }

                    $cert_filename .= $unique_code;

                    if($cert_filename != ''){
                        $cert_filename .= '-'. $current_date . '.pdf';
                    }else{
                        $cert_filename .= $current_date . '.pdf';
                    }
                    $cert_file_path = AYS_QUIZ_CERTIFICATES_SAVE_PATH .'/'.$cert_filename;
                    $cert_file_url = AYS_QUIZ_CERTIFICATES_SAVE_URL .'/'.$cert_filename;
                    $result['cert_file_name'] = $cert_filename;
                    $result['cert_file_path'] = $cert_file_path;
                    $result['cert_file_url'] = $cert_file_url;
                    file_put_contents($cert_file_path, $fileContent);
                }
                return $result;
            }else{
                $result = array(
                    'status' => false,
                );
                return $result;
            }
        }
    }

    public function generate_report_PDF($data){
        $curl = curl_init();

        // $url = "https://ays-pro.com/pdfapi/export-report/";
        $url = "https://poll-plugin.com/pdfapi/export-report/";
//        $url = "https://tt-soft.com/pdfapi/export-report/";
//        $url = "http://localhost/pdfapi/export-report/";
        $api_url = apply_filters( 'ays_quiz_pdfapi_api_report_url', $url );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
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
                $fileName = AYS_QUIZ_ADMIN_PATH . '/partials/results/export_file/single-report.pdf';
                $fileUrl = AYS_QUIZ_ADMIN_URL . '/partials/results/export_file/single-report.pdf';
                file_put_contents($fileName, $fileContent);
                $result = array(
                    'status' => true,
                    'fileUrl' => $fileUrl,
                    'fileName' => 'single-report.pdf',
                );
                return $result;
            }else{
                $result = array(
                    'status' => false,
                );
                return $result;
            }
        }
    }

    public function generate_report_PDF_public($data){
        $curl = curl_init();

        // $url = "https://ays-pro.com/pdfapi/export-report-public/";
        $url = "https://poll-plugin.com/pdfapi/export-report-public/";
//        $url = "https://tt-soft.com/pdfapi/export-report-public/"; // open
        // $url = "http://localhost/pdfapi/quiz-maker-pdfapi/pdfapi/export-report-public/"; // for localhost
//        $url = "http://localhost/pdfapi/export-report-public/";

        $api_url = apply_filters( 'ays_quiz_pdfapi_api_report_public_url', $url );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
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
            $response = json_decode($response, true);
            if($response["code"] == 1 && $response['msg'] == "Success"){
                $date = current_time('Y-m-d-H-i-s');
                $fileContent = base64_decode($response["data"]);
                $fileName = AYS_QUIZ_ADMIN_PATH . '/partials/results/export_file/public-single-report-'.$date.'.pdf';
                $fileUrl = AYS_QUIZ_ADMIN_URL . '/partials/results/export_file/public-single-report-'.$date.'.pdf';
                $files = preg_grep( '~^public-single-.*\.pdf$~', scandir( AYS_QUIZ_ADMIN_PATH . "/partials/results/export_file/" ) );
                foreach( $files as $file ){
                    unlink( AYS_QUIZ_ADMIN_PATH . "/partials/results/export_file/" . $file );
                    //if ( !in_array( $file, array( ".", ".." ) ) ){
                    //}
                }

                file_put_contents($fileName, $fileContent);
                $result = array(
                    'status'   => true,
                    'fileUrl'  => $fileUrl,
                    'fileName' => 'public-single-report-'.$date.'.pdf',
                );
                return $result;
            }else{
                $result = array(
                    'status' => false,
                );
                return $result;
            }
        }
    }

    public function generate_quiz_PDF_public_user($data){
        $curl = curl_init();

        $url = "https://ays-pro.com/pdfapi/export-quiz-public-user/";
       // $url = "https://tt-soft.com/pdfapi/export-report/";
       // $url = "http://localhost/pdfapi/export-report/";
        $api_url = apply_filters( 'ays_quiz_pdfapi_api_report_public_user_url', $url );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
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
                $fileName = AYS_QUIZ_ADMIN_PATH . '/partials/results/export_file/public-quiz-user.pdf';
                $fileUrl = AYS_QUIZ_ADMIN_URL . '/partials/results/export_file/public-quiz-user.pdf';
                file_put_contents($fileName, $fileContent);
                $result = array(
                    'status' => true,
                    'fileUrl' => $fileUrl,
                    'fileName' => 'public-quiz-user.pdf',
                );
                return $result;
            }else{
                $result = array(
                    'status' => false,
                );
                return $result;
            }
        }
    }

}
