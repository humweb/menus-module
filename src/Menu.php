<?php namespace Humweb\Menus;

use Humweb\Menus\Presenters\PresenterInterface;
use Humweb\Menus\Presenters\Bootstrap;

/**
 * Class Menu
 *
 * @package Humweb\Menus
 */
class Menu
{
    protected $items = [];
    protected $presenter;
    protected $request;

    protected $labelAttribute = 'title';

    /**
     * Menu constructor
     *
     * @param array              $items
     * @param PresenterInterface $presenter
     *
     */
    public function __construct($items = [], PresenterInterface $presenter = null)
    {
        $this->request = app()->make('request');
        $this->items = $items;
        $this->presenter = $presenter ?: new Bootstrap();
    }

    /**
     * Render Menu
     *
     * @return string
     */
    public function render()
    {
        return $this->recurseMenu($this->items, 0);
    }

    /**
     * Build and renders menu
     *
     * @param     $menu
     * @param int $level
     *
     * @return string
     */
    public function recurseMenu($menu, $level = 0)
    {
//        dd($menu);
        $str = '';

        $level++;

        foreach ($menu as $menu_section => $menus) {
            $menus['label'] = $this->getLabel($menus, $menu_section);
            $menus['selected'] = $this->getSelected($menus, $menu_section);
            $menus['icon'] = $this->getPresenter()->itemIcon($menus);
            $menus['url'] = $this->getUrl($menus);
            $menus['level'] = $level;

            $hasDivider = isset($menus['divider']);

            if ($hasDivider && $menus['divider'] == 'above') {
                $str .= $this->getPresenter()->divider();
            }

            // Has children
            if (isset($menus['children']) && is_array($menus['children'])) {
                $children = $this->recurseMenu($menus['children'], $level);
                $str .= $this->getPresenter()->itemWithChildren($menus, $children, $level);
            } else {
                $str .= $this->getPresenter()->item($menus, $level);
            }

            if ($hasDivider && $menus['divider'] == 'below') {
                $str .= $this->getPresenter()->divider();
            }
        }

        return $str;

    }


    /**
     * Get label uses section if no label prop is found
     *
     * @param array $item
     * @param       $section
     *
     * @return string
     */
    public function getLabel($item = [], $section)
    {
        return isset($item[$this->labelAttribute]) ? $item[$this->labelAttribute] : ucwords(str_replace('_', ' ', $section));
    }


    /**
     * Returns bool if item should be selected
     *
     * @param $item
     * @param $section
     *
     * @return bool
     */
    public function getSelected($item, $section)
    {
        $url = $this->request->url();

        if (isset($item['route'])) {
            return route($item['route']) == $url;
        }

        return (isset($item['url']) && $item['url'] == $url);
    }


    /**
     * Get url for link defaults to hash
     *
     * @param $item
     *
     * @return string
     */
    public function getUrl($item)
    {
        if (isset($item['route'])) {
            return route($item['route']);
        }

        return isset($item['url']) ? $item['url'] : '#';
    }

    /**
     * Check if menu exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name = '')
    {
        return isset($this->items[$name]);
    }


    /**
     * Check if menu is empty
     *
     * @param string $name
     *
     * @return bool
     */
    public function isEmpty($name = '')
    {
        return empty($this->items[$name]);
    }

    /**
     * Sets the presenter to be used for menu
     *
     * @param PresenterInterface $presenter
     *
     * @return $this
     *
     */
    public function setPresenter(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }


    public function getPresenter()
    {
        if (empty($this->presenter)) {
            throw new \Exception('Presenter not set.');
        }

        return $this->presenter;
    }

    /**
     * @param string $labelAttribute
     *
     * @return Menu
     */
    public function setLabelAttribute($labelAttribute)
    {
        $this->labelAttribute = $labelAttribute;

        return $this;
    }
}
