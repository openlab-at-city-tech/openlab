<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\BlackFriday;

/**
 * Decides whether the Black Friday campaign is running, using the server clock, and remembers per user whether the
 * campaign banner was dismissed.
 *
 * The admin UI shows the campaign to Free users only; the tier check lives in the UI with every other upsell.
 */
class BlackFridayCampaign
{
    /**
     * Local QA only: define this constant as true or false (for example with `wp config set`) to force the campaign on
     * or off, whatever the date.
     */
    public const FORCE_CONSTANT = 'SL_INSTA_FORCE_BLACK_FRIDAY';

    /** @var int */
    protected $start;

    /** @var int */
    protected $end;

    /** @var string */
    protected $metaKey;

    /**
     * @param int    $start   When the campaign starts, as a timestamp.
     * @param int    $end     The last second of the campaign, as a timestamp. The campaign still runs at this second.
     * @param string $metaKey The user meta key that records the banner dismissal.
     */
    public function __construct(int $start, int $end, string $metaKey)
    {
        $this->start = $start;
        $this->end = $end;
        $this->metaKey = $metaKey;
    }

    public function isActive(): bool
    {
        if (defined(static::FORCE_CONSTANT)) {
            $active = (bool) constant(static::FORCE_CONSTANT);
        } else {
            $now = time();
            $active = $now >= $this->start && $now <= $this->end;
        }

        return (bool) apply_filters('spotlight/instagram/black_friday/is_active', $active, $this);
    }

    public function isDismissed(): bool
    {
        $userId = get_current_user_id();

        return $userId > 0 && (bool) get_user_meta($userId, $this->metaKey, true);
    }

    public function dismiss(): void
    {
        $userId = get_current_user_id();

        if ($userId > 0) {
            update_user_meta($userId, $this->metaKey, 1);
        }
    }

    /** The campaign state for the admin UI. */
    public function getData(): array
    {
        $isActive = $this->isActive();

        return [
            'isActive' => $isActive,
            'isDismissed' => $isActive && $this->isDismissed(),
        ];
    }
}
