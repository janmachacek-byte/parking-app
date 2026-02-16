<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ParkingSpotTest extends TestCase
{
    use RefreshDatabase;

    public function test_parking_spot_requires_auth(): void
    {
        $this->getJson('/api/parking-spot')
            ->assertUnauthorized();

        $this->putJson('/api/parking-spot', ['spot' => 'C4'])
            ->assertUnauthorized();
    }

    public function test_user_can_set_and_get_parking_spot(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // první uložení = vytvoření => 201
        $this->putJson('/api/parking-spot', ['spot' => 'C4'])
            ->assertCreated()
            ->assertJsonPath('data.spot', 'C4');

        // druhé uložení = update => 200
        $this->putJson('/api/parking-spot', ['spot' => 'D7'])
            ->assertOk()
            ->assertJsonPath('data.spot', 'D7');

        $this->getJson('/api/parking-spot')
            ->assertOk()
            ->assertJsonPath('data.spot', 'D7');
    }

    public function test_spot_validation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson('/api/parking-spot', ['spot' => 'c4'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['spot']);

        $this->putJson('/api/parking-spot', ['spot' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['spot']);
    }

    public function test_user_cannot_update_other_users_parking_spot(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        // vytvoříme spot ownerovi
        \App\Models\ParkingSpot::query()->create([
            'user_id' => $owner->id,
            'spot' => 'A1',
        ]);

        // přihlásíme se jako "other" a uložíme si vlastní spot
        Sanctum::actingAs($other);

        // pro "other" je to první uložení => 201
        $this->putJson('/api/parking-spot', ['spot' => 'B2'])
            ->assertCreated()
            ->assertJsonPath('data.spot', 'B2');

        // ověříme, že ownerovi zůstal původní spot
        $this->assertDatabaseHas('parking_spots', [
            'user_id' => $owner->id,
            'spot' => 'A1',
        ]);
    }
}