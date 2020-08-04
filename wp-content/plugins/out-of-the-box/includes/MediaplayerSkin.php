<?php

namespace TheLion\OutoftheBox;

abstract class MediaplayerSkin
{
    public $url;
    public $template_path;
    public $processor;

    public function __construct($processor)
    {
        $this->processor = $processor;
    }

    public function load_player()
    {
        $this->load_scripts();
        $this->load_styles();

        return $this->render_template();
    }

    abstract public function load_scripts();

    abstract public function load_styles();

    public function set_url($url)
    {
        return $this->url = $url;
    }

    public function get_url()
    {
        return $this->url;
    }

    public function set_template_path($template_path)
    {
        $this->template_path = $template_path;
    }

    public function get_template_path()
    {
        return $this->template_path;
    }

    public function render_template()
    {
        return include $this->get_template_path();
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        if (empty($this->_processor)) {
            global $OutoftheBox;
            $this->_processor = $OutoftheBox->get_processor();
        }

        return $this->_processor;
    }
}
