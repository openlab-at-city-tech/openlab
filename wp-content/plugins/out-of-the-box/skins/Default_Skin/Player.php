<?php

namespace TheLion\OutoftheBox\MediaPlayers;

class Default_Skin extends \TheLion\OutoftheBox\MediaplayerSkin
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
        wp_register_script('Default_Skin.Polyfill', 'https://cdn.polyfill.io/v3/polyfill.min.js?features=es6,html5-elements,NodeList.prototype.forEach,Element.prototype.classList,CustomEvent,Object.entries,Object.assign,document.querySelector&flags=gated');
        wp_register_script('Default_Skin.Library', $this->get_url().'/js/mediaelement-and-player.min.js', false, OUTOFTHEBOX_VERSION);
        wp_register_script('OutoftheBox.Default_Skin.Player', $this->get_url().'/js/Player.js', ['Default_Skin.Polyfill', 'Default_Skin.Library'], OUTOFTHEBOX_VERSION, true);

        wp_enqueue_script('OutoftheBox.Default_Skin.Player');

        $localize_library = [
            'language' => strtolower(strtok(determine_locale(), '_-')),
            'strings' => [
                'mejs.plural-form' => 2,
                'mejs.install-flash' => __('You are using a browser that does not have Flash player enabled or installed. Please turn on your Flash player plugin or download the latest version from https://get.adobe.com/flashplayer/'),
                'mejs.fullscreen-off' => __('Turn off Fullscreen'),
                'mejs.fullscreen-on' => __('Go Fullscreen'),
                'mejs.download-video' => __('Download Video'),
                'mejs.download-file' => __('Download'),
                'mejs.purchase' => __('Purchase', 'wpcloudplugins'),
                'mejs.fullscreen' => __('Fullscreen'),
                'mejs.time-jump-forward' => [__('Jump forward 1 second'), __('Jump forward %1 seconds')],
                'mejs.loop' => __('Toggle Loop'),
                'mejs.play' => __('Play'),
                'mejs.pause' => __('Pause'),
                'mejs.close' => __('Close'),
                'mejs.playlist' => __('Close'),
                'mejs.playlist-prev' => __('Previous'),
                'mejs.playlist-next' => __('Next'),
                'mejs.playlist-loop' => __('Loop'),
                'mejs.playlist-shuffle' => __('Shuffle'),
                'mejs.time-slider' => __('Time Slider'),
                'mejs.time-help-text' => __('Use Left/Right Arrow keys to advance one second, Up/Down arrows to advance ten seconds.'),
                'mejs.time-skip-back' => [__('Skip back 1 second'), __('Skip back %1 seconds')],
                'mejs.captions-subtitles' => __('Captions/Subtitles'),
                'mejs.captions-chapters' => __('Chapters'),
                'mejs.none' => __('None'),
                'mejs.mute-toggle' => __('Mute Toggle'),
                'mejs.volume-help-text' => __('Use Up/Down Arrow keys to increase or decrease volume.'),
                'mejs.unmute' => __('Unmute'),
                'mejs.mute' => __('Mute'),
                'mejs.volume-slider' => __('Volume Slider'),
                'mejs.video-player' => __('Video Player'),
                'mejs.audio-player' => __('Audio Player'),
                'mejs.ad-skip' => __('Skip ad'),
                'mejs.ad-skip-info' => [__('Skip in 1 second'), __('Skip in %1 seconds')],
                'mejs.source-chooser' => __('Source Chooser'),
                'mejs.stop' => __('Stop'),
                'mejs.speed-rate' => __('Speed Rate'),
                'mejs.live-broadcast' => __('Live Broadcast'),
                'mejs.afrikaans' => __('Afrikaans'),
                'mejs.albanian' => __('Albanian'),
                'mejs.arabic' => __('Arabic'),
                'mejs.belarusian' => __('Belarusian'),
                'mejs.bulgarian' => __('Bulgarian'),
                'mejs.catalan' => __('Catalan'),
                'mejs.chinese' => __('Chinese'),
                'mejs.chinese-simplified' => __('Chinese (Simplified)'),
                'mejs.chinese-traditional' => __('Chinese (Traditional)'),
                'mejs.croatian' => __('Croatian'),
                'mejs.czech' => __('Czech'),
                'mejs.danish' => __('Danish'),
                'mejs.dutch' => __('Dutch'),
                'mejs.english' => __('English'),
                'mejs.estonian' => __('Estonian'),
                'mejs.filipino' => __('Filipino'),
                'mejs.finnish' => __('Finnish'),
                'mejs.french' => __('French'),
                'mejs.galician' => __('Galician'),
                'mejs.german' => __('German'),
                'mejs.greek' => __('Greek'),
                'mejs.haitian-creole' => __('Haitian Creole'),
                'mejs.hebrew' => __('Hebrew'),
                'mejs.hindi' => __('Hindi'),
                'mejs.hungarian' => __('Hungarian'),
                'mejs.icelandic' => __('Icelandic'),
                'mejs.indonesian' => __('Indonesian'),
                'mejs.irish' => __('Irish'),
                'mejs.italian' => __('Italian'),
                'mejs.japanese' => __('Japanese'),
                'mejs.korean' => __('Korean'),
                'mejs.latvian' => __('Latvian'),
                'mejs.lithuanian' => __('Lithuanian'),
                'mejs.macedonian' => __('Macedonian'),
                'mejs.malay' => __('Malay'),
                'mejs.maltese' => __('Maltese'),
                'mejs.norwegian' => __('Norwegian'),
                'mejs.persian' => __('Persian'),
                'mejs.polish' => __('Polish'),
                'mejs.portuguese' => __('Portuguese'),
                'mejs.romanian' => __('Romanian'),
                'mejs.russian' => __('Russian'),
                'mejs.serbian' => __('Serbian'),
                'mejs.slovak' => __('Slovak'),
                'mejs.slovenian' => __('Slovenian'),
                'mejs.spanish' => __('Spanish'),
                'mejs.swahili' => __('Swahili'),
                'mejs.swedish' => __('Swedish'),
                'mejs.tagalog' => __('Tagalog'),
                'mejs.thai' => __('Thai'),
                'mejs.turkish' => __('Turkish'),
                'mejs.ukrainian' => __('Ukrainian'),
                'mejs.vietnamese' => __('Vietnamese'),
                'mejs.welsh' => __('Welsh'),
                'mejs.yiddish' => __('Yiddish'),
            ],
        ];

        $localize_mediaplayer = [
            'player_url' => $this->get_url(),
        ];

        wp_localize_script('Default_Skin.Library', 'mejsL10n', $localize_library);
        wp_localize_script('OutoftheBox.Default_Skin.Player', 'Default_Skin_vars', $localize_mediaplayer);
    }

    public function load_styles()
    {
        $is_rtl_css = (is_rtl() ? '.rtl' : '');

        wp_register_style('OutoftheBox.Default_Skin.Player.CSS', $this->get_url().'/css/style'.$is_rtl_css.'.css', false, OUTOFTHEBOX_VERSION);
        wp_enqueue_style('OutoftheBox.Default_Skin.Player.CSS');
    }
}

// Backwards compatability for < WP 5.0
if (false === function_exists('determine_locale')) {
    function determine_locale()
    {
        return get_locale();
    }
}
