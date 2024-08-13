<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-js-datepicker-calendar-mode <?php echo get_option( 'bookly_app_datepicker_inverted' ) ? ' bookly-collapse' : '' ?>" data-mode="0">
    <?php
    $start = date_create( 'first day of this month' )->modify( 'monday this week' );
    $end = date_create( 'first day of next month' )->modify( 'sunday this week' );
    ?>
    <div class="border rounded p-1" style="border-color:#d6d6d6;">
        <div class="d-flex py-2">
            <i class="fas fa-fw fa-angle-left" style="font-family: 'Font Awesome 5 Free';color:#d6d6d6;"></i>
            <div class="flex-grow-1 text-center"><?php echo date_i18n( 'M Y' ) ?></div>
            <i class="fas fa-fw fa-angle-right" style="font-family: 'Font Awesome 5 Free';color:#d6d6d6;"></i>
        </div>
        <hr style="margin: 0;"/>
        <div class="d-flex w-100">
            <?php for ( $i = 1; $i <= 7; $i++ ): ?>
                <div class="py-2 flex-fill text-center"><?php echo date_i18n( 'D', strtotime( 'sunday +' . $i . ' days' ) ) ?></div>
            <?php endfor ?>
        </div>
        <hr style="margin: 0;"/>
        <?php while ( $start <= $end ): ?>
            <?php if ( $start->format( 'w' ) === '1' ) echo '<div class="d-flex">' ?>
            <div class="p-2 flex-fill text-center bookly-calendar-button" style="min-width: 36px;">
                <span class="<?php if ( $start->format( 'm' ) === date( 'm' ) ) : ?>text-color-bookly<?php else: ?>text-muted<?php endif ?>"><?php echo esc_html( $start->format( 'j' ) ) ?></span></div>
            <?php if ( $start->modify( '+1 day' )->format( 'w' ) === '1' ) echo '</div>' ?>
        <?php endwhile ?>
    </div>
</div>
<div class="bookly-js-datepicker-calendar-mode <?php echo ! get_option( 'bookly_app_datepicker_inverted' ) ? ' bookly-collapse' : '' ?>" data-mode="1">
    <?php
    $start = date_create( 'first day of this month' )->modify( 'monday this week' );
    $end = date_create( 'first day of next month' )->modify( 'sunday this week' );
    ?>
    <div class="border rounded p-1 bg-bookly text-white" style="border-color:#d6d6d6;">
        <div class="d-flex py-2">
            <i class="fas fa-fw fa-angle-left" style="font-family: 'Font Awesome 5 Free';"></i>
            <div class="flex-grow-1 text-center"><?php echo date_i18n( 'M Y' ) ?></div>
            <i class="fas fa-fw fa-angle-right" style="font-family: 'Font Awesome 5 Free';"></i>
        </div>
        <hr style="margin: 0;"/>
        <div class="d-flex w-100">
            <?php for ( $i = 1; $i <= 7; $i++ ): ?>
                <div class="py-2 flex-fill text-center"><?php echo date_i18n( 'D', strtotime( 'sunday +' . $i . ' days' ) ) ?></div>
            <?php endfor ?>
        </div>
        <hr style="margin: 0;"/>
        <?php while ( $start <= $end ): ?>
            <?php if ( $start->format( 'w' ) === '1' ) echo '<div class="d-flex">' ?>
            <div class="p-2 flex-fill text-center bookly-calendar-button" style="min-width: 36px;">
                <span class="<?php if ( $start->format( 'm' ) === date( 'm' ) ) : ?>text-white<?php else: ?>text-light<?php endif ?>"><?php echo esc_html( $start->format( 'j' ) ) ?></span></div>
            <?php if ( $start->modify( '+1 day' )->format( 'w' ) === '1' ) echo '</div>' ?>
        <?php endwhile ?>
    </div>
</div>