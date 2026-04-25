<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionVote;
use Illuminate\Http\Request;

class QuestionVoteController extends Controller
{
    public function store(Request $request, Question $question)
    {
        $validated = $request->validate([
            'value' => 'required|integer|in:1,-1',
        ]);

        $userId = $request->user()->id;
        $value = (int) $validated['value'];

        $currentVote = $question->votes()
            ->where('user_id', $userId)
            ->first();

        if ($currentVote && $currentVote->value === $value) {
            $currentVote->delete();

            return back()->with('success', 'Голос по вопросу убран.');
        }

        QuestionVote::updateOrCreate(
            [
                'question_id' => $question->id,
                'user_id' => $userId,
            ],
            [
                'value' => $value,
            ]
        );

        return back()->with('success', 'Голос по вопросу сохранен.');
    }
}
