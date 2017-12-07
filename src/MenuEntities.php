<?php

namespace Webelightdev\LaravelMenu;

use Illuminate\Database\Eloquent\Model;

class MenuEntities extends Model
{
    protected $table = 'menu_entities';
    protected $fillable = ['entity_id', 'entity_type', 'menu_id'];

    public function menu()
    {
        return $this->belongsTo(MenuHeader::class, 'menu_id');
    }
}
