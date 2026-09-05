<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'category', 'topic_id', 'difficulty', 'difficulty_weight',
        'question', 'question_np',
        'options', 'options_np', 'correct_index',
        'explanation', 'explanation_np', 'image_url', 'is_active',
        'learning_chapter_id', 'tags',
    ];

    protected $casts = [
        'options'          => 'array',
        'options_np'       => 'array',
        'correct_index'    => 'integer',
        'is_active'        => 'boolean',
        'difficulty_weight'=> 'decimal:2',
    ];

    // Never expose correct answer in API (returned only after answering)
    protected $hidden = ['correct_index', 'explanation', 'explanation_np'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
