<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Register;
use Illuminate\Database\Eloquent\Model;

class CustomRegister extends Register
{
    protected function handleRegistration(array $data): Model
    {
        $user = $this->getUserModel()::create(
            $data
        );

        $user->email_verified_at = now();
        $user->save();
        return $user;
    }
}
