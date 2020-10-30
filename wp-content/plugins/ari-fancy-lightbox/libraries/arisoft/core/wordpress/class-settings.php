<?php
namespace Ari\Wordpress;

class Settings extends Settings_Generic {
    protected $settings_group;

    public function init() {
        register_setting(
            $this->settings_group,
            $this->settings_name,
            array( $this, 'sanitize' )
        );
    }
}
