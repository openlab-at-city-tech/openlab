<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

abstract class CT_Base implements CT_Contract {

	protected $text_domain;

	public function __construct( $txt_domain ) {

		$this->text_domain = $txt_domain;
	}

}
