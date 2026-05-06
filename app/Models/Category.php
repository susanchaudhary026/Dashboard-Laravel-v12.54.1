<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title'];

    public function articles()
    {
        return $this->hasMany(Article::class);
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