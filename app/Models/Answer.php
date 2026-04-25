<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'body',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(AnswerVote::class);
    }

    public function score(): int
    {
        return (int) $this->votes->sum('value');
    }

    public function userVote(?int $userId): ?int
    {
        if (! $userId) {
            return null;
        }

        return $this->votes
            ->where('user_id', $userId)
            ->first()
            ?->value;
    }
}
