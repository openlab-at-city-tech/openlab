<?php

namespace TheLion\Integrations\Divi;

class WPCP_DiviExtension extends \DiviExtension
{
    /**
     * The extension's WP Plugin name.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $name = 'wpcp-divi-extension';

    /**
     * The extension's version.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * WPCP_DiviExtension constructor.
     *
     * @param string $name
     * @param array  $args
     */
    public function __construct($name = 'wpcp-divi-extension', $args = [])
    {
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->plugin_dir_url = plugin_dir_url($this->plugin_dir);

        parent::__construct($name, $args);
    }
}

new WPCP_DiviExtension();
