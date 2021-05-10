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
        $dependence = false;
        if ('Yes' === $this->get_processor()->get_setting('mediaplayer_load_native_mediaelement')) {
            $dependence = ['wp-mediaelement'];
        }

        wp_register_script('Default_Skin.Polyfill', 'https://cdn.polyfill.io/v3/polyfill.min.js?features=es6,html5-elements,NodeList.prototype.forEach,Element.prototype.classList,CustomEvent,Object.entries,Object.assign,document.querySelector&flags=gated');
        wp_register_script('Default_Skin.Library', $this->get_url().'/js/mediaelement-and-player.min.js', $dependence, OUTOFTHEBOX_VERSION);
        wp_register_script('OutoftheBox.Default_Skin.Player', $this->get_url().'/js/Player.js', ['Default_Skin.Polyfill', 'Default_Skin.Library'], OUTOFTHEBOX_VERSION, true);

        wp_enqueue_script('OutoftheBox.Default_Skin.Player');

        $localize_library = [
            'language' => strtolower(strtok(determine_locale(), '_-')),
            'strings' => [
                'mejs.plural-form' => 2,
                'mejs.install-flash' => esc_html__('You are using a browser that does not have Flash player enabled or installed. Please turn on your Flash player plugin or download the latest version from https://get.adobe.com/flashplayer/'),
                'mejs.fullscreen-off' => esc_html__('Turn off Fullscreen'),
                'mejs.fullscreen-on' => esc_html__('Go Fullscreen'),
                'mejs.download-video' => esc_html__('Download Video'),
                'mejs.download-file' => esc_html__('Download'),
                'mejs.purchase' => esc_html__('Purchase', 'wpcloudplugins'),
                'mejs.fullscreen' => esc_html__('Fullscreen'),
                'mejs.time-jump-forward' => [esc_html__('Jump forward 1 second'), esc_html__('Jump forward %1 seconds')],
                'mejs.loop' => esc_html__('Toggle Loop'),
                'mejs.play' => esc_html__('Play'),
                'mejs.pause' => esc_html__('Pause'),
                'mejs.close' => esc_html__('Close'),
                'mejs.playlist' => esc_html__('Close'),
                'mejs.playlist-prev' => esc_html__('Previous'),
                'mejs.playlist-next' => esc_html__('Next'),
                'mejs.playlist-loop' => esc_html__('Loop'),
                'mejs.playlist-shuffle' => esc_html__('Shuffle'),
                'mejs.time-slider' => esc_html__('Time Slider'),
                'mejs.time-help-text' => esc_html__('Use Left/Right Arrow keys to advance one second, Up/Down arrows to advance ten seconds.'),
                'mejs.time-skip-back' => [esc_html__('Skip back 1 second'), esc_html__('Skip back %1 seconds')],
                'mejs.captions-subtitles' => esc_html__('Captions/Subtitles'),
                'mejs.captions-chapters' => esc_html__('Chapters'),
                'mejs.none' => esc_html__('None'),
                'mejs.mute-toggle' => esc_html__('Mute Toggle'),
                'mejs.volume-help-text' => esc_html__('Use Up/Down Arrow keys to increase or decrease volume.'),
                'mejs.unmute' => esc_html__('Unmute'),
                'mejs.mute' => esc_html__('Mute'),
                'mejs.volume-slider' => esc_html__('Volume Slider'),
                'mejs.video-player' => esc_html__('Video Player'),
                'mejs.audio-player' => esc_html__('Audio Player'),
                'mejs.ad-skip' => esc_html__('Skip ad'),
                'mejs.ad-skip-info' => [esc_html__('Skip in 1 second'), esc_html__('Skip in %1 seconds')],
                'mejs.source-chooser' => esc_html__('Source Chooser'),
                'mejs.stop' => esc_html__('Stop'),
                'mejs.speed-rate' => esc_html__('Speed Rate'),
                'mejs.live-broadcast' => esc_html__('Live Broadcast'),
                'mejs.afrikaans' => esc_html__('Afrikaans'),
                'mejs.albanian' => esc_html__('Albanian'),
                'mejs.arabic' => esc_html__('Arabic'),
                'mejs.belarusian' => esc_html__('Belarusian'),
                'mejs.bulgarian' => esc_html__('Bulgarian'),
                'mejs.catalan' => esc_html__('Catalan'),
                'mejs.chinese' => esc_html__('Chinese'),
                'mejs.chinese-simplified' => esc_html__('Chinese (Simplified)'),
                'mejs.chinese-traditional' => esc_html__('Chinese (Traditional)'),
                'mejs.croatian' => esc_html__('Croatian'),
                'mejs.czech' => esc_html__('Czech'),
                'mejs.danish' => esc_html__('Danish'),
                'mejs.dutch' => esc_html__('Dutch'),
                'mejs.english' => esc_html__('English'),
                'mejs.estonian' => esc_html__('Estonian'),
                'mejs.filipino' => esc_html__('Filipino'),
                'mejs.finnish' => esc_html__('Finnish'),
                'mejs.french' => esc_html__('French'),
                'mejs.galician' => esc_html__('Galician'),
                'mejs.german' => esc_html__('German'),
                'mejs.greek' => esc_html__('Greek'),
                'mejs.haitian-creole' => esc_html__('Haitian Creole'),
                'mejs.hebrew' => esc_html__('Hebrew'),
                'mejs.hindi' => esc_html__('Hindi'),
                'mejs.hungarian' => esc_html__('Hungarian'),
                'mejs.icelandic' => esc_html__('Icelandic'),
                'mejs.indonesian' => esc_html__('Indonesian'),
                'mejs.irish' => esc_html__('Irish'),
                'mejs.italian' => esc_html__('Italian'),
                'mejs.japanese' => esc_html__('Japanese'),
                'mejs.korean' => esc_html__('Korean'),
                'mejs.latvian' => esc_html__('Latvian'),
                'mejs.lithuanian' => esc_html__('Lithuanian'),
                'mejs.macedonian' => esc_html__('Macedonian'),
                'mejs.malay' => esc_html__('Malay'),
                'mejs.maltese' => esc_html__('Maltese'),
                'mejs.norwegian' => esc_html__('Norwegian'),
                'mejs.persian' => esc_html__('Persian'),
                'mejs.polish' => esc_html__('Polish'),
                'mejs.portuguese' => esc_html__('Portuguese'),
                'mejs.romanian' => esc_html__('Romanian'),
                'mejs.russian' => esc_html__('Russian'),
                'mejs.serbian' => esc_html__('Serbian'),
                'mejs.slovak' => esc_html__('Slovak'),
                'mejs.slovenian' => esc_html__('Slovenian'),
                'mejs.spanish' => esc_html__('Spanish'),
                'mejs.swahili' => esc_html__('Swahili'),
                'mejs.swedish' => esc_html__('Swedish'),
                'mejs.tagalog' => esc_html__('Tagalog'),
                'mejs.thai' => esc_html__('Thai'),
                'mejs.turkish' => esc_html__('Turkish'),
                'mejs.ukrainian' => esc_html__('Ukrainian'),
                'mejs.vietnamese' => esc_html__('Vietnamese'),
                'mejs.welsh' => esc_html__('Welsh'),
                'mejs.yiddish' => esc_html__('Yiddish'),
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
