<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Показываем форму создания вопроса.
     */
    public function create()
    {
        return view('questions.create', [
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Сохраняем новый вопрос.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
            'tags' => 'nullable|string|max:255',
        ]);

        $question = $request->user()->questions()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $this->syncTags($question, $validated['tags'] ?? '');

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Вопрос успешно добавлен.');
    }

    /**
     * Показываем страницу вопроса и ответы к нему.
     */
    public function show(Question $question)
    {
        $question->load([
            'user',
            'tags',
            'votes',
            'answers.user',
            'answers.votes',
        ]);

        return view('questions.show', [
            'question' => $question,
        ]);
    }

    /**
     * Разбираем строку тегов и привязываем их к вопросу.
     */
    private function syncTags(Question $question, string $tagLine): void
    {
        $tagIds = [];
        $tagNames = explode(',', $tagLine);

        foreach ($tagNames as $tagName) {
            $tagName = trim($tagName);

            if ($tagName === '') {
                continue;
            }

            if (count($tagIds) >= 5) {
                break;
            }

            $slug = $this->makeSlug($tagName);

            // Если slug не получился, просто пропускаем такой тег.
            if ($slug === '') {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $tagName]
            );

            $tagIds[] = $tag->id;
        }

        $question->tags()->sync($tagIds);
    }

    /**
     * Делаем простой slug, который подходит и для русских тегов.
     */
    private function makeSlug(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^\pL\pN]+/u', '-', $value) ?? '';

        return trim($value, '-');
    }
}
