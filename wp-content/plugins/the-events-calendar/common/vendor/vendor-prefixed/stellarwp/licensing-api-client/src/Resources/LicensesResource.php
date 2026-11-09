<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Resources;

use Generator;
use JsonException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\AuthState;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use TEC\Common\LiquidWeb\LicensingApiClient\Http\RequestHeaderCollection;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Activate as ActivateRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\ImportAliases as ImportAliasesRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Alias\RemoveAliases as RemoveAliasesRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Deactivate as DeactivateRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\DeleteActivation as DeleteActivationRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\LicenseReference;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\Listing\ListRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Requests\License\RegenerateKey as RegenerateKeyRequest;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsRequestHeaderCollection;
use TEC\Common\LiquidWeb\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Activate;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\ImportAliases;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Alias\RemoveAliases;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Deactivate;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\DeleteActivation;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Listing\Listing;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\RegenerateKey;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\StatusChange;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\License\Validate;
use TEC\Common\Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the licenses API resource.
 *
 * @phpstan-type ActivationDomainPayload array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool,
 *     is_production: bool
 * }
 * @phpstan-type ActivationDomainsPayload array<string, ActivationDomainPayload>
 * @phpstan-type ValidatePayload array{
 *     license: array{license_key: string, status: string}|null,
 *     domain: string,
 *     is_production: bool,
 *     products: list<array{
 *         product_slug: string,
 *         status: string,
 *         is_valid: bool,
 *         entitlement: array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             over_limit: bool,
 *             excess_activations: int,
 *             expiration_date: string,
 *             status: string,
 *             capabilities: list<string>
 *         }|null,
 *         activation: array{
 *             domain: string,
 *             activated_at: string
 *         }|null,
 *         available_entitlements?: list<array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             over_limit: bool,
 *             excess_activations: int,
 *             capabilities: list<string>,
 *             status: string,
 *             expires: string
 *         }>
 *     }>
 * }
 * @phpstan-type ListingPayload array{
 *     licenses: list<array{
 *         license_key: string,
 *         identity_id: string,
 *         status: string,
 *         created_at: string,
 *         updated_at: string,
 *         products: list<array{
 *             product_slug: string,
 *             tier: string,
 *             status: string,
 *             expires: string,
 *             capabilities: list<string>,
 *             activations: array{
 *                 site_limit: int,
 *                 active_count: int,
 *                 over_limit: bool,
 *                 excess_activations: int,
 *                 domains: ActivationDomainsPayload
 *             }
 *         }>,
 *         aliases: list<array{alias_key: string, product_slug: string|null}>
 *     }>,
 *     links: array{
 *         first: string,
 *         last: string|null,
 *         prev: string|null,
 *         next: string|null
 *     },
 *     meta: array{
 *         page: array{
 *             total: int,
 *             limit: int,
 *             max_size: int
 *         }
 *     }
 * }
 * @phpstan-import-type ActivatePayload from ActivateRequest
 * @phpstan-import-type DeactivatePayload from DeactivateRequest
 * @phpstan-import-type DeleteActivationPayload from DeleteActivationRequest
 * @phpstan-import-type LicenseReferencePayload from LicenseReference
 * @phpstan-import-type RegenerateKeyPayload from RegenerateKeyRequest
 * @phpstan-import-type ImportAliasesPayload from ImportAliasesRequest
 * @phpstan-import-type RemoveAliasesPayload from RemoveAliasesRequest
 * @phpstan-type ActivateResponsePayload array{
 *     status: string,
 *     is_valid: bool,
 *     is_production: bool,
 *     license: array{license_key: string, status: string}|null,
 *     entitlement: array{
 *         product_slug: string,
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         available: int,
 *         over_limit: bool,
 *         excess_activations: int,
 *         expiration_date: string,
 *         status: string,
 *         capabilities: list<string>
 *     }|null,
 *     activation: array{
 *         domain: string,
 *         activated_at: string
 *     }|null
 * }
 * @phpstan-type DeactivateResponsePayload array{deactivated: bool}
 * @phpstan-type DeleteActivationResponsePayload array{deleted: bool}
 * @phpstan-type StatusChangePayload array{license_key: string, status: string}
 * @phpstan-type RegenerateKeyResponsePayload array{license_key: string}
 * @phpstan-type ImportAliasesResponsePayload array{
 *     imported: list<array{alias_key: string, product_slug?: string|null}>
 * }
 * @phpstan-type RemoveAliasesResponsePayload array{removed: int}
 */
final class LicensesResource implements LicensesResourceInterface
{
	use RebindsAuthState;
	use RebindsRequestHeaderCollection;

	private RequestExecutor $requestExecutor;

	private ApiUriFactory $apiUriFactory;

	private AuthState $authState;

	private RequestHeaderCollection $requestHeaderCollection;

	public function __construct(
		RequestExecutor $requestExecutor,
		ApiUriFactory $apiUriFactory,
		AuthState $authState,
		RequestHeaderCollection $requestHeaderCollection
	) {
		$this->requestExecutor         = $requestExecutor;
		$this->apiUriFactory           = $apiUriFactory;
		$this->authState               = $authState;
		$this->requestHeaderCollection = $requestHeaderCollection;
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function activate(ActivateRequest $request): Activate {
		/** @var ActivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/activate'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var ActivateResponsePayload $result */
		return Activate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deactivate(DeactivateRequest $request): Deactivate {
		/** @var DeactivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/deactivate'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var DeactivateResponsePayload $result */
		return Deactivate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deleteActivation(DeleteActivationRequest $request): DeleteActivation {
		/** @var DeleteActivationPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/licenses/activation'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var DeleteActivationResponsePayload $result */
		return DeleteActivation::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(ListRequest $request): Listing {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/licenses'),
			$request->toQuery(),
			null,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var ListingPayload $result */
		return Listing::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, Listing, mixed, void>
	 */
	public function pages(ListRequest $request): Generator {
		$page = $this->list($request);

		while (true) {
			yield $page;

			if ($page->links->next === null) {
				return;
			}

			$result = $this->requestExecutor->executeJson(
				'GET',
				$this->apiUriFactory->fromPaginationLink($page->links->next),
				[],
				null,
				$this->authState->requiredToken(),
				$this->requestHeaderCollection->all()
			);

			/** @var ListingPayload $result */
			$page = Listing::from($result);
		}
	}

	/**
	 * @param list<string> $productSlugs
	 *
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function validate(string $licenseKey, array $productSlugs, string $domain): Validate {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/licenses/validate'),
			[
				'license_key'   => $licenseKey,
				'product_slugs' => $productSlugs,
				'domain'        => $domain,
			],
			null,
			$this->authState->optionalToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var ValidatePayload $result */
		return Validate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function suspend(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/suspend', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function reinstate(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/reinstate', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function ban(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/ban', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function regenerateKey(RegenerateKeyRequest $request): RegenerateKey {
		/** @var RegenerateKeyPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/regenerate-key'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var RegenerateKeyResponsePayload $result */
		return RegenerateKey::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function importAliases(ImportAliasesRequest $request): ImportAliases {
		/** @var ImportAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var ImportAliasesResponsePayload $result */
		return ImportAliases::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function removeAliases(RemoveAliasesRequest $request): RemoveAliases {
		/** @var RemoveAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var RemoveAliasesResponsePayload $result */
		return RemoveAliases::from($result);
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $authState, $this->requestHeaderCollection);
	}

	protected function rebindWithRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $this->authState, $requestHeaderCollection);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	private function changeLicenseStatus(string $path, LicenseReference $request): StatusChange {
		/** @var LicenseReferencePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make($path),
			[],
			$body,
			$this->authState->requiredToken(),
			$this->requestHeaderCollection->all()
		);

		/** @var StatusChangePayload $result */
		return StatusChange::from($result);
	}
}
