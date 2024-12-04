<?php


namespace ColibriWP\Theme\Core;

interface PartialComponentInterface extends ComponentInterface {
	public function renderContent( $parameters );
}
