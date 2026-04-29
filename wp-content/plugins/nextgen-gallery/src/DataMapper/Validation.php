<?php

namespace Imagely\NGG\DataMapper;

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
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
	 * Gets the default error message for a validator.
	 *
	 * @param string $validator The validator name.
	 * @return string|false The error message or false if not found.
	 */
	public function _get_default_error_message_for( $validator ) {
		$retval = false;

		if ( isset( ValidationMessages::$default_messages[ $validator ] ) ) {
			$retval = ValidationMessages::$default_messages[ $validator ];
		}

		return $retval;
	}

	/**
	 * Gets the default pattern for a formatter.
	 *
	 * @param string $formatter The formatter name.
	 * @return string|false The pattern or false if not found.
	 */
	public function get_default_pattern_for( $formatter ) {
		$retval = false;

		if ( isset( ValidationMessages::$default_patterns[ $formatter ] ) ) {
			$retval = ValidationMessages::$default_patterns[ $formatter ];
		}

		return $retval;
	}

	/**
	 * Humanizes a string or array of strings.
	 *
	 * @param string|array<string> $str The string or array of strings to humanize.
	 * @return string The humanized string.
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
	 * @param string          $variable The variable to check.
	 * @param string|int|bool $element Optional element key if $variable is an array.
	 * @return bool True if empty, false otherwise.
	 */
	public function is_empty( $variable, $element = false ) {
		if ( is_array( $variable ) && $element ) {
			if ( isset( $variable[ $element ] ) ) {
				$variable = $variable[ $element ];
			} else {
				$variable = false;
			}
		}

		return ( is_null( $variable ) || ( is_string( $variable ) && strlen( $variable ) == 0 ) || $variable === false );
	}

	/**
	 * Validates the length of a property.
	 *
	 * @param string      $property           The property name to validate.
	 * @param int         $length             The expected length.
	 * @param string      $comparison_operator The comparison operator: ===, !=, <, >, <=, or >=.
	 * @param bool|string $msg                Optional custom error message.
	 * @return array Array of validation errors if invalid, empty array if valid.
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
	 * Validates numericality of a property.
	 *
	 * @param string    $property
	 * @param int|float $comparison
	 * @param string    $comparison_operator
	 * @param bool      $int_only
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
	 * Validates inclusion of a property value.
	 *
	 * @param string $property
	 * @param array  $values
	 * @param string $msg
	 * @return array
	 */
	public function validates_inclusion_of( $property, $values = [], $msg = false ) {
		if ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
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
	 * Validates format of a property.
	 *
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
	 * Validates exclusion of a property value.
	 *
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
	 * Validates confirmation of a property.
	 *
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
	 * Validates uniqueness of a property.
	 *
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
	 * Validates presence of a property.
	 *
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

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * This class exists to prevent the Validation trait from adding any new attributes to the classes that use it and is
 * only used by the above Validation trait.
 */
class ValidationMessages {

	/**
	 * Default validation messages.
	 *
	 * @var array
	 */
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

	/**
	 * Default validation patterns.
	 *
	 * @var array
	 */
	public static $default_patterns = [
		'email_address' => '//',
	];
}
