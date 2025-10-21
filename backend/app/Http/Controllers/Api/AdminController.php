<?php
// app/Http/Controllers/Api/AdminController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // إحصائيات لوحة تحكم الأدمن
    public function dashboardStats()
    {
        $totalUsers = User::count();
        $totalRestaurants = Restaurant::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total');

        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalRestaurants' => $totalRestaurants,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
        ]);
    }

    // الحصول على جميع المستخدمين
    public function users()
    {
        $users = User::with('restaurant')->latest()->paginate(10);
        return response()->json($users);
    }

    // الحصول على جميع المطاعم
    public function restaurants()
    {
        $restaurants = Restaurant::with('user')->latest()->paginate(10);
        return response()->json($restaurants);
    }
}
