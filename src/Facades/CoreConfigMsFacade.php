<?php

namespace Bulbalara\CoreConfigMs\Facades;

use Illuminate\Support\Facades\Facade;

class CoreConfigMsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bl.config.config_ms';
    }
}
