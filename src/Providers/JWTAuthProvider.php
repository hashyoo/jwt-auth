<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HashyooJWTAuth\Providers;

use HashyooJWTAuth\JWTAuth;
use Illuminate\Support\ServiceProvider;

class JWTAuthProvider extends ServiceProvider
{

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../../config/hashyoo-jwt.php');
        $this->publishes([$path => config_path('hashyoo-jwt.php')], 'config');
        //        $this->mergeConfigFrom($path, 'hashyoo-jwt');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 在容器中注册
        $this->app->singleton('JWTAuth', function () {
            //            $module = config('hashyoo-jwt.defaults.guard');
            //            $model = ;
            return new JWTAuth();
        });
    }


}
