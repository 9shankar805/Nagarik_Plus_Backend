<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoubtAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'doubt_id', 'user_id', 'body', 'image_url',
        'is_accepted', 'is_instructor', 'upvotes',
    ];

    protected $casts = [
        'is_accepted'   => 'boolean',
        'is_instructor' => 'boolean',
        'upvotes'       => 'integer',
    ];

    public function doubt()
    {
        return $this->belongsTo(Doubt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
