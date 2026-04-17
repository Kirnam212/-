<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerVote;
use Illuminate\Http\Request;

class AnswerVoteController extends Controller
{
    /**
     * Ставим лайк или дизлайк ответу.
     */
    public function store(Request $request, Answer $answer)
    {
        $validated = $request->validate([
            'value' => 'required|integer|in:1,-1',
        ]);

        $userId = $request->user()->id;
        $value = (int) $validated['value'];

        $currentVote = $answer->votes()
            ->where('user_id', $userId)
            ->first();

        if ($currentVote && $currentVote->value === $value) {
            $currentVote->delete();

            return back()->with('success', 'Голос по ответу убран.');
        }

        AnswerVote::updateOrCreate(
            [
                'answer_id' => $answer->id,
                'user_id' => $userId,
            ],
            [
                'value' => $value,
            ]
        );

        return back()->with('success', 'Голос по ответу сохранен.');
    }
}
