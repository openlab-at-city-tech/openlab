<?php

class B2S_Api_Network_Pinterest {

    public $cookie = array();
    public $csrf = '';
    public $appVersion = '';
    public $route = 'https://www.pinterest.com/';
    public $host = 'www.pinterest.com';
    public $origin = 'https://www.pinterest.com/';
    public $timeout = 25;
    private $pinterestCountryList = array();

    public function __construct($location = 'en') {
        $this->pinterestCountryList = B2S_Tools::getCountryListByNetwork(6);
        $this->location = ((isset($this->pinterestCountryList[$location]) && isset($this->pinterestCountryList[$location]['url'])) ? $this->pinterestCountryList[$location]['url'] : 'https://www.pinterest.com/');
        $this->route = $this->location;
        $this->host = parse_url($this->location, PHP_URL_HOST);
        $this->origin = $this->location;
    }

    public function setHeader($referer = '', $org = '', $type = 'GET', $request = false, $csrf = true) {
        $header = array();
        $header['Cache-Control'] = 'max-age=0';
        $header['Connection'] = 'keep-alive';
        $header['Upgrade-Insecure-Requests'] = '1';
        $header['Referer'] = $referer;
        $header['User-Agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36';
        if ($type == 'JSON') {
            $header['Content-Type'] = 'application/json;charset=UTF-8';
        } else {
            $header['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8;';
        }
        if ($request === true) {
            $header['X-Requested-With'] = 'XMLHttpRequest';
        }
        $header['Accept-Encoding'] = 'gzip, deflate';
        $header['Accept-Language'] = 'en-US,en;q=0.5';
        $header['DNT'] = '1';
        $header['X-Pinterest-AppState'] = 'active';
        $header['X-NEW-APP'] = '1';
        $header['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        $header['Host'] = $this->host;
        $header['Origin'] = $this->origin;
        if ($csrf) {
            $header['X-CSRFToken'] = substr(md5(microtime()), rand(0, 26), 5);
        }
        return $header;
    }

    public function setRoute() {
        $cookie = $this->cookie;
        $headerData = $this->setHeader($this->location);
        $requestData = array('headers' => $headerData, 'cookies' => $cookie, 'timeout' => $this->timeout);
        $result = wp_remote_get($this->location . 'pinterest/', $requestData);
        if (is_wp_error($result)) {
            return array('error' => 1, 'error_pos' => 0, 'location' => 'setRoute', 'error_data' => serialize($result), 'error_code' => $result->get_error_code());
        }
        if ($result['response']['code'] == '302' && !empty($result['headers']['location'])) {
            $this->route = 'https://' . $this->cutFromTo($result['headers']['location'] . '/', "//", '/') . '/';
            return $this->route;
        }
        return $this->route;
    }

    public function authorize($username, $password) {
        $this->setRoute();
        $headerData = $this->setHeader($this->route . 'login/');
        $requestData = array('headers' => $headerData, 'timeout' => $this->timeout);
        $result = wp_remote_get($this->route . 'login/', $requestData);
        if (is_wp_error($result)) {
            return array('error' => 1, 'error_pos' => 1, 'location' => $this->route . 'login/', 'error_data' => serialize($result), 'error_code' => $result->get_error_code());
        }
        $cookie = $result['cookies'];
        $content = $result['body'];
        $csrfToken = '';
        $appVersion = trim($this->cutFromTo($content, '"app_version":"', '"'));


        //DEPRECATED REQUEST 12/2020
        /* $loginData = array(
          "options" => array(
          "username_or_email" => $username,
          "password" => $password
          ),
          "context" => array(
          "app_version" => $appVersion,
          )
          );
          $fields = array('data' => json_encode($loginData), 'source_url' => '/login/', 'module_path' => 'App()>LoginPage()>Login()>Button(class_name=primary, text=Log in, type=submit, tagName=button, size=large)'); */

        //START SINCE 12/2020
        $loginData = array(
            "options" => array(
                "get_user" => true,
                "username_or_email" => $username,
                "password" => $password,
                "no_fetch_context_on_resource" => false,
                "app_type_from_client" => 6,
                "recaptchaV3Token" => str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
            ),
            "context" => array()
        );
        $fields = array('data' => json_encode($loginData), 'source_url' => '/login/');
        //END

        foreach ($cookie as $c) {
            if ($c->name == 'csrftoken') {
                $csrfToken = $c->value;
            }
        }
        if (empty($csrfToken)) {
            $error_data = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($this->cutFromTo($content, '</head>', '</body>'))));
            return array('error' => 1, 'error_pos' => 2, 'location' => $this->route . 'login/', 'error_data' => 'CSRF verification failed - RESPONSE: ' . serialize($error_data) . '  COOKIE: ' . serialize($cookie), 'error_code' => 'invalid');
        }
        $headerData = $this->setHeader($this->route . 'login/', $this->route, 'POST', true);
        $headerData['X-APP-VERSION'] = $appVersion;
        $headerData['X-CSRFToken'] = $csrfToken;

        $requestData = array('headers' => $headerData, 'cookies' => $cookie, 'body' => $fields, 'timeout' => $this->timeout);
        $result = wp_remote_post($this->route . 'resource/UserSessionResource/create/', $requestData);
        if (is_wp_error($result)) {
            return array('error' => 1, 'error_pos' => 3, 'error_data' => serialize($result), 'error_code' => $result->get_error_code());
        }
        if (!empty($result['headers']['location'])) {
            $loc = $this->cutFromTo($result['headers']['location'], 'https://', '.pinterest');
            $headerData = $this->setHeader('https://' . $loc . '.pinterest.com/login/', 'https://' . $loc . '.pinterest.com', 'POST', true);
            $requestData = array('headers' => $headerData, 'cookies' => $cookie, 'body' => $fields, 'timeout' => $this->timeout);
            $result = wp_remote_post('https://' . $loc . '.pinterest.com/resource/UserSessionResource/create/', $requestData);
            if (is_wp_error($result)) {
                return array('error' => 1, 'error_pos' => 4, 'error_data' => serialize($result), 'error_code' => $result->get_error_code());
            }
        } else {
            $loc = 'www';
        }
        if (!empty($result['body'])) {
            $content = $result['body'];
            $response = json_decode($content, true);
        } else {
            return array('error' => 1, 'error_pos' => 5, 'error_data' => serialize($result), 'error_code' => 'invalid');
        }
        if (is_array($response) && empty($response['resource_response']['error'])) {
            $this->cookie = $result['cookies'];
            return array('error' => 0, 'identData' => serialize($this->cookie));
        } elseif (is_array($response) && isset($response['resource_response']['error'])) {
            return array('error' => 1, 'error_pos' => 6, 'error_data' => serialize($response['resource_response']['error']), 'error_code' => 'login');
        } elseif (stripos($content, 'CSRF') !== false) {
            $error_data = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($this->cutFromTo($content, '</head>', '</body>'))));
            return array('error' => 1, 'error_pos' => 6, 'error_data' => 'CSRF verification failed ' . serialize($error_data), 'error_code' => 'invalid');
        } elseif (stripos($content, 'suspicious activity') !== false) {
            return array('error' => 1, 'error_pos' => 6, 'error_data' => 'Pinterest blocked logins from this IP because of suspicious activity', 'error_code' => 'http_request_failed');
        } elseif (stripos($content, 'bot!') !== false) {
            return array('error' => 1, 'error_pos' => 6, 'error_data' => 'Pinterest has your ip in the list of potentially suspicious networks and blocked it', 'error_code' => 'http_request_failed');
        } else {
            $error_data = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($this->cutFromTo($content, '</head>', '</body>'))));
            return array('error' => 1, 'error_pos' => 6, 'error_data' => 'Pinterest login failed - unknown error ' . serialize($error_data), 'error_code' => 'access');
        }
        return array('error' => 1, 'error_pos' => 7, 'error_data' => 'Pinterest login failed - unknown error', 'error_code' => 'access');
    }

    public function cutFromTo($string, $from, $to) {
        $fstart = stripos($string, $from);
        $tmp = substr($string, $fstart + strlen($from));
        $flen = stripos($tmp, $to);
        return substr($tmp, 0, $flen);
    }

    public function getPinBoards() {
        $pinBoardsData = array();
        //$this->setRoute();
        $headerData = $this->setHeader($this->route, $this->route, 'JSON', true, false);
        $headerData['X-APP-VERSION'] = $this->appVersion;
        if(is_array($this->cookie) && !empty($this->cookie)) {
            foreach($this->cookie as $cookie) {
                if(isset($cookie->name) && !empty($cookie->name) && $cookie->name == "_auth" && isset($cookie->domain) && !empty($cookie->domain)) {
                    $this->route = 'https://' . $cookie->domain . '/';
                    break;
                }
            }
        }
        $pinBoardUrl = $this->route . 'resource/BoardPickerBoardsResource/get/';
        $requestData = array('headers' => $headerData, 'cookies' => $this->cookie, 'timeout' => $this->timeout);
        $result = wp_remote_get($pinBoardUrl, $requestData);
        if (is_wp_error($result)) {
            return array('error' => 1, 'error_pos' => 1, 'error_data' => serialize($result), 'error_code' => $result->get_error_code());
        }
        $content = $result['body'];
        $response = json_decode($content, true);
        if($response == null || !is_array($response) || empty($response)) {
            if(isset($result['response']) && !empty($result['response']) && isset($result['response']['code']) && $result['response']['code'] == 403) {
                return array('error' => 403, 'error_pos' => 1, 'error_data' => 'access_denied', 'error_code' => 'limit');
            }
        }
        if (!empty($response['resource_data_cache']) || !empty($response['resource_response'])) {
            if (!empty($response['resource_data_cache'])) {
                $boardsData = $response['resource_data_cache'];
            } else {
                $boardsData = array();
                $boardsData[] = $response['resource_response'];
            }
            if (isset($boardsData[0]['error']['status']) && $boardsData[0]['error']['status'] != 'success') {
                return array('error' => 2, 'error_pos' => 1, 'error_data' => 'access_denied');
            }

            foreach ($boardsData as $allBoards) {
                if (!empty($allBoards) && !empty($allBoards['data']['all_boards'])) {
                    foreach ($allBoards['data']['all_boards'] as $board) {
                        $pinBoardsData[] = array('board_id' => $board['id'], 'name' => $board['name']);
                    }
                    return array('error' => 0, 'data' => $pinBoardsData);
                } else {
                    return array('error' => 3, 'error_pos' => 1, 'error_data' => 'no_board');
                }
            }
        }
        return array('error' => 4, 'error_pos' => 1, 'error_data' => 'unknown_error');
    }
    
}
