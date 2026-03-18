<?php

namespace App\Http\Requests;

use App\Enums\LanguageEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $languageValues = implode(',', array_column(LanguageEnum::cases(), 'value'));

        return [
            'password'              => ['nullable', 'confirmed', Password::defaults()],
            'password_confirmation' => ['nullable', 'required_with:password'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'language'              => ['nullable', 'string', "in:{$languageValues}"],
        ];
    }

    public function messages(): array
    {
        return [
            'language.in'              => 'The selected language is not supported.',
            'date_of_birth.before'     => 'Date of birth must be in the past.',
            'password_confirmation.required_with' => 'Please confirm your new password.',
        ];
    }
}
