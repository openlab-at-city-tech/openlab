<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var array $forms */
/** @var string $active */
?>
<?php if ( count( $forms ) > 0 ) : ?>
    <ul class="list-group" id="bookly-js-booking-forms">
        <?php foreach ( $forms as $id => $data ) : ?>
            <li class="list-group-item list-group-item-action<?php if ( $id === $active ) : ?> list-group-item-primary<?php endif ?>" data-form_id="<?php echo esc_attr( $id ) ?>" data-form_data="<?php echo esc_attr( json_encode( $data ) ) ?>">
                <div class="row align-items-center">
                    <div class="col">
                        <?php echo esc_html( $id ) ?>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-default ladda-button" data-action="copy"><i class="far fa-copy fa-fw"></i></button>
                        <button type="button" class="btn btn-danger ladda-button" data-action="destroy" data-spinner-size="40" data-style="zoom-in"><span class="ladda-label"><i class="far fa-trash-alt fa-fw"></i></span></button>
                    </div>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
<?php else : ?>
    Data for [booking-form] is missing in the session
<?php endif ?>