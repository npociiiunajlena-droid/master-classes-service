<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $ivan = User::query()->where('email', 'visitor.ivan@example.com')->firstOrFail();
        $anna = User::query()->where('email', 'visitor.anna@example.com')->firstOrFail();

        $transport = MasterClass::query()->where('title', 'Моделирование моделей транспорта')->firstOrFail();
        $steaks = MasterClass::query()->where('title', 'Приготовление стейков')->firstOrFail();
        $wood = MasterClass::query()->where('title', 'Геометрическая резьба по дереву')->firstOrFail();

        $enrollments = [
            ['master_class_id' => $transport->id, 'user_id' => $ivan->id],
            ['master_class_id' => $steaks->id, 'user_id' => $anna->id],
            ['master_class_id' => $wood->id, 'user_id' => $ivan->id],
        ];

        foreach ($enrollments as $enrollmentData) {
            Enrollment::query()->updateOrCreate($enrollmentData, []);
        }
    }
}
