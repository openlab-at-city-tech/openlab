<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class GravityPDF
{
    public function init()
    {
        if (false === class_exists('GFPDF_Core')) {
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
                'Yes' => __('Yes'),
                'No' => __('No'),
            ],
            'std' => __('No'),
        ];

        global $OutoftheBox;

        $main_account = $OutoftheBox->get_accounts()->get_primary_account();

        $account_id = '';
        if (!empty($main_account)) {
            $account_id = $main_account->get_id();
        }

        $fields['outofthebox_save_to_account_id'] = [
            'id' => 'outofthebox_save_to_account_id',
            'name' => '[DROPBOX] Account ID',
            'desc' => 'Account ID where the PDFs need to be stored. E.g. <code>'.$account_id.'</code>',
            'type' => 'text',
            'std' => $account_id,
        ];

        $fields['outofthebox_save_to_dropbox_path'] = [
            'id' => 'outofthebox_save_to_dropbox_path',
            'name' => '[DROPBOX] Path',
            'desc' => 'Full path where the PDFs need to be stored. E.g. <code>/path/to/folder</code>',
            'type' => 'text',
            'std' => '',
        ];

        return $fields;
    }

    public function outofthebox_post_save_pdf($pdf_path, $filename, $settings, $entry, $form)
    {
        global $OutoftheBox;
        $processor = $OutoftheBox->get_processor();

        if (!isset($settings['outofthebox_save_to_dropbox']) || 'No' === $settings['outofthebox_save_to_dropbox']) {
            return false;
        }

        if (!isset($settings['outofthebox_save_to_account_id'])) {
            // Fall back for older PDF configurations
            $settings['outofthebox_save_to_account_id'] = $OutoftheBox->get_accounts()->get_primary_account()->get_id();
        }

        $account_id = apply_filters('outofthebox_gravitypdf_set_account_id', $settings['outofthebox_save_to_account_id'], $settings, $entry, $form, $processor);

        $requested_account = $processor->get_accounts()->get_account_by_id($account_id);

        if (null !== $requested_account) {
            $processor->set_current_account($requested_account);
        } else {
            error_log(sprintf("[WP Cloud Plugin message]: Dropbox account (ID: %s) as it isn't linked with the plugin", $account_id));
            die();
        }

        $client = $processor->get_app();

        $upload_path = \TheLion\OutoftheBox\Helpers::clean_folder_path($settings['outofthebox_save_to_dropbox_path'].'/'.$entry['id'].'-'.$filename);

        try {
            $upload = new \TheLion\OutoftheBox\Upload($processor);
            $result = $upload->do_upload_to_dropbox($pdf_path, $upload_path);
        } catch (\Exception $ex) {
            return false;
        }

        return $result;
    }
}

new GravityPDF();
