<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class MenuHeader extends Model
{
    protected $fillable = ['name', 'is_parent', 'position', 'target', 'url'];

    public function menuSubHeader()
    {
        return $this->hasMany(MenuSubHeader::class);
    }
}
