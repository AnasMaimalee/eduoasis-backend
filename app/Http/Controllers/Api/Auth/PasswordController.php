<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
class PasswordController extends Controller
{
    public function update(Request $request, UserService $userService)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $userService->updatePassword(
            auth()->user(),
            $request->password
        );

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful'])
            : response()->json(['message' => 'Invalid or expired token'], 422);
    }

}
