<?php
namespace Bookly\Lib\Cloud;

class ProductX extends Product
{
    const ACTIVATE                = '/1.2/users/%token%/products/%product_id%/activate';                  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/%product_id%/deactivate/next-renewal';   //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/%product_id%/deactivate/now';            //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/%product_id%/revert-cancel';             //POST
    const ENDPOINT                = '/1.0/users/%token%/products/%product_id%/endpoint';                  //POST

    /**
     * @inheritDoc
     */
    public function translateError( $error_code )
    {
        $translated = null;
        switch ( $error_code ) {
            case 'ERROR_PURCHASE_CODE_INVALID':
                $translated = __( 'Purchase code is not valid', 'bookly' );
                break;
            case 'ERROR_PURCHASE_CODE_IN_USE':
                $translated = __( 'Purchase code is used on another account', 'bookly' );
                break;
            case 'ERROR_PURCHASE_CODE_UNKNOWN':
                $translated = __( 'Purchase code verification is temporarily unavailable. Please try again later.', 'bookly' );
                break;
        }

        return $translated;
    }
}