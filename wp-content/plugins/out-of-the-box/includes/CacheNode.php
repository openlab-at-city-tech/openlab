<?php

namespace TheLion\OutoftheBox;

class CacheNode implements \Serializable
{
    /**
     * ID of the Node = ID of the Cached Entry.
     *
     * @var string
     */
    private $_id;

    /**
     * ID of the Account.
     *
     * @var mixed
     */
    private $_account_id;

    private $_rev;
    private $_shared_links;
    private $_media_info = [];
    private $_temporarily_link;

    public function __construct($params = null)
    {
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $this->{$key} = $val;
            }
        }
    }

    public function serialize()
    {
        $data = [
            '_id' => $this->_id,
            '_account_id' => $this->_account_id,
            '_rev' => $this->_rev,
            '_shared_links' => $this->_shared_links,
            '_media_info' => $this->_media_info,
            '_temporarily_link' => $this->_temporarily_link,
        ];

        return serialize($data);
    }

    public function unserialize($data)
    {
        $values = unserialize($data);
        foreach ($values as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function get_id()
    {
        return $this->_id;
    }

    public function get_account_id()
    {
        return $this->_account_id;
    }

    public function get_rev()
    {
        return $this->_rev;
    }

    public function set_rev($rev)
    {
        return $this->_rev = $rev;
    }

    public function add_temporarily_link($link, $expires = null)
    {
        if (empty($expires)) {
            $expires = time() + (4 * 60 * 60);
        }

        $this->_temporarily_link = [
            'url' => $link,
            'expires' => $expires,
        ];
    }

    public function get_temporarily_link()
    {
        if (!isset($this->_temporarily_link['url']) || empty($this->_temporarily_link['url'])) {
            return false;
        }

        if (!(empty($this->_temporarily_link['expires'])) && $this->_temporarily_link['expires'] < time() + 60) {
            return false;
        }

        // Update URL so that it directly points to the content
        return str_replace('www.dropbox.com', 'dl.dropboxusercontent.com', $this->_temporarily_link['url']);
    }

    /**
     * @param \Kunnu\Dropbox\Models\FileLinkMetaData|\Kunnu\Dropbox\Models\FolderLinkMetaData $shared_link_info
     */
    public function add_shared_link($shared_link_info)
    {
        $this->_shared_links[$shared_link_info->getLinkPermissions()->getResolvedVisibility()] = [
            'url' => str_replace('?dl=0', '', $shared_link_info->getUrl()),
            //'permissions' => $shared_link_info->getLinkPermissions(),
            'expires' => $shared_link_info->getExpires(),
        ];

        return $this->get_shared_link($shared_link_info->getLinkPermissions()->getResolvedVisibility());
    }

    public function get_shared_link($visibility = 'public')
    {
        if (!isset($this->_shared_links[$visibility])) {
            return false;
        }

        if (!(empty($this->_shared_links[$visibility]['expires'])) && $this->_shared_links[$visibility]['expires'] < time() + 60) {
            return false;
        }

        return $this->_shared_links[$visibility]['url'];
    }

    /**
     * @param \Kunnu\Dropbox\Models\MediaInfo $media_info
     */
    public function add_media_info($media_info)
    {
        $media_data = $media_info->getMediaMetadata();
        if (!($media_data instanceof \Kunnu\Dropbox\Models\MediaMetadata)) {
            return $this->_media_info;
        }

        $dimensions = $media_data->getDimensions();
        if (!empty($dimensions)) {
            $this->_media_info['width'] = $dimensions['width'];
            $this->_media_info['height'] = $dimensions['height'];
        }

        $time_taken = $media_data->getTimeTaken();
        if (!empty($time_taken)) {
            $this->_media_info['time'] = $time_taken->getTimestamp();
        }

        if ($media_data instanceof \Kunnu\Dropbox\Models\VideoMetadata) {
            $this->_media_info['duration'] = $media_data->getDuration();
        }

        return $this->_media_info;
    }

    public function get_media_info($key = null)
    {
        if (null === $key) {
            return $this->_media_info;
        }

        if (!isset($this->_media_info[$key])) {
            return null;
        }

        return $this->_media_info[$key];
    }
}
