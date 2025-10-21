<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استيراد المتحكمات
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Vendor\CategoryController;
use App\Http\Controllers\Api\Vendor\ProductController;
use App\Http\Controllers\Api\Vendor\TableController;
use App\Http\Controllers\Api\Vendor\OrderController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- مسارات المصادقة العامة ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});


// --- مسارات التاجر المحمية (Vendor Routes) ---
Route::middleware(['auth:sanctum', 'role:vendor'])->prefix('vendor')->group(function () {

    // إدارة الأصناف (Categories) الخاصة بالمطعم
    // GET /api/vendor/categories -> index()
    // POST /api/vendor/categories -> store()
    // GET /api/vendor/categories/{category} -> show()
    // PUT /api/vendor/categories/{category} -> update()
    // DELETE /api/vendor/categories/{category} -> destroy()
    Route::apiResource('categories', CategoryController::class);

    // إضافة منتج جديد (Product) لصنف معين
    // POST /api/vendor/categories/{category}/products
    Route::post('categories/{category}/products', [ProductController::class, 'store']);

    // تحديث وحذف منتج (Product) - باستخدام مسار مباشر لتبسيط الأمور
    // PUT /api/vendor/products/{product}
    Route::put('products/{product}', [ProductController::class, 'update']);
    // DELETE /api/vendor/products/{product}
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    // إدارة الطاولات (Tables)
    // GET /api/vendor/tables -> index()
    // POST /api/vendor/tables -> store()
    // GET /api/vendor/tables/{table} -> show()
    // PUT /api/vendor/tables/{table} -> update()
    // DELETE /api/vendor/tables/{table} -> destroy()
    Route::apiResource('tables', TableController::class);

    // إدارة الطلبات (Orders)
    Route::get('orders', [OrderController::class, 'index']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);

});


// --- مسارات المدير المحمية (Admin Routes) ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats']);
    // ... باقي مسارات المدير
});
