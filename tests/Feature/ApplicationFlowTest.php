<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CreativityType;
use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_home_and_category_pages(): void
    {
        $type = $this->createType();
        $this->createMasterClass(type: $type);

        $this->get(route('home'))->assertOk();
        $this->get(route('categories.show', $type))->assertOk();
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $masterClass = $this->createMasterClass();

        $this->get(route('cabinet.index'))->assertRedirect(route('auth.login'));
        $this->get(route('enrollments.confirm', $masterClass))->assertRedirect(route('auth.login'));
    }

    public function test_master_login_redirects_to_cabinet(): void
    {
        $master = $this->createMaster();

        $this->post(route('auth.login.attempt'), [
            'email' => $master->email,
            'password' => 'password123',
        ])->assertRedirect(route('cabinet.index'));
    }

    public function test_visitor_login_redirects_to_home(): void
    {
        $visitor = $this->createVisitor();

        $this->post(route('auth.login.attempt'), [
            'email' => $visitor->email,
            'password' => 'password123',
        ])->assertRedirect(route('home'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $visitor = $this->createVisitor();

        $response = $this->post(route('auth.login.attempt'), [
            'email' => $visitor->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_validation_rejects_invalid_input(): void
    {
        $response = $this->post(route('auth.login.attempt'), [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_registration_creates_visitor_and_logs_in(): void
    {
        $response = $this->post(route('auth.register.store'), [
            'full_name' => 'Иванов Иван Иванович',
            'email' => 'new.user@example.com',
            'phone' => '+79005555555',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'new.user@example.com',
            'role' => 'visitor',
        ]);
    }

    public function test_registration_validation_rejects_invalid_name_and_phone(): void
    {
        $response = $this->post(route('auth.register.store'), [
            'full_name' => '123',
            'email' => 'not-an-email',
            'phone' => 'phone',
            'password' => '123',
        ]);

        $response->assertSessionHasErrors(['full_name', 'email', 'phone', 'password']);
        $this->assertSame(
            'Телефон должен быть в формате +79991234567 или 89991234567.',
            session('errors')->first('phone')
        );
    }

    public function test_master_can_view_cabinet_and_create_master_class(): void
    {
        $type = $this->createType();
        $master = $this->createMaster();

        $this->actingAs($master)
            ->get(route('cabinet.index'))
            ->assertOk();

        $this->actingAs($master)
            ->post(route('master-classes.store'), [
                'creativity_type_id' => $type->id,
                'title' => 'Большой мастер-класс по резьбе',
                'description' => 'Подробный практический мастер-класс по резьбе с пошаговыми объяснениями и упражнениями.',
                'class_date' => CarbonImmutable::today()->addDays(3)->format('Y-m-d'),
                'class_time' => '09:00',
                'max_participants' => 10,
                'price' => 2500,
            ])
            ->assertRedirect(route('cabinet.index'));

        $this->assertDatabaseHas('master_classes', [
            'master_id' => $master->id,
            'title' => 'Большой мастер-класс по резьбе',
            'class_time' => '09:00:00',
        ]);
    }

    public function test_master_cannot_create_master_class_in_busy_slot(): void
    {
        $master = $this->createMaster();
        $type = $this->createType();
        $date = CarbonImmutable::today()->addDays(5)->format('Y-m-d');

        $this->createMasterClass($master, $type, [
            'class_date' => $date,
            'class_time' => '11:00:00',
        ]);

        $response = $this->actingAs($master)->post(route('master-classes.store'), [
            'creativity_type_id' => $type->id,
            'title' => 'Конфликтный слот мастер-класса',
            'description' => 'Описание мастер-класса с достаточной длиной, чтобы пройти базовые проверки формы.',
            'class_date' => $date,
            'class_time' => '11:00',
            'max_participants' => 8,
            'price' => 1200,
        ]);

        $response->assertSessionHasErrors(['class_time']);
        $this->assertDatabaseCount('master_classes', 1);
    }

    public function test_master_can_edit_and_update_own_master_class(): void
    {
        $master = $this->createMaster();
        $masterClass = $this->createMasterClass($master);

        $this->actingAs($master)
            ->get(route('master-classes.edit', $masterClass))
            ->assertOk();

        $this->actingAs($master)
            ->patch(route('master-classes.update', $masterClass), [
                'description' => 'Обновленное подробное описание мастер-класса для проверки успешного редактирования записи.',
                'price' => 3500,
            ])
            ->assertRedirect(route('cabinet.index'));

        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'price' => '3500.00',
        ]);
    }

    public function test_master_cannot_edit_foreign_master_class(): void
    {
        $owner = $this->createMaster();
        $anotherMaster = $this->createMaster();
        $masterClass = $this->createMasterClass($owner);

        $this->actingAs($anotherMaster)
            ->get(route('master-classes.edit', $masterClass))
            ->assertForbidden();
    }

    public function test_visitor_cannot_access_master_only_pages(): void
    {
        $visitor = $this->createVisitor();

        $this->actingAs($visitor)
            ->get(route('cabinet.index'))
            ->assertForbidden();

        $this->actingAs($visitor)
            ->get(route('master-classes.create'))
            ->assertForbidden();
    }

    public function test_visitor_can_confirm_and_store_enrollment(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        $this->actingAs($visitor)
            ->get(route('enrollments.confirm', $masterClass))
            ->assertOk();

        $this->actingAs($visitor)
            ->post(route('enrollments.store', $masterClass))
            ->assertRedirect(route('categories.show', $masterClass->creativityType));

        $this->assertDatabaseHas('enrollments', [
            'master_class_id' => $masterClass->id,
            'user_id' => $visitor->id,
        ]);
    }

    public function test_store_enrollment_rejects_duplicate_record(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        Enrollment::query()->create([
            'master_class_id' => $masterClass->id,
            'user_id' => $visitor->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $masterClass));

        $response->assertRedirect(route('categories.show', $masterClass->creativityType));
        $response->assertSessionHas('error', 'Вы уже записаны на этот мастер-класс.');
        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_store_enrollment_rejects_same_datetime_conflict(): void
    {
        $visitor = $this->createVisitor();
        $type = $this->createType();
        $masterOne = $this->createMaster();
        $masterTwo = $this->createMaster();
        $date = CarbonImmutable::today()->addDays(2)->format('Y-m-d');

        $first = $this->createMasterClass($masterOne, $type, [
            'class_date' => $date,
            'class_time' => '13:00:00',
        ]);

        $second = $this->createMasterClass($masterTwo, $type, [
            'class_date' => $date,
            'class_time' => '13:00:00',
        ]);

        Enrollment::query()->create([
            'master_class_id' => $first->id,
            'user_id' => $visitor->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $second));

        $response->assertSessionHas('error', 'У вас уже есть запись на это же время и дату.');
    }

    public function test_store_enrollment_rejects_when_no_seats_left(): void
    {
        $visitor = $this->createVisitor();
        $otherVisitor = $this->createVisitor();
        $masterClass = $this->createMasterClass(overrides: ['max_participants' => 1]);

        Enrollment::query()->create([
            'master_class_id' => $masterClass->id,
            'user_id' => $otherVisitor->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $masterClass));

        $response->assertSessionHas('error', 'Свободных мест больше нет.');
    }

    public function test_master_cannot_enroll_as_participant(): void
    {
        $master = $this->createMaster();
        $masterClass = $this->createMasterClass();

        $response = $this->actingAs($master)
            ->post(route('enrollments.store', $masterClass));

        $response->assertSessionHas('error', 'Ведущий мастер-класса не может записываться как участник.');
    }

    public function test_visitor_can_view_and_destroy_enrollment(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        Enrollment::query()->create([
            'master_class_id' => $masterClass->id,
            'user_id' => $visitor->id,
        ]);

        $this->actingAs($visitor)
            ->get(route('enrollments.index'))
            ->assertOk();

        $this->actingAs($visitor)
            ->delete(route('enrollments.destroy', $masterClass))
            ->assertRedirect(route('enrollments.index'));

        $this->assertDatabaseMissing('enrollments', [
            'master_class_id' => $masterClass->id,
            'user_id' => $visitor->id,
        ]);

        $this->actingAs($visitor)
            ->delete(route('enrollments.destroy', $masterClass))
            ->assertSessionHas('error', 'Запись не найдена или уже отменена.');
    }

    public function test_cancel_enrollment_endpoint_returns_status_message(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        $this->actingAs($visitor)
            ->post(route('enrollments.cancel', $masterClass))
            ->assertRedirect(route('categories.show', $masterClass->creativityType))
            ->assertSessionHas('status', 'Запись отменена.');
    }

    public function test_logout_ends_authenticated_session(): void
    {
        $visitor = $this->createVisitor();

        $this->actingAs($visitor)
            ->post(route('auth.logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_master_class_store_validation_rejects_unknown_slot(): void
    {
        $master = $this->createMaster();
        $type = $this->createType();

        $response = $this->actingAs($master)->post(route('master-classes.store'), [
            'creativity_type_id' => $type->id,
            'title' => 'Невалидный слот мастер-класса',
            'description' => 'Описание мастер-класса достаточно длинное для проверки только поля времени слота.',
            'class_date' => CarbonImmutable::today()->addDays(7)->format('Y-m-d'),
            'class_time' => '10:00',
            'max_participants' => 5,
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors(['class_time']);
    }

    private function createType(array $overrides = []): CreativityType
    {
        $slug = Str::lower(Str::random(10));

        return CreativityType::query()->create(array_merge([
            'name' => 'Тип '.$slug,
            'slug' => $slug,
            'description' => 'Описание направления творчества для тестов.',
            'hero_image_path' => null,
        ], $overrides));
    }

    private function createMaster(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'master',
            'password' => Hash::make('password123'),
        ], $overrides));
    }

    private function createVisitor(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'visitor',
            'password' => Hash::make('password123'),
        ], $overrides));
    }

    private function createMasterClass(?User $master = null, ?CreativityType $type = null, array $overrides = []): MasterClass
    {
        $master ??= $this->createMaster();
        $type ??= $this->createType();

        return MasterClass::query()->create(array_merge([
            'creativity_type_id' => $type->id,
            'master_id' => $master->id,
            'title' => 'Мастер-класс '.$type->slug,
            'description' => 'Подробное описание мастер-класса, достаточное для прохождения валидации формы.',
            'class_date' => CarbonImmutable::today()->addDays(2)->format('Y-m-d'),
            'class_time' => '09:00:00',
            'max_participants' => 10,
            'price' => 1500,
        ], $overrides));
    }
}
