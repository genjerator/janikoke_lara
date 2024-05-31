<?php

namespace App\Http\Requests;

use App\Models\Area;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class InsidePolygonRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'area_id' => ['required', 'integer', 'exists:' . Area::class . ',id'],
            'challenge_id' => ['required', 'integer', 'exists:' . Challenge::class . ',id'],
        ];
    }
}
