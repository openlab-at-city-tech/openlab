<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Concerns;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;

/**
 * Provides shared DateTime parsing and UTC formatting helpers.
 */
trait InteractsWithDateTime
{
	/**
	 * @throws UnexpectedResponseException
	 */
	private static function parseDateTime(string $value): DateTimeImmutable {
		try {
			return new DateTimeImmutable($value);
		} catch (Exception $exception) {
			throw new UnexpectedResponseException('Invalid date value [' . $value . '].', 0, $exception);
		}
	}

	/**
	 * @throws UnexpectedResponseException
	 */
	private static function parseNullableDateTime(?string $value): ?DateTimeImmutable {
		return $value === null ? null : self::parseDateTime($value);
	}

	/**
	 * Format a DateTimeImmutable for the API's UTC RFC3339 wire format.
	 */
	private function formatDateTime(DateTimeImmutable $dt): string {
		return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
	}
}
