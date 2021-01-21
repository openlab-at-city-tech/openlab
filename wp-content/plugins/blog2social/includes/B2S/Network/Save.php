<?php

class B2S_Network_Save {

    public static function saveUserMandant($mandantName, $allow_delete = true, $assignToken = false) {
        require_once B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php';
        $userToken = B2S_PLUGIN_TOKEN;
        if($assignToken != false) {
            $userToken = $assignToken;
        }
        $postData = array('action' => 'saveUserMandant', 'mandant' => $mandantName, 'token' => $userToken, 'allow_delete' => $allow_delete);
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $postData));

        $newMandant = array('result' => false,
            'mandantId' => "",
            'mandantName' => "",
            'content' => "");

        if (isset($result->result) && $result->result == true && (int) $result->data > 0) {
            $newMandant['result'] = true;
            $newMandant['mandantId'] = $result->data;
            $newMandant['mandantName'] = $mandantName;
            $networkItem = new B2S_Network_Item();
            $networkData = $networkItem->getData();
            $newMandant['content'] = $networkItem->getItemHtml($result->data, array(), array(),$networkData['portale'],$networkData['auth_count']);
        }

        return $newMandant;
    }

}
