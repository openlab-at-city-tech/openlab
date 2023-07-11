<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Utils\Common;
/**
 * @var string $type
 * @var array $promotion
 */
?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-sms-promotion-notice" class="alert <?php echo esc_attr( $type == 'registration' ? 'alert-success' : 'alert-info' ) ?>" data-id="<?php echo esc_attr( $promotion['id'] ) ?>" data-type="<?php echo esc_attr( $type ) ?>" data-dismiss="close">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-cloud fa-2x"></i></div>
            <div class="col">
                <div>
                    <?php switch ( $type ) :
                        case 'registration':
                        case 'first_recharge':
                            echo Common::stripScripts( $promotion['texts']['info'] );
                            break;
                        case 'manual': ?>
                            <b><?php printf( esc_html__( 'Recharge your account balance and get up to %s extra.', 'bookly' ), '$' . $promotion['amount'] ) ?></b>
                            <?php esc_html_e( 'Take advantage of Bookly Cloud products which increase customers\' loyalty and involvement.', 'bookly' ) ?>
                            <?php break ?>
                        <?php case 'auto': ?>
                            <b><?php printf( esc_html__( 'Enable Auto-Recharge and get up to %s extra.', 'bookly' ), '$' . $promotion['amount'] ) ?></b>
                            <?php esc_html_e( 'Let Bookly Cloud products continuously work without interruptions.', 'bookly' ) ?>
                            <?php break ?>
                    <?php endswitch ?>
                </div>
                <div class="d-inline-flex mt-2">
                    <?php Buttons::render(
                            null,
                            'btn-success bookly-js-apply-action',
                            $type == 'registration'
                                ? __( 'Register', 'bookly' )
                                : ( $type == 'auto' ? __( 'Enable', 'bookly' ) : __( 'Recharge', 'bookly' ) )
                    ) ?>
                    <?php Buttons::renderDefault( null, 'ml-2 bookly-js-remind-me-later', __( 'Remind me later', 'bookly' ) ) ?>
                </div>
            </div>
        </div>
    </div>
</div>