<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'user_id', 'image', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? 'Published' : 'Unpublished';
    }
    
    public function getStatusColorAttribute()
    {
        return $this->status == 1 ? '#28a745' : '#dc3545';
    }
}