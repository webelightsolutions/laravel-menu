<?php

namespace Webelightdev\LaravelMenu;

/*use Illuminate\Support\Facades\Request;*/
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Menu.
 */
class MenuBuilder
{
    protected $menuHeader;

    public function __construct(MenuHeader $menuHeader, Menu $menu, MenuItem $menuItem, MenuSubHeader $menuSubHeader, MenuEntities $menuEntities)
    {
        $this->menuHeader = $menuHeader;
        $this->menu = $menu;
        $this->menuItem = $menuItem;
        $this->menuSubHeader = $menuSubHeader;
        $this->menuEntities = $menuEntities;
    }

    public function store(Request $request)
    {
        /*$menu = $this->menuHeader->where('menu_id', 1)->with('menuSubHeaders.menuItems')->get();
        return response()->json($menu);*/

        $result = [];
        $menuHeaders = $request->all();
        DB::beginTransaction();
        foreach ($menuHeaders as $menuHeader) {
            try {
                $menuHeaderCreated = $this->menuHeader->create($menuHeader);
            } catch (Exception $e) {
                DB::rollback();
                $result['error'] = 'Error to Saving Menu Details';

                return $result;
            }
            foreach ($menuHeader['menuSubHeader'] as $menuSubHeader) {
                try {
                    $menuSubHeaderCreated = $menuHeaderCreated->menuSubHeaders()->create($menuSubHeader);
                } catch (Exception $e) {
                    DB::rollback();
                    $result['error'] = 'Error to Saving menu details';

                    return $result;
                }
                foreach ($menuSubHeader['menuItems'] as $menuItem) {
                    try {
                        $menuSubHeaderCreated->menuItems()->create($menuItem);
                    } catch (Exception $e) {
                        DB::rollback();
                        $result['error'] = 'Error to Saving menu items details';

                        return $result;
                    }
                }
            }
        }
        DB::commit();

        return response()->json(['message' => 'Menu created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $result = [];
        $menuHeaders = $request->all();

        //Update Or Create menuHeader Details
        foreach ($menuHeaders as $menuHeader) {
            if (array_has($menuHeader, 'id')) {
                $existMenuHeader = $this->menuHeader->where('id', $menuHeader['id'])->first();

                $this->toDeleteVariousMenuDetails($menuHeaders, $this->menuHeader, 'id');
                $menuHeaderCreated = $this->updateMenuDetails($menuHeader, $this->menuHeader, $existMenuHeader);
            } else {
                $menuHeaderCreated = $this->createNewMenuDetails($this->menuHeader, $menuHeader);
            }

            //Update Or Create menuSubHeader Details
            foreach ($menuHeader['menu_sub_headers'] as $menuSubHeader) {
                if (array_has($menuSubHeader, 'id')) {
                    $existMenuSubHeader = $this->menuSubHeader->where('menu_header_id', $menuHeader['id'])->where('id', $menuSubHeader['id'])->first();

                    $this->toDeleteVariousMenuDetails($menuHeader['menu_sub_headers'], $this->menuSubHeader->where('menu_header_id', $menuHeader['id']), 'id');

                    $menuSubHeaderCreated = $this->updateMenuDetails($menuSubHeader, $this->menuSubHeader, $existMenuSubHeader);
                } else {
                    $menuSubHeaderCreated = $this->createNewMenuDetails($menuHeaderCreated->menuSubHeaders(), $menuSubHeader);
                }

                //Update Or Create menuItems Details

                foreach ($menuSubHeader['menu_items'] as $menuItem) {
                    if (array_has($menuItem, 'id')) {
                        $existMenuSubItem = $this->menuItem->where('menu_sub_header_id', $menuSubHeader['id'])->where('id', $menuItem['id'])->first();

                        $this->toDeleteVariousMenuDetails($menuSubHeader['menu_items'], $this->menuItem->where('menu_sub_header_id', $menuSubHeader['id']), 'id');

                        $menuHeaderCreated = $this->updateMenuDetails($menuItem, $this->menuItem, $existMenuSubItem);
                    } else {
                        $this->createNewMenuDetails($menuSubHeaderCreated->menuItems(), $menuItem);
                    }
                }
            }
        }
    }

    public function updateMenuDetails($data, $model, $existMenuHeader)
    {
        $newUpdatedMenu = [];

        if (!$existMenuHeader) {
            $result['error'] = 'Menu header details does not exists.';

            return $result;
        }

        $newUpdatedMenu = $existMenuHeader->fill($data);
        try {
            $newUpdatedMenu->save();
        } catch (Exception $e) {
            $result['error'] = 'Error to updating menu details';

            return $result;
        }

        return $newUpdatedMenu;
    }

    public function createNewMenuDetails($model, $data)
    {
        try {
            $newGenratedMenu = $model->create($data);
        } catch (Exception $e) {
            $result['error'] = 'Error to creating new menu';

            return $result;
        }

        return $newGenratedMenu;
    }

    public function toDeleteVariousMenuDetails($collection, $model, $column)
    {
        $inputIds = array_pluck($collection, 'id');
        $existIds = $model->pluck('id')->all();
        $toBeDeleteIds = array_diff($existIds, $inputIds);
        $model->whereIn('id', $toBeDeleteIds)->delete();

        $toBeDeleteItems = $this->menuSubHeader->whereIn('menu_header_id', $toBeDeleteIds)->pluck('id')->all();
        $this->menuSubHeader->whereIn('menu_header_id', $toBeDeleteIds)->delete();
        $this->menuItem->whereIn('menu_sub_header_id', $toBeDeleteItems)->delete();
    }

    // public function get($entityType, $entityId)
    // {
    //     if (isset($entityType)) {
    //         if ($entityType == 'menu' && isset($entityId)) {
    //             $menus = $this->menuHeader->where('entity_type', $entityType)->where('entity_id', $entityId)->first();
    //         } else {
    //             $menus = $this->menuHeader->join('menu_entities')->where('entity_type', $entityType)->where('entity_id', $entityId)->get();
    //         }
    //     } else {
    //         $menu = $this->menuHeader->where('entity_type', $entityType)->get();
    //     }
    //     return view('laravel-slider::show', compact('slider'));
    // }

    // public function sliderEntities(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $this->menuEntities->create($request->all());
    //     } catch (Exception $e) {
    //         return redirect('/menu')->with('error', $e->getMessage())->withInput();
    //     }
    //     DB::commit();
    //     return redirect('/menu')->with('success', 'Menu stored successfully.');
    // }

    public function getBy($attribute, $value)
    {
        $menu = $this->menu->where($attribute, $value)->with('menuHeaders.menuSubHeaders.menuItems')->get();

        return response()->json($menu);
    }

    public function gerByUrl()
    {
        $currentPath = Route::getFacadeRoot()->current()->uri();

        $getUrlFromMenuHeader = $this->menuHeader->where('url', '/'.$currentPath);
        $getUrlFromMenuSubHeader = $this->menuSubHeader->where('url', '/'.$currentPath);

        if ($getUrlFromMenuHeader) {
            $menuHeaderId = $getUrlFromMenuHeader->pluck('id')->first();
            $menuSubHeader = $this->menuSubHeader->where('menu_header_id', $menuHeaderId)->orderBy('position')->get();

            return $menuSubHeader;
        } elseif ($getUrlFromMenuSubHeader) {
            $menuSubHeaderId = $getUrlFromMenuSubHeader->pluck('id')->all();
            $menuSubHeaderItem = $this->menuItem->whereIn('menu_sub_header_id', $menuSubHeaderId)->get();

            return $menuSubHeaderItem;
        }
    }
}
