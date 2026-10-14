<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class SidebarTenantBrandTest extends TestCase
{
    public function test_broken_logo_keeps_avatar_fallback_and_constrained_tenant_name(): void
    {
        $html = Blade::render(
            '<x-tenant-brand name="Rumah Sakit Dengan Nama Tenant Yang Sangat Panjang" logo-url="https://example.test/broken.png" color="#2563eb" />'
        );

        $this->assertStringContainsString('data-tenant-logo-fallback', $html);
        $this->assertStringContainsString('data-tenant-logo', $html);
        $this->assertStringContainsString("onerror=\"this.remove()\"", $html);
        $this->assertStringContainsString('object-contain', $html);
        $this->assertStringContainsString('truncate', $html);
        $this->assertStringContainsString('title="Rumah Sakit Dengan Nama Tenant Yang Sangat Panjang"', $html);
    }

    public function test_missing_logo_renders_only_avatar_fallback(): void
    {
        $html = Blade::render('<x-tenant-brand name="RS Tanpa Logo" />');

        $this->assertStringContainsString('data-tenant-logo-fallback', $html);
        $this->assertStringNotContainsString('data-tenant-logo=', $html);
    }

    public function test_compact_brand_hides_name_and_keeps_fixed_logo_size(): void
    {
        $html = Blade::render('<x-tenant-brand name="RS Ringkas" compact />');

        $this->assertStringContainsString('h-8 w-8', $html);
        $this->assertStringNotContainsString('data-tenant-name', $html);
    }
}
