<?php
/**
 * Class Connecting Zoom APi V1
 * Only use for Development purposes as v1 of the API has already been deprecated by zoom
 *
 * @since  2.0
 * @author  Deepen
 * @modifiedn
 * @deprecated 2.0.0
 */
if( !class_exists('Zoom_Video_Conferencing_Api') ) {

  class Zoom_Video_Conferencing_Api {

    public $zoom_api_key;

    public $zoom_api_secret;

    protected static $_instance;

    private $api_url = 'https://api.zoom.us/v1/';

    /**
    * Create only one instance so that it may not Repeat
    * @since 2.0.0
    */
    public static function instance() {
      if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }

    public function __construct($zoom_api_key = '', $zoom_api_secret = '') {
      $this->zoom_api_key = $zoom_api_key;
      $this->zoom_api_secret = $zoom_api_secret;
    }

    protected function sendRequest($calledFunction, $data){
      $request_url = $this->api_url.$calledFunction;

      /*Adds the Key, Secret, & Datatype to the passed array*/
      $data['api_key'] = $this->zoom_api_key;
      $data['api_secret'] = $this->zoom_api_secret;
      $data['data_type'] = 'JSON';

      $postFields = http_build_query($data);

      /*Preparing Query...*/
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_URL, $request_url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);

      if(!$response){
        return false;
      }

      return $response;
    }

    /**
     * Create a User
     * @return Object
     */
    public function createAUser($email, $first_name, $last_name, $type, $dept = ""){
      $createAUserArray = array();
      $createAUserArray['email'] = $email;
      $createAUserArray['type'] = $type;
      $createAUserArray['first_name'] = $first_name;
      $createAUserArray['last_name'] = $last_name;
      $createAUserArray['dept'] = $dept;
      return $this->sendRequest('user/create', $createAUserArray);
    }

    /**
     * User Function to List
     * @return Array
     */
    public function listUsers(){
      $listUsersArray = array();
      $listUsersArray['page_size'] = 300;
      return $this->sendRequest('user/list', $listUsersArray);
    }

    /**
     * Get A users info by user Id
     * @return JSON DATA
     */
    public function getUserInfo($user_id){
      $getUserInfoArray = array();
      $getUserInfoArray['id'] = $user_id;
      return $this->sendRequest('user/get',$getUserInfoArray);
    }

    /**
    * Delete a User
    * @return Boolean
    */
    public function deleteAUser($userid){
      $deleteAUserArray = array();
      $deleteAUserArray['id'] = $userid;
      return $this->sendRequest('user/delete', $deleteAUserArray);
    }

    /**
     * Get Meetings
     * @return ARRAY
     */
    public function listMeetings($host_id){
      $listMeetingsArray = array();
      $listMeetingsArray['page_size'] = 300;
      $listMeetingsArray['host_id'] = $host_id;
      return $this->sendRequest('meeting/list',$listMeetingsArray);
    }

    /**
     * Create A meeting API
     * @param  ARRAY  $data
     * @return ARRAY
     */
    public function createAMeeting( $data = array() ){
      $post_time = $data['start_date'];
      $start_time = gmdate("Y-m-d\TH:i:s\Z", strtotime($post_time));

      $createAMeetingArray = array();
      $createAMeetingArray['host_id'] = $data['userId'];
      $createAMeetingArray['topic'] = $data['meetingTopic'];
      $createAMeetingArray['type'] = 2; //Scheduled
      $createAMeetingArray['start_time'] = $start_time;
      $createAMeetingArray['timezone'] = $data['timezone'];
      $createAMeetingArray['password'] = $data['password'] ? $data['password'] : NULL;
      $createAMeetingArray['duration'] = $data['duration'];
      $createAMeetingArray['option_jbh'] = $data['join_before_host'] ? true : false;
      $createAMeetingArray['option_host_video'] = $data['option_host_video'] ? true : false;
      $createAMeetingArray['option_participants_video'] = $data['option_participants_video'] ? true : false;
      $createAMeetingArray['option_cn_meeting'] = $data['option_cn_meeting'] ? true : false;
      $createAMeetingArray['option_in_meeting'] = $data['option_in_meeting'] ? true : false;
      $createAMeetingArray['option_enforce_login'] = $data['option_enforce_login'] ? true : false;
      $createAMeetingArray['option_audio'] = $data['option_audio'] ? true : false;
      return $this->sendRequest('meeting/create', $createAMeetingArray);
    }

    /**
     * Updating Meeting Info
     * @return JSON
     */
    public function updateMeetingInfo( $update_data = array() ){
      $post_time = $update_data['start_date'];
      $start_time = gmdate("Y-m-d\TH:i:s\Z", strtotime($post_time));

      $updateMeetingInfoArray = array();
      $updateMeetingInfoArray['id'] = $update_data['meeting_id'];
      $updateMeetingInfoArray['host_id'] = $update_data['host_id'];
      $updateMeetingInfoArray['topic'] = $update_data['topic'];
      $updateMeetingInfoArray['type'] = 2; //Scheduled
      $updateMeetingInfoArray['start_time'] = $start_time;
      $updateMeetingInfoArray['timezone'] = $update_data['timezone'];
      $updateMeetingInfoArray['duration'] = $update_data['duration'];
      $updateMeetingInfoArray['option_jbh'] = $update_data['option_jbh'] ? true : false;
      $updateMeetingInfoArray['option_host_video'] = $update_data['option_host_video'] ? true : false;
      $updateMeetingInfoArray['option_participants_video'] = $update_data['option_participants_video'] ? true : false;
      $updateMeetingInfoArray['option_cn_meeting'] = $update_data['option_cn_meeting'] ? true : false;
      $updateMeetingInfoArray['option_in_meeting'] = $update_data['option_in_meeting'] ? true : false;
      $updateMeetingInfoArray['option_enforce_login'] = $update_data['option_enforce_login'] ? true : false;
      $updateMeetingInfoArray['option_audio'] = $data['option_audio'] ? true : false;
      return $this->sendRequest('meeting/update', $updateMeetingInfoArray);
    }

    /**
     * Get a Meeting Info
     * @param  [INT] $id
     * @param  [STRING] $host_id
     * @return JSON
     */
    public function getMeetingInfo($id, $host_id) {
      $getMeetingInfoArray = array();
      $getMeetingInfoArray['id'] = $id;
      $getMeetingInfoArray['host_id'] = $host_id;
      return $this->sendRequest('meeting/get', $getMeetingInfoArray);
    }

    /**
     * Delete A Meeting
     * @param $meeting_id[int], $host_id[string]
     * @return [type] [description]
     */
    public function deleteAMeeting($meeting_id, $host_id){
      $deleteAMeetingArray = array();
      $deleteAMeetingArray['id'] = $meeting_id;
      $deleteAMeetingArray['host_id'] = $host_id;
      return $this->sendRequest('meeting/delete', $deleteAMeetingArray);
    }

    /*Functions for management of reports*/
    public function getDailyReport($month, $year){
      $getDailyReportArray = array();
      $getDailyReportArray['year'] = $year;
      $getDailyReportArray['month'] = $month;
      return $this->sendRequest('report/getdailyreport', $getDailyReportArray);
    }

    public function getAccountReport($zoom_account_from, $zoom_account_to){
      $getAccountReportArray = array();
      $getAccountReportArray['from'] = $zoom_account_from;
      $getAccountReportArray['to'] = $zoom_account_to;
      $getAccountReportArray['page_size'] = 300;
      return $this->sendRequest('report/getaccountreport', $getAccountReportArray);
    }

    public function getUserReport(){
      $getUserReportArray = array();
      $getUserReportArray['user_id'] = $_POST['userId'];
      $getUserReportArray['from'] = $_POST['from'];
      $getUserReportArray['to'] = $_POST['to'];
      return $this->sendRequest('report/getuserreport', $getUserReportArray);
    }

  }

  function zoom_conference() {
    return Zoom_Video_Conferencing_Api::instance();
  }

  zoom_conference();
}
