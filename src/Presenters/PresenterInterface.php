<?php namespace Humweb\Menus\Presenters;
/**
 * StaffSidebar
 *
 * @package Humweb\Menus\Presenters
 */
interface PresenterInterface
{

    /**
     * @param $menu
     *
     * @return string
     */
    public function itemIcon($menu);

    /**
     * @param       $attr
     * @param array $children
     *
     * @return string
     */
    public function itemWithChildren($attr, $children = []);


    /**
     * @param array $attr
     * @param int   $level
     *
     * @return string
     */
    public function item($attr = [], $level = 0);


    /**
     * @return string
     */
    public function divider();

}
