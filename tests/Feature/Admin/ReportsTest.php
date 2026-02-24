<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reports_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response
            ->assertOk()
            ->assertSee('Raporlama')
            ->assertSee('Günlük Aktivite Özeti');
    }

    public function test_non_admin_cannot_view_reports_page(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_export_reports_as_csv(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export', [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();

        $contentType = (string) $response->headers->get('content-type');
        $contentDisposition = (string) $response->headers->get('content-disposition');
        $content = $response->streamedContent();

        $this->assertStringContainsString('text/csv', $contentType);
        $this->assertStringContainsString('attachment; filename=', $contentDisposition);
        $this->assertStringContainsString('Rapor Periyodu', $content);
        $this->assertStringContainsString('Genel Metrikler', $content);
    }
}
