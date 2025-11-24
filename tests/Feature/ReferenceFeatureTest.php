<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Hospital;
use App\Models\Reference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Hospital $hospital;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->hospital = Hospital::factory()->create();
        $this->admin = User::factory()->create([
            'hospital_id' => $this->hospital->id,
            'role' => User::ROLE_ADMIN,
        ]);

        session(['hospital_id' => $this->hospital->id]);
    }

    public function test_admin_can_create_reference(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'title' => 'Panduan Operasional ICU',
            'content' => '## Instruksi Utama' . PHP_EOL . 'Pastikan standar dipenuhi.',
            'status' => Reference::STATUS_PUBLISHED,
            'is_pinned' => true,
        ];

        $response = $this->post(route('references.store'), $payload);

        $response->assertRedirect(route('references.index'));

        $this->assertDatabaseHas('references', [
            'title' => 'Panduan Operasional ICU',
            'hospital_id' => $this->hospital->id,
            'author_id' => $this->admin->id,
            'is_pinned' => true,
            'status' => Reference::STATUS_PUBLISHED,
        ]);
    }

    public function test_view_count_incremented_when_reference_is_opened(): void
    {
        $this->actingAs($this->admin);

        $reference = Reference::factory()->create([
            'hospital_id' => $this->hospital->id,
            'author_id' => $this->admin->id,
            'status' => Reference::STATUS_PUBLISHED,
            'view_count' => 0,
        ]);

        $this->get(route('references.show', $reference))
            ->assertStatus(200);

        $this->assertDatabaseHas('references', [
            'id' => $reference->id,
            'view_count' => 1,
        ]);
    }

    public function test_user_from_other_hospital_cannot_access_reference(): void
    {
        $otherHospital = Hospital::factory()->create();
        $otherReference = Reference::factory()->create([
            'hospital_id' => $otherHospital->id,
            'author_id' => User::factory()->create(['hospital_id' => $otherHospital->id])->id,
        ]);

        $this->actingAs($this->admin);

        $this->get(route('references.show', $otherReference))
            ->assertForbidden();
    }
}

