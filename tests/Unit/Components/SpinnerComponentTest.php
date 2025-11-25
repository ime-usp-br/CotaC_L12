<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class SpinnerComponentTest extends TestCase
{
    /** @test */
    public function it_renders_spinner_with_default_size(): void
    {
        $view = $this->blade('<x-spinner />');

        $view->assertSee('animate-spin');
        $view->assertSee('h-6');
        $view->assertSee('w-6');
    }

    /** @test */
    public function it_renders_small_spinner(): void
    {
        $view = $this->blade('<x-spinner size="sm" />');

        $view->assertSee('h-4');
        $view->assertSee('w-4');
    }

    /** @test */
    public function it_renders_large_spinner(): void
    {
        $view = $this->blade('<x-spinner size="lg" />');

        $view->assertSee('h-8');
        $view->assertSee('w-8');
    }

    /** @test */
    public function it_renders_extra_large_spinner(): void
    {
        $view = $this->blade('<x-spinner size="xl" />');

        $view->assertSee('h-12');
        $view->assertSee('w-12');
    }

    /** @test */
    public function it_renders_spinner_with_default_color(): void
    {
        $view = $this->blade('<x-spinner />');

        $view->assertSee('text-blue-600');
    }

    /** @test */
    public function it_renders_white_spinner(): void
    {
        $view = $this->blade('<x-spinner color="white" />');

        $view->assertSee('text-white');
    }

    /** @test */
    public function it_renders_green_spinner(): void
    {
        $view = $this->blade('<x-spinner color="green" />');

        $view->assertSee('text-green-600');
    }

    /** @test */
    public function spinner_has_accessibility_attributes(): void
    {
        $view = $this->blade('<x-spinner />');

        $view->assertSee('role="status"');
        $view->assertSee('aria-label');
    }

    /** @test */
    public function spinner_has_spin_animation(): void
    {
        $view = $this->blade('<x-spinner />');

        $view->assertSee('animate-spin');
    }
}
