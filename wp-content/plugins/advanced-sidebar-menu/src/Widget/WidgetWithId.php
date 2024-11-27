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
 * @phpstan-template SETTINGS of array<string, mixed>
 * @phpstan-template DEFAULTS of array<key-of<SETTINGS>, mixed>
 *
 * @todo   Switch all PRO `Widget` classes to use this interface once minimum basic version is 9.6.0+.
 * @todo   Add a @todo to `__Temp_Id_Proxy` to remove it when minimum PRO version is whatever the new version is.
 *
 * @extends Widget<SETTINGS, DEFAULTS>
 * @extends WidgetId<SETTINGS>
 */
interface WidgetWithId extends Widget, WidgetId {
}
