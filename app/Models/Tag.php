<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * Разрешенные поля тега.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Один тег может быть связан со многими вопросами.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
