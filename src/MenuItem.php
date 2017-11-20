<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_sub_header_id', 'name', 'is_parent', 'position', 'target', 'url'];

    public function menuSubHeader()
    {
        $this->belongsTo(MenuSubHeader::class, 'menu_sub_header_id');
    }

    public function menuHeader()
    {
        $this->hasMany(MenuHeader::class);
    }
}
