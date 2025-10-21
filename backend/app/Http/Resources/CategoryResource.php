<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            // تحميل المنتجات فقط إذا كانت العلاقة موجودة
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
