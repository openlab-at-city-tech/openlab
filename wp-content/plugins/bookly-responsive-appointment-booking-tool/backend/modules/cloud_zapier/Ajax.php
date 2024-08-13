<?php
namespace Bookly\Backend\Modules\CloudZapier;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Generate new API Key
     */
    public static function cloudZapierGenerateNewApiKey()
    {
        $api = Lib\Cloud\API::getInstance();
        $response = $api->getProduct( Lib\Cloud\Account::PRODUCT_ZAPIER )->generateNewApiKey();
        if ( $response !== false ) {
            wp_send_json_success( array(
                'api_key' => $response['api_key']
            ) );
        } else {
            wp_send_json_error( array( 'message' => current( $api->getErrors() ) ) );
        }
    }
}