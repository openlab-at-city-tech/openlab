<?php

namespace TheLion\OutoftheBox;

class Mediaplayer
{
    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    public function __construct(Processor $_processor)
    {
        $this->_processor = $_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    public function get_media_list()
    {
        $this->_folder = $this->get_processor()->get_client()->get_folder(null, true, true, false);

        if ((false !== $this->_folder)) {
            //Create Gallery array
            $this->mediaarray = $this->createMediaArray();

            if (count($this->mediaarray) > 0) {
                $response = json_encode($this->mediaarray);

                $cached_request = new CacheRequest($this->get_processor());
                $cached_request->add_cached_response($response);

                echo $response;
            }
        }

        die();
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

    public function createMediaArray()
    {
        $covers = [];
        $captions = [];

        // Add covers and Captions
        if ($this->_folder->has_children()) {
            foreach ($this->_folder->get_children() as $child) {
                if (!isset($child->extension)) {
                    continue;
                }

                if (in_array(strtolower($child->extension), ['png', 'jpg', 'jpeg'])) {
                    // Add images to cover array
                    $covers[$child->get_basename()] = $child;
                } elseif ('vtt' === strtolower($child->extension)) {
                    /**
                     * VTT files are supported for captions:.
                     *
                     * Filename: Videoname.Caption Label.Language.VTT
                     */
                    $caption_values = explode('.', $child->get_basename());

                    if (3 !== count($caption_values)) {
                        continue;
                    }

                    $video_name = $caption_values[0];

                    if (!isset($captions[$video_name])) {
                        $captions[$video_name] = [];
                    }

                    $captions[$video_name][] = [
                        'label' => $caption_values[1],
                        'language' => $caption_values[2],
                        'src' => OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-stream&OutoftheBoxpath='.rawurlencode($child->get_id()).'&dl=1&caption=1&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken(),
                    ];
                }
            }
        }

        $files = [];

        //Create Filelist array
        if ($this->_folder->has_children()) {
            foreach ($this->_folder->get_children() as $child) {
                if (($child->is_dir())) {
                    continue;
                }

                if ('audio' === $this->get_processor()->get_shortcode_option('mode')) {
                    $allowedextensions = ['mp3', 'm4a', 'ogg', 'oga', 'wav'];
                } else {
                    $allowedextensions = ['mp4', 'm4v', 'ogg', 'ogv', 'webmv', 'webm'];
                }

                if (empty($child->extension) || !in_array($child->extension, $allowedextensions)) {
                    continue;
                }

                $basename = $child->get_basename();
                $foldername = basename(dirname($child->get_path_display()));
                $extension = $child->get_extension();

                if (isset($covers[$basename])) {
                    $poster = $this->get_processor()->get_client()->get_thumbnail($covers[$basename], true, 480, 640);
                    $thumbnailsmall = $this->get_processor()->get_client()->get_thumbnail($covers[$basename], true, 64, 64);
                } elseif (isset($covers[$foldername])) {
                    $poster = $this->get_processor()->get_client()->get_thumbnail($covers[$foldername], true, 480, 640);
                    $thumbnailsmall = $this->get_processor()->get_client()->get_thumbnail($covers[$foldername], true, 64, 64);
                } else {
                    $poster = $this->get_processor()->get_client()->get_thumbnail($child, true, 256, 256);
                    $thumbnailsmall = $this->get_processor()->get_client()->get_thumbnail($child, true, 64, 64);
                }

                $folder_str = dirname($child->get_path_display());
                $folder_str = trim(str_replace('\\', '/', $folder_str), '/');
                $path = $folder_str.$basename;

                // combine same files with different extensions
                if (!isset($files[$path])) {
                    $source_url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-stream&OutoftheBoxpath='.rawurlencode($child->get_id()).'&lastpath='.rawurlencode($this->get_processor()->get_last_path()).'&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
                    if (('Yes' !== $this->get_processor()->get_setting('google_analytics'))) {
                        $cached_source_url = get_transient('outofthebox_stream_'.$child->get_id().'_'.$child->get_extension());
                        if (false !== $cached_source_url && false === filter_var($cached_source_url, FILTER_VALIDATE_URL)) {
                            $source_url = $cached_source_url;
                        }
                    }

                    $files[$path] = [
                        'title' => $basename,
                        'name' => $basename,
                        'path_display' => $child->get_path_display(),
                        'artist' => '',
                        'is_dir' => false,
                        'folder' => $folder_str,
                        'poster' => $poster,
                        'thumb' => $thumbnailsmall,
                        'size' => $child->get_size(),
                        'last_edited' => $child->get_last_edited(),
                        'last_edited_str' => $child->get_last_edited_str(),
                        'download' => (('1' === $this->get_processor()->get_shortcode_option('linktomedia')) && $this->get_processor()->get_user()->can_download()) ? str_replace('outofthebox-stream', 'outofthebox-download', $source_url) : false,
                        'source' => $source_url,
                        'captions' => isset($captions[$basename]) ? $captions[$basename] : [],
                        'type' => Helpers::get_mimetype($extension),
                        'extension' => $extension,
                        'height' => $child->get_media('height'),
                        'width' => $child->get_media('width'),
                        'duration' => $child->get_media('duration') * 1000, //ms to sec,
                        'linktoshop' => ('' !== $this->get_processor()->get_shortcode_option('linktoshop')) ? $this->get_processor()->get_shortcode_option('linktoshop') : false,
                    ];
                }
            }
        }

        return array_values($files);
    }
}
