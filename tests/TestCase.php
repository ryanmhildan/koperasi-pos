<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Livewire\Features\SupportTesting\Testable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use Testable;
}
