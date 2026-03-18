<?php

namespace App\Http\Controllers;

use App\Enums\LanguageEnum;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * GET /profile
     * Return the authenticated user's profile data.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'date_of_birth' => $user->date_of_birth?->toDateString(),
                'language'      => $user->language instanceof LanguageEnum
                                        ? $user->language->value
                                        : $user->language,
                'language_label'=> $user->language instanceof LanguageEnum
                                        ? $user->language->label()
                                        : null,
            ],
        ]);
    }

    /**
     * PATCH /profile
     * Update password, date_of_birth and/or language.
     * All fields are optional — only provided fields are updated.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        try {
            $validated = $request->validated();
            $updated   = [];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $updated[] = 'password';
            }

            if (array_key_exists('date_of_birth', $validated) && $validated['date_of_birth'] !== null) {
                $user->date_of_birth = $validated['date_of_birth'];
                $updated[] = 'date_of_birth';
            }

            if (array_key_exists('language', $validated) && $validated['language'] !== null) {
                $user->language = $validated['language'];
                $updated[] = 'language';
            }

            if (empty($updated)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No fields provided to update.',
                ], 422);
            }

            $user->save();

            Log::info('Profile updated.', ['user_id' => $user->id, 'fields' => $updated]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully.',
                'updated' => $updated,
                'user'    => [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'date_of_birth' => $user->date_of_birth?->toDateString(),
                    'language'      => $user->language instanceof LanguageEnum
                                            ? $user->language->value
                                            : $user->language,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update failed.', [
                'user_id'   => $user->id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Profile update failed. Please try again later.',
            ], 500);
        }
    }
}
