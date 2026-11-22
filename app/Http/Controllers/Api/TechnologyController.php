<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TechnologyResource;
use App\Models\Technology;

class TechnologyController extends Controller
{
    /**
     * GET /api/technologies
     */
    public function index()
    {
        $technologies = Technology::withCount('projects')->orderBy('name')->get();

        return TechnologyResource::collection($technologies);
    }
}
