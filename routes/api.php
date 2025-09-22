<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    // Route::apiResource('permissions', 'PermissionsApiController');

    // Route::apiResource('roles', 'RolesApiController');

    // Route::apiResource('users', 'UsersApiController');

    // Route::apiResource('products', 'ProductsApiController');
});
