# Licensing API Client WordPress

> ⚠️ **This is a read-only repository!**
> For pull requests or issues, see [stellarwp/licensing-api-client-monorepo](https://github.com/stellarwp/licensing-api-client-monorepo).

WordPress transport and factory integration for the StellarWP Licensing API client.

## Installation

Install with composer:

```shell
composer require stellarwp/licensing-api-client-wordpress
```

## Examples

For end-to-end API cookbook examples, see:

- [API Examples](https://github.com/stellarwp/licensing-api-client-monorepo/blob/main/docs/examples/index.md)

## Usage

For a DI52 Provider:

```php
<?php declare(strict_types=1);

namespace MyPlugin\Providers;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use LiquidWeb\LicensingApiClient\Api;
use LiquidWeb\LicensingApiClient\Config;
use LiquidWeb\LicensingApiClient\Contracts\LicensingClientInterface;
use LiquidWeb\LicensingApiClient\Http\ApiVersion;
use LiquidWeb\LicensingApiClient\Http\AuthContext;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClientWordPress\Http\WordPressHttpClient;
use lucatume\DI52\ServiceProvider;
use MyPlugin\Support\CurrentRequestTraceParent;

final class LicensingApiProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->container->singleton(WordPressHttpClient::class);
		$this->container->singleton(Psr17Factory::class);

		$this->container->when(RequestExecutor::class)
		                ->needs(ClientInterface::class)
		                ->give(static fn( $c ): ClientInterface => $c->get(WordPressHttpClient::class));

		$this->container->bind(
			RequestFactoryInterface::class,
			static fn( $c ): RequestFactoryInterface => $c->get(Psr17Factory::class)
		);

		$this->container->bind(
			StreamFactoryInterface::class,
			static fn( $c ): StreamFactoryInterface => $c->get(Psr17Factory::class)
		);

		$this->container->singleton(
			Config::class,
			static function (): Config {
				return new Config(
					'https://licensing.example.com',
					null, // Pass a token if you plan to make authenticated requests.
					'my-plugin/1.0.0' // Your client user agent.
				);
			}
		);

		$this->container->singleton(
			AuthState::class,
			static fn( $c ): AuthState => new AuthState(
				new AuthContext(),
				$c->get(Config::class)->configuredToken
			)
		);

		$this->container->singleton(
			ApiVersion::class,
			static fn(): ApiVersion => ApiVersion::default()
		);

		$this->container->singleton(CurrentRequestTraceParent::class);
		$this->container->singleton(Api::class);

		$this->container->bind(
			LicensingClientInterface::class,
			static fn( $c ): LicensingClientInterface => $c
				->get(Api::class)
				->withTraceParent($c->get(CurrentRequestTraceParent::class)->traceParent())
		);
	}
}
```

That gives you a base client as a singleton, but resolves the public `LicensingClientInterface` as a fresh clone with one stable TraceParent applied for the current PHP request.

That is usually what you want for tracing: if one request in your application makes multiple licensing calls, those calls should normally share the same TraceParent so they can be tied together in Axiom as part of the same trace.

> [!WARNING]
> That only works end to end if your own application is also exporting spans to Axiom or another tracing backend. If your application is not instrumented, the licensing request can still carry the propagated `traceparent`, but Axiom will not be able to look up the true parent span because it never received it.
>
> If an upstream service already gives you a W3C `traceparent` header, parse that into a `TraceParent` and use `TraceContext` when you also need to preserve inbound `tracestate`. If you only have a generic correlation ID, do not try to stuff that into a `TraceParent`; keep it as separate application metadata and generate a new `TraceParent` locally for distributed tracing.

DI52 does not provide a built-in request scope, but in a normal short-lived PHP request this singleton is still effectively request-local.

One simple implementation looks like this:

```php
<?php declare(strict_types=1);

namespace MyPlugin\Support;

use LiquidWeb\LicensingApiClient\Tracing\TraceParent;

final class CurrentRequestTraceParent
{
	private TraceParent $traceParent;

	public function __construct()
	{
		$this->traceParent = TraceParent::generate();
	}

	public function traceParent(): TraceParent
	{
		return $this->traceParent;
	}
}
```

If you are continuing inbound trace headers instead, build a `TraceContext` from them and apply that to the cloned client:

```php
<?php declare(strict_types=1);

use LiquidWeb\LicensingApiClient\Tracing\TraceContext;
use LiquidWeb\LicensingApiClient\Tracing\TraceParent;

$traceContext = TraceContext::fromValues(
	TraceParent::fromString($incomingTraceparent),
	$incomingTracestate ?? null
);

$api = $container
	->get(Api::class)
	->withTraceContext($traceContext);
```

The important detail is that `AuthState` is built from `Config::configuredToken`, so your configured token only lives in one place:

```php
$api = $container->get(LicensingClientInterface::class);
```

API errors are thrown as exceptions, so catch the specific cases you care about and fall back to `ApiErrorExceptionInterface` for the rest:

```php
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\NotFoundException;
use LiquidWeb\LicensingApiClient\Exceptions\ValidationException;

try {
	$catalog = $api->products()->catalog('LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N');

	$validation = $api->licenses()->validate(
		'LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N',
		['kadence', 'learndash'],
		'customer-site.com'
	);

	$balances = $api->withConfiguredToken()->credits()->balance(
		'LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N',
		'customer-site.com'
	);

	if ($catalog->products->isCapabilityValid('kadence', 'blocks')) {
		// ...
	}

	if ($validation->products->isCapabilityValid('learndash', 'blocks')) {
		// ...
	}
} catch (NotFoundException $e) {
	// Return the API message when the requested record does not exist.
	return [
		'success' => false,
		'message' => $e->getMessage(),
	];
} catch (ValidationException $e) {
	// Return the validation message and log the details for debugging.
	$this->logger->warning('Licensing validation failed.', [
		'message' => $e->getMessage(),
		'code' => $e->errorCode(),
	]);

	return [
		'success' => false,
		'message' => $e->getMessage(),
	];
} catch (ApiErrorExceptionInterface $e) {
	// Log unexpected API-declared errors and return a generic failure message.
	$this->logger->error('Licensing API request failed.', [
		'status' => $e->getResponse()->getStatusCode(),
		'code' => $e->errorCode(),
		'message' => $e->getMessage(),
	]);

	return [
		'success' => false,
		'message' => 'We could not complete the licensing request right now. Please try again later.',
	];
}
```

For a public or unauthenticated client without a Container:

```php
<?php declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use LiquidWeb\LicensingApiClient\Config;
use LiquidWeb\LicensingApiClientWordPress\Http\WordPressHttpClient;
use LiquidWeb\LicensingApiClientWordPress\WordPressApiFactory;

$psr17 = new Psr17Factory();

$api = (new WordPressApiFactory(
    new WordPressHttpClient(),
    $psr17,
    $psr17
))->make(new Config(
    'https://licensing.example.com',
    null,
    'my-plugin/1.0.0' // Your client user agent.
));
```

For a trusted source with a configured token:

```php
<?php declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use LiquidWeb\LicensingApiClient\Config;
use LiquidWeb\LicensingApiClientWordPress\Http\WordPressHttpClient;
use LiquidWeb\LicensingApiClientWordPress\WordPressApiFactory;

$psr17 = new Psr17Factory();

$api = (new WordPressApiFactory(
    new WordPressHttpClient(),
    $psr17,
    $psr17
))->make(new Config(
    'https://licensing.example.com',
    'pk_test_your_token_here',
    'portal/1.0.0' // Your client user agent.
));

$trustedApi = $api->withConfiguredToken();
```

## Status

This package is being developed in the monorepo and published as a read-only split repository.
