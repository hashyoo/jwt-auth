<?php

namespace HashyooJWTAuth\Facade;

use Illuminate\Support\Facades\Facade;

class JWTAuth extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'JWTAuth';
    }
}