<?php

namespace TheLion\OutoftheBox\MediaPlayers;

class Legacy_jPlayer extends \TheLion\OutoftheBox\MediaplayerSkin
{
    public $url;
    public $template_path = __DIR__.'/Template.php';

    public function __construct($processor)
    {
        parent::__construct($processor);

        $this->url = plugins_url('', __FILE__);

        add_action('wp_footer', [&$this, 'load_custom_css'], 100);
        add_action('admin_footer', [&$this, 'load_custom_css'], 100);
    }

    public function load_scripts()
    {
        wp_register_script('Legacy_jPlayer.Playlist', $this->get_url().'/js/jplayer.playlist.min.js', ['jquery'], OUTOFTHEBOX_VERSION);
        wp_register_script('OutoftheBox.Legacy_jPlayer.jPlayer', $this->get_url().'/js/jquery.jplayer.min.js', ['jquery', 'Legacy_jPlayer.Playlist'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.Legacy_jPlayer.Player', $this->get_url().'/js/Player.js', ['OutoftheBox.Legacy_jPlayer.jPlayer', 'jquery-ui-slider', 'OutoftheBox'], OUTOFTHEBOX_VERSION, true);

        wp_enqueue_script('OutoftheBox.Legacy_jPlayer.Player');

        $localize_mediaplayer = [
            'player_url' => $this->get_url(),
        ];

        wp_localize_script('OutoftheBox.Legacy_jPlayer.Player', 'Legacy_jPlayer_vars', $localize_mediaplayer);
    }

    public function load_styles()
    {
        wp_register_style('OutoftheBox.Legacy_jPlayer.Player.CSS', $this->get_url().'/css/style.css', false, OUTOFTHEBOX_VERSION);
        wp_enqueue_style('OutoftheBox.Legacy_jPlayer.Player.CSS');
    }

    public function load_custom_css()
    {
        $css_html = '<!-- Custom OutoftheBox Legacy Player CSS Styles -->'."\n";
        $css_html .= '<style type="text/css" media="screen">'."\n";
        $css = '';

        $colors = $this->get_processor()->get_setting('colors');

        $css = file_get_contents(__DIR__.'/css/skin.'.$colors['style'].'.min.css');
        $css = preg_replace_callback('/%(.*)%/iU', [&$this, 'fill_placeholder_styles'], $css);

        $css_html .= \TheLion\OutoftheBox\Helpers::compress_css($css);
        $css_html .= '</style>'."\n";

        echo $css_html;
    }

    public function fill_placeholder_styles($matches)
    {
        $colors = $this->get_processor()->get_setting('colors');

        if (isset($colors[$matches[1]])) {
            return $colors[$matches[1]];
        }

        return 'initial';
    }
}