<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // إضافة عنصر جديد
    public function store(StoreItemRequest $request, Category $category)
    {
        // يجب التأكد من أن المستخدم يملك هذا الصنف عبر القائمة
        $item = $category->items()->create($request->validated());
        return response()->json($item, 201);
    }

    // تحديث عنصر
    public function update(UpdateItemRequest $request, Category $category, Item $item)
    {
        $item->update($request->validated());
        return response()->json($item);
    }

    // حذف عنصر
    public function destroy(Category $category, Item $item)
    {
        $item->delete();
        return response()->noContent();
    }
}
