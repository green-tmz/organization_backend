<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Modules\Api\Building\Models\Building;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    public function testIndex(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $building = Building::factory()->create();
        $response = $this->getJson(route('building.index'));

        $response->assertOk();

        $coordinates = $building->location;
        $this->assertEquals(26.193056, $coordinates->getLatitude());
        $this->assertEquals(-80.161111, $coordinates->getLongitude());

        $response->assertJson([
           'data' => [
               [
                   'id' => $building->id,
                   'city' => $building->city,
                   'street' => $building->street,
                   'office' => $building->office,
                   'location' => $response['data'][0]['location'],
               ]
           ],
        ]);
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $building = Building::factory()->create();
        $response = $this->getJson(route('building.show', ['building' => $building->id]));

        $response->assertOk();

        $coordinates = $building->location;
        $this->assertEquals(26.193056, $coordinates->getLatitude());
        $this->assertEquals(-80.161111, $coordinates->getLongitude());

        $response->assertJson([
            "data" => [
                'id' => $building->id,
                'city' => $building->city,
                'street' => $building->street,
                'office' => $building->office,
                'location' => $response['data']['location'],
            ]
        ]);
    }

    public function testCreate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'city' => 'Москва2',
            'street' => 'Ленина 1',
            "office" => "3",
            "location" => "53.463493, -2.292279"
        ];

        $response = $this->postJson(route('building.create'), $data);
        $response->assertCreated();

        $response->assertJson([
            "data" => [
                'id' => $response['data']['id'],
                'city' => $response['data']['city'],
                'street' => $response['data']['street'],
                'office' => $response['data']['office'],
                'location' => $response['data']['location'],
            ]
        ]);

        $coordinates = $response['data']['location']['coordinates'];
        $this->assertDatabaseHas('buildings', [
            'id' => $response['data']['id'],
            'city' => $response['data']['city'],
            'street' => $response['data']['street'],
            'office' => $response['data']['office'],
            'location' => Point::makeGeodetic($coordinates[1], $coordinates[0]),
        ]);
    }

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $building = Building::factory()->create();
        $data = [
            'city' => 'Москва3',
            'street' => 'Ленина 2',
            "office" => "4",
            "location" => "53.463493, -2.292279"
        ];

        $response = $this->putJson(route('building.update', ['building' => $building->id]), $data);
        $response->assertOk();

        $response->assertJson([
            "data" => [
                'id' => $response['data']['id'],
                'city' => $response['data']['city'],
                'street' => $response['data']['street'],
                'office' => $response['data']['office'],
                'location' => $response['data']['location'],
            ]
        ]);

        $coordinates = $response['data']['location']['coordinates'];
        $this->assertDatabaseHas('buildings', [
            'id' => $response['data']['id'],
            'city' => $response['data']['city'],
            'street' => $response['data']['street'],
            'office' => $response['data']['office'],
            'location' => Point::makeGeodetic($coordinates[1], $coordinates[0]),
        ]);
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $building = Building::factory()->create();

        $response = $this->deleteJson(route('building.destroy', ['building' => $building->id]));
        $response->assertOk();

        $this->assertDatabaseMissing('buildings', ['id' => $building->id]);
    }
}
