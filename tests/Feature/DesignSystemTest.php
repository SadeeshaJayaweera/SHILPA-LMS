<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Testing\TestCase;

class DesignSystemTest extends TestCase {
    public function testDesignSystemShowcaseRenders(): void {
        $response = $this->get('/design-system');
        $response->assertOk()
                 ->assertSee('Premium Visual Foundation')
                 ->assertSee('Color System')
                 ->assertSee('Button Matrix')
                 ->assertSee('Clean Data Tables')
                 ->assertSee('design-system.css')
                 ->assertSee('design-system.js');
    }

    public function testComponentHelperRendering(): void {
        $button = component('button', ['variant' => 'primary', 'slot' => 'Click Me']);
        assert(str_contains($button, 'btn-primary'), "Button must render with btn-primary class");
        assert(str_contains($button, 'Click Me'), "Button must contain slot text");

        $badge = component('badge', ['variant' => 'success', 'slot' => 'Active']);
        assert(str_contains($badge, 'badge-success'), "Badge must render with badge-success class");

        $alert = component('alert', ['type' => 'warning', 'title' => 'Caution', 'slot' => 'Check input']);
        assert(str_contains($alert, 'alert-warning'), "Alert must render with alert-warning class");

        $icon = icon('shield', ['size' => 24]);
        assert(str_contains($icon, '<svg'), "Icon must render svg markup");
    }

    public function testStaticAssetsExist(): void {
        $cssPath = dirname(__DIR__, 2) . '/public/css/design-system.css';
        $jsPath = dirname(__DIR__, 2) . '/public/js/design-system.js';

        assert(file_exists($cssPath), "design-system.css must exist");
        assert(file_exists($jsPath), "design-system.js must exist");

        $cssContent = file_get_contents($cssPath);
        assert(str_contains($cssContent, '--bg-app'), "CSS must define design tokens");
        assert(str_contains($cssContent, '[data-theme="light"]'), "CSS must define light mode overrides");
    }
}
