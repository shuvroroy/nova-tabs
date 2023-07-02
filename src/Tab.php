<?php

declare(strict_types=1);

namespace ShuvroRoy\NovaTabs;

use Closure;
use ShuvroRoy\NovaTabs\Contracts\TabContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonSerializable;
use Laravel\Nova\Fields\Field;
use function is_bool;
use function is_callable;

class Tab implements TabContract, JsonSerializable, Arrayable
{
    /** @var string|Closure */
    protected $title;

    /** @var Field[] */
    protected $fields;

    /** @var string|null */
    protected $name;

    /** @var bool|Closure|null */
    protected $showIf;

    /** @var bool|Closure|null */
    protected $showUnless;

    /** @var int */
    protected $position;

    /** @var array */
    protected $relationshipAttributeVisibility = [];

    public function __construct($title, array $fields, int $position = 0)
    {
        $this->title = $title;
        $this->fields = $fields;
        $this->position = $position;
    }

    public static function make($title, array $fields): self
    {
        return new static($title, $fields);
    }

    public function position(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function showHeadingForRelationships(array $attributes): self
    {
        $this->relationshipAttributeVisibility = array_merge($this->relationshipAttributeVisibility, $attributes);

        return $this;
    }

    public function showIf(bool|Closure $condition): self
    {
        if (is_bool($condition) || is_callable($condition)) {
            $this->showIf = $condition;

            return $this;
        }

        throw new InvalidArgumentException('The $condition parameter must be a boolean or a closure returning one');
    }

    public function showUnless(bool|Closure $condition): self
    {
        if (is_bool($condition) || is_callable($condition)) {
            $this->showUnless = $condition;

            return $this;
        }

        throw new InvalidArgumentException('The $condition parameter must be a boolean or a closure returning one');
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getTitle(): string
    {
        return (string) $this->resolve($this->title);
    }

    private function resolve($value)
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getName(): string
    {
        return $this->name ?? $this->getTitle();
    }

    public function getSlug(): string
    {
        return Str::slug($this->getName());
    }

    public function shouldShow(): bool
    {
        if ($this->showIf !== null) {
            return $this->resolve($this->showIf);
        }

        if ($this->showUnless !== null) {
            return !$this->resolve($this->showUnless);
        }

        return true;
    }

    public function getHeadingVisibilityForRelationshipAttributes(): array
    {
        return $this->relationshipAttributeVisibility;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'position' => $this->getPosition(),
            'title' => $this->getTitle(),
            'fields' => $this->getFields(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'shouldShow' => $this->shouldShow(),
            'headingVisibilityForRelationshipAttributes' => $this->getHeadingVisibilityForRelationshipAttributes(),
        ];
    }
}
