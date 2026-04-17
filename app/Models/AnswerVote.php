<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerVote extends Model
{
    use HasFactory;

    /**
     * Поля голосования по ответу.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'answer_id',
        'user_id',
        'value',
    ];

    /**
     * Голос относится к ответу.
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Голос относится к пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
