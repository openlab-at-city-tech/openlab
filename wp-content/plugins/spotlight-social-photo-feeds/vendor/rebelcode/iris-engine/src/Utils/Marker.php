<?php

declare(strict_types=1);

namespace RebelCode\Iris\Utils;

/** Represents a flag that is persisted somehow, to preserve its value between invocations and threads. */
interface Marker
{
    public function create(): void;

    public function isSet(): bool;

    public function delete(): void;
}
