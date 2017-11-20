<?php
namespace Webelightdev\LaravelMenu;

use Illuminate\Database\QueryException;
/*use Illuminate\Support\Facades\Request;*/
use Webelightdev\LaravelMenu\MenuHeader;
use Webelightdev\LaravelMenu\MenuItem;
use Webelightdev\LaravelMenu\MenuSubHeader;
use Webelightdev\LaravelMenu\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

/**
 * Menu
 */
class MenuBuilder
{
    protected $menuHeader;

    public function __construct(MenuHeader $menuHeader, Menu $menu, MenuItem $menuItem, MenuSubHeader $menuSubHeader)
    {
        $this->menuHeader = $menuHeader;
        $this->menu = $menu;
        $this->menuItem = $menuItem;
        $this->menuSubHeader = $menuSubHeader;
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

    public function getBy($attribute, $value)
    {
        $menu = $this->menuHeader->where($attribute, $value)->with('menuSubHeader.menuItems')->get();
        return response()->json($menu);
    }
}
