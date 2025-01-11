<?php

namespace App\Modules\Api\Building\Requests;

use App\Rules\MinNumber;
use App\Rules\MinUppercase;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\RequestFailedValidationTrait;

class BuildingRequest extends FormRequest
{
    use RequestFailedValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string'],
            'street' => ['required', 'string'],
            'office' => ['required', 'integer'],
            'location' => [],
        ];
    }

    public function geometries(): array
    {
        return ['location'];
    }
}
