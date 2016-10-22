<?php

namespace Humweb\Menus\Controllers;

use App\Http\Controllers\AdminController;
use Humweb\Pages\Repositories\DbPageRepositoryInterface;
use Humweb\Menus\Models\MenuModel;
use Humweb\Menus\Models\MenuLinkModel;
use Humweb\Pages\Models\Page;
use Illuminate\Http\Request;

class AdminMenuController extends AdminController
{
    protected $menu;
    protected $menulink;

    protected $data;

    public function __construct(DbPageRepositoryInterface $page)
    {
        parent::__construct();

        if (!empty($page)) {
            $this->page = $page;
        }

        $this->menu = new MenuModel();
        $this->menulink = new MenuLinkModel();
//        $this->setTitle('Menus');
    }

    public function getCreate()
    {
        $this->setTitle('Create Menu');
        $this->crumb('Menus', route('get.admin.menu.index'));
        return $this->setContent('menus::admin.menu.create', $this->data);
    }

    public function postCreate(Request $request)
    {
        $this->menu->title = $request->get('title');
        $this->menu->slug = $request->get('slug');

        //$Menu = $this->menu->create($data);

        if ($this->menu->save()) {
            return redirect()->route('get.admin.menu.index')->with('success', 'Menu has been created');
        }

        return redirect()->back()->withInput()->withErrors($this->menu->getErrors());
    }

    public function getIndex()
    {
        $this->setTitle('Menus');
        $this->crumb('Menus');

        $this->data['menus'] = $this->menu->orderBy('title')->get();

        return $this->setContent('menus::admin.menu.index', $this->data);
    }

    public function getDeleteItem(Request $request, $menu_id, $id)
    {
        if ($item = $this->menulink->find($id)) {

            // Shift childrens parent_id's up one
            $this->menulink->where('parent_id', '=', $id)->update(array('parent_id' => $item->parent_id));
            $item->delete();

            return redirect()->route('get.admin.menuitem.index', array($menu_id))->with('success', 'Menu removed menu item.');
        }
    }

    public function getItems($id)
    {


        empty($id) and dd('Must have a menu ID');
        $this->setTitle('Menu Items');
        $this->data['menu_id'] = $id;
        $this->data['items'] = $this->menulink->orderBy('order', 'desc')->get();
        $this->data['content'] = $this->menulink->build_admin_tree($id);
        $this->data['tree'] = $this->menulink->tree($id);

        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Menu Items');
        return $this->setContent('menus::admin.index', $this->data);
    }

    public function getEditItem(Request $request, $table_id, $id)
    {
        $this->data['link'] = $this->menulink->findOrFail($id);
        $this->data['user_groups'] = \DB::table('groups')->pluck('name');
        $this->data['pages'] = $this->page->build_select();

        // dd($this->data['link']->permissions);

        // Group permissions
        if (!empty($this->data['link']->permissions->groups)) {
            $vals = array_values($this->data['link']->permissions->groups);
            $this->data['link']->groups = array_combine($vals, $vals);

            foreach ($this->data['link']->permissions->groups as $key => $value) {
                $menu[$value] = $value;
            }

            $this->data['link']->groups = $menu;
        }

        // Users permissions
         if (!empty($this->data['link']->permissions->users)) {
             $vals = array_values($this->data['link']->permissions->users);
             $this->data['link']->users = array_combine($vals, $vals);
         }

        $this->setTitle('Edit Menu Items');
        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Edit');
        return $this->setContent('menus::admin.edit', $this->data);
    }

    public function getNewItem($id, $parent_id = 0)
    {
        $this->data['menu_id'] = $id;
        $this->data['parent_id'] = $parent_id ?: 0;
        $this->data['pages'] = $this->page->build_select();
        $this->data['user_groups'] = \DB::table('groups')->pluck('name', 'name');
        $this->setTitle('Create Menu Item');
        $this->crumb('Menus', route('get.admin.menu.index'))->crumb('Create');

        return $this->setContent('menus::admin.create', $this->data);
    }

    public function postEditItem(Request $request, $menu_id, $id = 0)
    {
        $link = $this->menulink->findOrFail($id);

        $link->menu_id = $request->get('menu_id');
        $link->parent_id = $request->get('parent_id', 0);
        $link->title = $request->get('title');
        $link->url = $request->get('url');
        $permissions = [];

        if ($request->has('groups')) {
            $permissions['groups'] = $request->get('groups');
        }
        if ($request->has('users')) {
            $permissions['users'] = $request->get('users');
        }

        if (!empty($permissions)) {
            $link->permissions = $permissions;
            // dd($permissions);
        }

        // dd(json_encode($permissions));

        if ($link->save()) {
            \Cache::forget('menu_links_'.$link->menu_id);

            return redirect()->route('get.admin.menuitem.index', array($link->menu_id))->with('success', 'Menu has been item has been saved.');
        }

        return back()->withInput()->withErrors($link->getErrors());
    }

    public function postNewItem(Request $request, $menu_id, $id = 0)
    {
        $this->menulink->menu_id = $menu_id;
        $this->menulink->parent_id = $request->get('parent_id', 0);
        $this->menulink->title = $request->get('title');
        $this->menulink->url = $request->get('url');
        $permissions = [];

        if ($request->has('groups')) {
            $permissions['groups'] = $request->get('groups');
        }
        if ($request->has('users')) {
            $permissions['users'] = $request->get('users');
        }
        //dd(json_encode($permissions));

        if (!empty($permissions)) {
            $this->menulink->permissions = json_encode($permissions);
        }

        if ($this->menulink->save()) {
            \Cache::forget('menu_links_'.$menu_id);

            return redirect()->route('get.admin.menuitem.index', array($menu_id))->with('success', 'Menu has been created');
        }

        return redirect()->back()->withInput()->withErrors($this->menulink->getErrors());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function getEdit($id)
    {
        $this->data['menu'] = $this->menu->findOrFail($id);
        $this->setTitle('Edit Menu: '.$this->data['menu']->title);
        $this->crumb('Menus', route('get.admin.menu.index'))->crumb($this->data['menu']->title);
        return $this->setContent('menus::admin.menu.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function postEdit($id)
    {
        $Menu = $this->menu->findOrFail($id);
        $Menu::$rules['title'] = 'required|min:3|unique:menus,title,'.$Menu->id;
        $Menu::$rules['slug'] = 'required_with:title|min:3|alpha_dash|unique:menus,slug,'.$Menu->id;

        $Menu->title = $request->get('title');
        $Menu->slug = $request->get('slug');

        if ($Menu->save()) {
            // Save associated tags and menus
            return redirect()->route('get.admin.menu.index')->with('success', 'The item has been updated.');
        }

        return redirect()->back()->withInput()->withErrors($Menu->getErrors());
    }

    public function postSort(Request $request)
    {
        $order = json_decode($request->get('pages'), true);
        $menu_id = $request->get('menu_id');

        foreach ($order as $key => $value) {
            $this->menulink->reorder($menu_id, $order);
        }
        \Cache::forget('menu_links_'.$menu_id);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $post = $this->menu->find($id);

        if ($post->exists()) {
            $post->menus()->detach();
            $post->tags()->detach();
            $post->delete();

            return redirect()->route('get.admin.menu.index')->with('success', 'The item has been deleted.');
        }

        return redirect()->route('get.admin.menu.index')->withErrors('The item you tried to delete does not exist.');
    }
}
