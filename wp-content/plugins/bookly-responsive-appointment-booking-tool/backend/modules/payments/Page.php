<?php
namespace Bookly\Backend\Modules\Payments;

use Bookly\Lib;

class Page extends Lib\Base\Component
{
    /**
     * Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'alias' => array( 'bookly-backend-globals', ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/payments.js' => array( 'bookly-backend-globals' ) ),
        ) );

        $datatables = Lib\Utils\Tables::getSettings( Lib\Utils\Tables::PAYMENTS );

        wp_localize_script( 'bookly-payments.js', 'BooklyL10n', array(
            'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
            'dateRange' => Lib\Utils\DateTime::dateRangeOptions( array( 'lastMonth' => __( 'Last month', 'bookly' ), ) ),
            'zeroRecords' => __( 'No payments for selected period and criteria.', 'bookly' ),
            'processing' => __( 'Processing...', 'bookly' ),
            'details' => __( 'Details', 'bookly' ),
            'areYouSure' => __( 'Are you sure?', 'bookly' ),
            'noResultFound' => __( 'No result found', 'bookly' ),
            'searching' => __( 'Searching', 'bookly' ),
            'multiple' => __( 'See details for more items', 'bookly' ),
            'datatables' => $datatables,
            'invoice' => array(
                'enabled' => (int) Lib\Config::invoicesActive(),
                'button'  => __( 'Invoice', 'bookly' ),
            ),
        ) );

        $types = Lib\Entities\Payment::getTypes();

        $providers = Lib\Entities\Staff::query()->select( 'id, full_name' )->sortBy( 'full_name' )->whereNot( 'visibility', 'archive' )->fetchArray();
        $services  = Lib\Entities\Service::query()->select( 'id, title' )->sortBy( 'title' )->fetchArray();
        $customers = Lib\Entities\Customer::query()->count() < Lib\Entities\Customer::REMOTE_LIMIT
            ? array_map( function ( $row ) {
                unset( $row['id'] );

                return $row;
            }, Lib\Entities\Customer::query( 'c' )->select( 'c.id, c.full_name, c.email, c.phone' )->indexBy( 'id' )->fetchArray() )
            : false;

        self::renderTemplate( 'index', compact( 'types', 'providers', 'services', 'customers', 'datatables' ) );
    }
}