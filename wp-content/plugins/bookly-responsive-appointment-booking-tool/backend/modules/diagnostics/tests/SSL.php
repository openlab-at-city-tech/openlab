<?php

namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

/**
 * Class SSL
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class SSL extends Test
{
    protected $slug = 'check-ssl';

    public function __construct()
    {
        $this->title = __( 'Secure connection', 'bookly' );
        $this->description = __( 'Some Bookly integrations require HTTPS connection and won\'t work with your website if there is no valid SSL certificate installed on your web server.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        if ( ! is_ssl() ) {
            return false;
        }

        return empty( $this->errors );
    }
}