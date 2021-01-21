<?php
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Save.php');

class B2S_Calendar_Save extends B2S_Ship_Save {
    private $b2s_id = null;

    /**
     * @param int $value
     * @return $this
     */
    public function setB2SId($value){
        if(is_numeric($value))
        {
            $this->b2s_id = $value;
        }

        return $this;
    }


    /**
     * @return int|null
     */
    public function getB2SId(){
        return $this->b2s_id;
    }
}