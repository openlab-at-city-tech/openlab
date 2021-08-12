<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Integrations
{
    /**
     * @var \TheLion\OutoftheBox\Main
     */
    private $_main;

    public function __construct(\TheLion\OutoftheBox\Main $main)
    {
        $this->_main = $main;

        // Add Global Form Helpers
        require_once 'FormHelpers.php';
        new FormHelpers();

        // Load integrations
        $this->load_contactform7();
        $this->load_elementor();
        $this->load_gravityforms();
        $this->load_formidableforms();
        $this->load_gravitypdf();
        $this->load_gutenberg();
        $this->load_woocommcerce();
        $this->load_wpforms();
    }

    public function load_contactform7()
    {
        if (!defined('WPCF7_PLUGIN')) {
            return false;
        }

        require_once 'contactform7/init.php';

        new ContactForm($this->_main);
    }

    public function load_elementor()
    {
        if (!did_action('elementor/loaded')) {
            return false;
        }

        require_once 'elementor/init.php';
    }

    public function load_gravityforms()
    {
        if (!class_exists('GFForms')) {
            return false;
        }

        require_once 'gravityforms/init.php';
    }

    public function load_formidableforms()
    {
        if (!class_exists('FrmHooksController')) {
            return false;
        }

        require_once 'formidableforms/init.php';
    }

    public function load_fluentforms()
    {
        if (!defined('FLUENTFORM')) {
            return false;
        }

        require_once 'fluentforms/init.php';
    }

    public function load_gravitypdf()
    {
        if (!class_exists('GFForms')) {
            return false;
        }

        require_once 'gravitypdf/init.php';
    }

    public function load_gutenberg()
    {
        require_once 'gutenberg/init.php';
    }

    public function load_woocommcerce()
    {
        if (!class_exists('woocommerce')) {
            return false;
        }

        require_once 'woocommerce/init.php';
    }

    public function load_wpforms()
    {
        if (!defined('WPFORMS_VERSION')) {
            return false;
        }

        require_once 'wpforms/init.php';
    }
}
