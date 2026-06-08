<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Wp;

use RebelCode\Spotlight\Instagram\Config\ConfigEntry;

class Notice
{
    const INFO = 'info';
    const ERROR = 'error';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const NONE = '';

    /** @var string */
    public $id;

    /** @var string */
    public $type;

    /** @var string */
    public $content;

    /** @var bool|callable */
    public $dismiss;

    /** Constructor */
    public function __construct(string $id, string $type, $dismiss, string $content)
    {
        $this->id = $id;
        $this->type = $type;
        $this->dismiss = $dismiss;
        $this->content = $content;
    }

    /** Renders the notice */
    public function render(): string
    {
        $id = "sli-notice-{$this->id}";

        $class = ['sli-notice', 'notice', 'notice-' . $this->type];
        if ($this->dismiss) {
            $class[] = 'is-dismissible';
        }

        return sprintf(
            '<div id="%s" class="%s" data-notice="%s">%s</div>',
            esc_attr($id),
            esc_attr(implode(' ', $class)),
            esc_attr($this->id),
            wpautop($this->content)
        );
    }

    /** Utility method for $dismiss. Useful for creating Notices that simply enable an option when dismissed. */
    public static function enableOption(ConfigEntry $entry): callable
    {
        return function () use ($entry) { $entry->setValue(1); };
    }

    /** Utility method for $dismiss. Useful for creating Notices that simply disable an option when dismissed. */
    public static function disableOption(ConfigEntry $entry): callable
    {
        return function () use ($entry) { $entry->setValue(0); };
    }
}
