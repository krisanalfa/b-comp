<?php namespace BComp\Facade;

use RuntimeException;
use KrisanAlfa\Kraken\Facade;

class Schema extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'kraken';
    }
}
