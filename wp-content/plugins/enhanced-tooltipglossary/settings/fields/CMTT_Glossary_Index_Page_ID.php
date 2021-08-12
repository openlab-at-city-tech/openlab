<?php

namespace CM;

class CMTT_Glossary_Index_Page_ID {

    public static function render() {
        ob_start();
        wp_dropdown_pages(array('name' => 'cmtt_glossaryID', 'selected' => (int) \CM\CMTT_Settings::get('cmtt_glossaryID', -1), 'show_option_none' => '-None-', 'option_none_value' => '0'))
        ?>
        <br/><input type="checkbox" name="cmtt_glossaryID" value="-1" /> Generate page for Glossary Index
        <?php
        $content = ob_get_clean();
        return $content;
    }

}
