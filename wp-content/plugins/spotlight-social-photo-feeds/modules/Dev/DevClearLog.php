<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

/**
 * Dev tool that clears the WordPress debug.log file.
 *
 * @since 0.1
 */
class DevClearLog
{
    /**
     * @since 0.1
     */
    public function __invoke()
    {
        $resetDb = filter_input(INPUT_POST, 'sli_clear_log');
        if (!$resetDb) {
            return;
        }

        if (!wp_verify_nonce($resetDb, 'sli_clear_log')) {
            wp_die('You cannot do that!', 'Unauthorized', [
                'back_link' => true,
            ]);
        }

        @unlink(WP_CONTENT_DIR . '/debug.log');
    }
}
