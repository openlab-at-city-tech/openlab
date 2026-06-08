<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Actions;

use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaItem;
use RebelCode\Spotlight\Instagram\Engine\Data\Item\MediaType;

/**
 * Listens for Instagram image requests from the UI client, fetches the corresponding image and outputs it to the
 * browser.
 */
class IgImageProxy
{
    public const IMG_PARAM = 'sli-img';
    public const SIZE_PARAM = 'size';
    public const USERAGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36';
    public const TIMEOUT = 10;
    public const CONTENT_TYPE = 'image/jpeg';

    public function __invoke()
    {
        $shortcode = filter_input(INPUT_GET, static::IMG_PARAM);
        $size = filter_input(INPUT_GET, static::SIZE_PARAM);
        if (empty($shortcode) || empty($size)) {
            return;
        }

        $size = strtolower($size);
        $size = $size === 's' ? 't' : $size;

        $url = "https://www.instagram.com/p/$shortcode/media/?size=$size";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, static::USERAGENT);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, static::TIMEOUT);
        $response = curl_exec($ch);
        curl_close($ch);

        status_header(302);
        header('Content-type: ' . static::CONTENT_TYPE);
        echo $response;
        exit;
    }

    public static function getUrl(string $shortcode, string $size): string
    {
        return site_url() . '?' . static::IMG_PARAM . '=' . $shortcode . '&' . static::SIZE_PARAM . '=' . $size;
    }

    public static function itemWithProxyImages(Item $item): Item
    {
        $type = $item->get(MediaItem::MEDIA_TYPE);

        switch ($type) {
            case MediaType::IMAGE:
                $shortcode = $item->get(MediaItem::SHORTCODE);
                if ($shortcode) {
                    $item = $item->with(MediaItem::MEDIA_URL, static::getUrl($shortcode, 'l'));
                }
                break;

            case MediaType::ALBUM:
                $children = $item->get(MediaItem::CHILDREN, []);

                foreach ($children as $idx => $child) {
                    $type = $child[MediaItem::MEDIA_TYPE] ?? null;
                    $shortcode = $child[MediaItem::SHORTCODE] ?? null;

                    if ($type === MediaType::IMAGE && !empty($shortcode)) {
                        $children[$idx][MediaItem::MEDIA_URL] = IgImageProxy::getUrl($shortcode, 'l');
                    }
                }

                $item = $item->with(MediaItem::CHILDREN, $children);
                break;
        }

        return $item;
    }
}
