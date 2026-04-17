@extends('layouts.app')

@section('content')
    <section class="hero">
        <div>
            <h1>Площадка вопросов и ответов для поваров</h1>
            <p>
                Здесь можно спросить про супы, мясо, десерты, ошибки в рецептах и тонкости приготовления.
            </p>
        </div>

        @auth
            <a href="{{ route('questions.create') }}" class="button">Задать вопрос</a>
        @else
            <a href="{{ route('register') }}" class="button">Начать общение</a>
        @endauth
    </section>

    @if ($popularTags->isNotEmpty())
        <section class="panel">
            <div class="panel-header">
                <h2>Популярные теги</h2>

                @if ($currentTag)
                    <a href="{{ route('home', array_filter(['search' => $search])) }}" class="small-link">Сбросить фильтр</a>
                @endif
            </div>

            <div class="tag-list">
                @foreach ($popularTags as $tag)
                    <a
                        href="{{ route('home', array_filter(['search' => $search, 'tag' => $tag->slug])) }}"
                        class="tag {{ $currentTag === $tag->slug ? 'tag-active' : '' }}"
                    >
                        {{ $tag->name }} ({{ $tag->questions_count }})
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="panel">
        <div class="panel-header">
            <h2>
                @if ($search !== '')
                    Результаты поиска по запросу "{{ $search }}"
                @elseif ($currentTag !== '')
                    Вопросы по выбранному тегу
                @else
                    Последние вопросы
                @endif
            </h2>
            <span class="muted">Найдено: {{ $questions->total() }}</span>
        </div>

        @forelse ($questions as $question)
            <article class="question-card">
                <div class="stats-box">
                    <div><strong>{{ $question->score() }}</strong><span>рейтинг</span></div>
                    <div><strong>{{ $question->answers_count }}</strong><span>ответов</span></div>
                </div>

                <div class="question-main">
                    <h3>
                        <a href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                    </h3>

                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($question->body), 180) }}</p>

                    <div class="tag-list">
                        @foreach ($question->tags as $tag)
                            <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="tag">{{ $tag->name }}</a>
                        @endforeach
                    </div>

                    <div class="meta-line">
                        <span>Автор: <a href="{{ route('profile.show', $question->user) }}">{{ $question->user->name }}</a></span>
                        <span>{{ $question->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-text">Пока ничего не найдено. Попробуйте изменить запрос или создайте новый вопрос.</p>
        @endforelse

        @if ($questions->hasPages())
            <div class="pagination-box simple-pagination">
                @if ($questions->onFirstPage())
                    <span class="page-disabled">Назад</span>
                @else
                    <a href="{{ $questions->previousPageUrl() }}" class="button-light">Назад</a>
                @endif

                <span class="muted">
                    Страница {{ $questions->currentPage() }} из {{ $questions->lastPage() }}
                </span>

                @if ($questions->hasMorePages())
                    <a href="{{ $questions->nextPageUrl() }}" class="button-light">Вперед</a>
                @else
                    <span class="page-disabled">Вперед</span>
                @endif
            </div>
        @endif
    </section>
@endsection
