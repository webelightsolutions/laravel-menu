<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name'];

    public function menuHeaders()
    {
        return $this->hasMany(MenuHeader::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
