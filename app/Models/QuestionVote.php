<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionVote extends Model
{
    use HasFactory;

    /**
     * Поля голосования по вопросу.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'user_id',
        'value',
    ];

    /**
     * Голос относится к вопросу.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Голос относится к пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
