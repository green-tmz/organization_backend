<?php

namespace App\Modules\Api\Building\Models;

use Database\Factories\BuildingFactory;
use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;
    use HasPostgisColumns;

    protected $fillable = [
        'city',
        'street',
        'office',
        'location',
    ];

    protected array $postgisColumns = [
        'location' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected static function newFactory(): BuildingFactory
    {
        return BuildingFactory::new();
    }
}
