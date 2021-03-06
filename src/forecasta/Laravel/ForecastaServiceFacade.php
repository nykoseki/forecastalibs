<?php

namespace Forecasta\Laravel;

use Illuminate\Support\Facades\Facade;

class ForecastaServiceFacade extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Forecasta';
    }
}