<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * The rules which all widgets must follow.
 *
 * Combination of `Widget\Widget` and the `Widget\WidgetId` interfaces.
 *
 * @author OnPoint Plugins
 * @since  9.6.0
 *
 * @phpstan-template SETTINGS of array<string, string|int|bool|array<string, string>>
 * @phpstan-template DEFAULTS of array<key-of<SETTINGS>, int|string|array<string, string>>
 *
 * @extends Widget<SETTINGS, DEFAULTS>
 * @extends WidgetId<SETTINGS>
 */
interface WidgetWithId extends Widget, WidgetId {
}
