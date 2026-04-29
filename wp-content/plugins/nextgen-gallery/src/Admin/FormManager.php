<?php

namespace Imagely\NGG\Admin;

/**
 * Form Manager for NextGEN Gallery admin forms.
 *
 * Manages form registration and ordering in the admin interface.
 */
class FormManager {

	/**
	 * Singleton instance.
	 *
	 * @var FormManager|null
	 */
	protected static $instance = null;

	/**
	 * Registered forms array.
	 *
	 * @var array
	 */
	protected $forms = [];

	/**
	 * Gets the singleton instance.
	 *
	 * @return FormManager The FormManager instance.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new FormManager();
		}
		return self::$instance;
	}

	/**
	 * Moves the registration of the first form so that it follows the second form.
	 *
	 * @param string $type Which form grouping to manipulate.
	 * @param string $form_name The name of the form to move.
	 * @param string $form_to_follow_name The form that $form_name will follow.
	 * @return void
	 */
	public function move_form_to_follow_other_form( string $type, string $form_name, string $form_to_follow_name ) {
		if ( ! is_array( $this->forms[ $type ] ) ) {
			return;
		}

		$index_one = array_search( $form_name, $this->forms[ $type ], true );
		$index_two = array_search( $form_to_follow_name, $this->forms[ $type ], true );

		if ( ! $index_one || ! $index_two ) {
			return;
		}

		$value = $this->forms[ $type ][ $index_one ];
		unset( $this->forms[ $type ][ $index_one ] );

		array_splice( $this->forms[ $type ], $index_two + 1, 0, $value );
	}

	/**
	 * Adds one or more forms to a form type.
	 *
	 * @param string       $type       The form type.
	 * @param array|string $form_names Form name(s) to add.
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
	 * Gets forms of a specific type.
	 *
	 * @param string $type        The form type.
	 * @param bool   $instantiate Whether to instantiate the forms (optional).
	 * @return array Array of forms.
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
	 * Gets the count of forms for a specific type.
	 *
	 * @param string $type Form type.
	 * @return int Number of forms.
	 */
	public function get_form_count( $type ) {
		return ( isset( $this->forms[ $type ] ) ) ? count( $this->forms[ $type ] ) : 0;
	}
}
