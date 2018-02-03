<?php namespace Humweb\Menus;

use Humweb\Menus\Presenters\Bootstrap4;
use Humweb\Menus\Presenters\PresenterInterface;
use Illuminate\Support\Collection;

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
    protected $childrenKey    = 'children';


    /**
     * Menu constructor
     *
     * @param array              $items
     * @param PresenterInterface $presenter
     *
     */
    public function __construct($items = [], PresenterInterface $presenter = null)
    {
        $this->request   = app()->make('request');
        $this->items     = $items;
        $this->presenter = $presenter ?: new Bootstrap4();
    }


    /**
     * Render Menu
     *
     * @return string
     */
    public function render($level = 0)
    {
        return $this->recurseMenu($this->items, $level);
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

        $str = '';

        $level++;

        foreach ($menu as $menu_section => $menus) {
            $menus['label']    = $this->getLabel($menus, $menu_section);
            $menus['selected'] = $this->getSelected($menus, $menu_section);
            $menus['icon']     = $this->getPresenter()->itemIcon($menus);
            $menus['url']      = $this->getUrl($menus);
            $menus['level']    = $level;

            $hasDivider = isset($menus['divider']);

            if ($hasDivider && $menus['divider'] == 'above') {
                $str .= $this->getPresenter()->divider();
            }

            // Has children
            if (isset($menus[$this->getChildrenKey()]) && $this->isCollection($menus[$this->getChildrenKey()])) {
                $menus[$this->getChildrenKey()] = $menus[$this->getChildrenKey()]->toArray();
            }

            if (isset($menus[$this->getChildrenKey()]) && ! empty($menus[$this->getChildrenKey()]) && $level < 2) {
                $children = $this->recurseMenu($menus[$this->getChildrenKey()], $level);
                $str      .= $this->getPresenter()->itemWithChildren($menus, $children, $level);
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
        return isset($item[$this->labelAttribute]) ? $item[$this->labelAttribute]
            : ucwords(str_replace('_', ' ', $section));
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


    public function getPresenter()
    {
        if (empty($this->presenter)) {
            throw new \Exception('Presenter not set.');
        }

        return $this->presenter;
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

        if (isset($item['uri'])) {
            return url($item['uri']);
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
     * @param string $labelAttribute
     *
     * @return Menu
     */
    public function setLabelAttribute($labelAttribute)
    {
        $this->labelAttribute = $labelAttribute;

        return $this;
    }


    /**
     * @return string
     */
    public function getChildrenKey(): string
    {
        return $this->childrenKey;
    }


    /**
     * @param string $childrenKey
     *
     * @return Menu
     */
    public function setChildrenKey(string $childrenKey): Menu
    {
        $this->childrenKey = $childrenKey;

        return $this;
    }


    protected function isCollection($menu = null)
    {
        return $menu instanceof Collection;
    }
}
