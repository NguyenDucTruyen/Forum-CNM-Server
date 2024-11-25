<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reactions';

    protected $fillable = [
        'user_id',
        'blog_id',
        'reactionType'
    ];

    // Đảm bảo cast đúng kiểu
    protected $casts = [
        'reactionType' => 'boolean'
    ];

    //set relationship one
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
}
