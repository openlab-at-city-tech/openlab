<?php

namespace TheLion\OutoftheBox;

use TheLion\OutoftheBox\API\Dropbox\Dropbox;
use TheLion\OutoftheBox\API\Dropbox\DropboxApp;

class App
{
    /**
     * @var string
     */
    private $_access_token;

    /**
     * @var string
     */
    private $_root_namespace_id = '';

    /**
     * @var string
     */
    private $_account_type = '';

    /**
     * @var bool
     */
    private $_own_app = false;

    /**
     * @var string
     */
    private $_app_key = 'm3n3zyvyr59cdjb';

    /**
     * @var string
     */
    private $_app_secret = 'eu73x5upk7ehes4';

    /**
     * @var string
     */
    private $_identifier;

    /**
     * @var \TheLion\OutoftheBox\API\Dropbox\Dropbox
     */
    private $_client;

    /**
     * @var \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     */
    private $_client_app;

    /**
     * We don't save your data or share it.
     * This script just simply creates a redirect with your id and secret to Dropbox and returns the created token.
     * It is exactly the same script as the _authorizeApp.php file in the includes folder of the plugin,
     * and is used for an easy and one-click authorization process that will always work!
     *
     * @var string
     */
    private $_redirect_uri = 'https://www.wpcloudplugins.com:443/out-of-the-box/_AuthorizeApp.php';

    /**
     * @var \TheLion\OutoftheBox\Processor
     */
    private $_processor;

    public function __construct(Processor $processor)
    {
        $this->_processor = $processor;

        require_once OUTOFTHEBOX_ROOTDIR.'/vendors/dropbox-sdk/vendor/autoload.php';

        // Call back for refresh token function in SDK client
        add_action('out-of-the-box-refresh-token', [$this, 'refresh_token'], 10, 1);

        $own_key = $this->get_processor()->get_setting('dropbox_app_key');
        $own_secret = $this->get_processor()->get_setting('dropbox_app_secret');

        if (
                (!empty($own_key))
                && (!empty($own_secret))
        ) {
            $this->_app_key = $this->get_processor()->get_setting('dropbox_app_key');
            $this->_app_secret = $this->get_processor()->get_setting('dropbox_app_secret');
            $this->_own_app = true;
        }

        // Set right redirect URL
        $this->set_redirect_uri();
    }

    public function process_authorization()
    {
        if (!empty($_REQUEST['state'])) {
            $state = (strtr($_REQUEST['state'], '-_~', '+/='));

            $csrfToken = $state;
            $urlState = null;

            $splitPos = strpos($state, '|');

            if (false !== $splitPos) {
                $csrfToken = substr($state, 0, $splitPos);
                $urlState = substr($state, $splitPos + 1);
            }
            $redirectto = base64_decode($urlState);

            if (false === strpos($redirectto, 'outofthebox_authorization')) {
                return false;
            }
        } else {
            return false;
        }

        $this->get_processor()->reset_complete_cache();

        $redirect = admin_url('admin.php?page=OutoftheBox_settings');
        if (isset($_GET['network'])) {
            $redirect = network_admin_url('admin.php?page=OutoftheBox_network_settings');
        }

        if (isset($_GET['code'])) {
            $access_token = $this->create_access_token();
            // Close oAuth popup and refresh admin page. Only possible with inline javascript.. Only possible with inline javascript.
            echo '<script type="text/javascript">window.opener.parent.location.href = "'.$redirect.'"; window.close();</script>';

            exit();
        }

        return false;
    }

    public function can_do_own_auth()
    {
        $blog_url = parse_url(admin_url());

        return 'https' === $blog_url['scheme'] || 'localhost' === $blog_url['host'];
    }

    public function has_plugin_own_app()
    {
        return $this->_own_app;
    }

    public function get_auth_url($params = [])
    {
        $auth_helper = $this->get_client()->getAuthHelper();

        if ($this->get_processor()->is_network_authorized()) {
            $redirect = network_admin_url('admin.php?page=OutoftheBox_network_settings&action=outofthebox_authorization&network=1');
        } else {
            $redirect = admin_url('admin.php?page=OutoftheBox_settings&action=outofthebox_authorization');
        }

        $encodedredirect = strtr(base64_encode($redirect), '+/=', '-_~');

        return $auth_helper->getAuthUrl($this->get_redirect_uri(), $params, $encodedredirect);
    }

    public function start_client(Account $account = null)
    {
        return $this->get_client($account);
    }

    public function refresh_token(Account $account = null)
    {
        $authorization = $account->get_authorization();
        $access_token = $authorization->get_access_token();

        if (!flock($authorization->get_token_file_handle(), LOCK_EX | LOCK_NB)) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Wait till another process has renewed the Authorization Token'));

            /*
             * If the file cannot be unlocked and the last time
             * it was modified was 1 minute, assume that
             * the previous process died and unlock the file manually
             */
            $requires_unlock = ((filemtime($authorization->get_token_location()) + 60) < (time()));

            // Temporarily workaround when flock is disabled. Can cause problems when plugin is used in multiple processes
            if (false !== strpos(ini_get('disable_functions'), 'flock')) {
                $requires_unlock = false;
            }

            if ($requires_unlock) {
                $authorization->unlock_token_file();
            }

            if (flock($authorization->get_token_file_handle(), LOCK_SH)) {
                clearstatcache();
                rewind($authorization->get_token_file_handle());
                $token = fread($authorization->get_token_file_handle(), filesize($authorization->get_token_location()));
                $decrypted_token = Helpers::decrypt($token);
                $token_object = unserialize($decrypted_token);
                error_log('[WP Cloud Plugin message]: '.sprintf('New Authorization Token has been received by another process.'));
                $this->_client->setAccessToken($token_object);
                $authorization->unlock_token_file();

                return $this->_client;
            }
        }

        // Stop if we need to get a new AccessToken but somehow ended up without a refreshtoken
        $refresh_token = $access_token->getRefreshToken();

        if (empty($refresh_token)) {
            error_log('[WP Cloud Plugin message]: '.sprintf('No Refresh Token found during the renewing of the current token. We will stop the authorization completely.'));
            $authorization->set_is_valid(false);
            $authorization->unlock_token_file();
            $this->revoke_token($account);

            return false;
        }

        // Refresh token
        try {
            $new_accesstoken = $this->_client->getAuthHelper()->refreshToken();

            // Store the new token
            $authorization->set_access_token($new_accesstoken);
            $authorization->unlock_token_file();
            $this->get_client()->setAccessToken($new_accesstoken);

            if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()]))) {
                wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()]);
            }
        } catch (\Exception $ex) {
            $authorization->set_is_valid(false);
            $authorization->unlock_token_file();
            error_log('[WP Cloud Plugin message]: '.sprintf('Cannot refresh Authorization Token'));

            if (!wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()])) {
                wp_schedule_event(time(), 'daily', 'outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()]);
            }

            $this->get_processor()->reset_complete_cache();

            throw $ex;
        }

        return $this->_client;
    }

    public function create_access_token()
    {
        try {
            $code = $_REQUEST['code'];
            $state = $_REQUEST['state'];

            //Fetch the AccessToken
            $accessToken = $this->get_client()->getAuthHelper()->getAccessToken($code, $state, $this->get_redirect_uri());
            $this->_client->setAccessToken($accessToken);

            $account_data = $this->get_client()->getCurrentAccount();
            $root_info = $account_data->getRootInfo();
            $root_namespace_id = $root_info['root_namespace_id'];

            $account = new Account($account_data->getAccountId(), $account_data->getDisplayName(), $account_data->getEmail(), $root_namespace_id, $account_data->getAccountType(), $account_data->getProfilePhotoUrl());
            $account->get_authorization()->set_access_token($accessToken);
            $account->get_authorization()->unlock_token_file();

            if ($account_data->emailIsVerified()) {
                $account->set_is_verified(true);
            }

            $this->get_accounts()->add_account($account);

            delete_transient('outofthebox_'.$account->get_id().'_is_authorized');
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Cannot generate Access Token: %s', $ex->getMessage()));

            return new \WP_Error('broke', esc_html__('Error communicating with API:', 'wpcloudplugins').$ex->getMessage());
        }

        return true;
    }

    public function revoke_token(Account $account)
    {
        error_log('[WP Cloud Plugin message]: '.'Lost authorization');

        // Reset Private Folders Back-End if the account it is pointing to is deleted
        $private_folders_data = $this->get_processor()->get_setting('userfolder_backend_auto_root', []);
        if (is_array($private_folders_data) && isset($private_folders_data['account']) && $private_folders_data['account'] === $account->get_id()) {
            $this->get_processor()->set_setting('userfolder_backend_auto_root', null);
        }

        $this->get_processor()->reset_complete_cache();

        if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()]))) {
            wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification', ['account_id' => $account->get_id()]);
        }

        $this->get_processor()->get_main()->send_lost_authorisation_notification($account->get_id());

        try {
            $this->get_client($account)->getAuthHelper()->revokeAccessToken();
            $this->get_accounts()->remove_account($account->get_id());
        } catch (\Exception $ex) {
            error_log('[Out-of-the-Box  message]: '.$ex->getMessage());
        }

        delete_transient('outofthebox_'.$account->get_id().'_is_authorized');
    }

    public function get_app_key()
    {
        return $this->_app_key;
    }

    public function get_app_secret()
    {
        return $this->_app_secret;
    }

    public function set_app_key($_app_key)
    {
        $this->_app_key = $_app_key;
    }

    public function set_app_secret($_app_secret)
    {
        $this->_app_secret = $_app_secret;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        return $this->_processor;
    }

    /**
     * @param null|mixed $account
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Dropbox
     */
    public function get_client($account = null)
    {
        if (empty($this->_client)) {
            $this->_client = new Dropbox($this->get_client_app($account), ['persistent_data_store' => new \TheLion\OutoftheBox\API\Dropbox\Store\DatabasePersistentDataStore()]);
        }

        if (!empty($account)) {
            $this->_client->setAccessToken($account->get_authorization()->get_access_token());
        }

        return $this->_client;
    }

    /**
     * @param null|mixed $account
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\DropboxApp
     */
    public function get_client_app($account = null)
    {
        if (empty($this->_client_app)) {
            if (!empty($account)) {
                $this->_client_app = new DropboxApp($this->get_app_key(), $this->get_app_secret(), $account->get_authorization()->get_access_token());
            } else {
                $this->_client_app = new DropboxApp($this->get_app_key(), $this->get_app_secret());
            }
        }

        return $this->_client_app;
    }

    /**
     * @return \TheLion\OutoftheBox\Accounts
     */
    public function get_accounts()
    {
        return $this->get_processor()->get_main()->get_accounts();
    }

    public function get_redirect_uri()
    {
        return $this->_redirect_uri;
    }

    public function set_redirect_uri()
    {
        if ($this->can_do_own_auth() && $this->has_plugin_own_app()) {
            $this->_redirect_uri = admin_url('admin.php?page=OutoftheBox_settings&action=outofthebox_authorization');
            if (isset($_GET['network'])) {
                $this->_redirect_uri = network_admin_url('admin.php?page=OutoftheBox_network_settings&action=outofthebox_authorization');
            }
        }
    }
}
