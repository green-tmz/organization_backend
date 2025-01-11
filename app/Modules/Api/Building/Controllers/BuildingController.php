<?php

namespace App\Modules\Api\Building\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Api\Building\Requests\BuildingRequest;
use App\Modules\Api\Building\Models\Building;
use App\Modules\Api\Building\Resources\BuildingListResource;
use App\Modules\Api\Building\Resources\BuildingResource;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Здания/Базовые методы",
 *         version="1.0.0",
 *     )
 * )
 *
 * Class Controller
 * @package App\Modules\Api\Building\Controllers
 */
class BuildingController extends Controller
{
    /**
     * Список всех зданий.
     *
     * @OA\Get(
     *     path="/api/building",
     *     operationId="getBuildingList",
     *     tags={"Список всех зданий"},
     *     summary="Список всех зданий.",
     *     description="Получения списока всех зданий",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список всех зданий",
     *           @OA\JsonContent(
     *              @OA\Property(property="lang", type="string", example="ru"),
     *              @OA\Property(
     *                  property="users",
     *                  type="array",
     *                  @OA\Items(
     *
     *                  )
     *              )
     *         )
     *     )
     * )
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return BuildingListResource::collection(Building::all());
    }

    /**
     * Добавление нового здания.
     *
     * @OA\Post(
     *     path="/api/building",
     *     operationId="buildingStore",
     *     tags={"Добавление нового здания"},
     *     summary="Добавление нового здания.",
     *     description="Добавляет новое здание",
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Значения общих настроек",
     *          @OA\JsonContent(
     *              @OA\Property(property="city", description="Сity", type="string", example="Москва"),
     *              @OA\Property(property="street", description="Street", type="string", example="Ленина 1"),
     *              @OA\Property(property="office", description="Office", type="string", example="3"),
     *              @OA\Property(property="location", description="Location", type="string", example="53.463493, -2.292279"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ответ с id должности",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Должность"),
     *                 @OA\Property(property="slug", type="string", example="department")
     *              )
     *         )
     *     )
     * )
     *
     * @param BuildingRequest $request
     * @return BuildingListResource
     */
    public function create(BuildingRequest $request): JsonResource
    {
        $coordinates =  explode(',', $request->validated('location'));
        Building::create([
            'city' => $request->validated('city'),
            'street' => $request->validated('street'),
            'office' => $request->validated('office'),
            'location' => Point::makeGeodetic($coordinates[0], $coordinates[1]),
        ]);

        return BuildingListResource::collection(Building::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Редактирование здания.
     *
     * @OA\Put(
     *     path="/api/building/{building}",
     *     operationId="buildingUpdate",
     *     tags={"Редактирование здания"},
     *     summary="Редактирование здания",
     *     description="Редактирует здание и возвращает ее данные",
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="id здания",
     *         in="path",
     *         name="building",
     *         required=true,
     *         example="1"
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Значения общих настроек",
     *          @OA\JsonContent(
     *              @OA\Property(property="city", description="Сity", type="string", example="Москва"),
     *              @OA\Property(property="street", description="Street", type="string", example="Ленина 1"),
     *              @OA\Property(property="office", description="Office", type="string", example="3"),
     *              @OA\Property(property="location", description="Location", type="string", example="49.87108851299202, 8.625026485851762"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ответ с id группы",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Новая должность"),
     *                 @OA\Property(property="slug", type="string", example="new-department")
     *              )
     *         )
     *     )
     * )
     *
     * @param BuildingRequest $request
     * @param Building $building
     * @return BuildingResource
     */
    public function update(BuildingRequest $request, Building $building): BuildingResource
    {
        $coordinates =  explode(',', $request->validated('location'));
        $building->update([
            'city' => $request->validated('city'),
            'street' => $request->validated('street'),
            'office' => $request->validated('office'),
            'location' => Point::makeGeodetic($coordinates[0], $coordinates[1]),
        ]);

        return new BuildingResource($building);
    }

    /**
     * Удаление здания.
     *
     * @OA\Delete(
     *     path="/api/building/{building}",
     *     operationId="buildingDestroy",
     *     tags={"Удаление здания"},
     *     summary="Удаление здания.",
     *     description="УУдаление здания.",
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="id здания",
     *         in="path",
     *         name="building",
     *         required=true,
     *         example="1"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Удаление здания прошло успешно",
     *     )
     * )
     *
     * @param  Building  $building
     * @return JsonResource
     */
    public function destroy(Building  $building): JsonResource
    {
        // /** @var User $user */
        // foreach($department->user as $user) {
        //     $user->group_id = null;
        //     $user->save();
        // }
        $building->delete();

        return $this->index();
    }
}
