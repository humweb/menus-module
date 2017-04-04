<?php
Route::group(['middleware' => 'auth'], function () {
    //GET
    Route::get('admin/menus/', [
        'as'          => 'get.admin.menu.index',
        'uses'        => 'MenuController@getIndex',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.list'],
    ]);

    Route::get('admin/menus/create', [
        'as'          => 'get.admin.menu.create',
        'uses'        => 'MenuController@getCreate',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.create'],
    ]);

    Route::get('admin/menus/edit/{id}', [
        'as'          => 'get.admin.menu.edit',
        'uses'        => 'MenuController@getEdit',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    //POST
    Route::post('admin/menus/create', [
        'as'          => 'post.admin.menu.create',
        'uses'        => 'MenuController@postCreate',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.create'],
    ]);

    Route::post('admin/menus/edit/{id}', [
        'as'          => 'post.admin.menu.edit',
        'uses'        => 'MenuController@postEdit',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    Route::post('admin/menus/sort', [
        'as'          => 'post.admin.menu.sort',
        'uses'        => 'MenuController@postSort',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    Route::group(array('prefix' => 'admin/menus/{id}', 'where' => array('id', '[0-9]+')), function () {
        Route::post('create/{parentid?}', [
            'as'   => 'post.admin.menuitem.create',
            'uses' => 'MenuItemsController@postNewitem',
        ]);

        Route::get('create/{parentid?}', [
            'as'   => 'get.admin.menuitem.create',
            'uses' => 'MenuItemsController@getNewitem',
        ]);

        Route::post('edit/{iid}', [
            'as'   => 'post.admin.menuitem.edit',
            'uses' => 'MenuController@postEditItem',
        ]);

        Route::get('edit/{iid}', [
            'as'   => 'get.admin.menuitem.edit',
            'uses' => 'MenuItemsController@getEditItem',
        ]);

        Route::get('delete/{iid}', [
            'as'   => 'get.admin.menuitem.delete',
            'uses' => 'MenuItemsController@getDeleteItem',
        ]);

        Route::post('delete/{iid}', [
            'as'   => 'post.admin.menuitem.delete',
            'uses' => 'MenuItemsController@postDeleteitem',
        ]);

        Route::get('/', [
            'as'   => 'get.admin.menuitem.index',
            'uses' => 'MenuItemsController@getItems',
        ]);
    });
});
