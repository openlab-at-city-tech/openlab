<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Notice;

use InvalidArgumentException;

/**
 * A Notice to display in wp-admin.
 */
final class Notice {

	public const INFO    = 'info';
	public const SUCCESS = 'success';
	public const WARNING = 'warning';
	public const ERROR   = 'error';

	public const ALLOWED_TYPES = [
		self::INFO,
		self::SUCCESS,
		self::WARNING,
		self::ERROR,
	];

	/**
	 * The notice type, one of the above constants.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The already translated message to display.
	 *
	 * @see __()
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Whether this notice is dismissible.
	 *
	 * @var bool
	 */
	private $dismissible;

	/**
	 * Whether this is an alt-notice.
	 *
	 * @var bool
	 */
	private $alt;

	/**
	 * Whether this should be a large notice.
	 *
	 * @var bool
	 */
	private $large;

	/**
	 * Optional unique identifier used for persistent dismissal.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * @param string $type        The notice type, one of the above constants.
	 * @param string $message     The already translated message to display.
	 * @param bool   $dismissible Whether this notice is dismissible.
	 * @param bool   $alt         Whether this is an alt-notice.
	 * @param bool   $large       Whether this should be a large notice.
	 * @param string $id          Optional unique ID for persistent dismissal.
	 *
	 * @throws InvalidArgumentException If the type is not one of the allowed types or the message is empty.
	 */
	public function __construct(
		string $type,
		string $message,
		bool $dismissible = false,
		bool $alt = false,
		bool $large = false,
		string $id = ''
	) {
		if ( ! in_array( $type, self::ALLOWED_TYPES, true ) ) {
			throw new InvalidArgumentException(
				sprintf(
					/* translators: %s is the list of allowed notice types. */
					__( 'Notice $type must be one of: %s', 'tribe-common' ),
					implode( ', ', self::ALLOWED_TYPES )
				)
			);
		}

		if ( empty( $message ) ) {
			throw new InvalidArgumentException(
				/* translators: %s is the list of allowed notice types. */
				__( 'The $message cannot be empty', 'tribe-common' )
			);
		}

		$this->type        = $type;
		$this->message     = $message;
		$this->dismissible = $dismissible;
		$this->alt         = $alt;
		$this->large       = $large;
		$this->id          = $id;
	}

	/**
	 * @deprecated 3.0.0 Use to_array() instead.
	 * @return array<mixed>
	 */
	public function toArray(): array {
		return get_object_vars( $this );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array{type: string, message: string, dismissible: bool, alt: bool, large: bool, id: string}
	 */
	public function to_array(): array {
		return [
			'type'        => $this->type,
			'message'     => $this->message,
			'dismissible' => $this->dismissible,
			'alt'         => $this->alt,
			'large'       => $this->large,
			'id'          => $this->id,
		];
	}
}
