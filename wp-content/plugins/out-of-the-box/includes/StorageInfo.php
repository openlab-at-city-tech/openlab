<?php

namespace TheLion\OutoftheBox;

class StorageInfo
{
    private $_quota_used;
    private $_quota_total;

    public function get_quota_used()
    {
        return Helpers::bytes_to_size_1024($this->_quota_used, 1);
    }

    public function get_quota_total()
    {
        if (empty($this->_quota_total)) {
            return __('Unlimited', 'wpcloudplugins');
        }

        return Helpers::bytes_to_size_1024($this->_quota_total, 1);
    }

    public function set_quota_used($_quota_used)
    {
        $this->_quota_used = $_quota_used;
    }

    public function set_quota_total($_quota_total)
    {
        $this->_quota_total = $_quota_total;
    }
}
