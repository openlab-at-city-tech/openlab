<?php

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Plugin;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Utils\Types;
use ReflectionClass;
use ReflectionException;
use wpdb;

/**
 * Renders a hierarchical graph of services from a container, showing their resolved values.
 *
 * @since 0.1
 */
class ServiceTree
{
    /**
     * Maximum depth by which to expand objects and arrays.
     *
     * @since 0.1
     */
    const DEPTH = 3;
    /**
     * Depth overrides for specific classes.
     */
    const DEPTH_OVERRIDES = [
        wpdb::class => 0,
        Plugin::class => 0,
    ];

    /**
     * Builds a service tree.
     *
     * This algorithm builds the tree by interpreting service keys as paths.
     *
     * @since 0.1
     *
     * @param string             $delimiter The delimiter used to separate path segments.
     * @param array              $services  The service keys.
     * @param ContainerInterface $container The container to use to resolve services.
     *
     * @return array An associative array that maps service key to a sub-array. Each sub-array will have a "__value"
     *               index that maps to the service's value, unless the service key is only a path node in which case
     *               the "__value" index will be omitted. In addition to the value, each sub-array will contain
     *               additional mappings for any child services, each of which map to a similar sub-array.
     */
    public static function buildTree(string $delimiter, array $services, ContainerInterface $container)
    {
        $tree = [];

        foreach ($services as $service) {
            try {
                $value = $container->get($service);
            } catch (ContainerExceptionInterface $exception) {
                throw $exception;
            }

            $path = explode($delimiter, $service);
            Arrays::setPath($tree, $path, ['__value' => $value]);
        }

        return $tree;
    }

    /**
     * Renders a built tree into HTML.
     *
     * @since 0.1
     *
     * @see   ServiceTree::buildTree()
     *
     * @param array $tree A built tree. See {@link ServiceTree::buildTree()}.
     *
     * @return string The rendered HTML.
     */
    public static function renderTree(array $tree)
    {
        ob_start();

        ?>
        <style>
            div.sli-service-graph {
                font-family: monospace;
            }

            .service {
                margin: 1px 0;
            }

            .service-list > .service-children > .service-list > .service::before {
                position: absolute;
                content: '─';
                left: 0;
            }

            .service-children {
                position: relative;
                margin: 0 0 0 0.5em;
                padding-left: 1.5em;
                border-left: 1px solid #444;
            }

            .service-name {
                font-weight: 500;
            }

            details {
                display: inline-block;
                vertical-align: text-top;
            }

            details summary {
                font-size: 11px;
                font-family: monospace;
                cursor: pointer;
            }

            details div {
                padding: 4px 6px;
                background: #fff;
                border-radius: 4px;
            }

            pre {
                display: inline;
                font-size: 11px;
                color: #555;
                background: transparent;
                white-space: pre-wrap;
                padding: 2px 0;
            }
        </style>
        <div class="sli-service-graph"><?php static::printTree($tree) ?></div>
        <?php

        return ob_get_clean();
    }

    /**
     * Outputs a service tree.
     *
     * @since 0.1
     *
     * @param array $tree The tree to output.
     */
    public static function printTree(array $tree)
    {
        echo '<div class="service-list">';

        foreach ($tree as $key => $node) {
            printf('<div class="service"><span class="service-name">%s</span>', $key);

            if (isset($node['__value'])) {
                $value = $node['__value'];

                echo ' = <details>';
                printf('<summary>&lt;%s&gt;</summary>', Types::getType($value));
                echo '<div><pre>';
                static::printValue(static::buildValue($value));
                echo '</pre></div>';
                echo '</details>';
            }

            echo '</div>';
            unset($node['__value']);

            if (count($tree) > 0) {
                echo '<div class="service-children">';
                static::printTree($node);
                echo '</div>';
            }
        }

        echo '</div>';
    }

    /**
     * Builds a value, which can be outputted later.
     *
     * This formats values (such as null and booleans) or processes them into an intermediate format (such as for
     * arrays and objects).
     *
     * @since 0.1
     *
     * @param mixed $val   The value.
     * @param int   $depth The depth up to which to expand objects and arrays.
     *
     * @return array|string The printable value, array or object in array form with meta data (__CLASS AND __PROPS).
     */
    public static function buildValue($val, int $depth = self::DEPTH)
    {
        if ($val === null) {
            return 'null';
        }

        if (is_bool($val)) {
            return $val ? 'true' : 'false';
        }

        if (is_object($val)) {
            $class = get_class($val);

            foreach (static::DEPTH_OVERRIDES as $key => $override) {
                if ($class === $key) {
                    $depth = $override;
                    break;
                }
            }

            $props = static::getObjectProps($val, $depth);

            return [
                '__CLASS' => $class,
                '__PROPS' => $props,
            ];
        }

        if (is_array($val)) {
            return Arrays::map($val, function ($elem) use ($depth) {
                return static::buildValue($elem, $depth - 1);
            });
        }

        return $val;
    }

    /**
     * Outputs a built value.
     *
     * @since 0.1
     *
     * @param mixed $value  The value to output.
     * @param int   $indent The current indent level. Used by recursive calls. Should be set to 0 for normal calls.
     */
    public static function printValue($value, int $indent = 0)
    {
        if (is_array($value)) {
            $isObject = isset($value['__CLASS']);
            $props = $isObject ? $value['__PROPS'] : $value;
            $isEmpty = empty($props);

            echo $isObject ? '{' : '[';

            if (is_array($props)) {
                echo $isEmpty ? '' : "\n";
                ++$indent;

                foreach ($props as $key => $propVal) {
                    echo str_repeat(' ', 4 * $indent) . $key . ': ';
                    echo static::printValue($propVal, $indent) . ",\n";
                }

                --$indent;
                echo $isEmpty ? '' : str_repeat(' ', 4 * $indent);
            } else {
                echo ' ' . $props . ' ';
            }

            echo $isObject ? '}' : ']';
        } else {
            echo strval($value);
        }
    }

    /**
     * Uses reflection to retrieve and object's properties.
     *
     * @since 0.1
     *
     * @param object $object The object.
     * @param int    $depth  The depth up to which to recurse for object properties.
     *
     * @return array|string An associative array of prop names to corresponding values, or a string if the max depth
     *                      is reached or failed to get reflection information.
     */
    public static function getObjectProps($object, int $depth = self::DEPTH)
    {
        if ($depth <= 0) {
            return "...";
        }

        try {
            $ref = new ReflectionClass($object);
        } catch (ReflectionException $exception) {
            return "...";
        }

        $results = [];

        $props = $ref->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);

            $results[$prop->getName()] = self::buildValue($prop->getValue($object), $depth - 1);
        }

        $sProps = $ref->getStaticProperties();
        foreach ($sProps as $key => $val) {
            $results['[static] ' . $key] = self::buildValue($val, $depth - 1);
        }

        return $results;
    }
}
