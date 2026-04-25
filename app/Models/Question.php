<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->latest();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->orderBy('name');
    }

    public function votes()
    {
        return $this->hasMany(QuestionVote::class);
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
