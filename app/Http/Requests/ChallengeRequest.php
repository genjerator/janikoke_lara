<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChallengeRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'active' => 'required|boolean',
            'round_id' => 'required|integer',
            'type' => 'required|string',
            //'areas' => 'required|string|max:1000', // Assuming areas is a text field
        ];
    }

    /**
     * Customize the error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'description.required' => 'The description field is required.',
            'active.required' => 'The active field is required.',
            'round_id.required' => 'The round field is required.',
           // 'areas.required' => 'The areas field is required.',
        ];
    }
}
