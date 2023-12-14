<?php

namespace Imagely\NGG\DataMapper;

trait Validation {

	/**
	 * Hide the above attributes added to Models from var_dump() and the like
	 *
	 * @return array
	 */
	public function __debugInfo() {
		$properties = get_object_vars( $this );
		unset( $properties['default_messages'] );
		unset( $properties['default_patterns'] );

		return $properties;
	}

	/**
	 * @param string $validator
	 * @return string
	 */
	public function _get_default_error_message_for( $validator ) {
		$retval = false;

		if ( isset( ValidationMessages::$default_messages[ $validator ] ) ) {
			$retval = ValidationMessages::$default_messages[ $validator ];
		}

		return $retval;
	}

	/**
	 * @param string $formatter
	 * @return string
	 */
	public function get_default_pattern_for( $formatter ) {
		$retval = false;

		if ( isset( ValidationMessages::$default_patterns[ $formatter ] ) ) {
			$retval = ValidationMessages::$default_patterns[ $formatter ];
		}

		return $retval;
	}

	/**
	 * @param string|array<string> $str
	 * @return string
	 */
	public function humanize_string( $str ) {
		$retval = [];
		if ( is_array( $str ) ) {
			foreach ( $str as $s ) {
				$retval[] = $this->humanize_string( $s );
			}
		} else {
			$retval = ucfirst( str_replace( '_', ' ', $str ) );
		}

		return $retval;
	}
	/**
	 * Returns TRUE if a property is empty.
	 *
	 * @deprecated Don't use this, it's silly.
	 * @param string $var
	 * @return bool
	 */
	public function is_empty( $var, $element = false ) {
		if ( is_array( $var ) && $element ) {
			if ( isset( $var[ $element ] ) ) {
				$var = $var[ $element ];
			} else {
				$var = false;
			}
		}

		return ( is_null( $var ) or ( is_string( $var ) and strlen( $var ) == 0 ) or $var === false );
	}

	/**
	 * @param string      $property
	 * @param int         $length
	 * @param string      $comparison_operator ===, !=, <, >, <=, or >=
	 * @param bool|string $msg
	 * @return array
	 */
	public function validates_length_of( $property, $length, $comparison_operator = '=', $msg = false ) {
		$valid       = true;
		$default_msg = $this->_get_default_error_message_for( __METHOD__ );

		if ( ! $this->is_empty( $this->$property ) ) {
			switch ( $comparison_operator ) {
				case '=':
				case '==':
					$valid       = strlen( $this->$property ) == $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_equals' );
					break;
				case '!=':
				case '!':
					$valid       = strlen( $this->$property ) != $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_equals' );
					break;
				case '<':
					$valid       = strlen( $this->$property ) < $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_less_than' );
					break;
				case '>':
					$valid       = strlen( $this->$property ) > $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_greater_than' );
					break;
				case '<=':
					$valid       = strlen( $this->$property ) <= $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_less_than' );
					break;
				case '>=':
					$valid       = strlen( $this->$property ) >= $length;
					$default_msg = $this->_get_default_error_message_for( 'validates_greater_than' );
					break;
			}
		} else {
			$valid = false;
		}

		if ( ! $valid ) {
			if ( ! $msg ) {
				$error_msg = sprintf( $default_msg, $this->humanize_string( $property ) );
			} else {
				$error_msg = $msg;
			}

			return [ $property => [ $error_msg ] ];
		}

		return [];
	}

	/**
	 * @param string    $property
	 * @param int|float $comparison
	 * @param string    $comparison_operator
	 * @param string    $msg
	 * @return array
	 */
	public function validates_numericality_of( $property, $comparison = false, $comparison_operator = false, $int_only = false, $msg = false ) {
		$default_msg = $this->_get_default_error_message_for( __METHOD__ );

		if ( ! $this->is_empty( $this->$property ) ) {
			$invalid = false;
			if ( is_numeric( $this->$property ) ) {
				$this->$property += 0;

				if ( $int_only ) {
					$invalid = ! is_int( $this->$property );
				}

				if ( ! $invalid ) {
					switch ( $comparison_operator ) {
						case '=':
						case '==':
							$invalid     = ( $this->$property == $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_equals' );
							break;
						case '!=':
						case '!':
							$invalid     = ( $this->$property != $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_equals' );
							break;
						case '<':
							$invalid     = ( $this->$property < $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_less_than' );
							break;
						case '>':
							$invalid     = ( $this->$property > $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_greater_than' );
							break;
						case '<=':
							$invalid     = ( $this->$property <= $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_less_than' );
							break;
						case '>=':
							$invalid     = ( $this->$property >= $comparison ) ? false : true;
							$default_msg = $this->_get_default_error_message_for( 'validates_greater_than' );
							break;
					}
				}
			} else {
				$invalid = true;
			}

			if ( $invalid ) {
				if ( ! $msg ) {
					$error_msg = sprintf( $default_msg, $this->humanize_string( $property ) );
				} else {
					$error_msg = $msg;
				}

				return [ $property => [ $error_msg ] ];
			}
		}

		return [];
	}

	/**
	 * @param string $property
	 * @param array  $values
	 * @param string $msg
	 * @return array
	 */
	public function validates_inclusion_of( $property, $values = [], $msg = false ) {
		if ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		if ( ! in_array( $this->$property, $values ) ) {
			if ( ! $msg ) {
				$msg = $this->_get_default_error_message_for( __METHOD__ );
				$msg = sprintf( $msg, $this->humanize_string( $property ) );
			}

			return [ $property => [ $msg ] ];
		}

		return [];
	}

	/**
	 * @param string|array $property
	 * @param string       $pattern
	 * @param string       $msg
	 * @return array
	 */
	public function validates_format_of( $property, $pattern, $msg = false ) {
		// We do not validate blank values - we rely on "validates_presence_of" for that.
		if ( ! $this->is_empty( $this->$property ) ) {
			// If it doesn't match, then it's an error.
			if ( ! preg_match( $pattern, $this->$property ) ) {
				// Get default message.
				if ( ! $msg ) {
					$msg = $this->_get_default_error_message_for( __METHOD__ );
					$msg = sprintf( $msg, $this->humanize_string( $property ) );
				}

				return [ $property => [ $msg ] ];
			}
		}

		return [];
	}

	/**
	 * @param string $property
	 * @param array  $exclusions
	 * @param string $msg (optional)
	 * @return array
	 */
	public function validates_exclusion_of( $property, $exclusions, $msg = false ) {
		$invalid = false;

		foreach ( $exclusions as $exclusion ) {
			if ( $exclusion == $this->$property ) {
				$invalid = true;
				break;
			}
		}

		if ( $invalid ) {
			if ( ! $msg ) {
				$msg = $this->_get_default_error_message_for( __METHOD__ );
				$msg = sprintf( $msg, $this->humanize_string( $property ) );
			}

			return [ $property => [ $msg ] ];
		}

		return [];
	}

	/**
	 * @param string $property
	 * @param string $confirmation
	 * @param string $msg
	 * @return array
	 */
	public function validates_confirmation_of( $property, $confirmation, $msg = false ) {
		if ( $this->$property != $this->$confirmation ) {
			if ( ! $msg ) {
				$msg = $this->_get_default_error_message_for( __METHOD__ );
				$msg = sprintf( $msg, $this->humanize_string( $property ) );
			}

			return [ $property => [ $msg ] ];
		}

		return [];
	}

	/**
	 * @param string $property
	 * @param array  $scope
	 * @param string $msg
	 * @return array
	 */
	public function validates_uniqueness_of( $property, $scope = [], $msg = false ) {
		// Get any entities that have the same property.
		$mapper = $this->get_mapper();
		$key    = $mapper->get_primary_key_column();
		$mapper->select( $key );
		$mapper->limit( 1 );
		$mapper->where_and( [ "{$property} = %s", $this->$property ] );

		if ( ! $this->is_new() ) {
			$mapper->where_and( [ "{$key} != %s", $this->id() ] );
		}

		foreach ( $scope as $another_property ) {
			$mapper->where_and( [ "{$another_property} = %s", $another_property ] );
		}

		$result = $mapper->run_query();

		// If there's a result, it means that the entity is NOT unique.
		if ( $result ) {
			// Get default msg.
			if ( ! $msg ) {
				$msg = $this->_get_default_error_message_for( __METHOD__ );
				$msg = sprintf( $msg, $this->humanize_string( $property ) );
			}

			return [ $property => [ $msg ] ];
		}

		return [];
	}
	/**
	 * @param string $property
	 * @param array  $with
	 * @param string $msg
	 * @return array
	 */
	public function validates_presence_of( $property, $with = [], $msg = false ) {
		$missing = [];

		$invalid = true;

		// Is a value present?
		if ( ! $this->is_empty( $this->$property ) ) {
			$invalid = false;

			// This property must be present with at least another property.
			if ( $with ) {
				if ( ! is_array( $with ) ) {
					$with = [ $with ];
				}

				foreach ( $with as $other ) {
					if ( $this->is_empty( $this->$other ) ) {
						$invalid   = true;
						$missing[] = $other;
					}
				}
			}
		}

		// Add error.
		if ( $invalid ) {
			if ( ! $msg ) {
				// If missing isn't empty, it means that we're to use the "with" error message.
				if ( $missing ) {
					$missing = implode( ', ', $this->humanize_string( $missing ) );
					$msg     = sprintf(
						$this->_get_default_error_message_for( 'validates_presence_with' ),
						$property,
						$missing
					);
				} else {
					// Has no 'with' arguments. Use the default error msg.
					$msg = sprintf(
						$this->_get_default_error_message_for( __METHOD__ ),
						$property
					);
				}
			}

			return [ $property => [ $msg ] ];
		}

		return [];
	}
}

/**
 * This class exists to prevent the Validation trait from adding any new attributes to the classes that use it and is
 * only used by the above Validation trait.
 */
class ValidationMessages {

	public static $default_messages = [
		'validates_presence_of'     => '%s should be present',
		'validates_presence_with'   => '%s should be present with %s',
		'validates_uniqueness_of'   => '%s should be unique',
		'validates_confirmation_of' => '%s should match confirmation',
		'validates_exclusion_of'    => '%s is reserved',
		'validates_format_of'       => '%s is invalid',
		'validates_inclusion_of'    => '%s is not included in the list',
		'validates_numericality_of' => '%s is not numeric',
		'validates_less_than'       => '%s is too small',
		'validates_greater_than'    => '%s is too large',
		'validates_equals'          => '%s is invalid',
	];

	public static $default_patterns = [
		'email_address' => '//',
	];
}
