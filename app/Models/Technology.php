<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    protected $fillable = ['name', 'slug'];

    // P11: satu technology dipakai banyak project (pivot project_technology).
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
