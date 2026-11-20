<?php

function su_get_available_styles_for(  $shortcode  ) {
    $styles = su_get_available_styles();
    return ( isset( $styles[$shortcode] ) ? $styles[$shortcode] : array() );
}

function su_get_available_styles() {
    $styles = array(
        'heading' => array(
            'default' => __( 'Default', 'shortcodes-ultimate' ),
        ),
        'quote'   => array(
            'default' => __( 'Default', 'shortcodes-ultimate' ),
        ),
        'tabs'    => array(
            'default' => __( 'Default', 'shortcodes-ultimate' ),
        ),
        'spoiler' => array(
            'default' => __( 'Default', 'shortcodes-ultimate' ),
            'fancy'   => __( 'Fancy', 'shortcodes-ultimate' ),
            'simple'  => __( 'Simple', 'shortcodes-ultimate' ),
        ),
    );
    if ( !su_fs()->can_use_premium_code() ) {
        $styles['heading'] += array(
            '_PRO-modern-1-dark'      => '[PRO] ' . __( 'Modern 1: Dark', 'shortcodes-ultimate' ),
            '_PRO-modern-1-light'     => '[PRO] ' . __( 'Modern 1: Light', 'shortcodes-ultimate' ),
            '_PRO-modern-1-blue'      => '[PRO] ' . __( 'Modern 1: Blue', 'shortcodes-ultimate' ),
            '_PRO-modern-1-orange'    => '[PRO] ' . __( 'Modern 1: Orange', 'shortcodes-ultimate' ),
            '_PRO-modern-1-violet'    => '[PRO] ' . __( 'Modern 1: Violet', 'shortcodes-ultimate' ),
            '_PRO-modern-2-dark'      => '[PRO] ' . __( 'Modern 2: Dark', 'shortcodes-ultimate' ),
            '_PRO-modern-2-light'     => '[PRO] ' . __( 'Modern 2: Light', 'shortcodes-ultimate' ),
            '_PRO-modern-2-blue'      => '[PRO] ' . __( 'Modern 2: Blue', 'shortcodes-ultimate' ),
            '_PRO-modern-2-orange'    => '[PRO] ' . __( 'Modern 2: Orange', 'shortcodes-ultimate' ),
            '_PRO-modern-2-violet'    => '[PRO] ' . __( 'Modern 2: Violet', 'shortcodes-ultimate' ),
            '_PRO-line-dark'          => '[PRO] ' . __( 'Line: Dark', 'shortcodes-ultimate' ),
            '_PRO-line-light'         => '[PRO] ' . __( 'Line: Light', 'shortcodes-ultimate' ),
            '_PRO-line-blue'          => '[PRO] ' . __( 'Line: Blue', 'shortcodes-ultimate' ),
            '_PRO-line-orange'        => '[PRO] ' . __( 'Line: Orange', 'shortcodes-ultimate' ),
            '_PRO-line-violet'        => '[PRO] ' . __( 'Line: Violet', 'shortcodes-ultimate' ),
            '_PRO-dotted-line-dark'   => '[PRO] ' . __( 'Dotted line: Dark', 'shortcodes-ultimate' ),
            '_PRO-dotted-line-light'  => '[PRO] ' . __( 'Dotted line: Light', 'shortcodes-ultimate' ),
            '_PRO-dotted-line-blue'   => '[PRO] ' . __( 'Dotted line: Blue', 'shortcodes-ultimate' ),
            '_PRO-dotted-line-orange' => '[PRO] ' . __( 'Dotted line: Orange', 'shortcodes-ultimate' ),
            '_PRO-dotted-line-violet' => '[PRO] ' . __( 'Dotted line: Violet', 'shortcodes-ultimate' ),
            '_PRO-flat-dark'          => '[PRO] ' . __( 'Flat: Dark', 'shortcodes-ultimate' ),
            '_PRO-flat-light'         => '[PRO] ' . __( 'Flat: Light', 'shortcodes-ultimate' ),
            '_PRO-flat-blue'          => '[PRO] ' . __( 'Flat: Blue', 'shortcodes-ultimate' ),
            '_PRO-flat-green'         => '[PRO] ' . __( 'Flat: Green', 'shortcodes-ultimate' ),
        );
        $styles['spoiler'] += array(
            '_PRO-carbon'        => '[PRO] ' . __( 'Carbon', 'shortcodes-ultimate' ),
            '_PRO-sharp'         => '[PRO] ' . __( 'Sharp', 'shortcodes-ultimate' ),
            '_PRO-grid'          => '[PRO] ' . __( 'Grid', 'shortcodes-ultimate' ),
            '_PRO-wood'          => '[PRO] ' . __( 'Wood', 'shortcodes-ultimate' ),
            '_PRO-fabric'        => '[PRO] ' . __( 'Fabric', 'shortcodes-ultimate' ),
            '_PRO-modern-dark'   => '[PRO] ' . __( 'Modern: Dark', 'shortcodes-ultimate' ),
            '_PRO-modern-light'  => '[PRO] ' . __( 'Modern: Light', 'shortcodes-ultimate' ),
            '_PRO-modern-violet' => '[PRO] ' . __( 'Modern: Violet', 'shortcodes-ultimate' ),
            '_PRO-modern-orange' => '[PRO] ' . __( 'Modern: Orange', 'shortcodes-ultimate' ),
            '_PRO-glass-dark'    => '[PRO] ' . __( 'Glass: Dark', 'shortcodes-ultimate' ),
            '_PRO-glass-light'   => '[PRO] ' . __( 'Glass: Light', 'shortcodes-ultimate' ),
            '_PRO-glass-blue'    => '[PRO] ' . __( 'Glass: Blue', 'shortcodes-ultimate' ),
            '_PRO-glass-green'   => '[PRO] ' . __( 'Glass: Green', 'shortcodes-ultimate' ),
            '_PRO-glass-gold'    => '[PRO] ' . __( 'Glass: Gold', 'shortcodes-ultimate' ),
        );
        $styles['tabs'] += array(
            '_PRO-carbon'        => '[PRO] ' . __( 'Carbon', 'shortcodes-ultimate' ),
            '_PRO-sharp'         => '[PRO] ' . __( 'Sharp', 'shortcodes-ultimate' ),
            '_PRO-grid'          => '[PRO] ' . __( 'Grid', 'shortcodes-ultimate' ),
            '_PRO-wood'          => '[PRO] ' . __( 'Wood', 'shortcodes-ultimate' ),
            '_PRO-fabric'        => '[PRO] ' . __( 'Fabric', 'shortcodes-ultimate' ),
            '_PRO-modern-dark'   => '[PRO] ' . __( 'Modern: Dark', 'shortcodes-ultimate' ),
            '_PRO-modern-light'  => '[PRO] ' . __( 'Modern: Light', 'shortcodes-ultimate' ),
            '_PRO-modern-blue'   => '[PRO] ' . __( 'Modern: Blue', 'shortcodes-ultimate' ),
            '_PRO-modern-orange' => '[PRO] ' . __( 'Modern: Orange', 'shortcodes-ultimate' ),
            '_PRO-flat-dark'     => '[PRO] ' . __( 'Flat: Dark', 'shortcodes-ultimate' ),
            '_PRO-flat-light'    => '[PRO] ' . __( 'Flat: Light', 'shortcodes-ultimate' ),
            '_PRO-flat-blue'     => '[PRO] ' . __( 'Flat: Blue', 'shortcodes-ultimate' ),
            '_PRO-flat-green'    => '[PRO] ' . __( 'Flat: Green', 'shortcodes-ultimate' ),
        );
        $styles['quote'] += array(
            '_PRO-carbon'        => '[PRO] ' . __( 'Carbon', 'shortcodes-ultimate' ),
            '_PRO-sharp'         => '[PRO] ' . __( 'Sharp', 'shortcodes-ultimate' ),
            '_PRO-grid'          => '[PRO] ' . __( 'Grid', 'shortcodes-ultimate' ),
            '_PRO-wood'          => '[PRO] ' . __( 'Wood', 'shortcodes-ultimate' ),
            '_PRO-fabric'        => '[PRO] ' . __( 'Fabric', 'shortcodes-ultimate' ),
            '_PRO-modern-dark'   => '[PRO] ' . __( 'Modern: Dark', 'shortcodes-ultimate' ),
            '_PRO-modern-light'  => '[PRO] ' . __( 'Modern: Light', 'shortcodes-ultimate' ),
            '_PRO-modern-blue'   => '[PRO] ' . __( 'Modern: Blue', 'shortcodes-ultimate' ),
            '_PRO-modern-orange' => '[PRO] ' . __( 'Modern: Orange', 'shortcodes-ultimate' ),
            '_PRO-modern-violet' => '[PRO] ' . __( 'Modern: Violet', 'shortcodes-ultimate' ),
            '_PRO-flat-dark'     => '[PRO] ' . __( 'Flat: Dark', 'shortcodes-ultimate' ),
            '_PRO-flat-light'    => '[PRO] ' . __( 'Flat: Light', 'shortcodes-ultimate' ),
            '_PRO-flat-blue'     => '[PRO] ' . __( 'Flat: Blue', 'shortcodes-ultimate' ),
            '_PRO-flat-green'    => '[PRO] ' . __( 'Flat: Green', 'shortcodes-ultimate' ),
        );
    }
    return $styles;
}
