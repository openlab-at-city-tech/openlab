<?php

class B2S_Form {

    public static function getNetworkBoardAndGroupHtml($data,$network_id=0) {
        $collection = '<select class="form-control b2s-select" data-network-id="'.esc_attr($network_id).'" id="b2s-modify-board-and-group-network-selected">';
        $collection .= $data;
        $collection .= '</select>';
        return $collection;
    }
}
