<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Notice;

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
	 * @param  string  $type  The notice type, one of the above constants.
	 * @param  string  $message  The already translated message to display.
	 * @param  bool  $dismissible  Whether this notice is dismissible.
	 * @param  bool  $alt  Whether this is an alt-notice.
	 * @param  bool  $large  Whether this should be a large notice.
	 */
	public function __construct(
		string $type,
		string $message,
		bool $dismissible = false,
		bool $alt = false,
		bool $large = false
	) {
		if ( ! in_array( $type, self::ALLOWED_TYPES, true ) ) {
			throw new InvalidArgumentException( sprintf(
					__( 'Notice $type must be one of: %s', '%TEXTDOMAIN%' ),
					implode( ', ', self::ALLOWED_TYPES ) )
			);
		}

		if ( empty( $message ) ) {
			throw new InvalidArgumentException( __( 'The $message cannot be empty', '%TEXTDOMAIN%' ) );
		}

		$this->type        = $type;
		$this->message     = $message;
		$this->dismissible = $dismissible;
		$this->alt         = $alt;
		$this->large       = $large;
	}

	/**
	 * @return array{type: string, message: string, dismissible: bool, alt: bool, large: bool}
	 */
	public function toArray(): array {
		return get_object_vars( $this );
	}

}
