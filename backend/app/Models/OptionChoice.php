<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionChoice extends Model
{
    use HasFactory;
    protected $fillable = ['product_option_id', 'name', 'price_modifier'];

    public function productOption() { return $this->belongsTo(ProductOption::class); }
}
