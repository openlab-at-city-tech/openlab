# Licensing API Client

> ⚠️ **This is a read-only repository!**
> For pull requests or issues, see [stellarwp/licensing-api-client-monorepo](https://github.com/stellarwp/licensing-api-client-monorepo).

A PHP client for the Liquid Web Licensing API.

💡 In most cases you should use one of the transport-specific client packages instead of installing this package directly unless you plan to provide your own HTTP client:

- [stellarwp/licensing-api-client-wordpress](https://github.com/stellarwp/licensing-api-client-wordpress)
- [stellarwp/licensing-api-client-guzzle](https://github.com/stellarwp/licensing-api-client-guzzle)

This package is the core API layer they build on top of.

## Installation

Install with composer:

```shell
composer require stellarwp/licensing-api-client
```

## Examples

For end-to-end API cookbook examples, see:

- [API Examples](https://github.com/stellarwp/licensing-api-client-monorepo/blob/main/docs/examples/index.md)

Short example:

```php
<?php declare(strict_types=1);

use LiquidWeb\LicensingApiClient\Tracing\TraceParent;

$catalog = $api
	->withTraceParent(TraceParent::generate())
	->products()
	->catalog('LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N', 'example.com');
```

Use `withTraceParent()` when you want the SDK to carry one validated W3C `traceparent` value. Build it with `TraceParent::generate()` when you are starting a new trace locally, or `TraceParent::fromString()` when you are continuing one from an inbound request. If you also have vendor-specific `tracestate`, use `TraceContext::fromValues()` and pass that to `withTraceContext()` instead. Use `withHeaders()` for unrelated custom headers.

> [!WARNING]
> This only pays off when your own application is also exporting spans to Axiom or another tracing backend. If your application is not instrumented, the licensing service can still continue the propagated trace context, but Axiom will not be able to find the real parent span because it never received it.

## Status

This package is being developed in the monorepo and published as a read-only split repository.
