<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    /**
     * Поля ответа, которые разрешено сохранять.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'user_id',
        'body',
    ];

    /**
     * Ответ относится к вопросу.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Ответ относится к автору.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Голоса ответа.
     */
    public function votes()
    {
        return $this->hasMany(AnswerVote::class);
    }

    /**
     * Считаем рейтинг ответа.
     */
    public function score(): int
    {
        return (int) $this->votes->sum('value');
    }

    /**
     * Показываем текущий голос пользователя.
     */
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
