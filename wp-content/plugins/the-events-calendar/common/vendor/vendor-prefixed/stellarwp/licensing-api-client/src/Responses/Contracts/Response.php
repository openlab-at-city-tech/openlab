<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Responses\Contracts;

/**
 * Response/value objects implement this interface to provide structured API data.
 *
 * @template TArray of array
 */
interface Response
{
	/**
	 * Map raw attributes to the response object.
	 *
	 * @param array<string, mixed> $attributes
	 *
	 * @return static
	 */
	public static function from(array $attributes): self;

	/**
	 * Return the array representation of the response object.
	 *
	 * @return TArray
	 */
	public function toArray(): array;
}
