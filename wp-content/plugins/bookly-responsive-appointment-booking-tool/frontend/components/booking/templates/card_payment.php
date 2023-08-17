<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-box bookly-table">
    <div class="bookly-form-group" style="width:200px!important">
        <label><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_ccard_number' ) ?></label>
        <div>
            <input type="text" name="card_number" autocomplete="off"/>
        </div>
    </div>
    <div class="bookly-form-group">
        <label><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_ccard_expire' ) ?></label>
        <div>
            <select class="bookly-card-exp" name="card_exp_month">
                <?php for ( $i = 1; $i <= 12; ++ $i ) : ?>
                    <option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( sprintf( '%02d', $i ) ) ?></option>
                <?php endfor ?>
            </select>
            <select class="bookly-card-exp" name="card_exp_year">
                <?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; ++ $i ) : ?>
                    <option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?></option>
                <?php endfor ?>
            </select>
        </div>
    </div>
</div>
<div class="bookly-box bookly-clear-bottom">
    <div class="bookly-form-group">
        <label><?php echo \Bookly\Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_ccard_code' ) ?></label>
        <div>
            <input type="text" class="bookly-card-cvc" name="card_cvc" autocomplete="off" />
        </div>
    </div>
</div>
<div class="bookly-label-error bookly-js-card-error"></div>