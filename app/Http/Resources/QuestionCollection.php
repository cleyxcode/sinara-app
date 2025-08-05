<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'questions' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'categories_count' => $this->collection->pluck('category')->unique()->count(),
            ]
        ];
    }
}