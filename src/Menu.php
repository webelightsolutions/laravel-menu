<?php
namespace Webelightdev\LaravelMenu;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Request;
use Webelightdev\LaravelMenu\MenuHeader;

/**
 * Menu
 */
class Menu
{
    protected $menuHeader;

    public function __construct(MenuHeader $menuHeader)
    {
        $this->menuHeader = $menuHeader;
    }

    public function store(Request $request)
    {
        $menuHeader = $this->menuHeader->create($request->all());
        
        if ($request->has('menuSubHeader') && !empty($request->menuSubHeader)) {
            $menuSubHeader = $menuHeader->menuSubHeader()->create($request->menuSubHeader);
        }
        if ($request->has('menuItems') && !empty($request->menuItems)) {
            $menuSubHeader->menuItems()->createMany($request->menuItems);
        }
        return response()->json(['message' => 'Menu created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $menuHeader = $this->menuHeader->findOrFail($id);
        $menuHeader->fill($request->all());
        $menuHeader->save();

        if ($request->has('menuSubHeader') && empty($request->menuSubHeader)) {
            $menuSubHeader = $menuHeader->menuSubHeader()->create($request->menuSubHeader);
        }
        if ($request->has('menuItems') && !empty($request->menuItems)) {
            $menuSubHeader->menuItems()->createMany($request->menuItems);
        }
    }

    public function getBy($attribute, $value)
    {
        $menu = $this->menuHeader->where($attribute, $value)->with('menuSubHeader')->get();
        return response()->json($menu);
    }
}
