<?php
/**
 * Kadence\Service_Provider class
 *
 * @package kadence
 */

namespace Kadence;

use Kadence\lucatume\DI52\ServiceProvider;

/**
 * Base class for the theme's service providers.
 *
 * A provider binds one feature's services in `register()` and adds its hooks there.
 * `$this->container` is the container the provider is registered with.
 *
 * @since 1.6.0
 */
abstract class Service_Provider extends ServiceProvider {
}
