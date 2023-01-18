<?php
namespace Bookly\Backend\Modules\CloudBilling;

use Bookly\Lib;
use Bookly\Backend\Components;

/**
 * Class Page
 * @package Bookly\Backend\Modules\CloudProducts
 */
class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        $cloud = Lib\Cloud\API::getInstance();
        if ( ! $cloud->account->loadProfile() ) {
            Components\Cloud\LoginRequired\Page::render( __( 'Bookly Cloud Billing', 'bookly' ), self::pageSlug() );
        } else {
            self::enqueueStyles( array(
                'alias' => array( 'bookly-backend-globals', ),
            ) );

            self::enqueueScripts( array(
                'module' => array( 'js/cloud-billing.js' => array( 'bookly-backend-globals' ), ),
            ) );

            $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::CLOUD_PURCHASES );

            $invoice_data = Lib\Cloud\API::getInstance()->account->getInvoiceData();

            wp_localize_script( 'bookly-cloud-billing.js', 'BooklyL10n', array(
                'csrfToken'   => Lib\Utils\Common::getCsrfToken(),
                'zeroRecords' => __( 'No records for selected period.', 'bookly' ),
                'processing'  => __( 'Processing...', 'bookly' ),
                'datePicker'  => Lib\Utils\DateTime::datePickerOptions(),
                'dateRange'   => Lib\Utils\DateTime::dateRangeOptions( array( 'lastMonth' => __( 'Last month', 'bookly' ), ) ),
                'invoice'     => array(
                    'button' => __( 'Invoice', 'bookly' ),
                    'alert'  => __( 'To generate an invoice you should fill in company information in Bookly Cloud settings -> Invoice', 'bookly' ),
                    'link'   => $cloud->account->getInvoiceLink(),
                    'valid'  => isset ( $invoice_data['company_name'], $invoice_data['company_address'] ) && $invoice_data['company_name'] != '' && $invoice_data['company_address'] != '',
                ),
                'datatables'  => $datatables,
            ) );

            self::renderTemplate( 'index', compact( 'datatables' ) );
        }
    }
}