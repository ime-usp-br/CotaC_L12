<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class SkeletonComponentTest extends TestCase
{
    /** @test */
    public function it_renders_skeleton_with_default_type(): void
    {
        $view = $this->blade('<x-skeleton />');

        $view->assertSee('animate-pulse');
        $view->assertSee('bg-gray-300');
        $view->assertSee('dark:bg-gray-700');
    }

    /** @test */
    public function it_renders_text_skeleton(): void
    {
        $view = $this->blade('<x-skeleton type="text" />');

        $view->assertSee('h-4');
    }

    /** @test */
    public function it_renders_card_skeleton(): void
    {
        $view = $this->blade('<x-skeleton type="card" />');

        $view->assertSee('h-32');
    }

    /** @test */
    public function it_renders_avatar_skeleton(): void
    {
        $view = $this->blade('<x-skeleton type="avatar" />');

        $view->assertSee('h-12');
        $view->assertSee('w-12');
        $view->assertSee('rounded-full');
    }

    /** @test */
    public function it_renders_button_skeleton(): void
    {
        $view = $this->blade('<x-skeleton type="button" />');

        $view->assertSee('h-10');
        $view->assertSee('w-24');
    }

    /** @test */
    public function it_renders_multiple_text_lines(): void
    {
        $view = $this->blade('<x-skeleton type="text" lines="3" />');

        $view->assertSee('space-y-3');
    }

    /** @test */
    public function skeleton_has_pulse_animation(): void
    {
        $view = $this->blade('<x-skeleton />');

        $view->assertSee('animate-pulse');
    }

    /** @test */
    public function skeleton_supports_dark_mode(): void
    {
        $view = $this->blade('<x-skeleton />');

        $view->assertSee('dark:bg-gray-700');
    }
}
