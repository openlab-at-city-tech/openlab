<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts;

use Generator;
use JsonException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Activate;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\ImportAliases;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\RemoveAliases;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Deactivate;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\DeleteActivation;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\LicenseReference;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Listing\ListRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\RegenerateKey;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Activate as ActivateResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\ImportAliases as ImportAliasesResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\RemoveAliases as RemoveAliasesResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Deactivate as DeactivateResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\DeleteActivation as DeleteActivationResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing\Listing;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\RegenerateKey as RegenerateKeyResponse;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\StatusChange;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Validate;
use TEC\Common\Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the licenses resource surface.
 */
interface LicensesResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(ListRequest $request): Listing;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, Listing, mixed, void>
	 */
	public function pages(ListRequest $request): Generator;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function activate(Activate $request): ActivateResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deactivate(Deactivate $request): DeactivateResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deleteActivation(DeleteActivation $request): DeleteActivationResponse;

	/**
	 * @param list<string> $productSlugs
	 *
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function validate(string $licenseKey, array $productSlugs, string $domain): Validate;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function suspend(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function reinstate(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function ban(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function regenerateKey(RegenerateKey $request): RegenerateKeyResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function importAliases(ImportAliases $request): ImportAliasesResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function removeAliases(RemoveAliases $request): RemoveAliasesResponse;
}
