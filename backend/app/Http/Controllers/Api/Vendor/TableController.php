<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // جلب الطاولات الخاصة بالمطعم الحالي فقط
        $tables = Auth::user()->restaurant->tables()->latest()->get();
        return response()->json(['data' => $tables]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            // يمكنك إضافة 'branch_id' إذا كنت تريد ربط الطاولة بفرع معين
            // 'branch_id' => 'nullable|exists:branches,id'
        ]);

        $table = Auth::user()->restaurant->tables()->create($validated);

        return response()->json($table, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        $this->authorize('manage', $table);
        return response()->json($table);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $this->authorize('manage', $table);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $table->update($validated);

        return response()->json($table);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        $this->authorize('manage', $table);
        $table->delete();
        return response()->noContent();
    }
}
