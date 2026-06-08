<?php

declare(strict_types=1);

namespace RebelCode\Iris\Data;

/** @psalm-immutable */
class Source extends ImmutableDataObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $type;

    /**
     * Constructor.
     *
     * @param string $id An ID that uniquely identifies this source from other sources of the same type.
     * @param string $type A string that categorizes the source.
     * @param array<string, mixed> $data Optional additional data for this source, such as meta info or config.
     */
    public function __construct(string $id, string $type, array $data = [])
    {
        parent::__construct($data);
        $this->id = $id;
        $this->type = $type;
    }

    public static function fromString(string $string): Source
    {
        $parts = explode('||', $string);

        return new Source($parts[0] ?? '', $parts[1] ?? '');
    }

    public function __toString(): string
    {
        return $this->id . '||' . $this->type;
    }
}
