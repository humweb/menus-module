<?php

namespace Humweb\Menus;

use Humweb\Modules\ModuleBaseProvider;

class MenusServiceProvider extends ModuleBaseProvider
{
    protected $permissions = [

        // Users
        'menu.create' => [
            'name' => 'Create Menus',
            'description' => 'Create Menu.',
        ],
        'menu.edit' => [
            'name' => 'Edit Menus',
            'description' => 'Edit Menu.',
        ],
        'menu.list' => [
            'name' => 'List Menus',
            'description' => 'List Menu.',
        ],
        'menu.delete' => [
            'name' => 'Delete Menus',
            'description' => 'Delete pages.',
        ],
    ];

    protected $moduleMeta = [
        'name' => 'Menus',
        'slug' => 'menus',
        'version' => '',
        'author' => '',
        'email' => '',
        'website' => '',
    ];

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->app['modules']->put('menus', $this);
        $this->loadLang();
        $this->loadViews();
        $this->publishViews();
    }

    public function register()
    {
    }

    public function getAdminMenu()
    {
        return [
            'Structure' => [
                [
                    'label' => 'Menus',
                    'icon' => '<i class="fa fa-navicon" ></i>',
                    'url' => route('get.admin.menu.index'),
                    'children' => [
                        [
                            'label' => 'List',
                            'url' => route('get.admin.menu.index'),
                        ],
                        [
                            'label' => 'New Menu',
                            'url' => route('get.admin.menu.create'),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
