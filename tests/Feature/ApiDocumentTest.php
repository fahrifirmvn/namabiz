<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_download_free_document(): void
    {
        $document = Document::factory()->create([
            'is_premium' => false,
            'external_url' => 'https://example.com/public-brochure.pdf',
        ]);

        $response = $this->get(route('api.documents.download', ['document' => $document->id]));

        $response->assertStatus(302)
            ->assertRedirect('https://example.com/public-brochure.pdf');
    }

    public function test_guest_cannot_download_premium_document(): void
    {
        $document = Document::factory()->create([
            'is_premium' => true,
            'external_url' => 'https://example.com/audit-report.pdf',
        ]);

        $response = $this->getJson(route('api.documents.download', ['document' => $document->id]));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This document requires an active Premium subscription.',
                'errors' => ['paywall' => true],
            ]);
    }

    public function test_free_user_cannot_download_premium_document(): void
    {
        $document = Document::factory()->create([
            'is_premium' => true,
            'external_url' => 'https://example.com/audit-report.pdf',
        ]);

        $freeUser = User::factory()->create(['subscription_tier' => 'free']);

        $response = $this->actingAs($freeUser, 'sanctum')
            ->getJson(route('api.documents.download', ['document' => $document->id]));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'This document requires an active Premium subscription.',
                'errors' => ['paywall' => true],
            ]);
    }

    public function test_premium_user_can_download_premium_document(): void
    {
        $document = Document::factory()->create([
            'is_premium' => true,
            'external_url' => 'https://example.com/audit-report.pdf',
        ]);

        $premiumUser = User::factory()->premium()->create();

        $response = $this->actingAs($premiumUser, 'sanctum')
            ->get(route('api.documents.download', ['document' => $document->id]));

        $response->assertStatus(302)
            ->assertRedirect('https://example.com/audit-report.pdf');
    }
}
