<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Latihan 2: withCount('projects') menambah atribut projects_count.
     */
    public function index()
    {
        $categories = Category::withCount('projects')->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }
}
