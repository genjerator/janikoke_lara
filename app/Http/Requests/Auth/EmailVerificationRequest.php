<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;

class EmailVerificationRequest extends FormRequest
{
    protected ?User $verifiableUser = null;

    /**
     * Fetch the user from DB by route {id} — no active session required.
     */
    public function getVerifiableUser(): User
    {
        if (! $this->verifiableUser) {
            $this->verifiableUser = User::findOrFail((int) $this->route('id'));
        }

        return $this->verifiableUser;
    }

    /**
     * Authorize by validating id and hash against DB user — no auth session needed.
     */
    public function authorize(): bool
    {
        $user = $this->getVerifiableUser();

        if (! hash_equals((string) $user->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * No extra validation rules needed.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Mark the user's email as verified and fire Verified event.
     */
    public function fulfill(): void
    {
        $user = $this->getVerifiableUser();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }
    }
}

