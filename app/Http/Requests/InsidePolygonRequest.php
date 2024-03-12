<?php

namespace App\Http\Requests;

use App\Models\Area;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class InsidePolygonRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'area_id' => ['required', 'integer', 'exists:' . Area::class . ',id'],
            'challenge_id' => ['required', 'integer', 'exists:' . Challenge::class . ',id'],
            'user_id' => ['required', 'integer', 'exists:' . User::class . ',id'],
        ];
    }
}
