<?php

class B2S_Options {

    public $optionData;
    protected $name;
    protected $blog_user_id;
    protected $autoLoad;

    public function __construct($blog_user_id = 0, $name = 'B2S_PLUGIN_OPTIONS', $autoLoad = false) {  //since V4.0.0
        $this->name = $name;
        $this->blog_user_id = $blog_user_id;
        $this->optionData = ($this->blog_user_id == 0) ? get_option($name) : get_option($name . '_' . $blog_user_id);
        $this->autoLoad = $autoLoad;
    }

    public function _getOption($key) {
        if (is_array($this->optionData)) {
            foreach ($this->optionData as $k) {
                if (isset($this->optionData[$key])) {
                    return $this->optionData[$key];
                }
            }
        }
        return false;
    }

    public function _setOption($key, $value, $addToArray = false) {
        $update = false;
        if (!is_array($this->optionData) || $this->optionData === false) {
            $this->optionData = array($key => (($addToArray) ? array($value) : $value));
            $update = true;
        } else {
            foreach ($this->optionData as $k) {
                if (isset($this->optionData[$key])) {
                    if ($addToArray && is_array($this->optionData[$key])) {
                        array_push($this->optionData[$key], $value);
                    } else {
                        $this->optionData[$key] = $value;
                    }
                    $update = true;
                }
            }
            if (!$update) {
                if (is_array($this->optionData)) {
                    $this->optionData[$key] = ($addToArray) ? array($value) : $value;
                }
            }
        }
        if ($this->blog_user_id == 0) {
            update_option($this->name, $this->optionData, $this->autoLoad);
        } else {
            update_option($this->name . '_' . $this->blog_user_id, $this->optionData, $this->autoLoad);
        }
        return true;
    }

    public function existsValueByKey($key, $value) {
        if (is_array($this->optionData)) {
            foreach ($this->optionData as $k) {
                if (isset($this->optionData[$key])) {
                    if (is_array($this->optionData[$key])) {
                        foreach ($this->optionData[$key]as $k => $v) {
                            if ($v == $value) {
                                return true;
                            }
                        }
                    } else {
                        if ($this->optionData[$key] == $value) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function deleteValueByKey($key, $value) {
        $return = false;
        if (is_array($this->optionData)) {
            if (isset($this->optionData[$key])) {
                if (is_array($this->optionData[$key])) {
                    $tempArray = array();
                    foreach ($this->optionData[$key] as $fkey => $v) {
                        if (isset($this->optionData[$key][$fkey])) {
                            if ($v != $value) {
                                array_push($tempArray, $this->optionData[$key][$fkey]);
                            }
                        }
                    }
                    $this->optionData[$key] = $tempArray;
                    $return = true;
                } else if ($this->optionData[$key] == $value) {
                    unset($this->optionData[$key]);
                    $return = true;
                }
            }
        }
        if ($return) {
            if ($this->blog_user_id == 0) {
                update_option($this->name, $this->optionData, $this->autoLoad);
            } else {
                update_option($this->name . '_' . $this->blog_user_id, $this->optionData, $this->autoLoad);
            }
        }
        return $return;
    }

}
