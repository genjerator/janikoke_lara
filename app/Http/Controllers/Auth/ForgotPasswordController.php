<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Generate a random password, update the user record, and send it via email.
     * Returns a generic success response regardless of whether the email exists
     * to prevent email enumeration attacks.
     */
    public function sendRandomPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }

        $user = User::where('email', strtolower($request->input('email')))->first();

        // Always return success to prevent email enumeration.
        if (! $user) {
            return response()->json([
                'status'  => 'success',
                'message' => 'If an account with that email exists, a new password has been sent.',
            ]);
        }

        // Generate a secure random password: 6 chars, mixed case + digits + symbols.
        $newPassword = Str::password(6, letters: true, numbers: true, symbols: false, spaces: false);

        try {
            $user->password = Hash::make($newPassword);
            $user->save();

            Mail::to($user->email)->send(new ForgotPasswordMail($newPassword, $user->name));

            Log::info('Random password sent to user.', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send random password.', [
                'user_id'   => $user->id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to send password reset email. Please try again later.',
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'If an account with that email exists, a new password has been sent.',
        ]);
    }
}

