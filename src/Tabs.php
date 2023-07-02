<?php

declare(strict_types=1);

namespace ShuvroRoy\NovaTabs;

use Closure;
use ShuvroRoy\NovaTabs\Contracts\TabContract;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Panel;
use function is_array;
use function is_callable;
use Laravel\Nova\Fields\Field;

class Tabs extends Panel
{
    public bool $defaultSearch = false;

    public bool $showTitle = false;

    public bool $selectFirstTab = true;

    private int $tabsCount = 0;

    public ?string $slug = null;

    private string $preservedName;

    public bool $retainTabPosition = false;

    public ?string $currentColor = null;

    public string $errorColor = 'red';

    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  (\Closure():array|iterable)|array  $fields
     * @return void
     */
    public function __construct($name, $fields = [])
    {
        $this->name = $name;
        $this->preservedName = $name;
        $this->withComponent('tabs');

        parent::__construct($name, $fields);
    }

    public function withSlug(string|bool $slug): self
    {
        $this->slug = is_bool($slug) ? ($slug ? Str::slug($this->preservedName, '_') : null) : $slug;

        return $this;
    }

    public function withCurrentColor(string $color): self
    {

        $this->currentColor = $color;

        return $this;
    }

    public function withErrorColor(string $color): self
    {

        $this->errorColor = $color;

        return $this;
    }

    public function rememberTabs(bool $retain): self
    {
        $this->retainTabPosition = $retain;

        return $this;
    }

    /**
     * @param  (\Closure():(object))|object  $fields
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    protected function prepareFields($fields): array
    {
        $this->convertFieldsToTabs($fields)
            ->filter(static function (Tab $tab): bool {
                return $tab->shouldShow();
            })
            ->each(function (Tab $tab): void {
                $this->addFields($tab);
            });


        return $this->data ?? [];
    }

    /**
     * @param  (\Closure():(object))|object  $fields
     * @return Collection
     */
    private function convertFieldsToTabs($fields): Collection
    {
        $fieldsCollection = collect(
            is_callable($fields) ? $fields() : $fields
        );

        return $fieldsCollection->map(function ($fields, $key) {
            return $this->convertToTab($fields, $key);
        })->values();
    }

    private function convertToTab(mixed $fields, string|int $key): TabContract
    {
        if ($fields instanceof TabContract) {
            return $fields;
        }

        $this->tabsCount++;

        if ($fields instanceof Panel) {
            return new Tab($fields->name, $fields->data, $this->tabsCount);
        }

        /**
         * If a field is not nested into an array or a Tab object
         * it acts as a tab in itself
         *
         * @link https://github.com/eminiarts/nova-tabs/issues/141
         */
        if (!is_array($fields)) {
            return new Tab($fields->name, [$fields], $this->tabsCount);
        }

        return new Tab($key, $fields, $this->tabsCount);
    }

    public function addFields(TabContract $tab): self
    {
        $this->tabs[] = $tab;

        /** @var Tab $tab */
        foreach ($tab->getFields() as $field) {
            if ($field instanceof Panel) {
                $field->assignedPanel = $this;

                $this->addFields(
                    new Tab($field->name, $field->data)
                );
                continue;
            }

            if ($field instanceof MergeValue) {
                /** @var Field $field */
                if (!isset($field->panel)) {
                    $field->assignedPanel = $this;
                    $field->panel = $this->name;
                }

                $this->addFields(
                    tap(new Tab($tab->getTitle(), $field->data), function (TabContract $newTab) use ($tab): void {
                        if ($tab->getName() !== $tab->getTitle()) {
                            $newTab->name($tab->getName());
                        }
                    })
                );
                continue;
            }

            $field->panel = $this->name;
            $field->assignedPanel = $this;

            $meta = [
                'tab' => $tab->getName(),
                'tabSlug' => $tab->getSlug(),
                'tabPosition' => $tab->getPosition(),
                'tabInfo' => Arr::except($tab->toArray(), ['fields', 'slug'])
            ];

            if ($field instanceof ListableField) {
                $meta += [
                    'listable' => false,
                    'listableTab' => true,
                ];
            }

            $field->withMeta($meta);

            $this->data[] = $field;
        }

        return $this;
    }

    public function defaultSearch(bool $value = true): self
    {
        $this->defaultSearch = $value;

        return $this;
    }

    public function showTitle(bool $show = true): self
    {
        $this->showTitle = $show;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $result = array_merge(parent::jsonSerialize(), [
            'defaultSearch' => $this->defaultSearch,
            'showTitle' => $this->showTitle,
            'slug' => $this->slug,
            'retainTabPosition' => $this->retainTabPosition,
            'currentColor' => $this->currentColor,
            'errorColor' => $this->errorColor
        ]);

        return $result;
    }
}
