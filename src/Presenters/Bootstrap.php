<?php namespace Humweb\Menus\Presenters;
/**
 * StaffSidebar
 *
 * @package Humweb\Menus\Presenters
 */
class Bootstrap implements PresenterInterface
{
    public function itemIcon($menu)
    {
        return isset($menu['icon']) ? $menu['icon'].' ' : '';
    }

    public function itemWithChildren($attr, $children = [])
    {
        $selected = $attr['selected'] ? ' in' : ' collapse';
        $class = ($attr['level']>1) ? ' dropdown-submenu' : '';
        $caret = ($attr['level']<=1) ? ' <span class="caret"></span>' : '';
        $str = '<li class="dropdown'.$class.'">'.
        '<a href="'.$attr['url'].'" class="dropdown-toggle" data-toggle="dropdown">'.$attr['icon'].$attr['label'].$caret.'</a>'.
        '<ul class="dropdown-menu">';
        $str .= $children;
        $str .= '</ul></li>';

        return $str;
    }

    public function item($attr = [], $level = 0)
    {
        $selected = $attr['selected'] ? ' active' : '';

        return '<li class="'.$selected.'">'.
                '<a href="'.$attr['url'].'">'.$attr['icon'].' '.$attr['label'].'</a></li>';
    }

    public function divider()
    {
        return '<li class="divider"></li>';
    }

}
