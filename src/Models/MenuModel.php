<?php

namespace Humweb\Menus\Models;

class MenuModel extends \Eloquent
{
    /**
     * Define the table name.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = array();

    public static $rules = [];

    /**
     * Disable updated_at and created_at on table.
     *
     * @var bool
     */
    public $timestamps = false;

    public function links()
    {
        return $this->hasMany('Humweb\Menus\Models\MenuLinkModel', 'menu_id');
    }

    /**
     * Get flat array of groups.
     *
     * @return array
     */
    public static function getList()
    {
        return static::lists('title', 'id');
    }

    /**
     * Get group by..
     *
     * @param string $what  What to get
     * @param string $value The value
     *
     * @return object
     */
    public static function findBySlug($value)
    {
        return static::where('slug', $value)->first();
    }
}
