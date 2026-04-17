@extends('layouts.app')

@section('content')
    <section class="panel">
        <div class="profile-header">
            <div>
                <h1>{{ $profileUser->name }}</h1>
                <p class="muted">{{ $profileUser->email }}</p>
                <p>{{ $profileUser->bio ?: 'Пользователь пока не добавил описание.' }}</p>
            </div>

            <div class="profile-stats">
                <div class="stats-tile">
                    <strong>{{ $questions->count() }}</strong>
                    <span>вопросов</span>
                </div>
                <div class="stats-tile">
                    <strong>{{ $answers->count() }}</strong>
                    <span>ответов</span>
                </div>
                <div class="stats-tile">
                    <strong>{{ $profileUser->totalScore() }}</strong>
                    <span>рейтинг</span>
                </div>
            </div>
        </div>
    </section>

    <section class="two-columns">
        <div class="panel">
            <h2>Вопросы пользователя</h2>

            @forelse ($questions as $question)
                <article class="mini-card">
                    <a href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                    <div class="tag-list">
                        @foreach ($question->tags as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <p class="muted">Рейтинг: {{ $question->score() }} | {{ $question->created_at->format('d.m.Y') }}</p>
                </article>
            @empty
                <p class="empty-text">Пользователь пока не задавал вопросов.</p>
            @endforelse
        </div>

        <div class="panel">
            <h2>Ответы пользователя</h2>

            @forelse ($answers as $answer)
                <article class="mini-card">
                    <a href="{{ route('questions.show', $answer->question) }}">{{ $answer->question->title }}</a>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($answer->body), 120) }}</p>
                    <p class="muted">Рейтинг: {{ $answer->score() }} | {{ $answer->created_at->format('d.m.Y') }}</p>
                </article>
            @empty
                <p class="empty-text">Пользователь пока не оставлял ответов.</p>
            @endforelse
        </div>
    </section>
@endsection
