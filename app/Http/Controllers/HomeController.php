<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $tag = trim((string) $request->input('tag'));

        $questions = Question::query()
            ->with(['user', 'tags', 'votes'])
            ->withCount('answers')
            ->latest();

        if ($search !== '') {
            $questions->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('answers', function ($answerQuery) use ($search) {
                        $answerQuery->where('body', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($tag !== '') {
            $questions->whereHas('tags', function ($query) use ($tag) {
                $query->where('slug', $tag);
            });
        }

        $popularTags = Tag::query()
            ->withCount('questions')
            ->orderByDesc('questions_count')
            ->orderBy('name')
            ->take(12)
            ->get();

        return view('home.index', [
            'questions' => $questions->paginate(10)->withQueryString(),
            'popularTags' => $popularTags,
            'search' => $search,
            'currentTag' => $tag,
        ]);
    }
}
