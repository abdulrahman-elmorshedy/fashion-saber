<?php
namespace InnoShop\Panel\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Article name resource class
 * Specifically handles name retrieval for article models
 */
class ArticleNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->getName(),
            'image'       => image_resize($this->image, 200, 150),
            'image_small' => image_resize($this->image, 100, 100),
            'image_big'   => image_resize($this->image, 400, 300),
        ];
    }

    /**
     * Get article name
     * Uses fallbackName('title') method
     *
     * @return string
     */
    private function getName(): string
    {
        return $this->resource->fallbackName('title');
    }
}
