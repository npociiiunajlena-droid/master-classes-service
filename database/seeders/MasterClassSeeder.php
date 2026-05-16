<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class MasterClassSeeder extends Seeder
{
    public function run(): void
    {
        $architecture = CreativityType::query()->where('slug', 'architectural-modeling')->firstOrFail();
        $cooking = CreativityType::query()->where('slug', 'cooking')->firstOrFail();
        $wood = CreativityType::query()->where('slug', 'wood-carving')->firstOrFail();

        $olga = User::query()->where('email', 'master.olga@example.com')->firstOrFail();
        $andrey = User::query()->where('email', 'master.andrey@example.com')->firstOrFail();

        $baseDate = CarbonImmutable::today()->addDays(2);

        $masterClasses = [
            [
                'creativity_type_id' => $architecture->id,
                'master_id' => $olga->id,
                'title' => 'Моделирование моделей транспорта',
                'description' => 'Участники создают бумажные и комбинированные модели транспорта, разбирают основы проектирования и сборки конструкций.',
                'class_date' => $baseDate->format('Y-m-d'),
                'class_time' => '09:00:00',
                'max_participants' => 6,
                'price' => 1800.00,
            ],
            [
                'creativity_type_id' => $architecture->id,
                'master_id' => $olga->id,
                'title' => 'Моделирование зданий и сооружений',
                'description' => 'Практика по созданию макетов малоэтажных зданий, проработка деталей фасадов, крыш и элементов благоустройства.',
                'class_date' => $baseDate->addDays(1)->format('Y-m-d'),
                'class_time' => '13:00:00',
                'max_participants' => 8,
                'price' => 2100.00,
            ],
            [
                'creativity_type_id' => $cooking->id,
                'master_id' => $andrey->id,
                'title' => 'Приготовление стейков',
                'description' => 'Разбор видов мяса, степеней прожарки и техники приготовления стейков. На практике готовится гарнир и соус.',
                'class_date' => $baseDate->format('Y-m-d'),
                'class_time' => '11:00:00',
                'max_participants' => 10,
                'price' => 2500.00,
            ],
            [
                'creativity_type_id' => $cooking->id,
                'master_id' => $andrey->id,
                'title' => 'Шоколадные поделки',
                'description' => 'Создание шоколадных фигур и конфет с использованием форм, базовое темперирование и украшение готовых изделий.',
                'class_date' => $baseDate->addDays(2)->format('Y-m-d'),
                'class_time' => '15:00:00',
                'max_participants' => 12,
                'price' => 2300.00,
            ],
            [
                'creativity_type_id' => $wood->id,
                'master_id' => $olga->id,
                'title' => 'Геометрическая резьба по дереву',
                'description' => 'Вводный мастер-класс по базовым элементам геометрической резьбы и созданию орнамента на деревянной заготовке.',
                'class_date' => $baseDate->addDays(3)->format('Y-m-d'),
                'class_time' => '11:00:00',
                'max_participants' => 7,
                'price' => 1950.00,
            ],
            [
                'creativity_type_id' => $wood->id,
                'master_id' => $andrey->id,
                'title' => 'Деревянные игрушки',
                'description' => 'Изготовление фигурок животных из натуральной древесины с безопасной обработкой и финишным покрытием.',
                'class_date' => $baseDate->addDays(4)->format('Y-m-d'),
                'class_time' => '09:00:00',
                'max_participants' => 6,
                'price' => 1700.00,
            ],
        ];

        foreach ($masterClasses as $masterClassData) {
            MasterClass::query()->updateOrCreate(
                [
                    'master_id' => $masterClassData['master_id'],
                    'class_date' => $masterClassData['class_date'],
                    'class_time' => $masterClassData['class_time'],
                ],
                $masterClassData
            );
        }
    }
}
