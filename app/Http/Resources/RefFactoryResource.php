<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class RefFactoryResource extends JsonResource
{

    public function toArray($request)
    {

        $category = Category::find($this->category);
        return [
            'id' => $this->id,
            'factory' => $this->factory,
            'brand' => $this->brand,
            'model' => $this->model,
            'category' => $category->title_ru,
            'class' => $this->class,
        ];
    }
}
