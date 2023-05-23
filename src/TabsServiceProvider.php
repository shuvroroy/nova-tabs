<?php

declare(strict_types=1);

namespace ShuvroRoy\NovaTabs;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class TabsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-tabs', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-tabs', __DIR__ . '/../dist/css/field.css');
        });
    }
}
