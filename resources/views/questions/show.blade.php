@extends('layouts.app')

@section('content')
    <section class="panel">
        <div class="question-page">
            <div class="vote-box">
                <div class="vote-score">{{ $question->score() }}</div>

                @auth
                    <form action="{{ route('questions.vote', $question) }}" method="POST">
                        @csrf
                        <input type="hidden" name="value" value="1">
                        <button type="submit" class="vote-button {{ $question->userVote(auth()->id()) === 1 ? 'vote-button-active' : '' }}">Лайк</button>
                    </form>

                    <form action="{{ route('questions.vote', $question) }}" method="POST">
                        @csrf
                        <input type="hidden" name="value" value="-1">
                        <button type="submit" class="vote-button {{ $question->userVote(auth()->id()) === -1 ? 'vote-button-active vote-button-negative' : '' }}">Дизлайк</button>
                    </form>
                @endauth
            </div>

            <div class="question-content">
                <h1>{{ $question->title }}</h1>

                <div class="question-text">{{ $question->body }}</div>

                <div class="tag-list">
                    @foreach ($question->tags as $tag)
                        <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="tag">{{ $tag->name }}</a>
                    @endforeach
                </div>

                <div class="meta-line">
                    <span>Автор: <a href="{{ route('profile.show', $question->user) }}">{{ $question->user->name }}</a></span>
                    <span>Дата: {{ $question->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2>Ответы ({{ $question->answers->count() }})</h2>
        </div>

        @forelse ($question->answers->sortByDesc(fn ($answer) => $answer->score()) as $answer)
            <article class="answer-card">
                <div class="vote-box">
                    <div class="vote-score">{{ $answer->score() }}</div>

                    @auth
                        <form action="{{ route('answers.vote', $answer) }}" method="POST">
                            @csrf
                            <input type="hidden" name="value" value="1">
                            <button type="submit" class="vote-button {{ $answer->userVote(auth()->id()) === 1 ? 'vote-button-active' : '' }}">Лайк</button>
                        </form>

                        <form action="{{ route('answers.vote', $answer) }}" method="POST">
                            @csrf
                            <input type="hidden" name="value" value="-1">
                            <button type="submit" class="vote-button {{ $answer->userVote(auth()->id()) === -1 ? 'vote-button-active vote-button-negative' : '' }}">Дизлайк</button>
                        </form>
                    @endauth
                </div>

                <div class="answer-content">
                    <div class="question-text">{{ $answer->body }}</div>

                    <div class="meta-line">
                        <span>Ответил: <a href="{{ route('profile.show', $answer->user) }}">{{ $answer->user->name }}</a></span>
                        <span>{{ $answer->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-text">На этот вопрос пока нет ответов. Вы можете стать первым.</p>
        @endforelse
    </section>

    <section class="panel narrow-panel">
        <h2>Добавить ответ</h2>

        @auth
            <form action="{{ route('answers.store', $question) }}" method="POST" class="form-grid">
                @csrf

                <div>
                    <label for="body">Ваш ответ</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="7"
                        placeholder="Опишите решение пошагово и простыми словами"
                    >{{ old('body') }}</textarea>
                </div>

                <button type="submit" class="button">Отправить ответ</button>
            </form>
        @else
            <p class="empty-text">
                Чтобы отвечать на вопросы, <a href="{{ route('login') }}">войдите</a> или
                <a href="{{ route('register') }}">зарегистрируйтесь</a>.
            </p>
        @endauth
    </section>
@endsection
