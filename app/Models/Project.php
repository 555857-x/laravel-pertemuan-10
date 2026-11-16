<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    // Kolom yang boleh diisi lewat create() dan update().
    // Modul P10: name, category_id, thumbnail. Sisanya kolom dari P7-P8.
    protected $fillable = [
        'name',
        'category_id',
        'thumbnail',
        'description',
        'status',
        'started_at',
        'budget',
    ];

    // started_at menjadi objek Carbon agar mudah diformat (pengganti $dates).
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'budget' => 'integer',
        ];
    }

    // Pencarian nama atau deskripsi dilakukan di database.
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    // Project yang dibuat hari ini.
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    // Project dengan status published.
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // Project dengan status draft.
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    // Project milik satu kategori.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Satu project punya banyak gambar gallery, urut sesuai sort_order.
    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }
}
