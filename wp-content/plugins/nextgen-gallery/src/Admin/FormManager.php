<?php

namespace Imagely\NGG\Admin;

class FormManager {

	protected static $instance = null;

	protected $forms = [];

	/**
	 * @return FormManager
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new FormManager();
		}
		return self::$instance;
	}

	/**
	 * @param string       $type
	 * @param array|string $form_names
	 * @return int Results of get_form_count($type)
	 */
	public function add_form( $type, $form_names ) {
		if ( ! isset( $this->forms[ $type ] ) ) {
			$this->forms[ $type ] = [];
		}

		if ( ! is_array( $form_names ) ) {
			$form_names = [ $form_names ];
		}

		foreach ( $form_names as $form ) {
			$this->forms[ $type ][] = $form;
		}

		return $this->get_form_count( $type );
	}

	/**
	 * @param string $type
	 * @param bool   $instantiate (optional).
	 * @return array
	 */
	public function get_forms( $type, $instantiate = false ) {
		$retval = [];
		if ( isset( $this->forms[ $type ] ) ) {
			if ( ! $instantiate ) {
				$retval = $this->forms[ $type ];
			} else {
				foreach ( $this->forms[ $type ] as $context ) {
					if ( class_exists( '\C_Component_Registry' ) ) {
						$retval[] = \C_Component_Registry::get_instance()->get_utility( 'I_Form', $context );
					}
				}
			}
		}
		return $retval;
	}

	/**
	 * @param string $type Form type.
	 * @return int
	 */
	public function get_form_count( $type ) {
		return ( isset( $this->forms[ $type ] ) ) ? count( $this->forms[ $type ] ) : 0;
	}
}
