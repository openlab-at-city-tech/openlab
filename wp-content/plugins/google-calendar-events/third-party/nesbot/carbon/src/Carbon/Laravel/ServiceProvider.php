<?php

namespace SimpleCalendar\plugin_deps\Carbon\Laravel;

use SimpleCalendar\plugin_deps\Carbon\Carbon;
use SimpleCalendar\plugin_deps\Carbon\CarbonImmutable;
use SimpleCalendar\plugin_deps\Carbon\CarbonInterval;
use SimpleCalendar\plugin_deps\Carbon\CarbonPeriod;
use SimpleCalendar\plugin_deps\Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use SimpleCalendar\plugin_deps\Illuminate\Events\Dispatcher;
use SimpleCalendar\plugin_deps\Illuminate\Events\EventDispatcher;
use SimpleCalendar\plugin_deps\Illuminate\Support\Carbon as IlluminateCarbon;
use SimpleCalendar\plugin_deps\Illuminate\Support\Facades\Date;
use Throwable;
class ServiceProvider extends \SimpleCalendar\plugin_deps\Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->updateLocale();
        if (!$this->app->bound('events')) {
            return;
        }
        $service = $this;
        $events = $this->app['events'];
        if ($this->isEventDispatcher($events)) {
            $events->listen(\class_exists('SimpleCalendar\\plugin_deps\\Illuminate\\Foundation\\Events\\LocaleUpdated') ? 'Illuminate\\Foundation\\Events\\LocaleUpdated' : 'locale.changed', function () use($service) {
                $service->updateLocale();
            });
        }
    }
    public function updateLocale()
    {
        $app = $this->app && \method_exists($this->app, 'getLocale') ? $this->app : app('translator');
        $locale = $app->getLocale();
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);
        if (\class_exists(IlluminateCarbon::class)) {
            IlluminateCarbon::setLocale($locale);
        }
        if (\class_exists(Date::class)) {
            try {
                $root = Date::getFacadeRoot();
                $root->setLocale($locale);
            } catch (Throwable $e) {
                // Non Carbon class in use in Date facade
            }
        }
    }
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
    protected function isEventDispatcher($instance)
    {
        return $instance instanceof EventDispatcher || $instance instanceof Dispatcher || $instance instanceof DispatcherContract;
    }
}
