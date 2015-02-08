<?php namespace Pixel\Facades;

use Illuminate\Support\Facades\Facade;

class ColorScheme extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Pixel\Contracts\ColorScheme\ColorSchemeContract'; }

}