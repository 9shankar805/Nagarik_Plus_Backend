<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mock_test_id', 'category',
        'total_questions', 'correct_answers', 'score_percentage',
        'passed', 'time_taken_seconds', 'answers', 'completed_at',
    ];

    protected $casts = [
        'answers'          => 'array',
        'passed'           => 'boolean',
        'completed_at'     => 'datetime',
        'score_percentage' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mockTest()
    {
        return $this->belongsTo(MockTest::class);
    }
}
