<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'category', 'question_count',
        'duration_minutes', 'pass_percentage', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }
}
