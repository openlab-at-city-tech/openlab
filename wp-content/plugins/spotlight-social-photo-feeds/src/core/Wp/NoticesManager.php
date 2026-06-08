<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Wp;

use RebelCode\Spotlight\Instagram\Utils\Functions;
use RebelCode\Spotlight\Instagram\Utils\Arrays;

class NoticesManager
{
    /** @var string */
    public $script;

    /** @var string */
    public $nonce;

    /** @var string */
    public $ajaxAction;

    /** @var Notice[] */
    public $notices;

    /** @var bool */
    protected $loadedScripts;

    /**
     * Constructor.
     *
     * @param Notice[] $notices
     */
    public function __construct(string $script, string $nonce, string $ajaxAction, array $notices = [])
    {
        $this->script = $script;
        $this->nonce = $nonce;
        $this->notices = [];
        $this->loadedScripts = false;
        $this->ajaxAction = $ajaxAction;

        Arrays::each($notices, [$this, 'register']);
    }

    /** Registers a notice */
    public function register(Notice $notice)
    {
        $this->notices[$notice->id] = $notice;
    }

    /** Shows a notice by ID */
    public function show(string $id)
    {
        $this->loadScripts();
        $notice = $this->notices[$id] ?? null;

        if ($notice !== null) {
            add_action('admin_notices', Functions::output([$notice, 'render']));
        }
    }

    /** Handles a notice dismissal request */
    public function handleAjax()
    {
        $id = isset($_POST['notice']) ? sanitize_text_field($_POST['notice']) : '';
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        if (!wp_verify_nonce($nonce, $this->nonce)) {
            status_header(400);
            echo __('Invalid nonce', 'sli');
        } else {
            $notice = $this->notices[$id] ?? null;
            if ($notice !== null && is_callable($notice->dismiss)) {
                ($notice->dismiss)();
            }
        }
    }

    /** Ensures the notices script is loaded */
    protected function loadScripts()
    {
        if ($this->loadedScripts) {
            return;
        }

        if (did_action('admin_enqueue_scripts')) {
            $this->loadedScripts = true;
            $this->enqueueScripts();
        } else {
            add_action('admin_enqueue_scripts', function () {
                $this->enqueueScripts();
            });
        }
    }

    /** Actually enqueues the script and localizes it */
    public function enqueueScripts()
    {
        wp_enqueue_script('sli-admin-notices', $this->script, ['jquery', 'wp-element', 'wp-i18n'], SL_INSTA_VERSION, true);

        wp_localize_script('sli-admin-notices', 'SliNoticesL10n', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'action' => $this->ajaxAction,
            'nonce' => wp_create_nonce($this->nonce),
        ]);
    }
}
