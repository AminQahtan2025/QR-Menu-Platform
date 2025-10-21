<?php
// app/Http/Controllers/Api/OrderController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // الحصول على جميع الطلبات
    public function index(Request $request)
    {
        $query = Order::with(['user', 'restaurant']);

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المطعم
        if ($request->has('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // ترتيب حسب التاريخ
        $orders = $query->latest()->paginate(10);

        return response()->json($orders);
    }

    // الحصول على طلب محدد
    public function show($id)
    {
        $order = Order::with(['user', 'restaurant', 'items'])->findOrFail($id);
        return response()->json($order);
    }

    // تحديث حالة الطلب
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'order' => $order
        ]);
    }

    // حذف طلب
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'message' => 'تم حذف الطلب بنجاح'
        ]);
    }
}
