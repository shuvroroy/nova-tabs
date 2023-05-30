# Nova tabs

[![PHP Version Require](http://poser.pugx.org/shuvroroy/nova-tabs/require/php)](https://packagist.org/packages/shuvroroy/nova-tabs)
[![Latest Stable Version](http://poser.pugx.org/shuvroroy/nova-tabs/v)](https://packagist.org/packages/shuvroroy/nova-tabs) 
[![Total Downloads](http://poser.pugx.org/shuvroroy/nova-tabs/downloads)](https://packagist.org/packages/shuvroroy/nova-tabs) 
[![License](http://poser.pugx.org/shuvroroy/nova-tabs/license)](https://packagist.org/packages/shuvroroy/nova-tabs) 

This package will help you to create tabs in your resource detail & form page.

<img width="1490" alt="Screenshot 2023-05-24 at 7 56 19 PM" src="https://github.com/shuvroroy/nova-tabs/assets/21066418/04d25d67-f641-4c14-8c03-a100f14ffb9e">

## Support For This Project

<a href="https://www.buymeacoffee.com/shuvroroy" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a>

## Requirements

- PHP (^8.1 or higher)
- Laravel Nova (^4.12 or higher)

## Installation

Require the package with composer

```bash
composer require shuvroroy/nova-tabs
```

## Usage

### Tabs Panel

You can group fields of a resource into tabs.

```php
<?php

use App\Nova\Resource;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShuvroRoy\NovaTabs\Tab;
use ShuvroRoy\NovaTabs\Tabs;
use ShuvroRoy\NovaTabs\Traits\HasActionsInTabs;
use ShuvroRoy\NovaTabs\Traits\HasTabs;

class User extends Resource
{
    use HasTabs, HasActionsInTabs;

    public function fields(Request $request)
    {
        return [
            Tabs::make('Author', [
                Tab::make(__('Author Details'), [
                    ID::make()->sortable(),

                    Avatar::make(__('Photo'), 'photo')
                        ->disk('public')
                        ->indexWidth(50)
                        ->detailWidth(200)
                        ->squared()
                        ->disableDownload()
                        ->showOnPreview(),

                    Stack::make('Name', [
                        Line::make(__('Name'), 'name')
                            ->asHeading(),
                        Line::make(__('Email'), 'email')
                            ->asSmall()
                    ])->onlyOnIndex(),

                    Text::make(__('Name'), 'name')
                        ->sortable()
                        ->rules('required', 'max:255')
                        ->hideFromIndex()
                        ->showOnPreview(),

                    Text::make(__('Email'), 'email')
                        ->sortable()
                        ->rules('required', 'email', 'max:255')
                        ->creationRules('unique:authors,email')
                        ->updateRules('unique:authors,email,{{resourceId}}')
                        ->hideFromIndex()
                        ->showOnPreview(),

                    HasMany::make(__('Posts'), 'posts', Post::class),
                ])->showHeadingForRelationships(['posts']),
                
                Tab::make(__('Additional Information'), [
                    Trix::make(__('Bio'), 'bio')
                        ->alwaysShow()
                        ->showOnPreview(),

                    Text::make(__('Github Handle'), 'github_handle')
                        ->sortable()
                        ->rules('required', 'max:255')
                        ->showOnPreview(),

                    Text::make(__('Twitter Handle'), 'twitter_handle')
                        ->sortable()
                        ->rules('required', 'max:255')
                        ->showOnPreview(),
                ]),
           ]),
       ];
    }
 }
```

The first tab in every `Tabs` instance will be auto-selected.

### Combine Fields and Relations in Tabs

```php
<?php

use ShuvroRoy\NovaTabs\Tab;
use ShuvroRoy\NovaTabs\Tabs;
use ShuvroRoy\NovaTabs\Traits\HasActionsInTabs;
use ShuvroRoy\NovaTabs\Traits\HasTabs;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class User extends Resource
{
    use HasTabs, HasActionsInTabs;

    public function fields(Request $request)
    {
          return [
              Tabs::make(__('Client Custom Details'), [
                  Tab::make(__('Details'), [
                      ID::make('Id', 'id')->rules('required')->hideFromIndex(),
                      Text::make('Name', 'name'),

                      HasMany::make('Invoices', 'invoices'),
                  ])->showHeadingForRelationships(['invoices']),
              ]),
         ];
    }
}
```

### Tabs on Edit View

Tabs are always shown on edit view as of Nova 4 for now.

<img width="1488" alt="Screenshot 2023-05-24 at 7 54 55 PM" src="https://github.com/shuvroroy/nova-tabs/assets/21066418/9b6a9850-9ea2-4386-aa82-59541903cb96">

## Tab object

It's possible to use a `Tab` class instead of an array to represent your tabs.

| Property   | Type                | Default | Description                                                                                                                         |
| ---------- | ------------------- | ------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| name       | `string`            | `null`  | The name of the tab, used for the slug. Defaults to the title if not set                                                            |
| showIf     | `bool` or `Closure` | `null`  | If the result is truthy the tab will be shown. `showIf` takes priority over `showUnless` and if neither are set, `true` is assumed. |
| showUnless | `bool` or `Closure` | `null`  | If the result is falsy the tab will be shown. `showIf` takes priority over `showUnless` and if neither are set, `true` is assumed.  |
| showHeadingForRelationships | `array` | `array`  | This is show a headings for individual relationship field based on array attribute value |

## Customization

### Display more than 5 items

By default, any `HasMany`, `BelongsToMany` and `MorphMany` fields show 5 items in their index. You can use Nova's built-in static property `$perPageViaRelationship` on the respective resource to show more (or less).

### Tab change Global Event

Nova Tabs emits an event upon tabs loading and the user clicking on a tab, using [Nova Event Bus](https://nova.laravel.com/docs/4.0/customization/frontend.html#event-bus). Developers can listen to the event called `nova-tabs-changed`, with 2 parameters as payload: The tab panel name and the tab name itself.

Example of a component that subscribes to this event:

```ES6
export default {
    mounted () {
        Nova.$on('nova-tabs-changed', (panel, tab) => {
            if (panel === 'Client Details' && tab === 'address') {
                this.$nextTick(() => this.map.updateSize())
            }
        })
    }
}
```

## Credits

- [Shuvro Roy](https://github.com/shuvroroy)
- [Emini Arts GmbH](https://github.com/eminiarts)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
