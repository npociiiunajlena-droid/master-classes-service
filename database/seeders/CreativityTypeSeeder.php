<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CreativityType;
use Illuminate\Database\Seeder;

class CreativityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Архитектурное моделирование',
                'slug' => 'architectural-modeling',
                'description' => 'Архитектурное моделирование — изготовление моделей зданий, сооружений и инженерных объектов. На занятиях изучаются основы композиции, дизайна и макетирования, а также применяются практические навыки конструирования.',
                'hero_image_path' => 'media/architectural-model-pictures-cool-architectural-model-.jpg',
            ],
            [
                'name' => 'Кулинария',
                'slug' => 'cooking',
                'description' => 'Кулинария учит готовить вкусно, быстро и правильно. На мастер-классах участники изучают основы выбора продуктов, техники приготовления и подачи блюд, в том числе десертов и стейков.',
                'hero_image_path' => 'media/steak-1024x678.jpg',
            ],
            [
                'name' => 'Резьба по дереву',
                'slug' => 'wood-carving',
                'description' => 'Резьба по дереву — традиционное декоративно-прикладное искусство. Участники осваивают базовые техники, учатся создавать геометрические узоры и деревянные фигурки из натуральных материалов.',
                'hero_image_path' => 'media/rd-1.jpg',
            ],
        ];

        CreativityType::query()->upsert(
            $types,
            ['slug'],
            ['name', 'description', 'hero_image_path']
        );
    }
}
