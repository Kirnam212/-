<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Сохраняем ответ пользователя.
     */
    public function store(Request $request, Question $question)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:10',
        ]);

        $question->answers()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ответ добавлен.');
    }
}
