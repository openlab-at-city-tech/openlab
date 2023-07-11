<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var Bookly\Backend\Components\Schedule\Component $schedule
 */
?>
<div class="form-group">
    <?php foreach ( $schedule as $title ) : ?>
        <div class="form-row form-group bookly-js-range-row" data-key="<?php echo esc_attr( $schedule->key() ) ?>" data-index="<?php echo esc_attr( $schedule->index() ) ?>">
            <div class="col-12 col-lg-6">
                <div class="form-row align-items-center">
                    <div class="col-3">
                        <?php echo esc_html( $title ) ?>
                    </div>
                    <div class="col">
                        <?php $schedule->start_select->render() ?>
                    </div>
                    <div class="col-auto bookly-js-invisible-on-off">
                        <?php esc_html_e( 'to', 'bookly' ) ?>
                    </div>
                    <div class="col bookly-js-invisible-on-off">
                        <?php $schedule->end_select->render() ?>
                    </div>
                </div>
            </div>
            <?php if ( $schedule->withBreaks() ) : ?>
                <div class="col-12 col-lg bookly-js-breaks-wrapper bookly-js-hide-on-off">
                    <button type="button" class="bookly-js-toggle-popover btn btn-link pl-0">
                        <?php esc_html_e( 'add break', 'bookly' ) ?>
                    </button>
                    <div class="bookly-js-breaks-list">
                        <?php foreach ( $schedule->day_breaks as $break ) $break->render() ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    <?php endforeach ?>
    <?php if ( $schedule->withBreaks() ): $schedule::renderBreakDialog(); endif ?>
</div>