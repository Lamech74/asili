<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'file_url',
        'mime_type',
        'category',
        'alt_text',
        'featured',
        'status',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];
}
