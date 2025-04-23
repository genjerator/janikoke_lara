<?php

namespace App\Domains\Customer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'bd_start_date' => ['date', 'before:dd_end_date'],
            'bd_end_date' => ['date', 'after:dd_start_date'],
            'dd_start_date' => ['date', 'before:dd_end_date'],
            'dd_end_date' => ['date', 'after:dd_start_date'],
        ];
    }
}
