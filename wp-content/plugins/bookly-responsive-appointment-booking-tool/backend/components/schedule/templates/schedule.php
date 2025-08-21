<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var Bookly\Backend\Components\Schedule\Component $schedule
 */
?>
<div class="form-group">
    <?php foreach ( $schedule as $title ) : ?>
        <div class="form-row form-group bookly-js-range-row" data-key="<?php echo esc_attr( $schedule->key() ) ?>" data-index="<?php echo esc_attr( $schedule->index() ) ?>">
            <div class="col-12 col-lg-8">
                <div class="form-row align-items-center">
                    <div class="col-3">
                        <?php echo esc_html( $title ) ?>
                    </div>
                    <div class="col">
                        <div class="form-row align-items-center">
                            <div class="col" style="max-width: 120px;">
                                <?php $schedule->start_select->render() ?>
                            </div>
                            <div class="col-auto bookly-js-invisible-on-off">
                                <?php esc_html_e( 'to', 'bookly' ) ?>
                            </div>
                            <div class="col bookly-js-invisible-on-off" style="max-width: 120px;">
                                <?php $schedule->end_select->render() ?>
                            </div>
                            <?php if ( $schedule->withClone() ) : ?>
                                <div class="col-auto bookly-js-clone-schedule">
                                    <a class="btn btn-default" title="<?php esc_attr_e( 'Clone', 'bookly' ) ?>"><i class="far fa-fw fa-copy"></i></a>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ( $schedule->withBreaks() ) : ?>
                <div class="col-12 col-lg bookly-js-breaks-wrapper bookly-js-hide-on-off text-lg-right">
                    <button type="button" class="bookly-js-toggle-popover btn btn-default">
                        <?php esc_html_e( 'Add break', 'bookly' ) ?>
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