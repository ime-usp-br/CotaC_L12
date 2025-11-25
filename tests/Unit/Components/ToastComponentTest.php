<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class ToastComponentTest extends TestCase
{
    /** @test */
    public function it_renders_toast_container_component(): void
    {
        $view = $this->blade('<x-toast-container />');

        $view->assertSee('x-data');
        $view->assertSeeHtml('aria-live="polite"');
        $view->assertSee('x-notify');
    }

    /** @test */
    public function toast_container_has_correct_positioning(): void
    {
        $view = $this->blade('<x-toast-container />');

        $view->assertSee('fixed');
        $view->assertSee('top-4');
        $view->assertSee('right-4');
        $view->assertSee('z-50');
    }

    /** @test */
    public function toast_container_has_accessibility_attributes(): void
    {
        $view = $this->blade('<x-toast-container />');

        $view->assertSeeHtml('aria-live="polite"');
        $view->assertSeeHtml('aria-atomic="true"');
    }

    /** @test */
    public function toast_styles_include_all_types(): void
    {
        $view = $this->blade('<x-toast-container />');

        // Verify all toast types are styled
        $view->assertSee('notify.success');
        $view->assertSee('notify.error');
        $view->assertSee('notify.warning');
        $view->assertSee('notify.info');
    }

    /** @test */
    public function toast_styles_support_dark_mode(): void
    {
        $view = $this->blade('<x-toast-container />');

        $view->assertSee('.dark [x-notify]');
    }
}
