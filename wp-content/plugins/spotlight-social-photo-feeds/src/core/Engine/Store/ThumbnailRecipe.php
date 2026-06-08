<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Engine\Store;

class ThumbnailRecipe
{
    /** @var bool Whether the recipe is enabled or not. */
    public $enabled;

    /** @var int The width of the thumbnail. */
    public $width;

    /** @var int The JPEG quality, between 0 and 100. */
    public $quality;

    /** Constructor. */
    public function __construct(bool $enabled, int $width, int $quality)
    {
        $this->enabled = $enabled;
        $this->width = $width;
        $this->quality = $quality;
    }

    public function isEqualTo(ThumbnailRecipe $other): bool
    {
        return $this->width === $other->width && $this->quality === $other->quality;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['enabled'] ?? false,
            intval($data['width'] ?? "0"),
            intval($data['quality'] ?? "100")
        );
    }
}
