<?php

declare(strict_types=1);

namespace ShuvroRoy\NovaTabs\Contracts;

use Closure;

interface TabContract
{
    /**
     * The name of the tab
     *
     * This will be used to remember which tab is selected
     * If the name if not supplied, the sluggified tab title is used
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function name(string $name);

    /**
     * A boolean or function that returns a boolean
     *
     * If the result is true, the tab will be shown
     *
     * showIf takes priority over showUnless
     *
     * @param  bool | Closure  $condition
     *
     * @return $this
     */
    public function showIf(bool|Closure $condition);

    /**
     * A boolean or function that returns a boolean
     *
     * If the result is false, the tab will be shown
     *
     * showIf takes priority over showUnless
     *
     * @param  bool | Closure  $condition
     *
     * @return $this
     */
    public function showUnless(bool|Closure $condition);

    /**
     * Array representation of the tab
     *
     * @return array
     */
    public function toArray(): array;
}
