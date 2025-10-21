<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * عرض جميع أصناف المطعم الحالي
     */
    public function index()
    {
        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'User is not associated with a restaurant.'
            ], 403);
        }

        $categories = $restaurant->categories()->with('products')->latest()->get();

        return CategoryResource::collection($categories);
    }

    /**
     * إضافة صنف جديد
     */
    public function store(Request $request)
    {
        // التحقق من أن المستخدم يمكنه إنشاء صنف
        $this->authorize('create', Category::class);

        $restaurant = Auth::user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'User is not associated with a restaurant.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            // 'image' => 'nullable|image|max:2048' // يمكن تفعيلها لاحقًا
        ]);

        $category = $restaurant->categories()->create($validated);

        return new CategoryResource($category);
    }

    /**
     * عرض صنف محدد
     */
    public function show(Category $category)
    {
        $this->authorize('manage', $category);

        return new CategoryResource($category->load('products.options.choices'));
    }

    /**
     * تحديث صنف
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('manage', $category);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    /**
     * حذف صنف
     */
    public function destroy(Category $category)
    {
        $this->authorize('manage', $category);

        $category->delete();

        return response()->noContent(); // 204 No Content
    }
}
