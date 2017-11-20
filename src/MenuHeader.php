<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class MenuHeader extends Model
{
    protected $fillable = ['name', 'is_parent', 'position', 'target', 'url', 'menu_id'];

    public function menuSubHeaders()
    {
        return $this->hasMany(MenuSubHeader::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
