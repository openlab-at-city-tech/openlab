<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class GravityPDF
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        if (false === get_option('gfpdf_current_version') && false === class_exists('GFPDF_Core')) {
            return;
        }

        add_action('gfpdf_post_save_pdf', [$this, 'outofthebox_post_save_pdf'], 10, 5);
        add_filter('gfpdf_form_settings_advanced', [$this, 'outofthebox_add_pdf_setting'], 10, 1);
    }

    /*
     * GravityPDF
     * Basic configuration in Form Settings -> PDF:
     *
     * Always Save PDF = YES
     * [DROPBOX] Export PDF = YES
     * [DROPBOX] Path = Full path where the PDFs need to be stored
     */

    public function outofthebox_add_pdf_setting($fields)
    {
        $fields['outofthebox_save_to_dropbox'] = [
            'id' => 'outofthebox_save_to_dropbox',
            'name' => '[DROPBOX] Export PDF',
            'desc' => 'Save the created PDF to Dropbox',
            'type' => 'radio',
            'options' => [
                'Yes' => esc_html__('Yes'),
                'No' => esc_html__('No'),
            ],
            'std' => esc_html__('No'),
        ];

        $main_account = $this->get_main()->get_accounts()->get_primary_account();

        $account_id = '';
        if (!empty($main_account)) {
            $account_id = $main_account->get_id();
        }

        $fields['outofthebox_save_to_account_id'] = [
            'id' => 'outofthebox_save_to_account_id',
            'name' => '[DROPBOX] Account ID',
            'desc' => 'Account ID where the PDFs need to be stored. E.g. <code>'.$account_id.'</code>. Or use <code>%upload_account_id%</code> for the Account ID for the upload location of the plugin Upload Box field.',
            'type' => 'text',
            'std' => $account_id,
        ];

        $fields['outofthebox_save_to_dropbox_path'] = [
            'id' => 'outofthebox_save_to_dropbox_path',
            'name' => '[DROPBOX] Path',
            'desc' => 'Full path where the PDFs need to be stored. E.g. <code>/path/to/folder</code>. Or use <code>%upload_folder_id%</code> for the Account ID for the upload location of the plugin Upload Box field.',
            'type' => 'text',
            'std' => '',
        ];

        return $fields;
    }

    public function outofthebox_post_save_pdf($pdf_path, $filename, $settings, $entry, $form)
    {
        if (!isset($settings['outofthebox_save_to_dropbox']) || 'No' === $settings['outofthebox_save_to_dropbox']) {
            return false;
        }

        if (!isset($settings['outofthebox_save_to_account_id'])) {
            // Fall back for older PDF configurations
            $settings['outofthebox_save_to_account_id'] = $this->get_main()->get_accounts()->get_primary_account()->get_id();
        }

        // Placeholders
        list($upload_account_id, $upload_folder_path) = $this->get_upload_location($entry, $form);

        if ((false !== strpos($settings['outofthebox_save_to_account_id'], '%upload_account_id%'))) {
            $settings['outofthebox_save_to_account_id'] = $upload_account_id;
        }

        if ((false !== strpos($settings['outofthebox_save_to_dropbox_path'], '%upload_folder_id%'))
        ) {
            $settings['outofthebox_save_to_dropbox_path'] = $upload_folder_path;
        }

        $account_id = apply_filters('outofthebox_gravitypdf_set_account_id', $settings['outofthebox_save_to_account_id'], $settings, $entry, $form, $this->get_processor());

        $requested_account = $this->get_processor()->get_accounts()->get_account_by_id($account_id);

        if (null !== $requested_account) {
            $this->get_processor()->set_current_account($requested_account);
        } else {
            error_log(sprintf("[WP Cloud Plugin message]: Dropbox account (ID: %s) as it isn't linked with the plugin", $account_id));

            exit();
        }

        $client = $this->get_processor()->get_app();

        $upload_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($settings['outofthebox_save_to_dropbox_path'].'/'.$entry['id'].'-'.$filename);

        try {
            $upload = new \TheLion\OutoftheBox\Upload($this->get_processor());
            $result = $upload->do_upload_to_dropbox($pdf_path, $upload_path);
        } catch (\Exception $ex) {
            return false;
        }

        return $result;
    }

    public function get_upload_location($entry, $form)
    {
        $account_id = '';
        $folder_path = '';

        if (!is_array($form['fields'])) {
            return [$account_id, $folder_path];
        }

        foreach ($form['fields'] as $field) {
            if ('outofthebox' !== $field->type) {
                continue;
            }

            if (!isset($entry[$field->id])) {
                continue;
            }

            $uploadedfiles = json_decode($entry[$field->id]);

            if ((null !== $uploadedfiles) && (count((array) $uploadedfiles) > 0)) {
                $first_entry = reset($uploadedfiles);

                $account_id = $first_entry->account_id;
                $requested_account = $this->get_processor()->get_accounts()->get_account_by_id($account_id);
                $this->get_processor()->set_current_account($requested_account);

                $cached_entry = $this->get_processor()->get_client()->get_entry($first_entry->hash, false);
                $folder_path = $cached_entry->get_parent();
            }
        }

        return [$account_id, $folder_path];
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->get_main()->get_processor();
    }

    /**
     * @return \TheLion\OutoftheBox\Main
     */
    public function get_main()
    {
        if (empty($this->_main)) {
            global $OutoftheBox;
            $this->_main = $OutoftheBox;
        }

        return $this->_main;
    }
}

new GravityPDF();
