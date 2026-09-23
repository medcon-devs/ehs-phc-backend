<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'logo' => $this->logo, // Cloudinary URL
            
            'images1' => $this->mediaByGroup('images1'),
            'images2' => $this->mediaByGroup('images2'),
            'images3' => $this->mediaByGroup('images3'),
        ];
    }

    protected function mediaByGroup(string $group): array
{
    if (!$this->media) {
        return [];
    }

    return $this->media
        ->where('group_key', $group)
        ->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->url,
        ])
        ->values()
        ->toArray();
}

}

