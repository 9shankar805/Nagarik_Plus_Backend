<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudyActivity extends Model
{
    use HasFactory;

    protected $table = 'user_study_activity';

    protected $fillable = [
        'user_id', 'activity_date',
        'questions_answered', 'chapters_read',
        'tests_taken', 'minutes_studied',
    ];

    protected $casts = [
        'activity_date'      => 'date',
        'questions_answered' => 'integer',
        'chapters_read'      => 'integer',
        'tests_taken'        => 'integer',
        'minutes_studied'    => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
