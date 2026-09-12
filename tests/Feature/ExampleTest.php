<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_boots_successfully(): void
    {
        $this->assertNotNull(app());
    }
}
