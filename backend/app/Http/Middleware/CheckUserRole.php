<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
/**
* Handle an incoming request.
*
* @param \Illuminate\Http\Request $request
* @param \Closure $next
* @param string $role
* @return mixed
*/
public function handle(Request $request, Closure $next, string $role)
{
if (!Auth::check() || !$request->user()) {
return response()->json(['message' => 'Unauthenticated.'], 401);
}

// نفترض أن لديك حقل 'role' في جدول 'users'
// تأكد من أن اسم الحقل صحيح
if ($request->user()->role === $role) {
return $next($request);
}

return response()->json([
'message' => 'Forbidden: You do not have the required role.',
'required_role' => $role,
'user_role' => $request->user()->role
], 403);
}
}
