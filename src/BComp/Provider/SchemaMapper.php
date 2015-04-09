<?php namespace BComp\Provider;

use Bono\Provider\Provider;
use Schema;

class SchemaMapper extends Provider
{
    public function initialize()
    {
        foreach ($this->options as $key => $value)
        {
            Schema::register($key, $value, true);
        }
    }
}
