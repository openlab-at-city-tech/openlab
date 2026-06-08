<?php

namespace RebelCode\Atlas;

use RebelCode\Atlas\Exception\QueryCompileException;

interface QueryTypeInterface
{
    /**
     * @psalm-mutation-free
     *
     * @param Query $query The query.
     *
     * @throws QueryCompileException If the query could not be compiled.
     */
    public function compile(Query $query): string;
}
