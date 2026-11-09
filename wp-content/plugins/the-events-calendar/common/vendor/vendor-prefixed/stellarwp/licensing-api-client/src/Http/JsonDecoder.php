<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

use JsonException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\DecodingException;

/**
 * Decodes JSON response bodies into arrays for response mappers.
 */
final class JsonDecoder
{
	/**
	 *
	 * @throws DecodingException
	 * @return array<array-key, mixed>
	 */
	public function decode(string $json): array {
		try {
			$decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		} catch (JsonException $exception) {
			throw new DecodingException('Unable to decode JSON response.', 0, $exception);
		}

		if (!is_array($decoded)) {
			throw new DecodingException('Decoded JSON response must be an array.');
		}

		return $decoded;
	}
}
