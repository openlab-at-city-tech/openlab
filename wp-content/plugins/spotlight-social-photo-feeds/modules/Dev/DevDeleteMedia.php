<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Iris\Store;

/**
 * Dev tool that deletes all media from the DB.
 */
class DevDeleteMedia
{
    const NONCE_PARAM = 'sli_delete_media';
    const NONCE_ACTION = 'sli_delete_media';
    const ID_PARAM = 'id';

    /** @var callable */
    protected $action;

    /** @var Store */
    protected $store;

    /** Constructor */
    public function __construct(callable $action, Store $store)
    {
        $this->action = $action;
        $this->store = $store;
    }

    /**
     * @since 0.1
     */
    public function __invoke()
    {
        $deleteNonce = $_GET[static::NONCE_PARAM] ?? $_POST[static::NONCE_PARAM] ?? null;
        if (!$deleteNonce) {
            return;
        }

        if (!wp_verify_nonce($deleteNonce, static::NONCE_ACTION)) {
            wp_die('You cannot do that!', 'Unauthorized', [
                'back_link' => true,
            ]);
        }

        $id = filter_input(INPUT_GET, self::ID_PARAM, FILTER_VALIDATE_INT) ? : null;

        if (empty($id)) {
            // Delete all
            $result = ($this->action)();

            add_action('admin_notices', function () use ($result) {
                if ($result === false) {
                    echo '<div class="notice notice-error"><p>WordPress failed to delete the media</p></div>';
                } else {
                    printf('<div class="notice notice-success"><p>Deleted %d records from the database</p></div>', $result);
                }
            });
        } else {
            // Delete single
            $post = get_post($id);
            if (!empty($post)) {
                $this->store->delete($id);
            }

            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            $page = $page ? : 1;

            wp_redirect(admin_url("admin.php?page=sli-dev&tab=posts&db_page=$page"));
            die;
        }
    }
}
