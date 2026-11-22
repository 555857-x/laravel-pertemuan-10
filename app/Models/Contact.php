<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // Tanpa $fillable, Contact::create() gagal karena mass assignment protection.
    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
