<?php
class M_Widget extends C_Base_Module
{
    /**
     * Defines the module name & version
     */
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-widget',
            'Widget',
            'Handles clearing of NextGen Widgets',
            '3.1.6',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    /**
     * Register utilities
     */
    function _register_utilities()
    {
        $this->get_registry()->add_utility('I_Widget', 'C_Widget');
    }

    /**
     * Register hooks
     */
    function _register_hooks()
    {
    	add_action('widgets_init', array($this, 'register_widgets'));
    }

    function register_widgets()
    {
	    register_widget("C_Widget_Gallery");
	    register_widget("C_Widget_MediaRSS");
	    register_widget("C_Widget_Slideshow");
    }

    function get_type_list()
    {
        return array(
            'C_Widget' => 'class.widget.php',
            'C_Widget_Gallery' => 'class.widget_gallery.php',
            'C_Widget_Mediarss' => 'class.widget_mediarss.php',
            'C_Widget_Slideshow' => 'class.widget_slideshow.php'
        );
    }

}

new M_Widget();
