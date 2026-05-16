<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FailForScreenshotTest extends TestCase
{
    #[Test]
    public function intentional_failure_for_screenshot(): void
    {
        $this->assertTrue(false, 'Intentional failure');
    }
}
