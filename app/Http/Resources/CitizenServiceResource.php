<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitizenServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $locale === 'ne' && !empty($this->title_np) ? $this->title_np : $this->title,
            'description' => $locale === 'ne' && !empty($this->description_np) ? $this->description_np : $this->description,
            'category' => $this->category,
            'icon' => $this->icon,
            'color' => $this->color,
            'eligibility' => $this->eligibility,
            'required_documents' => $this->required_documents,
            'application_steps' => $this->application_steps,
            'fee' => $this->fee,
            'processing_time' => $this->processing_time,
            'faqs' => $this->faqs,
            'official_url' => $this->official_url,
            'video_url' => $this->video_url,
            // Keep the original np fields if frontend specifically asks for them
            'title_np' => $this->title_np,
            'description_np' => $this->description_np,
        ];
    }
}
