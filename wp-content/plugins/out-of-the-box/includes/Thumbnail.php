<?php

namespace TheLion\OutoftheBox;

class Thumbnail
{
    /**
     * @var App
     */
    private $_app;

    /**
     * @var \Kunnu\Dropbox\Dropbox
     */
    private $_client;

    /**
     * @var Processor
     */
    private $_processor;

    /**
     * @var Entry
     */
    private $_entry;

    /**
     * @var int
     */
    private $_width;

    /**
     * @var int
     */
    private $_height;

    /**
     * @var bool
     */
    private $_crop = false;

    /**
     * @var string
     */
    private $_format = 'jpeg';

    /**
     *  How to resize and crop the image to achieve the desired size.
     *
     * @var string
     */
    private $_mode = 'fitone_bestfit';

    /**
     * @var string
     */
    private $_thumbnail_name;

    /**
     * @var string
     */
    private $_location_thumbnails;

    /**
     * @var string
     */
    private $_location_thumbnails_url;

    /**
     * @var string
     */
    private $_image_data;

    /**
     * Possible thumbnail formats for Dropbox API.
     *
     * @var array
     */
    private $_sizes = [
        'w2048h1536' => ['width' => 2048, 'height' => 1536],
        'w1024h768' => ['width' => 1024, 'height' => 768],
        'w960h640' => ['width' => 960, 'height' => 640],
        'w640h480' => ['width' => 640, 'height' => 480],
        'w480h320' => ['width' => 480, 'height' => 320],
        'w256h256' => ['width' => 256, 'height' => 256],
        'w128h128' => ['width' => 128, 'height' => 128],
        'w64h64' => ['width' => 64, 'height' => 64],
        'w32h32' => ['width' => 32, 'height' => 32],
    ];

    /**
     * Default size.
     *
     * @var string
     */
    private $_size = 'w2048h1536';

    /**
     * @var bool
     */
    private $_loading_thumb = false;

    public function __construct(Processor $_processor, Entry $entry, $width, $height, $crop = false, $format = 'jpeg', $imagedata = null, $loading_thumb = false)
    {
        $this->_app = $_processor->get_app();
        $this->_client = $this->_app->get_client();
        $this->_processor = $_processor;
        $this->_entry = $entry;
        $this->_width = round($width);
        $this->_height = round($height);
        $this->_crop = $crop;
        $this->_format = $format;
        $this->_location_thumbnails = OUTOFTHEBOX_CACHEDIR.'thumbnails/';
        $this->_location_thumbnails_url = OUTOFTHEBOX_CACHEURL.'thumbnails/';
        $this->_image_data = $imagedata;

        $this->_size = $this->select_size();
        $this->_thumbnail_name = $this->_get_entry()->get_id().'_'.$this->_size.'_c'.(($this->_crop) ? '1' : '0').'_'.$this->_format;
    }

    public function select_size()
    {
        $selected_size = 'w2048h1536';

        if (0.0 !== $this->_height) {
            foreach ($this->_sizes as $size => $dimensions) {
                $selected_size = $size;
                if ($dimensions['height'] <= $this->_height) {
                    return $selected_size;
                }
            }
        }

        if (0.0 !== $this->_width) {
            foreach ($this->_sizes as $size => $dimensions) {
                $selected_size = $size;
                if ($dimensions['width'] <= $this->_width) {
                    return $selected_size;
                }
            }
        }

        return $selected_size;
    }

    public function get_url()
    {
        $cached_entry = $this->_processor->get_cache()->get_node_by_id($this->_get_entry()->get_id());
        if (empty($cached_entry) && 'gif' !== $this->_get_entry()->get_extension()) {
            // No metadata avialable yet. Gifs don't have metadata available
            return $this->_build_thumbnail_url();
        }

        if ($this->does_thumbnail_exist()) {
            return $this->_get_location_thumbnail_url();
        }

        return $this->_build_thumbnail_url();
    }

    public function get_thumbnail_name()
    {
        return str_replace(':', '', $this->_thumbnail_name);
    }

    public function does_thumbnail_exist()
    {
        if (!file_exists($this->_get_location_thumbnail())) {
            return false;
        }

        if (filemtime($this->_get_location_thumbnail()) !== $this->_get_entry()->get_last_edited()) {
            return false;
        }

        if (filesize($this->_get_location_thumbnail()) < 1) {
            return false;
        }

        return $this->_get_location_thumbnail();
    }

    public function build_thumbnail()
    {
        @set_time_limit(60); //Downloading the thumbnail can take a while

        try {
            $thumbnail = $this->_get_client()->getThumbnail($this->_get_entry()->get_path(), $this->_size, $this->_format, $this->_mode);
            $this->_image_data = $thumbnail->getContents();
            unset($thumbnail);
        } catch (\Exception $ex) {
            // TO DO LOG
            error_log($ex->getMessage());

            return false;
        }

        return $this->_create_thumbnail();
    }

    public function get_width()
    {
        return $this->_width;
    }

    public function get_height()
    {
        return $this->_height;
    }

    public function get_crop()
    {
        return $this->_crop;
    }

    public function get_format()
    {
        return $this->_format;
    }

    public function set_width($_width)
    {
        $this->_width = round((int) $_width);
    }

    public function set_height($_height)
    {
        $this->_height = round((int) $_height);
    }

    public function set_crop($_crop = false)
    {
        $this->_crop = (bool) $_crop;
    }

    public function set_format($_format)
    {
        $this->_format = $_format;
    }

    public function get_mode()
    {
        return $this->_mode;
    }

    public function get_size()
    {
        return $this->_size;
    }

    public function set_mode($_mode)
    {
        $this->_mode = $_mode;
    }

    public function set_size($_size)
    {
        $this->_size = $_size;
    }

    private function _build_thumbnail_url()
    {
        return OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-thumbnail&src='.$this->_thumbnail_name.'&account_id='.$this->_processor->get_current_account()->get_id();
    }

    private function _create_thumbnail()
    {
        // Create the requested thumbnail

        try {
            $this->_create_thumbnail_dir();

            $thumbnail_location = str_replace('_c1', '_c0', $this->_get_location_thumbnail());
            file_put_contents($thumbnail_location, $this->_get_image_data());
            touch($thumbnail_location, $this->_get_entry()->get_last_edited());

            if ($this->_crop) {
                $this->_crop_thumbnail();
            }

            return true;
        } catch (Exception $ex) {
            error_log($ex->getMessage());

            return false;
        }
    }

    private function _create_thumbnail_dir()
    {
        if (!file_exists($this->_get_location_thumbnails())) {
            @mkdir($this->_get_location_thumbnails(), 0755);
        } else {
            return true;
        }

        if (!is_writable($this->_get_location_thumbnails())) {
            @chmod($this->_get_location_thumbnails(), 0755);
        } else {
            return true;
        }

        return is_writable($this->_get_location_thumbnails());
    }

    private function _crop_thumbnail()
    {
        try {
            $php_thumb = $this->_load_phpthumb_object();
            $php_thumb->GenerateThumbnail();
            $php_thumb->CalculateThumbnailDimensions();
            $php_thumb->SetCacheFilename();
            $is_thumbnail_created = $php_thumb->RenderToFile($this->_get_location_thumbnail());
            unset($php_thumb);

            /* Set the modification date of the thumbnail to that of the entry
             * so we can check if a new thumbnail should be loaded */
            touch($this->_get_location_thumbnail(), $this->_get_entry()->get_last_edited());

            return $is_thumbnail_created;
        } catch (\Exception $ex) {
            // TO DO LOG
            error_log($ex->getMessage());

            return false;
        }
    }

    /**
     * @return phpThumb
     */
    private function _load_phpthumb_object()
    {
        if (!class_exists('\TheLion\OutoftheBox\phpthumb')) {
            try {
                require_once 'phpThumb/phpthumb.class.php';
            } catch (\Exception $ex) {
                // TO DO LOG
                die("Can't load PHPTHUMB Library");
            }
        }

        $this->_create_thumbnail_dir();

        $php_thumb = new \TheLion\OutoftheBox\phpthumb();
        $php_thumb->resetObject();
        $php_thumb->setParameter('config_temp_directory', $this->_get_location_thumbnails());
        $php_thumb->setParameter('config_cache_directory', $this->_get_location_thumbnails());
        $php_thumb->setParameter('config_output_format', $this->get_format());
        $php_thumb->setParameter('q', 75);
        $php_thumb->setParameter('h', max([$this->_width, $this->_height]));
        $php_thumb->setParameter('w', max([$this->_width, $this->_height]));
        $php_thumb->setParameter('zc', true);
        $php_thumb->setParameter('f', $this->get_format());
        $php_thumb->setParameter('bg', 'FFFFFF|0');
        $php_thumb->setParameter('ar', 'x');
        $php_thumb->setParameter('aoe', false);

        $php_thumb->setSourceData($this->_get_image_data());

        return $php_thumb;
    }

    /**
     * @return \Kunnu\Dropbox\Dropbox
     */
    private function _get_client()
    {
        return $this->_client;
    }

    /**
     * @return Cache\Node
     */
    private function _get_entry()
    {
        return $this->_entry;
    }

    private function _get_location_thumbnail()
    {
        $thumbnail_name = str_replace(['_jpeg', '_png'], ['.jpeg', '.png'], $this->get_thumbnail_name());

        return $this->_location_thumbnails.$thumbnail_name;
    }

    private function _get_location_thumbnail_url()
    {
        $thumbnail_name = str_replace(['_jpeg', '_png'], ['.jpeg', '.png'], $this->get_thumbnail_name());

        return $this->_location_thumbnails_url.$thumbnail_name;
    }

    private function _get_location_thumbnails()
    {
        return $this->_location_thumbnails;
    }

    private function _get_location_thumbnails_url()
    {
        return $this->_location_thumbnails_url;
    }

    private function _get_image_data()
    {
        return $this->_image_data;
    }

    private function _is_loading_thumb()
    {
        return $this->_loading_thumb;
    }
}
