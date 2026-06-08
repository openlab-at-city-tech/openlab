<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Spotlight\Instagram\ErrorLog;
use Exception;

class DevSimError
{
    public function __invoke()
    {
        $nonce = sanitize_text_field($_POST['sli_dev_sim_error'] ?? '');
        if (!$nonce) {
            return;
        }

        if (!wp_verify_nonce($nonce, 'sli_dev_sim_error')) {
            wp_die('You cannot do that!', 'Unauthorized', [
                'back_link' => true,
            ]);
        }

        $message = sanitize_text_field($_POST['sli_dev_error_msg'] ?? '');
        $message = empty(trim($message)) ? 'Simulated error' : $message;

        ErrorLog::exception(new Exception($message));
    }
}
