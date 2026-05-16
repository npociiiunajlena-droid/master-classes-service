<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class ModelBehaviorTest extends TestCase
{
    public function test_master_class_normalize_slot_adds_seconds_for_short_format(): void
    {
        $this->assertSame('09:00:00', MasterClass::normalizeSlot('09:00'));
        $this->assertSame('09:00:00', MasterClass::normalizeSlot('09:00:00'));
    }

    public function test_slots_label_formats_two_hour_interval(): void
    {
        $masterClass = new MasterClass([
            'class_time' => '11:00:00',
        ]);

        $this->assertSame('11:00 - 13:00', $masterClass->slotsLabel());
    }

    public function test_seats_left_uses_enrollments_count_attribute_when_loaded(): void
    {
        $masterClass = new MasterClass([
            'max_participants' => 12,
        ]);

        $masterClass->setAttribute('enrollments_count', 4);

        $this->assertSame(8, $masterClass->seatsLeft());
        $this->assertTrue($masterClass->hasFreeSeats());
    }

    public function test_has_free_seats_returns_false_when_capacity_is_exhausted(): void
    {
        $masterClass = new MasterClass([
            'max_participants' => 3,
        ]);

        $masterClass->setAttribute('enrollments_count', 3);

        $this->assertSame(0, $masterClass->seatsLeft());
        $this->assertFalse($masterClass->hasFreeSeats());
    }

    public function test_user_role_helpers_return_expected_flags(): void
    {
        $master = new User(['role' => 'master']);
        $visitor = new User(['role' => 'visitor']);

        $this->assertTrue($master->isMaster());
        $this->assertFalse($master->isVisitor());
        $this->assertFalse($visitor->isMaster());
        $this->assertTrue($visitor->isVisitor());
    }

    public function test_creativity_type_uses_slug_as_route_key(): void
    {
        $type = new CreativityType;

        $this->assertSame('slug', $type->getRouteKeyName());
    }
}
