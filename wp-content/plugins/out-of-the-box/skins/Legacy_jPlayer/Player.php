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
}
