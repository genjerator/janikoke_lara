<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {

        // Honeypot: the 'website' field should never be filled by real users.
        // Bots will auto-fill it. Return a fake success to not tip them off.
        if ($request->filled('website')) {
            Log::warning('Honeypot triggered', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully. Verification email sent.',
                'user' => [
                    'id' => 0,
                    'name' => $request->input('name', ''),
                    'email' => $request->input('email', ''),
                ],
            ], 201);
        }

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password'      => ['required', 'confirmed', Rules\Password::defaults()],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
                'language'      => ['nullable', 'string', 'in:' . implode(',', array_column(LanguageEnum::cases(), 'value'))],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'password'      => Hash::make($validated['password']),
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'language'      => $validated['language'] ?? LanguageEnum::English->value,
            ]);

            event(new Registered($user));

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully. Verification email sent.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration failed: '.$e->getMessage(), [
                'email' => $request->input('email'),
                'exception' => $e,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }
}
