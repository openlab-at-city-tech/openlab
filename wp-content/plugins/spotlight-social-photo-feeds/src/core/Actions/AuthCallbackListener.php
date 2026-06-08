<?php

namespace RebelCode\Spotlight\Instagram\Actions;

use Exception;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\IgApi\AccessToken;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use RebelCode\Spotlight\Instagram\IgApi\IgApiClient;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\Wp\PostType;

/**
 * Listens for requests from the auth server to insert connected accounts into the database.
 *
 * @since 0.1
 */
class AuthCallbackListener
{
    public const NONCE_ACTION = 'sli_connect_account';

    /**
     * @since 0.1
     *
     * @var IgApiClient
     */
    protected $api;

    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $cpt;

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $businessAuthUrl;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param IgApiClient $api             The API client.
     * @param PostType    $cpt             The accounts post type.
     * @param string      $businessAuthUrl The URL for the business auth dialog.
     */
    public function __construct(IgApiClient $api, PostType $cpt, string $businessAuthUrl)
    {
        $this->api = $api;
        $this->cpt = $cpt;
        $this->businessAuthUrl = $businessAuthUrl;
    }

    /**
     * @since 0.1
     */
    public function __invoke()
    {
        if (!isset($_GET['sli_connect'])) {
            return;
        }

        $nonce = filter_input(INPUT_GET, 'nonce', FILTER_DEFAULT);
        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            status_header(401);
            wp_die('You are not allowed to do that.', '401 Unauthorized');
        }

        if (!current_user_can('manage_options')) {
            status_header(403);
            wp_die('You do not have admin permissions to connect Spotlight accounts.', '403 Forbidden');
        }

        try {
            // Get access token
            $tokenData = $_GET['access_token'];
            $tokenCode = $tokenData['code'];
            $tokenExpiry = $tokenData['expiry'];
            $token = new AccessToken($tokenCode, $tokenExpiry);

            // Check if connecting a business account
            $isBusiness = filter_input(INPUT_GET, 'business', FILTER_VALIDATE_BOOLEAN);

            if ($isBusiness) {
                // BUSINESS ACCOUNTS
                //--------------------
                $version = filter_input(INPUT_GET, 'version', FILTER_DEFAULT);

                if (intval($version) >= 2) {
                    // VERSION 2
                    $userId = filter_input(INPUT_GET, 'user_id', FILTER_DEFAULT);
                    $account = $this->api->getGraphApi()->getAccountForUser($userId, $token);
                } else {
                    // VERSION 1
                    $pageId = filter_input(INPUT_GET, 'page_id');
                    $pageName = filter_input(INPUT_GET, 'page_name');
                    $account = $this->api->getGraphApi()->getAccountForPage($pageId, $token);
                }

                // IF NO ACCOUNT FOUND
                if ($account === null) {
                    $message = sprintf(
                        '%s does not have an associated Instagram account',
                        "<strong>{$pageName}</strong>"
                    );

                    $dieHtml = sprintf(
                        '<p>%2$s</p><p><a href="%1$s">%3$s</a></p>',
                        $this->businessAuthUrl,
                        $message,
                        'Choose another page'
                    );

                    wp_die($dieHtml, 'Spotlight Instagram - Connection Failed');
                }
            } else {
                // PERSONAL ACCOUNTS
                //--------------------
                $user = $this->api->getBasicApi()->getTokenUser($token);
                $account = new IgAccount($user, $token);
            }

            $accountId = AccountPostType::insertOrUpdate($this->cpt, $account);

            // Notify parent window of successful connection
            ?>
            <script type="text/javascript">
                setTimeout(function() {
                    if (window.opener && window.opener.SliAccountManagerState) {
                        window.opener.SliAccountManagerState.connectSuccess = true;
                        window.opener.SliAccountManagerState.connectedId = <?= $accountId ?? 'null' ?>;
                    }
                    window.close();
                }, 500);
            </script>
            <?php

            die;
        } catch (Exception $exception) {
            ErrorLog::exception($exception);
            $html = '<h1>Whoops!</h1><p>Something went wrong while trying to connect your account:</p>';
            $html .= '<pre style="white-space: pre-wrap">' . $exception->getMessage() . '</pre>';

            wp_die($html, 'Spotlight Instagram - Connection Failed');
        }
    }
}
