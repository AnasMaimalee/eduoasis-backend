<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Profile\ProfileService;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $service) {}

    // Get user profile + bank account
    public function show()
    {
        return response()->json(
            $this->service->profile(auth()->user())
        );
    }

    // Create or update bank account
    public function updateBank(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string',
            'account_name'   => 'required|string',
            'account_number' => 'required|string|min:10',
        ]);

        return response()->json(
            $this->service->updateBank(auth()->user(), $request->only([
                'bank_name',
                'account_name',
                'account_number'
            ]))
        );
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        return response()->json(
            $this->service->updatePassword(
                auth()->user(),
                $request->current_password,
                $request->password
            )
        );
    }
}
