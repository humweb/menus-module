<?php

namespace Humweb\Menus\Controllers;

use Humweb\Core\Http\Controllers\AdminController;
use Humweb\Menus\Models\Menu;
use Humweb\Menus\Models\MenuItem;
use Humweb\Menus\Requests\MenuSaveRequest;
use Humweb\Pages\Repositories\DbPageRepositoryInterface;
use Illuminate\Http\Request;

class MenuController extends AdminController
{
    protected $menu;
    protected $menulink;

    protected $data;


    public function __construct(DbPageRepositoryInterface $page)
    {
        parent::__construct();

        if ( ! empty($page)) {
            $this->page = $page;
        }

        $this->menu     = new Menu();
        $this->menulink = new MenuItem();
    }


    public function getIndex(Request $request)
    {
        $this->setTitle('Menus');
        $this->crumb('Menus');

        $this->data['menus'] = Menu::orderBy('title')->get();

        return $this->setContent('menus::admin.menu.index', $this->data);
    }


    public function getCreate()
    {
        $this->setTitle('Create Menu');
        $this->crumb('Menus', route('get.admin.menu.index'));

        return $this->setContent('menus::admin.menu.create', $this->data);
    }


    public function postCreate(MenuSaveRequest $request)
    {
        $menu = Menu::create($request->only(['title', 'slug']));

        if ( ! is_null($menu)) {
            return redirect()->route('get.admin.menu.index')->with('success', 'Menu has been created');
        }

        return back();
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
    public function postEdit(MenuSaveRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->fill($request->intercect(['title', 'slug']));

        if ($menu->save()) {
            // Save associated tags and menus
            return redirect()->route('get.admin.menu.index')->with('success', 'The item has been updated.');
        }

        return back();
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
        $menu = $this->menu->find($id);

        if ( ! is_null($menu)) {
            $menu->links()->detach();
            $menu->delete();

            return redirect()->route('get.admin.menu.index')->with('success', 'The item has been deleted.');
        }

        return redirect()->route('get.admin.menu.index')->withErrors('The item you tried to delete does not exist.');
    }
}
