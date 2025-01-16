<?php
/**
 * Homepage customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Section as CustomizerSection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Homepage_Section' ) ) {

	class Kenta_Homepage_Section extends CustomizerSection {
		public function getControls() {
            return [
                ( new Condition( 'kenta_show_homepage_options' ) )
                    ->setCondition( [
                        'show_on_front' => 'page',
                        'page_on_front' => '!0'
                    ] )
                    ->setControls( [
						( new Separator( 'kenta_homepage_separator' ) ),
						( new Toggle( 'kenta_show_frontpage_header' ) )
							->setLabel( __( 'Show Page Header', 'kenta' ) )
							->closeByDefault()
						,
                    ] )
                ,
            ];
        }
    }
}
