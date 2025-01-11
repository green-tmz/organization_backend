<?php

namespace Tests\Feature\Factories;

use App\Models\User;
use App\Modules\Api\Building\Models\Building;
use Clickbar\Magellan\Data\Geometries\Point;
use Tests\TestCase;

class FactoriesCreateTest extends TestCase
{
    public function testCreateBuilding(): void
    {
        $building = Building::factory()->create([
            'location' => Point::makeGeodetic(26.193056, -80.161111),
        ]);
        $coordinates = $building->location;
        $this->assertEquals(26.193056, $coordinates->getLatitude());
        $this->assertEquals(-80.161111, $coordinates->getLongitude());

        $this->assertDatabaseHas('buildings', [
            'city' => $building->city,
            'street' => $building->street,
            'office' => $building->office,
            'location' => $building->location,
        ]);
    }

    public function testCreateUser(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ]);
    }
}
