<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'order' => $this->order,
            'question_text' => $this->question_text,
            'options' => $this->formatOptions($this->options),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format options untuk response yang lebih user-friendly
     */
    private function formatOptions($options)
    {
        if (!is_array($options)) {
            return [];
        }

        return array_map(function ($option, $index) {
            return [
                'option_id' => $index + 1,
                'text' => $option['text'] ?? '',
                'score' => (int) ($option['score'] ?? 0),
                'is_risk' => (int) ($option['score'] ?? 0) > 0
            ];
        }, $options, array_keys($options));
    }
}