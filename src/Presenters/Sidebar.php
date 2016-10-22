<?php namespace Humweb\Menus\Presenters;
/**
 * StaffSidebar
 *
 * @package Humweb\Menus\Presenters
 */
class Sidebar implements PresenterInterface
{
    protected $numbers = [
        'first',
        'second',
        'third',
        'fourth',
        'fifth',
    ];

    public function itemIcon($menu)
    {
        return isset($menu['icon']) ? $menu['icon'].' ' : '<i class="fa fa-th-large"></i> ';
    }

    public function itemWithChildren($attr, $children = [], $level = 0)
    {
        $selected = $attr['selected'] ? ' in' : ' collapse';
        $label = $level <= 1 ? '<span class="nav-label">'.$attr['label'].'</span>' : $attr['label'];

        $str = '<li>'.
            '<a href="'.$attr['url'].'" >'.$attr['icon'].
            $label.' <span class="fa arrow"></span></a>'.
            '<ul class="nav nav-'.$this->numbers[$attr['level']].'-level'.$selected.'">';
        $str .= $children;
        $str .= '</ul></li>';

        return $str;
    }

    public function item($attr = [], $level = 0)
    {
        $selected =  $attr['selected'] ? ' class="active"' : '';
        $label = $level == 0 ? '<span class="nav-label">'.$attr['label'].'</span>' : $attr['label'];

        return '<li'.$selected.'><a href="'.$attr['url'].'">'.$attr['icon'].' '.$label.'</a></li>';
    }

    public function divider()
    {
        return '<li class="divider"></li>';
    }

}
