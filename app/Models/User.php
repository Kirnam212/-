<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Поля, которые можно заполнять через формы.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
    ];

    /**
     * Поля, которые не нужно показывать снаружи.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Приведение типов для некоторых полей.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Один пользователь может задать много вопросов.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Один пользователь может оставить много ответов.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Этот метод нужен только для понятного отображения статистики на профиле.
     */
    public function totalScore(): int
    {
        $questionScore = $this->questions->sum(function (Question $question) {
            return $question->score();
        });

        $answerScore = $this->answers->sum(function (Answer $answer) {
            return $answer->score();
        });

        return $questionScore + $answerScore;
    }
}
