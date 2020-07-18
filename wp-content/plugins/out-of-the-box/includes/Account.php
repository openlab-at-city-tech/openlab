<?php

namespace TheLion\OutoftheBox;

class Account
{
    /**
     * Account ID.
     *
     * @var string
     */
    private $_id;

    /**
     * Account Name.
     *
     * @var string
     */
    private $_name;

    /**
     * Account Email.
     *
     * @var string
     */
    private $_email;

    /**
     * Account profile picture (url).
     *
     * @var string
     */
    private $_image;

    /**
     * Kind of Account.
     *
     * @var string
     */
    private $_type;

    /**
     * The ID of the Account Root. Required when using a personal Dropbox Account with Team Folders.
     *
     * @var string
     */
    private $_root_namespace_id = '';

    /**
     * Is the account verified by Dropbox?
     * If not, you can't create shared links.
     *
     * @var bool
     */
    private $_is_verified = false;

    /**
     * $_authorization contains the authorization token for the linked Cloud storage.
     *
     * @var \TheLion\OutoftheBox\Authorization
     */
    private $_authorization;

    public function __construct($id, $name, $email, $root_namespace_id = null, $type = null, $image = null)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_email = $email;
        $this->_image = $image;
        $this->_root_namespace_id = $root_namespace_id;
        $this->_type = $type;
        $this->_authorization = new Authorization($this);
    }

    public function __sleep()
    {
        // Don't store authorization class in DB */
        $keys = get_object_vars($this);
        unset($keys['_authorization']);

        return array_keys($keys);
    }

    public function __wakeup()
    {
        $this->_authorization = new Authorization($this);
    }

    public function get_id()
    {
        return $this->_id;
    }

    public function get_name()
    {
        return $this->_name;
    }

    public function get_email()
    {
        return $this->_email;
    }

    public function get_image()
    {
        if (empty($this->_image)) {
            return OUTOFTHEBOX_ROOTPATH.'/css/images/dropbox_logo.png';
        }

        return $this->_image;
    }

    public function set_id($_id)
    {
        $this->_id = $_id;
    }

    public function set_name($_name)
    {
        $this->_name = $_name;
    }

    public function set_email($_email)
    {
        $this->_email = $_email;
    }

    public function set_image($_image)
    {
        $this->_image = $_image;
    }

    public function get_type()
    {
        return $this->_type;
    }

    public function set_type($_type)
    {
        $this->_type = $_type;
    }

    public function get_root_namespace_id()
    {
        return $this->_root_namespace_id;
    }

    public function set_root_namespace_id($root_namespace_id)
    {
        $this->_root_namespace_id = $root_namespace_id;
    }

    public function is_verified()
    {
        $transient_name = 'outofthebox_'.$this->get_id().'_verified';
        $account_info = get_transient($transient_name, false);

        if (false === $this->_is_verified && true !== $account_info) {
            global $OutoftheBox;
            $OutoftheBox->get_processor()->set_current_account($this);
            $account_info = $OutoftheBox->get_processor()->get_client()->get_account_info();

            if ($account_info->emailIsVerified()) {
                $this->_is_verified = true;
                set_transient($transient_name, true, DAY_IN_SECONDS);
            } else {
                set_transient($transient_name, false, DAY_IN_SECONDS);
            }
        }

        return $this->_is_verified;
    }

    public function set_is_verified($is_verified = true)
    {
        $this->_is_verified = $is_verified;
    }

    /**
     * @return \TheLion\OutoftheBox\StorageInfo
     */
    public function get_storage_info()
    {
        $transient_name = 'outofthebox_'.$this->get_id().'_driveinfo';
        $storage_info = get_transient($transient_name);

        if (false === $storage_info) {
            global $OutoftheBox;
            $OutoftheBox->get_processor()->set_current_account($this);
            $storage_info_data = $OutoftheBox->get_processor()->get_client()->get_account_space_info();

            $storage_info = new StorageInfo();
            $storage_info->set_quota_total($storage_info_data['allocation']['allocated']);
            $storage_info->set_quota_used($storage_info_data['used']);

            set_transient($transient_name, $storage_info, DAY_IN_SECONDS);
        }

        return $storage_info;
    }

    /**
     * @return \TheLion\OutoftheBox\Authorization
     */
    public function get_authorization()
    {
        return $this->_authorization;
    }
}
