<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'replies';

    protected $fillable = [
        'user_id',
        'comment_id',
        'content'
    ];

    //set relationship one
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comment()
    {
        return $this->belongsTo(Blog::class, 'comment_id', 'id');
    }


}
