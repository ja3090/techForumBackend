<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'category_id'
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function post() {
        return $this->hasMany(Post::class);
    }
}
