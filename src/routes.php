<?php
$router->group(['middleware' => 'auth', 'prefix' => 'admin/menus'], function ($router) {
    //GET
    $router->get('/', [
        'as'          => 'get.admin.menu.index',
        'uses'        => 'MenuController@getIndex',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.list'],
    ]);

    $router->get('create', [
        'as'          => 'get.admin.menu.create',
        'uses'        => 'MenuController@getCreate',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.create'],
    ]);

    $router->get('edit/{id}', [
        'as'          => 'get.admin.menu.edit',
        'uses'        => 'MenuController@getEdit',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    //POST
    $router->post('create', [
        'as'          => 'post.admin.menu.create',
        'uses'        => 'MenuController@postCreate',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.create'],
    ]);

    $router->post('edit/{id}', [
        'as'          => 'post.admin.menu.edit',
        'uses'        => 'MenuController@postEdit',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    $router->post('sort', [
        'as'          => 'post.admin.menu.sort',
        'uses'        => 'MenuItemsController@postSort',
        'middleware'  => 'allow.only',
        'permissions' => ['menu.edit'],
    ]);

    $router->group(['prefix' => '{id}', 'where' => ['id', '[0-9]+']], function ($router) {

        $router->post('create/{parentid?}', [
            'as'   => 'post.admin.menuitem.create',
            'uses' => 'MenuItemsController@postNewitem',
        ]);

        $router->get('create/{parentid?}', [
            'as'   => 'get.admin.menuitem.create',
            'uses' => 'MenuItemsController@getNewitem',
        ]);

        $router->post('edit/{iid}', [
            'as'   => 'post.admin.menuitem.edit',
            'uses' => 'MenuItemsController@postEditItem',
        ]);

        $router->get('edit/{iid}', [
            'as'   => 'get.admin.menuitem.edit',
            'uses' => 'MenuItemsController@getEditItem',
        ]);

        $router->get('delete/{iid}', [
            'as'   => 'get.admin.menuitem.delete',
            'uses' => 'MenuItemsController@getDeleteItem',
        ]);

        $router->post('delete/{iid}', [
            'as'   => 'post.admin.menuitem.delete',
            'uses' => 'MenuItemsController@postDeleteitem',
        ]);

        $router->get('/', [
            'as'   => 'get.admin.menuitem.index',
            'uses' => 'MenuItemsController@getItems',
        ]);
    });
});
