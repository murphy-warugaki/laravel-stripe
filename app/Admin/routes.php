<?php

use Illuminate\Routing\Router;


Admin::registerAuthRoutes();

// \App\Admin\MyAdminConfig::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {


    //管理画面アクセス系

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/sample', 'ExampleController@sample');

});


