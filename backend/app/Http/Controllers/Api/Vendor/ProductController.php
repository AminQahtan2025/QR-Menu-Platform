<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, Category $category)
    {
        // 1. التحقق من الصلاحية: هل يملك المستخدم هذا الصنف؟
        $this->authorize('manage', $category);

        // 2. التحقق من صحة المدخلات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // مثال للتحقق من الصور
            'is_available' => 'required|boolean',
            'options' => 'nullable|array',
            'options.*.name' => 'required_with:options|string|max:255',
            'options.*.type' => ['required_with:options', Rule::in(['radio', 'checkbox', 'text'])], // أنواع الخيارات المسموحة
            'options.*.min_choices' => 'nullable|integer|min:0',
            'options.*.max_choices' => 'nullable|integer|min:1',
            'options.*.choices' => 'required_with:options|array|min:1',
            'options.*.choices.*.name' => 'required_with:options.*.choices|string|max:255',
            'options.*.choices.*.price_modifier' => 'nullable|numeric',
        ]);

        // 3. استخدام Transaction لضمان سلامة البيانات
        $product = DB::transaction(function () use ($category, $validated, $request) {
            $productData = $validated;

            // التعامل مع رفع الصور (مثال)
            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $product = $category->products()->create($productData);

            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $optionData) {
                    $option = $product->options()->create($optionData);
                    // حفظ الاختيارات (choices) لهذا الخيار
                    $option->choices()->createMany($optionData['choices']);
                }
            }
            return $product;
        });

        // 4. إرجاع الاستجابة
        return response()->json($product->load('options.choices'), 201);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. التحقق من الصلاحية
        $this->authorize('manage', $product);

        // 2. التحقق من صحة المدخلات
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'sometimes|required|boolean',
            'category_id' => [
                'sometimes', 'required', 'integer',
                // تأكد من أن الصنف الجديد يتبع نفس المطعم
                Rule::exists('categories', 'id')->where('restaurant_id', $product->category->restaurant_id)
            ],
        ]);

        // 3. تحديث المنتج
        $product->update($validated);

        // (منطق تحديث الخيارات يمكن أن يكون معقدًا ويترك كتطوير لاحق)
        // غالبًا ما يتضمن حذف الخيارات القديمة وإعادة إنشائها

        // 4. إرجاع الاستجابة
        return response()->json($product->load('options.choices'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // 1. التحقق من الصلاحية
        $this->authorize('manage', $product);

        // 2. حذف المنتج
        $product->delete();

        // 3. إرجاع استجابة فارغة
        return response()->noContent();
    }
}
