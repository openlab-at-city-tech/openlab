<?php

if (!defined("ABSPATH")) {
    exit();
}

function wpDiscuz(){
    return WpdiscuzCore::getInstance();
}

function wpdiscuzGetOptions() {
    $wpDiscuz = wpDiscuz();
    $optionsObj = $wpDiscuz->getOptions();
    return $optionsObj->getOptions();
}

function wpDiscuzGetOption($key, $tab = null) {
    $wpDiscuz = wpDiscuz();
    $optionsObj = $wpDiscuz->getOptions();
    return $optionsObj->getOption($key, $tab);
}