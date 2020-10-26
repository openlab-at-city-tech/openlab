<?php

namespace TheLion\OutoftheBox;

class User
{
    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;
    private $_can_view = false;
    private $_can_preview = false;
    private $_can_download = false;
    private $_can_download_zip = false;
    private $_can_delete_files = false;
    private $_can_delete_folders = false;
    private $_can_rename_files = false;
    private $_can_rename_folders = false;
    private $_can_add_folders = false;
    private $_can_create_document = false;
    private $_can_upload = false;
    private $_can_move = false;
    private $_can_copy_files = false;
    private $_can_copy_folders = false;
    private $_can_share = false;
    private $_can_deeplink = false;

    public function __construct(Processor $_processor = null)
    {
        $this->_processor = $_processor;
        $this->_can_view = Helpers::check_user_role($this->get_processor()->get_shortcode_option('view_role'));

        if (false === $this->can_view()) {
            return;
        }

        $this->_can_preview = Helpers::check_user_role($this->get_processor()->get_shortcode_option('preview_role'));

        $this->_can_download = Helpers::check_user_role($this->get_processor()->get_shortcode_option('download_role'));
        $this->_can_download_zip = ('1' === $this->get_processor()->get_shortcode_option('can_download_zip')) && $this->can_download();

        if ('1' === $this->get_processor()->get_shortcode_option('delete')) {
            $this->_can_delete_files = Helpers::check_user_role($this->get_processor()->get_shortcode_option('deletefiles_role'));
            $this->_can_delete_folders = Helpers::check_user_role($this->get_processor()->get_shortcode_option('deletefolders_role'));
        }

        if ('1' === $this->get_processor()->get_shortcode_option('rename')) {
            $this->_can_rename_files = Helpers::check_user_role($this->get_processor()->get_shortcode_option('renamefiles_role'));
            $this->_can_rename_folders = Helpers::check_user_role($this->get_processor()->get_shortcode_option('renamefolders_role'));
        }

        $this->_can_add_folders = ('1' === $this->get_processor()->get_shortcode_option('addfolder')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('addfolder_role'));
        $this->_can_create_document = ('1' === $this->get_processor()->get_shortcode_option('create_document')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('create_document_role'));
        $this->_can_upload = ('1' === $this->get_processor()->get_shortcode_option('upload')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('upload_role'));
        $this->_can_move = ('1' === $this->get_processor()->get_shortcode_option('move')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('move_role'));

        if ('1' === $this->get_processor()->get_shortcode_option('copy')) {
            $this->_can_copy_files = Helpers::check_user_role($this->get_processor()->get_shortcode_option('copy_files_role'));
            $this->_can_copy_folders = Helpers::check_user_role($this->get_processor()->get_shortcode_option('copy_folders_role'));
        }

        $this->_can_share = ('1' === $this->get_processor()->get_shortcode_option('show_sharelink')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('share_role'));

        $this->_can_deeplink = ('1' === $this->get_processor()->get_shortcode_option('deeplink')) && Helpers::check_user_role($this->get_processor()->get_shortcode_option('deeplink_role'));
    }

    public function can_view()
    {
        return $this->_can_view;
    }

    public function can_preview()
    {
        return $this->_can_preview;
    }

    public function can_download()
    {
        return $this->_can_download;
    }

    public function can_download_zip()
    {
        return $this->_can_download_zip;
    }

    public function can_delete_files()
    {
        return $this->_can_delete_files;
    }

    public function can_delete_folders()
    {
        return $this->_can_delete_folders;
    }

    public function can_rename_files()
    {
        return $this->_can_rename_files;
    }

    public function can_rename_folders()
    {
        return $this->_can_rename_folders;
    }

    public function can_add_folders()
    {
        return $this->_can_add_folders;
    }

    public function can_create_document()
    {
        return $this->_can_create_document;
    }

    public function can_upload()
    {
        return $this->_can_upload;
    }

    public function can_move()
    {
        return $this->_can_move;
    }

    public function can_move_files()
    {
        return $this->can_move();
    }

    public function can_move_folders()
    {
        return $this->can_move();
    }

    public function can_copy_files()
    {
        return $this->_can_copy_files;
    }

    public function can_copy_folders()
    {
        return $this->_can_copy_folders;
    }

    public function can_share()
    {
        return $this->_can_share;
    }

    public function can_deeplink()
    {
        return $this->_can_deeplink;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }
}
