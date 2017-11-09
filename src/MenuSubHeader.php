<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class MenuSubHeader extends Model
{
    protected $fillable = ['menu_header_id', 'name', 'is_parent', 'position', 'target', 'url'];

    public function menuHeader()
    {
        return $this->belongsTo(MenuHeader::class, 'menu_header_id');
    }
}
