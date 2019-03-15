<?php namespace Humweb\Menus\Presenters;

/**
 * StaffSidebar
 *
 * @package Humweb\Menus\Presenters
 */
class Bootstrap4 implements PresenterInterface
{
    public function itemIcon($menu)
    {
        return isset($menu['icon']) ? $menu['icon'].' ' : '';
    }


    public function itemWithChildren($attr, $children = [])
    {
        $selected = $attr['selected'] ? ' in' : ' collapse';
        $class    = ($attr['level'] > 1) ? ' dropdown-submenu' : '';
        $caret    = ($attr['level'] <= 1) ? ' <span class="caret"></span>' : '';

        $str = '<li class="nav-item dropdown'.$class.'">';
        $str .= '<a href="'.$attr['url'].'" class="dropdown-toggle nav-link" data-toggle="dropdown">'.$attr['icon'].$attr['label'].$caret.'</a>';
        $str .= '<div class="dropdown-menu">';
        $str .= $children;
        $str .= '</div></li>';

        return $str;
    }


    public function item($attr = [], $level = 0)
    {
        $selected = $attr['selected'] ? ' active' : '';

        if ($level > 1) {
            return '<a href="'.$attr['url'].'" class="dropdown-item'.$selected.'">'.$attr['icon'].' '.$attr['label'].'</a>';
        } else {
            return '<li class="nav-item'.$selected.'">'.'<a href="'.$attr['url'].'" class="nav-link">'.$attr['icon'].' '.$attr['label'].'</a></li>';
        }
    }


    public function divider()
    {
        return '<div class="dropdown-divider"></div>';
    }

}
