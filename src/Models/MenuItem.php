<?php

namespace Humweb\Menus\Models;

use Humweb\Menus\Menu;
use Humweb\Menus\Presenters\Bootstrap4;
use Illuminate\Database\Eloquent\Model;

/**
 * Link types.
 *
 * - URI
 * - URL [ protocol: http, https, custom | input: url ]
 * - Page
 * - Content
 */
class MenuItem extends Model
{

    /**
     * Disable updated_at and created_at on table.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Define the table name.
     *
     * @var string
     */
    protected $table    = 'menu_items';
    protected $fillable = ['menu_id', 'title', 'url', 'content', 'permissions'];


    /**
     * Relationships.
     ******************************************************/
    public static function build_navigation($id, $tree = null, $depth = 0)
    {
        $output = '';
        $tree   = static::tree($id);
        $menu   = new Menu($tree, new Bootstrap4());

        return $menu->render();
    }


    public static function tree($id)
    {
        return \Cache::get('menu_links_'.$id, function () use ($id) {
            $menulinks  = [];
            $menu_array = [];
            $all_pages  = static::where('menu_id', $id)->orderBy('order')->get()->toArray();

            //dd($all_pages);

            // First, re-index the array.
            foreach ($all_pages as $row) {
                $menulinks[$row['id']] = $row;
            }

            unset($all_pages);

            // Build a multidimensional array of parent > children.
            foreach ($menulinks as $row) {
                if (array_key_exists($row['parent_id'], $menulinks)) {
                    // Add this page to the children array of the parent page.
                    $menulinks[$row['parent_id']]['children'][$row['id']] = $menulinks[$row['id']];
                }

                // This is a root page.
                if ($row['parent_id'] == 0) {
                    $menu_array[$row['id']] = $menulinks[$row['id']];
                }
            }
            \Cache::put('menu_links_'.$id, $menu_array, 60);

            return $menu_array;
        });
    }


    /**
     * Parents of link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }


    /**
     * Children of link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }


    /**
     * Get menu related to link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }


    /**
     * Accessors and Mutators.
     *****************************************************/
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = ! empty($value) ? json_encode($value) : null;
    }


    public function getPermissionsAttribute($value)
    {
        return ! empty($value) ? json_decode($value) : [];
    }


    public function build_admin_tree($id, $tree = null, $depth = 0)
    {
        $output = '';
        if ( ! $tree) {
            $tree = $this->tree($id);
        }

        if (is_array($tree)) {
            foreach ($tree as $leaf) {
                $output .= '<li class="dd-item" data-id="'.$leaf['id'].'">'.'<div class="dd-handle">Handle</div>'.'<div class="dd-content">'.$leaf['title'];
                $output .= '<div class="actions">'.'<div class="btn-group btn-group-xs">'.'<a href="'.route('get.admin.menuitem.create',
                        [$id, $leaf['id']]).'"><i class="fa fa-plus"></i></a>'.'<a href="'.route('get.admin.menuitem.edit',
                        [$id, $leaf['id']]).'"><i class="fa fa-pencil"></i></a>'.'<a href="'.route('get.admin.menuitem.delete',
                        [$id, $leaf['id']]).'"><i class="fa fa-remove"></i></a>'.'</div>'.'</div></div>';

                if (isset($leaf['children']) && ! empty($leaf['children'])) {
                    $output .= '<ol class="dd-list dd3-list">'.$this->build_admin_tree($id, $leaf['children'], $depth + 1).'</ol>';
                }
                $output .= '</li>';
            }
        }

        return $output;
    }


    public function reorder($menu_id, $pages)
    {
        if (is_array($pages)) {
            \Cache::forget('menu_links_'.$menu_id);
            //reset all parent > child relations
            $this->where('menu_id', $menu_id)->update(array('parent_id' => 0));

            foreach ($pages as $order => $leaf) {
                $root_ids[] = $leaf['id'];

                //set the order of the root pages
                $this->where('id', $leaf['id'])->update(array('order' => $order + 1));
                $this->reorderChilds($leaf);
            }
        }
    }


    /**
     * Set the parent > child relations and child order.
     *
     * @param array $page
     */
    public function reorderChilds($page)
    {
        if (isset($page['children'])) {
            foreach ($page['children'] as $i => $child) {
                $this->where('id', $child['id'])->update(array('parent_id' => $page['id'], 'order' => $i + 1));

                //repeat as long as there are children
                if (isset($child['children'])) {
                    $this->reorderChilds($child);
                }
            }
        }
    }
}
