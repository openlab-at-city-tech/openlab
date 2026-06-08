<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Di;

use Dhii\Services\Factory;
use RebelCode\Spotlight\Instagram\RestApi\EndPoint;

class EndPointService extends Factory
{
    /** @var string */
    protected $route;

    /** @var string[] */
    protected $methods;

    /** @var string */
    protected $class;

    /** Constructor */
    public function __construct(string $route, array $methods, string $class, array $ctorDeps, string $authGuard)
    {
        $dependencies = $ctorDeps;
        $dependencies[] = $authGuard;

        parent::__construct($dependencies, [$this, 'definition']);

        $this->route = $route;
        $this->methods = $methods;
        $this->class = $class;
    }

    public function definition(): EndPoint
    {
        $deps = func_get_args();
        $authGuard = array_pop($deps);
        $className = $this->class;

        return new EndPoint(
            $this->route,
            $this->methods,
            new $className(...$deps),
            $authGuard
        );
    }
}
