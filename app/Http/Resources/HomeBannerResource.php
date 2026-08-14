<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'link' => $this->link,
            'url' => $this->url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
