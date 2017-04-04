<?php

namespace Humweb\Menus\Controllers;

use Humweb\Core\Http\Controllers\AdminController;
use Humweb\Menus\Models\MenuItem;
use Humweb\Pages\Repositories\DbPageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuItemsController extends AdminController
{

    protected $data;


    public function __construct(DbPageRepositoryInterface $page)
    {
        parent::__construct();

        if ( ! empty($page)) {
            $this->page = $page;
        }
    }


    public function getDeleteItem(Request $request, $menuId, $id)
    {
        if ($item = MenuItem::find($id)) {

            // Shift children up one
            $this->menulink->where('parent_id', '=', $id)->update(['parent_id' => $item->parent_id]);
            $item->delete();
            \Cache::forget('menu_links_'.$menuId);

            return redirect()->route('get.admin.menuitem.index', array($menuId))->with('success', 'Menu removed menu item.');
        }
    }


    public function getItems($id)
    {

        $this->setTitle('Menu Items');

        $this->data['menu_id'] = $id;
        $this->data['items']   = MenuItem::orderBy('order', 'desc')->get();
        $this->data['content'] = (new MenuItem())->build_admin_tree($id);
        $this->data['tree']    = MenuItem::tree($id);

        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Menu Items');

        return $this->setContent('menus::admin.index', $this->data);
    }


    public function getEditItem(Request $request, $table_id, $id)
    {
        $this->data['link']        = MenuItem::findOrFail($id);
        $this->data['user_groups'] = \DB::table('groups')->pluck('name');
        $this->data['pages']       = $this->page->build_select(true);

        // Group permissions
        if ( ! empty($this->data['link']->permissions->groups)) {
            $vals                       = array_values($this->data['link']->permissions->groups);
            $this->data['link']->groups = array_combine($vals, $vals);

            foreach ($this->data['link']->permissions->groups as $key => $value) {
                $menu[$value] = $value;
            }

            $this->data['link']->groups = $menu;
        }

        // Users permissions
        if ( ! empty($this->data['link']->permissions->users)) {
            $vals                      = array_values($this->data['link']->permissions->users);
            $this->data['link']->users = array_combine($vals, $vals);
        }

        $this->setTitle('Edit Menu Items');
        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Edit');

        return $this->setContent('menus::admin.edit', $this->data);
    }


    public function getNewItem($id, $parent_id = 0)
    {
        $this->data['menu_id']     = $id;
        $this->data['parent_id']   = $parent_id ?: 0;
        $this->data['pages']       = $this->page->build_select(true);
        $this->data['user_groups'] = \DB::table('groups')->pluck('name', 'name');
        $this->setTitle('Create Menu Item');
        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Create');

        return $this->setContent('menus::admin.create', $this->data);
    }


    public function postEditItem(Request $request, $menu_id, $id = 0)
    {
        $link = MenuItem::findOrFail($id);

        $link->menu_id   = $request->get('menu_id');
        $link->parent_id = $request->get('parent_id', 0);
        $link->title     = $request->get('title');
        $link->url       = $request->get('url');
        $permissions     = [];

        if ($request->has('groups')) {
            $permissions['groups'] = $request->get('groups');
        }
        if ($request->has('users')) {
            $permissions['users'] = $request->get('users');
        }

        if ( ! empty($permissions)) {
            $link->permissions = $permissions;
        }

        if ($link->save()) {
            \Cache::forget('menu_links_'.$link->menu_id);

            return redirect()->route('get.admin.menuitem.index', array($link->menu_id))->with('success', 'Menu has been item has been saved.');
        }

        return back()->withInput()->withErrors($link->getErrors());
    }


    public function postNewItem(Request $request, $menu_id, $id = 0)
    {
        $this->menulink->menu_id   = $menu_id;
        $this->menulink->parent_id = $request->get('parent_id', 0);
        $this->menulink->title     = $request->get('title');
        $this->menulink->url       = $request->get('url');
        $permissions               = [];

        if ($request->has('groups')) {
            $permissions['groups'] = $request->get('groups');
        }
        if ($request->has('users')) {
            $permissions['users'] = $request->get('users');
        }

        if ( ! empty($permissions)) {
            $this->menulink->permissions = json_encode($permissions);
        }

        if ($this->menulink->save()) {
            Cache::forget('menu_links_'.$menu_id);

            return redirect()->route('get.admin.menuitem.index', array($menu_id))->with('success', 'Menu has been created');
        }

        return redirect()->back()->withInput()->withErrors($this->menulink->getErrors());
    }


    public function postSort(Request $request)
    {
        $order   = json_decode($request->get('pages'), true);
        $menu_id = $request->get('menu_id');

        foreach ($order as $key => $value) {
            $this->menulink->reorder($menu_id, $order);
        }
        \Cache::forget('menu_links_'.$menu_id);

        return response()->json(['status' => 'ok']);
    }

}
