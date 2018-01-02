<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['menu_type', 'institute_id'];

    public function menuHeaders()
    {
        return $this->hasMany(MenuHeader::class)->orderBy('position');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
