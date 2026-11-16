<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    // Satu kategori punya banyak project.
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
