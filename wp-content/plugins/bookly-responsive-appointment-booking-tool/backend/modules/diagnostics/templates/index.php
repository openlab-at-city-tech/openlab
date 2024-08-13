<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Modules\Diagnostics\Tests\Test;
use Bookly\Backend\Modules\Diagnostics\Tools\Tool;
use Bookly\Lib;

/** @var Test[] $tests */
/** @var array $tools */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Diagnostics', 'bookly' ) ?></h4>
        <div class="col-auto">
            <button class="btn btn-default bookly-js-autorun-all-tests" type="button" title="<?php esc_attr_e( 'Run tests automatically', 'bookly' ) ?>">
                <i class="far fa-square fa-check-square"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'Run tests automatically', 'bookly' ) ?></span>
            </button>
        </div>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body bookly-js-tests pb-2">
            <?php foreach ( $tools as $tool ) : ?>
                <?php /** @var Tool $tool */ ?>
                <div class="card bookly-collapse-with-arrow bookly-js-tool">
                    <div class="card-header bg-light d-flex align-items-center bookly-collapsed bookly-cursor-pointer" href="#<?php echo esc_attr( $tool->getSlug() ) ?>" data-toggle="bookly-collapse" style="min-height: 62px;">
                        <div class="d-flex w-100 align-items-center">
                            <div class="flex-fill bookly-collapse-title bookly-js-test-title"><?php echo esc_html( $tool->getTitle() ) ?></div>
                            <?php if ( $tool->hasError() ) : ?>
                                <button class="btn btn-danger bookly-cursor-default bookly-js-has-error" type="button" disabled>
                                    <?php esc_html_e( 'Error', 'bookly' ) ?>
                                </button>
                            <?php endif ?>
                        </div>
                    </div>
                    <div id="<?php echo esc_attr( $tool->getSlug() ) ?>" class="bookly-collapse">
                        <div class="card-body">
                            <?php echo Lib\Utils\Common::html( $tool->render() ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
            <?php foreach ( $tests as $test ) : ?>
                <div class="card bookly-collapse-with-arrow bookly-js-test" data-test="<?php echo esc_attr( $test->getSlug() ) ?>" data-class="<?php echo esc_attr( basename( str_replace( '\\', '/', get_class( $test ) ) ) ) ?>" data-error-type="<?php echo esc_attr( $test->getErrorType() ) ?>">
                    <div class="card-header bg-white d-flex align-items-center bookly-collapsed bookly-cursor-pointer" href="#<?php echo esc_attr( $test->getSlug() ) ?>" data-toggle="bookly-collapse">
                        <div class="d-flex w-100 align-items-center">
                            <div class="flex-fill bookly-collapse-title bookly-js-test-title"><?php echo esc_html( $test->getTitle() ) ?></div>
                            <div class="bookly-js-status-test">
                                <button class="btn btn-success mr-2 bookly-js-success-test bookly-cursor-default" type="button" disabled style="display: none; width: 140px;">
                                    <?php esc_html_e( 'Success', 'bookly' ) ?>
                                </button>
                                <button class="btn btn-danger mr-2 bookly-js-failed-test bookly-cursor-default" type="button" disabled style="display: none; width: 140px;">
                                    <?php esc_html_e( 'Failed', 'bookly' ) ?>
                                </button>
                                <button class="btn btn-warning mr-2 bookly-js-warning-test bookly-cursor-default" type="button" disabled style="display: none; width: 140px;">
                                    <?php esc_html_e( 'Warning', 'bookly' ) ?>
                                </button>
                            </div>
                            <div class="bookly-js-button-test" style="min-height: 40px">
                                <button class="btn btn-default bookly-js-loading-test" type="button" disabled style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </button>
                                <button class="btn btn-default bookly-js-reload-test" type="button" style="display: none;">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo esc_attr( $test->getSlug() ) ?>" class="bookly-collapse">
                        <div class="card-body">
                            <?php echo Lib\Utils\Common::html( $test->getDescription() ) ?>
                            <div class="bookly-js-test-errors text-danger w-100 mt-2"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>