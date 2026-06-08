<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Transformers;

use Dhii\Transformer\TransformerInterface;

/**
 * A REST API transformer that is composed of other transformers.
 *
 * The children transformers are called in sequence; each transformer will be given the previous transformer's result
 * as input, making the order of transformers potentially an important factor.
 *
 * @since 0.1
 */
class CompositeTransformer implements TransformerInterface
{
    /**
     * @since 0.1
     *
     * @var TransformerInterface[]
     */
    protected $transformers;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param TransformerInterface[] $transformers
     */
    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function transform($source)
    {
        foreach ($this->transformers as $transformer) {
            $source = $transformer->transform($source);
        }

        return $source;
    }
}
