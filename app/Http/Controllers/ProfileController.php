<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Показываем профиль и активность пользователя.
     */
    public function show(User $user)
    {
        $user->load([
            'questions' => function ($query) {
                $query->with(['tags', 'votes'])->latest();
            },
            'answers' => function ($query) {
                $query->with(['question', 'votes'])->latest();
            },
        ]);

        return view('profile.show', [
            'profileUser' => $user,
            'questions' => $user->questions,
            'answers' => $user->answers,
        ]);
    }
}
