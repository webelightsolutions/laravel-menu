<?php
namespace Webelightdev\LaravelMenu\Facades;

use Illuminate\Support\Facades\Facade;

class MenuBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'menu';
    }
}
