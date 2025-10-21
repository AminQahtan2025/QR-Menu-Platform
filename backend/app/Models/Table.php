<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = ['restaurant_id', 'branch_id', 'name', 'capacity'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
}
