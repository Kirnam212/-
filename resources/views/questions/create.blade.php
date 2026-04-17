@extends('layouts.app')

@section('content')
    <section class="panel narrow-panel">
        <h1>Новый вопрос</h1>
        <p class="muted">
            Опишите проблему простыми словами: что вы готовите, что уже пробовали и где именно возникла сложность.
        </p>

        <form action="{{ route('questions.store') }}" method="POST" class="form-grid">
            @csrf

            <div>
                <label for="title">Заголовок вопроса</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Например: почему бисквит оседает после выпечки?"
                >
            </div>

            <div>
                <label for="body">Описание</label>
                <textarea
                    id="body"
                    name="body"
                    rows="10"
                    placeholder="Опишите ингредиенты, температуру, время и свои действия"
                >{{ old('body') }}</textarea>
            </div>

            <div>
                <label for="tags">Теги через запятую</label>
                <input
                    id="tags"
                    type="text"
                    name="tags"
                    value="{{ old('tags') }}"
                    placeholder="супы, мясо, десерты"
                >
                @if ($tags->isNotEmpty())
                    <p class="field-hint">
                        Популярные теги: {{ $tags->take(8)->pluck('name')->implode(', ') }}
                    </p>
                @endif
            </div>

            <button type="submit" class="button">Сохранить вопрос</button>
        </form>
    </section>
@endsection
