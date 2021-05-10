<?php

namespace TheLion\OutoftheBox;

define('OUTOFTHEBOX_CURRENT_BLOG_ID', get_current_blog_id());

class CSS
{
    public $custom_css;
    public $colors;
    public $loaders;
    public $css_template_path;
    public static $css_url = OUTOFTHEBOX_CACHEURL.OUTOFTHEBOX_CURRENT_BLOG_ID.'_style.min.css';
    public static $css_path = OUTOFTHEBOX_CACHEDIR.OUTOFTHEBOX_CURRENT_BLOG_ID.'_style.min.css';

    public function __construct($settings)
    {
        $this->custom_css = $settings['custom_css'];
        $this->colors = $settings['colors'];
        $this->loaders = $settings['loaders'];

        $this->css_template_path = OUTOFTHEBOX_ROOTDIR.'/css/skin.'.$this->colors['style'].'.min.css';
    }

    public function register_style()
    {
        if (!file_exists(self::$css_path)) {
            $this->generate_custom_css();
        }

        wp_register_style('OutoftheBox.CustomCSS', self::$css_url, [], filemtime(self::$css_path));
    }

    public function generate_custom_css()
    {
        $css = '';

        if (!empty($this->custom_css)) {
            $css .= $this->custom_css."\n";
        }

        if ('custom' === $this->loaders['style']) {
            $css .= '#OutoftheBox .loading{  background-image: url('.$this->loaders['loading'].');}'."\n";
            $css .= '#OutoftheBox .loading.upload{    background-image: url('.$this->loaders['upload'].');}'."\n";
            $css .= '#OutoftheBox .loading.error{  background-image: url('.$this->loaders['error'].');}'."\n";
            $css .= '#OutoftheBox .no_results{  background-image: url('.$this->loaders['no_results'].');}'."\n";
        }

        $css .= "
iframe[src*='outofthebox'] {
        background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 512 512'%3E%3Cdefs/%3E%3Cdefs%3E%3ClinearGradient id='a' x1='48' x2='467.2' y1='259.7' y2='259.7' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%236d276b'/%3E%3Cstop offset='.3' stop-color='%236b2669'/%3E%3Cstop offset='.5' stop-color='%23632464'/%3E%3Cstop offset='.7' stop-color='%2355215a'/%3E%3Cstop offset='.7' stop-color='%23522058'/%3E%3C/linearGradient%3E%3ClinearGradient id='b' x1='365.3' x2='39.3' y1='41.5' y2='367.5' xlink:href='%23a'/%3E%3C/defs%3E%3Cg style='isolation:isolate'%3E%3Cpath fill='url(%23a)' d='M272 26a28 28 0 00-29 0L62 131a28 28 0 00-14 24v209a28 28 0 0014 25l181 105a28 28 0 0029 0l181-105a28 28 0 0014-25V155'/%3E%3Cpath fill='url(%23b)' d='M467 155a28 28 0 00-14-24L272 26a28 28 0 00-29 0L62 131a28 28 0 00-14 24v209a28 28 0 0014 25z'/%3E%3Cpath fill='%23fff' d='M115 230s5-36 40-55 59-5 59-5 19-18 35-22c0 0 10-5 10 4v19a6 6 0 01-3 6 66 66 0 00-30 26s-4 5-9 2c0 0-32-25-62 7 0 0-11 11-10 32 0 0 2 9-5 10s-34 8-33 40c0 0 3 33 39 33h81v-43h-25s-9 1-7-7a10 10 0 011-3l53-65s4-4 8-2a7 7 0 012 6v138s1 6-5 6h-96s-56 5-77-42c0 0-23-51 34-85zM270 150s-1-7 9-8c0 0 71-3 100 67 0 0 56 15 56 74 0 0 2 74-73 74h-83s-9 2-9-7v-16s-1-6 7-7h81s45 2 47-43c0 0 3-41-43-48 0 0-10 1-10-8s-14-40-50-53v60h26s9 0 7 9l-54 66s-9 9-11-3z'/%3E%3C/g%3E%3C/svg%3E\");
background-repeat: no-repeat;
        background-position: center center;
        background-size: 128px;
    }

    \n";

        $css .= $this->get_basic_style_css();

        $css_minified = \TheLion\OutoftheBox\Helpers::compress_css($css);

        \file_put_contents(self::$css_path, $css_minified);
    }

    public function get_basic_style_css()
    {
        $css = file_get_contents($this->css_template_path);

        return preg_replace_callback('/%(.*)%/iU', [&$this, 'fill_placeholder_styles'], $css);
    }

    public function fill_placeholder_styles($matches)
    {
        if (isset($this->colors[$matches[1]])) {
            return $this->colors[$matches[1]];
        }

        return 'initial';
    }

    public static function reset_custom_css()
    {
        @unlink(self::$css_path);
    }
}
