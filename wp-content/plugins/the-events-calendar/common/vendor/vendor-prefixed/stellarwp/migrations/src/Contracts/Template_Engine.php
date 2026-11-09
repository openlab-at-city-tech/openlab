<?php

/**
 * Template Engine Interface.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Contracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Contracts;

/**
 * Template Engine Interface.
 *
 * Consumers of the migrations library must implement this interface to provide
 * template rendering capabilities. This allows the library to remain agnostic
 * about the specific template engine used by the consumer.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Contracts
 */
interface Template_Engine
{
    /**
     * Render a template.
     *
     * @since 0.0.1
     *
     * @param string              $name    Template name (e.g., 'list', 'components/progress-bar').
     * @param array<string,mixed> $context Variables to pass to the template.
     * @param bool                $output  Whether to echo or return the output.
     *
     * @return string|void The rendered template if $echo is false, void otherwise.
     */
    public function template(string $name, array $context = [], bool $output = true);
}