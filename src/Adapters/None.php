<?php

namespace vWhois\Adapters;

use vWhois\Exceptions\NoInterfaceException;

class None extends Base
{
    public function request($query)
    {
        throw new NoInterfaceException();
    }
}
