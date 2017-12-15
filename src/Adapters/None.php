<?php

namespace vWhois\Adapters;

use vWhois\Exceptions\NoInterfaceException;

class None extends Base
{
    protected function request($query)
    {
        throw new NoInterfaceException();
    }
}
