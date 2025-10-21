<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    // عرض جميع قوائم المطعم الحالي
    public function index()
    {
        $restaurant = Auth::user()->restaurant;
        $menus = $restaurant->menus()->with('categories.items')->latest()->get();
        return MenuResource::collection($menus);
    }

    // إضافة قائمة جديدة
    public function store(StoreMenuRequest $request)
    {
        $restaurant = Auth::user()->restaurant;
        $menu = $restaurant->menus()->create($request->validated());
        return new MenuResource($menu);
    }

    // عرض قائمة محددة (مع التأكد من ملكيتها)
    public function show(Menu $menu)
    {
        $this->authorize('view', $menu); // استخدام Policies للأمان هو الأفضل
        return new MenuResource($menu->load('categories.items'));
    }

    // تحديث قائمة
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $this->authorize('update', $menu);
        $menu->update($request->validated());
        return new MenuResource($menu);
    }

    // حذف قائمة
    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);
        $menu->delete();
        return response()->noContent(); // 204
    }
}
