<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * The rules which all widgets must follow.
 *
 * Combination of `Widget\Widget` and the `Widget\WidgetId` interfaces.
 *
 * @author     OnPoint Plugins
 * @since      9.6.0
 *
 * @todo       Step 3 of the migration plan: Once the minimum basic version is 9.8.0+
 *
 * @internal   A temporary interface until we can complete the migration plan.
 *           1. ~~Remove the `WidgetId` interface by moving all rules to `Widget` once the minimum PRO version is 9.9.0+.~~
 *           2. ~~Make note in the PRO version the `WidgetWithId` interface is deprecated and uses may be removed at the next basic
 *           version~~.
 *           3. Remove uses from the PRO version in favor of the `Widget` interface only when required basic version is 9.8.0.
 *           4. Make note in the basic version the `WidgetWithId` interface is deprecated and uses may be removed at the next PRO version.
 *           5. Remove uses from the basic version in favor of the `Widget` interface only on the next PRO version is that of step 4.
 *
 *
 * @deprecated 9.8.0 use `Widget` instead.
 *
 * @phpstan-template SETTINGS of array<string, string|int|bool|array<string, string>>
 * @phpstan-template DEFAULTS of array<key-of<SETTINGS>, int|string|array<string, string>>
 *
 * @extends Widget<SETTINGS, DEFAULTS>
 */
interface WidgetWithId extends Widget {
}
