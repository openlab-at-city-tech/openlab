<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use RebelCode\Iris\Store;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Endpoint handler for deleting accounts.
 */
class DeleteAccountMediaEndpoint extends AbstractEndpointHandler
{
    /** @var PostType */
    protected $accountsCpt;

    /** @var Store */
    protected $store;

    /** Constructor */
    public function __construct(PostType $accountsCpt, Store $store)
    {
        $this->accountsCpt = $accountsCpt;
        $this->store = $store;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        if (!RestRequest::has_param($request, 'id')) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request', ['status' => 400]);
        }

        $id = $request->get_param('id');
        $accountPost = $this->accountsCpt->get($id);

        if ($accountPost === null) {
            return new WP_Error('sli_account_not_found', "Account with ID {$id} not found", ['status' => 404]);
        }

        $account = AccountPostType::fromWpPost($accountPost);
        $source = UserSource::create($account->user->username, $account->user->type);

        $this->store->deleteForSources([$source]);

        return new WP_REST_Response(['ok' => 1]);
    }
}
