<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Иван Повар',
            'email' => 'cook@example.com',
            'bio' => 'Люблю домашнюю кухню и простые рецепты.',
        ]);

        // Базовый набор тегов пригодится сразу после установки проекта.
        $tags = [
            'Супы',
            'Мясо',
            'Десерты',
            'Салаты',
            'Выпечка',
            'Соусы',
            'Напитки',
            'Гарниры',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate([
                'slug' => $this->makeSlug($tagName),
            ], [
                'name' => $tagName,
            ]);
        }
    }

    /**
     * Простой slug для русских названий тегов.
     */
    private function makeSlug(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^\pL\pN]+/u', '-', $value) ?? '';

        return trim($value, '-');
    }
}
