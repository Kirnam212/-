<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * Разрешаем сохранять только эти поля.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    /**
     * Вопрос принадлежит автору.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * У вопроса может быть много ответов.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class)->latest();
    }

    /**
     * У вопроса может быть несколько тегов.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->orderBy('name');
    }

    /**
     * Для лайков и дизлайков используем отдельную таблицу голосов.
     */
    public function votes()
    {
        return $this->hasMany(QuestionVote::class);
    }

    /**
     * Считаем общий рейтинг вопроса.
     */
    public function score(): int
    {
        return (int) $this->votes->sum('value');
    }

    /**
     * Узнаем, как проголосовал текущий пользователь.
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
