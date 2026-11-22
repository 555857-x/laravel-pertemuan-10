<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Resource menentukan kolom yang boleh keluar, jadi kolom internal
     * seperti budget, status, dan deleted_at tidak ikut bocor ke publik.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'        => $this->name,
            'slug'         => $this->slug,
            'summary'      => $this->short_description,
            'thumbnail'    => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'category'     => $this->category?->name,
            'technologies' => $this->technologies->pluck('name'),
            'project_date' => $this->project_date?->format('Y-m-d'),
            'is_featured'  => (bool) $this->is_featured,
        ];
    }
}
