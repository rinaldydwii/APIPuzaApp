<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\SubCategory as SubCategoryModel;

class SubCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'sub_categories'    => SubCategoryModel::where('category_id', $this->id)->orderBy('name', 'asc')->get()
        ];
    }
}